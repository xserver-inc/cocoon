<?php //アクセス数

//関数テキストテーブルのバージョン
define('ACCESSES_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0');//rand(0, 99)
define('ACCESSES_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_accesses');

define('INDEX_ACCESSES_PID', 'index_pid');
define('INDEX_ACCESSES_PID_DATE', 'index_pid_date');
define('INDEX_ACCESSES_PID_DATE_PTYPE', 'index_pid_date_ptype');


//アクセス数を取得するか
define('OP_ACCESS_COUNT_ENABLE', 'access_count_enable');
if ( !function_exists( 'is_access_count_enable' ) ):
function is_access_count_enable(){
  return get_theme_option(OP_ACCESS_COUNT_ENABLE, 1);
}
endif;

//トテーブルのバージョン取得
define('OP_ACCESSES_TABLE_VERSION', 'accesses_table_version');
if ( !function_exists( 'get_accesses_table_version' ) ):
function get_accesses_table_version(){
  return get_theme_option(OP_ACCESSES_TABLE_VERSION);
}
endif;

//ページタイプの取得
if ( !function_exists( 'get_accesses_page_type' ) ):
function get_accesses_page_type(){
  $res = 's';
  if (is_page()) {
    $res = 'p';
  }
  return $res;
}
endif;

//テーブルが存在するか
if ( !function_exists( 'is_accesses_table_exist' ) ):
function is_accesses_table_exist(){
  return is_db_table_exist(ACCESSES_TABLE_NAME);
}
endif;

//レコードを追加
if ( !function_exists( 'insert_accesses_record' ) ):
function insert_accesses_record($posts){
  $table = ACCESSES_TABLE_NAME;
  $data = array(
    'post_id' => $posts['post_id'],
    'date' => $posts['date'],
    'page_type' => $posts['page_type'],
    'count' => $posts['count'],
    'last_ip' => $posts['last_ip'],
  );
  $format = array(
    '%d',
    '%s',
    '%s',
    '%d',
    '%s',
  );
  return insert_db_table_record($table, $data, $format);
}
endif;

//レコードの編集
if ( !function_exists( 'update_accesses_record' ) ):
function update_accesses_record($id, $posts){
  $table = ACCESSES_TABLE_NAME;
  $data = array(
    'count' => $posts['count'],
    'last_ip' => $posts['last_ip'],
  );
  $where = array('id' => $id);
  $format = array(
    '%d',
    '%s',
  );
  $where_format = array('%d');
  return update_db_table_record($table, $data, $where, $format, $where_format);
}
endif;

//初期データの入力
if ( !function_exists( 'add_default_accesses_records' ) ):
function add_default_accesses_records(){
  //初期データ
}
endif;

//テーブルの作成
if ( !function_exists( 'create_accesses_table' ) ):
function create_accesses_table() {
  $add_default_records = false;
  //テーブルが存在しない場合初期データを挿入（テーブル作成時のみ挿入）
  if (!is_accesses_table_exist()) {
    $add_default_records = true;
  }
  // SQL文でテーブルを作る
  $sql = "CREATE TABLE ".ACCESSES_TABLE_NAME." (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      post_id bigint(20),
      date varchar(20),
      page_type varchar(2) DEFAULT 's',
      count bigint(20) DEFAULT 0,
      last_ip varchar(40),
      PRIMARY KEY (id),
      INDEX ".INDEX_ACCESSES_PID." (post_id),
      INDEX ".INDEX_ACCESSES_PID_DATE." (post_id,date),
      INDEX ".INDEX_ACCESSES_PID_DATE_PTYPE." (post_id,date,page_type)
    )";
  $res = create_db_table($sql);
  //_v($res);

  // //初期データの挿入
  // if ($res && $add_default_records) {
  //   //データ挿入処理
  //   add_default_accesses_records();
  // }

  set_theme_mod( OP_ACCESSES_TABLE_VERSION, ACCESSES_TABLE_VERSION );
  return $res;
}
endif;
//update_accesses_table();
//create_accesses_table();
//2017-12-12

//テーブルのアップデート
if ( !function_exists( 'update_accesses_table' ) ):
function update_accesses_table() {

  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_accesses_table_version();
  $now_ver = ACCESSES_TABLE_VERSION;
  if (is_update_db_table($installed_ver, $now_ver)) {
    create_accesses_table();
  }

}
endif;
update_accesses_table();
//_v( date('Y-m-d', strtotime(date('Y-m-d').' -99 day')) );
//_v(date('Y-m-d'));

//DBにアクセスをカウントするし
if ( !function_exists( 'count_this_page_access' ) ):
function count_this_page_access(){
  //投稿・固定ページのみでカウントする
  if (is_singular() && is_access_count_enable()) {
    global $post;
    $post_id = $post->ID;
    $date = current_time('Y-m-d');
    $page_type = get_accesses_page_type();
    $last_ip = $_SERVER['REMOTE_ADDR'];

    $record = get_accesse_record_from($post_id, $date, $page_type);

    $posts = array();

    $res = false;
    if ($record) {
      //アクセスカウントの連続カウント防止
      if (($record->last_ip != $last_ip) || DEBUG_MODE) {
        $id = $record->id;
        $posts['last_ip'] = $last_ip;
        $posts['count'] = intval($record->count) + 1;
        $res = update_accesses_record($id, $posts);
      }
    } else {
      $posts['post_id'] = $post_id;
      $posts['date'] = $date;
      $posts['page_type'] = $page_type;
      $posts['last_ip'] = $last_ip;
      $posts['count'] = 1;
      $res = insert_accesses_record($posts);
    }

    return $res;
    // _v($query);
    // _v($record);
  }

}
endif;
// _v(is_singular());
// count_this_page_access();

