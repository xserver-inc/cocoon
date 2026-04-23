<?php //アクセス解析ダッシュボード - 集計クエリ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * Cocoon アクセス解析ダッシュボード
 * 集計クエリとキャッシュ層。データソースは {prefix}cocoon_accesses のみ。
 */
if ( !defined( 'ABSPATH' ) ) exit;

//////////////////////////////////////////////////
// 設定オプション定義
//////////////////////////////////////////////////

define('OP_ACCESS_ANALYTICS_ENABLE', 'access_analytics_enable');
define('OP_ACCESS_ANALYTICS_CACHE_TTL', 'access_analytics_cache_ttl');
define('OP_ACCESS_ANALYTICS_DEFAULT_PERIOD', 'access_analytics_default_period');
define('OP_ACCESS_ANALYTICS_EXPORT_ENABLE', 'access_analytics_export_enable');

if ( !function_exists( 'is_access_analytics_enable' ) ):
function is_access_analytics_enable(){
  return get_theme_option(OP_ACCESS_ANALYTICS_ENABLE, 1);
}
endif;

if ( !function_exists( 'get_access_analytics_cache_ttl' ) ):
function get_access_analytics_cache_ttl(){
  $ttl = (int) get_theme_option(OP_ACCESS_ANALYTICS_CACHE_TTL, 15);
  if ($ttl < 1) $ttl = 15;
  return $ttl;
}
endif;

if ( !function_exists( 'get_access_analytics_default_period' ) ):
function get_access_analytics_default_period(){
  return get_theme_option(OP_ACCESS_ANALYTICS_DEFAULT_PERIOD, '30days');
}
endif;

if ( !function_exists( 'is_access_analytics_export_enable' ) ):
function is_access_analytics_export_enable(){
  return get_theme_option(OP_ACCESS_ANALYTICS_EXPORT_ENABLE, 1);
}
endif;

//////////////////////////////////////////////////
// キャッシュ基盤
//////////////////////////////////////////////////

//transient キープレフィックス（キャッシュ一括削除に使用）
define('COCOON_ANALYTICS_TRANSIENT_PREFIX', 'cocoon_analytics_');

/**
 * データリビジョンを取得（MAX(id) と MAX(date) を組み合わせたハッシュ）
 * 新規アクセス記録があると値が変わるため、キャッシュが自動失効する。
 */
if ( !function_exists( 'cocoon_analytics_revision' ) ):
function cocoon_analytics_revision(){
  global $wpdb;
  static $rev = null;
  if ($rev !== null) return $rev;
  $table = ACCESSES_TABLE_NAME;
  if (!is_accesses_table_exist()) {
    $rev = '0';
    return $rev;
  }
  // 初心者向け: MAX(id) と MAX(date) の組み合わせで「最新の記録状態」を表す
  $row = $wpdb->get_row("SELECT MAX(id) AS mid, MAX(date) AS mdate FROM `{$table}`");
  $rev = md5(($row->mid ?? '0') . '|' . ($row->mdate ?? ''));
  return $rev;
}
endif;

/**
 * キャッシュラッパ。$producer は結果を返すクロージャ。
 * 結果が null/false でも配列として扱えるよう、配列にラップして保存する。
 */
if ( !function_exists( 'cocoon_analytics_cached' ) ):
function cocoon_analytics_cached($key_parts, callable $producer){
  $rev = cocoon_analytics_revision();
  $raw = is_array($key_parts) ? wp_json_encode($key_parts) : (string) $key_parts;
  $key = COCOON_ANALYTICS_TRANSIENT_PREFIX . md5($rev . '|' . $raw);

  $cached = get_transient($key);
  if ($cached !== false && is_array($cached) && array_key_exists('v', $cached)) {
    return $cached['v'];
  }
  $value = $producer();
  $ttl_min = get_access_analytics_cache_ttl();
  set_transient($key, array('v' => $value), max(60, $ttl_min * 60));
  return $value;
}
endif;

/**
 * 解析系キャッシュを一括削除（wp_options を直接走査）。
 */
/**
 * アクセステーブル内の最古の日付（YYYY-MM-DD）を返す。未計測なら空文字。
 */
