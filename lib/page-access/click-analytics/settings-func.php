<?php //クリック解析設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

define('OP_CLICK_ANALYTICS_ENABLE', 'click_analytics_enable');
define('OP_CLICK_ANALYTICS_TRACK_INTERNAL', 'click_analytics_track_internal');
define('OP_CLICK_ANALYTICS_TRACK_EXTERNAL', 'click_analytics_track_external');
define('OP_CLICK_ANALYTICS_TRACK_SPECIAL', 'click_analytics_track_special');
define('OP_CLICK_ANALYTICS_IMPRESSIONS', 'click_analytics_impressions');
define('OP_CLICK_ANALYTICS_HEATMAP', 'click_analytics_heatmap');
define('OP_CLICK_ANALYTICS_OUTCOMES', 'click_analytics_outcomes');
define('OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN', 'click_analytics_exclude_logged_in');
define('OP_CLICK_ANALYTICS_RESPECT_PRIVACY', 'click_analytics_respect_privacy');
define('OP_CLICK_ANALYTICS_DAILY_RETENTION', 'click_analytics_daily_retention');
define('OP_CLICK_ANALYTICS_MONTHLY_RETENTION', 'click_analytics_monthly_retention');
define('OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS', 'click_analytics_excluded_domains');
define('OP_CLICK_ANALYTICS_EXCLUDED_URLS', 'click_analytics_excluded_urls');
define('OP_CLICK_ANALYTICS_QUERY_ALLOWLIST', 'click_analytics_query_allowlist');
define('OP_CLICK_ANALYTICS_SAMPLING_RATE', 'click_analytics_sampling_rate');
define('OP_CLICK_ANALYTICS_SAMPLING_UPDATED', 'click_analytics_sampling_updated');
define('OP_CLICK_ANALYTICS_ENABLED_AT', 'click_analytics_enabled_at');

if ( !function_exists( 'is_click_analytics_enable' ) ):
function is_click_analytics_enable(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_ENABLE, 0);
}
endif;

