<?php //グローバルナビ設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//グローバルナビ背景色
define('OP_GLOBAL_NAVI_BACKGROUND_COLOR', 'global_navi_background_color');
if ( !function_exists( 'get_global_navi_background_color' ) ):
function get_global_navi_background_color(){
  return get_theme_option(OP_GLOBAL_NAVI_BACKGROUND_COLOR, '');
}
endif;

//グローバルナビ文字色
define('OP_GLOBAL_NAVI_TEXT_COLOR', 'global_navi_text_color');
if ( !function_exists( 'get_global_navi_text_color' ) ):
function get_global_navi_text_color(){
  return get_theme_option(OP_GLOBAL_NAVI_TEXT_COLOR, '');
}
endif;

//グローバルナビメニュー幅
define('OP_GLOBAL_NAVI_MENU_WIDTH', 'global_navi_menu_width');
if ( !function_exists( 'get_global_navi_menu_width' ) ):
function get_global_navi_menu_width(){
  return get_theme_option(OP_GLOBAL_NAVI_MENU_WIDTH, '');
}
endif;

//グローバルメニュー幅をテキストの幅にする
define('OP_GLOBAL_NAVI_MENU_TEXT_WIDTH_ENABLE', 'global_navi_menu_text_width_enable');
if ( !function_exists( 'is_global_navi_menu_text_width_enable' ) ):
function is_global_navi_menu_text_width_enable(){
  return get_theme_option(OP_GLOBAL_NAVI_MENU_TEXT_WIDTH_ENABLE);
}
endif;

//グローバルナビサブメニュー幅
define('OP_GLOBAL_NAVI_SUB_MENU_WIDTH', 'global_navi_sub_menu_width');
if ( !function_exists( 'get_global_navi_sub_menu_width' ) ):
function get_global_navi_sub_menu_width(){
  return get_theme_option(OP_GLOBAL_NAVI_SUB_MENU_WIDTH);
}
endif;

