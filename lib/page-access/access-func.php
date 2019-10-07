<?php //アクセス数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//関数テキストテーブルのバージョン
define('ACCESSES_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0.3');//rand(0, 99)
define('ACCESSES_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_accesses');

// define('INDEX_ACCESSES_PID', 'index_pid');
// define('INDEX_ACCESSES_PID_PTYPE', 'index_pid_ptype');
// define('INDEX_ACCESSES_PID_DATE', 'index_pid_date');
define('INDEX_ACCESSES_PID_PTYPE_DATE', 'index_pid_ptype_date');


//アクセス数を取得するか
define('OP_ACCESS_COUNT_ENABLE', 'access_count_enable');
if ( !function_exists( 'is_access_count_enable' ) ):
function is_access_count_enable(){
  return get_theme_option(OP_ACCESS_COUNT_ENABLE, 1);
}
endif;

//アクセス数のキャッシュ有効
define('OP_ACCESS_COUNT_CACHE_ENABLE', 'access_count_cache_enable');
if ( !function_exists( 'is_access_count_cache_enable' ) ):
function is_access_count_cache_enable(){
  return get_theme_option(OP_ACCESS_COUNT_CACHE_ENABLE, 1);
}
endif;

//アクセス数のキャッシュインターバル（分）
define('OP_ACCESS_COUNT_CACHE_INTERVAL', 'access_count_cache_interval');
if ( !function_exists( 'get_access_count_cache_interval' ) ):
function get_access_count_cache_interval(){
  return get_theme_option(OP_ACCESS_COUNT_CACHE_INTERVAL, 360);
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
if ( !function_exists( 'get_accesses_post_type' ) ):
function get_accesses_post_type(){
  if (is_page()) {
    $res = 'page'; //page
  } else {
    $res = 'post'; //single
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
    'post_type' => $posts['post_type'],
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
      post_type varchar(10) DEFAULT 'post',
      date varchar(20),
      count bigint(20) DEFAULT 0,
      last_ip varchar(40),
      PRIMARY KEY (id),
      INDEX ".INDEX_ACCESSES_PID_PTYPE_DATE." (post_id,post_type,date)
    )";
  //_v($sql);
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
  //_v($installed_ver);
  $now_ver = ACCESSES_TABLE_VERSION;
  if (is_update_db_table($installed_ver, $now_ver)) {
    create_accesses_table();
  }

}
endif;

//DBにアクセスをカウントする
if ( !function_exists( 'logging_page_access' ) ):
function logging_page_access($post_id = null, $post_type = 'post'){
  //_v(111);
  $res = false;
  //投稿・固定ページのみでカウントする
  if (is_access_count_enable()
      //サイト管理者でないとき
      && (!is_user_administrator() || DEBUG_MODE)
      //ボットでないとき
      && !is_useragent_robot()
    ) {
    // _v($post_id);
    // _v($post_type);
    if (!$post_id || !$post_type ) {
      global $post;
      $post_id = $post->ID;
      $post_type = get_accesses_post_type();
    }
    //IDとページタイプが取得できたとき
    if ($post_id && $post_type) {
      $date = current_time('Y-m-d');
      $last_ip = $_SERVER['REMOTE_ADDR'];

      $record = get_accesse_record_from($post_id, $date, $post_type);

      $posts = array();


      if ($record) {
        //アクセスカウントの連続カウント防止
        //if (($record->last_ip != $last_ip) || DEBUG_MODE) {
          $post_id = $record->id;
          $posts['last_ip'] = $last_ip;
          $posts['count'] = intval($record->count) + 1;
          $res = update_accesses_record($post_id, $posts);
        //}
      } else {
        $posts['post_id'] = $post_id;
        $posts['date'] = $date;
        $posts['post_type'] = $post_type;
        $posts['last_ip'] = $last_ip;
        $posts['count'] = 1;
        $res = insert_accesses_record($posts);
      }
    }//$id && $type
  }//is_access_count_enable()
  return $res;
}
endif;
// _v(is_singular());
// logging_page_access();

//投稿IDと日付からレコードを取得
if ( !function_exists( 'get_accesse_record' ) ):
function get_accesse_record_from($post_id, $date, $post_type = 'post'){
  global $wpdb;
  $add_where = '';
  $table_name = ACCESSES_TABLE_NAME;
  $index = INDEX_ACCESSES_PID_PTYPE_DATE;
  $args = array($post_id, $date, $post_type);

  $query = $wpdb->prepare("SELECT * FROM {$table_name} USE INDEX({$index}) WHERE post_id = %d AND date = %s AND post_type = %s", $args);

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
  global $post;
  if (!$post_id) {
    $post_id = $post->ID;
  }

  $res = get_several_access_count($post_id, 1);
  return $res;
}
endif;

//アクセス取得関数（$daysに取得する日数を入力、もしくはallで全取得）
if ( !function_exists( 'get_several_access_count' ) ):
function get_several_access_count($post_id = null, $days = 'all'){
  $res = 0;
  if (is_access_count_enable()) {
    global $post;
    global $wpdb;

    if (!$post_id) {
      $post_id = $post->ID;
    }

    $date = get_current_db_date();

    $date_before = get_current_db_date_before($days);
    $table_name = ACCESSES_TABLE_NAME;
    $post_type = get_accesses_post_type();

    $add_where = '';
    switch ($days) {
      case 'all':
        $args = array($post_id, $post_type);
        break;
      case 1:
        $add_where = " AND date = %s";
        $args = array($post_id, $post_type, $date);
        break;
      default:
        $add_where = " AND date BETWEEN %s AND %s";
        $args = array($post_id, $post_type, $date_before, $date);
        break;
    }
    //_v($days);

    $query = $wpdb->prepare("SELECT SUM(count) FROM {$table_name} WHERE post_id = %d AND post_type = %s".$add_where, $args);

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

if ( !function_exists( 'wrap_joined_wp_posts_sql' ) ):
function wrap_joined_wp_posts_query($query, $limit){
  global $wpdb;
  $wp_posts = $wpdb->posts;
  $ranks_posts = 'ranks_posts';
  //$post_type = is_page() ? 'page' : 'post';
  $post_type = 'post';
  $query = "
    SELECT ID, sum_count, post_title, post_author, post_date, post_modified, post_status, post_type, comment_count FROM (
      {$query}
    ) AS {$ranks_posts}
    INNER JOIN {$wp_posts} ON {$ranks_posts}.post_id = {$wp_posts}.id
    WHERE post_status = 'publish' AND
          post_type = '{$post_type}'
    ORDER BY sum_count DESC
    LIMIT $limit
  ";
  //_v($query);
  //var_dump($query);
  return $query;
}
endif;

//アクセスランキングを取得
if ( !function_exists( 'get_access_ranking_records' ) ):
function get_access_ranking_records($days = 'all', $limit = 5, $type = 'post', $cat_ids = array(), $exclude_post_ids = array(), $exclude_cat_ids = array()){
  // //ページの判別ができない場合はDBにアクセスしない
  // if (!is_singular()) {
  //   return null;
  // }
  //var_dump($cat_ids);

  //アクセスキャッシュを有効にしている場合
  if (is_access_count_cache_enable()) {
    $cats = implode(',', $cat_ids);
    $expids = implode(',', $exclude_post_ids);
    $excats = implode(',', $exclude_cat_ids);
    $type = get_accesses_post_type();
    $transient_id = TRANSIENT_POPULAR_PREFIX.'?days='.$days.'&limit='.$limit.'&type='.$type.'&cats='.$cats.'&expids='.$expids.'&excats='.$excats;
    //_v($transient_id);
    $cache = get_transient( $transient_id );
    if ($cache) {
      if (DEBUG_MODE) {
        echo('<pre>');
        echo $transient_id;
        echo('</pre>');
      } elseif (is_user_administrator()){

      } else {
        return $cache;
      }

    }
  }



  global $wpdb;
  $access_table = ACCESSES_TABLE_NAME;
  $post_type = 'post';
  $date = get_current_db_date();


  $where = " WHERE {$access_table}.post_type = '$post_type' ".PHP_EOL;
  if ($days != 'all') {
    $date_before = get_current_db_date_before($days);
    $where .= " AND {$access_table}.date BETWEEN '$date_before' AND '$date' ".PHP_EOL;
  }
  if ($days == 1) {
    $where .= " AND {$access_table}.date = '$date' ".PHP_EOL;
  }
  //_v($exclude_post_ids);
  // _v($exclude_post_ids[0]);
  if (is_ids_exist($exclude_post_ids)) {
    $where .= " AND {$access_table}.post_id NOT IN(".implode(',', $exclude_post_ids).") ".PHP_EOL;
  }
  //3180, 3234
  if (!is_numeric($limit)) {
    $limit = 5;
  }
  //カテゴリを指定する場合
  if (is_ids_exist($cat_ids) || is_ids_exist($exclude_cat_ids)) {
    global $post;
    $term_relationships = $wpdb->term_relationships;
    $term_taxonomy = $wpdb->term_taxonomy;
    $joined_table = 'terms_accesses';
    //カテゴリー指定
    if (is_ids_exist($cat_ids)) {
      $cat_ids = implode(',', $cat_ids);
      //$where .= " AND {$term_relationships}.term_taxonomy_id IN ({$cat_ids}) ".PHP_EOL;
      $where .= " AND {$term_taxonomy}.term_id IN ({$cat_ids}) ".PHP_EOL;
    }
    //除外カテゴリー指定
    if (is_ids_exist($exclude_cat_ids)) {
      //空の配列を取り除く
      $exclude_cat_ids = array_filter($exclude_cat_ids, "strlen");
      //カンマ区切りにする
      $ex_cat_ids = implode(',', $exclude_cat_ids);
      $ex_cat_ids = preg_replace('/,$/', '', $ex_cat_ids);
      $where .= " AND {$term_relationships}.term_taxonomy_id NOT IN ({$ex_cat_ids}) ".PHP_EOL;
    }

    $where .= " AND {$term_taxonomy}.taxonomy = 'category' ".PHP_EOL;
    // //テーブル結合するクエリの場合はWHEREに付け加えるのでANDに変更する
    // $where = str_replace('WHERE', 'AND', $where);
    $query = "
      SELECT {$joined_table}.post_id, SUM({$joined_table}.count) AS sum_count, {$joined_table}.term_taxonomy_id, {$joined_table}.taxonomy
        FROM (

          #カテゴリとアクセステーブルを内部結合してグルーピングし並び替えた結果
          SELECT {$access_table}.post_id, {$access_table}.count, {$term_relationships}.term_taxonomy_id, {$term_taxonomy}.taxonomy
            FROM {$term_relationships}
            INNER JOIN {$access_table} ON {$term_relationships}.object_id = {$access_table}.post_id
            INNER JOIN {$term_taxonomy} ON {$term_relationships}.term_taxonomy_id = {$term_taxonomy}.term_taxonomy_id
            $where #WHERE句
            GROUP BY {$access_table}.id

        ) AS {$joined_table} #カテゴリとアクセステーブルを内部結合した仮の名前

        GROUP BY {$joined_table}.post_id
        ORDER BY sum_count DESC
    ";
    //_v($query);
    //1回のクエリで投稿データを取り出せるようにケーブル結合クエリを追加
    $query = wrap_joined_wp_posts_query($query, $limit);
  } else {
    $query = "
      SELECT {$access_table}.post_id, SUM({$access_table}.count) AS sum_count
        FROM {$access_table} $where
        GROUP BY {$access_table}.post_id
        ORDER BY sum_count DESC
    ";
    //1回のクエリで投稿データを取り出せるようにケーブル結合クエリを追加
    $query = wrap_joined_wp_posts_query($query, $limit);
  }

  //_v($query);
  $records = $wpdb->get_results( $query );
  //_v($query);
  if (is_access_count_cache_enable() && $records) {
    set_transient( $transient_id, $records, 60 * get_access_count_cache_interval() );
  }
  // _v($records);
  return $records;
}
endif;
//get_access_ranking_records();
// global $wpdb;
// var_dump($wpdb->term_relationships);


//Jetpackがインストールされているかどうか
if ( !function_exists( 'is_jetpack_stats_module_active' ) ):
function is_jetpack_stats_module_active(){
  return class_exists( 'jetpack' ) &&
    Jetpack::is_module_active( 'stats' );
}
endif;

//Jetpackアクセス数取得関数
if ( !function_exists( 'get_several_jetpack_access_count' ) ):
function get_several_jetpack_access_count($post_id = null, $days = -1){
  $views = 0;
  if (is_jetpack_stats_module_active()) {
    if (!$post_id) {
      global $post;
      $post_id = $post->ID;
    }
    $jetpack_views = stats_get_csv('postviews', array('days' => $days, 'limit' => 1, 'post_id' => $post_id ));
    if (isset($jetpack_views[0]['views'])) {
      $views = $jetpack_views[0]['views'];
    }
  }
  return $views;
}
endif;

//今日のJetpackアクセス数を取得
if ( !function_exists( 'get_todays_jetpack_access_count' ) ):
function get_todays_jetpack_access_count($post_id = null){
  return get_several_jetpack_access_count($post_id, 1);
}
endif;

//直近7日間Jetpackアクセス数を取得
if ( !function_exists( 'get_last_7days_jetpack_access_count' ) ):
function get_last_7days_jetpack_access_count($post_id = null){
  return get_several_jetpack_access_count($post_id, 7);
}
endif;

//直近30日間Jetpackアクセス数を取得
if ( !function_exists( 'get_last_30days_jetpack_access_count' ) ):
function get_last_30days_jetpack_access_count($post_id = null){
  return get_several_jetpack_access_count($post_id, 30);
}
endif;

//全てのJetpackアクセス数を取得
if ( !function_exists( 'get_all_jetpack_access_count' ) ):
function get_all_jetpack_access_count($post_id = null){
  return get_several_jetpack_access_count($post_id, -1);
}
endif;

//今日のアクセス数取得
if ( !function_exists( 'get_todays_pv' ) ):
function get_todays_pv(){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_todays_jetpack_access_count();
      break;
    default:
      $res = intval(get_todays_access_count());
      break;
  }
  return $res;
}
endif;

//直近7日間のアクセス数取得
if ( !function_exists( 'get_last_7days_pv' ) ):
function get_last_7days_pv(){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_last_7days_jetpack_access_count();
      break;
    default:
      $res = intval(get_last_7days_access_count());
      break;
  }
  return $res;
}
endif;

//直近30日間のアクセス数取得
if ( !function_exists( 'get_last_30days_pv' ) ):
function get_last_30days_pv(){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_last_30days_jetpack_access_count();
      break;
    default:
      $res = intval(get_last_30days_access_count());
      break;
  }
  return $res;
}
endif;

//全期間のアクセス数取得
if ( !function_exists( 'get_all_pv' ) ):
function get_all_pv(){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_all_jetpack_access_count();
      break;
    default:
      $res = intval(get_all_access_count());
      break;
  }
  return $res;
}
endif;
