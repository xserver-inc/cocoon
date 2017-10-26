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

//アピールエリア背景を固定にするか
define('OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED', 'appeal_area_background_attachment_fixed');
if ( !function_exists( 'is_appeal_area_background_attachment_fixed' ) ):
function is_appeal_area_background_attachment_fixed(){
  return get_theme_option(OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED);
}
endif;

//アピールエリアメッセージ
define('OP_APPEAL_AREA_MESSAGE', 'appeal_area_message');
if ( !function_exists( 'get_appeal_area_message' ) ):
function get_appeal_area_message(){
  return stripslashes_deep(get_theme_option(OP_APPEAL_AREA_MESSAGE));
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
define('OP_APPEAL_AREA_BUTTON_COLOR', 'appeal_area_button_color');
if ( !function_exists( 'get_appeal_area_button_color' ) ):
function get_appeal_area_button_color(){
  return get_theme_option(OP_APPEAL_AREA_BUTTON_COLOR);
}
endif;