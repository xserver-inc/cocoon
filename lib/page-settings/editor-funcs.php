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

//タグをチェックリストにするか
define('OP_EDITOR_TAG_CHECK_LIST_ENABLE', 'editor_tag_check_list_enable');
if ( !function_exists( 'is_editor_tag_check_list_enable' ) ):
function is_editor_tag_check_list_enable(){
  return get_theme_option(OP_EDITOR_TAG_CHECK_LIST_ENABLE);
}
endif;

//ルビボタン有効
define('OP_BLOCK_EDITOR_RUBY_BUTTON_VISIBLE', 'block_editor_ruby_button_visible');
if ( !function_exists( 'is_block_editor_ruby_button_visible' ) ):
function is_block_editor_ruby_button_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_RUBY_BUTTON_VISIBLE, 1);
}
endif;

//ブロックエディターインラインスタイルドロップダウン有効
define('OP_BLOCK_EDITOR_LETTER_STYLE_DROPDOWN_VISIBLE', 'block_editor_letter_style_dropdown_visible');
if ( !function_exists( 'is_block_editor_letter_style_dropdown_visible' ) ):
function is_block_editor_letter_style_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_LETTER_STYLE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディターマーカースタイルドロップダウン有効
define('OP_BLOCK_EDITOR_MARKER_STYLE_DROPDOWN_VISIBLE', 'block_editor_marker_style_dropdown_visible');
if ( !function_exists( 'is_block_editor_marker_style_dropdown_visible' ) ):
function is_block_editor_marker_style_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_MARKER_STYLE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディターバッジスタイルドロップダウン有効
define('OP_BLOCK_EDITOR_BADGE_STYLE_DROPDOWN_VISIBLE', 'block_editor_badge_style_dropdown_visible');
if ( !function_exists( 'is_block_editor_badge_style_dropdown_visible' ) ):
function is_block_editor_badge_style_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_BADGE_STYLE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディター文字サイズスタイルドロップダウン有効
define('OP_BLOCK_EDITOR_FONT_SIZE_STYLE_DROPDOWN_VISIBLE', 'block_editor_font_size_style_dropdown_visible');
if ( !function_exists( 'is_block_editor_font_size_style_dropdown_visible' ) ):
function is_block_editor_font_size_style_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_FONT_SIZE_STYLE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディター汎用ショートコードドロップダウン有効
define('OP_BLOCK_EDITOR_GENERAL_SHORTCODE_DROPDOWN_VISIBLE', 'block_editor_general_shortcode_dropdown_visible');
if ( !function_exists( 'is_block_editor_general_shortcode_dropdown_visible' ) ):
function is_block_editor_general_shortcode_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_GENERAL_SHORTCODE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディターテンプレートショートコードドロップダウン有効
define('OP_BLOCK_EDITOR_TEMPLATE_SHORTCODE_DROPDOWN_VISIBLE', 'block_editor_template_shortcode_dropdown_visible');
if ( !function_exists( 'is_block_editor_template_shortcode_dropdown_visible' ) ):
function is_block_editor_template_shortcode_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_TEMPLATE_SHORTCODE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディターアフィリエイトショートコードドロップダウン有効
define('OP_BLOCK_EDITOR_AFFILIATE_SHORTCODE_DROPDOWN_VISIBLE', 'block_editor_affiliate_shortcode_dropdown_visible');
if ( !function_exists( 'is_block_editor_affiliate_shortcode_dropdown_visible' ) ):
function is_block_editor_affiliate_shortcode_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_AFFILIATE_SHORTCODE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディターランキングショートコードドロップダウン有効
define('OP_BLOCK_EDITOR_RANKING_SHORTCODE_DROPDOWN_VISIBLE', 'block_editor_ranking_shortcode_dropdown_visible');
if ( !function_exists( 'is_block_editor_ranking_shortcode_dropdown_visible' ) ):
function is_block_editor_ranking_shortcode_dropdown_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_RANKING_SHORTCODE_DROPDOWN_VISIBLE, 1);
}
endif;

//ブロックエディタースタイルブロックオプション有効
define('OP_BLOCK_EDITOR_STYLE_BLOCK_OPTION_VISIBLE', 'block_editor_style_block_option_visible');
if ( !function_exists( 'is_block_editor_style_block_option_visible' ) ):
function is_block_editor_style_block_option_visible(){
  return get_theme_option(OP_BLOCK_EDITOR_STYLE_BLOCK_OPTION_VISIBLE);
}
endif;

//拡張カラーパレット色A
define('OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_A', 'block_editor_extended_palette_color_a');
if ( !function_exists( 'get_block_editor_extended_palette_color_a' ) ):
function get_block_editor_extended_palette_color_a(){
  $default_color = '#ffffff';
  $color = get_theme_option(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_A, $default_color);
  return $color ? $color : $default_color;
}
endif;

//拡張カラーパレット色B
define('OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_B', 'block_editor_extended_palette_color_b');
if ( !function_exists( 'get_block_editor_extended_palette_color_b' ) ):
function get_block_editor_extended_palette_color_b(){
  $default_color = '#ffffff';
  $color = get_theme_option(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_B, $default_color);
  return $color ? $color : $default_color;
}
endif;

//拡張カラーパレット色C
define('OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_C', 'block_editor_extended_palette_color_c');
if ( !function_exists( 'get_block_editor_extended_palette_color_c' ) ):
function get_block_editor_extended_palette_color_c(){
  $default_color = '#ffffff';
  $color = get_theme_option(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_C, $default_color);
  return $color ? $color : $default_color;
}
endif;

//拡張カラーパレット色D
define('OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_D', 'block_editor_extended_palette_color_d');
if ( !function_exists( 'get_block_editor_extended_palette_color_d' ) ):
function get_block_editor_extended_palette_color_d(){
  $default_color = '#ffffff';
  $color = get_theme_option(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_D, $default_color);
  return $color ? $color : $default_color;
}
endif;

//拡張カラーパレット色E
define('OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_E', 'block_editor_extended_palette_color_e');
if ( !function_exists( 'get_block_editor_extended_palette_color_e' ) ):
function get_block_editor_extended_palette_color_e(){
  $default_color = '#ffffff';
  $color = get_theme_option(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_E, $default_color);
  return $color ? $color : $default_color;
}
endif;

//拡張カラーパレット色F
define('OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_F', 'block_editor_extended_palette_color_f');
if ( !function_exists( 'get_block_editor_extended_palette_color_f' ) ):
function get_block_editor_extended_palette_color_f(){
  $default_color = '#ffffff';
  $color = get_theme_option(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_F, $default_color);
  return $color ? $color : $default_color;
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
