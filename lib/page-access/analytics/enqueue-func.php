<?php //アクセス解析ダッシュボード - アセット enqueue
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('admin_enqueue_scripts', 'cocoon_analytics_admin_enqueue');
add_action('admin_print_footer_scripts', 'cocoon_analytics_print_data', 5);

if ( !function_exists( 'cocoon_analytics_admin_enqueue' ) ):
function cocoon_analytics_admin_enqueue($hook){
  // アクセス解析ページでのみ読み込む
  if (strpos($hook, 'theme-access') === false) return;

  $base = get_cocoon_template_directory_uri() . '/lib/page-access/analytics/assets';
  $path = get_template_directory() . '/lib/page-access/analytics/assets';

  // Chart.js は CDN（UMD 版）を利用
  wp_enqueue_script(
    'cocoon-analytics-chartjs',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
    array(),
    '4.4.1',
    true
  );

  // SortableJS（ドラッグ&ドロップでタイル並び替え）
  wp_enqueue_script(
    'cocoon-analytics-sortablejs',
    'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js',
    array(),
    '1.15.2',
    true
  );

  wp_enqueue_style(
    'cocoon-analytics-style',
    $base . '/analytics.css',
    array(),
    file_exists($path . '/analytics.css') ? filemtime($path . '/analytics.css') : false
  );

  wp_enqueue_script(
    'cocoon-analytics-js',
    $base . '/analytics.js',
    array('cocoon-analytics-chartjs', 'cocoon-analytics-sortablejs'),
    file_exists($path . '/analytics.js') ? filemtime($path . '/analytics.js') : false,
    true
  );
}
endif;

/**
 * チャートデータをフッタでインライン出力する。
 * 初心者向け: admin_enqueue_scripts はページ描画より先に走るため、
 * ページ内で $GLOBALS にセットされたデータをフッタで注入する。
 */
if ( !function_exists( 'cocoon_analytics_print_data' ) ):
function cocoon_analytics_print_data(){
  // アクセス解析ページでのみ出力
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || strpos($screen->id, 'theme-access') === false) return;

  $chart_data = isset($GLOBALS['cocoon_analytics_chart_data']) ? $GLOBALS['cocoon_analytics_chart_data'] : array();

  $weekdays = array(
    __('日', THEME_NAME),
    __('月', THEME_NAME),
    __('火', THEME_NAME),
    __('水', THEME_NAME),
    __('木', THEME_NAME),
    __('金', THEME_NAME),
    __('土', THEME_NAME),
  );

  // サジェスト用の著者一覧を取得します（IDと表示名）
  $authors = array();
  $users = get_users(array('fields' => array('ID', 'display_name')));
  foreach ($users as $u) {
    $authors[] = array('id' => (int)$u->ID, 'name' => $u->display_name);
  }

  // サジェスト用のカテゴリー一覧を取得します（IDと名前）
  $categories = array();
  $cats = get_categories(array('hide_empty' => false));
  foreach ($cats as $c) {
    $categories[] = array('id' => (int)$c->term_id, 'name' => $c->name);
  }

  $payload = array(
    'data'     => $chart_data,
    'weekdays' => $weekdays,
    'suggest'  => array(
      'authors'    => $authors,
      'categories' => $categories,
    ),
    'ajax'     => array(
      'url'   => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('cocoon_analytics_layout'),
      // ライフサイクルデータの非同期取得で使うセキュリティトークン（nonce）を発行します
      'lifecycle_nonce' => wp_create_nonce('cocoon_analytics_lifecycle'),
    ),
    'i18n'     => array(
      'pv'           => __('PV', THEME_NAME),
      'date'         => __('日付', THEME_NAME),
      'dayAfter'     => __('公開後日数', THEME_NAME),
      'days'         => __('日', THEME_NAME),
      'days_count'   => __('日数', THEME_NAME),
      'partial_week' => __('部分週', THEME_NAME),
      'saved'        => __('レイアウトを保存しました', THEME_NAME),
      'save_failed'  => __('レイアウトの保存に失敗しました', THEME_NAME),
      'reset_confirm'=> __('タイル配置を既定に戻しますか？', THEME_NAME),
      // ライフサイクルで選択期間内にデータが無いときに表示するメッセージです
      'no_period_data' => __('選択した期間のアクセスデータがありません。', THEME_NAME),
      // ライフサイクルの期間サマリーとズームバッジに使う文言です
      'shown_period_total' => __('表示期間の合計', THEME_NAME),
      'list_period_total'  => __('リスト期間の合計', THEME_NAME),
      'all_total'          => __('累計', THEME_NAME),
      'zoom_notice'        => __('グラフはリストと異なる期間を表示中', THEME_NAME),
    ),
  );

  echo '<script id="cocoon-analytics-data">window.CocoonAnalytics = ' . wp_json_encode($payload) . ';</script>' . "\n";
}
endif;
