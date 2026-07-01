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
define('ACCESSES_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0.3');//rand(0, 99)
define('ACCESSES_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_accesses');

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
      INDEX `".INDEX_ACCESSES_PID_PTYPE_DATE."` (post_id,post_type,date)
    )";
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
    create_accesses_table();
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
    $transient_id = TRANSIENT_POPULAR_PREFIX.'?days='.$days.'&limit='.$limit.'&type='.$type.'&cats='.$cats.'&children='.$children.'&expids='.$expids.'&excats='.$excats.'&author='.$author.'&post_type='.$post_type;

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
    $joined_table = 'terms_accesses';
    //カテゴリー指定
    if (is_ids_exist($cat_ids)) {
      $cat_ids_safe = implode(',', array_map('intval', $cat_ids));
      $where .= " AND `{$term_taxonomy}`.term_id IN ({$cat_ids_safe}) ".PHP_EOL;
    }
    //除外カテゴリー指定
    if (is_ids_exist($exclude_cat_ids)) {
      $ex_cat_ids = implode(',', array_map('intval', $exclude_cat_ids));
      $where .= " AND `{$term_relationships}`.term_taxonomy_id NOT IN ({$ex_cat_ids}) ".PHP_EOL;
    }

    $where .= " AND `{$term_taxonomy}`.taxonomy = 'category' ".PHP_EOL;
    $query = "
      SELECT `{$joined_table}`.post_id, SUM(`{$joined_table}`.count) AS sum_count, `{$joined_table}`.term_taxonomy_id, `{$joined_table}`.taxonomy
        FROM (

          #カテゴリーとアクセステーブルを内部結合してグルーピングし並び替えた結果
          SELECT `{$access_table}`.post_id, `{$access_table}`.count, `{$term_relationships}`.term_taxonomy_id, `{$term_taxonomy}`.taxonomy
            FROM `{$term_relationships}`
            INNER JOIN `{$access_table}` ON `{$term_relationships}`.object_id = `{$access_table}`.post_id
            INNER JOIN `{$term_taxonomy}` ON `{$term_relationships}`.term_taxonomy_id = `{$term_taxonomy}`.term_taxonomy_id
            $where #WHERE句
            GROUP BY `{$access_table}`.id

        ) AS `{$joined_table}` #カテゴリーとアクセステーブルを内部結合した仮の名前

        GROUP BY `{$joined_table}`.post_id
        ORDER BY sum_count DESC, post_id
    ";

    //1回のクエリで投稿データを取り出せるようにテーブル結合クエリを追加
    $query = wrap_joined_wp_posts_query($query, $limit, $author, $post_type, $snippet);
  } else {
    $query = "
      SELECT `{$access_table}`.post_id, SUM(`{$access_table}`.count) AS sum_count
        FROM `{$access_table}` $where
        GROUP BY `{$access_table}`.post_id
        ORDER BY sum_count DESC, post_id
    ";
    //1回のクエリで投稿データを取り出せるようにテーブル結合クエリを追加
    $query = wrap_joined_wp_posts_query($query, $limit, $author, $post_type, $snippet);
  }
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
  // アクセス解析機能が無効の場合は処理を中断します
  if (!is_access_count_enable()) return;

  wp_add_dashboard_widget(
    'cocoon_analytics_dashboard_widget', // ウィジェットを一意に識別するIDです
    __('アクセス推移 (Cocoon)', THEME_NAME), // ダッシュボードに表示されるウィジェットのタイトルです
    'cocoon_analytics_dashboard_widget_renderer' // グラフを描画するための表示関数です
  );
}
endif;

/**
 * ダッシュボードウィジェットの表示HTMLとJSをレンダリングします
 */
