<?php //アクセス数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//関数テキストテーブルのバージョン
global $wpdb;
define('ACCESSES_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0.4');//rand(0, 99)
define('ACCESSES_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_accesses');

define('INDEX_ACCESSES_PID_PTYPE_DATE', 'index_pid_ptype_date');
//期間集計（WHERE date BETWEEN …）用。date先頭でレンジスキャンが効き、countまで含むためテーブル本体を読まずに完結するカバリングインデックス
define('INDEX_ACCESSES_DATE_PTYPE_PID_COUNT', 'index_date_ptype_pid_count');

//長時間かかるテーブル更新の多重起動を防ぐためのロックキー
define('TRANSIENT_ACCESSES_TABLE_UPDATING', THEME_NAME.'_accesses_table_updating');


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

//テーブルのバージョン取得
define('OP_ACCESSES_TABLE_VERSION', 'accesses_table_version');
if ( !function_exists( 'get_accesses_table_version' ) ):
function get_accesses_table_version(){
  return get_theme_option(OP_ACCESSES_TABLE_VERSION);
}
endif;

//ページタイプの取得
if ( !function_exists( 'get_accesses_post_type' ) ):
function get_accesses_post_type(){
  global $post;
  global $post_type;

  if (isset($post->post_type)) {
    $res = $post->post_type;
  } elseif (isset($post_type)) {
    $res = $post_type;
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
  $sql = "CREATE TABLE `".ACCESSES_TABLE_NAME."` (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      post_id bigint(20),
      post_type varchar(126) DEFAULT 'post',
      date varchar(20),
      count bigint(20) DEFAULT 0,
      last_ip varchar(40),
      PRIMARY KEY (id),
      INDEX `".INDEX_ACCESSES_PID_PTYPE_DATE."` (post_id,post_type,date),
      INDEX `".INDEX_ACCESSES_DATE_PTYPE_PID_COUNT."` (date,post_type,post_id,count)
    )";
  //行数の多いテーブルではインデックス追加に時間がかかり、PHPのタイムアウトで永久に完了しなくなるおそれ
  if (function_exists('set_time_limit')) {
    @set_time_limit(600);
  }
  $res = create_db_table($sql);

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
    //管理画面表示のたびに呼ばれるため、長時間かかるインデックス追加が同時多発してロック待ちになるのを防止
    if (!DEBUG_MODE) {
      if (get_transient(TRANSIENT_ACCESSES_TABLE_UPDATING)) {
        return;
      }
      set_transient(TRANSIENT_ACCESSES_TABLE_UPDATING, 1, 15 * MINUTE_IN_SECONDS);
    }
    create_accesses_table();
    delete_transient(TRANSIENT_ACCESSES_TABLE_UPDATING);
  }

}
endif;

