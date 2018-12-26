<?php //アピールエリア設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// //アピールエリアの表示
// define('OP_APPEAL_AREA_VISIBLE', 'appeal_area_visible');
// if ( !function_exists( 'is_appeal_area_visible' ) ):
// function is_appeal_area_visible(){
//   return get_theme_option(OP_APPEAL_AREA_VISIBLE, 1);
// }
// endif;

//アピールエリアの表示
define('OP_APPEAL_AREA_DISPLAY_TYPE', 'appeal_area_display_type');
if ( !function_exists( 'get_appeal_area_display_type' ) ):
function get_appeal_area_display_type(){
  return get_theme_option(OP_APPEAL_AREA_DISPLAY_TYPE, 'none');
}
endif;
if ( !function_exists( 'is_appeal_area_visible' ) ):
function is_appeal_area_visible(){
  return get_appeal_area_display_type() != 'none';
}
endif;

//アピールエリアの高さ
define('OP_APPEAL_AREA_HEIGHT', 'appeal_area_height');
if ( !function_exists( 'get_appeal_area_height' ) ):
function get_appeal_area_height(){
  return get_theme_option(OP_APPEAL_AREA_HEIGHT);
}
endif;

//アピールエリア画像
define('OP_APPEAL_AREA_IMAGE_URL', 'appeal_area_image_url');
if ( !function_exists( 'get_appeal_area_image_url' ) ):
function get_appeal_area_image_url(){
  return get_theme_option(OP_APPEAL_AREA_IMAGE_URL);
}
endif;

//アピールエリア背景色
define('OP_APPEAL_AREA_BACKGROUND_COLOR', 'appeal_area_background_color');
if ( !function_exists( 'get_appeal_area_background_color' ) ):
function get_appeal_area_background_color(){
  return get_theme_option(OP_APPEAL_AREA_BACKGROUND_COLOR);
}
endif;

//アピールエリア背景を固定にするか
define('OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED', 'appeal_area_background_attachment_fixed');
if ( !function_exists( 'is_appeal_area_background_attachment_fixed' ) ):
function is_appeal_area_background_attachment_fixed(){
  return get_theme_option(OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED);
}
endif;

//アピールエリアタイトル
define('OP_APPEAL_AREA_TITLE', 'appeal_area_title');
if ( !function_exists( 'get_appeal_area_title' ) ):
function get_appeal_area_title(){
  return get_theme_option(OP_APPEAL_AREA_TITLE);
}
endif;

//アピールエリアメッセージ
define('OP_APPEAL_AREA_MESSAGE', 'appeal_area_message');
if ( !function_exists( 'get_appeal_area_message' ) ):
function get_appeal_area_message(){
  $appeal_area_message = stripslashes_deep(get_theme_option(OP_APPEAL_AREA_MESSAGE));
  return apply_filters('appeal_area_message', $appeal_area_message);
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

//アピールエリアボタンのブラウザでの開き方
define('OP_APPEAL_AREA_BUTTON_TARGET', 'appeal_area_button_target');
if ( !function_exists( 'get_appeal_area_button_target' ) ):
function get_appeal_area_button_target(){
  return get_theme_option(OP_APPEAL_AREA_BUTTON_TARGET, '_self');
}
endif;

//アピールエリアボタン色
define('OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR', 'appeal_area_button_background_color');
if ( !function_exists( 'get_appeal_area_button_background_color' ) ):
function get_appeal_area_button_background_color(){
  return get_theme_option(OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR);
}
endif;
