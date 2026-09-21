<?php //クリック解析テーブル
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;
define('CLICK_ANALYTICS_TABLE_VERSION', '0.7.0');
define('OP_CLICK_ANALYTICS_TABLE_VERSION', 'click_analytics_table_version');
define('CLICK_LINKS_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_links');
define('CLICK_STATS_DAILY_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_stats_daily');
define('CLICK_STATS_MONTHLY_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_stats_monthly');
define('CLICK_HEATMAP_DAILY_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_heatmap_daily');
define('CLICK_BATCHES_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_batches');
define('CLICK_UNIQUES_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_uniques');
define('CLICK_LIMITS_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_limits');
define('TRANSIENT_CLICK_ANALYTICS_TABLE_UPDATING', THEME_NAME . '_click_analytics_table_updating');

if ( !function_exists( 'cocoon_click_tables_exist' ) ):
function cocoon_click_tables_exist($force_database_check = false){
  if (!function_exists('is_db_table_exist')) return false;
  if (!$force_database_check) return get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION, '') === CLICK_ANALYTICS_TABLE_VERSION && !get_theme_option('click_analytics_schema_error', false);
  global $wpdb;
  $tables = array(CLICK_LINKS_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_LIMITS_TABLE_NAME);
  $placeholders = implode(',', array_fill(0, count($tables), '%s'));
  // 専用テーブルを個別に問い合わせず、1本のSQLでまとめて確認
  $count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=%s AND table_name IN ({$placeholders})", array_merge(array(DB_NAME), $tables)));
  return $count === count($tables);
}
endif;

