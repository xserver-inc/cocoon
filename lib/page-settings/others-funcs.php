<?php //その他設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//簡単SSL対応
define('OP_EASY_SSL_ENABLE', 'easy_ssl_enable');
if ( !function_exists( 'is_easy_ssl_enable' ) ):
function is_easy_ssl_enable(){
  return get_theme_option(OP_EASY_SSL_ENABLE);
}
endif;

//ファイルシステム認証を有効にする
define('OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE', 'request_filesystem_credentials_enable');
if ( !function_exists( 'is_request_filesystem_credentials_enable' ) ):
function is_request_filesystem_credentials_enable(){
  return get_theme_option(OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE);
}
endif;

// WordPressのランダム投稿転送の無効化設定
define('OP_RANDOM_CONTENT_REDIRECT_DISABLE', 'random_content_redirect_disable');
if ( !function_exists( 'is_random_content_redirect_disabled' ) ):
function is_random_content_redirect_disabled(){
  return (bool) get_theme_option(OP_RANDOM_CONTENT_REDIRECT_DISABLE, false);
}
endif;

// WordPress 7.2以降のランダム投稿転送APIの有無
if ( !function_exists( 'cocoon_supports_random_content_redirect' ) ):
function cocoon_supports_random_content_redirect(){
  return function_exists('wp_is_random_content_redirect_enabled');
}
endif;

// 未チェック時のWordPress標準動作と他プラグインの設定の維持
add_filter('wp_enable_random_content_redirect', 'cocoon_filter_random_content_redirect_enabled');
if ( !function_exists( 'cocoon_filter_random_content_redirect_enabled' ) ):
function cocoon_filter_random_content_redirect_enabled($enabled){
  return is_random_content_redirect_disabled() ? false : $enabled;
}
endif;

// 設定保存・復元・リセット後のランダム投稿転送用URLルールの同期
add_action('wp_loaded', 'cocoon_sync_random_content_redirect_rewrite_rules');
add_action('cocoon_settings_after_save', 'cocoon_sync_random_content_redirect_rewrite_rules');
if ( !function_exists( 'cocoon_sync_random_content_redirect_rewrite_rules' ) ):
function cocoon_sync_random_content_redirect_rewrite_rules(){
  if (!cocoon_supports_random_content_redirect()) {
    return;
  }

  $disabled = is_random_content_redirect_disabled();
  $previous = (bool) get_option('cocoon_random_content_redirect_disabled', false);
  if ($disabled === $previous) {
    return;
  }

  // 有効状態の変更時だけの再生成と、サーバー設定ファイルへの書き込みの回避
  flush_rewrite_rules(false);
  update_option('cocoon_random_content_redirect_disabled', $disabled, false);
}
endif;

//Simplicityから設定情報の移行
define('OP_MIGRATE_FROM_SIMPLICITY', 'migrate_from_simplicity');
if ( !function_exists( 'is_migrate_from_simplicity' ) ):
function is_migrate_from_simplicity(){
  return get_theme_option(OP_MIGRATE_FROM_SIMPLICITY);
}
endif;

//スラッグが日本語の時はpost-XXXXのような連番形式にする
define('OP_AUTO_POST_SLUG_ENABLE', 'auto_post_slug_enable');
if ( !function_exists( 'is_auto_post_slug_enable' ) ):
function is_auto_post_slug_enable(){
  return get_theme_option(OP_AUTO_POST_SLUG_ENABLE);
}
endif;

if (is_auto_post_slug_enable()) {
  add_filter( 'wp_unique_post_slug', 'auto_post_slug', 10, 4  );
}
if ( !function_exists( 'auto_post_slug' ) ):
function auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
  $type = utf8_uri_encode( $post_type );
  if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) &&
     ( $post_type == 'post' || $post_type == 'page') ) {//投稿もしくは固定ページのときのみ実行する
    $slug = $type . '-' . $post_ID;
  }
  return $slug;
}
endif;

