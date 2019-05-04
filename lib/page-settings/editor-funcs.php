<?php //その他設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Gutenbergエディターの有効化
define('OP_GUTENBERG_EDITOR_ENABLE', 'gutenberg_editor_enable');
if ( !function_exists( 'is_gutenberg_editor_enable' ) ):
function is_gutenberg_editor_enable(){
  return get_theme_option(OP_GUTENBERG_EDITOR_ENABLE, 1);
}
endif;
//Gutenbergを無効化する場合
if (!is_gutenberg_editor_enable()) {
  add_filter('gutenberg_can_edit_post_type', '__return_false');
  add_filter('use_block_editor_for_post', '__return_false');
}

add_action('after_setup_theme', 'after_setup_theme_gutenberg_editor_setup');
if ( !function_exists( 'after_setup_theme_gutenberg_editor_setup' ) ):
function after_setup_theme_gutenberg_editor_setup(){
  //Gutenbergエディターにワイドボタン表示
  add_theme_support( 'align-wide' );
}
endif;

//ビジュアルエディタースタイル
define('OP_VISUAL_EDITOR_STYLE_ENABLE', 'visual_editor_style_enable');
if ( !function_exists( 'is_visual_editor_style_enable' ) ):
function is_visual_editor_style_enable(){
  return get_theme_option(OP_VISUAL_EDITOR_STYLE_ENABLE, 1);
}
endif;

//エディター背景色
define('OP_EDITOR_BACKGROUND_COLOR', 'editor_background_color');
if ( !function_exists( 'get_editor_background_color' ) ):
function get_editor_background_color(){
  return get_theme_option(OP_EDITOR_BACKGROUND_COLOR);
}
endif;

//エディター文字色
define('OP_EDITOR_TEXT_COLOR', 'editor_text_color');
if ( !function_exists( 'get_editor_text_color' ) ):
function get_editor_text_color(){
  return get_theme_option(OP_EDITOR_TEXT_COLOR);
}
endif;

//タイトル等の文字数カウンター表示
define('OP_ADMIN_EDITOR_COUNTER_VISIBLE', 'admin_editor_counter_visible');
if ( !function_exists( 'is_admin_editor_counter_visible' ) ):
function is_admin_editor_counter_visible(){
  return get_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE, 1);
}
endif;

//ページ公開前に確認アラートを出す
define('OP_CONFIRMATION_BEFORE_PUBLISH', 'confirmation_before_publish');
if ( !function_exists( 'is_confirmation_before_publish' ) ):
function is_confirmation_before_publish(){
  return get_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH, 1);
}
endif;