if ( !function_exists( 'cocoon_analytics_min_date' ) ):
function cocoon_analytics_min_date(){
  global $wpdb;
  static $min = null;
  if ($min !== null) return $min;
  if (!is_accesses_table_exist()) {
    $min = '';
    return $min;
  }
  $table = ACCESSES_TABLE_NAME;
  // 初心者向け: アクセステーブルで最も古い日付を取得し「全期間」の開始日に使う
  $val = $wpdb->get_var("SELECT MIN(date) FROM `{$table}` WHERE date IS NOT NULL AND date <> ''");
  $min = ($val && preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) ? $val : '';
  return $min;
}
endif;

if ( !function_exists( 'cocoon_analytics_flush_cache' ) ):
function cocoon_analytics_flush_cache(){
  global $wpdb;
  $like = $wpdb->esc_like('_transient_' . COCOON_ANALYTICS_TRANSIENT_PREFIX) . '%';
  $like_timeout = $wpdb->esc_like('_transient_timeout_' . COCOON_ANALYTICS_TRANSIENT_PREFIX) . '%';
  $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like));
  $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like_timeout));
}
endif;

//////////////////////////////////////////////////
// 期間解決
//////////////////////////////////////////////////

/**
 * プリセット名 or カスタム日付から [from, to] (YYYY-MM-DD) を返す。
 */
if ( !function_exists( 'cocoon_analytics_resolve_period' ) ):
function cocoon_analytics_resolve_period($preset = '30days', $from = '', $to = ''){
  $today = current_time('Y-m-d');
  $from_out = $today;
  $to_out = $today;
  switch ($preset) {
    case 'today':
      $from_out = $today; $to_out = $today; break;
    case 'yesterday':
      $from_out = date('Y-m-d', strtotime($today . ' -1 day'));
      $to_out = $from_out; break;
    case '7days':
      $from_out = date('Y-m-d', strtotime($today . ' -6 days')); $to_out = $today; break;
    case '30days':
      $from_out = date('Y-m-d', strtotime($today . ' -29 days')); $to_out = $today; break;
    case '90days':
      $from_out = date('Y-m-d', strtotime($today . ' -89 days')); $to_out = $today; break;
    case 'thismonth':
      $from_out = date('Y-m-01', strtotime($today)); $to_out = $today; break;
    case 'lastmonth':
      $from_out = date('Y-m-01', strtotime($today . ' -1 month'));
      $to_out = date('Y-m-t', strtotime($from_out)); break;
    case 'ytd':
      $from_out = date('Y-01-01', strtotime($today)); $to_out = $today; break;
    case 'all':
      // 最初にアクセスが計測された日を開始日にする（未計測なら今日）
      $min_date = cocoon_analytics_min_date();
      $from_out = $min_date ? $min_date : $today;
      $to_out = $today; break;
    case 'custom':
      $from_out = cocoon_analytics_sanitize_date($from, $today);
      $to_out = cocoon_analytics_sanitize_date($to, $today);
      if ($from_out > $to_out) { list($from_out, $to_out) = array($to_out, $from_out); }
      break;
    default:
      $from_out = date('Y-m-d', strtotime($today . ' -29 days')); $to_out = $today;
  }
  return array('from' => $from_out, 'to' => $to_out);
}
endif;

