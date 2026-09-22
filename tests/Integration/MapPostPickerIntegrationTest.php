<?php
/**
 * 人気記事セレクターのデータベース統合テスト
 */
namespace Cocoon\Tests\Integration;

class MapPostPickerIntegrationTest extends IntegrationTestCase
{
    private array $candidateIds = array();

    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('create_click_analytics_tables')) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/_loader.php';
        }
        if (!function_exists('cocoon_analytics_map_post_candidates')) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/_loader.php';
        }
        create_accesses_table();
        create_click_analytics_tables();
        cocoon_analytics_flush_cache();
        cocoon_click_analytics_flush_cache();
    }

    protected function tearDown(): void
    {
        // WordPress未初期化によるスキップ時のDB後処理の除外
        if (!defined('WP_TESTS_DOMAIN')) {
            parent::tearDown();
            return;
        }
        global $wpdb;
        // 今回のテストで作成した投稿に属する記録だけの後片付け
        foreach ($this->candidateIds as $id) {
            $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id));
            $wpdb->delete(CLICK_STATS_DAILY_TABLE_NAME, array('source_post_id' => $id));
        }
        cocoon_analytics_flush_cache();
        cocoon_click_analytics_flush_cache();
        parent::tearDown();
    }

    private function candidate(int $pv, string $date, string $status = 'publish', string $type = 'post'): int
    {
        global $wpdb;
        $id = $this->createPost(array('post_title' => '人気候補テスト ' . $pv, 'post_status' => $status, 'post_type' => $type));
        $this->candidateIds[] = $id;
        $wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $id, 'post_type' => $type, 'date' => $date, 'count' => $pv));
        return $id;
    }

    public function testRankingUsesRequestedPeriodAndKeepsTenPublishedPostsAndPages(): void
    {
        global $wpdb;
        $date = current_time('Y-m-d');
        $yesterday = gmdate('Y-m-d', strtotime($date . ' -1 day'));
        $ids = array();
        for ($i = 1; $i <= 12; $i++) $ids[] = $this->candidate($i * 100, $date, 'publish', $i === 12 ? 'page' : 'post');
        $this->candidate(99999, $date, 'draft');
        $this->candidate(99998, $date, 'private');
        $this->candidate(99997, $date, 'publish', 'attachment');
        $wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $ids[0], 'post_type' => 'post', 'date' => $yesterday, 'count' => 50000));
        $items = cocoon_analytics_map_post_candidates('', $date, $date);
        $this->assertCount(10, $items);
        $this->assertSame(array_reverse(array_slice($ids, 2)), array_column($items, 'id'));
        $this->assertSame(range(1, 10), array_column($items, 'rank'));
        $this->assertSame(number_format_i18n(1200), $items[0]['pv']);
        $older = cocoon_analytics_map_post_candidates('', $yesterday, $yesterday);
        $this->assertSame($ids[0], $older[0]['id']);
        // トップ10の外にある記事もID検索で選択できることの確認
        $found = cocoon_analytics_map_post_candidates((string) $ids[0], $date, $date);
        $this->assertSame($ids[0], $found[0]['id']);
        $this->assertArrayNotHasKey('rank', $found[0]);
    }

    public function testAllPeriodMatchesClickMapInsteadOfOlderPageViews(): void
    {
        $today = current_time('Y-m-d');
        $historical = $this->candidate(50000, '2000-01-01');
        $current = $this->candidate(100, $today);
        $rows = array();
        cocoon_click_add_stat_row($rows, '2001-01-01', $current, 1, 'desktop', str_repeat('b', 64), array('clicks' => 1));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $period = cocoon_analytics_map_post_period('all');
        $this->assertSame(cocoon_click_analytics_min_date(), $period['from']);
        $this->assertNotSame(cocoon_analytics_resolve_period('all')['from'], $period['from']);
        $this->assertSame($today, $period['to']);
        $items = cocoon_analytics_map_post_candidates('', $period['from'], $period['to']);
        $this->assertNotContains($historical, array_column($items, 'id'));
        $this->assertContains($current, array_column($items, 'id'));
        $this->assertSame(cocoon_analytics_resolve_period('30days'), cocoon_analytics_map_post_period('30days'));
        $this->assertSame(array('from' => '2026-09-01', 'to' => '2026-09-22'), cocoon_analytics_map_post_period('custom', '2026-09-01', '2026-09-22'));
    }

    public function testClickRecordBadgeUsesMapDeviceAndExcludesImpressions(): void
    {
        $date = current_time('Y-m-d');
        $id = $this->candidate(1000, $date);
        $rows = array();
        $revision = str_repeat('a', 64);
        cocoon_click_add_stat_row($rows, $date, $id, 1, 'mobile', $revision, array('clicks' => 3));
        cocoon_click_add_stat_row($rows, $date, $id, 0, 'desktop', $revision, array('weighted_impressions' => 20));
        cocoon_click_upsert_stats($rows, current_time('mysql'));
        $mobile = cocoon_analytics_map_post_candidates((string) $id, $date, $date, 'mobile');
        $desktop = cocoon_analytics_map_post_candidates((string) $id, $date, $date, 'all');
        $this->assertTrue($mobile[0]['has_clicks']);
        $this->assertFalse($desktop[0]['has_clicks']);
    }
}
