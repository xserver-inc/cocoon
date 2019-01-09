<?php //モバイルボタン設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//モバイルボタンレイアウト
define('OP_MOBILE_BUTTON_LAYOUT_TYPE', 'mobile_button_layout_type');
if ( !function_exists( 'get_mobile_button_layout_type' ) ):
function get_mobile_button_layout_type(){
  return get_theme_option(OP_MOBILE_BUTTON_LAYOUT_TYPE, 'slide_in');
}
endif;
if ( !function_exists( 'is_mobile_button_layout_type_slide_in' ) ):
function is_mobile_button_layout_type_slide_in(){
  return get_mobile_button_layout_type() == 'slide_in';
}
endif;

if ( !function_exists( 'is_slicknav_visible' ) ):
function is_slicknav_visible(){
  switch (get_mobile_button_layout_type()) {
    case 'top':
    case 'top_slidein':
      return true;
      break;
  }
  return false;
}
endif;

//スライドインメニュー表示の際にメインコンテンツ下にサイドバーを表示するか
define('OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE', 'slide_in_content_bottom_sidebar_visible');
if ( !function_exists( 'is_slide_in_content_bottom_sidebar_visible' ) ):
function is_slide_in_content_bottom_sidebar_visible(){
  return get_theme_option(OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE);
}
endif;
