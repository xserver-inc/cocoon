<?php //吹き出し関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//関数テキストテーブルのバージョン
define('SPEECH_BALLOONS_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0.0');
define('SPEECH_BALLOONS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_speech_balloons');

//関数テキスト移動用URL
define('SB_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('SB_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));

//デフォルトの名前
define('SB_DEFAULT_NAME', __( '匿名', THEME_NAME ));

//スタイル
define('SBS_STANDARD', 'stn');
define('SBS_FLAT', 'flat');
define('SBS_LINE', 'line');
define('SBS_THINK', 'think');

//ポジション
define('SBP_LEFT', 'l');
define('SBP_RIGHT', 'r');

//アイコンスタイル
define('SBIS_SQUARE_NONE', 'sn');
define('SBIS_SQUARE_BORDER', 'sb');
define('SBIS_CIRCLE_NONE', 'cn');
define('SBIS_CIRCLE_BORDER', 'cb');

//いらすとやさん関係
define('IRASUTOYA_ CREDIT', '&copy; <a href="http://www.irasutoya.com/" target="_blank" rel="noopener">いらすとや</a>');
define('IMAGE_CDN_DIR_URL', 'https://im-cocoon.net/wp-content/uploads');


define('SB_IMAGE_DIR_URL', get_template_directory_uri().'/images');
//デフォルトアイコン
define('SB_DEFAULT_ICON', get_template_directory_uri().'/images/anony.png');
define('SB_DEFAULT_MAN_ICON', SB_IMAGE_DIR_URL.'/man.png');
define('SB_DEFAULT_WOMAN_ICON', SB_IMAGE_DIR_URL.'/woman.png');


//吹き出しテーブルのバージョン取得
define('OP_SPEECH_BALLOONS_TABLE_VERSION', 'speech_balloons_table_version');
if ( !function_exists( 'get_speech_balloons_table_version' ) ):
function get_speech_balloons_table_version(){
  return get_theme_option(OP_SPEECH_BALLOONS_TABLE_VERSION);
}
endif;

//吹き出しテーブルが存在するか
if ( !function_exists( 'is_speech_balloons_table_exist' ) ):
function is_speech_balloons_table_exist(){
  return is_db_table_exist(SPEECH_BALLOONS_TABLE_NAME);
}
endif;

//レコードを追加
if ( !function_exists( 'insert_speech_balloon_record' ) ):
function insert_speech_balloon_record($posts){
  $table = SPEECH_BALLOONS_TABLE_NAME;
  $now = current_time('mysql');
  //_v($posts);
  $data = array(
    'date' => $now,
    'modified' => $now,
    'title' => $posts['title'],
    'name' => $posts['name'],
    'icon' => $posts['icon'],
    'style' => $posts['style'],
    'position' => $posts['position'],
    'iconstyle' => $posts['iconstyle'],
    'credit' => !empty($posts['credit']) ? $posts['credit'] : null,
    'visible' => !empty($posts['visible']) ? 1 : 0,
  );
  $format = array(
    '%s',
    '%s',
    '%s',
    '%s',
    '%s',
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
if ( !function_exists( 'update_speech_balloon_record' ) ):
function update_speech_balloon_record($id, $posts){
  $table = SPEECH_BALLOONS_TABLE_NAME;
  $now = current_time('mysql');
  //$visible = $posts['visible'] ? 1 : 0;
  $data = array(
    'modified' => $now,
    'title' => $posts['title'],
    'name' => $posts['name'],
    'icon' => $posts['icon'],
    'style' => $posts['style'],
    'position' => $posts['position'],
    'iconstyle' => $posts['iconstyle'],
    'visible' => !empty($posts['visible']) ? 1 : 0,
  );
  $where = array('id' => $id);
  $format = array(
    '%s',
    '%s',
    '%s',
    '%s',
    '%s',
    '%s',
    '%s',
    '%d',
  );
  $where_format = array('%d');
  return update_db_table_record($table, $data, $where, $format, $where_format);
}
endif;

//初期データの入力
if ( !function_exists( 'add_default_speech_balloon_records' ) ):
function add_default_speech_balloon_records(){

  $posts = array();
  $posts['title'] = __( '[SAMPLE 001] 男性（左）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_DEFAULT_MAN_ICON;
  $posts['style'] = SBS_STANDARD;
  $posts['position'] = SBP_LEFT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 002] 女性（右）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_DEFAULT_WOMAN_ICON;
  $posts['style'] = SBS_STANDARD;
  $posts['position'] = SBP_RIGHT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);


  $posts['title'] = __( '[SAMPLE 003] ビジネスマン（左）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/b-man.png';
  $posts['style'] = SBS_LINE;
  $posts['position'] = SBP_LEFT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 004] ビジネスウーマン（右）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/b-woman.png';
  $posts['style'] = SBS_LINE;
  $posts['position'] = SBP_RIGHT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 005] 悩むおじさん（左）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/ojisan.png';
  $posts['style'] = SBS_FLAT;
  $posts['position'] = SBP_LEFT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 006] 悩むおばさん（右）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/obasan.png';
  $posts['style'] = SBS_FLAT;
  $posts['position'] = SBP_RIGHT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 007] 男性医師（左）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/doctor.png';
  $posts['style'] = SBS_STANDARD;
  $posts['position'] = SBP_LEFT;
  $posts['iconstyle'] = SBIS_SQUARE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 008] 女性医師（右）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/doctress.png';
  $posts['style'] = SBS_STANDARD;
  $posts['position'] = SBP_RIGHT;
  $posts['iconstyle'] = SBIS_SQUARE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 009] どや顔男性（左）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/doya-man.png';
  $posts['style'] = SBS_THINK;
  $posts['position'] = SBP_LEFT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

  $posts['title'] = __( '[SAMPLE 010] どや顔女性（右）', THEME_NAME );
  $posts['name']  = '';
  $posts['icon']  = SB_IMAGE_DIR_URL.'/doya-woman.png';
  $posts['style'] = SBS_THINK;
  $posts['position'] = SBP_RIGHT;
  $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  $posts['credit'] = '';
  $posts['visible'] = 1;
  insert_speech_balloon_record($posts);

}
endif;

