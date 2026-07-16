<?php //アクセス解析ダッシュボード - エクスポート
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('admin_post_cocoon_analytics_export', 'cocoon_analytics_handle_export');

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
      foreach (cocoon_analytics_ranking($from, $to, null, 500, 0) as $r) {
        $rows[] = array($r['post_id'], $r['post_type'], get_the_title($r['post_id']), $r['pv'], get_permalink($r['post_id']));
      }
      break;
    case 'posts':
      $headers = array('post_id', 'post_type', 'title', 'author_id', 'post_date', 'pv');
      $page = 1;
      do {
        $res = cocoon_analytics_posts_table($from, $to, array('per_page' => 500, 'page' => $page));
        foreach ($res['rows'] as $r) {
          $rows[] = array($r['post_id'], $r['post_type'], get_the_title($r['post_id']), $r['post_author'], $r['post_date'], $r['pv']);
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

if ( !function_exists( 'cocoon_analytics_output_csv' ) ):
function cocoon_analytics_output_csv($filename, $headers, $rows){
  nocache_headers();
  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '.csv"');
  // UTF-8 BOM（Excel互換）
  echo "\xEF\xBB\xBF";
  $out = fopen('php://output', 'w');
  // PHP8.4以降の仕様変更に伴う非推奨警告を防ぐため、区切り文字、囲み文字、エスケープ文字を明示的に指定します
  fputcsv($out, $headers, ',', '"', '\\');
  foreach ($rows as $r) {
    fputcsv($out, $r, ',', '"', '\\');
  }
  fclose($out);
}
endif;

if ( !function_exists( 'cocoon_analytics_output_json' ) ):
function cocoon_analytics_output_json($filename, $headers, $rows, $meta){
  nocache_headers();
  header('Content-Type: application/json; charset=UTF-8');
  header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '.json"');
  $data = array();
  foreach ($rows as $r) {
    $assoc = array();
    foreach ($headers as $i => $h) {
      $assoc[$h] = isset($r[$i]) ? $r[$i] : null;
    }
    $data[] = $assoc;
  }
  echo wp_json_encode(array(
    'meta' => array_merge($meta, array('generated_at' => current_time('mysql'))),
    'columns' => $headers,
    'data' => $data,
  ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
endif;
