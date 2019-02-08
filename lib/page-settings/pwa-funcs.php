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

//PWAアプリ名
define('OP_PWA_NAME', 'pwa_name');
if ( !function_exists( 'get_pwa_name' ) ):
function get_pwa_name(){
  return get_theme_option(OP_PWA_NAME, get_bloginfo('name'));
}
endif;

//PWAホーム画面に表示されるアプリ名
define('OP_PWA_SHORT_NAME', 'pwa_short_name');
if ( !function_exists( 'get_pwa_short_name' ) ):
function get_pwa_short_name(){
  $short_name = get_simplified_site_name();
  return get_theme_option(
    OP_PWA_SHORT_NAME,
    $short_name ? $short_name : get_bloginfo('name')
  );
}
endif;

//PWAアプリの説明
define('OP_PWA_DESCRIPTION', 'pwa_description');
if ( !function_exists( 'get_pwa_description' ) ):
function get_pwa_description(){
  return get_theme_option(OP_PWA_DESCRIPTION, bloginfo('description'));
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
  return get_theme_option(OP_PWA_ORIENTATION);
}
endif;
