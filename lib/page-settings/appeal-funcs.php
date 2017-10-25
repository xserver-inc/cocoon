<?php //アピールエリア設定に必要な定数や関数

//アピールエリアの表示
define('OP_APPEAL_AREA_VISIBLE', 'appeal_area_visible');
if ( !function_exists( 'is_appeal_area_visible' ) ):
function is_appeal_area_visible(){
  return get_theme_option(OP_APPEAL_AREA_VISIBLE, 1);
}
endif;

//アピールエリア画像
define('OP_APPEAL_AREA_IMAGE_URL', 'appeal_area_image_url');
if ( !function_exists( 'get_appeal_area_image_url' ) ):
function get_appeal_area_image_url(){
  return get_theme_option(OP_APPEAL_AREA_IMAGE_URL);
}
endif;

//アピールエリアメッセージ
define('OP_APPEAL_AREA_MESSAGE', 'appeal_area_message');
if ( !function_exists( 'get_appeal_area_message' ) ):
function get_appeal_area_message(){
  return get_theme_option(OP_APPEAL_AREA_MESSAGE);
}
endif;

//アピールエリアボタンメッセージ
define('OP_APPEAL_AREA_BUTTON_MESSAGE', 'appeal_area_button_message');
if ( !function_exists( 'get_appeal_area_button_message' ) ):
function get_appeal_area_button_message(){
  return get_theme_option(OP_APPEAL_AREA_BUTTON_MESSAGE);
}
endif;

//アピールエリアボタンURL
define('OP_APPEAL_AREA_BUTTON_URL', 'appeal_area_button_url');
if ( !function_exists( 'get_appeal_area_button_url' ) ):
function get_appeal_area_button_url(){
  return get_theme_option(OP_APPEAL_AREA_BUTTON_URL);
}
endif;

//アピールエリアボタン色
define('OP_APPEAL_AREA_BUTTON_COLOR', 'appeal_area_button_COLOR');
if ( !function_exists( 'get_appeal_area_button_COLOR' ) ):
function get_appeal_area_button_COLOR(){
  return get_theme_option(OP_APPEAL_AREA_BUTTON_COLOR);
}
endif;