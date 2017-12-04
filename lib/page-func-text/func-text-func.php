<?php //関数テキスト関係の関数

//関数テキストテーブルのバージョン
define('FUNCTION_TEXTS_TABLE_VERSION', '0.1');
define('FUNCTION_TEXTS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_function_texts');
//_v(FUNCTION_TEXTS_TABLE_NAME);

//関数テキスト移動用URL
define('FT_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('FT_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));
// define('FT_EDIT_URL',   add_query_arg(array('action' => 'edit',  'id' => $record->id)));
// define('FT_DELETE_URL', add_query_arg(array('action' => 'delete','id' => $record->id)));

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
  if ( function_exists( '_v' ) ):
    _v(THEME_NAME.'アップデートチェック');
  endif;
  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_function_texts_table_version();
  $now_ver = FUNCTION_TEXTS_TABLE_VERSION;
  if ( $installed_ver != $now_ver ) {
    if ( function_exists( '_v' ) ):
      _v(THEME_NAME.'がアップデートされました');
    endif;
    create_function_texts_table();
  }
}
endif;
//update_function_texts_table();

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
function get_function_texts( $keyword = null, $order_by = null ) {
  update_function_texts_table();
  global $wpdb;
  $table_name = FUNCTION_TEXTS_TABLE_NAME;
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

  //$query = "SELECT * FROM {$table_name}";
  //var_dump($query);

  $records = $wpdb->get_results( $query );

  return $records;
}
endif;

//関数テキストレコードの取得
if ( !function_exists( 'get_function_text' ) ):
function get_function_text( $id ) {
  global $wpdb;
  $table_name = FUNCTION_TEXTS_TABLE_NAME;

  //$query = ("SELECT * FROM {$table_name}  WHERE id = {intval($id)}");
  $query = $wpdb->prepare("SELECT * FROM {$table_name}  WHERE id = %d", $id);
  //_v($query);

  $record = $wpdb->get_row( $query );
  $record->title = esc_attr(stripslashes_deep($record->title));
  //_v($record->title);
  $record->text = wpautop(stripslashes_deep($record->text));

  return $record;
}
endif;

//関数テキストレコードの取得
if ( !function_exists( 'delete_function_text' ) ):
function delete_function_text( $id ) {
  global $wpdb;
  $table_name = FUNCTION_TEXTS_TABLE_NAME;

  $res = $wpdb->delete( $table_name, array(
      'id' => $id,
    ),
    array('%d')
  );
  return $res;
}
endif;

add_shortcode('ft', 'function_text_shortcode');
if ( !function_exists( 'function_text_shortcode' ) ):
function function_text_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_function_text($id)) {
      return $recode->text;
    }
  }

}
endif;
