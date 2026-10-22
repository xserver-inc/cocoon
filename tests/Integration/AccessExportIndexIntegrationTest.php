<?php
/**
 * 日付索引を利用できない場合のランキング全件書き出しの実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportIndexIntegrationTest extends IntegrationTestCase
{
    /**
     * 日付索引の欠落時も、DBとファイルの両経路で137記事のPVと順位を維持することの確認
     */
    public function testMissingDateIndexFallsBackWithoutLosingArticlesOrTheirOrder(): void
    {
        $this->withAccessFixture('missing', function (array $expected, string $snapshot): void {
            $this->assertBothExportPaths($expected, $snapshot);
        });
    }

    /**
     * 日付索引の不可視化時も、DBとファイルの両経路で137記事のPVと順位を維持することの確認
     */
    public function testInvisibleDateIndexFallsBackWithoutLosingArticlesOrTheirOrder(): void
    {
        global $wpdb;
        $server = (string) $wpdb->get_var('SELECT VERSION()');
        if (stripos($server, 'MariaDB') !== false || version_compare($wpdb->db_version(), '8.0.0', '<')) {
            $this->markTestSkipped('INVISIBLE索引を利用できるMySQL 8.0以降での検証');
        }
        $this->withAccessFixture('invisible', function (array $expected, string $snapshot): void {
            $this->assertBothExportPaths($expected, $snapshot);
        });
    }

    /**
     * 索引以外のソースSQLエラーを再試行せず、DBとファイルの両経路で失敗として返すことの確認
     */
    public function testOtherSourceErrorsDoNotTriggerTheIndexFallback(): void
    {
        global $wpdb;
        $this->withAccessFixture('visible', function (array $expected, string $snapshot) use ($wpdb): void {
            $sourceQueries = array();
            $intercept = static function ($sql) use (&$sourceQueries) {
                if (strpos($sql, 'SELECT totals.post_id, p.post_type, p.post_date, totals.pv') !== 0) return $sql;
                $sourceQueries[] = $sql;
                // 索引の状態に依存しない、実DBの不明な列エラーの再現
                return str_replace('SUM(count)', 'SUM(cocoon_missing_export_count)', $sql);
            };
            $suppressed = $wpdb->suppress_errors;
            add_filter('query', $intercept);
            try {
                foreach (array('snapshot', 'stream') as $path) {
                    $sourceQueries = array();
                    $beforeStreams = array_keys(get_resources('stream'));
                    $temporaryUnavailable = true;
                    $stream = null;
                    ob_start();
                    try {
                        $result = $path === 'snapshot'
                            ? cocoon_analytics_create_export_snapshot($wpdb, $snapshot, '2098-11-04', '2098-11-05', $temporaryUnavailable)
                            : ($stream = cocoon_analytics_create_export_stream($wpdb, '2098-11-04', '2098-11-05'));
                    } finally {
                        $output = ob_get_clean();
                    }
                    try {
                        $this->assertFalse($result, $path);
                        if ($path === 'snapshot') $this->assertFalse($temporaryUnavailable);
                        $this->assertCount(1, $sourceQueries, $path . 'の通常SQLエラーの不要な再試行');
                        $this->assertSame('', $output, $path . 'のDBエラー表示の混入');
                        $this->assertSame($suppressed, $wpdb->suppress_errors);
                    } finally {
                        if (is_resource($stream)) fclose($stream);
                        $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
                    }
                    $this->assertSame(array(), array_values(array_diff(array_keys(get_resources('stream')), $beforeStreams)));
                }
            } finally {
                remove_filter('query', $intercept);
            }
        });
    }

    /**
     * PDOのSQLSTATEとドライバー番号を混同せず、索引エラー1176だけを再試行することの確認
     */
    public function testExceptionDriversOnlyRetryTheMissingIndexErrorNumber(): void
    {
        foreach (array(
            array('runtime', null, true),
            array('pdo', array('42000', 1176, 'Missing index'), true),
            array('pdo', array('42000', '1176', 'Missing index'), true),
            array('pdo', array('42000', 1054, 'Unknown column'), false),
            array('pdo', array('42000'), false),
            array('pdo', array('42000', '1176oops'), false),
            array('pdo', array('42000', '1176.0'), false),
            array('pdo', null, false),
        ) as $case) {
            // SQLSTATE単独でも1176に見える場合を含む、例外ドライバーの判定条件
            $error = $case[0] === 'runtime' ? new \RuntimeException('索引エラーの検証例外', 1176) : new \PDOException('PDO索引エラーの検証例外', 1176);
            if ($error instanceof \PDOException) $error->errorInfo = $case[1];
            $database = new class($error) {
                public $posts = 'cocoon_pv_fake_posts';
                public $last_error = '';
                public $sourceQueries = array();
                private $error;

                public function __construct($error)
                {
                    $this->error = $error;
                }

                public function prepare($sql, $args)
                {
                    return $sql;
                }

                public function get_results($sql, $format)
                {
                    $this->sourceQueries[] = $sql;
                    if (count($this->sourceQueries) === 1) throw $this->error;
                    return array((object) array('post_id' => '1550000000', 'post_type' => 'post', 'post_date' => '2098-11-01 00:00:00', 'pv' => '7'));
                }
            };
            $caught = null;
            try {
                $batches = iterator_to_array(cocoon_analytics_export_record_batches($database, '2098-11-04', '2098-11-05'), false);
            } catch (\RuntimeException $failure) {
                $caught = $failure;
            }
            if ($case[2]) {
                $this->assertNull($caught);
                $this->assertSame(array(array(array('post_id' => 1550000000, 'post_type' => 'post', 'post_date' => '2098-11-01 00:00:00', 'pv' => '7'))), $batches);
                $this->assertCount(2, $database->sourceQueries);
                $this->assertStringContainsString('FORCE INDEX', $database->sourceQueries[0]);
                $this->assertStringNotContainsString('FORCE INDEX', $database->sourceQueries[1]);
            } else {
                $this->assertInstanceOf(\RuntimeException::class, $caught);
                $this->assertCount(1, $database->sourceQueries);
            }
        }
    }

    private function assertBothExportPaths(array $expected, string $snapshot): void
    {
        global $wpdb;
        $sourceQueries = array();
        $observe = static function ($sql) use (&$sourceQueries) {
            if (strpos($sql, 'SELECT totals.post_id, p.post_type, p.post_date, totals.pv') === 0) $sourceQueries[] = $sql;
            return $sql;
        };
        $suppressed = $wpdb->suppress_errors;
        add_filter('query', $observe);
        try {
            foreach (array('snapshot', 'stream') as $path) {
                $sourceQueries = array();
                $stream = null;
                $beforeStreams = array_keys(get_resources('stream'));
                ob_start();
                try {
                    if ($path === 'snapshot') {
                        $temporaryUnavailable = true;
                        $success = cocoon_analytics_create_export_snapshot($wpdb, $snapshot, '2098-11-04', '2098-11-05', $temporaryUnavailable);
                    } else {
                        $stream = cocoon_analytics_create_export_stream($wpdb, '2098-11-04', '2098-11-05');
                    }
                } finally {
                    $output = ob_get_clean();
                }
                try {
                    $this->assertSame('', $output, $path . 'の索引エラー表示の混入');
                    if ($path === 'snapshot') {
                        $this->assertTrue($success, $wpdb->last_error);
                        $this->assertFalse($temporaryUnavailable);
                        $rows = $wpdb->get_results('SELECT post_id,post_type,pv FROM `' . $snapshot . '` ORDER BY export_position LIMIT 1000', ARRAY_A);
                    } else {
                        $this->assertIsResource($stream);
                        $rows = array();
                        while (($row = cocoon_analytics_export_read_record($stream)) !== null) {
                            $this->assertIsArray($row);
                            $rows[] = array('post_id' => $row['post_id'], 'post_type' => $row['post_type'], 'pv' => $row['pv']);
                        }
                    }
                    foreach ($rows as &$row) {
                        $row['post_id'] = (int) $row['post_id'];
                        $row['pv'] = (int) $row['pv'];
                    }
                    unset($row);
                    $this->assertSame($expected, $rows, $path . 'の全件・PV・順位');
                    $this->assertCount(137, $rows);
                    $this->assertSame(137, count(array_unique(array_column($rows, 'post_id'))));
                    $this->assertSame(array_sum(array_column($expected, 'pv')), array_sum(array_column($rows, 'pv')));
                    // 100件ずつの再集計を含まない、索引ヒント付き1回とヒントなし1回だけの問い合わせ
                    $this->assertCount(2, $sourceQueries, $path . 'のソース集計回数');
                    $this->assertStringContainsString('FORCE INDEX', $sourceQueries[0]);
                    $this->assertStringNotContainsString('FORCE INDEX', $sourceQueries[1]);
                    $this->assertSame('', $wpdb->last_error);
                    $this->assertSame($suppressed, $wpdb->suppress_errors);
                } finally {
                    if (is_resource($stream)) fclose($stream);
                    $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
                }
                $this->assertSame(array(), array_values(array_diff(array_keys(get_resources('stream')), $beforeStreams)));
            }
        } finally {
            remove_filter('query', $observe);
        }
    }

    private function withAccessFixture(string $indexState, callable $verify): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $originalPosts = $wpdb->posts;
        $posts = 'cocoon_pv_index_posts';
        $snapshot = 'cocoon_pv_index_snapshot';
        $created = array();
        $originalOptimizer = null;
        try {
            // 永続テーブルを変更しない、同名の接続専用アクセス表と公開記事表
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertIsArray($schema);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $created[] = ACCESSES_TABLE_NAME;
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE `' . $posts . '` (ID BIGINT PRIMARY KEY, post_type VARCHAR(126), post_date DATETIME, post_status VARCHAR(20))'));
            $created[] = $posts;
            $wpdb->posts = $posts;
            $expected = array();
            for ($offset = 0; $offset < 137; $offset += 100) {
                $postValues = array();
                $accessValues = array();
                for ($i = $offset; $i < min(137, $offset + 100); $i++) {
                    $id = 1550000000 + $i;
                    $postDate = '2098-11-0' . (1 + $i % 3) . ' 00:00:00';
                    $firstCount = 1 + $i % 11;
                    $secondCount = 2 + $i % 3;
                    $postValues[] = $wpdb->prepare('(%d,%s,%s,%s)', $id, 'post', $postDate, 'publish');
                    $accessValues[] = $wpdb->prepare('(%d,%s,%s,%d,%s)', $id, 'post', '2098-11-04', $firstCount, '192.0.2.1');
                    $accessValues[] = $wpdb->prepare('(%d,%s,%s,%d,%s)', $id, 'post', '2098-11-05', $secondCount, '192.0.2.1');
                    $expected[] = array('post_id' => $id, 'post_type' => 'post', 'pv' => $firstCount + $secondCount, 'post_date' => $postDate);
                }
                $this->assertNotFalse($wpdb->query('INSERT INTO `' . $posts . '` (ID,post_type,post_date,post_status) VALUES ' . implode(',', $postValues)));
                $this->assertNotFalse($wpdb->query('INSERT INTO `' . ACCESSES_TABLE_NAME . '` (post_id,post_type,date,count,last_ip) VALUES ' . implode(',', $accessValues)));
            }
            // 集計値・公開日・IDの順序を独立に算出した、両出力経路の期待値
            usort($expected, static function (array $left, array $right): int {
                $comparison = $right['pv'] <=> $left['pv'];
                if ($comparison !== 0) return $comparison;
                $comparison = strcmp($right['post_date'], $left['post_date']);
                return $comparison !== 0 ? $comparison : ($right['post_id'] <=> $left['post_id']);
            });
            foreach ($expected as &$row) unset($row['post_date']);
            unset($row);
            if ($indexState === 'missing') {
                $this->assertNotFalse($wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` DROP INDEX `' . INDEX_ACCESSES_DATE_PTYPE_PID_COUNT . '`'));
            } elseif ($indexState === 'invisible') {
                // 接続既定の不可視索引使用設定に左右されない、非使用条件の明示と退避
                $originalOptimizer = (string) $wpdb->get_var('SELECT @@SESSION.optimizer_switch');
                $this->assertNotFalse($wpdb->query("SET SESSION optimizer_switch='use_invisible_indexes=off'"));
                $this->assertNotFalse($wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` ALTER INDEX `' . INDEX_ACCESSES_DATE_PTYPE_PID_COUNT . '` INVISIBLE'));
            }
            $verify($expected, $snapshot);
        } finally {
            // 検証の失敗にも影響を残さない、接続専用テーブルと論理投稿表の復元
            $wpdb->posts = $originalPosts;
            $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
            $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '_source`');
            foreach (array_reverse($created) as $table) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $table . '`');
            if ($originalOptimizer !== null) $wpdb->query($wpdb->prepare('SET SESSION optimizer_switch=%s', $originalOptimizer));
        }
    }
}
