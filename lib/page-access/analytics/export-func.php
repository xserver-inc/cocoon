<?php //アクセス解析ダッシュボード - エクスポート
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/export-stream-func.php';

add_action('admin_post_cocoon_analytics_export', 'cocoon_analytics_handle_export');

/**
 * 指定されたDBエラーだけを識別し、通常の読み取り・保存エラーと区別する。
 * PDOではSQLSTATEと分離されたドライバーコード、標準wpdbでは直前のDBエラーコードを確認する。
 */
if (!function_exists('cocoon_analytics_export_database_error_matches')):
function cocoon_analytics_export_database_error_matches($database, array $codes, $error = null){
  if ($error !== null) {
    // PDOのSQLSTATEをDBエラー番号と取り違えない、ドライバー固有コードの取得
    $code = $error instanceof PDOException ? ($error->errorInfo[1] ?? null) : $error->getCode();
    if (!is_int($code) && (!is_string($code) || !preg_match('/^[0-9]+$/D', $code))) return false;
    return in_array((int) $code, $codes, true);
  }
  if ($database instanceof wpdb) {
    try {
      // エラー保存件数0の接続でも有効な、標準ドライバーの直前のエラー番号の参照
      $connection = $database->dbh;
      if ($connection instanceof mysqli && mysqli_errno($connection) !== 0) return in_array(mysqli_errno($connection), $codes, true);
    } catch (Throwable $ignored) {
      // 接続情報を公開しない独自ドライバー向けの、通常SQLによる診断への切り替え
    }
  }
  try {
    if ($database->query('SHOW ERRORS LIMIT 1') === false) return false;
    return in_array((int) $database->get_var(null, 1), $codes, true);
  } catch (RuntimeException $ignored) {
    return false;
  }
}
endif;

/**
 * 一時表の作成権限・GTID制約の拒否を、通常のDB失敗と区別する。
 */
if (!function_exists('cocoon_analytics_export_temporary_table_unavailable')):
function cocoon_analytics_export_temporary_table_unavailable($database, $error = null){
  return cocoon_analytics_export_database_error_matches($database, array(1044, 1142, 1227, 1787, 3748), $error);
}
endif;

/**
 * 作成権限・GTID制約の拒否を通知し、その他のDB失敗は通常の失敗として返す。
 */
if (!function_exists('cocoon_analytics_export_create_temporary_table')):
function cocoon_analytics_export_create_temporary_table($database, $sql, &$temporary_unavailable){
  try {
    if ($database->query($sql) !== false) return true;
  } catch (RuntimeException $error) {
    $temporary_unavailable = cocoon_analytics_export_temporary_table_unavailable($database, $error);
    return false;
  }
  $temporary_unavailable = cocoon_analytics_export_temporary_table_unavailable($database);
  return false;
}
endif;

/**
 * 集計と公開情報を同じ通常SELECTで固定し、最大100記事分ずつ順番に返す。
 * DBドライバーの問い合わせと外側トランザクションを維持し、投稿本文は取得しない。
 * DB失敗は空のランキングと区別し、呼び出し側へ例外で通知する。
 */