if ( !function_exists( 'create_click_analytics_tables' ) ):
function create_click_analytics_tables(){
  $tables = array();
  $table_names = array(
    CLICK_LINKS_TABLE_NAME,
    CLICK_STATS_DAILY_TABLE_NAME,
    CLICK_STATS_MONTHLY_TABLE_NAME,
    CLICK_HEATMAP_DAILY_TABLE_NAME,
    CLICK_BATCHES_TABLE_NAME,
    CLICK_UNIQUES_TABLE_NAME,
    CLICK_LIMITS_TABLE_NAME,
  );
  $tables[] = "CREATE TABLE `" . CLICK_LINKS_TABLE_NAME . "` (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    link_key varbinary(64) NOT NULL,
    slot_key varbinary(64) NOT NULL,
    source_post_id bigint(20) unsigned NOT NULL,
    destination_key varbinary(64) NOT NULL,
    destination_url text NOT NULL,
    destination_host varchar(191) NOT NULL DEFAULT '',
    destination_type varchar(32) NOT NULL DEFAULT 'external',
    target_post_id bigint(20) unsigned NOT NULL DEFAULT 0,
    semantic_area varchar(32) NOT NULL DEFAULT 'other',
    heading_key varbinary(64) NOT NULL DEFAULT '',
    heading_label varchar(191) NOT NULL DEFAULT '',
    occurrence_no int(10) unsigned NOT NULL DEFAULT 0,
    anchor_text varchar(191) NOT NULL DEFAULT '',
    image_url varchar(2048) NOT NULL DEFAULT '',
    element_type varchar(32) NOT NULL DEFAULT 'text',
    rel_flags varchar(191) NOT NULL DEFAULT '',
    target_blank tinyint(1) unsigned NOT NULL DEFAULT 0,
    is_affiliate tinyint(1) unsigned NOT NULL DEFAULT 0,
    first_seen_at datetime NOT NULL,
    last_seen_at datetime NOT NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY link_key (link_key),
    KEY slot_key (slot_key),
    KEY source_area (source_post_id,semantic_area),
    KEY destination_lookup (destination_type,destination_host),
    KEY destination_key (destination_key),
    KEY target_post_id (target_post_id),
    KEY active_period (last_seen_at,first_seen_at),
    KEY active_type (destination_type,last_seen_at,first_seen_at)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_STATS_DAILY_TABLE_NAME . "` (
    stat_date date NOT NULL,
    source_post_id bigint(20) unsigned NOT NULL,
    link_id bigint(20) unsigned NOT NULL DEFAULT 0,
    device varchar(16) NOT NULL DEFAULT 'desktop',
    layout_revision varbinary(64) NOT NULL DEFAULT '',
    clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    unique_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    sampled_impressions bigint(20) unsigned NOT NULL DEFAULT 0,
    sampled_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    weighted_impressions bigint(20) unsigned NOT NULL DEFAULT 0,
    weighted_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    weight_squared bigint(20) unsigned NOT NULL DEFAULT 0,
    arrivals bigint(20) unsigned NOT NULL DEFAULT 0,
    engaged_arrivals bigint(20) unsigned NOT NULL DEFAULT 0,
    total_time_to_click_ms bigint(20) unsigned NOT NULL DEFAULT 0,
    received_events bigint(20) unsigned NOT NULL DEFAULT 0,
    rejected_events bigint(20) unsigned NOT NULL DEFAULT 0,
    accepted_batches bigint(20) unsigned NOT NULL DEFAULT 0,
    duplicate_batches bigint(20) unsigned NOT NULL DEFAULT 0,
    updated_at datetime NOT NULL,
    PRIMARY KEY  (stat_date,source_post_id,link_id,device,layout_revision),
    KEY link_date (link_id,stat_date),
    KEY source_date (source_post_id,stat_date)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_STATS_MONTHLY_TABLE_NAME . "` (
    stat_month char(7) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    source_post_id bigint(20) unsigned NOT NULL,
    link_id bigint(20) unsigned NOT NULL DEFAULT 0,
    device varchar(16) NOT NULL DEFAULT 'desktop',
    layout_revision varbinary(64) NOT NULL DEFAULT '',
    clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    unique_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    sampled_impressions bigint(20) unsigned NOT NULL DEFAULT 0,
    sampled_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    weighted_impressions bigint(20) unsigned NOT NULL DEFAULT 0,
    weighted_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    weight_squared bigint(20) unsigned NOT NULL DEFAULT 0,
    arrivals bigint(20) unsigned NOT NULL DEFAULT 0,
    engaged_arrivals bigint(20) unsigned NOT NULL DEFAULT 0,
    total_time_to_click_ms bigint(20) unsigned NOT NULL DEFAULT 0,
    received_events bigint(20) unsigned NOT NULL DEFAULT 0,
    rejected_events bigint(20) unsigned NOT NULL DEFAULT 0,
    accepted_batches bigint(20) unsigned NOT NULL DEFAULT 0,
    duplicate_batches bigint(20) unsigned NOT NULL DEFAULT 0,
    updated_at datetime NOT NULL,
    PRIMARY KEY  (stat_month,source_post_id,link_id,device,layout_revision),
    KEY link_month (link_id,stat_month),
    KEY source_month (source_post_id,stat_month),
    KEY report_candidates (stat_month,link_id,clicks,unique_clicks,weighted_impressions,sampled_clicks,weighted_clicks,weight_squared)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_HEATMAP_DAILY_TABLE_NAME . "` (
    stat_date date NOT NULL,
    source_post_id bigint(20) unsigned NOT NULL,
    device varchar(16) NOT NULL DEFAULT 'desktop',
    layout_revision varbinary(64) NOT NULL,
    x_bin tinyint(3) unsigned NOT NULL,
    y_bin tinyint(3) unsigned NOT NULL,
    clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    updated_at datetime NOT NULL,
    PRIMARY KEY  (stat_date,source_post_id,device,layout_revision,x_bin,y_bin),
    KEY source_date (source_post_id,stat_date)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_BATCHES_TABLE_NAME . "` (
    batch_key varbinary(64) NOT NULL,
    received_at datetime NOT NULL,
    expires_at datetime NOT NULL,
    PRIMARY KEY  (batch_key),
    KEY expires_at (expires_at)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_UNIQUES_TABLE_NAME . "` (
    stat_date date NOT NULL,
    link_id bigint(20) unsigned NOT NULL,
    session_key varbinary(64) NOT NULL,
    batch_key varbinary(64) NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY  (stat_date,link_id,session_key),
    KEY batch_key (batch_key),
    KEY created_at (created_at)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_LIMITS_TABLE_NAME . "` (
    limit_key varbinary(64) NOT NULL,
    request_count bigint(20) unsigned NOT NULL DEFAULT 0,
    expires_at datetime NOT NULL,
    PRIMARY KEY  (limit_key),
    KEY expires_at (expires_at)
  )";
  global $wpdb;
  //行数の多いテーブルでは主キー置換に時間がかかり、PHPのタイムアウトで永久に完了しなくなるおそれ
  if (function_exists('set_time_limit')) {
    @set_time_limit(600);
  }
  $all_created = true;
  foreach ($tables as $index => $sql) {
    // バッチ受理と集計を一括確定できるよう、全テーブルをInnoDBで作成します。
    // dbDeltaは既存主キーの置換ができないため、専用の移行処理へ任せます。
    if (in_array($table_names[$index], array(CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME), true) && is_db_table_exist($table_names[$index])) {
      $sql = preg_replace('/^    PRIMARY KEY[^\n]+\n/m', '', $sql);
    }
    $sql .= ' ENGINE=InnoDB';
    create_db_table($sql);
    // 初回のdbDeltaが部分的に失敗しても、欠けたテーブルだけを安全に1回再作成します。
    if (!is_db_table_exist($table_names[$index])) create_db_table($sql);
    if (!is_db_table_exist($table_names[$index])) $all_created = false;
    // テーブル名だけでなく、必要な列と索引が揃ったことを確認してから更新済みにします。
    preg_match_all('/^    ([a-z_]+) [a-z]/m', $tables[$index], $required_columns);
    $column_rows = $wpdb->get_results("SHOW COLUMNS FROM `{$table_names[$index]}`", ARRAY_A);
    $columns = array_column((array) $column_rows, 'Type', 'Field');
    if ($wpdb->last_error || array_diff($required_columns[1], array_keys($columns))) $all_created = false;
    // ASCII文字列列が混在するとWordPressが日本語SQLを拒否するため、ハッシュ列はバイナリ型を確認します。
    preg_match_all('/^    ([a-z_]+) varbinary\(64\)/m', $tables[$index], $binary_columns);
    foreach ($binary_columns[1] as $column) if (!isset($columns[$column]) || strtolower($columns[$column]) !== 'varbinary(64)') $all_created = false;
    preg_match_all('/^    (?:UNIQUE )?KEY ([a-z_]+)/m', $tables[$index], $required_indexes);
    $indexes = $wpdb->get_results("SHOW INDEX FROM `{$table_names[$index]}`", ARRAY_A);
    if ($wpdb->last_error || array_diff($required_indexes[1], array_column((array) $indexes, 'Key_name'))) $all_created = false;
  }
  if (!cocoon_click_ensure_layout_primary_key(CLICK_STATS_DAILY_TABLE_NAME, 'stat_date')) $all_created = false;
  if (!cocoon_click_ensure_layout_primary_key(CLICK_STATS_MONTHLY_TABLE_NAME, 'stat_month')) $all_created = false;
  foreach ($table_names as $table) {
    $engine = $wpdb->get_var($wpdb->prepare('SELECT ENGINE FROM information_schema.tables WHERE table_schema=%s AND table_name=%s', DB_NAME, $table));
    if (!$engine || (strtolower($engine) !== 'innodb' && $wpdb->query("ALTER TABLE `{$table}` ENGINE=InnoDB") === false)) $all_created = false;
  }
  set_theme_mod('click_analytics_schema_error', !$all_created);
  if ($all_created) set_theme_mod(OP_CLICK_ANALYTICS_TABLE_VERSION, CLICK_ANALYTICS_TABLE_VERSION);
  return $all_created;
}
endif;

