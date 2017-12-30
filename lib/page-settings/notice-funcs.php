<?php //通知

//通知エリアを表示するか
define('OP_NOTICE_AREA_VISIBLE', 'notice_area_visible');
if ( !function_exists( 'is_notice_area_visible' ) ):
function is_notice_area_visible(){
  return get_theme_option(OP_NOTICE_AREA_VISIBLE);
}
endif;

//通知エリアメッセージ
define('OP_NOTICE_AREA_MESSAGE', 'notice_area_message');
if ( !function_exists( 'get_notice_area_message' ) ):
function get_notice_area_message(){
  return get_theme_option(OP_NOTICE_AREA_MESSAGE);
}
endif;

//通知タイプ
define('OP_NOTICE_TYPE', 'notice_type');
if ( !function_exists( 'get_notice_type' ) ):
function get_notice_type(){
  return get_theme_option(OP_NOTICE_TYPE);
}
endif;

//通知エリア色
define('OP_NOTICE_AREA_COLOR', 'notice_area_color');
if ( !function_exists( 'get_notice_area_color' ) ):
function get_notice_area_color(){
  return get_theme_option(OP_NOTICE_AREA_COLOR);
}
endif;