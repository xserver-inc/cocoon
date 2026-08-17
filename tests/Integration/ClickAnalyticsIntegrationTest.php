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
    }

    protected function tearDown(): void
    {
        if (function_exists('cocoon_click_delete_all_data')) {
            cocoon_click_delete_all_data();
        }
        parent::tearDown();
    }

    private function receiveEvents(array $events, string $batchId, bool $heatmap): array
    {
        global $wpdb;
        set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_IMPRESSIONS, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_HEATMAP, $heatmap ? 1 : 0);
        set_theme_mod(OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN, 0);
        $postId = self::factory()->post->create(array('post_status' => 'publish', 'post_type' => 'post'));
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
        return array($response, (int) $wpdb->num_queries - $before, $postId, $layoutRevision);
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

    public function testSingleClickUsesAtMostSixCollectorQueries(): void
    {
        $event = array(
            'type' => 'click', 'href' => 'https://outside.example/offer', 'area' => 'content', 'heading' => '見出し',
            'occurrence' => 0, 'element_type' => 'text', 'label' => '外部リンク', 'sampled' => true,
            'forced_impression' => true, 'time_to_click_ms' => 1200, 'x_bp' => 5000, 'y_bp' => 6000,
        );
        list($response, $queries) = $this->receiveEvents(array($event), 'integration-click-0001', true);
        $this->assertSame(204, $response->get_status());
        $this->assertLessThanOrEqual(6, $queries);
    }

    public function testFiftyImpressionsUseAtMostFourCollectorQueries(): void
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
        $this->assertLessThanOrEqual(4, $queries);
    }
}