if ( !function_exists( 'cocoon_click_ensure_layout_primary_key' ) ):
function cocoon_click_ensure_layout_primary_key($table, $period_column){
  global $wpdb;
  $allowed = array(
    CLICK_STATS_DAILY_TABLE_NAME => 'stat_date',
    CLICK_STATS_MONTHLY_TABLE_NAME => 'stat_month',
  );
  if (!isset($allowed[$table]) || $allowed[$table] !== $period_column) return false;
  $indexes = $wpdb->get_results("SHOW INDEX FROM `{$table}` WHERE Key_name='PRIMARY'", ARRAY_A);
  if (!is_array($indexes) || !$indexes) return false;
  usort($indexes, function($a, $b){ return (int) $a['Seq_in_index'] <=> (int) $b['Seq_in_index']; });
  $columns = array_column($indexes, 'Column_name');
  $expected = array($period_column, 'source_post_id', 'link_id', 'device', 'layout_revision');
  if ($columns === $expected) return true;
  // レイアウト改訂ごとの集計混在を防ぐための複合主キーへの一度限りの移行
  return $wpdb->query("ALTER TABLE `{$table}` DROP PRIMARY KEY, ADD PRIMARY KEY (`{$period_column}`,`source_post_id`,`link_id`,`device`,`layout_revision`)") !== false;
}
endif;