if (!function_exists('cocoon_analytics_export_record_batches')):
function cocoon_analytics_export_record_batches($database, $from, $to){
  $types = cocoon_analytics_in_placeholder(cocoon_analytics_allowed_post_types(null));
  $select = 'SELECT totals.post_id, p.post_type, p.post_date, totals.pv
     FROM (SELECT post_id, SUM(count) AS pv FROM `' . ACCESSES_TABLE_NAME . '`';
  $conditions = ' WHERE post_id > 0 AND date BETWEEN %s AND %s GROUP BY post_id) AS totals
     INNER JOIN ' . $database->posts . " AS p ON p.ID = totals.post_id
     WHERE p.post_status = 'publish' AND p.post_type IN ({$types['sql']})
     ORDER BY totals.post_id ASC LIMIT 18446744073709551615";
  $args = array_merge(array($from, $to), $types['args']);
  $records = null;
  $failure = null;
  try {
    // バッチ間の更新を混在させない、日付のカバリング索引を優先した単一SELECT
    $records = $database->get_results($database->prepare($select . ' FORCE INDEX (`' . INDEX_ACCESSES_DATE_PTYPE_PID_COUNT . '`)' . $conditions, $args), OBJECT);
  } catch (RuntimeException $error) {
    $failure = $error;
  }
  if ($failure !== null || $database->last_error !== '' || !is_array($records)) {
    // 欠落・不可視の索引エラーだけを対象にした、索引指定なしの単一SELECTへの切り替え
    if (!cocoon_analytics_export_database_error_matches($database, array(1176), $failure)) {
      if ($failure !== null) throw $failure;
      throw new RuntimeException(__('データの取得に失敗しました。', THEME_NAME));
    }
    $records = $database->get_results($database->prepare($select . $conditions, $args), OBJECT);
  }
  if ($database->last_error !== '' || !is_array($records)) throw new RuntimeException(__('データの取得に失敗しました。', THEME_NAME));
  $batch = array();
  foreach ($records as $record) {
    // wpdb内の結果オブジェクトを重複保持しない、返却バッチだけの連想配列への変換
    $record = (array) $record;
    $record['post_id'] = (int) $record['post_id'];
    $batch[] = $record;
    if (count($batch) === 100) {
      yield $batch;
      $batch = array();
    }
  }
  if ($batch) yield $batch;
}
endif;

/**
 * 元のアクセス記録を単一の通常SELECTで取得し、書き込みを待たせず順位を固定する。
 * 元のDB接続・分離レベル・呼び出し元のトランザクションは変更しない。
 * 戻り値の真偽契約を維持し、任意の第5引数へ一時表の作成制約だけを通知する。
 */
if (!function_exists('cocoon_analytics_create_export_snapshot')):
function cocoon_analytics_create_export_snapshot($database, $snapshot, $from, $to, &$temporary_unavailable = false){
  $temporary_unavailable = false;
  $stage = $snapshot . '_source';
  $stage_created = false;
  $created = false;
  $stage_released = false;
  $suppressed = $database->suppress_errors(true);
  try {
    if (!cocoon_analytics_export_create_temporary_table($database, 'CREATE TEMPORARY TABLE `' . $stage . '` (post_id BIGINT NOT NULL PRIMARY KEY, post_type VARCHAR(126) NOT NULL, post_date VARCHAR(19) NOT NULL, pv DECIMAL(65,0) DEFAULT NULL)', $temporary_unavailable)) return false;
    $stage_created = true;
    foreach (cocoon_analytics_export_record_batches($database, $from, $to) as $posts) {
      if ($posts) {
        // 大きなPV値とNULLを保持する、最大100記事分の一括保存
        $values = array();
        $args = array();
        foreach ($posts as $post) {
          $pv = $post['pv'];
          $values[] = $pv === null ? '(%d,%s,%s,NULL)' : '(%d,%s,%s,%s)';
          $args = array_merge($args, array($post['post_id'], $post['post_type'], $post['post_date']));
          if ($pv !== null) $args[] = $pv;
        }
        if ($database->query($database->prepare('INSERT INTO `' . $stage . '` (post_id,post_type,post_date,pv) VALUES ' . implode(',', $values), $args)) === false) return false;
      }
    }
    // 接続専用の一時データだけを参照した、順位と集計値の固定
    $created = cocoon_analytics_export_create_temporary_table($database, 'CREATE TEMPORARY TABLE `' . $snapshot . '` (export_position BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY) AS SELECT post_id, post_type, pv FROM `' . $stage . '` ORDER BY pv DESC, post_date DESC, post_id DESC', $temporary_unavailable);
  } catch (RuntimeException $error) {
    return false;
  } finally {
    // 途中失敗・例外を含む作業用テーブルの解放と、エラー表示設定の復元
    try {
      $stage_released = !$stage_created || $database->query('DROP TEMPORARY TABLE IF EXISTS `' . $stage . '`') !== false;
    } finally {
      try {
        if ($stage_created && (!$created || !$stage_released)) {
          $database->query('DROP TEMPORARY TABLE IF EXISTS `' . $snapshot . '`');
          $created = false;
        }
        if (!$stage_released) $temporary_unavailable = false;
      } finally {
        $database->suppress_errors($suppressed);
      }
    }
  }
  return $created;
}
endif;

/**
 * 旧関数と同じ引数・型を保ち、投稿本文やターム検索のキャッシュを増やさず行を作成する。
 */
if (!function_exists('cocoon_analytics_export_ranking_row')):
function cocoon_analytics_export_ranking_row($record){
  global $wpdb;
  $cache_suspended = wp_suspend_cache_addition();
  wp_suspend_cache_addition(true);
  $uncached_terms = static function($args){
    $args['cache_results'] = false;
    $args['update_term_meta_cache'] = false;
    return $args;
  };
  // SQL中の文字列や既存LIMITに影響しない、カテゴリー検索の空の上限句だけへの補完
  $unlimited_term_queries = static function($clauses){
    if (empty(trim($clauses['limits'] ?? ''))) $clauses['limits'] = 'LIMIT 18446744073709551615';
    return $clauses;
  };
  // 親・既定カテゴリーにも対応する、WordPress標準の数値ID検索だけの補完
  $term_lookup = '/^\s*SELECT\s+t\.\*,\s*tt\.\*\s+FROM\s+`?' . preg_quote($wpdb->terms, '/')
    . '`?\s+AS\s+t\s+INNER\s+JOIN\s+`?' . preg_quote($wpdb->term_taxonomy, '/')
    . '`?\s+AS\s+tt\s+ON\s+t\.term_id\s*=\s*tt\.term_id\s+WHERE\s+t\.term_id\s*(?:=\s*[0-9]+|IN\s*\([0-9,\s]+\))\s*;?\s*$/i';
  $unlimited_term_lookup = static function($query) use ($term_lookup){
    if (!preg_match($term_lookup, $query)) return $query;
    return rtrim($query, " \t\n\r\0\x0B;") . ' LIMIT 18446744073709551615';
  };
  add_filter('get_terms_args', $uncached_terms, PHP_INT_MAX);
  add_filter('terms_clauses', $unlimited_term_queries, PHP_INT_MAX);
  add_filter('query', $unlimited_term_lookup, PHP_INT_MAX);
  try {
    $title = cocoon_analytics_plain_title($record['post_id']);
    return array($record['post_id'], $record['post_type'], $title, $record['pv'], get_permalink($record['post_id']));
  } finally {
    // 反復の途中終了やタイトル処理の例外にも影響を残さない、設定の復元
    remove_filter('query', $unlimited_term_lookup, PHP_INT_MAX);
    remove_filter('terms_clauses', $unlimited_term_queries, PHP_INT_MAX);
    remove_filter('get_terms_args', $uncached_terms, PHP_INT_MAX);
    wp_suspend_cache_addition($cache_suspended);
  }
}
endif;

/**
 * 標準実装は最小限の集計情報を全件固定し、記事本文を保持せず最大100件ずつ順番に返す。
 * ランキング本体かキャッシュ関数の上書きがあれば、従来の500件指定・1回呼び出しの契約を保つ。
 */
if (!function_exists('cocoon_analytics_export_ranking_rows')):
function cocoon_analytics_export_ranking_rows($from, $to){
  if (!defined('COCOON_ANALYTICS_DEFAULT_RANKING') || !defined('COCOON_ANALYTICS_DEFAULT_CACHED')) {
    // 独自の除外・補正・順序を持つ旧関数への、従来と同じ500件指定での1回の問い合わせ
    $records = cocoon_analytics_ranking($from, $to, null, 500, 0);
    if (!is_iterable($records)) return new WP_Error('cocoon_analytics_export_ranking', __('データの取得に失敗しました。', THEME_NAME));
    return (static function() use ($records){
      foreach ($records as $record) yield cocoon_analytics_export_ranking_row($record);
    })();
  }
  global $wpdb;
  $database = $wpdb;
  // GTID制約のある外側トランザクションにも対応した、一時ファイルへの順位固定
  if (!cocoon_analytics_database_is_autocommit($database)) return cocoon_analytics_export_stream_ranking_rows($database, $from, $to);
  $snapshot = 'cocoon_pv_export_' . md5(uniqid('', true));
  $temporary_unavailable = false;
  if (!cocoon_analytics_create_export_snapshot($database, $snapshot, $from, $to, $temporary_unavailable)) {
    // 一時表を作成できない接続に限定した、通常SELECTと一時ファイルによる全件書き出し
    if ($temporary_unavailable) return cocoon_analytics_export_stream_ranking_rows($database, $from, $to);
    return new WP_Error('cocoon_analytics_export_snapshot', __('データの取得に失敗しました。', THEME_NAME));
  }

  // 未開始・途中終了・例外のすべてで一時テーブルを解放するための所有オブジェクト
  $owner = new class($database, $snapshot) {
    private $database;
    private $table;
    private $released = false;
    public function __construct($database, $table){
      $this->database = $database;
      $this->table = $table;
    }
    public function release(){
      if ($this->released) return;
      $this->released = true;
      $this->database->query('DROP TEMPORARY TABLE IF EXISTS `' . $this->table . '`');
    }
    public function __destruct(){
      $this->release();
    }
  };
  return (static function() use ($database, $snapshot, $owner){
    $position = 0;
    try {
      do {
        $batch = $database->get_results($database->prepare('SELECT * FROM `' . $snapshot . '` WHERE export_position > %d ORDER BY export_position ASC LIMIT 100', $position), ARRAY_A);
        if ($database->last_error !== '') throw new RuntimeException(__('データの取得に失敗しました。', THEME_NAME));
        foreach ($batch as $r) {
          // 既存の標準ランキングと同じ整数型への変換と、キャッシュを蓄積しない行の作成
          $r['post_id'] = (int) $r['post_id'];
          $r['pv'] = (int) $r['pv'];
          $row = cocoon_analytics_export_ranking_row($r);
          $position = (int) $r['export_position'];
          // 途中で反復を終了した場合にも影響を残さない、設定復元後の行の返却
          yield $row;
        }
      } while (count($batch) === 100);
    } finally {
      $owner->release();
    }
  })();
}
endif;

if ( !function_exists( 'cocoon_analytics_handle_export' ) ):
function cocoon_analytics_handle_export(){
  if (!current_user_can('manage_options')) {
    wp_die(__('このページにアクセスする管理者権限がありません。', THEME_NAME));
  }
  check_admin_referer('cocoon_analytics_export');
  if (!is_access_analytics_export_enable()) {
    wp_die(__('エクスポート機能が無効です。', THEME_NAME));
  }

  $target = isset($_REQUEST['target']) ? sanitize_key($_REQUEST['target']) : 'daily';
  $format = isset($_REQUEST['format']) ? sanitize_key($_REQUEST['format']) : 'csv';
  $from = isset($_REQUEST['from']) ? sanitize_text_field($_REQUEST['from']) : '';
  $to = isset($_REQUEST['to']) ? sanitize_text_field($_REQUEST['to']) : '';
  $period = cocoon_analytics_resolve_period('custom', $from, $to);
  $from = $period['from'];
  $to = $period['to'];

  // データ取得
  $headers = array();
  $rows = array();
  switch ($target) {
    case 'daily':
      $headers = array('date', 'pv');
      foreach (cocoon_analytics_daily_pv($from, $to) as $r) {
        $rows[] = array($r['date'], $r['pv']);
      }
      break;
    case 'ranking':
      $headers = array('post_id', 'post_type', 'title', 'pv', 'url');
      $rows = cocoon_analytics_export_ranking_rows($from, $to);
      if (is_wp_error($rows)) wp_die($rows->get_error_message(), '', array('response' => 500));
      break;
    case 'posts':
      $headers = array('post_id', 'post_type', 'title', 'author_id', 'post_date', 'pv');
      $page = 1;
      do {
        $res = cocoon_analytics_posts_table($from, $to, array('per_page' => 500, 'page' => $page));
        foreach ($res['rows'] as $r) {
          // HTMLエンティティを含まないプレーンテキストでのタイトル出力
          $rows[] = array($r['post_id'], $r['post_type'], cocoon_analytics_plain_title($r['post_id']), $r['post_author'], $r['post_date'], $r['pv']);
        }
        $page++;
        if ($page > ceil($res['total'] / $res['per_page'])) break;
      } while (!empty($res['rows']) && $page < 200);
      break;
    case 'authors':
      $headers = array('author_id', 'display_name', 'posts_with_pv', 'total_pv', 'avg_pv');
      foreach (cocoon_analytics_pv_by_author($from, $to, 500) as $r) {
        $u = get_userdata($r['post_author']);
        $rows[] = array($r['post_author'], $u ? $u->display_name : '', $r['posts_with_pv'], $r['pv'], $r['avg_pv']);
      }
      break;
    case 'click_internal':
    case 'click_external':
    case 'click_domains':
    case 'click_daily':
    case 'click_positions':
      $dataset = cocoon_click_export_dataset($target, $from, $to);
      $headers = $dataset['headers'];
      $rows = $dataset['rows'];
      break;
    default:
      wp_die(__('対象が不正です。', THEME_NAME));
  }

  $filename = 'cocoon-analytics-' . $target . '-' . $from . '_' . $to;
  if ($format === 'json') {
    cocoon_analytics_output_json($filename, $headers, $rows, compact('target', 'from', 'to'));
  } else {
    cocoon_analytics_output_csv($filename, $headers, $rows);
  }
  exit;
}
endif;

if ( !function_exists( 'cocoon_analytics_csv_safe_cell' ) ):
function cocoon_analytics_csv_safe_cell($value){
  if (!is_string($value)) return $value;
  // 表計算ソフトでの数式実行を防ぐ、危険な先頭記号の無効化
  return preg_match('/^[\t\r\n ]*[=+\-@]/u', $value) || preg_match('/^[\t\r\n]/u', $value) ? "'" . $value : $value;
}
endif;

if ( !function_exists( 'cocoon_analytics_output_csv' ) ):
function cocoon_analytics_output_csv($filename, $headers, $rows){
  nocache_headers();
  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '.csv"');
  // UTF-8 BOM（Excel互換）
  echo "\xEF\xBB\xBF";
  $out = fopen('php://output', 'w');
  // PHP8.4以降の非推奨警告を防ぐための、区切り文字・囲み文字・エスケープ文字の明示
  fputcsv($out, $headers, ',', '"', '\\');
  foreach ($rows as $r) {
    fputcsv($out, array_map('cocoon_analytics_csv_safe_cell', $r), ',', '"', '\\');
  }
  fclose($out);
}
endif;

if ( !function_exists( 'cocoon_analytics_output_json' ) ):
function cocoon_analytics_output_json($filename, $headers, $rows, $meta){
  nocache_headers();
  header('Content-Type: application/json; charset=UTF-8');
  header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '.json"');
  // 全行の配列やJSON文字列を保持しない、ヘッダーと各行の逐次出力
  $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
  $meta = array_merge($meta, array('generated_at' => current_time('mysql')));
  echo "{\n    \"meta\": " . str_replace("\n", "\n    ", wp_json_encode($meta, $flags));
  echo ",\n    \"columns\": " . str_replace("\n", "\n    ", wp_json_encode($headers, $flags));
  echo ",\n    \"data\": [";
  $first = true;
  foreach ($rows as $r) {
    $assoc = array();
    foreach ($headers as $i => $h) {
      $assoc[$h] = isset($r[$i]) ? $r[$i] : null;
    }
    echo ($first ? "\n" : ",\n") . '        ' . str_replace("\n", "\n        ", wp_json_encode($assoc, $flags));
    $first = false;
  }
  echo ($first ? '' : "\n    ") . "]\n}";
}
endif;