//投稿IDと日付からレコードを取得
if ( !function_exists( 'get_accesse_record' ) ):
function get_accesse_record_from($post_id, $date, $page_type = 's'){
  global $wpdb;
  $add_where = '';
  $table_name = ACCESSES_TABLE_NAME;
  if ($page_type) {
    $index = INDEX_ACCESSES_PID_DATE_PTYPE;
    $add_where = " AND page_type = '$page_type'";
    $args = array($post_id, $date, $page_type);
  } else {
    $index = INDEX_ACCESSES_PID_DATE;
    $args = array($post_id, $date);
  }

  $query = $wpdb->prepare("SELECT * FROM {$table_name} USE INDEX({$index}) WHERE post_id = %d AND date = %s".$add_where, $args);

  $record = $wpdb->get_row( $query );
  //_v($query);

  return $record;
}
endif;

//IDからレコードを取得
if ( !function_exists( 'get_accesse_from_id' ) ):
function get_accesse_from_id($id){
  $table_name = ACCESSES_TABLE_NAME;
  $record = get_db_table_record( $table_name, $id );
  return $record;
}
endif;

//テーブルのアンインストール
if ( !function_exists( 'uninstall_accesses_table' ) ):
function uninstall_accesses_table() {
  uninstall_db_table(ACCESSES_TABLE_NAME);
  remove_theme_mod(OP_ACCESSES_TABLE_VERSION);
}
endif;

//今日のアクセス数を取得
if ( !function_exists( 'get_todays_access_count' ) ):
function get_todays_access_count($post_id = null){
  $res = 0;
  if (is_singular()) {
    global $post;
    if (!$post_id) {
      $post_id = $post->ID;
    }
    $date = current_time('Y-m-d');
    $page_type = get_accesses_page_type();

    $record = get_accesse_record_from($post_id, $date, $page_type);
    $res = $record->count;
  }
  return $res;
}
endif;

//アクセス取得関数（$daysに取得する日数を入力、もしくはallで全取得）
if ( !function_exists( 'get_several_access_count' ) ):
function get_several_access_count($post_id = null, $days = 'all'){
  $res = 0;
  if (is_singular()) {
    global $post;
    global $wpdb;

    if (!$post_id) {
      $post_id = $post->ID;
    }
    // $date = current_time('Y-m-d');
    // $date_before = date('Y-m-d', strtotime(current_time('Y-m-d').' -'.$days.' day'));
    $date = get_current_db_date();
    $date_before = get_current_db_date_before($days);
    $table_name = ACCESSES_TABLE_NAME;

    if ($days == 'all') {
      $query = $wpdb->prepare("SELECT SUM(count) FROM {$table_name} USE INDEX(".INDEX_ACCESSES_PID_DATE.") WHERE post_id = %d", $post_id);
    } else {
      $query = $wpdb->prepare("SELECT SUM(count) FROM {$table_name} USE INDEX(".INDEX_ACCESSES_PID_DATE.") WHERE post_id = %d AND date BETWEEN %s AND %s", $post_id, $date_before, $date);
    }

    $res = $wpdb->get_var( $query );
    //_v($query );
  }
  return $res;
}
endif;

//直近7日間のアクセス数を取得
if ( !function_exists( 'get_last_7days_access_count' ) ):
function get_last_7days_access_count($post_id = null){
  return get_several_access_count($post_id, 7);
}
endif;

//直近30日間のアクセス数を取得
if ( !function_exists( 'get_last_30days_access_count' ) ):
function get_last_30days_access_count($post_id = null){
  return get_several_access_count($post_id, 30);
}
endif;

//全期間のアクセス数を取得
if ( !function_exists( 'get_all_access_count' ) ):
function get_all_access_count($post_id = null){
  return get_several_access_count($post_id, 'all');
}
endif;

if ( !function_exists( 'get_access_ranking_records' ) ):
function get_access_ranking_records($days = 'all', $limit = 5){
  global $wpdb;
  $table_name = ACCESSES_TABLE_NAME;
  $where = '';
  if ($days != 'all') {
    $date = get_current_db_date();
    $date_before = get_current_db_date_before($days);
    $where = " WHERE date BETWEEN $date AND $date_before ";
  }
  if (!is_numeric($limit)) {
    $limit = 5;
  }
  $query = esc_sql("SELECT post_id, SUM(count) AS sum_count FROM $table_name $where GROUP BY post_id ORDER BY sum_count DESC LIMIT $limit;");
  $records = $wpdb->get_results( $query );
  // _v($query);
  // _v($records);
  return $records;
}
endif;
get_access_ranking_records();