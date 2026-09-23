<?php //クリック解析のリンク定義容量管理
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'cocoon_click_lock_definition_count' ) ):
function cocoon_click_lock_definition_count(){
  global $wpdb;
  $count = $wpdb->get_var($wpdb->prepare('SELECT request_count FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key=%s FOR UPDATE', cocoon_click_hmac('definition_capacity')));
  return $count === null || $wpdb->last_error ? false : (int) $count;
}
endif;

if ( !function_exists( 'cocoon_click_write_definition_count' ) ):
function cocoon_click_write_definition_count($count){
  global $wpdb;
  return $wpdb->query($wpdb->prepare('INSERT INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES (%s,%d,%s) ON DUPLICATE KEY UPDATE request_count=VALUES(request_count),expires_at=VALUES(expires_at)', cocoon_click_hmac('definition_capacity'), max(0, (int) $count), '9999-12-31 23:59:59')) !== false;
}
endif;

if ( !function_exists( 'cocoon_click_refresh_definition_count' ) ):
function cocoon_click_refresh_definition_count(){
  global $wpdb;
  // 容量ロック取得後の最初の非ロック読み取りによる最新件数の補正
  $original_db = cocoon_click_begin_transaction(5);
  if (!$original_db) return false;
  try {
    $key = cocoon_click_hmac('definition_capacity');
    if ($wpdb->query($wpdb->prepare('INSERT IGNORE INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES (%s,0,%s)', $key, '9999-12-31 23:59:59')) === false) throw new RuntimeException('definition_lock');
    if (cocoon_click_lock_definition_count() === false) throw new RuntimeException('definition_lock');
    $count = $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`');
    if ($count === null || $wpdb->last_error || !cocoon_click_write_definition_count($count)) throw new RuntimeException('definition_count');
    if ($wpdb->query('COMMIT') === false) throw new RuntimeException('definition_commit');
    return true;
  } catch (Throwable $error) {
    $wpdb->query('ROLLBACK');
    return false;
  } finally {
    cocoon_click_end_transaction($original_db, true);
  }
}
endif;
