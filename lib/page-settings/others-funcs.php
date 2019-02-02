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

///////////////////////////////////////////
// JavaScriptライブラリ
///////////////////////////////////////////

//jQueryのバージョン
define('OP_JQUERY_VERSION', 'jquery_version');
if ( !function_exists( 'get_jquery_version' ) ):
function get_jquery_version(){
  return get_theme_option(OP_JQUERY_VERSION, '1');
}
endif;

//jQuery Mygrateのバージョン
define('OP_JQUERY_MIGRATE_VERSION', 'jquery_migrate_version');
if ( !function_exists( 'get_jquery_migrate_version' ) ):
function get_jquery_migrate_version(){
  return get_theme_option(OP_JQUERY_MIGRATE_VERSION, '1');
}
endif;
