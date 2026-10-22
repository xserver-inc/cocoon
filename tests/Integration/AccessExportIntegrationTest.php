<?php
/**
 * 500記事を超えるランキングの書き出しと日次集計の実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportIntegrationTest extends IntegrationTestCase
{
    /**
     * 636記事810PVを省略せず書き出し、画面側の上限と過去の記録を維持することの確認
     */
    public function testRankingExportIncludesArticlesAfterTheFiveHundredth(): void
    {
        global $wpdb;
        create_accesses_table();
        $templateId = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'ランキング全件出力'));
        $template = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID=%d", $templateId), ARRAY_A);
        $originalPosts = $wpdb->posts;
        $temporaryPosts = $originalPosts . '_pv_export';
        $ids = array();
        $date = '2098-10-04';
        $temporaryAccessesCreated = false;
        $temporaryPostsCreated = false;
        try {
            // 投稿保存フックや既存データに依存しない、接続単位の一時データ
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $temporaryAccessesCreated = true;
            $this->assertNotFalse($wpdb->query("CREATE TEMPORARY TABLE `{$temporaryPosts}` LIKE `{$originalPosts}`"));
            $temporaryPostsCreated = true;
            $wpdb->posts = $temporaryPosts;
            for ($i = 0; $i < 636; $i++) {
                $id = 1000000000 + $i;
                $fixture = array_merge($template, array('ID' => $id, 'post_title' => 'ランキング全件出力 ' . $i, 'post_name' => 'pv-export-' . $i));
                $this->assertNotFalse($wpdb->insert($temporaryPosts, $fixture));
                $ids[] = $id;
                insert_accesses_record(array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => $i < 174 ? 2 : 1, 'last_ip' => '192.0.2.1'));
            }
            cocoon_analytics_flush_cache();
            $screen = cocoon_analytics_ranking($date, $date, null, 500);
            $this->assertCount(500, $screen);
            $this->assertSame(674, array_sum(array_column($screen, 'pv')));
            $iterator = cocoon_analytics_export_ranking_rows($date, $date);
            // 分割出力中の順位変動を想定した、スナップショット作成後のPV変更
            $wpdb->query($wpdb->prepare('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=999 WHERE post_id=%d', $ids[0]));
            $export = iterator_to_array($iterator, false);
            $this->assertCount(636, $export);
            $this->assertSame(810, array_sum(array_column($export, 3)));
            $this->assertEqualsCanonicalizing($ids, array_column($export, 0));
            $this->assertSame(array_column($screen, 'post_id'), array_slice(array_column($export, 0), 0, 500));
            $wpdb->query($wpdb->prepare('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=2 WHERE post_id=%d', $ids[0]));
            $this->assertSame(810, cocoon_analytics_daily_pv($date, $date)[0]['pv']);
        } finally {
            foreach ($ids as $id) wp_cache_delete($id, 'posts');
            $wpdb->posts = $originalPosts;
            if ($temporaryPostsCreated) $wpdb->query("DROP TEMPORARY TABLE `{$temporaryPosts}`");
            if ($temporaryAccessesCreated) $wpdb->query('DROP TEMPORARY TABLE `' . ACCESSES_TABLE_NAME . '`');
            cocoon_analytics_flush_cache();
        }
    }

    /**
     * DB名付き投稿表と取得上限0・1・10件でも、DB・ファイル両経路で236記事を取得することの確認
     */
    public function testRankingSnapshotIgnoresTheSessionSelectLimitWithoutChangingIt(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $originalPosts = $wpdb->posts;
        $originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1');
        $posts = 'cocoon_pv_select_limit_posts';
        $snapshot = 'cocoon_pv_select_limit_snapshot';
        $created = array();
        $date = '2098-10-07';
        try {
            // 永続データを変更しない、接続専用のアクセス記録と公開記事の準備
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $created[] = ACCESSES_TABLE_NAME;
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE `' . $posts . '` (ID BIGINT PRIMARY KEY, post_type VARCHAR(126), post_date DATETIME, post_status VARCHAR(20))'));
            $created[] = $posts;
            // 独自DB振り分けでも有効な、DB名で修飾した投稿テーブルの参照
            $wpdb->posts = DB_NAME . '.' . $posts;
            for ($offset = 0; $offset < 236; $offset += 100) {
                $postValues = array();
                $accessValues = array();
                for ($i = $offset; $i < min(236, $offset + 100); $i++) {
                    $id = 1460000000 + $i;
                    $postValues[] = $wpdb->prepare('(%d,%s,%s,%s)', $id, 'post', '2098-10-0' . (1 + $i % 5) . ' 00:00:00', 'publish');
                    $accessValues[] = $wpdb->prepare('(%d,%s,%s,%d,%s)', $id, 'post', $date, 1 + $i % 11, '192.0.2.1');
                }
                $this->assertNotFalse($wpdb->query('INSERT INTO `' . $posts . '` (ID,post_type,post_date,post_status) VALUES ' . implode(',', $postValues)));
                $this->assertNotFalse($wpdb->query('INSERT INTO `' . ACCESSES_TABLE_NAME . '` (post_id,post_type,date,count,last_ip) VALUES ' . implode(',', $accessValues)));
            }
            // 明示LIMITを持つ既存ランキングとの、全記事・同PV時の順序の比較
            $expected = cocoon_analytics_ranking_batch($date, $date, null, 500, 0);
            $this->assertCount(236, $expected);
            foreach (array(0, 1, 10) as $limit) {
                $this->assertNotFalse($wpdb->query('SET SESSION sql_select_limit=' . $limit));
                $this->assertTrue(cocoon_analytics_database_is_autocommit($wpdb));
                $this->assertTrue(cocoon_analytics_create_export_snapshot($wpdb, $snapshot, $date, $date), $wpdb->last_error);
                $stream = cocoon_analytics_create_export_stream($wpdb, $date, $date);
                $this->assertIsResource($stream);
                try {
                    $this->assertSame((string) $limit, (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'));
                    // 順位固定後のPV変更を含めた、両経路の全分割分の欠落と値の変動の検証
                    $this->assertNotFalse($wpdb->query('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=99999 WHERE post_id=1460000000'));
                    $rows = $wpdb->get_results('SELECT post_id,post_type,pv FROM `' . $snapshot . '` ORDER BY export_position LIMIT 1000', ARRAY_A);
                    foreach ($rows as &$row) {
                        $row['post_id'] = (int) $row['post_id'];
                        $row['pv'] = (int) $row['pv'];
                    }
                    unset($row);
                    $fileRows = array();
                    while (($row = cocoon_analytics_export_read_record($stream)) !== null) {
                        $this->assertIsArray($row);
                        $fileRows[] = array('post_id' => (int) $row['post_id'], 'post_type' => $row['post_type'], 'pv' => (int) $row['pv']);
                    }
                    $this->assertSame($expected, $rows, 'DB経路・取得上限=' . $limit);
                    $this->assertSame($expected, $fileRows, 'ファイル経路・取得上限=' . $limit);
                    $this->assertSame(236, count(array_unique(array_column($rows, 'post_id'))));
                    $this->assertSame(array_sum(array_column($expected, 'pv')), array_sum(array_column($rows, 'pv')));
                    $this->assertSame('', $wpdb->last_error);
                } finally {
                    fclose($stream);
                    $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
                    $wpdb->query('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=1 WHERE post_id=1460000000');
                }
            }
        } finally {
            // 例外や検証失敗にも影響を残さない、元の取得上限と論理テーブルの復元
            $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
            $wpdb->posts = $originalPosts;
            $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
            foreach (array_reverse($created) as $table) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $table . '`');
        }
        $this->assertSame($originalLimit, (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'));
    }

    /**
     * 64KiB本文の記事1,500件を128MiB制限の実ハンドラーからCSV出力することの確認
     */
    public function testRankingCsvHandlerStreamsLargeArticlesWithinMemoryLimit(): void
    {
        $output = $this->runExportHandler('csv');
        $this->assertStringStartsWith("\xEF\xBB\xBF", $output);
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, substr($output, 3));
        rewind($stream);
        try {
            $this->assertSame(array('post_id', 'post_type', 'title', 'pv', 'url'), fgetcsv($stream, 0, ',', '"', '\\'));
            $rows = array();
            while (($row = fgetcsv($stream, 0, ',', '"', '\\')) !== false) $rows[] = $row;
            $this->assertCount(1500, $rows);
            $this->assertSame(1500, count(array_unique(array_column($rows, 0))));
            $this->assertSame(1500, array_sum(array_column($rows, 3)));
            $this->assertSame("'=ランキング & 引用 \"試験\"", $rows[0][2]);
            $this->assertNotEmpty($rows[0][4]);
        } finally {
            fclose($stream);
        }
    }

    /**
     * 64KiB本文の記事1,500件を128MiB制限の実ハンドラーからJSON出力することの確認
     */
    public function testRankingJsonHandlerStreamsLargeArticlesWithinMemoryLimit(): void
    {
        $output = $this->runExportHandler('json');
        $data = json_decode($output, true);
        $this->assertIsArray($data, substr($output, -1000));
        $this->assertSame(array('post_id', 'post_type', 'title', 'pv', 'url'), $data['columns']);
        $this->assertSame('ranking', $data['meta']['target']);
        $this->assertSame('2098-10-04', $data['meta']['from']);
        $this->assertSame('2098-10-04', $data['meta']['to']);
        $this->assertNotEmpty($data['meta']['generated_at']);
        $this->assertCount(1500, $data['data']);
        $this->assertSame(1500, count(array_unique(array_column($data['data'], 'post_id'))));
        $this->assertSame(1500, array_sum(array_column($data['data'], 'pv')));
        $this->assertSame('=ランキング & 引用 "試験"', $data['data'][0]['title']);
        $this->assertNotEmpty($data['data'][0]['url']);
    }

    /**
     * 一時テーブル権限がない場合の、ファイル経路によるCSV全件出力の確認
     */
    public function testRankingCsvHandlerFallsBackWhenTemporaryTableCreationIsDenied(): void
    {
        $output = $this->runExportHandler('csv-denied');
        $this->assertStringStartsWith("\xEF\xBB\xBF", $output);
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, substr($output, 3));
        rewind($stream);
        try {
            $this->assertSame(array('post_id', 'post_type', 'title', 'pv', 'url'), fgetcsv($stream, 0, ',', '"', '\\'));
            $rows = array();
            while (($row = fgetcsv($stream, 0, ',', '"', '\\')) !== false) $rows[] = $row;
            $this->assertCount(1500, $rows);
            $this->assertSame(1500, count(array_unique(array_column($rows, 0))));
            $this->assertSame(1500, array_sum(array_column($rows, 3)));
            $this->assertSame("'=ランキング & 引用 \"試験\"", $rows[0][2]);
            $this->assertNotEmpty($rows[0][4]);
        } finally {
            fclose($stream);
        }
    }

    public function testRankingJsonHandlerFallsBackWhenTemporaryTableCreationIsDenied(): void
    {
        $data = json_decode($this->runExportHandler('json-denied'), true);
        $this->assertIsArray($data);
        $this->assertSame(array('post_id', 'post_type', 'title', 'pv', 'url'), $data['columns']);
        $this->assertCount(1500, $data['data']);
        $this->assertSame(1500, count(array_unique(array_column($data['data'], 'post_id'))));
        $this->assertSame(1500, array_sum(array_column($data['data'], 'pv')));
        $this->assertSame('=ランキング & 引用 "試験"', $data['data'][0]['title']);
        $this->assertNotEmpty($data['data'][0]['url']);
    }

    /**
     * ソース読み取りエラーを成功扱いにしない、CSV出力開始前の明示的な失敗確認
     */
    public function testRankingHandlerRejectsSourceFailureBeforeStartingCsv(): void
    {
        $output = $this->runExportHandler('source-error');
        $this->assertStringNotContainsString("\xEF\xBB\xBF", $output);
        $error = json_decode($output, true);
        $this->assertIsArray($error, substr($output, -1000));
        $this->assertSame(500, $error['response']);
        $this->assertSame(__('データの取得に失敗しました。', THEME_NAME), $error['error']);
    }

    /**
     * 作成拒否の数値コードと例外型ドライバーを含む、作成段階別の自動切り替えの確認
     */
    public function testTemporaryTableCreationRestrictionsUseTheFileFallbackAndReleaseResources(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $id = $this->createPost(array('post_status' => 'publish', 'post_title' => '一時表作成拒否の代替出力'));
        $date = '2098-10-11';
        $this->assertNotFalse(insert_accesses_record(array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => 7, 'last_ip' => '192.0.2.1')));
        $suppressed = $wpdb->suppress_errors;
        try {
            foreach (array(1044, 1142, 1227, 1787, 3748) as $code) {
                foreach (array('stage', 'snapshot') as $phase) {
                    foreach (array('wpdb', 'runtime', 'pdo') as $exceptionDriver) {
                        $active = array();
                        $denials = 0;
                        $beforeStreams = array_keys(get_resources('stream'));
                        $intercept = static function ($sql) use ($code, $phase, $exceptionDriver, &$active, &$denials) {
                            if (preg_match('/^CREATE TEMPORARY TABLE `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) {
                                $isStage = substr($match[1], -7) === '_source';
                                if (($phase === 'stage') === $isStage) {
                                    $denials++;
                                    if ($exceptionDriver === 'runtime') throw new \RuntimeException('一時表作成制約の検証例外', $code);
                                    if ($exceptionDriver === 'pdo') {
                                        $error = new \PDOException('PDO一時表作成制約の検証例外', 42000);
                                        $error->errorInfo = array('42000', $code, 'Temporary table creation denied');
                                        throw $error;
                                    }
                                    return "SIGNAL SQLSTATE 'HY000' SET MYSQL_ERRNO=" . $code . ", MESSAGE_TEXT='Temporary table creation denied'";
                                }
                                $active[$match[1]] = true;
                            }
                            if (preg_match('/^DROP TEMPORARY TABLE IF EXISTS `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) unset($active[$match[1]]);
                            return $sql;
                        };
                        add_filter('query', $intercept);
                        try {
                            $iterator = cocoon_analytics_export_ranking_rows($date, $date);
                            $this->assertInstanceOf(\Generator::class, $iterator);
                            $rows = iterator_to_array($iterator, false);
                            unset($iterator);
                            $this->assertCount(1, $rows);
                            $this->assertSame($id, $rows[0][0]);
                            $this->assertSame('一時表作成拒否の代替出力', $rows[0][2]);
                            $this->assertSame(7, $rows[0][3]);
                            $this->assertSame(1, $denials);
                            $this->assertSame(array(), $active);
                            $this->assertSame(array(), array_values(array_diff(array_keys(get_resources('stream')), $beforeStreams)));
                            $this->assertSame($suppressed, $wpdb->suppress_errors);
                        } finally {
                            remove_filter('query', $intercept);
                            if (isset($iterator)) unset($iterator);
                        }
                    }
                }
            }
        } finally {
            $wpdb->query($wpdb->prepare('DELETE FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id));
        }
    }

    /**
     * SQLSTATE単独・不正なドライバー番号・通常のSQL失敗を代替出力へ切り替えないことの確認
     */
    public function testUnknownCreationExceptionsDoNotHideSqlErrorsWithTheFileFallback(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $suppressed = $wpdb->suppress_errors;
        foreach (array(null, array('42000'), array('42000', null), array('42000', '1044oops'), array('42000', '1044.0'), array('42000', 1146)) as $errorInfo) {
            $error = new \PDOException('PDO作成エラーの検証例外', 42000);
            $error->errorInfo = $errorInfo;
            $denials = 0;
            $sourceReads = 0;
            $intercept = static function ($sql) use ($error, &$denials, &$sourceReads) {
                if (strpos($sql, ACCESSES_TABLE_NAME) !== false && preg_match('/SUM\(\s*(?:[a-zA-Z_]\w*\.)?`?count`?\s*\)/i', $sql)) $sourceReads++;
                if (preg_match('/^CREATE TEMPORARY TABLE `cocoon_pv_export_[a-f0-9]+_source`/', $sql)) {
                    $denials++;
                    throw $error;
                }
                return $sql;
            };
            add_filter('query', $intercept);
            try {
                $result = cocoon_analytics_export_ranking_rows('2098-10-12', '2098-10-12');
                $this->assertInstanceOf(\WP_Error::class, $result);
                $this->assertSame(1, $denials);
                $this->assertSame(0, $sourceReads);
                $this->assertSame($suppressed, $wpdb->suppress_errors);
            } finally {
                remove_filter('query', $intercept);
            }
        }
    }

    /**
     * 行の反復を途中終了した場合と例外発生時のキャッシュ設定の復元確認
     */
    public function testRankingIteratorRestoresCacheSettingsBeforeYieldAndAfterFailure(): void
    {
        global $wpdb;
        $id = $this->createPost(array('post_status' => 'publish', 'post_title' => 'キャッシュ復元'));
        $date = '2098-10-05';
        create_accesses_table();
        insert_accesses_record(array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => 1, 'last_ip' => '192.0.2.1'));
        $original = wp_suspend_cache_addition();
        $originalTermFilterCount = $this->termQueryFilterCount();
        $activeSnapshots = array();
        $observe = static function ($sql) use (&$activeSnapshots) {
            if (preg_match('/^CREATE TEMPORARY TABLE `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) $activeSnapshots[$match[1]] = true;
            if (preg_match('/^DROP TEMPORARY TABLE IF EXISTS `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) unset($activeSnapshots[$match[1]]);
            return $sql;
        };
        add_filter('query', $observe);
        try {
            $unstarted = cocoon_analytics_export_ranking_rows($date, $date);
            $this->assertCount(1, $activeSnapshots);
            unset($unstarted);
            $this->assertSame(array(), $activeSnapshots);
            foreach (array(false, true) as $suspended) {
                wp_suspend_cache_addition($suspended);
                wp_cache_delete($id, 'posts');
                $iterator = cocoon_analytics_export_ranking_rows($date, $date);
                $this->assertSame($id, $iterator->current()[0]);
                $this->assertSame($suspended, wp_suspend_cache_addition());
                $this->assertSame($originalTermFilterCount, $this->termQueryFilterCount());
                $this->assertFalse(wp_cache_get($id, 'posts'));
                unset($iterator);
                $this->assertSame($suspended, wp_suspend_cache_addition());
                $this->assertSame(array(), $activeSnapshots);
                $this->assertSame($originalTermFilterCount, $this->termQueryFilterCount());
            }
            wp_suspend_cache_addition(false);
            $failure = static function () { throw new \RuntimeException('タイトル処理の検証例外'); };
            add_filter('the_title', $failure);
            try {
                $iterator = cocoon_analytics_export_ranking_rows($date, $date);
                $iterator->current();
                $this->fail('タイトル処理の例外が発生しませんでした。');
            } catch (\RuntimeException $e) {
                $this->assertSame('タイトル処理の検証例外', $e->getMessage());
                $this->assertFalse(wp_suspend_cache_addition());
                $this->assertSame($originalTermFilterCount, $this->termQueryFilterCount());
                $this->assertSame(array(), $activeSnapshots);
            } finally {
                remove_filter('the_title', $failure);
            }
        } finally {
            remove_filter('query', $observe);
            wp_suspend_cache_addition($original);
            $wpdb->query($wpdb->prepare('DELETE FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id));
        }
    }

    /**
     * 集計取得・作業用データの保存・順位確定の失敗時の解放と設定復元の確認
     */
    public function testSnapshotPreparationFailuresReleaseBothTemporaryTables(): void
    {
        global $wpdb;
        create_accesses_table();
        $id = $this->createPost(array('post_status' => 'publish', 'post_title' => '書き出し準備の失敗検証'));
        $date = '2098-10-06';
        insert_accesses_record(array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => 1, 'last_ip' => '192.0.2.1'));
        $suppressed = $wpdb->suppress_errors;
        try {
            foreach (array('stage', 'source', 'insert', 'snapshot') as $phase) {
                $active = array();
                $failed = false;
                $intercept = static function($sql) use ($phase, &$active, &$failed) {
                    $matchPhase = ($phase === 'stage' && preg_match('/^CREATE TEMPORARY TABLE `cocoon_pv_export_[a-f0-9]+_source`/', $sql))
                        || ($phase === 'source' && strpos($sql, ACCESSES_TABLE_NAME) !== false && preg_match('/SUM\(\s*(?:[a-zA-Z_]\w*\.)?`?count`?\s*\)/i', $sql))
                        || ($phase === 'insert' && strpos($sql, 'INSERT INTO `cocoon_pv_export_') === 0)
                        || ($phase === 'snapshot' && strpos($sql, ' AS SELECT post_id, post_type, pv FROM ') !== false);
                    if (!$failed && $matchPhase) {
                        $failed = true;
                        return 'SELECT * FROM `cocoon_export_preparation_missing_table`';
                    }
                    if (preg_match('/^CREATE TEMPORARY TABLE `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) $active[$match[1]] = true;
                    if (preg_match('/^DROP TEMPORARY TABLE IF EXISTS `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) unset($active[$match[1]]);
                    return $sql;
                };
                add_filter('query', $intercept);
                try {
                    $result = cocoon_analytics_export_ranking_rows($date, $date);
                    $this->assertTrue($failed, $phase);
                    $this->assertInstanceOf(\WP_Error::class, $result, $phase);
                    $this->assertSame(array(), $active, $phase);
                    $this->assertSame($suppressed, $wpdb->suppress_errors, $phase);
                } finally {
                    remove_filter('query', $intercept);
                }
            }
        } finally {
            $wpdb->query($wpdb->prepare('DELETE FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id));
        }
    }

    private function termQueryFilterCount(): int
    {
        if (!isset($GLOBALS['wp_filter']['get_terms_args'])) return 0;
        $count = 0;
        foreach ($GLOBALS['wp_filter']['get_terms_args']->callbacks as $callbacks) $count += count($callbacks);
        return $count;
    }

    private function runExportHandler(string $format): string
    {
        global $wpdb;
        create_accesses_table();
        // 管理画面の起動時に参照する、テスト専用DB内のテーマ専用テーブルの準備
        create_speech_balloons_table();
        create_function_texts_table();
        create_affiliate_tags_table();
        create_item_rankings_table();
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '書き出しの雛形'));
        $template = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID=%d", $id), ARRAY_A);
        $environment = getenv();
        $environment['COCOON_EXPORT_TEST_WORKER'] = '1';
        $environment['COCOON_EXPORT_TEST_WP_ROOT'] = ABSPATH;
        // コマンド引数やログへの露出を避けた、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, '-d', 'memory_limit=128M', dirname(__DIR__) . '/fixtures/access-export-worker.php', $format);
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('redirect', 1)), $pipes, null, $this->workerEnvironment($environment));
        $this->assertIsResource($process);
        try {
            fwrite($pipes[0], json_encode($template) . "\n");
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            $output = '';
            $deadline = microtime(true) + 240;
            while (!feof($pipes[1])) {
                $this->assertLessThan($deadline, microtime(true), '書き出しワーカーの実行時間の超過');
                $read = array($pipes[1]);
                $write = null;
                $except = null;
                $ready = stream_select($read, $write, $except, 1);
                $this->assertNotFalse($ready);
                if ($ready > 0) $output .= stream_get_contents($pipes[1]);
            }
            fclose($pipes[1]);
            $this->assertSame(0, proc_close($process), substr($output, -1000));
            return $output;
        } finally {
            foreach ($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);
            if (is_resource($process)) {
                $status = proc_get_status($process);
                if ($status['running']) proc_terminate($process);
                proc_close($process);
            }
        }
    }
}