if ( !function_exists( 'cocoon_analytics_sanitize_date' ) ):
function cocoon_analytics_sanitize_date($s, $fallback){
  if (!is_string($s) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return $fallback;
  $t = strtotime($s);
  if ($t === false) return $fallback;
  return date('Y-m-d', $t);
}
endif;

/**
 * 前期間（同じ長さ、直前）の [from, to] を返す。
 */
if ( !function_exists( 'cocoon_analytics_previous_period' ) ):
function cocoon_analytics_previous_period($from, $to){
  $days = (int) ((strtotime($to) - strtotime($from)) / 86400) + 1;
  $prev_to = date('Y-m-d', strtotime($from . ' -1 day'));
  $prev_from = date('Y-m-d', strtotime($prev_to . ' -' . ($days - 1) . ' days'));
  return array('from' => $prev_from, 'to' => $prev_to);
}
endif;

/**
 * 有効な投稿タイプリストをホワイトリスト化して返す。
 */
if ( !function_exists( 'cocoon_analytics_allowed_post_types' ) ):
function cocoon_analytics_allowed_post_types($requested = null){
  $all = get_post_types(array('public' => true), 'names');
  // attachment は集計対象から外す（通常不要）
  unset($all['attachment']);
  if (empty($requested)) return array_values($all);
  if (is_string($requested)) {
    $requested = ($requested === 'all') ? array() : array($requested);
  }
  if (empty($requested)) return array_values($all);
  $filtered = array();
  foreach ($requested as $pt) {
    if (isset($all[$pt])) $filtered[] = $pt;
  }
  return empty($filtered) ? array_values($all) : $filtered;
}
endif;

/**
 * post_type 配列を SQL の IN 句向けに整形（全プリペア済み文字列を返す）。
 */
if ( !function_exists( 'cocoon_analytics_in_placeholder' ) ):
function cocoon_analytics_in_placeholder(array $values){
  if (empty($values)) return array('sql' => "''", 'args' => array());
  $placeholders = implode(',', array_fill(0, count($values), '%s'));
  return array('sql' => $placeholders, 'args' => $values);
}
endif;

//////////////////////////////////////////////////
// 集計クエリ
//////////////////////////////////////////////////

/**
 * 期間合計PV
 */
if ( !function_exists( 'cocoon_analytics_total_pv' ) ):
function cocoon_analytics_total_pv($from, $to, $post_types = null){
  return cocoon_analytics_cached(array('total_pv', $from, $to, $post_types), function() use ($from, $to, $post_types){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $types = cocoon_analytics_allowed_post_types($post_types);
    $in = cocoon_analytics_in_placeholder($types);
    $sql = "SELECT COALESCE(SUM(count),0) AS pv FROM `{$table}`
            WHERE date BETWEEN %s AND %s AND post_id > 0 AND post_type IN ({$in['sql']})";
    $args = array_merge(array($from, $to), $in['args']);
    $pv = $wpdb->get_var($wpdb->prepare($sql, $args));
    return (int) $pv;
  });
}
endif;

/**
 * 日次PV推移（欠損日は0埋め）
 */
if ( !function_exists( 'cocoon_analytics_daily_pv' ) ):
function cocoon_analytics_daily_pv($from, $to, $post_types = null){
  return cocoon_analytics_cached(array('daily_pv', $from, $to, $post_types), function() use ($from, $to, $post_types){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $types = cocoon_analytics_allowed_post_types($post_types);
    $in = cocoon_analytics_in_placeholder($types);
    $sql = "SELECT date, COALESCE(SUM(count),0) AS pv FROM `{$table}`
            WHERE date BETWEEN %s AND %s AND post_id > 0 AND post_type IN ({$in['sql']})
            GROUP BY date ORDER BY date ASC";
    $args = array_merge(array($from, $to), $in['args']);
    $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);

    $map = array();
    foreach ($rows as $r) { $map[$r['date']] = (int) $r['pv']; }

    // 初心者向け: from〜to までループして欠損日を0で埋める
    $result = array();
    $cur = strtotime($from);
    $end = strtotime($to);
    $guard = 0;
    while ($cur <= $end && $guard++ < 3700) {
      $d = date('Y-m-d', $cur);
      $result[] = array('date' => $d, 'pv' => isset($map[$d]) ? $map[$d] : 0);
      $cur = strtotime($d . ' +1 day');
    }
    return $result;
  });
}
endif;

/**
 * 週次PV推移（日次PVを月曜始まりで集約）
 * ラベルは週開始日（YYYY-MM-DD）、days はその週に含まれる実日数（1～7）。
 */
if ( !function_exists( 'cocoon_analytics_weekly_pv' ) ):
function cocoon_analytics_weekly_pv($from, $to, $post_types = null){
  return cocoon_analytics_cached(array('weekly_pv', $from, $to, $post_types), function() use ($from, $to, $post_types){
    $daily = cocoon_analytics_daily_pv($from, $to, $post_types);
    $buckets = array();
    foreach ($daily as $d) {
      $ts = strtotime($d['date']);
      if ($ts === false) continue;
      // 初心者向け: ISO週の月曜日を週の開始日とする
      $dow = (int) date('N', $ts); // 1=Mon ... 7=Sun
      $week_start = date('Y-m-d', strtotime($d['date'] . ' -' . ($dow - 1) . ' days'));
      if (!isset($buckets[$week_start])) $buckets[$week_start] = array('pv' => 0, 'days' => 0);
      $buckets[$week_start]['pv']   += (int) $d['pv'];
      $buckets[$week_start]['days'] += 1;
    }
    ksort($buckets);
    $result = array();
    foreach ($buckets as $ws => $b) {
      $result[] = array('date' => $ws, 'pv' => $b['pv'], 'days' => $b['days']);
    }
    return $result;
  });
}
endif;

