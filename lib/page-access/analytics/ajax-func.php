<?php //アクセス解析ダッシュボード - 非同期通信（AJAX）処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// ログインユーザー向けのAjaxアクションを登録します
add_action('wp_ajax_cocoon_analytics_get_posts', 'cocoon_analytics_ajax_get_posts');
add_action('wp_ajax_cocoon_analytics_get_lifecycle', 'cocoon_analytics_ajax_get_lifecycle');

/**
 * AJAX: 記事一覧をPV順で取得（検索キーワード対応、ページング対応）
 */
if ( !function_exists( 'cocoon_analytics_ajax_get_posts' ) ):
function cocoon_analytics_ajax_get_posts(){
  // 管理権限がないユーザーからのアクセスを拒否します
  if (!current_user_can('manage_options')) {
    wp_send_json_error(array('message' => 'forbidden'), 403);
  }
  // 送信されたセキュリティトークン（nonce）が正しいかチェックします
  check_ajax_referer('cocoon_analytics_lifecycle', 'nonce');

  // リクエストから検索キーワードと現在のページ番号を取得します
  $s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $per_page = 20;

  // 集計対象の期間を直近30日間に決定します
  $period = cocoon_analytics_resolve_period('30days');
  $from = $period['from'];
  $to = $period['to'];

  // 記事一覧を取得するためのクエリ引数を組み立てます
  $args = array(
    's'        => $s,
    'per_page' => $per_page,
    'page'     => $page,
  );

  // データベースから条件に合う記事のデータを取得します
  $data = cocoon_analytics_posts_table($from, $to, $args);

  $posts = array();
  // クライアント側（JS）で扱いやすいようにデータを整形します
  foreach ($data['rows'] as $row) {
    $posts[] = array(
      'post_id'  => $row['post_id'],
      'title'    => get_the_title($row['post_id']) ?: '(' . __('不明', THEME_NAME) . ')',
      'date'     => get_the_date('Y-m-d', $row['post_id']),
      'pv'       => number_format_i18n($row['pv']),
      'raw_pv'   => $row['pv'],
    );
  }

  // 次のページがあるか判定します
  $has_more = ($page * $per_page) < $data['total'];

  // 整形したデータをJSON形式で即座に返却します
  wp_send_json_success(array(
    'posts'    => $posts,
    'has_more' => $has_more,
    'total'    => $data['total']
  ));
}
endif;

/**
 * AJAX: 特定の記事のライフサイクル（経過日数ごとのPV推移）データを取得
 */
if ( !function_exists( 'cocoon_analytics_ajax_get_lifecycle' ) ):
function cocoon_analytics_ajax_get_lifecycle(){
  // 管理権限がないユーザーからのアクセスを拒否します
  if (!current_user_can('manage_options')) {
    wp_send_json_error(array('message' => 'forbidden'), 403);
  }
  // 送信されたセキュリティトークン（nonce）が正しいかチェックします
  check_ajax_referer('cocoon_analytics_lifecycle', 'nonce');

  // リクエストから投稿IDを取得し、不正な値でないか確認します
  $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
  if ($post_id <= 0) {
    wp_send_json_error(array('message' => 'invalid_post_id'), 400);
  }

  // 記事公開からの経過日数ごとのPV数をデータベースから取得します
  $rows = cocoon_analytics_lifecycle($post_id);
  $title = get_the_title($post_id) ?: '(' . __('不明', THEME_NAME) . ')';

  // グラフ描画に必要なデータ（投稿ID、タイトル、公開日、アクセス履歴）をJSON形式で即座に返却します
  wp_send_json_success(array(
    'post_id'   => $post_id,
    'title'     => $title,
    'post_date' => get_the_date('Y-m-d', $post_id),
    'lifecycle' => $rows,
  ));
}
endif;