//吹き出しテーブルの作成
if ( !function_exists( 'create_speech_balloons_table' ) ):
function create_speech_balloons_table() {
  $add_default_records = false;
  //テーブルが存在しない場合初期データを挿入（テーブル作成時のみ挿入）
  if (!is_speech_balloons_table_exist()) {
    $add_default_records = true;
  }
  // SQL文でテーブルを作る
  $sql = "CREATE TABLE ".SPEECH_BALLOONS_TABLE_NAME." (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    title varchar(126),
    name varchar(36) DEFAULT NULL,
    icon varchar(256) DEFAULT '".SB_DEFAULT_ICON."' NOT NULL,
    style varchar(20) DEFAULT '".SBS_STANDARD."' NOT NULL,
    position varchar(20) DEFAULT '".SBP_LEFT."' NOT NULL,
    iconstyle varchar(20) DEFAULT '".SBIS_CIRCLE_BORDER."' NOT NULL,
    credit varchar(256),
    visible bit(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
  )";
  $res = create_db_table($sql);

  //初期データの挿入
  if ($res && $add_default_records) {
    //データ挿入処理
    add_default_speech_balloon_records();
  }

  set_theme_mod( OP_SPEECH_BALLOONS_TABLE_VERSION, SPEECH_BALLOONS_TABLE_VERSION );
  return $res;
}
endif;


//吹き出しテーブルのアップデート
if ( !function_exists( 'update_speech_balloons_table' ) ):
function update_speech_balloons_table() {
  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_speech_balloons_table_version();
  $now_ver = SPEECH_BALLOONS_TABLE_VERSION;
  if ( is_update_db_table($installed_ver, $now_ver) ) {
    create_speech_balloons_table();
  }
}
endif;


//吹き出しテーブルのアンインストール
if ( !function_exists( 'uninstall_speech_balloons_table' ) ):
function uninstall_speech_balloons_table() {
  uninstall_db_table(SPEECH_BALLOONS_TABLE_NAME);
  remove_theme_mod(OP_SPEECH_BALLOONS_TABLE_VERSION);
}
endif;


//吹き出しテーブルレコードの取得
if ( !function_exists( 'get_speech_balloons' ) ):
function get_speech_balloons( $keyword = null, $order_by = null ) {
  $table_name = SPEECH_BALLOONS_TABLE_NAME;
  return get_db_table_records($table_name, 'title', $keyword, $order_by);
}
endif;