/**
 * 月次PV推移（日次PVを年月で集約）
 * ラベルは YYYY-MM、days はその月に含まれる実日数。1個のフル月は28～31、部分月はそれ未満。
 * 多すぎる場合（すべて・長期）は直近36ヶ月に割り込む。
 */
if ( !function_exists( 'cocoon_analytics_monthly_pv' ) ):
function cocoon_analytics_monthly_pv($from, $to, $post_types = null){
  return cocoon_analytics_cached(array('monthly_pv', $from, $to, $post_types), function() use ($from, $to, $post_types){
    $daily = cocoon_analytics_daily_pv($from, $to, $post_types);
    $buckets = array();
    foreach ($daily as $d) {
      $ym = substr($d['date'], 0, 7); // YYYY-MM
      if (!isset($buckets[$ym])) $buckets[$ym] = array('pv' => 0, 'days' => 0);
      $buckets[$ym]['pv']   += (int) $d['pv'];
      $buckets[$ym]['days'] += 1;
    }
    ksort($buckets);
    // 初心者向け: 月数が36を超えたら直近36ヶ月に限定しラベルを読みやすくする
    if (count($buckets) > 36) {
      $buckets = array_slice($buckets, -36, null, true);
    }
    $result = array();
    foreach ($buckets as $ym => $b) {
      $result[] = array('date' => $ym, 'pv' => $b['pv'], 'days' => $b['days']);
    }
    return $result;
  });
}
endif;

/**
 * 急上昇記事ランキング
 * 直近 $days 日 vs その前 $days 日 のPV比率で並べる。
 */
if ( !function_exists( 'cocoon_analytics_trending' ) ):
function cocoon_analytics_trending($days = 7, $limit = 10, $min_pv = 3){
  $days = max(1, min(90, (int) $days));
  $limit = max(1, min(100, (int) $limit));
  $min_pv = max(1, (int) $min_pv);
  return cocoon_analytics_cached(array('trending', $days, $limit, $min_pv), function() use ($days, $limit, $min_pv){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $today = current_time('Y-m-d');
    $cur_from  = date('Y-m-d', strtotime($today . ' -' . ($days - 1) . ' days'));
    $cur_to    = $today;
    $prev_to   = date('Y-m-d', strtotime($cur_from . ' -1 day'));
    $prev_from = date('Y-m-d', strtotime($cur_from . ' -' . $days . ' days'));
    $types = cocoon_analytics_allowed_post_types();
    $in = cocoon_analytics_in_placeholder($types);

    // 初心者向け: 同じ post_id に対して「直近」と「直前」のPVを条件付き合計し、増加率で並べる
    // 注: MariaDBでは ORDER BY の式中で集約エイリアスを参照できない場合があるため、SUM()式を直接書く
    $sql = "SELECT a.post_id, a.post_type,
              SUM(CASE WHEN a.date BETWEEN %s AND %s THEN a.count ELSE 0 END) AS cur_pv,
              SUM(CASE WHEN a.date BETWEEN %s AND %s THEN a.count ELSE 0 END) AS prev_pv
            FROM `{$table}` a
            INNER JOIN {$wpdb->posts} p ON p.ID = a.post_id AND p.post_status = 'publish'
            WHERE a.date BETWEEN %s AND %s AND a.post_id > 0 AND a.post_type IN ({$in['sql']})
            GROUP BY a.post_id, a.post_type
            HAVING SUM(CASE WHEN a.date BETWEEN %s AND %s THEN a.count ELSE 0 END) >= %d
            ORDER BY (SUM(CASE WHEN a.date BETWEEN %s AND %s THEN a.count ELSE 0 END) /
                      (SUM(CASE WHEN a.date BETWEEN %s AND %s THEN a.count ELSE 0 END) + 1)) DESC,
                     SUM(CASE WHEN a.date BETWEEN %s AND %s THEN a.count ELSE 0 END) DESC
            LIMIT %d";
    $args = array_merge(
      array($cur_from, $cur_to, $prev_from, $prev_to, $prev_from, $cur_to),
      $in['args'],
      array($cur_from, $cur_to, $min_pv,
            $cur_from, $cur_to, $prev_from, $prev_to,
            $cur_from, $cur_to,
            $limit)
    );
    $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);
    foreach ($rows as &$r) {
      $r['post_id'] = (int) $r['post_id'];
      $r['cur_pv']  = (int) $r['cur_pv'];
      $r['prev_pv'] = (int) $r['prev_pv'];
      // PV = 最新期間のPV（ランキングテーブル共通カラム用）
      $r['pv'] = $r['cur_pv'];
      // 増加率（%）。prev=0 の場合は +999%（新登場扱い）
      $r['growth'] = ($r['prev_pv'] > 0)
        ? round((($r['cur_pv'] - $r['prev_pv']) * 100) / $r['prev_pv'], 1)
        : 999;
    }
    return $rows ?: array();
  });
}
endif;

