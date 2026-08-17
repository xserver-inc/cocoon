<?php //クリック解析テーブル
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;
define('CLICK_ANALYTICS_TABLE_VERSION', '0.5.0');
define('OP_CLICK_ANALYTICS_TABLE_VERSION', 'click_analytics_table_version');
define('CLICK_LINKS_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_links');
define('CLICK_STATS_DAILY_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_stats_daily');
define('CLICK_STATS_MONTHLY_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_stats_monthly');
define('CLICK_HEATMAP_DAILY_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_heatmap_daily');
define('CLICK_BATCHES_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_batches');
define('CLICK_UNIQUES_TABLE_NAME', $wpdb->prefix . THEME_NAME . '_click_uniques');

if ( !function_exists( 'cocoon_click_tables_exist' ) ):
function cocoon_click_tables_exist($force_database_check = false){
  if (!function_exists('is_db_table_exist')) return false;
  if (!$force_database_check && get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION, '') === CLICK_ANALYTICS_TABLE_VERSION) return true;
  global $wpdb;
  $tables = array(CLICK_LINKS_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME);
  $placeholders = implode(',', array_fill(0, count($tables), '%s'));
  // 初心者向け: 6テーブルを1件ずつ確認せず、初回だけ1本のSQLでまとめて確認します。
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
  );
  $tables[] = "CREATE TABLE `" . CLICK_LINKS_TABLE_NAME . "` (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    link_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    slot_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    source_post_id bigint(20) unsigned NOT NULL,
    destination_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    destination_url text NOT NULL,
    destination_host varchar(191) NOT NULL DEFAULT '',
    destination_type varchar(32) NOT NULL DEFAULT 'external',
    target_post_id bigint(20) unsigned NOT NULL DEFAULT 0,
    semantic_area varchar(32) NOT NULL DEFAULT 'other',
    heading_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '',
    heading_label varchar(191) NOT NULL DEFAULT '',
    occurrence_no int(10) unsigned NOT NULL DEFAULT 0,
    anchor_text varchar(191) NOT NULL DEFAULT '',
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
    layout_revision char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '',
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
    layout_revision char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '',
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
    layout_revision char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    x_bin tinyint(3) unsigned NOT NULL,
    y_bin tinyint(3) unsigned NOT NULL,
    clicks bigint(20) unsigned NOT NULL DEFAULT 0,
    updated_at datetime NOT NULL,
    PRIMARY KEY  (stat_date,source_post_id,device,layout_revision,x_bin,y_bin),
    KEY source_date (source_post_id,stat_date)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_BATCHES_TABLE_NAME . "` (
    batch_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    received_at datetime NOT NULL,
    expires_at datetime NOT NULL,
    PRIMARY KEY  (batch_key),
    KEY expires_at (expires_at)
  )";
  $tables[] = "CREATE TABLE `" . CLICK_UNIQUES_TABLE_NAME . "` (
    stat_date date NOT NULL,
    link_id bigint(20) unsigned NOT NULL,
    session_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    batch_key char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY  (stat_date,link_id,session_key),
    KEY batch_key (batch_key),
    KEY created_at (created_at)
  )";
  $all_created = true;
  foreach ($tables as $index => $sql) {
    create_db_table($sql);
    // 初回のdbDeltaが部分的に失敗しても、欠けたテーブルだけを安全に1回再作成します。
    if (!is_db_table_exist($table_names[$index])) create_db_table($sql);
    if (!is_db_table_exist($table_names[$index])) $all_created = false;
  }
  cocoon_click_ensure_layout_primary_key(CLICK_STATS_DAILY_TABLE_NAME, 'stat_date');
  cocoon_click_ensure_layout_primary_key(CLICK_STATS_MONTHLY_TABLE_NAME, 'stat_month');
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
  usort($indexes, function($a, $b){ return (int) $a['Seq_in_index'] <=> (int) $b['Seq_in_index']; });
  $columns = array_column($indexes, 'Column_name');
  $expected = array($period_column, 'source_post_id', 'link_id', 'device', 'layout_revision');
  if ($columns === $expected) return true;
  // 初心者向け: レイアウト改訂ごとの集計が混ざらないよう複合主キーを一度だけ更新します。
  return $wpdb->query("ALTER TABLE `{$table}` DROP PRIMARY KEY, ADD PRIMARY KEY (`{$period_column}`,`source_post_id`,`link_id`,`device`,`layout_revision`)") !== false;
}
endif;

if ( !function_exists( 'update_click_analytics_tables' ) ):
function update_click_analytics_tables(){
  $installed = get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION, '');
  if (is_update_db_table($installed, CLICK_ANALYTICS_TABLE_VERSION)) create_click_analytics_tables();
}
endif;

if ( !function_exists( 'cocoon_click_delete_all_data' ) ):
function cocoon_click_delete_all_data(){
  global $wpdb;
  foreach (array(CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_LINKS_TABLE_NAME) as $table) {
    if (is_db_table_exist($table)) $wpdb->query("TRUNCATE TABLE `{$table}`");
  }
}
endif;

add_action('admin_init', 'update_click_analytics_tables');
