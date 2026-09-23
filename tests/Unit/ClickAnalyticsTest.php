<?php
/**
 * クリック解析の統計・URL正規化ユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/settings-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/statistics-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/normalize-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/rest-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/admin-query-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/export-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/render-func.php';
require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/render-func.php';

class ClickAnalyticsTest extends TestCase
{
    public function testTableSortAcceptsOnlyKnownColumnsAndDirections(): void
    {
        $this->assertSame(array('order' => 'clicks', 'direction' => 'desc'), cocoon_click_table_sort_args());
        foreach (array('clicks', 'unique', 'impressions', 'ctr') as $column) {
            foreach (array('asc', 'desc') as $direction) {
                $args = array('order' => $column, 'direction' => $direction);
                $this->assertSame($args, cocoon_click_table_sort_args($args));
            }
        }
        foreach (array('', 'unknown', 'clicks DESC; DROP TABLE links', array('ctr'), null) as $invalid) {
            $this->assertSame(array('order' => 'clicks', 'direction' => 'desc'), cocoon_click_table_sort_args(array('order' => $invalid, 'direction' => $invalid)));
        }
    }

    public function testTableOrderUsesDisplayedCtrAndStableTieBreakers(): void
    {
        $this->assertSame('clicks DESC, link_id ASC', cocoon_click_table_order_sql(array()));
        $this->assertSame('unique_clicks ASC, link_id ASC', cocoon_click_table_order_sql(array('order' => 'unique', 'direction' => 'asc')));
        foreach (array('asc', 'desc') as $direction) {
            $sql = cocoon_click_table_order_sql(array('order' => 'ctr', 'direction' => $direction));
            $this->assertStringContainsString('weighted_clicks * 1.0e0 / NULLIF(weighted_impressions, 0)', $sql);
            $this->assertStringContainsString('IS NULL ASC', $sql);
            $this->assertStringEndsWith(strtoupper($direction) . ', link_id ASC', $sql);
            $this->assertStringNotContainsString('sampled_clicks', $sql);
            $this->assertStringNotContainsString('weight_squared', $sql);
        }
    }

    public function testPaginationIsOmittedForZeroOrOnePage(): void
    {
        \Brain\Monkey\Functions\expect('paginate_links')->never();
        foreach (array(0, 25) as $total) {
            ob_start();
            try {
                cocoon_click_render_pagination(array('total' => $total, 'per_page' => 25, 'page' => 1));
                $html = (string) ob_get_contents();
            } finally {
                ob_end_clean();
            }
            $this->assertSame('', $html);
        }
    }

    public function testEstimatedSinglePageKeepsExplanationWithoutPageLinks(): void
    {
        \Brain\Monkey\Functions\expect('paginate_links')->never();
        \Brain\Monkey\Functions\when('esc_html__')->alias(static function ($text) {return $text;});
        ob_start();
        try {
            cocoon_click_render_pagination(array('total' => 25, 'per_page' => 25, 'page' => 1, 'total_is_estimate' => true));
            $html = (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $this->assertStringContainsString('class="cocoon-click-results-footer"', $html);
        $this->assertStringContainsString('class="description cocoon-click-estimate-note"', $html);
        $this->assertStringNotContainsString('cocoon-click-pagination', $html);
    }

    public function testPreviewUrlsPreserveImageQueriesAndRejectSecrets(): void
    {
        $this->assertSame('https://images.example.org/banner.png?id=123&w=320&h=180', cocoon_click_sanitize_image_url('https://images.example.org/banner.png?id=123&amp;w=320&amp;h=180'));
        foreach (array('javascript:alert(1)', 'data:image/png;base64,xxx', 'https://user:pass@example.org/a.png', 'https://example.org/a.png" onerror="alert(1)', "https://example.org/a\n.png", array('src'), str_repeat('a', 2049)) as $url) {
            $this->assertSame('', cocoon_click_sanitize_image_url($url));
        }
        foreach (array('https://example.org/image.php?id=123&w=320', 'https://www.google.com/s2/favicons?domain=example.org', 'https://example.org/resize?url=https%3A%2F%2Fexample.org%2Fphoto.png&fit=contain', 'https://example.org/sprite.svg#view') as $url) {
            $this->assertSame($url, cocoon_click_sanitize_image_url($url));
        }
        foreach (array('token=private', 'X-Amz-Signature=private', 'api_key=private', '%74oken=private', '%74oken=%FF', 'key[]=private', 'jwt=private', 'url=https%3A%2F%2Fuser%3Asecret%40example.org%2Fa.png', 'url=https%3A%2F%2Fexample.org%2Fa.png%3Ftoken%3Dprivate') as $query) {
            $this->assertSame('', cocoon_click_sanitize_image_url('https://example.org/image?' . $query));
        }
    }

    private function mockPreviewRoots(): void
    {
        \Brain\Monkey\Functions\when('wp_get_upload_dir')->justReturn(array('baseurl' => 'https://example.org/uploads', 'basedir' => dirname(__DIR__, 2) . '/images', 'error' => false));
        \Brain\Monkey\Functions\when('get_cocoon_template_directory_uri')->justReturn('https://example.org/theme');
        \Brain\Monkey\Functions\when('get_cocoon_template_directory')->justReturn(dirname(__DIR__, 2));
    }

    public function testOnlyVerifiedStaticImagesCanAutoload(): void
    {
        $this->mockPreviewRoots();
        $this->assertTrue(cocoon_click_image_can_autoload('https://example.org/uploads/no-image-160.png'));
        $this->assertTrue(cocoon_click_image_can_autoload('https://example.org/theme/images/no-image-160.png'));
        foreach (array('https://collector.example.org/pixel.png', 'http://127.0.0.1/image.png', 'http://192.168.1.1/image.png', 'https://example.org/uploads-evil/no-image-160.png', 'https://example.org/uploads/missing.png', 'https://example.org/uploads/no-image-160.png?id=123', 'https://example.org/theme/functions.php', 'https://example.org/uploads/%2e%2e/screenshot.png') as $url) {
            $this->assertFalse(cocoon_click_image_can_autoload($url), $url);
        }
    }

    public function testCaptionCleanupAndLegacyImageRecovery(): void
    {
        $base = array('destination_url' => 'https://example.org/page', 'is_affiliate' => 0);
        $cases = array(
            array('通常のリンク', '通常のリンク', ''),
            array('カードの説明<img src="https://example.org/favicon.png" class="', 'カードの説明', ''),
            array('&lt;img src=&quot;https://example.org/banner.png?w=320&amp;h=180&quot; alt=&quot;&quot; class=&quot;', '画像', 'https://example.org/banner.png?w=320&h=180'),
            array('<img src="https://example.org/truncated', '画像', ''),
            array('<img src="javascript:alert(1)" onerror="alert(2)">', '画像', ''),
        );
        foreach ($cases as $case) {
            $preview = cocoon_click_link_preview($base + array('anchor_text' => $case[0]));
            $this->assertSame(array('label' => $case[1], 'image_url' => $case[2]), $preview);
        }
        $preview = cocoon_click_link_preview($base + array('anchor_text' => 'バナー', 'image_url' => 'https://example.org/banner.png'));
        $this->assertSame('バナー', $preview['label']);
        $this->assertSame('https://example.org/banner.png', $preview['image_url']);
    }

    public function testTableEscapesCaptionsAndKeepsReliabilityOutsideDetails(): void
    {
        $this->mockPreviewRoots();
        \Brain\Monkey\Functions\when('esc_html__')->alias(static function ($text, $domain) {
            return esc_html(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('esc_attr__')->alias(static function ($text, $domain) {
            return esc_attr(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('esc_html_e')->alias(static function ($text, $domain) {
            echo esc_html(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('number_format_i18n')->alias(static function ($number, $decimals = 0) {
            return number_format((float) $number, $decimals);
        });
        $row = array_merge(cocoon_click_metric_row(array('clicks' => 1, 'sampled_clicks' => 1, 'weighted_clicks' => 1, 'weighted_impressions' => 2, 'weight_squared' => 2)), array(
            'source_post_id' => 0, 'anchor_text' => '安全な表示 " onmouseover="alert(1)',
            'image_url' => 'https://example.org/banner.png', 'is_affiliate' => 0,
            'destination_url' => 'https://example.org/?label=<script>alert(1)</script>',
            'heading_label' => '<script>alert(2)</script>', 'destination_type' => 'external',
            'semantic_area' => 'content', 'element_type' => 'image', 'occurrence_no' => 0,
        ));
        foreach (array(true, false) as $showSource) {
            ob_start();
            try {
                cocoon_click_render_links_table(array('rows' => array($row)), $showSource);
                $html = (string) ob_get_contents();
            } finally {
                ob_end_clean();
            }
            $this->assertStringNotContainsString('<script>', $html);
            $this->assertStringNotContainsString(' onmouseover="', $html);
            $this->assertStringNotContainsString('<img ', $html);
            $this->assertStringContainsString('data-image-url="https://example.org/banner.png"', $html);
            $this->assertStringContainsString('読み込み先: example.org', $html);
            $this->assertSame($showSource ? 10 : 9, substr_count($html, 'scope="col"'));
            $this->assertLessThan(strpos($html, '<details'), strpos($html, 'データ不足'));
            $this->assertStringNotContainsString('<details open', $html);
            $this->assertStringContainsString('95%信頼区間', $html);
            $this->assertStringContainsString('cocoon-click-stat-content', $html);
            $this->assertStringContainsString('class="cocoon-click-scroll-controls" hidden', $html);
            $this->assertStringContainsString('aria-label="表を左にスクロール"', $html);
            $this->assertStringContainsString('aria-label="表を右にスクロール"', $html);
            $this->assertStringContainsString('class="cocoon-click-table-frame"', $html);
            $this->assertStringNotContainsString('列見出しをクリックすると並べ替えできます。', $html);
            $this->assertStringContainsString('横にスクロールできます', $html);
        }
    }

    public function testImagePrivacyNoticeAppearsOnceOutsideScrollAreaOnlyForManualImages(): void
    {
        $this->mockPreviewRoots();
        \Brain\Monkey\Functions\when('esc_html__')->alias(static function ($text, $domain) {
            return esc_html(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('esc_html_e')->alias(static function ($text, $domain) {
            echo esc_html(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('number_format_i18n')->alias(static function ($number, $decimals = 0) {
            return number_format((float) $number, $decimals);
        });
        $row = array_merge(cocoon_click_metric_row(array()), array(
            'source_post_id' => 0, 'anchor_text' => 'リンク', 'is_affiliate' => 0,
            'destination_url' => 'https://example.org/page', 'heading_label' => '',
            'destination_type' => 'external', 'semantic_area' => 'content',
            'element_type' => 'image', 'occurrence_no' => 0, 'data_sufficient' => true,
        ));
        $manual = array_merge($row, array('image_url' => 'https://images.example.net/banner.png'));
        $local = array_merge($row, array('image_url' => 'https://example.org/uploads/no-image-160.png'));
        $notice = 'プライバシー保護のため、外部画像などは自動で読み込まず、「画像を読み込む」を押したときに読み込みます。読み込むと、画像の配信元にお使いのIPアドレスなどが伝わります。';
        foreach (array(true, false) as $showSource) {
            // 同一リクエスト内の別テーブルに対する表示条件の独立性の確認
            foreach (array(array($manual, $manual, $local), array($row), array($local), array()) as $rows) {
                ob_start();
                try {
                    cocoon_click_render_links_table(array('rows' => $rows), $showSource);
                    $html = (string) ob_get_contents();
                } finally {
                    ob_end_clean();
                }
                $expected = count($rows) === 3 ? 1 : 0;
                $this->assertSame($expected, substr_count($html, 'cocoon-click-image-notice'));
                $this->assertSame($expected, substr_count($html, esc_html($notice)));
                if ($expected) {
                    $this->assertMatchesRegularExpression('/<\/table>\s*(?:<\/div>\s*){3}<p class="description cocoon-click-image-notice">/', $html);
                    $this->assertStringNotContainsString('<img class="cocoon-click-thumbnail" src="https://images.example.net/', $html);
                }
            }
        }
    }

    public function testCtrDetailsShowsMetricsAndUnavailableConfidenceInterval(): void
    {
        \Brain\Monkey\Functions\when('esc_attr__')->alias(static function ($text, $domain) {
            return esc_attr(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('esc_html_e')->alias(static function ($text, $domain) {
            echo esc_html(__($text, $domain));
        });
        \Brain\Monkey\Functions\when('number_format_i18n')->alias(static function ($number, $decimals = 0) {
            return number_format((float) $number, $decimals);
        });
        foreach (array(0, 28, 100) as $impressions) {
            $row = cocoon_click_metric_row(array('weighted_clicks' => 21, 'weighted_impressions' => $impressions, 'weight_squared' => $impressions, 'sampled_clicks' => 21));
            ob_start();
            try {
                cocoon_click_render_ctr_details($row, '<img src=x onerror=alert(1)>');
                $html = (string) ob_get_contents();
            } finally {
                ob_end_clean();
            }
            $this->assertStringContainsString('推定CTRの詳細', $html);
            $this->assertStringContainsString('有効標本数（n）', $html);
            $this->assertStringContainsString('サンプルクリック', $html);
            $this->assertStringContainsString('比較の目安: 100以上', $html);
            $this->assertStringContainsString('比較の目安: 10以上', $html);
            $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
            $this->assertStringNotContainsString('<img ', $html);
            $this->assertSame($impressions < 100, strpos($html, 'データ不足') !== false);
            $this->assertSame($impressions === 0, strpos($html, '信頼区間を計算できません。') !== false);
            if ($impressions === 28) {
                $this->assertStringContainsString('75.0%', $html);
                $this->assertStringContainsString('56.6% 〜 87.3%', $html);
            }
        }
    }

    public function testTrackingIsEnabledByDefaultAndRespectsSavedSettings(): void
    {
        $previous = $GLOBALS['test_theme_mods'] ?? array();
        try {
            unset($GLOBALS['test_theme_mods'][OP_CLICK_ANALYTICS_ENABLE]);
            $this->assertTrue(is_click_analytics_enable());

            foreach (array(0, '0', false, 1, '1', true) as $savedValue) {
                $GLOBALS['test_theme_mods'][OP_CLICK_ANALYTICS_ENABLE] = $savedValue;
                $this->assertSame((bool) $savedValue, is_click_analytics_enable());
            }
        } finally {
            $GLOBALS['test_theme_mods'] = $previous;
        }
    }

    public function testPostPickerShowsTitleAndSubmitsHiddenPostId(): void
    {
        \Brain\Monkey\Functions\when('wp_parse_args')->alias(static function ($args, $defaults) {
            return array_merge($defaults, $args);
        });
        $GLOBALS['test_mock_get_post'] = (object) array('ID' => 42);

        ob_start();
        cocoon_analytics_render_post_picker('source_post_id', 42, array('label' => 'クリック元記事:'));
        $html = (string) ob_get_clean();
        unset($GLOBALS['test_mock_get_post']);

        $this->assertStringContainsString('クリック元記事:', $html);
        $this->assertStringContainsString('role="combobox"', $html);
        $this->assertStringContainsString('value="Test Title"', $html);
        $this->assertStringContainsString('type="hidden" name="source_post_id"', $html);
        $this->assertStringContainsString('value="42"', $html);
        $this->assertStringContainsString('IDで検索', $html);
        $this->assertStringNotContainsString('type="number"', $html);
    }

    public function testSamplingRateFollowsTrafficThresholds(): void
    {
        $this->assertSame(10, cocoon_click_sampling_rate_for_daily_pv(100, false));
        $this->assertSame(100, cocoon_click_sampling_rate_for_daily_pv(500, true));
        $this->assertSame(20, cocoon_click_sampling_rate_for_daily_pv(501, true));
        $this->assertSame(5, cocoon_click_sampling_rate_for_daily_pv(2501, true));
        $this->assertSame(1, cocoon_click_sampling_rate_for_daily_pv(10001, true));
    }

    public function testEffectiveSampleSizeUsesSquaredWeights(): void
    {
        // 同じ重みが10観測ある場合、有効標本数も10になります。
        $this->assertEqualsWithDelta(10.0, cocoon_click_effective_sample_size(50, 250), 0.0001);
    }

    public function testSamplingWeightAndRetentionBoundary(): void
    {
        $this->assertSame(5, cocoon_click_sampling_weight(20));
        $this->assertSame(20, cocoon_click_sampling_weight(5));
        $this->assertSame('2026-05-20', cocoon_click_retention_cutoff('2026-08-17', 90));
    }

    public function testTrackingTokenDetectsModifiedPayload(): void
    {
        $token = cocoon_click_tracking_token(10, str_repeat('a', 64), 20);
        $this->assertTrue(cocoon_click_verify_tracking_token(10, str_repeat('a', 64), 20, $token));
        $this->assertFalse(cocoon_click_verify_tracking_token(11, str_repeat('a', 64), 20, $token));
    }

    public function testWilsonIntervalDoesNotPromoteOneOfOneToCertain(): void
    {
        $interval = cocoon_click_wilson_interval(1, 1, 1);
        $this->assertSame(1.0, $interval['rate']);
        $this->assertLessThan(0.3, $interval['lower']);
        $this->assertFalse(cocoon_click_data_is_sufficient($interval['effective_n'], 1));
        $this->assertSame(array('effective_sample_size', 'sampled_clicks'), cocoon_click_data_sufficiency_reasons($interval['effective_n'], 1));
    }

    public function testCtrAnomalyRequiresSeparatedConfidenceIntervals(): void
    {
        $previous = array('data_sufficient' => true, 'ctr_lower' => 0.08, 'ctr_upper' => 0.12);
        $this->assertSame('high', cocoon_click_detect_ctr_anomaly(array('data_sufficient' => true, 'ctr_lower' => 0.15, 'ctr_upper' => 0.2), $previous));
        $this->assertSame('stable', cocoon_click_detect_ctr_anomaly(array('data_sufficient' => true, 'ctr_lower' => 0.1, 'ctr_upper' => 0.15), $previous));
        $this->assertSame('insufficient', cocoon_click_detect_ctr_anomaly(array('data_sufficient' => false), $previous));
    }

    public function testCoordinateIsRoundedIntoConfiguredGrid(): void
    {
        $this->assertSame(0, cocoon_click_coordinate_bin(0, 10));
        $this->assertSame(5, cocoon_click_coordinate_bin(5000, 10));
        $this->assertSame(9, cocoon_click_coordinate_bin(10000, 10));
        $this->assertSame(49, cocoon_click_coordinate_bin(10000, 50));
    }

    public function testInternalHostRequiresExactHostMatch(): void
    {
        $hosts = array('example.com', 'www.example.com');
        $this->assertTrue(cocoon_click_is_internal_host('example.com', $hosts));
        $this->assertFalse(cocoon_click_is_internal_host('example.com.evil.test', $hosts));
    }

    public function testQueryNormalizationDropsTrackingAndSensitiveValues(): void
    {
        $pairs = cocoon_click_query_pairs('item_id=42&utm_source=news&gclid=x&access_token=secret&user_email=a%40example.com&sort=asc');
        $this->assertSame(array('item_id' => '42', 'sort' => 'asc'), $pairs);
    }

    public function testPathNormalizationResolvesDotSegments(): void
    {
        $this->assertSame('/guide/start/', cocoon_click_normalize_path('/docs/../guide//start/'));
    }

    public function testContactDestinationNeverKeepsRawAddress(): void
    {
        $destination = cocoon_click_normalize_destination('mailto:person@example.com?subject=hello', 'https://example.com/article');
        $this->assertSame('mailto:', $destination['display']);
        $this->assertStringNotContainsString('person@example.com', $destination['canonical']);
    }

    public function testSocialAndAffiliateDestinationsAreClassified(): void
    {
        \Brain\Monkey\Functions\when('sanitize_key')->alias(static function ($value) {
            return strtolower((string) preg_replace('/[^a-z0-9_\-]/i', '', $value));
        });
        $social = cocoon_click_normalize_destination('https://www.youtube.com/watch?v=abc', 'https://example.com/article');
        $affiliate = cocoon_click_normalize_destination('https://shop.example.net/item/1', 'https://example.com/article', array('is_affiliate' => true));
        $this->assertSame('social', $social['type']);
        $this->assertSame('affiliate', $affiliate['type']);
    }

    public function testOfficialAndReferenceDestinationsAreClassified(): void
    {
        $this->assertSame('official', cocoon_click_classify_external_destination('external', 'www.mhlw.go.jp'));
        $this->assertSame('official', cocoon_click_classify_external_destination('external', 'cdc.gov'));
        $this->assertSame('reference', cocoon_click_classify_external_destination('external', 'arxiv.org'));
        $this->assertSame('reference', cocoon_click_classify_external_destination('external', 'example.ac.jp'));
        $this->assertSame('external', cocoon_click_classify_external_destination('external', 'example.com'));
    }

    public function testDailyRowsAreSeparatedByLayoutRevision(): void
    {
        $rows = array();
        cocoon_click_add_stat_row($rows, '2026-08-17', 10, 20, 'desktop', str_repeat('a', 64), array('clicks' => 1));
        cocoon_click_add_stat_row($rows, '2026-08-17', 10, 20, 'desktop', str_repeat('b', 64), array('clicks' => 1));
        $this->assertCount(2, $rows);
        $this->assertSame(1, reset($rows)['clicks']);
    }

    public function testAbsoluteSamePageFragmentIsClassifiedAsAnchor(): void
    {
        $destination = cocoon_click_normalize_destination('https://example.com/article#details', 'https://example.com/article');
        $this->assertSame('anchor', $destination['type']);
        $this->assertSame('#details', $destination['display']);
    }

    public function testCsvFormulaPrefixIsNeutralized(): void
    {
        $this->assertSame("'=HYPERLINK(\"https://evil.test\")", cocoon_analytics_csv_safe_cell('=HYPERLINK("https://evil.test")'));
        $this->assertSame("' \t=1+1", cocoon_analytics_csv_safe_cell(" \t=1+1"));
        $this->assertSame('通常テキスト', cocoon_analytics_csv_safe_cell('通常テキスト'));
        $this->assertSame(123, cocoon_analytics_csv_safe_cell(123));
    }

    public function testRateLimitUsesNetworkBucketsWithoutStoringFullIp(): void
    {
        $this->assertSame('192.0.2.0/24', cocoon_click_network_bucket('192.0.2.123'));
        $this->assertSame('20010db800000000/64', cocoon_click_network_bucket('2001:db8::1234'));
        $this->assertSame('', cocoon_click_network_bucket('not-an-ip'));
    }

    public function testRelativeAndAbsoluteFragmentsShareAnIdentity(): void
    {
        foreach (array('details', '日本語の見出し', rawurlencode('日本語の見出し')) as $fragment) {
            $relative = cocoon_click_normalize_destination('#' . $fragment, 'https://example.com/article');
            $absolute = cocoon_click_normalize_destination('https://example.com/article#' . $fragment, 'https://example.com/article');
            $this->assertSame($relative['canonical'], $absolute['canonical']);
            $this->assertStringStartsWith('#', $relative['canonical']);
        }
        $this->assertSame('#日本語の見出し', $relative['canonical']);
    }

    public function testExclusionMatchesBeforeRemovingQueryValues(): void
    {
        $previous = $GLOBALS['test_theme_mods'] ?? array();
        $GLOBALS['test_theme_mods'][OP_CLICK_ANALYTICS_EXCLUDED_URLS] = 'private=1';
        try {
            $this->assertFalse(cocoon_click_normalize_destination('https://outside.test/path?private=1', 'https://example.com/article'));
        } finally {
            $GLOBALS['test_theme_mods'] = $previous;
        }
    }

    public function testContactLabelDoesNotStoreAddress(): void
    {
        $destination = cocoon_click_normalize_destination('mailto:person@example.com', 'https://example.com/article');
        $identity = cocoon_click_build_link_identity(10, $destination, array('label' => 'person@example.com'));
        $this->assertSame('mailto:', $identity['anchor_text']);
    }

    public function testOverviewArrivalRateUsesInternalClicksOnly(): void
    {
        $row = cocoon_click_metric_row(array('clicks' => 100, 'internal_clicks' => 10, 'arrivals' => 10));
        $this->assertEquals(1, $row['arrival_rate']);
        $external = cocoon_click_metric_row(array('clicks' => 90, 'destination_type' => 'external'));
        $this->assertNull($external['arrival_rate']);
    }

    public function testValidCookieIsExcludedEvenAfterRestClearsCurrentUser(): void
    {
        \Brain\Monkey\Functions\when('wp_validate_auth_cookie')->justReturn(42);
        $this->assertTrue(cocoon_click_request_user_is_excluded());
    }

    public function testInternalOutcomesRespectInternalTrackingSetting(): void
    {
        $previous = $GLOBALS['test_theme_mods'] ?? array();
        $GLOBALS['test_theme_mods'][OP_CLICK_ANALYTICS_TRACK_INTERNAL] = 0;
        $GLOBALS['test_theme_mods'][OP_CLICK_ANALYTICS_OUTCOMES] = 1;
        try {
            $this->assertFalse(cocoon_click_event_is_trackable('internal_outcome', 'internal'));
        } finally {
            $GLOBALS['test_theme_mods'] = $previous;
        }
    }

    public function testMapPreviewUsesDeviceWidths(): void
    {
        $this->assertSame(390, cocoon_click_map_device_width('mobile'));
        $this->assertSame(820, cocoon_click_map_device_width('tablet'));
        $this->assertSame(1280, cocoon_click_map_device_width('desktop'));
        $this->assertSame(1280, cocoon_click_map_device_width('invalid'));
    }
}
