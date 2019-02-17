<?php //PWA設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PWAを有効にする
define('OP_PWA_ENABLE', 'pwa_enable');
if ( !function_exists( 'is_pwa_enable' ) ):
function is_pwa_enable(){
  return get_theme_option(OP_PWA_ENABLE);
}
endif;

//管理者にPWAを有効にする
define('OP_PWA_ADMIN_ENABLE', 'pwa_admin_enable');
if ( !function_exists( 'is_pwa_admin_enable' ) ):
function is_pwa_admin_enable(){
  return get_theme_option(OP_PWA_ADMIN_ENABLE);
}
endif;

//PWAアプリ名
define('OP_PWA_NAME', 'pwa_name');
if ( !function_exists( 'get_pwa_name' ) ):
function get_pwa_name(){
  return stripslashes_deep(trim(get_theme_option(OP_PWA_NAME, get_bloginfo('name'))));
}
endif;

//PWAホーム画面に表示されるアプリ名
define('OP_PWA_SHORT_NAME', 'pwa_short_name');
if ( !function_exists( 'get_pwa_short_name' ) ):
function get_pwa_short_name(){
  $default_name = get_simplified_site_name();
  $default_name = $default_name ? $default_name : get_bloginfo('name');
  return stripslashes_deep(trim(get_theme_option(OP_PWA_SHORT_NAME, $default_name)));
}
endif;

//PWAアプリの説明
define('OP_PWA_DESCRIPTION', 'pwa_description');
if ( !function_exists( 'get_pwa_description' ) ):
function get_pwa_description(){
  return stripslashes_deep(trim(get_theme_option(OP_PWA_DESCRIPTION, get_bloginfo('description'))));
}
endif;

//PWAテーマカラー
define('OP_PWA_THEME_COLOR', 'pwa_theme_color');
if ( !function_exists( 'get_pwa_theme_color' ) ):
function get_pwa_theme_color(){
  return get_theme_option(OP_PWA_THEME_COLOR, '#19448e');
}
endif;

//PWA背景色
define('OP_PWA_BACKGROUND_COLOR', 'pwa_background_color');
if ( !function_exists( 'get_pwa_background_color' ) ):
function get_pwa_background_color(){
  return get_theme_option(OP_PWA_BACKGROUND_COLOR, '#ffffff');
}
endif;

//PWA表示モード
define('OP_PWA_DISPLAY', 'pwa_display');
if ( !function_exists( 'get_pwa_display' ) ):
function get_pwa_display(){
  return get_theme_option(OP_PWA_DISPLAY, 'minimal-ui');
}
endif;

//PWA画面の向き
define('OP_PWA_ORIENTATION', 'pwa_orientation');
if ( !function_exists( 'get_pwa_orientation' ) ):
function get_pwa_orientation(){
  return get_theme_option(OP_PWA_ORIENTATION, 'any');
}
endif;

//SサイズのサイトアイコンURLを取得
if ( !function_exists( 'get_site_icon_url_s' ) ):
function get_site_icon_url_s(){
  $icon_url = get_site_icon_url(192);
  if (empty($icon_url)) {
    $icon_url = DEFAULT_SITE_ICON_192;
  }
  return $icon_url;
}
endif;

//LサイズのサイトアイコンURLを取得
if ( !function_exists( 'get_site_icon_url_l' ) ):
function get_site_icon_url_l(){
  $icon_url = get_site_icon_url(512);
  if (empty($icon_url)) {
    $icon_url = DEFAULT_SITE_ICON_270;
  }
  return $icon_url;
}
endif;

//サイトアイコンURLからサイズの取得
if ( !function_exists( 'get_site_icon_size_text' ) ):
function get_site_icon_size_text($url){
  $size = null;
  $res = preg_match('/(\d+?x\d+?)[\.\-]/', $url, $m);
  if (isset($m[1])) {
    $size = $m[1];
  }
  return $size;
}
endif;

//.htaccessにHTTPSリダイレクトルールを書き込む
if ( !function_exists( 'add_https_rewriterule_to_htaccess' ) ):
function add_https_rewriterule_to_htaccess(){
  $resoce_file = get_template_directory().'/configs/https-rewriterule.conf';
  $begin = THEME_HTTPS_REDIRECT_HTACCESS_BEGIN;
  $end = THEME_HTTPS_REDIRECT_HTACCESS_END;
  $reg = THEME_HTTPS_REDIRECT_HTACCESS_REG;
  add_code_to_htaccess($resoce_file, $begin, $end, $reg);
}
endif;

//.htaccessからHTTPSリダイレクトルールを削除する
if ( !function_exists( 'remove_https_rewriterule_from_htacccess' ) ):
function remove_https_rewriterule_from_htacccess(){
  $reg = THEME_HTTPS_REDIRECT_HTACCESS_REG;
  remove_code_from_htacccess($reg);
}
endif;