if ( !function_exists( 'cocoon_analytics_dashboard_widget_renderer' ) ):
function cocoon_analytics_dashboard_widget_renderer() {
  $to = current_time('Y-m-d');
  
  // 1. 日別 (daily): 直近7日間（本日を含む）
  $from_daily = date('Y-m-d', strtotime($to . ' -6 days'));
  $daily = cocoon_analytics_daily_pv($from_daily, $to);
  
  // 2. 週別 (weekly): 直近7週分（今週を含む）
  $from_weekly = date('Y-m-d', strtotime($to . ' -48 days')); // 約7週間前
  $weekly = cocoon_analytics_weekly_pv($from_weekly, $to);
  if (count($weekly) > 7) {
    $weekly = array_slice($weekly, -7);
  }
  
  // 3. 月別 (monthly): 直近7ヶ月分（当月を含む）
  $from_monthly = date('Y-m-01', strtotime($to . ' -6 months'));
  $monthly = cocoon_analytics_monthly_pv($from_monthly, $to);
  if (count($monthly) > 7) {
    $monthly = array_slice($monthly, -7);
  }

  // 4. 年別 (yearly): 直近7年分（今年を含む）
  $current_year = (int) date('Y');
  $from_year = $current_year - 6;
  $from_yearly = $from_year . '-01-01';
  $daily_all = cocoon_analytics_daily_pv($from_yearly, $to);
  $yearly_buckets = array();
  for ($y = $from_year; $y <= $current_year; $y++) {
    $yearly_buckets[$y] = 0;
  }
  foreach ($daily_all as $d) {
    $y = (int) substr($d['date'], 0, 4);
    if (isset($yearly_buckets[$y])) {
      $yearly_buckets[$y] += (int) $d['pv'];
    }
  }
  $yearly = array();
  foreach ($yearly_buckets as $y => $pv) {
    $yearly[] = array('date' => (string) $y, 'pv' => $pv);
  }

  // 5. 人気記事の期間別データ取得 (today / 7days / 30days / 90days / 1year / all)
  $periods = array(
    'today'  => cocoon_analytics_resolve_period('today'),
    '7days'  => cocoon_analytics_resolve_period('7days'),
    '30days' => cocoon_analytics_resolve_period('30days'),
    '90days' => cocoon_analytics_resolve_period('90days'),
    '1year'  => array(
      'from' => date('Y-m-d', strtotime($to . ' -1 year +1 day')),
      'to'   => $to
    ),
    'all'    => array('from' => '1000-01-01', 'to' => $to),
  );

  $ranking_data = array();
  foreach ($periods as $key => $p) {
    $rows = cocoon_analytics_ranking($p['from'], $p['to'], null, 5);
    $formatted = array();
    $rank = 1;
    foreach ($rows as $item) {
      $post_id = $item['post_id'];
      $title = get_the_title($post_id);
      if (empty($title)) {
        $title = __('(タイトルなし)', THEME_NAME);
      }
      $formatted[] = array(
        'rank'  => $rank,
        'title' => esc_html($title),
        'url'   => esc_url(get_permalink($post_id)),
        'pv'    => number_format_i18n($item['pv']),
      );
      $rank++;
    }
    $ranking_data[$key] = $formatted;
  }

  // JS側へデータを渡すJSON形式にエンコードします
  $json_data = wp_json_encode(array(
    'daily'   => $daily,
    'weekly'  => $weekly,
    'monthly' => $monthly,
    'yearly'  => $yearly,
    'ranking' => $ranking_data,
  ));

  // WordPressの管理画面「アクセス集計」ページのリンクURLを取得します
  $analytics_page_url = admin_url('admin.php?page=theme-access');

  // グラフ描画に必要なChart.jsスクリプトを読み込みリストに登録します
  wp_enqueue_script(
    'cocoon-analytics-chartjs',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
    array(),
    '4.4.1',
    true
  );
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
          <option value="today"><?php _e('今日', THEME_NAME); ?></option>
          <option value="7days" selected><?php _e('7日間', THEME_NAME); ?></option>
          <option value="30days"><?php _e('30日間', THEME_NAME); ?></option>
          <option value="90days"><?php _e('3ヶ月', THEME_NAME); ?></option>
          <option value="1year"><?php _e('1年', THEME_NAME); ?></option>
          <option value="all"><?php _e('全期間', THEME_NAME); ?></option>
        </select>
      </h4>
      
      <ul id="cocoon-analytics-dashboard-ranking-list" style="margin: 0; padding: 0; list-style: none;">
        <!-- 初期表示時は直近7日間のデータをPHP側から出力します -->
        <?php if (!empty($ranking_data['7days'])): ?>
          <?php foreach ($ranking_data['7days'] as $item): 
            $badge_bg = '#888';
            if ($item['rank'] === 1) $badge_bg = '#dfb100'; // 金
            if ($item['rank'] === 2) $badge_bg = '#a8a8a8'; // 銀
            if ($item['rank'] === 3) $badge_bg = '#b06f00'; // 銅
            ?>
            <li style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #f0f0f0; font-size: 12px; gap: 10px;">
              <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1;">
                <span style="display: inline-block; width: 18px; height: 18px; line-height: 18px; text-align: center; background-color: <?php echo $badge_bg; ?>; color: #fff; border-radius: 50%; font-size: 10px; font-weight: bold; flex-shrink: 0;">
                  <?php echo $item['rank']; ?>
                </span>
                <a href="<?php echo $item['url']; ?>" target="_blank" style="text-decoration: none; color: #0073aa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                  <?php echo $item['title']; ?>
                </a>
              </div>
              <span style="color: #666; font-size: 11px; font-weight: 500; flex-shrink: 0; min-width: 45px; text-align: right;">
                <?php echo $item['pv']; ?> PV
              </span>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="cocoon-analytics-dashboard-ranking-empty" style="font-size: 12px; color: #999; margin: 10px 0; text-align: center;">
            <?php _e('集計データがまだありません。', THEME_NAME); ?>
          </p>
        <?php endif; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
      // サーバーから渡されたPVおよび人気記事データです
      var chartData = <?php echo $json_data; ?>;
      var canvas = document.getElementById('cocoon-analytics-dashboard-chart');
      if (!canvas || !chartData) return;

      var chartInstance = null;
      var buttons = document.querySelectorAll('.cocoon-analytics-dashboard-btn');

      // グラフの描画およびアップデートを実行する関数です
      var renderChart = function(type) {
        if (typeof Chart === 'undefined') {
          // スクリプトのロードが終わっていない場合は100ミリ秒後に再試行します
          setTimeout(function() { renderChart(type); }, 100);
          return;
        }

        var list = chartData[type];
        if (!list || !list.length) {
          if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
          }
          return;
        }

        // 各種期間タイプに応じた日付ラベルの日本語フォーマット処理を行います
        var labels = list.map(function(d) {
          var parts = d.date.split('-');
          if (type === 'daily' || type === 'weekly') {
            // YYYY-MM-DD ➔ M月 D
            if (parts.length === 3) {
              return parseInt(parts[1], 10) + '月 ' + parseInt(parts[2], 10);
            }
          } else if (type === 'monthly') {
            // YYYY-MM ➔ M月
            if (parts.length === 2) {
              return parseInt(parts[1], 10) + '月';
            }
          }
          return d.date; // 年別の場合は YYYY そのまま
        });
        var pvData = list.map(function(d) { return d.pv; });

        var config = {
          type: 'bar', // すべての期間でJetpack Statsに合わせ「棒グラフ」に統一します
          data: {
            labels: labels,
            datasets: [{
              label: '<?php echo esc_js(__('PV数', THEME_NAME)); ?>',
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
                  afterLabel: function (ctx) {
                    var row = list[ctx.dataIndex];
                    if (type === 'weekly') {
                      if (row && row.days && row.days < 7) {
                        return '<?php echo esc_js(__('部分週', THEME_NAME)); ?>' + ': ' + row.days + '/7 ' + '<?php echo esc_js(__('日', THEME_NAME)); ?>';
                      }
                    } else if (type === 'monthly') {
                      if (row && row.days) {
                        return '<?php echo esc_js(__('日数', THEME_NAME)); ?>' + ': ' + row.days + ' ' + '<?php echo esc_js(__('日', THEME_NAME)); ?>';
                      }
                    }
                    return '';
                  }
                }
              }
            },
            scales: {
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
        chartInstance = new Chart(canvas, config);
      };

      // 切り替えトグルボタンのイベントハンドラーを設定します
      buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          buttons.forEach(function (b) { b.classList.remove('is-active'); });
          btn.classList.add('is-active');
          renderChart(btn.getAttribute('data-type'));
        });
      });

      // 初期選択状態として「日」を設定して描画します
      var defaultBtn = document.querySelector('.cocoon-analytics-dashboard-btn[data-type="daily"]');
      if (defaultBtn) {
        defaultBtn.classList.add('is-active');
      }
      renderChart('daily');

      // 人気記事の期間切り替えセレクトボックスのイベントハンドラーを設定します
      var rankingPeriodSelect = document.querySelector('.cocoon-analytics-dashboard-ranking-period');
      var rankingListContainer = document.getElementById('cocoon-analytics-dashboard-ranking-list');

      if (rankingPeriodSelect && rankingListContainer && chartData.ranking) {
        rankingPeriodSelect.addEventListener('change', function() {
          var period = rankingPeriodSelect.value;
          var items = chartData.ranking[period] || [];

          rankingListContainer.innerHTML = '';

          if (items.length === 0) {
            var empty = document.createElement('p');
            empty.className = 'cocoon-analytics-dashboard-ranking-empty';
            empty.style.cssText = 'font-size: 12px; color: #999; margin: 10px 0; text-align: center;';
            empty.textContent = '<?php echo esc_js(__('集計データがまだありません。', THEME_NAME)); ?>';
            rankingListContainer.appendChild(empty);
            return;
          }

          // 新しい期間の人気記事データを動的にリスト生成して描画します
          items.forEach(function(item) {
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
            link.style.cssText = 'text-decoration: none; color: #0073aa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
            link.textContent = item.title;
            
            link.addEventListener('mouseover', function() { link.style.textDecoration = 'underline'; });
            link.addEventListener('mouseout', function() { link.style.textDecoration = 'none'; });

            leftDiv.appendChild(badge);
            leftDiv.appendChild(link);

            var pvSpan = document.createElement('span');
            pvSpan.style.cssText = 'color: #666; font-size: 11px; font-weight: 500; flex-shrink: 0; min-width: 45px; text-align: right;';
            pvSpan.textContent = item.pv + ' PV';

            li.appendChild(leftDiv);
            li.appendChild(pvSpan);

            rankingListContainer.appendChild(li);
          });
        });
      }
    });
  </script>
  <?php
}
endif;
