<?php //クリックマップの記事選択
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_cocoon_analytics_map_post_candidates', 'cocoon_analytics_ajax_map_post_candidates');

if ( !function_exists( 'cocoon_analytics_map_post_period' ) ):
function cocoon_analytics_map_post_period($preset, $from = '', $to = ''){
  $period = cocoon_analytics_resolve_period($preset, $from, $to);
  // クリックマップ本体と同じクリック計測開始日に基づく全期間の解決
  if ($preset === 'all' && function_exists('cocoon_click_tables_exist') && cocoon_click_tables_exist()) {
    $click_from = cocoon_click_analytics_min_date();
    if ($click_from) $period['from'] = $click_from;
  }
  return $period;
}
endif;

/**
 * 人気記事または検索結果のクリックマップ候補への整形
 */
if ( !function_exists( 'cocoon_analytics_map_post_candidates' ) ):
function cocoon_analytics_map_post_candidates($keyword, $from, $to, $device = 'desktop'){
  $items = array();
  if ($keyword !== '') {
    $items = cocoon_analytics_search_posts($keyword);
  } elseif (is_accesses_table_exist()) {
    $rows = cocoon_analytics_ranking($from, $to, array('post', 'page'), 10);
    // 候補の記事情報をまとめて取得するためのキャッシュ準備
    _prime_post_caches(wp_list_pluck($rows, 'post_id'), false, false);
    foreach ($rows as $row) {
      $post = get_post($row['post_id']);
      if (!$post || $post->post_status !== 'publish' || !in_array($post->post_type, array('post', 'page'), true) || $row['pv'] <= 0) continue;
      $item = cocoon_analytics_post_picker_item($post);
      $item['rank'] = count($items) + 1;
      $item['pv'] = number_format_i18n($row['pv']);
      $items[] = $item;
    }
  }

  // 全候補のクリック記録を1回の集計で取得するための投稿ID限定
  if ($items && function_exists('cocoon_click_tables_exist') && cocoon_click_tables_exist()) {
    global $wpdb;
    $ids = array_map('intval', wp_list_pluck($items, 'id'));
    $device = in_array($device, array('desktop', 'tablet', 'mobile'), true) ? $device : 'desktop';
    $with_clicks = cocoon_click_analytics_cached(array('map_post_candidates', $from, $to, $ids, $device), function() use ($wpdb, $from, $to, $ids, $device){
      $source = cocoon_click_stats_source_sql($from, $to);
      $table = $source['direct_sql'] ? $source['direct_sql'] : $source['sql'];
      $where = $source['direct_where'] ? $source['direct_where'] . ' AND ' : '';
      $placeholders = implode(',', array_fill(0, count($ids), '%d'));
      $sql = "SELECT DISTINCT s.source_post_id FROM {$table} s WHERE {$where}s.source_post_id IN ({$placeholders}) AND s.device=%s AND s.link_id>0 AND s.clicks>0";
      return array_map('intval', $wpdb->get_col($wpdb->prepare($sql, array_merge($source['args'], $ids, array($device)))));
    });
    foreach ($items as &$item) {
      $item['has_clicks'] = in_array($item['id'], $with_clicks, true);
    }
    unset($item);
  }
  return $items;
}
endif;

/**
 * 検証済みの期間と検索語による管理者向け記事候補の取得
 */
