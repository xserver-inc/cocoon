<?php
/** クリック解析のページリンクと絞り込み条件の統合テスト */
namespace Cocoon\Tests\Integration;

class ClickPaginationIntegrationTest extends IntegrationTestCase
{
    public function testPageLinksKeepFiltersAtFirstMiddleAndLastPages(): void
    {
        require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/render-func.php';
        $originalUri = $_SERVER['REQUEST_URI'] ?? '';
        try {
            foreach (array('overview', 'internal', 'external', 'map') as $view) {
                $filters = array('page' => 'theme-access', 'view' => 'clicks', 'click_view' => $view, 'period' => 'custom', 'from' => '2026-08-01', 'to' => '2026-09-22', 'device' => 'mobile', 'source_post_id' => '42', 'area' => 'content', 'link_type' => $view, 'group' => 'destination', 'order' => 'ctr');
                $filters['direction'] = 'asc';
                foreach (array(1, 13, 25) as $current) {
                    $_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?' . http_build_query($filters + array('paged' => $current));
                    ob_start();
                    try {
                        cocoon_click_render_pagination(array('total' => 625, 'per_page' => 25, 'page' => $current, 'total_is_estimate' => $current === 13), $filters);
                        $html = (string) ob_get_contents();
                    } finally {
                        ob_end_clean();
                    }
                    $dom = new \DOMDocument();
                    $dom->loadHTML('<meta charset="utf-8">' . $html);
                    $xpath = new \DOMXPath($dom);
                    $this->assertStringContainsString('cocoon-click-pagination', $html);
                    $this->assertStringContainsString('class="pagination-links"', $html);
                    $this->assertSame($current === 13, strpos($html, '<p class="description">') !== false);
                    $this->assertSame(0, $xpath->query('//ul')->length);
                    $this->assertSame((string) $current, $xpath->query('//*[@aria-current="page"]')->item(0)->textContent);
                    $this->assertSame($current > 1 ? 1 : 0, $xpath->query('//a[contains(@class,"prev")]')->length);
                    $this->assertSame($current < 25 ? 1 : 0, $xpath->query('//a[contains(@class,"next")]')->length);
                    foreach ($xpath->query('//a') as $link) {
                        $this->assertSame('cocoon-click-sort-ctr', parse_url($link->getAttribute('href'), PHP_URL_FRAGMENT));
                        parse_str((string) parse_url($link->getAttribute('href'), PHP_URL_QUERY), $query);
                        foreach ($filters as $key => $value) {
                            $this->assertSame($value, $query[$key], $key);
                        }
                        $this->assertGreaterThanOrEqual(1, (int) $query['paged']);
                        $this->assertLessThanOrEqual(25, (int) $query['paged']);
                        $this->assertNotSame($current, (int) $query['paged']);
                    }
                }
            }
        } finally {
            $_SERVER['REQUEST_URI'] = $originalUri;
        }
    }
}
