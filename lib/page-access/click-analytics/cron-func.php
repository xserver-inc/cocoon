<?php //クリック解析定期集計
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

define('COCOON_CLICK_CRON_HOOK', 'cocoon_click_analytics_daily_maintenance');
define('COCOON_CLICK_CRON_CONTINUE_HOOK', 'cocoon_click_analytics_continue_maintenance');
define('OP_CLICK_ANALYTICS_MONTHLY_STATUS', 'click_analytics_monthly_status');
define('OP_CLICK_ANALYTICS_MAINTENANCE_STATUS', 'click_analytics_maintenance_status');
add_action('init', 'cocoon_click_manage_cron_schedule');
add_action(COCOON_CLICK_CRON_HOOK, 'cocoon_click_run_maintenance');
add_action(COCOON_CLICK_CRON_CONTINUE_HOOK, 'cocoon_click_run_maintenance');
add_action('switch_theme', 'cocoon_click_unschedule_maintenance');

if ( !function_exists( 'cocoon_click_manage_cron_schedule' ) ):
function cocoon_click_manage_cron_schedule(){
  $scheduled = wp_next_scheduled(COCOON_CLICK_CRON_HOOK);
  if (is_click_analytics_enable() && !$scheduled) wp_schedule_event(time() + 300, 'daily', COCOON_CLICK_CRON_HOOK);
  if (!is_click_analytics_enable() && $scheduled) wp_unschedule_event($scheduled, COCOON_CLICK_CRON_HOOK);
  $continuation = wp_next_scheduled(COCOON_CLICK_CRON_CONTINUE_HOOK);
  if (!is_click_analytics_enable() && $continuation) wp_unschedule_event($continuation, COCOON_CLICK_CRON_CONTINUE_HOOK);
}
endif;

if ( !function_exists( 'cocoon_click_unschedule_maintenance' ) ):
function cocoon_click_unschedule_maintenance(){
  $scheduled = wp_next_scheduled(COCOON_CLICK_CRON_HOOK);
  if ($scheduled) wp_unschedule_event($scheduled, COCOON_CLICK_CRON_HOOK);
  $continuation = wp_next_scheduled(COCOON_CLICK_CRON_CONTINUE_HOOK);
  if ($continuation) wp_unschedule_event($continuation, COCOON_CLICK_CRON_CONTINUE_HOOK);
}
endif;

if ( !function_exists( 'cocoon_click_update_sampling_rate' ) ):
function cocoon_click_update_sampling_rate(){
  global $wpdb;
  $from = gmdate('Y-m-d', strtotime(current_time('Y-m-d') . ' -6 days'));
  $table = CLICK_STATS_DAILY_TABLE_NAME;
  $row = $wpdb->get_row($wpdb->prepare(
    "SELECT COALESCE(SUM(weighted_impressions),0) AS pageviews FROM `{$table}` WHERE link_id=0 AND stat_date BETWEEN %s AND %s",
    $from,
    current_time('Y-m-d')
  ), ARRAY_A);
  $enabled_at = (string) get_theme_option(OP_CLICK_ANALYTICS_ENABLED_AT, '');
  if ($enabled_at === '') {
    $enabled_at = current_time('mysql');
    set_theme_mod(OP_CLICK_ANALYTICS_ENABLED_AT, $enabled_at);
  }
  $history_started = strtotime($enabled_at);
  $has_enough_history = $history_started !== false && $history_started <= strtotime(current_time('mysql') . ' -6 days');
  $daily = $has_enough_history ? (float) $row['pageviews'] / 7 : 0;
  $rate = cocoon_click_sampling_rate_for_daily_pv($daily, $has_enough_history);
  $rate = (int) apply_filters('cocoon_click_analytics_sampling_rate', $rate);
  if (!in_array($rate, cocoon_click_allowed_sampling_rates(), true)) $rate = 10;
  set_theme_mod(OP_CLICK_ANALYTICS_SAMPLING_RATE, $rate);
  set_theme_mod(OP_CLICK_ANALYTICS_SAMPLING_UPDATED, current_time('mysql'));
}
endif;

if ( !function_exists( 'cocoon_click_rollup_monthly' ) ):
function cocoon_click_rollup_monthly(){
  global $wpdb;
  $started_at = current_time('mysql');
  $daily = CLICK_STATS_DAILY_TABLE_NAME;
  $monthly = CLICK_STATS_MONTHLY_TABLE_NAME;
  $first = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -3 months'));
  $current = current_time('Y-m-01');
  $columns = array('clicks', 'unique_clicks', 'sampled_impressions', 'sampled_clicks', 'weighted_impressions', 'weighted_clicks', 'weight_squared', 'arrivals', 'engaged_arrivals', 'total_time_to_click_ms', 'received_events', 'rejected_events', 'accepted_batches', 'duplicate_batches');
  $selects = array();
  $updates = array();
  foreach ($columns as $column) {
    $selects[] = 'SUM(' . $column . ') AS ' . $column;
    $updates[] = $column . '=VALUES(' . $column . ')';
  }
  $updates[] = 'updated_at=VALUES(updated_at)';
  // 初心者向け: 足し直しではなく月全体を置き換え、Cron再実行でも二重集計を防ぎます。
  $sql = "INSERT INTO `{$monthly}` (stat_month,source_post_id,link_id,device,layout_revision," . implode(',', $columns) . ",updated_at)
    SELECT DATE_FORMAT(stat_date,'%Y-%m'),source_post_id,link_id,device,layout_revision," . implode(',', $selects) . ",%s
    FROM `{$daily}` WHERE stat_date >= %s AND stat_date < %s
    GROUP BY DATE_FORMAT(stat_date,'%Y-%m'),source_post_id,link_id,device,layout_revision
    ON DUPLICATE KEY UPDATE " . implode(',', $updates);
  $rows = $wpdb->query($wpdb->prepare($sql, current_time('mysql'), $first, $current));
  $status = array(
    'status' => $rows === false ? 'error' : 'success',
    'started_at' => $started_at,
    'completed_at' => current_time('mysql'),
    'rows_affected' => $rows === false ? 0 : (int) $rows,
    'covered_from' => substr($first, 0, 7),
    'covered_through' => gmdate('Y-m', strtotime($current . ' -1 day')),
  );
  set_theme_mod(OP_CLICK_ANALYTICS_MONTHLY_STATUS, $status);
  return $status;
}
endif;

