<?php //ボタン設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//トップへ戻るボタンの表示
define('OP_GO_TO_TOP_BUTTON_VISIBLE', 'go_to_top_button_visible');
if ( !function_exists( 'is_go_to_top_button_visible' ) ):
function is_go_to_top_button_visible(){
  return get_theme_option(OP_GO_TO_TOP_BUTTON_VISIBLE, 1);
}
endif;

//トップへ戻るボタンのアイコンフォント
define('OP_GO_TO_TOP_BUTTON_ICON_FONT', 'go_to_top_button_icon_font');
if ( !function_exists( 'get_go_to_top_button_icon_font' ) ):
function get_go_to_top_button_icon_font(){
  return get_theme_option(OP_GO_TO_TOP_BUTTON_ICON_FONT, 'fa-angle-double-up');
}
endif;

//ボタン背景色
define('OP_GO_TO_TOP_BACKGROUND_COLOR', 'go_to_top_background_color');
if ( !function_exists( 'get_go_to_top_background_color' ) ):
function get_go_to_top_background_color(){
  return get_theme_option(OP_GO_TO_TOP_BACKGROUND_COLOR);
}
endif;

//ボタン文字色
define('OP_GO_TO_TOP_TEXT_COLOR', 'go_to_top_text_color');
if ( !function_exists( 'get_go_to_top_text_color' ) ):
function get_go_to_top_text_color(){
  return get_theme_option(OP_GO_TO_TOP_TEXT_COLOR);
}
endif;

//トップへ戻るボタンの画像
define('OP_GO_TO_TOP_BUTTON_IMAGE_URL', 'go_to_top_button_image_url');
if ( !function_exists( 'get_go_to_top_button_image_url' ) ):
function get_go_to_top_button_image_url(){
  return get_theme_option(OP_GO_TO_TOP_BUTTON_IMAGE_URL);
}
endif;