/**
 * 日次PVのマップ（date => pv）を取得。ヒートマップ等で高速に使える。
 */
if ( !function_exists( 'cocoon_analytics_daily_pv_map' ) ):
function cocoon_analytics_daily_pv_map($from, $to){
  return cocoon_analytics_cached(array('daily_pv_map', $from, $to), function() use ($from, $to){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $types = cocoon_analytics_allowed_post_types();
    $in = cocoon_analytics_in_placeholder($types);
    $sql = "SELECT date, COALESCE(SUM(count),0) AS pv FROM `{$table}`
            WHERE date BETWEEN %s AND %s AND post_id > 0 AND post_type IN ({$in['sql']})
            GROUP BY date";
    $args = array_merge(array($from, $to), $in['args']);
    $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);
    $map = array();
    foreach ($rows as $r) { $map[$r['date']] = (int) $r['pv']; }
    return $map;
  });
}
endif;

/**
 * 投稿タイプ別PV
 */
if ( !function_exists( 'cocoon_analytics_pv_by_post_type' ) ):
function cocoon_analytics_pv_by_post_type($from, $to){
  return cocoon_analytics_cached(array('pv_by_post_type', $from, $to), function() use ($from, $to){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    // 初心者向け: 有効な投稿タイプだけに絞る（不正データや attachment 等の混入を防ぐ）
    $types = cocoon_analytics_allowed_post_types();
    $in = cocoon_analytics_in_placeholder($types);
    $sql = "SELECT post_type, COALESCE(SUM(count),0) AS pv FROM `{$table}`
            WHERE date BETWEEN %s AND %s AND post_id > 0 AND post_type IN ({$in['sql']})
            GROUP BY post_type ORDER BY pv DESC";
    $args = array_merge(array($from, $to), $in['args']);
    $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);
    foreach ($rows as &$r) { $r['pv'] = (int) $r['pv']; }
    return $rows ?: array();
  });
}
endif;

/**
 * 人気記事ランキング
 */
if ( !function_exists( 'cocoon_analytics_ranking' ) ):
function cocoon_analytics_ranking($from, $to, $post_types = null, $limit = 10, $offset = 0){
  $limit = max(1, min(500, (int) $limit));
  $offset = max(0, (int) $offset);
  return cocoon_analytics_cached(array('ranking', $from, $to, $post_types, $limit, $offset), function() use ($from, $to, $post_types, $limit, $offset){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $types = cocoon_analytics_allowed_post_types($post_types);
    $in = cocoon_analytics_in_placeholder($types);
    $sql = "SELECT a.post_id, a.post_type, SUM(a.count) AS pv
            FROM `{$table}` a
            INNER JOIN {$wpdb->posts} p ON p.ID = a.post_id AND p.post_status = 'publish'
            WHERE a.date BETWEEN %s AND %s AND a.post_id > 0 AND a.post_type IN ({$in['sql']})
            GROUP BY a.post_id, a.post_type
            ORDER BY pv DESC
            LIMIT %d OFFSET %d";
    $args = array_merge(array($from, $to), $in['args'], array($limit, $offset));
    $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);
    foreach ($rows as &$r) { $r['pv'] = (int) $r['pv']; $r['post_id'] = (int) $r['post_id']; }
    return $rows ?: array();
  });
}
endif;

/**
 * 全記事テーブル（ページング）: 期間内PVと投稿情報を返す。
 */
