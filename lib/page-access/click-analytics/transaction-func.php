<?php //クリック解析のトランザクション保護
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (class_exists('wpdb') && !class_exists('Cocoon_Click_Transaction_DB')):
// 同じ接続を使い、接続切断後に途中のSQLだけが自動確定されることを防ぎます。
class Cocoon_Click_Transaction_DB extends wpdb {
  public function __construct($source){
    foreach (get_object_vars($source) as $name => $value) {
      if (property_exists('wpdb', $name)) $this->$name = $value;
    }
  }

  public function check_connection($allow_bail = true){
    return false;
  }

  // 一時的に借りた接続を閉じず、クエリ結果と件数を元のwpdbへ返します。
  public function restore($source){
    foreach (get_object_vars($this) as $name => $value) $source->$name = $value;
  }
}
endif;

if ( !function_exists( 'cocoon_click_begin_transaction' ) ):
function cocoon_click_begin_transaction($lock_wait_timeout = 0){
  global $wpdb;
  if (!class_exists('Cocoon_Click_Transaction_DB') || $wpdb instanceof Cocoon_Click_Transaction_DB) return false;
  // 月次集計などの長い排他ロックを待ち続けてPHPワーカーを占有しないための待ち時間短縮
  if ($lock_wait_timeout > 0) $wpdb->query($wpdb->prepare('SET SESSION innodb_lock_wait_timeout = %d', $lock_wait_timeout));
  // 元の接続管理に書き込み先を選ばせてから、その接続だけで最後まで処理します。
  if ($wpdb->query('START TRANSACTION') === false) return false;
  if (!($wpdb->dbh instanceof mysqli)) {
    $wpdb->query('ROLLBACK');
    return false;
  }
  $original = $wpdb;
  $wpdb = new Cocoon_Click_Transaction_DB($original);
  return $original;
}
endif;

if ( !function_exists( 'cocoon_click_end_transaction' ) ):
function cocoon_click_end_transaction($original, $restore_lock_wait_timeout = false){
  global $wpdb;
  if ($wpdb instanceof Cocoon_Click_Transaction_DB) $wpdb->restore($original);
  $wpdb = $original;
  if ($restore_lock_wait_timeout) $wpdb->query('SET SESSION innodb_lock_wait_timeout = DEFAULT');
}
endif;
