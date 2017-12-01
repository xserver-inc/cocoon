<?php //関数テキスト関係の関数

//関数テキストテーブルのバージョン
define('FUNCTION_TEXTS_TABLE_VERSION', '0.1');
define('FUNCTION_TEXTS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_function_texts');
//_v(FUNCTION_TEXTS_TABLE_NAME);

//関数テキストテーブルのバージョン取得
define('OP_FUNCTION_TEXTS_TABLE_VERSION', 'function_texts_table_version');
if ( !function_exists( 'get_function_texts_table_version' ) ):
function get_function_texts_table_version(){
  return get_theme_option(OP_FUNCTION_TEXTS_TABLE_VERSION);
}
endif;

//関数テキストテーブルの作成
if ( !function_exists( 'create_function_texts_table' ) ):
function create_function_texts_table() {
   global $wpdb;
   //_v('$wpdb');
   $sql = "";
   $charset_collate = "";

   // // 接頭辞の追加（socal_count_cache）
   // $table_name = $wpdb->prefix . 'function_texts';

   // charsetを指定する
   if ( !empty($wpdb->charset) )
      $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

   // 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
   if ( !empty($wpdb->collate) )
      $charset_collate .= "COLLATE {$wpdb->collate}";

    // SQL文でテーブルを作る
    $sql = "CREATE TABLE ".FUNCTION_TEXTS_TABLE_NAME." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      title varchar(126),
      text text NOT NULL,
      PRIMARY KEY (id),
      INDEX (title)
    ) $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   //_v($sql);
   $res = dbDelta( $sql );
   //_v($res);

   set_theme_mod( OP_FUNCTION_TEXTS_TABLE_VERSION, FUNCTION_TEXTS_TABLE_VERSION );
}
endif;
//create_function_texts_table();

//関数テキストテーブルのアップデート
if ( !function_exists( 'update_function_texts_table' ) ):
function update_function_texts_table() {
  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_function_texts_table_version();
  $now_ver = FUNCTION_TEXTS_TABLE_VERSION;
  if ( $installed_ver != $now_ver ) {
    create_function_texts_table();
  }
}
endif;

//関数テキストテーブルのアンインストール
if ( !function_exists( 'uninstall_function_texts_table' ) ):
function uninstall_function_texts_table() {
  global $wpdb;
  //$table_name = $wpdb->prefix . 'oxy_table';

  $wpdb->query("DROP TABLE IF EXISTS ".FUNCTION_TEXTS_TABLE_NAME);

  //delete_option(OP_FUNCTION_TEXTS_TABLE_VERSION);
}
endif;

//関数テキストレコードの取得
if ( !function_exists( 'get_function_texts' ) ):
function get_function_texts( $where = null ) {
  global $wpdb;
  $table_name = FUNCTION_TEXTS_TABLE_NAME;

  $query = "SELECT * FROM {$table_name} ".$where;

  $records = $wpdb->get_results( $query );

  return $records;
}
endif;

