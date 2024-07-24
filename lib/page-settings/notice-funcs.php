<?php //通知
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

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
  return stripslashes_deep(get_theme_option(OP_NOTICE_AREA_MESSAGE));
}
endif;

//通知エリアURL
define('OP_NOTICE_AREA_URL', 'notice_area_url');
if ( !function_exists( 'get_notice_area_url' ) ):
function get_notice_area_url(){
  return get_theme_option(OP_NOTICE_AREA_URL);
}
endif;

//通知リンクを新しいタブで開く
define('OP_NOTICE_LINK_TARGET_BLANK', 'notice_link_target_blank');
if ( !function_exists( 'is_notice_link_target_blank' ) ):
function is_notice_link_target_blank(){
  return get_theme_option(OP_NOTICE_LINK_TARGET_BLANK);
}
endif;

//通知タイプ
define('OP_NOTICE_TYPE', 'notice_type');
if ( !function_exists( 'get_notice_type' ) ):
function get_notice_type(){
  return get_theme_option(OP_NOTICE_TYPE);
}
endif;

//通知エリア背景色
define('OP_NOTICE_AREA_BACKGROUND_COLOR', 'notice_area_background_color');
if ( !function_exists( 'get_notice_area_background_color' ) ):
function get_notice_area_background_color(){
  return get_theme_option(OP_NOTICE_AREA_BACKGROUND_COLOR);
}
endif;

//通知エリアテキスト色
define('OP_NOTICE_AREA_TEXT_COLOR', 'notice_area_text_color');
if ( !function_exists( 'get_notice_area_text_color' ) ):
function get_notice_area_text_color(){
  return get_theme_option(OP_NOTICE_AREA_TEXT_COLOR);
}
endif;
