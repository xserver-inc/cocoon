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
add_action('wp_ajax_cocoon_analytics_get_dashboard_widget', 'cocoon_analytics_ajax_get_dashboard_widget');
add_action('wp_ajax_cocoon_analytics_get_dashboard_ranking', 'cocoon_analytics_ajax_get_dashboard_ranking');

/**
 * ダッシュボードウィジェットの人気記事ランキングを表示用に整形して返す
 */
if ( !function_exists( 'cocoon_analytics_dashboard_ranking_items' ) ):
function cocoon_analytics_dashboard_ranking_items($from, $to, $limit = 5){
  $rows = cocoon_analytics_ranking($from, $to, null, $limit);
  // タイトルとパーマリンクの取得で記事ごとにクエリが出ないよう、投稿キャッシュを一括で温める
  // タームも含めるのはパーマリンク構造に %category% がある場合に必要なため
  if (!empty($rows) && function_exists('_prime_post_caches')) {
    _prime_post_caches(wp_list_pluck($rows, 'post_id'), true, false);
  }
  $items = array();
  $rank = 1;
  foreach ($rows as $item) {
    $post_id = $item['post_id'];
    $title = get_the_title($post_id);
    if (empty($title)) {
      $title = __('(タイトルなし)', THEME_NAME);
    }
    $items[] = array(
      'rank'  => $rank,
      'title' => $title,
      'url'   => esc_url_raw(get_permalink($post_id)),
      'pv'    => number_format_i18n($item['pv']),
    );
    $rank++;
  }
  return $items;
}
endif;

/**
 * ダッシュボードウィジェットのリクエスト共通の権限・nonce検証
 */
if ( !function_exists( 'cocoon_analytics_dashboard_widget_guard' ) ):
function cocoon_analytics_dashboard_widget_guard(){
  if (!current_user_can('manage_options')) {
    wp_send_json_error(array('message' => 'forbidden'), 403);
  }
  check_ajax_referer('cocoon_analytics_dashboard_widget', 'nonce');
  // 設定でダッシュボード機能が無効なら集計クエリを一切実行しない
  if (!is_access_count_enable() || !is_access_analytics_enable()) {
    wp_send_json_error(array('message' => 'disabled'), 403);
  }
}
endif;

/**
 * AJAX: ダッシュボードウィジェットのグラフ＋初期ランキングを取得
 *
 * ウィジェットが実際に表示されたときだけ呼ばれるため、
 * 非表示・折りたたみ状態では集計クエリが一切走らない。
 */
if ( !function_exists( 'cocoon_analytics_ajax_get_dashboard_widget' ) ):
function cocoon_analytics_ajax_get_dashboard_widget(){
  cocoon_analytics_dashboard_widget_guard();

  $to = current_time('Y-m-d');

  // 1. 日別: 直近7日間（本日を含む）
  $daily = cocoon_analytics_daily_pv(date('Y-m-d', strtotime($to . ' -6 days')), $to);

  // 2. 週別: 直近7週分（今週を含む）
  $weekly = cocoon_analytics_weekly_pv(date('Y-m-d', strtotime($to . ' -48 days')), $to);
  if (count($weekly) > 7) {
    $weekly = array_slice($weekly, -7);
  }

  // 3. 月別: 直近7ヶ月分（当月を含む）。月末日に-6ヶ月すると翌月へ繰り上がるため、先に月初へ丸める
  $monthly_from = date('Y-m-01', strtotime(date('Y-m-01', strtotime($to)) . ' -6 months'));
  $monthly = cocoon_analytics_monthly_pv($monthly_from, $to);
  if (count($monthly) > 7) {
    $monthly = array_slice($monthly, -7);
  }

  // 4. 年別: 直近7年分（今年を含む）。年の基準はサイトのタイムゾーン
  $yearly = cocoon_analytics_yearly_pv(((int) current_time('Y') - 6) . '-01-01', $to);

  // 5. 人気記事は初期表示期間の1件だけ取得し、期間切り替え時に追加取得する
  $period = cocoon_analytics_widget_period_range(get_access_analytics_default_period());

  wp_send_json_success(array(
    'daily'   => cocoon_analytics_chart_labels($daily, 'daily'),
    'weekly'  => cocoon_analytics_chart_labels($weekly, 'weekly'),
    'monthly' => cocoon_analytics_chart_labels($monthly, 'monthly'),
    'yearly'  => cocoon_analytics_chart_labels($yearly, 'yearly'),
    'period'  => $period['key'],
    'ranking' => cocoon_analytics_dashboard_ranking_items($period['from'], $period['to']),
  ));
}
endif;

/**
 * AJAX: ダッシュボードウィジェットの人気記事ランキングを期間指定で取得
 */
if ( !function_exists( 'cocoon_analytics_ajax_get_dashboard_ranking' ) ):
function cocoon_analytics_ajax_get_dashboard_ranking(){
  cocoon_analytics_dashboard_widget_guard();

  $requested = isset($_GET['period']) ? sanitize_key($_GET['period']) : '';
  $period = cocoon_analytics_widget_period_range($requested);

  wp_send_json_success(array(
    'period'  => $period['key'],
    'ranking' => cocoon_analytics_dashboard_ranking_items($period['from'], $period['to']),
  ));
}
endif;

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

  // リスト側で選択された集計期間を取得します（不正な値は既定の直近30日にフォールバック）
  $list_period = isset($_GET['period']) ? sanitize_key($_GET['period']) : '30days';
  $allowed_periods = array('7days', '30days', '90days', '1year', 'all');
  if (!in_array($list_period, $allowed_periods, true)) {
    $list_period = '30days';
  }

  if ($list_period === '1year') {
    // resolve_periodに「1年」プリセットが無いため、今日を含む直近365日を直接計算します
    $to = current_time('Y-m-d');
    $from = date('Y-m-d', strtotime($to . ' -364 days'));
  } else {
    $period = cocoon_analytics_resolve_period($list_period);
    $from = $period['from'];
    $to = $period['to'];
  }

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
      'title'    => cocoon_analytics_plain_title($row['post_id']) ?: '(' . __('不明', THEME_NAME) . ')',
      //一覧に表示するだけの値なのでWordPressの日付フォーマット設定に従わせる
      'date'     => get_the_date(get_option('date_format'), $row['post_id']),
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
  $title = cocoon_analytics_plain_title($post_id) ?: '(' . __('不明', THEME_NAME) . ')';

  // グラフ描画に必要なデータ（投稿ID、タイトル、公開日、アクセス履歴）をJSON形式で即座に返却します
  wp_send_json_success(array(
    'post_id'   => $post_id,
    'title'     => $title,
    'post_date' => get_the_date('Y-m-d', $post_id),
    'lifecycle' => cocoon_analytics_chart_labels($rows, 'daily'),
  ));
}
endif;
