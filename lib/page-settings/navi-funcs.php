<?php //グローバルナビ設定に必要な定数や関数

//グローバルナビ背景色
define('OP_GLOBAL_NAVI_BACKGROUND_COLOR', 'global_navi_background_color');
if ( !function_exists( 'get_global_navi_background_color' ) ):
function get_global_navi_background_color(){
  return get_theme_option(OP_GLOBAL_NAVI_BACKGROUND_COLOR);
}
endif;

//グローバルナビ文字色
define('OP_GLOBAL_NAVI_TEXT_COLOR', 'global_navi_text_color');
if ( !function_exists( 'get_global_navi_text_color' ) ):
function get_global_navi_text_color(){
  return get_theme_option(OP_GLOBAL_NAVI_TEXT_COLOR);
}
endif;

//グローバルナビメニュー幅
define('OP_GLOBAL_NAVI_MENU_WIDTH', 'global_navi_menu_width');
if ( !function_exists( 'get_global_navi_menu_width' ) ):
function get_global_navi_menu_width(){
  return get_theme_option(OP_GLOBAL_NAVI_MENU_WIDTH);
}
endif;

//グローバルナビサブメニュー幅
define('OP_GLOBAL_NAVI_SUB_MENU_WIDTH', 'global_navi_sub_menu_width');
if ( !function_exists( 'get_global_navi_sub_menu_width' ) ):
function get_global_navi_sub_menu_width(){
  return get_theme_option(OP_GLOBAL_NAVI_SUB_MENU_WIDTH);
}
endif;

//グローバルナビメニューの固定
define('OP_GLOBAL_NAVI_FIXED', 'global_navi_fixed');
if ( !function_exists( 'is_global_navi_fixed' ) ):
function is_global_navi_fixed(){
  return get_theme_option(OP_GLOBAL_NAVI_FIXED);
}
endif;

