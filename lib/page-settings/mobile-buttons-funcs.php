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
  $res = get_theme_option(OP_MOBILE_BUTTON_LAYOUT_TYPE, 'footer_mobile_buttons');
  if ($res == 'slide_in') {
    $res = 'footer_mobile_buttons';
  }
  return $res;
}
endif;
if ( !function_exists( 'is_mobile_button_layout_type_footer_mobile_buttons' ) ):
function is_mobile_button_layout_type_footer_mobile_buttons(){
  return get_mobile_button_layout_type() == 'footer_mobile_buttons';
}
endif;
//is_mobile_button_layout_type_footer_mobile_buttonsの旧関数名
if ( !function_exists( 'is_mobile_button_layout_type_slide_in' ) ):
function is_mobile_button_layout_type_slide_in(){
  return is_mobile_button_layout_type_footer_mobile_buttons();
}
endif;
if ( !function_exists( 'is_mobile_button_layout_type_header_mobile_buttons' ) ):
function is_mobile_button_layout_type_header_mobile_buttons(){
  return get_mobile_button_layout_type() == 'header_mobile_buttons';
}
endif;
if ( !function_exists( 'is_mobile_button_layout_type_header_and_footer_mobile_buttons' ) ):
function is_mobile_button_layout_type_header_and_footer_mobile_buttons(){
  return get_mobile_button_layout_type() == 'header_and_footer_mobile_buttons';
}
endif;
if ( !function_exists( 'is_mobile_button_layout_type_mobile_buttons' ) ):
function is_mobile_button_layout_type_mobile_buttons(){
  return is_mobile_button_layout_type_footer_mobile_buttons() ||
    is_mobile_button_layout_type_header_mobile_buttons() ||
    is_mobile_button_layout_type_header_and_footer_mobile_buttons();
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

//モバイルボタンを固定表示するか
define('OP_FIXED_MOBILE_BUTTONS_ENABLE', 'fixed_mobile_buttons_enable');
if ( !function_exists( 'is_fixed_mobile_buttons_enable' ) ):
function is_fixed_mobile_buttons_enable(){
  return get_theme_option(OP_FIXED_MOBILE_BUTTONS_ENABLE);
}
endif;

//ヘッダーロゴを表示する（モバイルヘッダーボタン表示時）
define('OP_MOBILE_HEADER_LOGO_VISIBLE', 'mobile_header_logo_visible');
if ( !function_exists( 'is_mobile_header_logo_visible' ) ):
function is_mobile_header_logo_visible(){
  return get_theme_option(OP_MOBILE_HEADER_LOGO_VISIBLE, 1);
}
endif;

//スライドインメニュー表示の際にメインコンテンツ下にサイドバーを表示するか
define('OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE', 'slide_in_content_bottom_sidebar_visible');
if ( !function_exists( 'is_slide_in_content_bottom_sidebar_visible' ) ):
function is_slide_in_content_bottom_sidebar_visible(){
  return get_theme_option(OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE);
}
endif;