if ( !function_exists( 'cocoon_click_delete_limited' ) ):
function cocoon_click_delete_limited($table, $column, $cutoff){
  global $wpdb;
  return $wpdb->query($wpdb->prepare("DELETE FROM `{$table}` WHERE `{$column}` < %s LIMIT 1000", $cutoff));
}
endif;

if ( !function_exists( 'cocoon_click_enrich_internal_targets' ) ):
function cocoon_click_enrich_internal_targets(){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT id,destination_url FROM `" . CLICK_LINKS_TABLE_NAME . "` WHERE destination_type='internal' AND target_post_id=0 ORDER BY id ASC LIMIT 50", ARRAY_A);
  foreach ((array) $rows as $row) {
    // 初心者向け: URLから投稿IDを探す重い処理は閲覧時ではなくCronで少しずつ行います。
    $post_id = url_to_postid($row['destination_url']);
    if ($post_id > 0) $wpdb->update(CLICK_LINKS_TABLE_NAME, array('target_post_id' => $post_id), array('id' => (int) $row['id']), array('%d'), array('%d'));
  }
}
endif;

if ( !function_exists( 'cocoon_click_run_maintenance' ) ):
function cocoon_click_run_maintenance(){
  if (!is_click_analytics_enable() || !cocoon_click_tables_exist()) return;
  $lock = 'cocoon_click_maintenance_lock';
  if (get_transient($lock)) return;
  set_transient($lock, 1, 15 * MINUTE_IN_SECONDS);
  $started = microtime(true);
  cocoon_click_update_sampling_rate();
  $monthly_status = cocoon_click_rollup_monthly();
  cocoon_click_enrich_internal_targets();
  $today = current_time('Y-m-d');
  $daily_cutoff = cocoon_click_retention_cutoff($today, get_click_analytics_daily_retention());
  $short_cutoff = gmdate('Y-m-d H:i:s', strtotime(current_time('mysql') . ' -2 days'));
  $month_cutoff = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -' . get_click_analytics_monthly_retention() . ' months'));
  $purges = array(
    array(CLICK_STATS_DAILY_TABLE_NAME, 'stat_date', $daily_cutoff),
    array(CLICK_HEATMAP_DAILY_TABLE_NAME, 'stat_date', $daily_cutoff),
    array(CLICK_BATCHES_TABLE_NAME, 'expires_at', current_time('mysql')),
    array(CLICK_UNIQUES_TABLE_NAME, 'created_at', $short_cutoff),
    array(CLICK_STATS_MONTHLY_TABLE_NAME, 'stat_month', $month_cutoff),
  );
  $remaining = false;
  foreach ($purges as $purge) {
    if ((microtime(true) - $started) >= 10) { $remaining = true; break; }
    if ((int) cocoon_click_delete_limited($purge[0], $purge[1], $purge[2]) >= 1000) $remaining = true;
  }
  delete_transient($lock);
  if ($remaining && !wp_next_scheduled(COCOON_CLICK_CRON_CONTINUE_HOOK)) wp_schedule_single_event(time() + 300, COCOON_CLICK_CRON_CONTINUE_HOOK);
  set_theme_mod(OP_CLICK_ANALYTICS_MAINTENANCE_STATUS, array(
    'status' => $monthly_status['status'] === 'success' ? 'success' : 'error',
    'completed_at' => current_time('mysql'),
    'duration_ms' => (int) round((microtime(true) - $started) * 1000),
    'continuation_scheduled' => $remaining,
  ));
}
endif;

if ( !function_exists( 'cocoon_click_bump_layout_revision' ) ):
function cocoon_click_bump_layout_revision(){
  update_option('cocoon_click_layout_revision', (string) microtime(true), false);
}
endif;
add_action('wp_update_nav_menu', 'cocoon_click_bump_layout_revision');
add_action('update_option_sidebars_widgets', 'cocoon_click_bump_layout_revision');
add_action('customize_save_after', 'cocoon_click_bump_layout_revision');

if ( !function_exists( 'cocoon_click_theme_mods_changed' ) ):
function cocoon_click_theme_mods_changed($old_value, $new_value){
  $old_value = is_array($old_value) ? $old_value : array();
  $new_value = is_array($new_value) ? $new_value : array();
  foreach (array_unique(array_merge(array_keys($old_value), array_keys($new_value))) as $key) {
    if (strpos((string) $key, 'click_analytics_') === 0) continue;
    if (!array_key_exists($key, $old_value) || !array_key_exists($key, $new_value) || $old_value[$key] !== $new_value[$key]) {
      cocoon_click_bump_layout_revision();
      return;
    }
  }
}
endif;
$cocoon_click_stylesheet = (string) get_option('stylesheet', '');
if ($cocoon_click_stylesheet !== '') add_action('update_option_theme_mods_' . $cocoon_click_stylesheet, 'cocoon_click_theme_mods_changed', 10, 2);