//吹き出しテーブルレコードの取得
if ( !function_exists( 'get_speech_balloon' ) ):
function get_speech_balloon( $id ) {
  $table_name = SPEECH_BALLOONS_TABLE_NAME;
  $record = get_db_table_record( $table_name, $id );
  if (empty($record)) return false;
  $record->title = !empty($record->title) ? $record->title : '';
  $record->name = !empty($record->name) ? $record->name : null;
  $record->icon = !empty($record->icon) ? $record->icon : SB_DEFAULT_MAN_ICON;
  $record->style = !empty($record->style) ? $record->style : SBS_FLAT;
  $record->position = !empty($record->position) ? $record->position : SBP_LEFT;
  $record->iconstyle = !empty($record->iconstyle) ? $record->iconstyle : SBIS_CIRCLE_BORDER;
  $record->credit = !empty($record->credit) ? $record->credit : null;
  $record->visible = !empty($record->visible) ? 1 : 0;

  return $record;
}
endif;

//テーブルのレコードが空か
if ( !function_exists( 'is_speech_balloons_record_empty' ) ):
function is_speech_balloons_record_empty(){
  $table_name = SPEECH_BALLOONS_TABLE_NAME;
  return is_db_table_record_empty($table_name);
}
endif;

//関数テキストレコードの削除
if ( !function_exists( 'delete_speech_balloon' ) ):
function delete_speech_balloon( $id ) {
  $table_name = SPEECH_BALLOONS_TABLE_NAME;
  return delete_db_table_record( $table_name, $id );
}
endif;

//吹き出しHTMLを生成
if ( !function_exists( 'generate_speech_balloon_tag' ) ):
function generate_speech_balloon_tag($record, $voice){
  $w = 92;
  $h = 92;
  if (isset($record->icon)) {
    $size = get_image_width_and_height($record->icon);
    if ($size) {
      $w = $size['width'];
      $h = $size['height'];
    }
  }
?>
<div class="speech-wrap sb-id-<?php echo esc_html($record->id); ?> sbs-<?php echo esc_html($record->style); ?> sbp-<?php echo esc_html($record->position); ?> sbis-<?php echo esc_html($record->iconstyle); ?> cf"><div class="speech-person"><figure class="speech-icon"><img src="<?php echo esc_html($record->icon); ?>" alt="<?php echo esc_html($record->name); ?>" class="speech-icon-image" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></figure><?php if ($record->name): ?><div class="speech-name"><?php echo esc_html($record->name); ?></div><?php endif ?></div><div class="speech-balloon"><p><?php echo esc_html($voice); ?></p></div></div>
<?php
}
endif;

if ( !function_exists( 'is_icon_irasutoya' ) ):
function is_icon_irasutoya($record){
  if (isset($record->icon) && isset($record->credit)) {
    if ($record->credit == '') {
      if (strpos($record->icon, SB_IMAGE_DIR_URL) !== false) {
        return true;
      }
    }

  }

}
endif;

//REST API出力用の吹き出しデータをまとめて取得
if ( !function_exists( 'get_rest_speech_balloons' ) ):
function get_rest_speech_balloons( $data ) {
  $records = get_speech_balloons();
  $results = array();
  if ($records) {
    foreach ($records as $record) {
      //オブジェクトを配列に変換
      $result = json_decode(json_encode($record), true);
      $results[] = $result;
    }
  }
  return $results;
}
endif;

// //https://cocoon.local/wp-json/cocoon/v1/balloon/
// add_action( 'rest_api_init', 'rest_api_init_balloon_custom' );
// if ( !function_exists( 'rest_api_init_balloon_custom' ) ):
// function rest_api_init_balloon_custom(){
//   register_rest_route( THEME_NAME.'/v1', '/balloon/', array(
//     'methods' => 'GET',
//     'callback' => 'get_rest_speech_balloons',
//   ) );
// }
// endif;

//REST API出力用の吹き出しデータ取得
if ( !function_exists( 'get_rest_speech_balloon' ) ):
function get_rest_speech_balloon( $data ) {
  $result = array();
  if (isset($data['id'])) {
    $result = get_speech_balloon( $data['id'] );
    //オブジェクトを配列に変換
    $result = json_decode(json_encode($result), true);
  }
  return $result;
}
endif;

// //https://cocoon.local/wp-json/cocoon/v1/balloon/1
// add_action( 'rest_api_init', function () {
//   register_rest_route( THEME_NAME.'/v1', '/balloon/(?P<id>\d+)', array(
//     'methods' => 'GET',
//     'callback' => 'get_rest_speech_balloon',
//   ) );
// } );
