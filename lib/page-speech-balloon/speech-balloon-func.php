<?php //吹き出し関係の関数

//関数テキストテーブルのバージョン
define('SPEECH_BALLOONS_TABLE_VERSION', '0.0');
define('SPEECH_BALLOONS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_speech_balloons');

//関数テキスト移動用URL
define('SB_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('SB_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));



//吹き出しテーブルのバージョン取得
define('OP_SPEECH_BALLOONS_TABLE_VERSION', 'speech_balloons_table_version');
if ( !function_exists( 'get_speech_balloons_table_version' ) ):
function get_speech_balloons_table_version(){
  return get_theme_option(OP_SPEECH_BALLOONS_TABLE_VERSION);
}
endif;

//吹き出しテーブルの作成
if ( !function_exists( 'create_speech_balloons_table' ) ):
function create_speech_balloons_table() {
   global $wpdb;
   //_v('$wpdb');
   $sql = "";
   $charset_collate = "";

   // // 接頭辞の追加（socal_count_cache）
   // $table_name = $wpdb->prefix . 'speech_balloons';

   // charsetを指定する
   if ( !empty($wpdb->charset) )
      $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

   // 照合順序を指定する（ある場合。通常デフォルトのutf8_general_ci）
   if ( !empty($wpdb->collate) )
      $charset_collate .= "COLLATE {$wpdb->collate}";

    // SQL文でテーブルを作る
    $sql = "CREATE TABLE ".SPEECH_BALLOONS_TABLE_NAME." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      title varchar(126),
      name varchar(36),
      icon varchar(256),
      type varchar(20),
      subtype varchar(20),
      PRIMARY KEY (id),
      INDEX (title),
      INDEX (name)
    ) $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   //_v($sql);
   $res = dbDelta( $sql );
   //_v($res);

   set_theme_mod( OP_SPEECH_BALLOONS_TABLE_VERSION, SPEECH_BALLOONS_TABLE_VERSION );
}
endif;
//create_speech_balloons_table();


//吹き出しテーブルのアップデート
if ( !function_exists( 'update_speech_balloons_table' ) ):
function update_speech_balloons_table() {
  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_speech_balloons_table_version();
  $now_ver = speech_balloons_TABLE_VERSION;
  if ( $installed_ver != $now_ver ) {
    create_speech_balloons_table();
  }
}
endif;
//update_speech_balloons_table();

//関数テキストレコードの取得
if ( !function_exists( 'get_speech_balloons' ) ):
function get_speech_balloons( $keyword = null, $order_by = null ) {
  update_speech_balloons_table();
  global $wpdb;
  $table_name = SPEECH_BALLOONS_TABLE_NAME;
  $where = null;
  if ($keyword) {
    $where = $wpdb->prepare(" WHERE title LIKE %s", '%'.$keyword.'%');
    //$where = (" WHERE title LIKE %%$keyword%%");
  }
  if ($order_by) {
    $order_by = esc_sql(" ORDER BY $order_by");
  }
  $query = "SELECT * FROM {$table_name}".
              $where.
              $order_by;

  $records = $wpdb->get_results( $query );

  return $records;
}
endif;
