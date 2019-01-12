<?php //コメント設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//コメント表示形式
define('OP_COMMENT_DISPLAY_TYPE', 'comment_display_type');
if ( !function_exists( 'get_comment_display_type' ) ):
function get_comment_display_type(){
  return get_theme_option(OP_COMMENT_DISPLAY_TYPE, 'default');
}
endif;
if ( !function_exists( 'is_comment_display_type_default' ) ):
function is_comment_display_type_default(){
  return get_comment_display_type() == 'default';
}
endif;
if ( !function_exists( 'is_comment_display_type_simple_thread' ) ):
function is_comment_display_type_simple_thread(){
  return get_comment_display_type() == 'simple_thread';
}
endif;

//コメントの見出し
define('OP_COMMENT_HEADING', 'comment_heading');
if ( !function_exists( 'get_comment_heading' ) ):
function get_comment_heading(){
  return stripslashes_deep(get_theme_option(OP_COMMENT_HEADING, __( 'コメント', THEME_NAME )));
}
endif;

//コメントのサブ見出し
define('OP_COMMENT_SUB_HEADING', 'comment_sub_heading');
if ( !function_exists( 'get_comment_sub_heading' ) ):
function get_comment_sub_heading(){
  return stripslashes_deep(get_theme_option(OP_COMMENT_SUB_HEADING));
}
endif;

//コメント入力欄の表示タイプ
define('OP_COMMENT_FORM_DISPLAY_TYPE', 'comment_form_display_type');
if ( !function_exists( 'get_comment_form_display_type' ) ):
function get_comment_form_display_type(){
  return get_theme_option(OP_COMMENT_FORM_DISPLAY_TYPE, 'toggle_button');
}
endif;
if ( !function_exists( 'is_comment_form_display_type_always' ) ):
function is_comment_form_display_type_always(){
  return get_comment_form_display_type() == 'always';
}
endif;
if ( !function_exists( 'is_comment_form_display_type_toggle_button' ) ):
function is_comment_form_display_type_toggle_button(){
  return get_comment_form_display_type() == 'toggle_button';
}
endif;

//コメント入力欄の見出し
define('OP_COMMENT_FORM_HEADING', 'comment_form_heading');
if ( !function_exists( 'get_comment_form_heading' ) ):
function get_comment_form_heading(){
  return stripslashes_deep(get_theme_option(OP_COMMENT_FORM_HEADING, __( 'コメントをどうぞ', THEME_NAME )));
}
endif;

//コメント案内メッセージ
define('OP_COMMENT_INFORMATION_MESSAGE', 'comment_information_message');
if ( !function_exists( 'get_comment_information_message' ) ):
function get_comment_information_message(){
  return stripslashes_deep(get_theme_option(OP_COMMENT_INFORMATION_MESSAGE));
}
endif;

//ウェブサイト入力欄表示
define('OP_COMMENT_WEBSITE_VISIBLE', 'comment_website_visible');
if ( !function_exists( 'is_comment_website_visible' ) ):
function is_comment_website_visible(){
  return get_theme_option(OP_COMMENT_WEBSITE_VISIBLE, 1);
}
endif;

//コメント送信ボタンのラベル
define('OP_COMMENT_SUBMIT_LABEL', 'comment_submit_label');
if ( !function_exists( 'get_comment_submit_label' ) ):
function get_comment_submit_label(){
  return stripslashes_deep(get_theme_option(OP_COMMENT_SUBMIT_LABEL, __( 'コメントを送信', THEME_NAME )));
}
endif;
