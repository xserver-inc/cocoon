<?php //アクセス解析ダッシュボード - タイル配置（ドラッグ&ドロップ）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * ユーザーごとのダッシュボードタイル配置を保存／読み込みするユーティリティ。
 * 保存先: usermeta（キー: cocoon_analytics_dashboard_layout）
 * データ形式:
 *   array(
 *     'columns' => array(
 *       0 => array('daily', 'weekly', ...),
 *       1 => array('monthly', 'category', ...),
 *     ),
 *     'col_count' => 2,
 *   )
 */
if ( !defined( 'ABSPATH' ) ) exit;

define('COCOON_ANALYTICS_LAYOUT_META_KEY', 'cocoon_analytics_dashboard_layout');

/**
 * 既定のタイル一覧と並び（条件に応じて可視性も制御）
 * 各要素: id => bool（true なら表示）
 */
if ( !function_exists( 'cocoon_analytics_default_tile_ids' ) ):
function cocoon_analytics_default_tile_ids($visibility = array()){
  // 初心者向け: 新しいタイルを追加するときはここに id を追加するだけでOK
  $all = array('daily', 'weekly', 'monthly', 'category', 'tag', 'type', 'top', 'trending', 'dow');
  $result = array();
  foreach ($all as $id) {
    if (!isset($visibility[$id]) || $visibility[$id]) {
      $result[] = $id;
    }
  }
  return $result;
}
endif;

/**
 * 既定の2列レイアウトを生成
 */
if ( !function_exists( 'cocoon_analytics_default_layout' ) ):
function cocoon_analytics_default_layout($tile_ids, $col_count = 2){
  $col_count = max(1, min(4, (int) $col_count));
  $columns = array_fill(0, $col_count, array());
  // 初心者向け: ラウンドロビンで均等配置（後でユーザーがドラッグ&ドロップで調整可能）
  foreach (array_values($tile_ids) as $i => $id) {
    $columns[$i % $col_count][] = $id;
  }
  return array('columns' => $columns, 'col_count' => $col_count);
}
endif;

/**
 * 現在ユーザーの保存レイアウトを返す。未保存なら既定。
 * 欠落タイルは末尾列に補完、未知タイルは除外、重複は排除。
 */
if ( !function_exists( 'cocoon_analytics_get_user_layout' ) ):
function cocoon_analytics_get_user_layout($tile_ids, $col_count = 2){
  $user_id = get_current_user_id();
  $saved = $user_id ? get_user_meta($user_id, COCOON_ANALYTICS_LAYOUT_META_KEY, true) : '';
  $layout = null;
  if (is_array($saved) && isset($saved['columns']) && is_array($saved['columns'])) {
    $layout = $saved;
  }
  if (!$layout) {
    return cocoon_analytics_default_layout($tile_ids, $col_count);
  }

  $valid = array_flip($tile_ids); // 有効タイルの検索用
  $seen = array();
  $columns = array();
  foreach ($layout['columns'] as $col) {
    if (!is_array($col)) continue;
    $new_col = array();
    foreach ($col as $id) {
      $id = is_string($id) ? sanitize_key($id) : '';
      if ($id === '' || !isset($valid[$id]) || isset($seen[$id])) continue;
      $new_col[] = $id;
      $seen[$id] = true;
    }
    $columns[] = $new_col;
  }
  // 欠落タイルを最後の列に追加（新機能追加時の後方互換）
  $missing = array();
  foreach ($tile_ids as $id) {
    if (!isset($seen[$id])) $missing[] = $id;
  }
  if ($missing) {
    if (empty($columns)) $columns[] = array();
    $last = count($columns) - 1;
    $columns[$last] = array_merge($columns[$last], $missing);
  }
  // 空列を削除しつつ、最低1列は確保
  $columns = array_values(array_filter($columns, function($c){ return !empty($c); }));
  if (empty($columns)) {
    return cocoon_analytics_default_layout($tile_ids, $col_count);
  }
  return array(
    'columns'   => $columns,
    'col_count' => count($columns),
  );
}
endif;

/**
 * AJAX: レイアウト保存
 */
add_action('wp_ajax_cocoon_analytics_save_layout', 'cocoon_analytics_ajax_save_layout');

if ( !function_exists( 'cocoon_analytics_ajax_save_layout' ) ):
function cocoon_analytics_ajax_save_layout(){
  if (!current_user_can('manage_options')) {
    wp_send_json_error(array('message' => 'forbidden'), 403);
  }
  check_ajax_referer('cocoon_analytics_layout', 'nonce');

  $raw = isset($_POST['columns']) ? $_POST['columns'] : '';
  if (is_string($raw)) {
    $decoded = json_decode(wp_unslash($raw), true);
  } else {
    $decoded = null;
  }
  if (!is_array($decoded)) {
    wp_send_json_error(array('message' => 'invalid_payload'), 400);
  }
  // 既定タイルのホワイトリストで検証
  $all_ids = cocoon_analytics_default_tile_ids();
  $valid = array_flip($all_ids);
  $seen = array();
  $columns = array();
  foreach ($decoded as $col) {
    if (!is_array($col)) continue;
    $new_col = array();
    foreach ($col as $id) {
      $id = is_string($id) ? sanitize_key($id) : '';
      if ($id === '' || !isset($valid[$id]) || isset($seen[$id])) continue;
      $new_col[] = $id;
      $seen[$id] = true;
    }
    $columns[] = $new_col;
  }
  // 列は最大4まで
  if (count($columns) > 4) $columns = array_slice($columns, 0, 4);
  $payload = array('columns' => $columns, 'col_count' => count($columns));
  $user_id = get_current_user_id();
  if ($user_id) {
    update_user_meta($user_id, COCOON_ANALYTICS_LAYOUT_META_KEY, $payload);
  }
  wp_send_json_success(array('saved' => $payload));
}
endif;

/**
 * AJAX: レイアウトを既定に戻す
 */
add_action('wp_ajax_cocoon_analytics_reset_layout', 'cocoon_analytics_ajax_reset_layout');

if ( !function_exists( 'cocoon_analytics_ajax_reset_layout' ) ):
function cocoon_analytics_ajax_reset_layout(){
  if (!current_user_can('manage_options')) {
    wp_send_json_error(array('message' => 'forbidden'), 403);
  }
  check_ajax_referer('cocoon_analytics_layout', 'nonce');
  $user_id = get_current_user_id();
  if ($user_id) {
    delete_user_meta($user_id, COCOON_ANALYTICS_LAYOUT_META_KEY);
  }
  wp_send_json_success();
}
endif;
