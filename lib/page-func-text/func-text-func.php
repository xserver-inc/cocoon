<?php //テンプレート関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//関数テキストテーブルのバージョン
define('FUNCTION_TEXTS_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0');
define('FUNCTION_TEXTS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_function_texts');
//_v(FUNCTION_TEXTS_TABLE_NAME);

//関数テキスト移動用URL
define('FT_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('FT_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));


//関数テキストテーブルのバージョン取得
define('OP_FUNCTION_TEXTS_TABLE_VERSION', 'function_texts_table_version');
if ( !function_exists( 'get_function_texts_table_version' ) ):
function get_function_texts_table_version(){
  return get_theme_option(OP_FUNCTION_TEXTS_TABLE_VERSION);
}
endif;

//関数テキストテーブルが存在するか
if ( !function_exists( 'is_function_texts_table_exist' ) ):
function is_function_texts_table_exist(){
  return is_db_table_exist(FUNCTION_TEXTS_TABLE_NAME);
}
endif;

//初期データの入力
if ( !function_exists( 'add_default_function_text_records' ) ):
function add_default_function_text_records(){
  $posts = array();
  $posts['title'] = __( 'Wordpressカスタマイズ注意文サンプル', THEME_NAME );
  $posts['text'] = '<p class="alert-box">'.__( 'Wordpressのfunctions.phpを編集する前は、編集前に必ずバックアップを取って保存してください。もし編集後、エラーが出るようでしたら、バックアップファイルを元に復元してください。 ', THEME_NAME ).'</p>';
  $posts['visible'] = 1;
  insert_function_text_record($posts);
}
endif;

//関数テキストテーブルの作成
if ( !function_exists( 'create_function_texts_table' ) ):
function create_function_texts_table() {
  $add_default_records = false;
  //テーブルが存在しない場合初期データを挿入（テーブル作成時のみ挿入）
  if (!is_function_texts_table_exist()) {
    $add_default_records = true;
  }
  // SQL文でテーブルを作る
  $sql = "CREATE TABLE ".FUNCTION_TEXTS_TABLE_NAME." (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    title varchar(126) NOT NULL,
    text text NOT NULL,
    visible bit(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
    )";
  $res = create_db_table($sql);

  //初期データの挿入
  if ($res && $add_default_records) {
    //データ挿入処理
    add_default_function_text_records();
  }

  set_theme_mod( OP_FUNCTION_TEXTS_TABLE_VERSION, FUNCTION_TEXTS_TABLE_VERSION );
  return $res;
}
endif;
//create_function_texts_table();

//関数テキストテーブルのアップデート
if ( !function_exists( 'update_function_texts_table' ) ):
function update_function_texts_table() {

  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_function_texts_table_version();
  $now_ver = FUNCTION_TEXTS_TABLE_VERSION;
  if (is_update_db_table($installed_ver, $now_ver)) {
    create_function_texts_table();
  }

}
endif;
//update_function_texts_table();

//関数テキストテーブルのアンインストール
if ( !function_exists( 'uninstall_function_texts_table' ) ):
function uninstall_function_texts_table() {
  uninstall_db_table(FUNCTION_TEXTS_TABLE_NAME);
  remove_theme_mod(OP_FUNCTION_TEXTS_TABLE_VERSION);
}
endif;

//関数テキストレコードの取得
if ( !function_exists( 'get_function_texts' ) ):
function get_function_texts( $keyword = null, $order_by = null ) {
  $table_name = FUNCTION_TEXTS_TABLE_NAME;
  return get_db_table_records($table_name, 'title', $keyword, $order_by);
}
endif;

//関数テキストレコードの取得
if ( !function_exists( 'get_function_text' ) ):
function get_function_text( $id ) {
  $table_name = FUNCTION_TEXTS_TABLE_NAME;
  $record = get_db_table_record( $table_name, $id );
  if (!empty($record->title))
    $record->title = esc_attr(stripslashes_deep($record->title));

  if (!empty($record->text))
    $record->text = wpautop(stripslashes_deep($record->text));

  return $record;
}
endif;

//テーブルのレコードが空か
if ( !function_exists( 'is_function_texts_record_empty' ) ):
function is_function_texts_record_empty(){
  $table_name = FUNCTION_TEXTS_TABLE_NAME;
  return is_db_table_record_empty($table_name);
}
endif;

//関数テキストレコードの削除
if ( !function_exists( 'delete_function_text' ) ):
function delete_function_text( $id ) {
  $table_name = FUNCTION_TEXTS_TABLE_NAME;
  return delete_db_table_record( $table_name, $id );
}
endif;

//レコードを追加
if ( !function_exists( 'insert_function_text_record' ) ):
function insert_function_text_record($posts){
  $table = FUNCTION_TEXTS_TABLE_NAME;
  $now = current_time('mysql');
  $data = array(
    'date' => $now,
    'modified' => $now,
    'title' => $posts['title'],
    'text' => $posts['text'],
    'visible' => !empty($posts['visible']) ? 1 : 0,
  );
  $format = array(
    '%s',
    '%s',
    '%s',
    '%s',
    '%d',
  );
  return insert_db_table_record($table, $data, $format);
}
endif;

//レコードの編集
if ( !function_exists( 'update_function_text_record' ) ):
function update_function_text_record($id, $posts){
  $table = FUNCTION_TEXTS_TABLE_NAME;
  $now = current_time('mysql');
  $data = array(
    'modified' => $now,
    'title' => $posts['title'],
    'text' => $posts['text'],
    'visible' => !empty($posts['visible']) ? 1 : 0,
  );
  $where = array('id' => $id);
  $format = array(
    '%s',
    '%s',
    '%s',
    '%d',
  );
  $where_format = array('%d');
  return update_db_table_record($table, $data, $where, $format, $where_format);
}
endif;
