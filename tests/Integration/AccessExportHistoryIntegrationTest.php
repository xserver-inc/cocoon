<?php
/**
 * 過去の履歴が多いランキング書き出しの、単一集計と走査量の実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportHistoryIntegrationTest extends IntegrationTestCase
{
    /**
     * 短期間・全期間・記事IDが偏る条件でも、単一の索引走査で全件を取得することの検証
     */
    public function testShortAndFullPeriodsAvoidUnnecessaryHistoryScans(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $originalPosts = $wpdb->posts;
        $posts = 'cocoon_pv_history_posts';
        $snapshot = 'cocoon_pv_history_snapshot';
        $created = array();
        $sourceQueries = array();
        $observe = static function ($sql) use (&$sourceQueries) {
            if (strpos($sql, 'SELECT totals.post_id, p.post_type, p.post_date, totals.pv') === 0) $sourceQueries[] = $sql;
            return $sql;
        };
        try {
            // 実データや他のテストへ影響を残さない、接続専用の合成テーブル
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $created[] = ACCESSES_TABLE_NAME;
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE `' . $posts . '` (ID BIGINT PRIMARY KEY, post_type VARCHAR(126), post_date DATETIME, post_status VARCHAR(20))'));
            $created[] = $posts;
            $wpdb->posts = $posts;
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE cocoon_pv_history_seq (n INT PRIMARY KEY)'));
            $created[] = 'cocoon_pv_history_seq';
            $values = array();
            for ($i = 0; $i < 1000; $i++) $values[] = '(' . $i . ')';
            $this->assertNotFalse($wpdb->query('INSERT INTO cocoon_pv_history_seq VALUES ' . implode(',', $values)));
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE cocoon_pv_history_days LIKE cocoon_pv_history_seq'));
            $created[] = 'cocoon_pv_history_days';
            $this->assertNotFalse($wpdb->query('INSERT INTO cocoon_pv_history_days SELECT n FROM cocoon_pv_history_seq WHERE n < 300'));
            $this->assertSame(1000, $wpdb->query("INSERT INTO `{$posts}` SELECT 1400000000+n,'post','2090-01-01 00:00:00','publish' FROM cocoon_pv_history_seq"));
            // 1000記事と300日分による、1日指定と全期間指定を同じ履歴で比べる条件
            $this->assertSame(300000, $wpdb->query("INSERT INTO `" . ACCESSES_TABLE_NAME . "` (post_id,post_type,date,count,last_ip,last_accessed_at) SELECT 1400000000+p.n,'post',DATE_ADD('2090-01-01',INTERVAL d.n DAY),1,'192.0.2.1',0 FROM cocoon_pv_history_seq p CROSS JOIN cocoon_pv_history_days d"));
            add_filter('query', $observe);
            foreach (array('day', 'all') as $period) {
                $sourceQueries = array();
                $beforeReads = $this->indexReads();
                $from = $period === 'day' ? '2090-10-27' : '2090-01-01';
                $this->assertTrue(cocoon_analytics_create_export_snapshot($wpdb, $snapshot, $from, '2090-10-27'), $wpdb->last_error);
                $reads = $this->indexReads() - $beforeReads;
                $rows = $wpdb->get_results('SELECT post_id,pv FROM `' . $snapshot . '` ORDER BY export_position', ARRAY_A);
                $this->assertCount(1000, $rows);
                $this->assertSame(1000, count(array_unique(array_column($rows, 'post_id'))));
                $this->assertSame($period === 'day' ? 1000 : 300000, array_sum(array_column($rows, 'pv')));
                $this->assertSame('1400000999', $rows[0]['post_id']);
                $this->assertSame('1400000000', $rows[999]['post_id']);
                $this->assertCount(1, $sourceQueries);
                // 実行速度の揺れに依存しない、単一集計のカバリング索引と走査量の検証
                $plan = $this->coveringSourcePlan($sourceQueries[0]);
                if ($period === 'day') $this->assertLessThanOrEqual(2000, (int) $plan['rows']);
                // 集計後の一時表処理も含めた、従来の読み取り上限の維持
                $this->assertLessThanOrEqual($period === 'day' ? 30000 : 650000, $reads, $period . 'の履歴再走査の増加');
                $this->assertNotFalse($wpdb->query('DROP TEMPORARY TABLE `' . $snapshot . '`'));
            }
            // 低IDの過去履歴と高IDの新記事が偏る、記事ID索引では不要な履歴走査が増える条件
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE cocoon_pv_history_digits LIKE cocoon_pv_history_seq'));
            $created[] = 'cocoon_pv_history_digits';
            $this->assertNotFalse($wpdb->query('INSERT INTO cocoon_pv_history_digits SELECT n FROM cocoon_pv_history_seq WHERE n<10'));
            $this->assertSame(10000, $wpdb->query("INSERT INTO `{$posts}` SELECT 1450000000+p.n*10+d.n,'post','2091-01-01 00:00:00','publish' FROM cocoon_pv_history_seq p CROSS JOIN cocoon_pv_history_digits d"));
            $this->assertSame(10000, $wpdb->query("INSERT INTO `" . ACCESSES_TABLE_NAME . "` (post_id,post_type,date,count,last_ip,last_accessed_at) SELECT 1450000000+p.n*10+d.n,'post','2091-01-01',1,'192.0.2.1',0 FROM cocoon_pv_history_seq p CROSS JOIN cocoon_pv_history_digits d"));
            $sourceQueries = array();
            $beforeReads = $this->indexReads();
            $this->assertTrue(cocoon_analytics_create_export_snapshot($wpdb, $snapshot, '2091-01-01', '2091-01-01'), $wpdb->last_error);
            $reads = $this->indexReads() - $beforeReads;
            $rows = $wpdb->get_results('SELECT post_id,pv FROM `' . $snapshot . '` ORDER BY export_position', ARRAY_A);
            $this->assertCount(10000, $rows);
            $this->assertSame(10000, count(array_unique(array_column($rows, 'post_id'))));
            $this->assertSame(10000, array_sum(array_column($rows, 'pv')));
            $this->assertSame('1450009999', $rows[0]['post_id']);
            $this->assertSame('1450000000', $rows[9999]['post_id']);
            $this->assertCount(1, $sourceQueries);
            $this->coveringSourcePlan($sourceQueries[0]);
            $this->assertLessThanOrEqual(50000, $reads, '対象日1万行の繰り返し走査の再発');
        } finally {
            remove_filter('query', $observe);
            $wpdb->posts = $originalPosts;
            $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
            foreach (array_reverse($created) as $table) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $table . '`');
        }
    }

    /**
     * 接続既定のEXPLAIN形式に依存せず、警告も設定変更もなく書き出すことの検証
     */
    public function testSessionExplainFormatsDoNotProduceWarningsOrChangeResults(): void
    {
        global $wpdb;
        $server = (string) $wpdb->get_var('SELECT VERSION()');
        if (stripos($server, 'MariaDB') !== false || version_compare($wpdb->db_version(), '8.0.32', '<')) {
            $this->markTestSkipped('既定のEXPLAIN形式を変更できるMySQL 8.0.32以降での検証');
        }
        $this->assertTrue(create_accesses_table());
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '実行計画の出力形式'));
        $date = '2099-10-22';
        $originalFormat = $wpdb->get_var('SELECT @@SESSION.explain_format');
        $snapshot = 'cocoon_pv_format_snapshot';
        $this->assertNotFalse(insert_accesses_record(array('post_id' => $id, 'post_type' => 'post', 'date' => $date, 'count' => 7, 'last_ip' => '192.0.2.1')));
        try {
            foreach (array('JSON', 'TREE', 'TRADITIONAL') as $format) {
                $this->assertNotFalse($wpdb->query($wpdb->prepare('SET SESSION explain_format=%s', $format)));
                ob_start();
                try {
                    $this->assertTrue(cocoon_analytics_create_export_snapshot($wpdb, $snapshot, $date, $date), $wpdb->last_error);
                } finally {
                    $output = ob_get_clean();
                }
                $this->assertSame('', $output, '出力形式の違いによる警告の混入');
                $this->assertSame($format, $wpdb->get_var('SELECT @@SESSION.explain_format'));
                $this->assertSame('7', $wpdb->get_var('SELECT pv FROM `' . $snapshot . '` WHERE post_id=' . $id));
                $this->assertNotFalse($wpdb->query('DROP TEMPORARY TABLE `' . $snapshot . '`'));
            }
        } finally {
            $wpdb->query($wpdb->prepare('SET SESSION explain_format=%s', $originalFormat));
            $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
            $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id), array('%d'));
        }
    }

    private function coveringSourcePlan(string $sql): array
    {
        global $wpdb;
        $plans = $wpdb->get_results('EXPLAIN ' . $sql, ARRAY_A);
        $this->assertIsArray($plans);
        foreach ($plans as $plan) {
            if (($plan['key'] ?? null) !== INDEX_ACCESSES_DATE_PTYPE_PID_COUNT) continue;
            $this->assertContains($plan['type'], array('ref', 'range', 'index'));
            $this->assertStringContainsString('Using index', $plan['Extra']);
            return $plan;
        }
        $this->fail('元のアクセス表へのカバリング索引による単一走査の欠落');
    }

    private function indexReads(): int
    {
        global $wpdb;
        $status = $wpdb->get_row("SHOW SESSION STATUS LIKE 'Handler_read_next'", ARRAY_N);
        $this->assertIsArray($status);
        return (int) $status[1];
    }
}
