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
add_action(COCOON_CLICK_CRON_CONTINUE_HOOK, 'cocoon_click_continue_maintenance');
add_action('switch_theme', 'cocoon_click_unschedule_maintenance');

if ( !function_exists( 'cocoon_click_manage_cron_schedule' ) ):
function cocoon_click_manage_cron_schedule(){
  $scheduled = wp_next_scheduled(COCOON_CLICK_CRON_HOOK);
  // 計測停止後も保存済みデータの保持期限を管理します。
  $enabled = is_click_analytics_enable();
  $needed = get_theme_option('click_analytics_maintenance_needed', null);
  if ($enabled && !$needed) {
    $needed = true;
    set_theme_mod('click_analytics_maintenance_needed', true);
  } elseif ($needed === null && cocoon_click_tables_exist()) {
    global $wpdb;
    // 旧版から移行したデータも整理し、未使用のサイトには定期処理を追加しません。
    $tables = array(CLICK_LINKS_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_LIMITS_TABLE_NAME);
    $checks = array_map(function($table){ return "EXISTS(SELECT 1 FROM `{$table}` LIMIT 1)"; }, $tables);
    $needed = (bool) $wpdb->get_var('SELECT ' . implode(' OR ', $checks));
    if (!$wpdb->last_error) set_theme_mod('click_analytics_maintenance_needed', $needed);
  }
  $maintain = $enabled || $needed;
  if ($maintain && !$scheduled) wp_schedule_event(time() + 300, 'daily', COCOON_CLICK_CRON_HOOK);
  if (!$maintain && $scheduled) wp_unschedule_event($scheduled, COCOON_CLICK_CRON_HOOK);
  $continuation = wp_next_scheduled(COCOON_CLICK_CRON_CONTINUE_HOOK);
  if (!$maintain && $continuation) wp_unschedule_event($continuation, COCOON_CLICK_CRON_CONTINUE_HOOK);
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
  $previous = get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_STATUS, array());
  $previous = is_array($previous) ? $previous : array();
  $finalized = isset($previous['finalized_through']) ? $previous['finalized_through'] : '';
  $oldest = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -' . get_click_analytics_monthly_retention() . ' months'));
  // 一度確定した月は再計算せず、日次削除後の不完全な合計で上書きしません。
  // 初回は未確定の受信が見えない場合もあるため、保存対象の全月をロックして確定します。
  $first = $finalized !== '' ? gmdate('Y-m-01', strtotime($finalized . '-01 +1 month')) : $oldest;
  $first = max($first, $oldest);
  $current = current_time('Y-m-01');
  $columns = array('clicks', 'unique_clicks', 'sampled_impressions', 'sampled_clicks', 'weighted_impressions', 'weighted_clicks', 'weight_squared', 'arrivals', 'engaged_arrivals', 'total_time_to_click_ms', 'received_events', 'rejected_events', 'accepted_batches', 'duplicate_batches');
  $selects = array();
  $updates = array();
  foreach ($columns as $column) {
    $selects[] = 'SUM(' . $column . ') AS ' . $column;
    // 旧版で日次が一部削除済みの月も、既存の月次値を減らさず移行します。
    $updates[] = $column . '=GREATEST(' . $column . ',VALUES(' . $column . '))';
  }
  $updates[] = 'updated_at=VALUES(updated_at)';
  // 日次の合計と既存の月次の大きい方を採用し、Cron再実行時の二重集計を回避
  $sql = "INSERT INTO `{$monthly}` (stat_month,source_post_id,link_id,device,layout_revision," . implode(',', $columns) . ",updated_at)
    SELECT DATE_FORMAT(stat_date,'%Y-%m'),source_post_id,link_id,device,layout_revision," . implode(',', $selects) . ",%s
    FROM `{$daily}` WHERE stat_date >= %s AND stat_date < %s
    GROUP BY DATE_FORMAT(stat_date,'%Y-%m'),source_post_id,link_id,device,layout_revision
    ON DUPLICATE KEY UPDATE " . implode(',', $updates);
  $rows = 0;
  if ($first < $current) {
    $rows = false;
    $original_db = cocoon_click_begin_transaction();
    if ($original_db) {
      try {
        // 前月に開始した受信が確定するのを待ってから、月次集計を凍結します。
        for ($month = substr($first, 0, 7); $month < substr($current, 0, 7); $month = gmdate('Y-m', strtotime($month . '-01 +1 month'))) {
          if (!cocoon_click_lock_month($month, true)) throw new RuntimeException('month_lock');
        }
        $rows = $wpdb->query($wpdb->prepare($sql, current_time('mysql'), $first, $current));
        if ($rows === false || $wpdb->query('COMMIT') === false) throw new RuntimeException('rollup');
      } catch (Throwable $error) {
        $wpdb->query('ROLLBACK');
        $rows = false;
      } finally {
        cocoon_click_end_transaction($original_db);
      }
    }
  }
  $status = array(
    'status' => $rows === false ? 'error' : 'success',
    'started_at' => $started_at,
    'completed_at' => current_time('mysql'),
    'rows_affected' => $rows === false ? 0 : (int) $rows,
    'covered_from' => isset($previous['covered_from']) ? min($previous['covered_from'], substr($first, 0, 7)) : substr($first, 0, 7),
    'covered_through' => $rows === false ? $finalized : gmdate('Y-m', strtotime($current . ' -1 day')),
    'finalized_through' => $rows === false ? $finalized : gmdate('Y-m', strtotime($current . ' -1 day')),
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
  $cursor = max(0, (int) get_theme_option('click_analytics_enrichment_cursor', 0));
  $rows = $wpdb->get_results($wpdb->prepare("SELECT id,destination_url FROM `" . CLICK_LINKS_TABLE_NAME . "` WHERE destination_type='internal' AND target_post_id=0 AND id>%d ORDER BY id ASC LIMIT 50", $cursor), ARRAY_A);
  if ($wpdb->last_error) return false;
  foreach ((array) $rows as $row) {
    // 解決不能なURLも処理済み位置を進め、後続の投稿リンクを補完します。
    $post_id = url_to_postid($row['destination_url']);
    if ($post_id > 0 && $wpdb->update(CLICK_LINKS_TABLE_NAME, array('target_post_id' => $post_id), array('id' => (int) $row['id']), array('%d'), array('%d')) === false) return false;
    $cursor = (int) $row['id'];
  }
  set_theme_mod('click_analytics_enrichment_cursor', count((array) $rows) === 50 ? $cursor : 0);
  return true;
}
endif;