if ( !function_exists( 'update_click_analytics_tables' ) ):
function update_click_analytics_tables(){
  $installed = get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION, '');
  if (!is_update_db_table($installed, CLICK_ANALYTICS_TABLE_VERSION)) return;
  //管理画面表示のたびに呼ばれるため、長時間かかる主キー置換が同時多発してロック待ちになるのを防止
  if (!DEBUG_MODE) {
    if (get_transient(TRANSIENT_CLICK_ANALYTICS_TABLE_UPDATING)) return;
    set_transient(TRANSIENT_CLICK_ANALYTICS_TABLE_UPDATING, 1, 15 * MINUTE_IN_SECONDS);
  }
  create_click_analytics_tables();
  delete_transient(TRANSIENT_CLICK_ANALYTICS_TABLE_UPDATING);
}
endif;

if ( !function_exists( 'cocoon_click_delete_all_data' ) ):
function cocoon_click_delete_all_data(){
  global $wpdb;
  $original_db = cocoon_click_begin_transaction();
  if (!$original_db) return false;
  try {
    // DELETEは途中失敗を戻せるため、参照先だけ消える部分削除を防げます。
    foreach (array(CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_LINKS_TABLE_NAME, CLICK_LIMITS_TABLE_NAME) as $table) {
      // 存在確認の失敗を「表がない」と扱わず、取り消せる表だけを削除します。
      $engine = $wpdb->get_var($wpdb->prepare('SELECT ENGINE FROM information_schema.tables WHERE table_schema=%s AND table_name=%s', DB_NAME, $table));
      if ($wpdb->last_error || ($engine !== null && strtolower($engine) !== 'innodb')) throw new RuntimeException('delete_schema');
      if ($engine !== null && $wpdb->query("DELETE FROM `{$table}`") === false) throw new RuntimeException('delete');
    }
    if ($wpdb->query('COMMIT') === false) throw new RuntimeException('commit');
  } catch (Throwable $error) {
    $wpdb->query('ROLLBACK');
    return false;
  } finally {
    cocoon_click_end_transaction($original_db);
  }
  set_theme_mod('click_analytics_maintenance_needed', false);
  remove_theme_mod('click_analytics_monthly_status');
  remove_theme_mod('click_analytics_daily_purged_before');
  remove_theme_mod('click_analytics_enrichment_cursor');
  return true;
}
endif;

add_action('admin_init', 'update_click_analytics_tables');

// 更新失敗を隠さず、次の管理画面アクセスでも再試行できる状態を保ちます。
add_action('admin_notices', 'cocoon_click_schema_notice');
if ( !function_exists( 'cocoon_click_schema_notice' ) ):
function cocoon_click_schema_notice(){
  if (current_user_can('manage_options') && get_theme_option('click_analytics_schema_error', false)) {
    echo '<div class="notice notice-error"><p>' . esc_html__('クリック解析のデータベース更新に失敗しました。データベースの権限・空き容量を確認してください。計測は更新が完了するまで停止します。', THEME_NAME) . '</p></div>';
  }
}
endif;

if ( !function_exists( 'cocoon_click_lock_month' ) ):
function cocoon_click_lock_month($month, $exclusive = false){
  global $wpdb;
  if (!preg_match('/^[0-9]{4}-[0-9]{2}$/', $month)) return false;
  $key = cocoon_click_hmac('month|' . $month);
  $locking = $exclusive ? ' FOR UPDATE' : ' LOCK IN SHARE MODE';
  $sql = $wpdb->prepare('SELECT request_count FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key=%s' . $locking, $key);
  $value = $wpdb->get_var($sql);
  if ($wpdb->last_error) return false;
  if ($value !== null) return true;
  // 通常の受信は並行できる共有ロック、月次確定だけは排他ロックで処理順を保証します。
  $expires = gmdate('Y-m-d H:i:s', strtotime($month . '-01 +1 month +2 days'));
  if ($wpdb->query($wpdb->prepare('INSERT IGNORE INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES (%s,0,%s)', $key, $expires)) === false) return false;
  return $wpdb->get_var($sql) !== null && !$wpdb->last_error;
}
endif;
