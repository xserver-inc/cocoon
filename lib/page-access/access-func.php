<?php //アクセス数

//関数テキストテーブルのバージョン
define('ACCESSES_TABLE_VERSION', rand(0, 99));
define('ACCESSES_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_accesses');

//アクセス数を取得するか
define('OP_ACCESS_COUNT_ENABLE', 'access_count_enable');
if ( !function_exists( 'is_access_count_enable' ) ):
function is_access_count_enable(){
  return get_theme_option(OP_ACCESS_COUNT_ENABLE);
}
endif;

//トテーブルのバージョン取得
define('OP_ACCESSES_TABLE_VERSION', 'accesses_table_version');
if ( !function_exists( 'get_accesses_table_version' ) ):
function get_accesses_table_version(){
  return get_theme_option(OP_ACCESSES_TABLE_VERSION);
}
endif;

//テーブルが存在するか
if ( !function_exists( 'is_accesses_table_exist' ) ):
function is_accesses_table_exist(){
  return is_db_table_exist(ACCESSES_TABLE_NAME);
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
      count bigint(20) DEFAULT 0,
      last_ip varchar(40),
      PRIMARY KEY (id),
      INDEX idx_post_id (post_id),
      INDEX idx_post_id_and_date (post_id,date)
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
//_v(date("Y-m-d",strtotime("+_37 day")));

//DBにアクセスをカウントするし
if ( !function_exists( 'count_this_page_access' ) ):
function count_this_page_access($post_id){
  //投稿・固定ページのみでカウントする
  if (is_singular()) {
    # code...
  }

}
endif;