if ( !function_exists( 'cocoon_analytics_ajax_map_post_candidates' ) ):
function cocoon_analytics_ajax_map_post_candidates(){
  if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'forbidden'), 403);
  check_ajax_referer('cocoon_analytics_post_picker', 'nonce');
  // 配列型の入力による警告を防ぐための文字列限定
  $read = static function($key, $default = ''){
    return isset($_GET[$key]) && is_string($_GET[$key]) ? sanitize_text_field(wp_unslash($_GET[$key])) : $default;
  };
  $preset = $read('period', '30days');
  if (!in_array($preset, array('today', '7days', '30days', '90days', 'thismonth', 'all', 'custom'), true)) $preset = '30days';
  $from = $read('from');
  $to = $read('to');
  if ($preset === 'custom' && ($from === '' || $to === '' || cocoon_analytics_sanitize_date($from, '') !== $from || cocoon_analytics_sanitize_date($to, '') !== $to)) {
    wp_send_json_error(array('message' => 'invalid_period'), 400);
  }
  $period = cocoon_analytics_map_post_period($preset, $from, $to);
  $keyword = trim($read('q'));
  $device = $read('device', 'desktop');
  $devices = array('desktop' => __('PC', THEME_NAME), 'tablet' => __('タブレット', THEME_NAME), 'mobile' => __('モバイル', THEME_NAME));
  if (!isset($devices[$device])) $device = 'desktop';
  wp_send_json_success(array(
    'items' => cocoon_analytics_map_post_candidates($keyword, $period['from'], $period['to'], $device),
    'from' => $period['from'],
    'to' => $period['to'],
    // translators: %s: クリックマップの表示端末名
    'no_clicks' => sprintf(__('%sのクリック記録なし', THEME_NAME), $devices[$device]),
  ));
}
endif;

/**
 * 検索を必須としない人気記事選択パネルの出力
 */
if ( !function_exists( 'cocoon_analytics_render_map_post_picker' ) ):
function cocoon_analytics_render_map_post_picker($selected_post_id){
  $post = $selected_post_id > 0 ? get_post($selected_post_id) : null;
  $title = $post ? cocoon_analytics_plain_title($post) : '';
  if ($post && $title === '') $title = __('（無題）', THEME_NAME);
  $placeholder = __('アクセスの多い記事から選ぶ', THEME_NAME);
  ?>
  <div class="cocoon-analytics-post-picker-field">
    <label id="cocoon-map-picker-label" for="cocoon-map-picker-trigger"><?php esc_html_e('クリック元記事:', THEME_NAME); ?></label>
    <div class="cocoon-map-picker" data-placeholder="<?php echo esc_attr($placeholder); ?>">
      <button type="button" id="cocoon-map-picker-trigger" class="cocoon-map-picker-trigger" aria-haspopup="dialog" aria-expanded="false" aria-controls="cocoon-map-picker-panel" aria-labelledby="cocoon-map-picker-label cocoon-map-picker-value">
        <span id="cocoon-map-picker-value" class="cocoon-map-picker-value"><?php echo esc_html($title !== '' ? $title : $placeholder); ?></span><span aria-hidden="true">▾</span>
      </button>
      <input type="hidden" name="source_post_id" value="<?php echo $post ? (int) $post->ID : 0; ?>">
      <button type="button" class="cocoon-map-picker-clear" aria-label="<?php echo esc_attr__('記事の選択を解除', THEME_NAME); ?>" <?php echo $post ? '' : 'hidden'; ?>>×</button>
      <div id="cocoon-map-picker-panel" class="cocoon-map-picker-panel" role="dialog" aria-labelledby="cocoon-map-picker-label" tabindex="-1" hidden>
        <div class="cocoon-map-picker-heading"><strong class="cocoon-map-picker-heading-text"><?php esc_html_e('人気記事トップ10', THEME_NAME); ?></strong><button type="button" class="cocoon-map-picker-close" aria-label="<?php echo esc_attr__('閉じる', THEME_NAME); ?>">×</button></div>
        <label for="cocoon-map-picker-search" class="screen-reader-text"><?php esc_html_e('ほかの記事を検索', THEME_NAME); ?></label>
        <input type="search" id="cocoon-map-picker-search" placeholder="<?php echo esc_attr__('ほかの記事を検索', THEME_NAME); ?>" autocomplete="off">
        <p class="cocoon-map-picker-context"></p>
        <ol class="cocoon-map-picker-list" aria-label="<?php echo esc_attr__('記事候補', THEME_NAME); ?>"></ol>
        <p class="cocoon-map-picker-status" role="status" aria-live="polite"></p>
        <button type="button" class="button cocoon-map-picker-retry" hidden><?php esc_html_e('再試行', THEME_NAME); ?></button>
      </div>
    </div>
  </div>
  <?php
}
endif;
