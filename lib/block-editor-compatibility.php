<?php
/**
 * ブロックエディターのコンテンツ用スタイルとREST事前取得の互換処理
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('after_setup_theme', 'cocoon_setup_block_editor_asset_hooks');
if ( !function_exists('cocoon_setup_block_editor_asset_hooks') ):
function cocoon_setup_block_editor_asset_hooks() {
  if (!is_admin() || !function_exists('wp_enqueue_global_styles_css_custom_properties')) {
    return;
  }
  // iframe内のenqueue_block_assetsに対応したWordPress 6.3以降だけの移行
  if (version_compare(get_bloginfo('version'), '6.3', '<')) {
    return;
  }

  // iframe内外の両方へCSS変数を渡すためのコンテンツ用フックへの移行
  $callback = 'wp_enqueue_global_styles_css_custom_properties';
  $priority = has_action('enqueue_block_editor_assets', $callback);
  if ($priority === false) {
    return;
  }
  remove_action('enqueue_block_editor_assets', $callback, $priority);
  if (
    has_action('enqueue_block_assets', $callback) === false &&
    has_action('enqueue_block_assets', 'cocoon_enqueue_editor_global_style_variables') === false
  ) {
    add_action('enqueue_block_assets', 'cocoon_enqueue_editor_global_style_variables', $priority);
  }
}
endif;

if ( !function_exists('cocoon_enqueue_editor_global_style_variables') ):
function cocoon_enqueue_editor_global_style_variables() {
  if (!is_admin() || !function_exists('wp_enqueue_global_styles_css_custom_properties')) {
    return;
  }
  $handle = 'global-styles-css-custom-properties';
  // iframeのアセット収集で共有される登録情報への同一CSSの重複追加の防止
  if (!wp_styles()->get_data($handle, 'after')) {
    wp_enqueue_global_styles_css_custom_properties();
  } else {
    wp_enqueue_style($handle);
  }
}
endif;

add_filter('block_editor_rest_api_preload_paths', 'cocoon_adjust_block_editor_preload_paths', 10, 2);
if ( !function_exists('cocoon_adjust_block_editor_preload_paths') ):
function cocoon_adjust_block_editor_preload_paths($preload_paths, $editor_context) {
  if (empty($editor_context->post) || ($editor_context->name ?? '') !== 'core/edit-post') {
    return $preload_paths;
  }

  $post = $editor_context->post;
  $template_slug = $post->post_type === 'page' ? 'page' : 'single-' . $post->post_type;
  // コアのPHP側とJavaScript側で異なる文字列0の判定への対応
  $core_template_slug = $template_slug;
  if (!empty($post->post_name)) {
    $core_template_slug .= '-' . $post->post_name;
  }
  if ($post->post_name !== '') {
    $template_slug .= '-' . $post->post_name;
  }

  // JavaScript側が送るパーセント符号を含むスラッグ全体のクエリ値としての符号化
  $template_path = add_query_arg('slug', $core_template_slug, '/wp/v2/templates/lookup');
  $encoded_template_path = add_query_arg('slug', rawurlencode($template_slug), '/wp/v2/templates/lookup');
  $author_path = '/wp/v2/users/' . (int) $post->post_author;
  $adjusted_paths = array();
  foreach ($preload_paths as $preload_path) {
    $is_array = is_array($preload_path);
    $path = $is_array ? ($preload_path[0] ?? '') : $preload_path;
    $method = $is_array ? ($preload_path[1] ?? 'GET') : 'GET';
    if (!is_string($path) || $method !== 'GET') {
      $adjusted_paths[] = $preload_path;
      continue;
    }

    // 投稿者選択UIが利用するユーザー一覧取得とは異なる未使用の個別取得だけの除外
    $query = array();
    wp_parse_str((string) wp_parse_url($path, PHP_URL_QUERY), $query);
    if (
      $post->post_author > 0 &&
      wp_parse_url($path, PHP_URL_PATH) === $author_path &&
      count($query) === 2 &&
      ($query['context'] ?? '') === 'view' &&
      ($query['_fields'] ?? '') === 'id,name'
    ) {
      continue;
    }

    // コアのテンプレート事前取得だけの補正と他のクエリ・HTTPメソッドの保持
    if ($path === $template_path) {
      if ($is_array) {
        $preload_path[0] = $encoded_template_path;
      } else {
        $preload_path = $encoded_template_path;
      }
    }
    $adjusted_paths[] = $preload_path;
  }
  return $adjusted_paths;
}
endif;