//DBにアクセスをカウントする
if ( !function_exists( 'logging_page_access' ) ):
function logging_page_access($post_id = null, $post_type = 'post'){

  $res = false;
  //投稿・固定ページのみでカウントする
  if (is_access_count_enable()
      //サイト管理者でないとき
      && (!is_user_administrator() || DEBUG_MODE)
      //ボットでないとき
      && !is_useragent_robot()
    ) {
    if (!$post_id || !$post_type ) {
      global $post;
      if (isset($post->ID)) {
        $post_id = $post->ID;
      }
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
        if (($record->last_ip != $last_ip) || DEBUG_MODE) {
          $post_id = $record->id;
          $posts['last_ip'] = $last_ip;
          $posts['count'] = intval($record->count) + 1;
          $res = update_accesses_record($post_id, $posts);
        }
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

  $query = $wpdb->prepare("SELECT * FROM `{$table_name}` USE INDEX(`{$index}`) WHERE post_id = %d AND date = %s AND post_type = %s", $args);

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
  if (isset($post->ID)) {
    if (!$post_id) {
      $post_id = $post->ID;
    }

    $res = get_several_access_count($post_id, 1);
  }
  return $res;
}
endif;

//投稿一覧のPV列は1記事あたり4クエリ発行するため、一覧に表示中の全記事分を1クエリで先読みする
if ( !function_exists( 'prime_several_access_count_cache' ) ):
function prime_several_access_count_cache($post_ids, $post_type, $days_list = array(1, 7, 30, 'all')){
  global $wpdb;
  if (!is_access_count_enable()) {
    return;
  }
  $post_ids = array_values(array_unique(array_filter(array_map('intval', (array) $post_ids))));
  if (empty($post_ids) || !$post_type) {
    return;
  }

  if (!isset($GLOBALS['cocoon_access_count_cache'])) {
    $GLOBALS['cocoon_access_count_cache'] = array();
  }
  $cache =& $GLOBALS['cocoon_access_count_cache'];

  $days_list = array_values((array) $days_list);
  if (empty($days_list)) {
    return;
  }

  //既に先読み済みの記事を除外
  $targets = array();
  foreach ($post_ids as $pid) {
    if (!isset($cache[$pid.'|'.$post_type.'|'.$days_list[0]])) {
      $targets[] = $pid;
    }
  }
  if (empty($targets)) {
    return;
  }

  $date = get_current_db_date();
  $selects = array();
  $args = array();
  $aliases = array();
  foreach ($days_list as $i => $days) {
    $alias = 'pv'.$i;
    $aliases[$alias] = $days;
    if ($days === 'all') {
      $selects[] = "COALESCE(SUM(count),0) AS {$alias}";
    } elseif ((int) $days === 1) {
      $selects[] = "COALESCE(SUM(CASE WHEN date = %s THEN count END),0) AS {$alias}";
      $args[] = $date;
    } else {
      $selects[] = "COALESCE(SUM(CASE WHEN date BETWEEN %s AND %s THEN count END),0) AS {$alias}";
      $args[] = get_current_db_date_before($days);
      $args[] = $date;
    }
  }

  $table_name = ACCESSES_TABLE_NAME;
  $ids = implode(',', $targets); //intval済みの整数のみのため直接埋め込み
  $args[] = $post_type;
  $sql = "SELECT post_id, ".implode(', ', $selects)."
          FROM `{$table_name}`
          WHERE post_id IN ({$ids}) AND post_type = %s
          GROUP BY post_id";
  $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);

  $found = array();
  foreach ((array) $rows as $row) {
    $found[(int) $row['post_id']] = $row;
  }
  //アクセス記録が無い記事も0で埋め、キャッシュミスによる再クエリを防ぐ
  foreach ($targets as $pid) {
    foreach ($aliases as $alias => $days) {
      $cache[$pid.'|'.$post_type.'|'.$days] = isset($found[$pid]) ? (int) $found[$pid][$alias] : 0;
    }
  }
}
endif;

//アクセス取得関数（$daysに取得する日数を入力、もしくはallで全取得）
if ( !function_exists( 'get_several_access_count' ) ):
function get_several_access_count($post_id = null, $days = 'all'){
  $res = 0;
  global $post;
  global $wpdb;
  if (is_access_count_enable() && isset($post->ID)) {;
    if (!$post_id) {
      $post_id = $post->ID;
    }

    $date = get_current_db_date();

    $date_before = get_current_db_date_before($days);
    $table_name = ACCESSES_TABLE_NAME;
    $post_type = get_accesses_post_type();

    //一覧表示用に先読み済みの値があれば再クエリしない
    $cache_key = $post_id.'|'.$post_type.'|'.$days;
    if (isset($GLOBALS['cocoon_access_count_cache'][$cache_key])) {
      return $GLOBALS['cocoon_access_count_cache'][$cache_key];
    }

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

    $query = $wpdb->prepare("SELECT SUM(count) FROM `{$table_name}` WHERE post_id = %d AND post_type = %s".$add_where, $args);

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

if ( !function_exists( 'wrap_joined_wp_posts_query' ) ):
function wrap_joined_wp_posts_query($query, $limit, $author, $post_type, $snippet = 0){
  global $wpdb;
  $wp_posts = $wpdb->posts;
  $ranks_posts = 'ranks_posts';

  // LIMIT句の組み立て（-1 の場合は全件取得のため LIMIT を付けない）
  $limit = intval($limit);
  $limit_query = '';
  if ( $limit > 0 ) {
    $limit_query = " LIMIT {$limit}\n";
  }

  $author_query = '';
  if ($author) {
    $author_query = $wpdb->prepare(' AND post_author = %d', $author);
  }

  $post_type_where = $wpdb->prepare('post_type = %s', $post_type);
  $query = "
    SELECT ID, sum_count, post_title, post_author, post_date, post_modified, post_status, post_type, comment_count FROM (
      {$query}
    ) AS `{$ranks_posts}`
    INNER JOIN `{$wp_posts}` ON `{$ranks_posts}`.post_id = `{$wp_posts}`.id
    WHERE post_status = 'publish' AND
          {$post_type_where}" .
          $author_query . "
    ORDER BY sum_count DESC, post_date DESC
  ";

  // 必要に応じて LIMIT 句を後ろに付け足す
  $query .= $limit_query;

  return $query;
}
endif;

//アクセスランキングを取得
if ( !function_exists( 'get_access_ranking_records' ) ):
function get_access_ranking_records($days = 'all', $limit = 5, $type = ET_DEFAULT, $cat_ids = array(), $exclude_post_ids = array(), $exclude_cat_ids = array(), $children = 0, $author = null, $post_type = 'post', $snippet = 0){
  //カテゴリー配列を文字列に変換
  $cat_ids = is_array($cat_ids) ? $cat_ids : array();
  $cats = implode(',', $cat_ids);

  //アクセスキャッシュを有効にしている場合
  if (is_access_count_cache_enable()) {
    if ($cat_ids) {
      //子孫カテゴリーも含める場合
      if ($children) {
        $categories = $cat_ids;
        $res = $categories;
        foreach ($categories as $category) {
          $res = array_merge($res, get_term_children( $category, 'category' ));
        }
        $cat_ids = $res;
        $cats = implode(',', $res);
      }
    }

    //除外投稿
    $archive_exclude_post_ids = get_archive_exclude_post_ids();
    if ($archive_exclude_post_ids && is_array($archive_exclude_post_ids)) {
      $exclude_post_ids = array_unique(array_merge($exclude_post_ids, $archive_exclude_post_ids));
    }

    $exclude_post_ids = is_array($exclude_post_ids) ? $exclude_post_ids : array();
    $expids = implode(',', $exclude_post_ids);
    $exclude_cat_ids = is_array($exclude_cat_ids) ? $exclude_cat_ids : array();
    $excats = implode(',', $exclude_cat_ids);
    $type = get_accesses_post_type();
    //option_nameは191文字までのため、除外指定が多いとキーが切り詰められて別設定と衝突する
    $transient_id = TRANSIENT_POPULAR_PREFIX.'_'.md5('?days='.$days.'&limit='.$limit.'&type='.$type.'&cats='.$cats.'&children='.$children.'&expids='.$expids.'&excats='.$excats.'&author='.$author.'&post_type='.$post_type);

    $cache = get_transient( $transient_id );
    if ($cache) {
      if (DEBUG_MODE && is_user_administrator()) {
        // echo('<pre>');
        // echo $transient_id;
        // echo('</pre>');
      } elseif (is_user_administrator()){

      } else {
        return $cache;
      }
    }
  }



  global $wpdb;
  $access_table = ACCESSES_TABLE_NAME;
  $date = get_current_db_date();


  $where = $wpdb->prepare(" WHERE `{$access_table}`.post_type = %s ", $post_type).PHP_EOL;
  if ($days != 'all') {
    $date_before = get_current_db_date_before($days);
    $where .= $wpdb->prepare( " AND `{$access_table}`.date BETWEEN %s AND %s ", $date_before, $date ) . PHP_EOL;
  }

  if (is_ids_exist($exclude_post_ids)) {
    $exclude_post_ids_safe = array_map('intval', $exclude_post_ids);
    $where .= " AND `{$access_table}`.post_id NOT IN(".implode(',', $exclude_post_ids_safe).") ".PHP_EOL;
  }

  if (!is_numeric($limit)) {
    $limit = 5;
  }
  //カテゴリーを指定する場合
  if (is_ids_exist($cat_ids) || is_ids_exist($exclude_cat_ids)) {
    $term_relationships = $wpdb->term_relationships;
    $term_taxonomy = $wpdb->term_taxonomy;

    $term_where = " WHERE `{$term_taxonomy}`.taxonomy = 'category' ".PHP_EOL;
    //カテゴリー指定
    if (is_ids_exist($cat_ids)) {
      $cat_ids_safe = implode(',', array_map('intval', $cat_ids));
      $term_where .= " AND `{$term_taxonomy}`.term_id IN ({$cat_ids_safe}) ".PHP_EOL;
    }
    //除外カテゴリー指定
    if (is_ids_exist($exclude_cat_ids)) {
      $ex_cat_ids = implode(',', array_map('intval', $exclude_cat_ids));
      $term_where .= " AND `{$term_relationships}`.term_taxonomy_id NOT IN ({$ex_cat_ids}) ".PHP_EOL;
    }

    //アクセステーブルとカテゴリーを直接結合すると記事数×日数の一時テーブルが実体化するため、
    //カテゴリー側は対象記事IDの絞り込みだけを担当させ、集計はアクセステーブル単体で完結させる
    $where .= " AND `{$access_table}`.post_id IN (
          SELECT `{$term_relationships}`.object_id
            FROM `{$term_relationships}`
            INNER JOIN `{$term_taxonomy}` ON `{$term_relationships}`.term_taxonomy_id = `{$term_taxonomy}`.term_taxonomy_id
            {$term_where}
        ) ".PHP_EOL;
  }

  $query = "
    SELECT `{$access_table}`.post_id, SUM(`{$access_table}`.count) AS sum_count
      FROM `{$access_table}` $where
      GROUP BY `{$access_table}`.post_id
      ORDER BY sum_count DESC, post_id
  ";
  //1回のクエリで投稿データを取り出せるようにテーブル結合クエリを追加
  $query = wrap_joined_wp_posts_query($query, $limit, $author, $post_type, $snippet);

  $records = $wpdb->get_results( $query );

  if (is_access_count_cache_enable() && $records) {
    set_transient( $transient_id, $records, 60 * get_access_count_cache_interval() );
  }
  return $records;
}
endif;


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
  global $post;
  if (is_jetpack_stats_module_active() && isset($post->ID)) {
    if (!$post_id) {
      $post_id = $post->ID;
    }
    if (function_exists('stats_get_csv')) {
      $jetpack_views = stats_get_csv('postviews', array('days' => $days, 'limit' => 1, 'post_id' => $post_id ));
      if (isset($jetpack_views[0]['views'])) {
        $views = $jetpack_views[0]['views'];
      }
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
function get_todays_pv($post_id = null){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_todays_jetpack_access_count($post_id);
      break;
    default:
      $res = intval(get_todays_access_count($post_id));
      break;
  }
  return $res;
}
endif;

//直近7日間のアクセス数取得
if ( !function_exists( 'get_last_7days_pv' ) ):
function get_last_7days_pv($post_id = null){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_last_7days_jetpack_access_count($post_id);
      break;
    default:
      $res = intval(get_last_7days_access_count($post_id));
      break;
  }
  return $res;
}
endif;

//直近30日間のアクセス数取得
if ( !function_exists( 'get_last_30days_pv' ) ):
function get_last_30days_pv($post_id = null){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_last_30days_jetpack_access_count($post_id);
      break;
    default:
      $res = intval(get_last_30days_access_count($post_id));
      break;
  }
  return $res;
}
endif;

//全期間のアクセス数取得
if ( !function_exists( 'get_all_pv' ) ):
function get_all_pv($post_id = null){
  $res = 0;
  switch (get_admin_panel_pv_type()) {
    case 'jetpack':
      $res = get_all_jetpack_access_count($post_id);
      break;
    default:
      $res = intval(get_all_access_count($post_id));
      break;
  }
  return $res;
}
endif;

/**
 * ダッシュボードにCocoonアクセス解析ウィジェットを登録します
 */
add_action('wp_dashboard_setup', 'cocoon_analytics_add_dashboard_widget');
if ( !function_exists( 'cocoon_analytics_add_dashboard_widget' ) ):
function cocoon_analytics_add_dashboard_widget() {
  // 管理権限がないユーザーにはウィジェットを追加しないように制限します
  if (!current_user_can('manage_options')) return;
  // アクセス集計機能が無効の場合は処理を中断します
  if (!is_access_count_enable()) return;
  // アクセス解析ダッシュボード機能が無効の場合は処理を中断します
  if (!is_access_analytics_enable()) return;

  wp_add_dashboard_widget(
    'cocoon_analytics_dashboard_widget', // ウィジェットを一意に識別するIDです
    __('アクセス推移 (Cocoon)', THEME_NAME), // ダッシュボードに表示されるウィジェットのタイトルです
    'cocoon_analytics_dashboard_widget_renderer' // グラフを描画するための表示関数です
  );
}
endif;

/**
 * ダッシュボードウィジェットの表示HTMLとJSをレンダリングします
 *
 * 集計クエリはこの時点では一切実行せず、ウィジェットが実際に画面へ表示された
 * ときだけ非同期（AJAX）で取得します。「表示オプション」で非表示にした場合や
 * 折りたたんだ状態では、重い集計クエリが走りません。
 */
if ( !function_exists( 'cocoon_analytics_dashboard_widget_renderer' ) ):
function cocoon_analytics_dashboard_widget_renderer() {
  // アクセス集計ページの期間セレクトボックスと統一した表記
  $period_labels = cocoon_analytics_widget_period_labels();

  // アクセス集計ページの「既定の集計期間」設定に合わせた初期表示期間
  $default_ranking_period = cocoon_analytics_widget_period_range(get_access_analytics_default_period());
  $default_ranking_period = $default_ranking_period['key'];

  // WordPressの管理画面「アクセス集計」ページのリンクURLを取得します
  $analytics_page_url = admin_url('admin.php?page=theme-access');

  // JS側の設定値（AJAXエンドポイント・Chart.jsのURL・表示文言）
  $widget_config = wp_json_encode(array(
    'ajaxUrl'  => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('cocoon_analytics_dashboard_widget'),
    'chartJs'  => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
    'period'   => $default_ranking_period,
    'i18n'     => array(
      'pvLabel'     => __('PV数', THEME_NAME),
      'pv'          => __('PV', THEME_NAME),
      'day'         => __('日', THEME_NAME),
      'daysCount'   => __('日数', THEME_NAME),
      'partialWeek' => __('部分週', THEME_NAME),
      'empty'       => __('集計データがまだありません。', THEME_NAME),
      'error'       => __('データの取得に失敗しました。', THEME_NAME),
    ),
  ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); //インラインscript内への直接出力のため、タグを閉じられないようエスケープ
  ?>
  <!-- ダッシュボード専用の切り替えボタンスタイルを定義します -->
  <style>
    .cocoon-analytics-dashboard-btn:hover {
      background-color: rgba(0, 0, 0, 0.04);
      color: #202124;
    }
    .cocoon-analytics-dashboard-btn.is-active {
      background-color: #000 !important; /* Jetpack Stats風の黒いアクティブ背景にします */
      color: #fff !important;
      font-weight: bold;
    }
  </style>

  <div class="cocoon-analytics-dashboard-widget-container">
    <!-- 日、週、月、年の切り替えトグルボタン群です -->
    <div class="cocoon-analytics-dashboard-switcher" style="display: inline-flex; background-color: #fff; border: 1px solid #ccd0d4; border-radius: 6px; padding: 2px; margin-bottom: 12px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
      <button type="button" class="cocoon-analytics-dashboard-btn" data-type="daily" style="background: transparent; border: none; border-radius: 4px; padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3c434a; cursor: pointer; outline: none; transition: background-color 0.2s, color 0.2s;"><?php _e('日', THEME_NAME); ?></button>
      <button type="button" class="cocoon-analytics-dashboard-btn" data-type="weekly" style="background: transparent; border: none; border-radius: 4px; padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3c434a; cursor: pointer; outline: none; transition: background-color 0.2s, color 0.2s;"><?php _e('週', THEME_NAME); ?></button>
      <button type="button" class="cocoon-analytics-dashboard-btn" data-type="monthly" style="background: transparent; border: none; border-radius: 4px; padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3c434a; cursor: pointer; outline: none; transition: background-color 0.2s, color 0.2s;"><?php _e('月', THEME_NAME); ?></button>
      <button type="button" class="cocoon-analytics-dashboard-btn" data-type="yearly" style="background: transparent; border: none; border-radius: 4px; padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3c434a; cursor: pointer; outline: none; transition: background-color 0.2s, color 0.2s;"><?php _e('年', THEME_NAME); ?></button>
    </div>

    <!-- グラフを描画するためのキャンバス領域です -->
    <div style="height: 180px; position: relative; margin-bottom: 20px;">
      <canvas id="cocoon-analytics-dashboard-chart"></canvas>
      <p class="cocoon-analytics-dashboard-chart-message" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; margin: 0; font-size: 12px; color: #999;">
        <?php _e('読み込み中...', THEME_NAME); ?>
      </p>
    </div>

    <!-- グラフとランキングの間に区切り線を引きます -->
    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;" />

    <!-- 人気記事ランキングセクションです -->
    <div class="cocoon-analytics-dashboard-ranking-section">
      <h4 style="margin: 0 0 10px 0; font-size: 13px; color: #23282d; display: flex; align-items: center; justify-content: space-between; gap: 5px;">
        <span style="display: flex; align-items: center; gap: 5px;">
          <span class="dashicons dashicons-editor-ol" style="font-size: 17px; width: 17px; height: 17px;"></span>
          <?php _e('人気記事 TOP5', THEME_NAME); ?>
        </span>
        <!-- 期間切り替えのドロップダウンです -->
        <select class="cocoon-analytics-dashboard-ranking-period" style="font-size: 11px; height: auto; padding: 2px 24px 2px 8px; margin: 0; line-height: 1.5; border-radius: 4px; border: 1px solid #ccd0d4; background-color: #f6f7f7; color: #2c3338; cursor: pointer;">
          <?php foreach ($period_labels as $period_key => $period_label): ?>
            <option value="<?php echo esc_attr($period_key); ?>" <?php selected($default_ranking_period, $period_key); ?>><?php echo esc_html($period_label); ?></option>
          <?php endforeach; ?>
        </select>
      </h4>

      <ul id="cocoon-analytics-dashboard-ranking-list" style="margin: 0; padding: 0; list-style: none;">
        <!-- 中身は表示された時点でAJAX取得します -->
      </ul>
    </div>

    <!-- アクセス集計詳細ページへのリンク動線を設置します -->
    <div style="margin-top: 15px; text-align: right;">
      <a href="<?php echo esc_url($analytics_page_url); ?>" style="display: inline-flex; align-items: center; gap: 3px; font-size: 12px; color: #0073aa; text-decoration: none; font-weight: 500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
        <?php _e('アクセス集計の詳細を見る', THEME_NAME); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 14px; width: 14px; height: 14px; margin-top: 1px;"></span>
      </a>
    </div>
  </div>
  <script>
    (function () {
      var config = <?php echo $widget_config; ?>;
      var widget = document.getElementById('cocoon_analytics_dashboard_widget');
      var container = (widget || document).querySelector('.cocoon-analytics-dashboard-widget-container');
      var canvas = document.getElementById('cocoon-analytics-dashboard-chart');
      if (!container || !canvas) return;

      var chartData = null;
      var chartInstance = null;
      var currentType = 'daily';
      var buttons = container.querySelectorAll('.cocoon-analytics-dashboard-btn');
      var message = container.querySelector('.cocoon-analytics-dashboard-chart-message');
      var rankingPeriodSelect = container.querySelector('.cocoon-analytics-dashboard-ranking-period');
      var rankingListContainer = document.getElementById('cocoon-analytics-dashboard-ranking-list');

      var setMessage = function (text) {
        if (!message) return;
        message.textContent = text || '';
        message.style.display = text ? 'flex' : 'none';
      };

      // Chart.js はウィジェットが実際に表示されたときだけ読み込みます
      var chartJsPromise = null;
      var ensureChartJs = function () {
        if (window.Chart) return Promise.resolve();
        if (chartJsPromise) return chartJsPromise;
        chartJsPromise = new Promise(function (resolve, reject) {
          var script = document.createElement('script');
          script.src = config.chartJs;
          script.onload = resolve;
          script.onerror = reject;
          document.head.appendChild(script);
        });
        return chartJsPromise;
      };

      var request = function (action, params) {
        var query = new URLSearchParams(params || {});
        query.set('action', action);
        query.set('nonce', config.nonce);
        return fetch(config.ajaxUrl + '?' + query.toString(), { credentials: 'same-origin' })
          .then(function (res) { return res.json(); })
          .then(function (json) {
            if (!json || !json.success) throw new Error('request_failed');
            return json.data;
          });
      };

      // グラフの描画およびアップデートを実行する関数です
      var renderChart = function (type) {
        currentType = type;
        if (!chartData) return;

        var list = chartData[type];
        if (!list || !list.length) {
          if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
          }
          setMessage(config.i18n.empty);
          return;
        }
        setMessage('');

        // ラベルはPHP側でサイトの日付書式・言語に合わせて整形済み
        var labels = list.map(function (d) { return d.label || d.date; });
        var pvData = list.map(function (d) { return d.pv; });

        var config_chart = {
          type: 'bar', // すべての期間でJetpack Statsに合わせ「棒グラフ」に統一します
          data: {
            labels: labels,
            datasets: [{
              label: config.i18n.pvLabel,
              data: pvData,
              backgroundColor: 'rgba(0, 138, 32, 0.85)', // Jetpack風の鮮やかな緑色に統一します
              borderColor: '#008a20',
              borderWidth: 1,
              borderRadius: 4, // 棒の頂点に丸みをつけてプレミアム感を演出します
              barPercentage: 0.6 // 棒の太さをJetpack風に適度にすっきりさせます
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  // 軸ラベルは月日のみのため、見出しは年を含む日付で表示します
                  title: function (items) {
                    if (!items || !items.length) { return ''; }
                    var row = list[items[0].dataIndex];
                    return row ? (row.tooltip_label || row.label || row.date) : '';
                  },
                  afterLabel: function (ctx) {
                    var row = list[ctx.dataIndex];
                    if (type === 'weekly') {
                      if (row && row.days && row.days < 7) {
                        return config.i18n.partialWeek + ': ' + row.days + '/7 ' + config.i18n.day;
                      }
                    } else if (type === 'monthly') {
                      if (row && row.days) {
                        return config.i18n.daysCount + ': ' + row.days + ' ' + config.i18n.day;
                      }
                    }
                    return '';
                  }
                }
              }
            },
            scales: {
              x: {
                ticks: {
                  // 軸の目盛りは月日のみのため、年が切り替わる位置と左端だけ年を2行目に併記します
                  // 表示は7件固定で目盛りの間引きが起きないため、隣同士の比較で判定できます
                  callback: function (value, index, ticks) {
                    var fallback = this && typeof this.getLabelForValue === 'function' ? this.getLabelForValue(value) : value;
                    if (type !== 'daily' && type !== 'weekly') { return fallback; }
                    var first = list[0];
                    var last = list[list.length - 1];
                    var firstYear = first && first.date ? String(first.date).slice(0, 4) : '';
                    var lastYear = last && last.date ? String(last.date).slice(0, 4) : '';
                    // 単年に収まる範囲では年が自明なため、月日のみのままにします
                    if (!firstYear || firstYear === lastYear) { return fallback; }
                    var tick = ticks && ticks[index];
                    var dataIndex = tick && typeof tick.value === 'number' ? tick.value : index;
                    var row = list[dataIndex];
                    if (!row || !row.date) { return fallback; }
                    var label = row.label || row.date;
                    var year = String(row.date).slice(0, 4);
                    var prevTick = index > 0 ? ticks[index - 1] : null;
                    var prevRow = prevTick ? list[prevTick.value] : null;
                    var prevYear = prevRow && prevRow.date ? String(prevRow.date).slice(0, 4) : '';
                    return year === prevYear ? label : [label, year];
                  }
                }
              },
              y: {
                beginAtZero: true,
                ticks: { precision: 0 }
              }
            }
          }
        };

        // すでに描画されているグラフを一旦破棄して再生成します
        if (chartInstance) {
          chartInstance.destroy();
        }
        chartInstance = new Chart(canvas, config_chart);
      };

      // 人気記事ランキングのリストを組み立てます
      var renderRanking = function (items, emptyMessage) {
        if (!rankingListContainer) return;
        rankingListContainer.innerHTML = '';

        if (!items || !items.length) {
          var empty = document.createElement('p');
          empty.className = 'cocoon-analytics-dashboard-ranking-empty';
          empty.style.cssText = 'font-size: 12px; color: #999; margin: 10px 0; text-align: center;';
          empty.textContent = emptyMessage || config.i18n.empty;
          rankingListContainer.appendChild(empty);
          return;
        }

        items.forEach(function (item) {
          var badgeBg = '#888';
          if (item.rank === 1) badgeBg = '#dfb100';
          else if (item.rank === 2) badgeBg = '#a8a8a8';
          else if (item.rank === 3) badgeBg = '#b06f00';

          var li = document.createElement('li');
          li.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #f0f0f0; font-size: 12px; gap: 10px;';

          var leftDiv = document.createElement('div');
          leftDiv.style.cssText = 'display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1;';

          var badge = document.createElement('span');
          badge.style.cssText = 'display: inline-block; width: 18px; height: 18px; line-height: 18px; text-align: center; background-color: ' + badgeBg + '; color: #fff; border-radius: 50%; font-size: 10px; font-weight: bold; flex-shrink: 0;';
          badge.textContent = item.rank;

          var link = document.createElement('a');
          link.href = item.url;
          link.target = '_blank';
          link.rel = 'noopener noreferrer';
          link.style.cssText = 'text-decoration: none; color: #0073aa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
          link.textContent = item.title;
          link.addEventListener('mouseover', function () { link.style.textDecoration = 'underline'; });
          link.addEventListener('mouseout', function () { link.style.textDecoration = 'none'; });

          leftDiv.appendChild(badge);
          leftDiv.appendChild(link);

          var pvSpan = document.createElement('span');
          pvSpan.style.cssText = 'color: #666; font-size: 11px; font-weight: 500; flex-shrink: 0; min-width: 45px; text-align: right;';
          pvSpan.textContent = item.pv + ' ' + config.i18n.pv;

          li.appendChild(leftDiv);
          li.appendChild(pvSpan);
          rankingListContainer.appendChild(li);
        });
      };

      // 切り替えトグルボタンのイベントハンドラーを設定します
      buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          buttons.forEach(function (b) { b.classList.remove('is-active'); });
          btn.classList.add('is-active');
          renderChart(btn.getAttribute('data-type'));
        });
      });

      var defaultBtn = container.querySelector('.cocoon-analytics-dashboard-btn[data-type="daily"]');
      if (defaultBtn) {
        defaultBtn.classList.add('is-active');
      }

      // 初期取得と期間切り替えが前後してもランキングが巻き戻らないよう、最新リクエストだけを採用します
      var rankingSeq = 0;

      // 人気記事の期間切り替えは選択された期間だけを追加取得します
      if (rankingPeriodSelect) {
        rankingPeriodSelect.addEventListener('change', function () {
          var seq = ++rankingSeq;
          request('cocoon_analytics_get_dashboard_ranking', { period: rankingPeriodSelect.value })
            .then(function (data) { if (seq === rankingSeq) renderRanking(data.ranking); })
            .catch(function () { if (seq === rankingSeq) renderRanking([], config.i18n.error); });
        });
      }

      // ウィジェットが表示された最初の1回だけデータを取得します
      var loaded = false;
      var load = function () {
        if (loaded) return;
        loaded = true;
        var seq = ++rankingSeq;
        var dataPromise = request('cocoon_analytics_get_dashboard_widget', {});

        // Chart.js の取得に失敗してもランキングだけは表示できるよう、描画を独立させます
        dataPromise.then(function (data) {
          chartData = data;
          if (seq !== rankingSeq) return;
          if (rankingPeriodSelect && data.period) {
            rankingPeriodSelect.value = data.period;
          }
          renderRanking(data.ranking);
        }).catch(function () {
          if (seq === rankingSeq) renderRanking([], config.i18n.error);
        });

        Promise.all([dataPromise, ensureChartJs()]).then(function () {
          renderChart(currentType);
        }).catch(function () {
          setMessage(config.i18n.error);
        });
      };

      // 「表示オプション」で非表示・折りたたみ中はクエリを走らせないための可視判定
      if (typeof IntersectionObserver === 'function') {
        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              observer.disconnect();
              load();
            }
          });
        });
        observer.observe(container);
      } else {
        load();
      }
    })();
  </script>
  <?php
}
endif;