if ( !function_exists( 'cocoon_analytics_posts_table' ) ):
function cocoon_analytics_posts_table($from, $to, $args = array()){
  $defaults = array(
    'post_types' => null,
    'author' => 0,
    'category' => 0,
    'order' => 'DESC', // DESC or ASC
    'min_pv' => -1,
    'max_pv' => -1,
    'per_page' => 50,
    'page' => 1,
  );
  $args = array_merge($defaults, $args);
  return cocoon_analytics_cached(array('posts_table', $from, $to, $args), function() use ($from, $to, $args){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $types = cocoon_analytics_allowed_post_types($args['post_types']);
    $in = cocoon_analytics_in_placeholder($types);
    $order = (strtoupper($args['order']) === 'ASC') ? 'ASC' : 'DESC';
    $per_page = max(10, min(500, (int) $args['per_page']));
    $page = max(1, (int) $args['page']);
    $offset = ($page - 1) * $per_page;

    // 初心者向け: WHERE / JOIN を動的組み立て。author / category は整数キャストでエスケープ。
    $where_parts = array("p.post_status = 'publish'", "p.post_type IN ({$in['sql']})");
    $p2 = $in['args'];
    if ((int) $args['author'] > 0) {
      $where_parts[] = 'p.post_author = ' . (int) $args['author'];
    }
    $join2 = '';
    if ((int) $args['category'] > 0) {
      $join2 = "INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
                INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                  AND tt.taxonomy = 'category' AND tt.term_id = " . (int) $args['category'];
    }

    $having = '';
    $having_parts = array();
    if ((int) $args['min_pv'] >= 0) $having_parts[] = $wpdb->prepare('pv >= %d', (int) $args['min_pv']);
    if ((int) $args['max_pv'] >= 0) $having_parts[] = $wpdb->prepare('pv <= %d', (int) $args['max_pv']);
    if (!empty($having_parts)) $having = 'HAVING ' . implode(' AND ', $having_parts);

    // 件数取得
    $total_sql = "SELECT COUNT(*) FROM (
      SELECT p.ID, COALESCE(SUM(a.count),0) AS pv
      FROM {$wpdb->posts} p
      {$join2}
      LEFT JOIN `{$table}` a ON a.post_id = p.ID AND a.date BETWEEN %s AND %s
      WHERE " . implode(' AND ', $where_parts) . "
      GROUP BY p.ID
      {$having}
    ) x";
    $total_params = array_merge(array($from, $to), $p2);
    $total = (int) $wpdb->get_var($wpdb->prepare($total_sql, $total_params));

    // 本体
    $main_sql = "SELECT p.ID AS post_id, p.post_title, p.post_type, p.post_date, p.post_author, COALESCE(SUM(a.count),0) AS pv
      FROM {$wpdb->posts} p
      {$join2}
      LEFT JOIN `{$table}` a ON a.post_id = p.ID AND a.date BETWEEN %s AND %s
      WHERE " . implode(' AND ', $where_parts) . "
      GROUP BY p.ID
      {$having}
      ORDER BY pv {$order}, p.post_date DESC
      LIMIT %d OFFSET %d";
    $main_params = array_merge(array($from, $to), $p2, array($per_page, $offset));
    $rows = $wpdb->get_results($wpdb->prepare($main_sql, $main_params), ARRAY_A);
    foreach ($rows as &$r) {
      $r['post_id'] = (int) $r['post_id'];
      $r['pv'] = (int) $r['pv'];
      $r['post_author'] = (int) $r['post_author'];
    }
    return array('rows' => $rows ?: array(), 'total' => $total, 'page' => $page, 'per_page' => $per_page);
  });
}
endif;

/**
 * タクソノミー別集計（category / post_tag 等）
 */
