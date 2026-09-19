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

class ClickAnalyticsTest extends TestCase
{
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