if ( !function_exists( 'cocoon_click_run_maintenance' ) ):
function cocoon_click_run_maintenance($purge_only = false){
  global $wpdb;
  if (!cocoon_click_tables_exist()) return;
  // DB接続に属するロックを使い、同時Cronによる重複集計を防ぎます。
  $lock = cocoon_click_hmac('maintenance|' . CLICK_STATS_DAILY_TABLE_NAME);
  if ((int) $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)', $lock)) !== 1) return;
  $started = microtime(true);
  $remaining = false;
  $failed = false;
  try {
    if (!$purge_only) {
      if (is_click_analytics_enable()) cocoon_click_update_sampling_rate();
      $monthly_status = cocoon_click_rollup_monthly();
      if ($monthly_status['status'] !== 'success') $failed = true;
      if (!cocoon_click_enrich_internal_targets()) $failed = true;
    }
    $monthly_status = get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_STATUS, array());
    $today = current_time('Y-m-d');
    $daily_cutoff = cocoon_click_retention_cutoff($today, get_click_analytics_daily_retention());
    $short_cutoff = gmdate('Y-m-d H:i:s', strtotime(current_time('mysql') . ' -2 days'));
    $month_cutoff = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -' . get_click_analytics_monthly_retention() . ' months'));
    // 有効期限の短いカウンターを先に整理し、送信量の多い環境でも滞留を防ぎます。
    $purges = array(array(CLICK_LIMITS_TABLE_NAME, 'expires_at', gmdate('Y-m-d H:i:s')));
    // 月次へ確定した期間だけを削除し、失敗時や当月の元データは次回まで保持します。
    if (!empty($monthly_status['finalized_through']) && !$failed) {
      $archived_end = gmdate('Y-m-01', strtotime($monthly_status['finalized_through'] . '-01 +1 month'));
      $purges[] = array(CLICK_STATS_DAILY_TABLE_NAME, 'stat_date', min($daily_cutoff, $archived_end));
    }
    $purges = array_merge($purges, array(
      array(CLICK_HEATMAP_DAILY_TABLE_NAME, 'stat_date', $daily_cutoff),
      array(CLICK_BATCHES_TABLE_NAME, 'expires_at', current_time('mysql')),
      array(CLICK_UNIQUES_TABLE_NAME, 'created_at', $short_cutoff),
      array(CLICK_STATS_MONTHLY_TABLE_NAME, 'stat_month', $month_cutoff),
    ));
    // 重い集計とは別に削除用の時間枠を確保し、継続時は削除だけを再開します。
    $purge_started = microtime(true);
    foreach ($purges as $purge) {
      // 1,000行ずつ繰り返し、残り時間を実際の削除に使います。
      do {
        if ((microtime(true) - $purge_started) >= 10) { $remaining = true; break 2; }
        $deleted = cocoon_click_delete_limited($purge[0], $purge[1], $purge[2]);
        if ($deleted === false) { $failed = true; $remaining = true; break; }
        if ($purge[0] === CLICK_STATS_DAILY_TABLE_NAME) {
          // 保持日数を延ばしても、削除済みの期間を日次表から読まないよう記録します。
          set_theme_mod('click_analytics_daily_purged_before', max((string) get_theme_option('click_analytics_daily_purged_before', ''), $purge[2]));
        }
      } while ((int) $deleted >= 1000);
    }
    do {
      if ((microtime(true) - $purge_started) >= 10) { $remaining = true; break; }
      $deleted = cocoon_click_prune_definitions($month_cutoff . '-01 00:00:00');
      if ($deleted === false) { $failed = true; $remaining = true; break; }
    } while ((int) $deleted >= 1000);
  } finally {
    $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $lock));
  }
  if ($remaining && !wp_next_scheduled(COCOON_CLICK_CRON_CONTINUE_HOOK)) wp_schedule_single_event(time() + 300, COCOON_CLICK_CRON_CONTINUE_HOOK);
  set_theme_mod(OP_CLICK_ANALYTICS_MAINTENANCE_STATUS, array(
    'status' => $failed ? 'error' : 'success',
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

if ( !function_exists( 'cocoon_click_continue_maintenance' ) ):
function cocoon_click_continue_maintenance(){
  cocoon_click_run_maintenance(true);
}
endif;

if ( !function_exists( 'cocoon_click_prune_definitions' ) ):
function cocoon_click_prune_definitions($cutoff){
  global $wpdb;
  // 保存期間を過ぎ、日次・月次のどちらからも参照されない定義だけを少しずつ削除します。
  return $wpdb->query($wpdb->prepare('DELETE FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE last_seen_at<%s
    AND NOT EXISTS (SELECT 1 FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '` d WHERE d.link_id=`' . CLICK_LINKS_TABLE_NAME . '`.id)
    AND NOT EXISTS (SELECT 1 FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '` m WHERE m.link_id=`' . CLICK_LINKS_TABLE_NAME . '`.id)
    LIMIT 1000', $cutoff));
}
endif;
