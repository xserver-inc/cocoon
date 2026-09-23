<?php
/**
 * クリック解析の実データベース統合テスト
 */

namespace Cocoon\Tests\Integration;

class ClickAnalyticsIntegrationTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('create_click_analytics_tables')) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/_loader.php';
        }
        create_click_analytics_tables();
        cocoon_click_delete_all_data();
        cocoon_click_analytics_flush_cache();
    }

    protected function tearDown(): void
    {
        if (function_exists('cocoon_click_delete_all_data')) {
            cocoon_click_delete_all_data();
        }
        parent::tearDown();
    }

    public function testDefaultTrackingCanBeDisabledAndStaysDisabledAfterSchemaUpdate(): void
    {
        $previousMods = get_theme_mods();
        $previousPost = $_POST;
        try {
            remove_theme_mod(OP_CLICK_ANALYTICS_ENABLE);
            update_click_analytics_tables();
            $this->assertTrue(is_click_analytics_enable());

            // チェックを外した設定フォームの送信内容
            $_POST = array();
            cocoon_click_save_settings();
            $this->assertSame(0, get_theme_mod(OP_CLICK_ANALYTICS_ENABLE));

            // 更新処理後も保存済みの無効設定を維持することの確認
            update_click_analytics_tables();
            $this->assertFalse(is_click_analytics_enable());

            $_POST[OP_CLICK_ANALYTICS_ENABLE] = '1';
            cocoon_click_save_settings();
            $this->assertTrue(is_click_analytics_enable());
        } finally {
            $_POST = $previousPost;
            update_option('theme_mods_' . get_stylesheet(), $previousMods);
        }
    }

    public function testDefaultEnabledTimestampAndSamplingRecalculation(): void
    {
        $previousMods = get_theme_mods();
        try {
            remove_theme_mod(OP_CLICK_ANALYTICS_ENABLE);
            remove_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT);
            $before = time();
            cocoon_click_manage_cron_schedule();
            $start = get_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT);
            $this->assertGreaterThanOrEqual($before, cocoon_click_local_timestamp($start));
            cocoon_click_update_sampling_rate();
            $this->assertSame(10, get_click_analytics_sampling_rate());
            $this->assertSame($start, get_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT));
            set_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT, (new \DateTimeImmutable('now', wp_timezone()))->modify('-6 days')->format('Y-m-d H:i:s'));
            cocoon_click_update_sampling_rate();
            $this->assertSame(100, get_click_analytics_sampling_rate());
            set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 0);
            remove_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT);
            cocoon_click_manage_cron_schedule();
            $this->assertFalse(get_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT));
        } finally {
            update_option('theme_mods_' . get_stylesheet(), $previousMods);
        }
    }

    public function testSamplingQueryFailurePreservesLastSuccessfulRate(): void
    {
        global $wpdb;
        $previousMods = get_theme_mods();
        $previousErrors = $wpdb->suppress_errors(true);
        $fail = static function ($sql) {
            return strpos($sql, 'SELECT COALESCE(SUM(weighted_impressions),0) AS pageviews') === 0 ? 'SELECT missing_sampling_column' : $sql;
        };
        try {
            set_theme_mod(OP_CLICK_ANALYTICS_SAMPLING_RATE, 20);
            set_theme_mod(OP_CLICK_ANALYTICS_SAMPLING_UPDATED, '2026-09-01 12:00:00');
            add_filter('query', $fail);
            $this->assertFalse(cocoon_click_update_sampling_rate());
            $this->assertSame(20, get_click_analytics_sampling_rate());
            $this->assertSame('2026-09-01 12:00:00', get_theme_mod(OP_CLICK_ANALYTICS_SAMPLING_UPDATED));
        } finally {
            remove_filter('query', $fail);
            $wpdb->suppress_errors($previousErrors);
            update_option('theme_mods_' . get_stylesheet(), $previousMods);
        }
    }

    public function testDisabledTrackingDoesNotRecreateRejectedRequestHistory(): void
    {
        $previousMods = get_theme_mods();
        try {
            set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 0);
            cocoon_click_health_increment('request_rejected', 10);
            $this->assertSame(0, cocoon_click_rejected_requests_count(true));
        } finally {
            update_option('theme_mods_' . get_stylesheet(), $previousMods);
        }
    }

    private function receiveEvents(array $events, string $batchId, bool $heatmap, int $postId = 0, bool $impressions = true): array
    {
        global $wpdb;
        set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_IMPRESSIONS, $impressions ? 1 : 0);
        set_theme_mod(OP_CLICK_ANALYTICS_HEATMAP, $heatmap ? 1 : 0);
        set_theme_mod(OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN, 0);
        $postId = $postId ?: $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'クリック解析テスト'));
        $layoutRevision = cocoon_click_layout_revision($postId);
        $payload = array(
            'batch_id' => $batchId,
            'session_id' => 'integration-session-0001',
            'source_post_id' => $postId,
            'layout_revision' => $layoutRevision,
            'sampling_rate' => 100,
            'device' => 'desktop',
            'token' => cocoon_click_tracking_token($postId, $layoutRevision, 100),
            'events' => $events,
        );
        $request = new \WP_REST_Request('POST', '/cocoon/v1/click-events');
        $request->set_header('origin', home_url('/'));
        $request->set_header('sec-fetch-site', 'same-origin');
        $request->set_body(wp_json_encode($payload));

        // SQL計測前にWordPress側の投稿・URL・設定キャッシュを温めます。
        get_post($postId);
        get_permalink($postId);
        cocoon_click_tables_exist();
        $before = (int) $wpdb->num_queries;
        $response = cocoon_click_rest_receive_events($request);
        return array($response, (int) $wpdb->num_queries - $before, $postId, $layoutRevision, $request);
    }

    public function testTablesUseCurrentWordPressPrefixAndLayoutPrimaryKey(): void
    {
        global $wpdb;
        $this->assertStringStartsWith($wpdb->prefix, CLICK_STATS_DAILY_TABLE_NAME);
        $existingTables = $wpdb->get_col('SHOW TABLES FROM `' . DB_NAME . '`');
        foreach (array(CLICK_LINKS_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME) as $table) {
            // アンダースコアをワイルドカード扱いするLIKEを避け、取得済み一覧を完全一致で確認します。
            $this->assertContains($table, $existingTables);
        }
        $indexes = $wpdb->get_results('SHOW INDEX FROM `' . CLICK_STATS_DAILY_TABLE_NAME . "` WHERE Key_name='PRIMARY'", ARRAY_A);
        usort($indexes, static function ($left, $right) {
            return (int) $left['Seq_in_index'] <=> (int) $right['Seq_in_index'];
        });
        $this->assertSame(array('stat_date', 'source_post_id', 'link_id', 'device', 'layout_revision'), array_column($indexes, 'Column_name'));
    }

    public function testPostPickerSearchReturnsOnlyPublishedPostsAndPages(): void
    {
        $postId = $this->createPost(array(
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_title' => '初心者向けクリック解析ガイド',
        ));
        $pageId = $this->createPost(array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_title' => 'クリック解析の設定方法',
        ));
        $draftId = $this->createPost(array(
            'post_status' => 'draft',
            'post_type' => 'post',
            'post_title' => 'クリック解析の未公開メモ',
        ));

        $items = cocoon_analytics_search_posts('クリック解析');
        $ids = array_map('intval', array_column($items, 'id'));

        $this->assertContains($postId, $ids);
        $this->assertContains($pageId, $ids);
        $this->assertNotContains($draftId, $ids);
        $this->assertLessThanOrEqual(20, count($items));

        $idItems = cocoon_analytics_search_posts((string) $pageId);
        $this->assertCount(1, $idItems);
        $this->assertSame($pageId, (int) $idItems[0]['id']);

        $draftItems = cocoon_analytics_search_posts((string) $draftId);
        $this->assertSame(array(), $draftItems);
    }

    public function testAtomicUpsertSeparatesLayoutRevisions(): void
    {
        global $wpdb;
        $rows = array();
        $date = current_time('Y-m-d');
        $revision_a = str_repeat('a', 64);
        $revision_b = str_repeat('b', 64);
        cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', $revision_a, array('clicks' => 1));
        cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', $revision_b, array('clicks' => 2));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        cocoon_click_upsert_stats(array(reset($rows)), current_time('mysql'));

        $stored = $wpdb->get_results('SELECT layout_revision,clicks FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '` ORDER BY layout_revision', ARRAY_A);
        $this->assertCount(2, $stored);
        $this->assertSame(2, (int) $stored[0]['clicks']);
        $this->assertSame(2, (int) $stored[1]['clicks']);
    }

    public function testBatchReplayIsRejected(): void
    {
        $now = current_time('mysql');
        $first = cocoon_click_accept_batch('integration-batch-0001', $now);
        $second = cocoon_click_accept_batch('integration-batch-0001', $now);
        $this->assertTrue($first['accepted']);
        $this->assertFalse($second['accepted']);
        $this->assertSame($first['batch_key'], $second['batch_key']);
    }

    public function testSessionUniqueCountsOnlyOncePerDayAndLink(): void
    {
        $date = current_time('Y-m-d');
        $now = current_time('mysql');
        $first = cocoon_click_insert_uniques($date, 'integration-session-0001', str_repeat('a', 64), array(20), $now);
        $second = cocoon_click_insert_uniques($date, 'integration-session-0001', str_repeat('b', 64), array(20), $now);
        $this->assertSame(1, $first[20]);
        $this->assertSame(0, $second[20]);
    }

    public function testMonthlyRollupIsIdempotentAndKeepsLayoutRevision(): void
    {
        global $wpdb;
        $date = gmdate('Y-m-15', strtotime(current_time('Y-m-01') . ' -1 month'));
        $revision = str_repeat('c', 64);
        $rows = array();
        cocoon_click_add_stat_row($rows, $date, 10, 20, 'mobile', $revision, array('clicks' => 3, 'unique_clicks' => 2));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $first = cocoon_click_rollup_monthly();
        $second = cocoon_click_rollup_monthly();

        $monthly = $wpdb->get_row('SELECT layout_revision,clicks,unique_clicks FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`', ARRAY_A);
        $this->assertSame('success', $first['status']);
        $this->assertSame('success', $second['status']);
        $this->assertSame($revision, $monthly['layout_revision']);
        $this->assertSame(3, (int) $monthly['clicks']);
        $this->assertSame(2, (int) $monthly['unique_clicks']);
    }

    public function testRetentionDeletionIsLimitedAndUsesStrictCutoff(): void
    {
        global $wpdb;
        $now = current_time('mysql');
        $rows = array();
        cocoon_click_add_stat_row($rows, '2026-01-01', 10, 20, 'desktop', str_repeat('d', 64), array('clicks' => 1));
        cocoon_click_add_stat_row($rows, '2026-01-02', 10, 20, 'desktop', str_repeat('d', 64), array('clicks' => 1));
        cocoon_click_upsert_stats($rows, $now);

        $deleted = cocoon_click_delete_limited(CLICK_STATS_DAILY_TABLE_NAME, 'stat_date', '2026-01-02');
        $this->assertSame(1, (int) $deleted);
        $this->assertSame('2026-01-02', $wpdb->get_var('SELECT stat_date FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
    }

    public function testSingleClickHasBoundedQueriesIncludingSafetyChecks(): void
    {
        $event = array(
            'type' => 'click', 'href' => 'https://outside.example/offer', 'area' => 'content', 'heading' => '見出し',
            'occurrence' => 0, 'element_type' => 'text', 'label' => '外部リンク', 'sampled' => true,
            'forced_impression' => true, 'time_to_click_ms' => 1200, 'x_bp' => 5000, 'y_bp' => 6000,
        );
        list($response, $queries) = $this->receiveEvents(array($event), 'integration-click-0001', true);
        $this->assertSame(204, $response->get_status());
        // レート制限・定義数確認・トランザクションの固定費を含めた上限です。
        $this->assertLessThanOrEqual(28, $queries);
    }

    public function testFiftyImpressionsAreStillInsertedAsOneBatch(): void
    {
        $events = array();
        for ($index = 0; $index < 50; $index++) {
            $events[] = array(
                'type' => 'impression', 'href' => 'https://outside.example/link-' . $index, 'area' => 'content',
                'occurrence' => $index, 'element_type' => 'text', 'label' => 'リンク' . $index,
            );
        }
        list($response, $queries) = $this->receiveEvents($events, 'integration-impressions-0001', false);
        $this->assertSame(204, $response->get_status());
        $this->assertLessThanOrEqual(26, $queries);
    }

    private function clickEvent(string $url = 'https://outside.test/offer'): array
    {
        return array('type' => 'click', 'href' => $url, 'area' => 'content', 'label' => 'テストリンク',
            'sampled' => true, 'forced_impression' => true, 'x_bp' => 5000, 'y_bp' => 5000);
    }

    public function testFailedWritesRollBackAndCanBeRetried(): void
    {
        global $wpdb;
        // 保存の各段階を個別に失敗させ、先行した書き込みも残らないことを確認します。
        foreach (array(CLICK_BATCHES_TABLE_NAME, CLICK_LINKS_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME) as $table) {
            cocoon_click_delete_all_data();
            $fail = static function ($sql) use ($table) {
                return preg_match('/^INSERT(?: IGNORE)? INTO `' . preg_quote($table, '/') . '`/', $sql) ? 'INVALID CLICK AUDIT QUERY' : $sql;
            };
            $previous = $wpdb->suppress_errors(true);
            add_filter('query', $fail);
            try {
                list($response, , , , $request) = $this->receiveEvents(array($this->clickEvent()), 'retry-' . md5($table), true);
            } finally {
                remove_filter('query', $fail);
                $wpdb->suppress_errors($previous);
            }
            $this->assertWPErrorStatus(503, $response);
            foreach (array(CLICK_BATCHES_TABLE_NAME, CLICK_LINKS_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME) as $emptyTable) {
                $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . $emptyTable . '`'), $table . 'で失敗した後に' . $emptyTable . 'へ行が残っています');
            }
            $this->assertSame(204, cocoon_click_rest_receive_events($request)->get_status());
            $this->assertSame(204, cocoon_click_rest_receive_events($request)->get_status());
            $this->assertSame('1', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        }
    }

    private function assertWPErrorStatus(int $status, $response): void
    {
        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertSame($status, $response->get_error_data()['status']);
    }

    public function testRateLimitWorksWithoutPersistentObjectCache(): void
    {
        $this->assertFalse((bool) wp_using_ext_object_cache());
        // 分境界で正しくリセットされた結果と、同じ分内の制限不良の区別
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $minute = (int) floor(time() / 60);
            $results = array();
            for ($index = 0; $index < 31; $index++) $results[] = cocoon_click_rate_limit_allows('limit-session-' . $attempt);
            if ((int) floor(time() / 60) !== $minute) continue;
            $this->assertSame(array_merge(array_fill(0, 30, true), array(false)), $results);
            return;
        }
        $this->fail('実行が毎回分境界をまたいだため、同一分内のレート制限を検証できませんでした。');
    }

    public function testDefinitionLimitAllowsExistingLinksButRejectsNewOnes(): void
    {
        global $wpdb;
        $cap = static function () { return 1; };
        add_filter('cocoon_click_analytics_post_definition_limit', $cap);
        try {
            list($first, , $postId) = $this->receiveEvents(array($this->clickEvent()), 'definition-first-001', false);
            $this->assertSame(204, $first->get_status());
            list($second) = $this->receiveEvents(array($this->clickEvent('https://outside.test/other')), 'definition-second-001', false, $postId);
            $this->assertSame(204, $second->get_status());
            $this->assertSame('1', $wpdb->get_var('SELECT SUM(rejected_events) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
            list($repeat) = $this->receiveEvents(array($this->clickEvent()), 'definition-repeat-001', false, $postId);
            $this->assertSame(204, $repeat->get_status());
            $this->assertSame('1', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
        } finally {
            remove_filter('cocoon_click_analytics_post_definition_limit', $cap);
        }
    }

    public function testFinalizedMonthSurvivesPartialDailyDeletion(): void
    {
        global $wpdb;
        $month = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -3 months'));
        $days = (int) gmdate('t', strtotime($month . '-01'));
        $rows = array();
        for ($day = 1; $day <= $days; $day++) cocoon_click_add_stat_row($rows, $month . '-' . sprintf('%02d', $day), 10, 20, 'desktop', str_repeat('d', 64), array('clicks' => 1));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $this->assertSame('success', cocoon_click_rollup_monthly()['status']);
        cocoon_click_delete_limited(CLICK_STATS_DAILY_TABLE_NAME, 'stat_date', $month . '-10');
        $this->assertSame('success', cocoon_click_rollup_monthly()['status']);
        $this->assertSame($days, (int) $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`'));
    }

    public function testFailedMonthlyRollupDoesNotDeleteSourceData(): void
    {
        global $wpdb;
        $date = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -4 months'));
        $rows = array();
        cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', str_repeat('d', 64), array('clicks' => 3));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $fail = static function ($sql) { return str_starts_with($sql, 'INSERT INTO `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`') ? 'INVALID CLICK AUDIT QUERY' : $sql; };
        $previous = $wpdb->suppress_errors(true);
        add_filter('query', $fail);
        try {
            cocoon_click_run_maintenance();
        } finally {
            remove_filter('query', $fail);
            $wpdb->suppress_errors($previous);
        }
        $this->assertSame('3', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        $this->assertSame('error', get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_STATUS)['status']);
    }

    public function testUnfinalizedMonthUsesDailyRowsInLongRange(): void
    {
        global $wpdb;
        $previousMonth = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -1 month'));
        $finalized = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -2 months'));
        set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_STATUS, array('status' => 'error', 'finalized_through' => $finalized));
        $rows = array();
        cocoon_click_add_stat_row($rows, $previousMonth . '-15', 10, 20, 'desktop', str_repeat('d', 64), array('clicks' => 7));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $source = cocoon_click_stats_source_sql('2020-01-01', current_time('Y-m-d'));
        $total = $wpdb->get_var($wpdb->prepare('SELECT SUM(clicks) FROM ' . $source['sql'] . ' source', $source['args']));
        $this->assertSame('7', $total);
    }

    public function testUnresolvableTargetsDoNotStarveLaterLinks(): void
    {
        global $wpdb;
        $target = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'クリック解析テスト'));
        $definitions = array();
        for ($index = 1; $index <= 51; $index++) {
            $event = $this->clickEvent($index === 51 ? get_permalink($target) : home_url('/missing/' . $index));
            $event['occurrence'] = $index;
            $item = cocoon_click_sanitize_link_event($event, 10, home_url('/source'));
            $item['definition']['target_post_id'] = 0;
            $definitions[$item['definition']['link_key']] = $item['definition'];
        }
        cocoon_click_upsert_link_definitions($definitions, current_time('mysql'));
        $this->assertTrue(cocoon_click_enrich_internal_targets());
        $this->assertSame('0', $wpdb->get_var('SELECT MAX(target_post_id) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
        $this->assertTrue(cocoon_click_enrich_internal_targets());
        $this->assertSame($target, (int) $wpdb->get_var('SELECT MAX(target_post_id) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
    }

    public function testSqlWilsonMatchesPhpForLegacyOvercount(): void
    {
        global $wpdb;
        $sql = cocoon_click_wilson_lower_sql('200', '100', '100');
        $actual = $wpdb->get_var('SELECT ' . $sql);
        $expected = cocoon_click_wilson_interval(200, 100, 100);
        $this->assertNotNull($actual);
        $this->assertEqualsWithDelta($expected['lower'], (float) $actual, 0.0001);
    }

    public function testGroupedRowsIdentifyMultipleSources(): void
    {
        $this->receiveEvents(array($this->clickEvent()), 'group-first-0001', false);
        $this->receiveEvents(array($this->clickEvent()), 'group-second-0001', false);
        $result = cocoon_click_analytics_links_table(current_time('Y-m-d'), current_time('Y-m-d'), array('group' => 'destination'));
        $this->assertCount(1, $result['rows']);
        $this->assertSame(0, $result['rows'][0]['source_post_id']);
        $this->assertSame(2, (int) $result['rows'][0]['source_count']);
        $this->assertSame(2, $result['rows'][0]['clicks']);
    }

    public function testImagePreviewMigrationAndUpsertPreserveHistoricalLink(): void
    {
        global $wpdb;
        $event = $this->clickEvent();
        list($response, , $postId) = $this->receiveEvents(array($event), 'preview-legacy-0001', false);
        $this->assertSame(204, $response->get_status());
        $originalId = (int) $wpdb->get_var('SELECT id FROM `' . CLICK_LINKS_TABLE_NAME . '` LIMIT 1');

        // 旧スキーマからの列追加と既存リンク・集計の維持
        $wpdb->query('ALTER TABLE `' . CLICK_LINKS_TABLE_NAME . '` DROP COLUMN image_url');
        create_click_analytics_tables();
        $event['image_url'] = 'https://example.org/image.php?id=123&w=320';
        list($response) = $this->receiveEvents(array($event), 'preview-image-0001', false, $postId);
        $this->assertSame(204, $response->get_status());
        unset($event['image_url']);
        $this->receiveEvents(array($event), 'preview-old-client-0001', false, $postId);
        $this->assertSame(1, (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
        $this->assertSame($originalId, (int) $wpdb->get_var('SELECT id FROM `' . CLICK_LINKS_TABLE_NAME . '` LIMIT 1'));

        // 掲載箇所別とリンク先別の両方で同一画像を取得
        foreach (array('occurrence', 'destination') as $group) {
            cocoon_click_analytics_flush_cache();
            $result = cocoon_click_analytics_links_table(current_time('Y-m-d'), current_time('Y-m-d'), array('group' => $group));
            $this->assertCount(1, $result['rows']);
            $this->assertSame('https://example.org/image.php?id=123&w=320', $result['rows'][0]['image_url']);
            $this->assertSame(3, $result['rows'][0]['clicks']);
        }
    }

    public function testAnonymousImageUrlsRequireManualLoadingAndSecretsAreNotStored(): void
    {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/render-func.php';
        $event = $this->clickEvent();
        $event['image_url'] = 'https://collector.example.org/image.php?id=123';
        list($response, , $postId) = $this->receiveEvents(array($event), 'unverified-image-0001', false);
        $this->assertSame(204, $response->get_status());
        $imageUrl = $wpdb->get_var('SELECT image_url FROM `' . CLICK_LINKS_TABLE_NAME . '` LIMIT 1');
        $this->assertSame($event['image_url'], $imageUrl);
        $this->assertFalse(cocoon_click_image_can_autoload($imageUrl));

        $event['label'] = '機密画像';
        $event['image_url'] = 'https://collector.example.org/image.php?id=123&token=private';
        list($response) = $this->receiveEvents(array($event), 'secret-image-0001', false, $postId);
        $this->assertSame(204, $response->get_status());
        // 空文字をnullへ変換するget_varを避けた保存値の確認
        $secretImage = $wpdb->get_row($wpdb->prepare('SELECT image_url FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE anchor_text=%s', '機密画像'), ARRAY_A);
        $this->assertNotNull($secretImage);
        $this->assertSame('', $secretImage['image_url']);
        $this->assertTrue(cocoon_click_image_can_autoload(get_cocoon_template_directory_uri() . '/images/no-image-160.png'));
    }

    public function testSchemaFailureDoesNotAdvanceVersion(): void
    {
        global $wpdb;
        $wpdb->query('ALTER TABLE `' . CLICK_STATS_DAILY_TABLE_NAME . '` DROP PRIMARY KEY, ADD PRIMARY KEY(stat_date,source_post_id,link_id,device)');
        set_theme_mod(OP_CLICK_ANALYTICS_TABLE_VERSION, '0.5.0');
        $fail = static function ($sql) { return str_starts_with($sql, 'ALTER TABLE `' . CLICK_STATS_DAILY_TABLE_NAME . '`') ? 'INVALID CLICK AUDIT QUERY' : $sql; };
        $previous = $wpdb->suppress_errors(true);
        add_filter('query', $fail);
        try {
            $this->assertFalse(create_click_analytics_tables());
            $this->assertSame('0.5.0', get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION));
        } finally {
            remove_filter('query', $fail);
            $wpdb->suppress_errors($previous);
            create_click_analytics_tables();
        }
    }

    public function testPhpConfigurationPreservesConsentBoolean(): void
    {
        $enable = '__return_true';
        $deny = '__return_false';
        add_filter('cocoon_click_analytics_should_enqueue', $enable);
        add_filter('cocoon_click_analytics_initial_consent', $deny);
        try {
            cocoon_click_enqueue_tracking_script();
            $inline = implode("\n", (array) wp_scripts()->get_data(COCOON_CLICK_ANALYTICS_SCRIPT_HANDLE, 'before'));
            preg_match('/window\.CocoonClickAnalyticsConfig = (.+);/', $inline, $matches);
            $config = json_decode($matches[1], true);
            $this->assertSame(false, $config['initialConsent']);
        } finally {
            remove_filter('cocoon_click_analytics_should_enqueue', $enable);
            remove_filter('cocoon_click_analytics_initial_consent', $deny);
            wp_deregister_script(COCOON_CLICK_ANALYTICS_SCRIPT_HANDLE);
        }
    }

    public function testSlowRollupStillGetsItsOwnPurgeBudget(): void
    {
        global $wpdb;
        $rows = array();
        $date = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -4 months'));
        cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', str_repeat('e', 64), array('clicks' => 4));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        // 集計だけを10秒以上遅延させ、削除処理の時間枠が独立していることを確認します。
        $delay = static function ($sql) {
            if (str_starts_with($sql, 'INSERT INTO `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`')) usleep(10100000);
            return $sql;
        };
        add_filter('query', $delay);
        try { cocoon_click_run_maintenance(); } finally { remove_filter('query', $delay); }
        $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        $this->assertSame('4', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`'));
    }

    public function testExpiredDefinitionsAreRemovedOnlyWithoutStoredStats(): void
    {
        global $wpdb;
        $item = cocoon_click_sanitize_link_event($this->clickEvent(), 10, home_url('/source'));
        $map = cocoon_click_upsert_link_definitions(array($item['definition']['link_key'] => $item['definition']), '2020-01-01 00:00:00');
        $linkId = reset($map);
        $rows = array();
        cocoon_click_add_stat_row($rows, current_time('Y-m-d'), 10, $linkId, 'desktop', str_repeat('a', 64), array('clicks' => 1));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $this->assertSame(0, cocoon_click_prune_definitions('2021-01-01 00:00:00'));
        $wpdb->query('DELETE FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`');
        $this->assertSame(1, cocoon_click_prune_definitions('2021-01-01 00:00:00'));
    }

    public function testSchemaChecksFailedColumnTypeMigration(): void
    {
        global $wpdb;
        $wpdb->query('ALTER TABLE `' . CLICK_LINKS_TABLE_NAME . '` MODIFY anchor_text varchar(191) NOT NULL DEFAULT \'\', MODIFY link_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL');
        set_theme_mod(OP_CLICK_ANALYTICS_TABLE_VERSION, '0.5.0');
        $fail = static function ($sql) {
            return preg_match('/^ALTER TABLE `?' . preg_quote(CLICK_LINKS_TABLE_NAME, '/') . '\b/', $sql) ? 'INVALID CLICK AUDIT QUERY' : $sql;
        };
        $previous = $wpdb->suppress_errors(true);
        add_filter('query', $fail);
        try {
            $this->assertFalse(create_click_analytics_tables());
            $this->assertSame('0.5.0', get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION));
        } finally {
            remove_filter('query', $fail);
            $wpdb->suppress_errors($previous);
            create_click_analytics_tables();
        }
    }

    public function testMonthLockAllowsReceiversTogetherButWaitsBeforeFinalization(): void
    {
        global $wpdb;
        $original = $wpdb;
        $other = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $other->set_prefix($original->prefix);
        $other->suppress_errors(true);
        $other->query('SET SESSION innodb_lock_wait_timeout=1');
        $month = current_time('Y-m');
        try {
            $original->query('START TRANSACTION');
            $this->assertTrue(cocoon_click_lock_month($month));
            $original->query('COMMIT');
            $original->query('START TRANSACTION');
            $this->assertTrue(cocoon_click_lock_month($month));
            $other->query('START TRANSACTION');
            $wpdb = $other;
            $this->assertTrue(cocoon_click_lock_month($month));
            // 受信中の共有ロックがある間は、月次確定の排他ロックへ進めません。
            $this->assertFalse(cocoon_click_lock_month($month, true));
            $other->query('ROLLBACK');
            $original->query('COMMIT');
            $other->query('START TRANSACTION');
            $this->assertTrue(cocoon_click_lock_month($month, true));
        } finally {
            $other->query('ROLLBACK');
            $original->query('ROLLBACK');
            $wpdb = $original;
            $other->close();
        }
    }

    public function testDefinitionQuotaSeesConcurrentCommittedRows(): void
    {
        global $wpdb;
        $original = $wpdb;
        $other = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $other->set_prefix($original->prefix);
        $cap = static function () { return 1; };
        add_filter('cocoon_click_analytics_definition_limit', $cap);
        $a = cocoon_click_sanitize_link_event($this->clickEvent('https://outside.test/a'), 10, home_url('/source'))['definition'];
        $b = cocoon_click_sanitize_link_event($this->clickEvent('https://outside.test/b'), 20, home_url('/source'))['definition'];
        try {
            $original->query('START TRANSACTION');
            $accepted = cocoon_click_filter_definitions_by_capacity(array($a['link_key'] => $a), 10, $newCount);
            $this->assertCount(1, $accepted);
            $other->query('START TRANSACTION');
            $this->assertSame('0', $other->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
            cocoon_click_upsert_link_definitions($accepted, current_time('mysql'), $newCount);
            $original->query('COMMIT');
            $wpdb = $other;
            // 古いスナップショットで0件が見えていても、上限判定は確定後の1件を認識します。
            $this->assertFalse(cocoon_click_definition_capacity_allows(array($b['link_key'] => $b), 20));
        } finally {
            $other->query('ROLLBACK');
            $original->query('ROLLBACK');
            $wpdb = $original;
            $other->close();
            remove_filter('cocoon_click_analytics_definition_limit', $cap);
        }
    }

    public function testExtendingRetentionStillReadsPreviouslyArchivedData(): void
    {
        global $wpdb;
        set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, 90);
        $rows = array();
        $date = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -4 months'));
        cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', str_repeat('e', 64), array('clicks' => 6));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        cocoon_click_run_maintenance();
        set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, 400);
        try {
            $from = gmdate('Y-m-d', strtotime(current_time('Y-m-d') . ' -200 days'));
            $source = cocoon_click_stats_source_sql($from, current_time('Y-m-d'));
            $this->assertSame('6', $wpdb->get_var($wpdb->prepare('SELECT SUM(clicks) FROM ' . $source['sql'] . ' source', $source['args'])));
        } finally {
            set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, 90);
        }
    }

    public function testMaintenanceRunsForStoredDataButNotUnusedSites(): void
    {
        set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 0);
        cocoon_click_unschedule_maintenance();
        cocoon_click_manage_cron_schedule();
        $this->assertFalse(wp_next_scheduled(COCOON_CLICK_CRON_HOOK));
        remove_theme_mod('click_analytics_maintenance_needed');
        $rows = array();
        cocoon_click_add_stat_row($rows, current_time('Y-m-d'), 10, 0, 'desktop', '', array('clicks' => 1));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        cocoon_click_manage_cron_schedule();
        $this->assertNotFalse(wp_next_scheduled(COCOON_CLICK_CRON_HOOK));
        cocoon_click_delete_all_data();
        cocoon_click_manage_cron_schedule();
        $this->assertFalse(wp_next_scheduled(COCOON_CLICK_CRON_HOOK));
    }

    public function testEmptyInitialRollupWaitsForUncommittedReceivers(): void
    {
        global $wpdb;
        $original = $wpdb;
        $other = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $other->set_prefix($original->prefix);
        $timeout = (int) $original->get_var('SELECT @@innodb_lock_wait_timeout');
        $suppressed = $original->suppress_errors(true);
        $original->query('SET SESSION innodb_lock_wait_timeout=1');
        $month = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -1 month'));
        try {
            $wpdb = $other;
            $other->query('START TRANSACTION');
            $this->assertTrue(cocoon_click_lock_month($month));
            $other->query('COMMIT');
            $other->query('START TRANSACTION');
            $this->assertTrue(cocoon_click_lock_month($month));
            $wpdb = $original;
            // 日次行がまだ0件でも、別接続で保存中の前月を確定してはいけません。
            $this->assertSame('error', cocoon_click_rollup_monthly()['status']);
            $other->query('ROLLBACK');
            $this->assertSame('success', cocoon_click_rollup_monthly()['status']);
        } finally {
            $other->query('ROLLBACK');
            $wpdb = $original;
            $original->query('SET SESSION innodb_lock_wait_timeout=' . $timeout);
            $original->suppress_errors($suppressed);
            $other->close();
        }
    }

    public function testConnectionLossRollsBackEveryWriteStageAndAllowsRetry(): void
    {
        global $wpdb;
        $original = $wpdb;
        $stages = array(
            'INSERT IGNORE INTO `' . CLICK_BATCHES_TABLE_NAME . '`',
            'INSERT INTO `' . CLICK_LINKS_TABLE_NAME . '`',
            'INSERT IGNORE INTO `' . CLICK_UNIQUES_TABLE_NAME . '`',
            'INSERT INTO `' . CLICK_STATS_DAILY_TABLE_NAME . '`',
            'INSERT INTO `' . CLICK_HEATMAP_DAILY_TABLE_NAME . '`',
            'COMMIT',
        );
        foreach ($stages as $index => $stage) {
            cocoon_click_delete_all_data();
            $killer = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
            $connection = (int) $wpdb->get_var('SELECT CONNECTION_ID()');
            $killed = false;
            // 本物の接続を切断し、wpdbが途中のSQLを自動再実行しないことを確認します。
            $disconnect = static function ($sql) use ($killer, $connection, $stage, &$killed) {
                if (!$killed && str_starts_with($sql, $stage)) {
                    $killed = true;
                    $killer->query('KILL CONNECTION ' . $connection);
                }
                return $sql;
            };
            add_filter('query', $disconnect);
            try {
                list($response, , , , $request) = $this->receiveEvents(array($this->clickEvent()), 'disconnect-stage-000' . $index, true);
            } finally {
                remove_filter('query', $disconnect);
                $killer->close();
            }
            $this->assertTrue($killed, $stage);
            $this->assertWPErrorStatus(503, $response);
            $this->assertSame($original, $wpdb);
            foreach (array(CLICK_BATCHES_TABLE_NAME, CLICK_LINKS_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME) as $table) {
                $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . $table . '`'), $stage . ': ' . $table);
            }
            $this->assertSame(204, cocoon_click_rest_receive_events($request)->get_status());
            $this->assertSame(204, cocoon_click_rest_receive_events($request)->get_status());
            $this->assertSame('1', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
            $this->assertSame('1', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_BATCHES_TABLE_NAME . '`'));
        }
    }

    public function testMonthlyConnectionLossKeepsDailyRowsAndAllowsRetry(): void
    {
        global $wpdb;
        foreach (array('INSERT INTO `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`', 'COMMIT') as $stage) {
            cocoon_click_delete_all_data();
            $rows = array();
            $date = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -4 months'));
            cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', str_repeat('b', 64), array('clicks' => 3));
            cocoon_click_upsert_stats($rows, current_time('mysql'));
            $connection = (int) $wpdb->get_var('SELECT CONNECTION_ID()');
            $killer = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
            $killed = false;
            $disconnect = static function ($sql) use ($killer, $connection, $stage, &$killed) {
                if (!$killed && str_starts_with($sql, $stage)) { $killed = true; $killer->query('KILL CONNECTION ' . $connection); }
                return $sql;
            };
            add_filter('query', $disconnect);
            try { $status = cocoon_click_rollup_monthly(); }
            finally { remove_filter('query', $disconnect); $killer->close(); }
            $this->assertTrue($killed);
            $this->assertSame('error', $status['status']);
            $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`'));
            $this->assertSame('3', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
            $this->assertSame('success', cocoon_click_rollup_monthly()['status']);
            $this->assertSame('3', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`'));
        }
    }

    public function testDefinitionQuotaKeepsExistingClicksInMixedBatchAndReplay(): void
    {
        global $wpdb;
        $cap = static function () { return 1; };
        add_filter('cocoon_click_analytics_post_definition_limit', $cap);
        try {
            list(, , $postId) = $this->receiveEvents(array($this->clickEvent()), 'mixed-baseline-00001', false);
            $new = array_merge($this->clickEvent('https://outside.test/new'), array('type' => 'impression'));
            list($response, , , , $request) = $this->receiveEvents(array($new, $this->clickEvent()), 'mixed-replay-0000001', false, $postId);
            $this->assertSame(204, $response->get_status());
            $this->assertSame(204, cocoon_click_rest_receive_events($request)->get_status());
            $this->assertSame('1', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
            $this->assertSame('2', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
            $this->assertSame('1', $wpdb->get_var('SELECT SUM(rejected_events) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        } finally { remove_filter('cocoon_click_analytics_post_definition_limit', $cap); }
    }

    public function testLongDailyRetentionReadsDataOutsideMonthlyRetention(): void
    {
        global $wpdb;
        set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, 400);
        set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, 12);
        $oldMonth = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -13 months'));
        $rows = array();
        // 月次より古い日次、確定月、当月を併用しても欠落・二重加算させません。
        foreach (array($oldMonth . '-20' => 1, gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -4 months')) => 2, current_time('Y-m-d') => 3) as $date => $clicks) {
            cocoon_click_add_stat_row($rows, $date, 10, 20, 'desktop', str_repeat('d', 64), array('clicks' => $clicks));
        }
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        cocoon_click_rollup_monthly();
        try {
            foreach (array(gmdate('Y-m-t', strtotime($oldMonth . '-01')) => '1', current_time('Y-m-d') => '6') as $to => $expected) {
                $source = cocoon_click_stats_source_sql($oldMonth . '-01', $to);
                $this->assertSame($expected, $wpdb->get_var(cocoon_click_prepare_query('SELECT SUM(clicks) FROM ' . $source['sql'] . ' source', $source['args'])));
            }
        } finally {
            set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, 90);
            set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, 24);
        }
    }

    public function testDisabledImpressionsIgnoreSampleFlagsFromCachedPages(): void
    {
        global $wpdb;
        $click = array_merge($this->clickEvent(), array('sampled' => true, 'forced_impression' => false));
        $impression = array_merge($click, array('type' => 'impression'));
        list($response) = $this->receiveEvents(array($impression, $click), 'disabled-samples-001', false, 0, false);
        $this->assertSame(204, $response->get_status());
        $row = $wpdb->get_row('SELECT SUM(clicks) AS clicks,SUM(sampled_impressions) AS impressions,SUM(sampled_clicks) AS sampled FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`', ARRAY_A);
        $this->assertSame(array('clicks' => '1', 'impressions' => '0', 'sampled' => '0'), $row);
    }

    public function testDeleteFailureRollsBackAndPreservesMaintenanceState(): void
    {
        global $wpdb;
        $this->receiveEvents(array($this->clickEvent()), 'delete-rollback-001', true);
        set_theme_mod('click_analytics_maintenance_needed', true);
        $status = array('status' => 'success', 'finalized_through' => '2020-01');
        set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_STATUS, $status);
        $fail = static function ($sql) { return $sql === 'DELETE FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`' ? 'INVALID DELETE AUDIT QUERY' : $sql; };
        $previous = $wpdb->suppress_errors(true);
        add_filter('query', $fail);
        try { $this->assertFalse(cocoon_click_delete_all_data()); }
        finally { remove_filter('query', $fail); $wpdb->suppress_errors($previous); }
        foreach (array(CLICK_LINKS_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME) as $table) {
            $this->assertSame('1', $wpdb->get_var('SELECT COUNT(*) FROM `' . $table . '`'));
        }
        $this->assertTrue((bool) get_theme_option('click_analytics_maintenance_needed'));
        $this->assertSame($status, get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_STATUS));
        $this->assertTrue(cocoon_click_delete_all_data());
        $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
        $this->assertFalse((bool) get_theme_option('click_analytics_maintenance_needed'));
    }

    public function testCleanupDrainsSeveralBatchesOfExpiredCounters(): void
    {
        global $wpdb;
        cocoon_click_unschedule_maintenance();
        $expired = gmdate('Y-m-d H:i:s', time() - 600);
        $values = array();
        for ($index = 0; $index < 3000; $index++) $values[] = $wpdb->prepare('(%s,1,%s)', cocoon_click_hmac('expired-regression-' . $index), $expired);
        $wpdb->query('INSERT INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES ' . implode(',', $values));
        cocoon_click_continue_maintenance();
        $this->assertSame('0', $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key<>%s', cocoon_click_hmac('definition_capacity'))));
        $this->assertFalse(wp_next_scheduled(COCOON_CLICK_CRON_CONTINUE_HOOK));
    }

    public function testDeleteMetadataFailureDoesNotReportSuccessOrRemoveData(): void
    {
        global $wpdb;
        $this->receiveEvents(array($this->clickEvent()), 'delete-metadata-001', false);
        set_theme_mod('click_analytics_maintenance_needed', true);
        $fail = static function ($sql) {
            return str_starts_with($sql, 'SELECT ENGINE FROM information_schema.tables') && strpos($sql, CLICK_STATS_DAILY_TABLE_NAME) !== false ? 'INVALID TABLE LOOKUP AUDIT QUERY' : $sql;
        };
        $previous = $wpdb->suppress_errors(true);
        add_filter('query', $fail);
        try { $this->assertFalse(cocoon_click_delete_all_data()); }
        finally { remove_filter('query', $fail); $wpdb->suppress_errors($previous); }
        $this->assertSame('1', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_BATCHES_TABLE_NAME . '`'));
        $this->assertSame('1', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        $this->assertTrue((bool) get_theme_option('click_analytics_maintenance_needed'));
    }

    private function definitionCount(): int
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare('SELECT request_count FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key=%s', cocoon_click_hmac('definition_capacity')));
    }

    public function testRejectedRequestsUseDatabaseWithoutMixingEventCounts(): void
    {
        $previousCache = wp_using_ext_object_cache();
        wp_using_ext_object_cache(false);
        try {
            list($response) = $this->receiveEvents(array($this->clickEvent(), array('type' => 'invalid')), 'health-rejected-001', false);
            $this->assertSame(204, $response->get_status());
            for ($index = 0; $index < 3; $index++) cocoon_click_rest_error('click_analytics_rate', 'test', 429);
            $health = cocoon_click_analytics_health();
            $this->assertSame(3, $health['rejected_requests_14days']);
            $this->assertSame(1, $health['rejected_14days']);
            $this->assertEqualsWithDelta(0.5, $health['missing_rate'], 0.0001);
            $this->assertTrue(cocoon_click_delete_all_data());
            $this->assertSame(0, cocoon_click_rejected_requests_count(true));
        } finally {
            wp_using_ext_object_cache($previousCache);
        }
    }

    public function testRejectedRequestCacheAndDatabaseFallbackAreCountedOnce(): void
    {
        $previousCache = wp_using_ext_object_cache();
        $key = 'request_rejected|' . current_time('Y-m-d');
        wp_cache_delete($key, 'cocoon_click_analytics_health');
        try {
            wp_using_ext_object_cache(true);
            cocoon_click_health_increment('request_rejected');
            cocoon_click_health_increment('request_rejected', 2);
            $this->assertSame(3, cocoon_click_rejected_requests_count(true));
            wp_using_ext_object_cache(false);
            cocoon_click_health_increment('request_rejected', 4);
            $this->assertSame(7, cocoon_click_rejected_requests_count(true));
            cocoon_click_health_increment('unknown', 100);
            $this->assertSame(7, cocoon_click_rejected_requests_count(true));
            $this->assertNull(cocoon_click_rejected_requests_count(false));
            $this->assertTrue(cocoon_click_delete_all_data());
            $this->assertSame(0, cocoon_click_rejected_requests_count(true));
        } finally {
            wp_cache_delete($key, 'cocoon_click_analytics_health');
            wp_using_ext_object_cache($previousCache);
        }
    }

    public function testRejectedRequestHistoryExcludesExpiredDaysAndStorageErrorsDoNotRecurse(): void
    {
        global $wpdb;
        $date = gmdate('Y-m-d', strtotime(current_time('Y-m-d') . ' -14 days'));
        $wpdb->query($wpdb->prepare('INSERT INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES (%s,7,%s)', cocoon_click_hmac('health|request_rejected|' . $date), gmdate('Y-m-d H:i:s', time() + DAY_IN_SECONDS)));
        $this->assertSame(0, cocoon_click_rejected_requests_count(true));
        $previousCache = wp_using_ext_object_cache();
        wp_using_ext_object_cache(false);
        $fail = static function ($sql) { return str_starts_with($sql, 'INSERT INTO `' . CLICK_LIMITS_TABLE_NAME . '`') ? 'INVALID HEALTH INSERT' : $sql; };
        add_filter('query', $fail);
        try {
            $this->assertWPErrorStatus(429, cocoon_click_rest_error('click_analytics_rate', 'test', 429));
        } finally {
            remove_filter('query', $fail);
            wp_using_ext_object_cache($previousCache);
        }
        $this->assertSame(0, cocoon_click_rejected_requests_count(true));
    }

    public function testDefinitionCapacityNeverLocksTheWholeLinkTable(): void
    {
        $queries = array();
        $capture = static function ($sql) use (&$queries) { $queries[] = $sql; return $sql; };
        add_filter('query', $capture);
        try {
            list($response, , $postId) = $this->receiveEvents(array($this->clickEvent()), 'capacity-query-0001', false);
            $this->assertSame(204, $response->get_status());
            list($response) = $this->receiveEvents(array($this->clickEvent()), 'capacity-query-0002', false, $postId);
            $this->assertSame(204, $response->get_status());
        } finally {
            remove_filter('query', $capture);
        }
        $this->assertSame(1, $this->definitionCount());
        foreach ($queries as $sql) {
            $this->assertDoesNotMatchRegularExpression('/SELECT COUNT\(\*\) FROM `' . preg_quote(CLICK_LINKS_TABLE_NAME, '/') . '`\s+FOR UPDATE/i', $sql);
        }
    }

    public function testSiteAndPostCapacityKeepExistingClicks(): void
    {
        global $wpdb;
        $siteLimit = static function () { return 2; };
        $postLimit = static function () { return 1; };
        add_filter('cocoon_click_analytics_definition_limit', $siteLimit);
        add_filter('cocoon_click_analytics_post_definition_limit', $postLimit);
        try {
            list($response, , $postId) = $this->receiveEvents(array($this->clickEvent()), 'capacity-limit-0001', false);
            $this->assertSame(204, $response->get_status());
            list($response) = $this->receiveEvents(array($this->clickEvent(), $this->clickEvent('https://outside.test/new')), 'capacity-limit-0002', false, $postId);
            $this->assertSame(204, $response->get_status());
            $this->assertSame(1, $this->definitionCount());
            list($response) = $this->receiveEvents(array($this->clickEvent()), 'capacity-limit-0003', false);
            $this->assertSame(204, $response->get_status());
            list($response) = $this->receiveEvents(array($this->clickEvent()), 'capacity-limit-0004', false);
            $this->assertSame(204, $response->get_status());
            $this->assertSame(2, $this->definitionCount());
            $this->assertSame('2', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
            $this->assertSame('3', $wpdb->get_var('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        } finally {
            remove_filter('cocoon_click_analytics_definition_limit', $siteLimit);
            remove_filter('cocoon_click_analytics_post_definition_limit', $postLimit);
        }
    }

    public function testCapacityMigrationCorrectionPruningAndDeletionStayConsistent(): void
    {
        global $wpdb;
        $definition = cocoon_click_sanitize_link_event($this->clickEvent(), 10, home_url('/source'))['definition'];
        $this->assertIsArray(cocoon_click_upsert_link_definitions(array($definition), '2020-01-01 00:00:00'));
        $this->assertSame(1, $this->definitionCount());
        // 旧版の専用行に残る0件から、既存リンクを保持したまま移行する場合の確認
        $this->assertTrue(cocoon_click_write_definition_count(0));
        set_theme_mod(OP_CLICK_ANALYTICS_TABLE_VERSION, '0.7.0');
        $this->assertTrue(create_click_analytics_tables());
        $this->assertSame(1, $this->definitionCount());
        $this->assertTrue(cocoon_click_write_definition_count(99));
        $this->assertTrue(cocoon_click_refresh_definition_count());
        $this->assertSame(1, $this->definitionCount());
        $this->assertSame(1, cocoon_click_prune_definitions('2021-01-01 00:00:00'));
        $this->assertSame(0, $this->definitionCount());
        $this->assertIsArray(cocoon_click_upsert_link_definitions(array($definition), current_time('mysql')));
        $this->assertTrue(cocoon_click_delete_all_data());
        $this->assertSame(0, $this->definitionCount());
        $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
        $this->assertIsArray(cocoon_click_upsert_link_definitions(array($definition), current_time('mysql')));
        $this->assertSame(1, $this->definitionCount());
    }

    public function testCounterWriteFailureRollsBackDefinitionsAndBatch(): void
    {
        global $wpdb;
        $fail = static function ($sql) {
            return str_starts_with($sql, 'UPDATE `' . CLICK_LIMITS_TABLE_NAME . '` SET request_count=request_count+') ? 'INVALID CAPACITY UPDATE' : $sql;
        };
        $previous = $wpdb->suppress_errors(true);
        add_filter('query', $fail);
        try {
            list($response, , , , $request) = $this->receiveEvents(array($this->clickEvent()), 'capacity-rollback-001', false);
            $this->assertWPErrorStatus(503, $response);
        } finally {
            remove_filter('query', $fail);
            $wpdb->suppress_errors($previous);
        }
        $this->assertSame(0, $this->definitionCount());
        $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
        $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_BATCHES_TABLE_NAME . '`'));
        $this->assertSame(204, cocoon_click_rest_receive_events($request)->get_status());
        $this->assertSame(1, $this->definitionCount());
    }

    public function testExistingBatchIgnoresUnrelatedLinkAndCapacityLocks(): void
    {
        global $wpdb;
        list(, , $postId) = $this->receiveEvents(array($this->clickEvent()), 'capacity-concurrent-001', false);
        list(, , $otherPost) = $this->receiveEvents(array($this->clickEvent()), 'capacity-concurrent-002', false);
        $other = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $timeout = static function () { return 1; };
        add_filter('cocoon_click_analytics_lock_wait_timeout', $timeout);
        try {
            $other->query('START TRANSACTION');
            $id = $wpdb->get_var($wpdb->prepare('SELECT id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE source_post_id=%d', $otherPost));
            $this->assertNotNull($other->get_var($other->prepare('SELECT id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE id=%d FOR UPDATE', $id)));
            $this->assertNotNull($other->get_var($other->prepare('SELECT request_count FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key=%s FOR UPDATE', cocoon_click_hmac('definition_capacity'))));
            list($response) = $this->receiveEvents(array($this->clickEvent()), 'capacity-concurrent-003', false, $postId);
            $this->assertSame(204, $response->get_status());
        } finally {
            $other->query('ROLLBACK');
            $other->close();
            remove_filter('cocoon_click_analytics_lock_wait_timeout', $timeout);
        }
        $this->assertSame(2, $this->definitionCount());
    }
}
