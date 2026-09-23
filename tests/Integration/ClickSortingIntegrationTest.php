<?php
/** クリック解析の数値順・ページ境界・見出し操作の統合テスト */
namespace Cocoon\Tests\Integration;

class ClickSortingIntegrationTest extends IntegrationTestCase
{
    private ?array $originalMods = null;

    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('create_click_analytics_tables')) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/_loader.php';
        }
        require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/render-func.php';
        $this->originalMods = (array) get_theme_mods();
        create_click_analytics_tables();
        cocoon_click_delete_all_data();
        cocoon_click_analytics_flush_cache();
        set_theme_mod(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, 30);
        set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, 24);
    }

    protected function tearDown(): void
    {
        try {
            // 初期化前のスキップ時におけるWordPress依存処理の回避
            if ($this->originalMods !== null) {
                cocoon_click_delete_all_data();
                update_option('theme_mods_' . get_stylesheet(), $this->originalMods);
            }
        } finally {
            parent::tearDown();
        }
    }

    public function testAllSortsAcrossPagesGroupsAndDailyMonthlySources(): void
    {
        global $wpdb;
        $postId = $this->createPost(array('post_status' => 'publish', 'post_title' => '並べ替え検証'));
        $date = current_time('Y-m-d');
        $month = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -6 months'));
        $now = current_time('mysql');
        $revision = cocoon_click_layout_revision($postId);
        $expectedRows = array();
        for ($index = 1; $index <= 33; $index++) {
            $event = array('type' => 'click', 'href' => 'https://sort-' . $index . '.example.org/link', 'area' => 'content', 'element' => 'text', 'occurrence' => $index, 'label' => 'リンク' . $index);
            $item = cocoon_click_sanitize_link_event($event, $postId, get_permalink($postId));
            $this->assertIsArray($item);
            $ids = cocoon_click_upsert_link_definitions(array($item['definition']), $now);
            $id = (int) $ids[$item['definition']['link_key']];
            $impressions = $index % 9 === 0 ? 0 : (($index * 13) % 25 + 1) * 10;
            $weightedClicks = ($index * 17) % 70;
            // データ不足の100%・十分な60%・重み未記録・100%超過の検証値
            if ($index === 1) { $impressions = 10; $weightedClicks = 10; }
            if ($index === 2) { $impressions = 1000; $weightedClicks = 600; }
            // SQLの小数点丸めでは同値になる、75.0%と75.1%の境界値
            if ($index === 3) { $impressions = 2000000; $weightedClicks = 1501001; }
            if ($index === 4) { $impressions = 2000000; $weightedClicks = 1500999; }
            $metrics = array('clicks' => ($index * 7) % 11, 'unique_clicks' => ($index * 5) % 9, 'weighted_impressions' => $impressions, 'weighted_clicks' => $weightedClicks, 'sampled_clicks' => $index === 2 ? 600 : 1, 'weight_squared' => $index === 4 ? 0 : $impressions);
            $base = array('source_post_id' => $postId, 'link_id' => $id, 'device' => 'desktop', 'layout_revision' => $revision, 'updated_at' => $now) + $metrics;
            $this->assertSame(1, $wpdb->insert(CLICK_STATS_DAILY_TABLE_NAME, array('stat_date' => $date) + $base));
            $this->assertSame(1, $wpdb->insert(CLICK_STATS_MONTHLY_TABLE_NAME, array('stat_month' => $month) + $base));
            $expectedRows[] = array('link_id' => $id, 'ctr' => $impressions > 0 ? min(1, $weightedClicks / $impressions) : null) + $metrics;
        }
        set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_STATUS, array('status' => 'success', 'finalized_through' => $month));
        cocoon_click_analytics_flush_cache();
        foreach (array(array($date, $date), array($month . '-01', gmdate('Y-m-t', strtotime($month . '-01'))), array($month . '-01', $date)) as $period) {
            foreach (array('occurrence', 'destination', 'domain') as $group) {
                foreach (array('clicks' => 'clicks', 'unique' => 'unique_clicks', 'impressions' => 'weighted_impressions', 'ctr' => 'ctr') as $order => $field) {
                    foreach (array('asc', 'desc') as $direction) {
                        $expected = $expectedRows;
                        usort($expected, static function ($left, $right) use ($field, $direction) {
                            // 未計算値を末尾へ配置した、SQLから独立する期待順の算出
                            if (($left[$field] === null) !== ($right[$field] === null)) return $left[$field] === null ? 1 : -1;
                            $comparison = $left[$field] <=> $right[$field];
                            return $comparison ? ($direction === 'asc' ? $comparison : -$comparison) : ($left['link_id'] <=> $right['link_id']);
                        });
                        $actualIds = array();
                        for ($page = 1; $page <= 2; $page++) {
                            $result = cocoon_click_analytics_links_table($period[0], $period[1], array('scope' => 'external', 'source_post_id' => $postId, 'device' => 'desktop', 'group' => $group, 'order' => $order, 'direction' => $direction, 'page' => $page, 'per_page' => 25));
                            $this->assertSame('', $wpdb->last_error);
                            $this->assertSame(33, $result['total'], implode('/', array($period[0], $period[1], $group, $order, $direction)));
                            $this->assertCount($page === 1 ? 25 : 8, $result['rows']);
                            $actualIds = array_merge($actualIds, array_map('intval', array_column($result['rows'], 'link_id')));
                        }
                        $this->assertSame(array_column($expected, 'link_id'), $actualIds, implode('/', array($period[0], $period[1], $group, $order, $direction)));
                        $this->assertCount(33, array_unique($actualIds));
                    }
                }
            }
        }
        $mapIds = array_column(cocoon_click_analytics_map_links($date, $date, $postId, 'desktop', $revision), 'link_id');
        cocoon_click_analytics_links_table($date, $date, array('source_post_id' => $postId, 'order' => 'ctr', 'direction' => 'asc'));
        $this->assertSame($mapIds, array_column(cocoon_click_analytics_map_links($date, $date, $postId, 'desktop', $revision), 'link_id'));
        $before = $wpdb->num_queries;
        $sameMap = cocoon_click_analytics_links_table($date, $date, array('order' => 'clicks', 'direction' => 'desc', 'source_post_id' => $postId, 'device' => 'desktop', 'layout_revision' => $revision, 'page' => 1, 'per_page' => 100, 'group' => 'occurrence'));
        $this->assertSame($mapIds, array_column($sameMap['rows'], 'link_id'));
        $this->assertSame($before, $wpdb->num_queries);
    }

    public function testGroupedSortUsesCombinedCountsAndWeightedCtr(): void
    {
        global $wpdb;
        $postId = $this->createPost(array('post_status' => 'publish', 'post_title' => '集計単位別の並べ替え検証'));
        $date = current_time('Y-m-d');
        $now = current_time('mysql');
        // 同一リンク先の2箇所と、同一ドメインの別リンク先を含む合算用データ
        $fixtures = array(
            array('https://sort-a.example.org/same', 4, 3, 100, 90),
            array('https://sort-a.example.org/same', 4, 3, 900, 90),
            array('https://sort-a.example.org/other', 6, 5, 100, 60),
            array('https://sort-b.example.org/link', 9, 7, 100, 30),
        );
        $ids = array();
        foreach ($fixtures as $index => $fixture) {
            $event = array('type' => 'click', 'href' => $fixture[0], 'area' => 'content', 'element' => 'text', 'occurrence' => $index, 'label' => '合算' . $index);
            $item = cocoon_click_sanitize_link_event($event, $postId, get_permalink($postId));
            $this->assertIsArray($item);
            $definitions = cocoon_click_upsert_link_definitions(array($item['definition']), $now);
            $ids[$index] = (int) $definitions[$item['definition']['link_key']];
            $this->assertSame(1, $wpdb->insert(CLICK_STATS_DAILY_TABLE_NAME, array('stat_date' => $date, 'source_post_id' => $postId, 'link_id' => $ids[$index], 'device' => 'desktop', 'layout_revision' => cocoon_click_layout_revision($postId), 'updated_at' => $now, 'clicks' => $fixture[1], 'unique_clicks' => $fixture[2], 'weighted_impressions' => $fixture[3], 'weighted_clicks' => $fixture[4])));
        }
        $groups = array(
            'destination' => array(
                array('id' => $ids[0], 'clicks' => 8, 'unique' => 6, 'impressions' => 1000, 'ctr' => 0.18),
                array('id' => $ids[2], 'clicks' => 6, 'unique' => 5, 'impressions' => 100, 'ctr' => 0.6),
                array('id' => $ids[3], 'clicks' => 9, 'unique' => 7, 'impressions' => 100, 'ctr' => 0.3),
            ),
            'domain' => array(
                array('id' => $ids[0], 'clicks' => 14, 'unique' => 11, 'impressions' => 1100, 'ctr' => 240 / 1100),
                array('id' => $ids[3], 'clicks' => 9, 'unique' => 7, 'impressions' => 100, 'ctr' => 0.3),
            ),
        );
        cocoon_click_analytics_flush_cache();
        foreach ($groups as $group => $expectedRows) {
            foreach (array('clicks', 'unique', 'impressions', 'ctr') as $order) {
                foreach (array('asc', 'desc') as $direction) {
                    $expected = $expectedRows;
                    usort($expected, static function ($left, $right) use ($order, $direction) {
                        $comparison = $left[$order] <=> $right[$order];
                        return $comparison ? ($direction === 'asc' ? $comparison : -$comparison) : ($left['id'] <=> $right['id']);
                    });
                    foreach ($expected as $index => $row) {
                        $result = cocoon_click_analytics_links_table($date, $date, array('source_post_id' => $postId, 'group' => $group, 'order' => $order, 'direction' => $direction, 'page' => $index + 1, 'per_page' => 1));
                        $this->assertSame('', $wpdb->last_error);
                        $this->assertSame(count($expected), $result['total']);
                        $this->assertCount(1, $result['rows']);
                        $this->assertSame($row['id'], (int) $result['rows'][0]['link_id']);
                        $this->assertEqualsWithDelta($row['ctr'], $result['rows'][0]['ctr'], 0.000001);
                    }
                }
            }
        }
    }

    public function testEmptyPageRetainsSortingControlsAndRecoveryLinks(): void
    {
        $originalUri = $_SERVER['REQUEST_URI'] ?? '';
        $_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=theme-access&view=clicks&click_view=internal&order=ctr&direction=asc&paged=99';
        try {
            ob_start();
            try {
                cocoon_click_render_links_table(array('rows' => array(), 'total' => 30, 'page' => 99, 'per_page' => 25), true, array('order' => 'ctr', 'direction' => 'asc'));
                $html = (string) ob_get_contents();
            } finally { ob_end_clean(); }
            $dom = new \DOMDocument();
            $dom->loadHTML('<meta charset="utf-8">' . $html);
            $xpath = new \DOMXPath($dom);
            $this->assertSame(4, $xpath->query('//th/a')->length);
            $this->assertSame(1, $xpath->query('//th[@aria-sort="ascending"]')->length);
            $this->assertStringContainsString(__('該当するクリックデータがありません。', THEME_NAME), $html);
            foreach ($xpath->query('//th/a') as $link) {
                parse_str((string) parse_url($link->getAttribute('href'), PHP_URL_QUERY), $query);
                $this->assertArrayNotHasKey('paged', $query);
                $this->assertSame('internal', $query['click_view']);
            }
        } finally { $_SERVER['REQUEST_URI'] = $originalUri; }
    }

    public function testHeadersToggleDirectionAndKeepFiltersInEveryView(): void
    {
        $originalUri = $_SERVER['REQUEST_URI'] ?? '';
        try {
            foreach (array('overview', 'internal', 'external', 'map') as $view) {
                foreach (array('asc', 'desc') as $direction) {
                    $filters = array('page' => 'theme-access', 'view' => 'clicks', 'click_view' => $view, 'period' => 'custom', 'from' => '2026-08-01', 'to' => '2026-09-23', 'device' => 'mobile', 'source_post_id' => '42', 'area' => 'content', 'link_type' => 'external', 'group' => 'destination');
                    $_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?' . http_build_query($filters + array('order' => 'ctr', 'direction' => $direction, 'paged' => 13));
                    ob_start();
                    try {
                        foreach (array('clicks', 'unique', 'impressions', 'ctr') as $key) cocoon_click_render_sort_header($key, '<テスト>', array('order' => 'ctr', 'direction' => $direction));
                        $html = (string) ob_get_contents();
                    } finally { ob_end_clean(); }
                    $dom = new \DOMDocument();
                    $dom->loadHTML('<meta charset="utf-8"><table><thead><tr>' . $html . '</tr></thead></table>');
                    $xpath = new \DOMXPath($dom);
                    $this->assertSame(4, $xpath->query('//th/a')->length);
                    $this->assertSame(1, $xpath->query('//th[@aria-sort]')->length);
                    $this->assertSame($direction === 'asc' ? 'ascending' : 'descending', $xpath->query('//th[@aria-sort]')->item(0)->getAttribute('aria-sort'));
                    $this->assertStringNotContainsString('<テスト>', $html);
                    foreach ($xpath->query('//th/a') as $link) {
                        parse_str((string) parse_url($link->getAttribute('href'), PHP_URL_QUERY), $query);
                        foreach ($filters as $key => $value) $this->assertSame($value, $query[$key]);
                        $this->assertArrayNotHasKey('paged', $query);
                        $this->assertSame($query['order'] === 'ctr' && $direction === 'desc' ? 'asc' : 'desc', $query['direction']);
                        $this->assertSame('cocoon-click-sort-' . $query['order'], parse_url($link->getAttribute('href'), PHP_URL_FRAGMENT));
                        $this->assertSame(sprintf($query['direction'] === 'asc' ? __('%s: 昇順で並べ替え', THEME_NAME) : __('%s: 降順で並べ替え', THEME_NAME), '<テスト>'), $link->getAttribute('aria-label'));
                    }
                }
            }
        } finally { $_SERVER['REQUEST_URI'] = $originalUri; }
    }
}
