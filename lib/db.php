<?php //データベース共通関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// wp-admin以下の全てのページが表示されたらDBのアップデートチェックをする
add_action('admin_head', 'update_db_tables');
if ( !function_exists( 'update_db_tables' ) ):
function update_db_tables(){
  if (is_admin()) {
    //アクセス数テーブル
    update_accesses_table();
    //吹き出しテーブル
    update_speech_balloons_table();
    //アフィリエイトタグテーブル
    update_affiliate_tags_table();
    //テンプレートテーブル
    update_function_texts_table();
    //アイテムランキングテーブル
    update_item_rankings_table();
  }
}
endif;

//テーブル作成関数
if ( !function_exists( 'create_db_table' ) ):
function create_db_table( $sql ){
   global $wpdb;
   $charset_collate = null;

   // charsetを指定する
   if ( !empty($wpdb->charset) )
      $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

   // 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
   if ( !empty($wpdb->collate) )
      $charset_collate .= "COLLATE {$wpdb->collate}";

    // SQL文でテーブルを作る
    $sql = $sql." $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   //_v($sql);
   $res = dbDelta( $sql );
   return $res;
}
endif;

//関数テキストテーブルのアップデート
if ( !function_exists( 'is_update_db_table' ) ):
function is_update_db_table($installed_ver, $now_ver) {
  // if ( function_exists( '_v' ) ):
  //   _v(THEME_NAME.'アップデートチェック');
  // endif;
  // オプションに登録されたデータベースのバージョンを取得
  // $installed_ver = get_function_texts_table_version();
  // $now_ver = FUNCTION_TEXTS_TABLE_VERSION;
  if ( $installed_ver != $now_ver ) {
    // if ( function_exists( '_v' ) ):
    //   _v(THEME_NAME.'がアップデートされました');
    // endif;
    return true;
  }
}
endif;


//汎用的なテーブルからレコードの取得
if ( !function_exists( 'get_db_table_records' ) ):
function get_db_table_records( $table_name, $column, $keyword = null, $order_by = null){
  global $wpdb;
  $where = null;
  if ($column && $keyword) {
    $where = $wpdb->prepare(' WHERE '.$column.' LIKE %s', '%'.$keyword.'%');
  }
  if ($order_by) {
    $order_by = esc_sql(' ORDER BY '.$order_by);
  }
  $query = "SELECT * FROM {$table_name}".
              $where.
              $order_by;

  $records = $wpdb->get_results( $query );
  //_v($query);

  return $records;
}
endif;


//吹き出しテーブルのアンインストール
if ( !function_exists( 'uninstall_db_table' ) ):
function uninstall_db_table($table_name) {
  global $wpdb;

  $wpdb->query('DROP TABLE IF EXISTS '.$table_name);
}
endif;


//テーブルレコードの取得
if ( !function_exists( 'get_db_table_record' ) ):
function get_db_table_record( $table_name, $id ) {
  global $wpdb;

  $query = $wpdb->prepare("SELECT * FROM {$table_name}  WHERE id = %d", $id);

  $record = $wpdb->get_row( $query );

  return $record;
}
endif;


//テーブルレコードの削除
if ( !function_exists( 'delete_db_table_record' ) ):
function delete_db_table_record( $table_name, $id ) {
  global $wpdb;
  $res = $wpdb->delete( $table_name, array(
      'id' => $id,
    ),
    array('%d')
  );
  return $res;
}
endif;

//キャッシュレコードの削除
if ( !function_exists( 'delete_db_cache_records' ) ):
function delete_db_cache_records( $option_name_part ) {
  global $wpdb;
  $query = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%{$option_name_part}%';";
  return $wpdb->query($query);
}
endif;

//レコードを追加
if ( !function_exists( 'insert_db_table_record' ) ):
function insert_db_table_record($table, $data, $format){
  global $wpdb;
  return $wpdb->insert( $table, $data, $format );
}
endif;

//レコードの編集
if ( !function_exists( 'update_db_table_record' ) ):
function update_db_table_record($table, $data, $where, $format, $where_format){
  global $wpdb;
  return $wpdb->update( $table, $data, $where, $format, $where_format );
}
endif;

//レコード数を取得
if ( !function_exists( 'get_db_table_record_count' ) ):
function get_db_table_record_count($table){
  global $wpdb;
  $query = "SELECT COUNT(id) FROM {$table}";
  $count = $wpdb->get_var( $query );
  //var_dump($count);

  return intval($count);
}
endif;

//テーブルのレコードが空か
if ( !function_exists( 'is_db_table_record_empty' ) ):
function is_db_table_record_empty($table){
  return get_db_table_record_count($table) <= 0;
}
endif;

//データベースにテーブルが存在するかどうか
if ( !function_exists( 'is_db_table_exist' ) ):
function is_db_table_exist($table){
  global $wpdb; //グローバル変数「$wpdb」を使うよっていう記述
  $table_search = $wpdb->get_row("SHOW TABLES FROM " . DB_NAME . " LIKE '" . $table . "'"); //「$wpdb->posts」テーブルがあるかどうか探す
  if( $wpdb->num_rows == 1 ){ //結果を判別して条件分岐
    //テーブルがある場合の処理
    return true;
  } else {
    //テーブルがない場合の処理
    return false;
  }
}
endif;
