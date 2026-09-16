<?php //クリック解析フロントエンドアセット
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

define('COCOON_CLICK_ANALYTICS_SCRIPT_HANDLE', 'cocoon-click-analytics');
add_action('wp_enqueue_scripts', 'cocoon_click_enqueue_tracking_script', 30);
add_filter('script_loader_tag', 'cocoon_click_add_defer_attribute', 10, 2);

if ( !function_exists( 'cocoon_click_enqueue_tracking_script' ) ):
function cocoon_click_enqueue_tracking_script(){
  if (!cocoon_click_should_enqueue()) return;
  $post_id = (int) get_queried_object_id();
  $rate = get_click_analytics_sampling_rate();
  $layout_revision = cocoon_click_layout_revision($post_id);
  $path = get_cocoon_template_directory() . '/js/click-analytics.js';
  wp_enqueue_script(
    COCOON_CLICK_ANALYTICS_SCRIPT_HANDLE,
    get_cocoon_template_directory_uri() . '/js/click-analytics.js',
    array(),
    file_exists($path) ? filemtime($path) : CLICK_ANALYTICS_TABLE_VERSION,
    true
  );
  $config = array(
    'endpoint' => rest_url('cocoon/v1/click-events'),
    'sourcePostId' => $post_id,
    'layoutRevision' => $layout_revision,
    'samplingRate' => $rate,
    'token' => cocoon_click_tracking_token($post_id, $layout_revision, $rate),
    'siteHosts' => cocoon_click_site_hosts(),
    'excludedDomains' => get_click_analytics_excluded_domains(),
    'excludedUrls' => get_click_analytics_excluded_urls(),
    'trackInternal' => is_click_analytics_track_internal(),
    'trackExternal' => is_click_analytics_track_external(),
    'trackSpecial' => is_click_analytics_track_special(),
    'impressions' => is_click_analytics_impressions_enable(),
    'heatmap' => is_click_analytics_heatmap_enable(),
    'outcomes' => is_click_analytics_outcomes_enable(),
    'respectPrivacy' => is_click_analytics_respect_privacy(),
    'initialConsent' => (bool) apply_filters('cocoon_click_analytics_initial_consent', true),
  );
  // 真偽値を文字列へ変換せず、同意待ちの false をそのままブラウザーへ渡します。
  wp_add_inline_script(COCOON_CLICK_ANALYTICS_SCRIPT_HANDLE, 'window.CocoonClickAnalyticsConfig = ' . wp_json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';', 'before');
}
endif;

if ( !function_exists( 'cocoon_click_add_defer_attribute' ) ):
function cocoon_click_add_defer_attribute($tag, $handle){
  if ($handle !== COCOON_CLICK_ANALYTICS_SCRIPT_HANDLE || strpos($tag, ' defer') !== false) return $tag;
  // LCP用画像やCSSの取得を優先できるよう、解析スクリプトの通信優先度を下げます。
  return str_replace(' src=', ' defer fetchpriority="low" src=', $tag);
}
endif;