if ( !function_exists( 'is_click_analytics_track_internal' ) ):
function is_click_analytics_track_internal(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_TRACK_INTERNAL, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_track_external' ) ):
function is_click_analytics_track_external(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_track_special' ) ):
function is_click_analytics_track_special(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_TRACK_SPECIAL, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_impressions_enable' ) ):
function is_click_analytics_impressions_enable(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_IMPRESSIONS, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_heatmap_enable' ) ):
function is_click_analytics_heatmap_enable(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_HEATMAP, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_outcomes_enable' ) ):
function is_click_analytics_outcomes_enable(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_OUTCOMES, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_exclude_logged_in' ) ):
function is_click_analytics_exclude_logged_in(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN, 1);
}
endif;

if ( !function_exists( 'is_click_analytics_respect_privacy' ) ):
function is_click_analytics_respect_privacy(){
  return (bool) get_theme_option(OP_CLICK_ANALYTICS_RESPECT_PRIVACY, 1);
}
endif;

if ( !function_exists( 'get_click_analytics_daily_retention' ) ):
function get_click_analytics_daily_retention(){
  $allowed = array(30, 90, 400);
  $value = (int) get_theme_option(OP_CLICK_ANALYTICS_DAILY_RETENTION, 90);
  return in_array($value, $allowed, true) ? $value : 90;
}
endif;

if ( !function_exists( 'get_click_analytics_monthly_retention' ) ):
function get_click_analytics_monthly_retention(){
  $allowed = array(12, 24, 60);
  $value = (int) get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, 24);
  return in_array($value, $allowed, true) ? $value : 24;
}
endif;

if ( !function_exists( 'cocoon_click_allowed_sampling_rates' ) ):
function cocoon_click_allowed_sampling_rates(){
  return array(1, 5, 10, 20, 100);
}
endif;

if ( !function_exists( 'cocoon_click_sampling_rate_for_daily_pv' ) ):
function cocoon_click_sampling_rate_for_daily_pv($daily_pv, $has_enough_history = true){
  if (!$has_enough_history) return 10;
  $daily_pv = max(0, (float) $daily_pv);
  if ($daily_pv <= 500) return 100;
  if ($daily_pv <= 2500) return 20;
  if ($daily_pv <= 10000) return 5;
  return 1;
}
endif;

if ( !function_exists( 'get_click_analytics_sampling_rate' ) ):
function get_click_analytics_sampling_rate(){
  $rate = (int) get_theme_option(OP_CLICK_ANALYTICS_SAMPLING_RATE, 10);
  if (!in_array($rate, cocoon_click_allowed_sampling_rates(), true)) $rate = 10;
  // 初心者向け: フィルター後も許可済みの率だけに絞り、重み計算が壊れないようにします。
  $rate = (int) apply_filters('cocoon_click_analytics_sampling_rate', $rate);
  return in_array($rate, cocoon_click_allowed_sampling_rates(), true) ? $rate : 10;
}
endif;

if ( !function_exists( 'cocoon_click_option_lines' ) ):
function cocoon_click_option_lines($option_name){
  $raw = (string) get_theme_option($option_name, '');
  $lines = preg_split('/\r\n|\r|\n/', $raw);
  $result = array();
  foreach ((array) $lines as $line) {
    $line = trim($line);
    if ($line !== '') $result[] = $line;
  }
  return array_values(array_unique($result));
}
endif;

if ( !function_exists( 'get_click_analytics_excluded_domains' ) ):
function get_click_analytics_excluded_domains(){
  return array_map('strtolower', cocoon_click_option_lines(OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS));
}
endif;

if ( !function_exists( 'get_click_analytics_excluded_urls' ) ):
function get_click_analytics_excluded_urls(){
  return cocoon_click_option_lines(OP_CLICK_ANALYTICS_EXCLUDED_URLS);
}
endif;

if ( !function_exists( 'get_click_analytics_query_allowlist' ) ):
function get_click_analytics_query_allowlist(){
  $result = array();
  foreach (cocoon_click_option_lines(OP_CLICK_ANALYTICS_QUERY_ALLOWLIST) as $line) {
    $parts = array_map('trim', explode(':', $line, 2));
    if (count($parts) !== 2 || $parts[0] === '') continue;
    $host = strtolower(rtrim($parts[0], '.'));
    $keys = array_filter(array_map('sanitize_key', explode(',', $parts[1])));
    if ($keys) $result[$host] = array_values(array_unique($keys));
  }
  return $result;
}
endif;

if ( !function_exists( 'cocoon_click_hmac' ) ):
function cocoon_click_hmac($value){
  if (function_exists('wp_salt')) {
    $secret = wp_salt('auth');
  } elseif (defined('AUTH_SALT')) {
    $secret = AUTH_SALT;
  } else {
    $secret = THEME_NAME . '|click-analytics';
  }
  // 初心者向け: サイト固有の秘密鍵で変換し、元の識別値をDBへ保存しません。
  return hash_hmac('sha256', (string) $value, $secret);
}
endif;

if ( !function_exists( 'cocoon_click_layout_revision' ) ):
function cocoon_click_layout_revision($post_id){
  $modified = get_post_field('post_modified_gmt', $post_id);
  $site_revision = (string) get_option('cocoon_click_layout_revision', '1');
  return hash('sha256', (string) $post_id . '|' . (string) $modified . '|' . $site_revision);
}
endif;

if ( !function_exists( 'cocoon_click_tracking_token' ) ):
function cocoon_click_tracking_token($post_id, $layout_revision, $sampling_rate){
  return cocoon_click_hmac('source|1|' . (int) $post_id . '|' . $layout_revision . '|' . (int) $sampling_rate);
}
endif;

if ( !function_exists( 'cocoon_click_verify_tracking_token' ) ):
function cocoon_click_verify_tracking_token($post_id, $layout_revision, $sampling_rate, $token){
  $expected = cocoon_click_tracking_token($post_id, $layout_revision, $sampling_rate);
  return is_string($token) && function_exists('hash_equals') && hash_equals($expected, $token);
}
endif;

if ( !function_exists( 'cocoon_click_should_enqueue' ) ):
function cocoon_click_should_enqueue(){
  $enabled = is_click_analytics_enable()
    && !is_admin()
    && is_singular(array('post', 'page'))
    && (!function_exists('is_preview') || !is_preview())
    && (!function_exists('is_customize_preview') || !is_customize_preview());
  if ($enabled && function_exists('is_user_administrator') && is_user_administrator()) $enabled = false;
  if ($enabled && is_click_analytics_exclude_logged_in() && is_user_logged_in()) $enabled = false;
  if ($enabled && function_exists('is_useragent_robot') && is_useragent_robot()) $enabled = false;
  $post_id = $enabled ? (int) get_queried_object_id() : 0;
  if ($enabled && (!$post_id || get_post_status($post_id) !== 'publish')) $enabled = false;
  return (bool) apply_filters('cocoon_click_analytics_should_enqueue', $enabled, $post_id);
}
endif;

if ( !function_exists( 'cocoon_click_save_settings' ) ):
function cocoon_click_save_settings(){
  $was_enabled = is_click_analytics_enable();
  $checkboxes = array(
    OP_CLICK_ANALYTICS_ENABLE,
    OP_CLICK_ANALYTICS_TRACK_INTERNAL,
    OP_CLICK_ANALYTICS_TRACK_EXTERNAL,
    OP_CLICK_ANALYTICS_TRACK_SPECIAL,
    OP_CLICK_ANALYTICS_IMPRESSIONS,
    OP_CLICK_ANALYTICS_HEATMAP,
    OP_CLICK_ANALYTICS_OUTCOMES,
    OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN,
    OP_CLICK_ANALYTICS_RESPECT_PRIVACY,
  );
  foreach ($checkboxes as $name) {
    set_theme_mod($name, isset($_POST[$name]) ? 1 : 0);
  }
  if (!$was_enabled && is_click_analytics_enable()) set_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT, current_time('mysql'));
  if (!is_click_analytics_enable()) remove_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT);
  $daily = isset($_POST[OP_CLICK_ANALYTICS_DAILY_RETENTION]) ? (int) $_POST[OP_CLICK_ANALYTICS_DAILY_RETENTION] : 90;
  $monthly = isset($_POST[OP_CLICK_ANALYTICS_MONTHLY_RETENTION]) ? (int) $_POST[OP_CLICK_ANALYTICS_MONTHLY_RETENTION] : 24;
  set_theme_mod(OP_CLICK_ANALYTICS_DAILY_RETENTION, in_array($daily, array(30, 90, 400), true) ? $daily : 90);
  set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, in_array($monthly, array(12, 24, 60), true) ? $monthly : 24);
  foreach (array(OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS, OP_CLICK_ANALYTICS_EXCLUDED_URLS, OP_CLICK_ANALYTICS_QUERY_ALLOWLIST) as $name) {
    $value = isset($_POST[$name]) ? sanitize_textarea_field(wp_unslash($_POST[$name])) : '';
    set_theme_mod($name, $value);
  }
}
endif;

if ( !function_exists( 'cocoon_click_map_device_width' ) ):
function cocoon_click_map_device_width($device){
  // 管理画面の幅に左右されず、選んだ端末のレスポンシブ配置を再現します。
  $widths = array('mobile' => 390, 'tablet' => 820, 'desktop' => 1280);
  return isset($widths[$device]) ? $widths[$device] : $widths['desktop'];
}
endif;