if ( !function_exists( 'cocoon_analytics_pv_by_taxonomy' ) ):
function cocoon_analytics_pv_by_taxonomy($from, $to, $taxonomy = 'category', $limit = 50){
  $taxonomy = in_array($taxonomy, get_taxonomies(array('public' => true)), true) ? $taxonomy : 'category';
  $limit = max(1, min(500, (int) $limit));
  return cocoon_analytics_cached(array('pv_by_tax', $from, $to, $taxonomy, $limit), function() use ($from, $to, $taxonomy, $limit){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $sql = "SELECT t.term_id, t.name, t.slug, SUM(a.count) AS pv
            FROM `{$table}` a
            INNER JOIN {$wpdb->posts} p ON p.ID = a.post_id AND p.post_status = 'publish'
            INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = a.post_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = %s
            INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
            WHERE a.date BETWEEN %s AND %s AND a.post_id > 0
            GROUP BY t.term_id
            ORDER BY pv DESC
            LIMIT %d";
    $rows = $wpdb->get_results($wpdb->prepare($sql, $taxonomy, $from, $to, $limit), ARRAY_A);
    foreach ($rows as &$r) { $r['term_id'] = (int) $r['term_id']; $r['pv'] = (int) $r['pv']; }
    return $rows ?: array();
  });
}
endif;

/**
 * 著者別集計
 */
if ( !function_exists( 'cocoon_analytics_pv_by_author' ) ):
function cocoon_analytics_pv_by_author($from, $to, $limit = 100){
  $limit = max(1, min(500, (int) $limit));
  return cocoon_analytics_cached(array('pv_by_author', $from, $to, $limit), function() use ($from, $to, $limit){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $sql = "SELECT p.post_author, SUM(a.count) AS pv, COUNT(DISTINCT a.post_id) AS posts_with_pv
            FROM `{$table}` a
            INNER JOIN {$wpdb->posts} p ON p.ID = a.post_id AND p.post_status = 'publish'
            WHERE a.date BETWEEN %s AND %s AND a.post_id > 0
            GROUP BY p.post_author
            ORDER BY pv DESC
            LIMIT %d";
    $rows = $wpdb->get_results($wpdb->prepare($sql, $from, $to, $limit), ARRAY_A);
    foreach ($rows as &$r) {
      $r['post_author'] = (int) $r['post_author'];
      $r['pv'] = (int) $r['pv'];
      $r['posts_with_pv'] = (int) $r['posts_with_pv'];
      $r['avg_pv'] = $r['posts_with_pv'] > 0 ? round($r['pv'] / $r['posts_with_pv'], 1) : 0;
    }
    return $rows ?: array();
  });
}
endif;

/**
 * 曜日別PV（DAYOFWEEK: 1=Sunday ... 7=Saturday）
 */
if ( !function_exists( 'cocoon_analytics_pv_by_dow' ) ):
function cocoon_analytics_pv_by_dow($from, $to){
  return cocoon_analytics_cached(array('pv_by_dow', $from, $to), function() use ($from, $to){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $sql = "SELECT DAYOFWEEK(date) AS dow, SUM(count) AS pv
            FROM `{$table}`
            WHERE date BETWEEN %s AND %s AND post_id > 0
            GROUP BY dow";
    $rows = $wpdb->get_results($wpdb->prepare($sql, $from, $to), ARRAY_A);
    $map = array();
    foreach ($rows as $r) { $map[(int) $r['dow']] = (int) $r['pv']; }
    $result = array();
    for ($i = 1; $i <= 7; $i++) {
      $result[] = array('dow' => $i, 'pv' => isset($map[$i]) ? $map[$i] : 0);
    }
    return $result;
  });
}
endif;

/**
 * ライフサイクル: 単一記事の公開後N日目PV
 */
if ( !function_exists( 'cocoon_analytics_lifecycle' ) ):
function cocoon_analytics_lifecycle($post_id){
  $post_id = (int) $post_id;
  if ($post_id <= 0) return array();
  return cocoon_analytics_cached(array('lifecycle', $post_id), function() use ($post_id){
    global $wpdb;
    $table = ACCESSES_TABLE_NAME;
    $sql = "SELECT DATEDIFF(a.date, DATE(p.post_date)) AS day_after, SUM(a.count) AS pv
            FROM `{$table}` a
            INNER JOIN {$wpdb->posts} p ON p.ID = a.post_id
            WHERE a.post_id = %d AND a.date >= DATE(p.post_date)
            GROUP BY day_after
            ORDER BY day_after ASC";
    $rows = $wpdb->get_results($wpdb->prepare($sql, $post_id), ARRAY_A);
    foreach ($rows as &$r) { $r['day_after'] = (int) $r['day_after']; $r['pv'] = (int) $r['pv']; }
    return $rows ?: array();
  });
}
endif;
