<?php //お勧め記事設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//お勧め記事の表示
define('OP_RECOMMENDED_CARDS_DISPLAY_TYPE', 'recommended_cards_display_type');
if ( !function_exists( 'get_recommended_cards_display_type' ) ):
function get_recommended_cards_display_type(){
  return get_theme_option(OP_RECOMMENDED_CARDS_DISPLAY_TYPE, 'none');
}
endif;
if ( !function_exists( 'is_recommended_cards_visible' ) ):
function is_recommended_cards_visible(){
  return (
    is_recommended_cards_display_type_all_page() ||
    (is_front_top_page() && is_recommended_cards_display_type_front_page_only()) ||
    (!is_singular() && is_recommended_cards_display_type_not_singular()) ||
    (is_singular() && is_recommended_cards_display_type_singular_only()) ||
    (is_single() && is_recommended_cards_display_type_single_only()) ||
    (is_page() && is_recommended_cards_display_type_page_only()) ||
    (is_admin() && get_recommended_cards_display_type() != 'none') //設定プレビュー
  );
}
endif;
if ( !function_exists( 'is_recommended_cards_display_type_all_page' ) ):
function is_recommended_cards_display_type_all_page(){
  return get_recommended_cards_display_type() == 'all_page';
}
endif;
if ( !function_exists( 'is_recommended_cards_display_type_front_page_only' ) ):
function is_recommended_cards_display_type_front_page_only(){
  return get_recommended_cards_display_type() == 'front_page_only';
}
endif;
if ( !function_exists( 'is_recommended_cards_display_type_not_singular' ) ):
function is_recommended_cards_display_type_not_singular(){
  return get_recommended_cards_display_type() == 'not_singular';
}
endif;
if ( !function_exists( 'is_recommended_cards_display_type_singular_only' ) ):
function is_recommended_cards_display_type_singular_only(){
  return get_recommended_cards_display_type() == 'singular_only';
}
endif;
if ( !function_exists( 'is_recommended_cards_display_type_single_only' ) ):
function is_recommended_cards_display_type_single_only(){
  return get_recommended_cards_display_type() == 'single_only';
}
endif;
if ( !function_exists( 'is_recommended_cards_display_type_page_only' ) ):
function is_recommended_cards_display_type_page_only(){
  return get_recommended_cards_display_type() == 'page_only';
}
endif;

//お勧め記事のメニュー名
define('OP_RECOMMENDED_CARDS_MENU_NAME', 'recommended_cards_menu_name');
if ( !function_exists( 'get_recommended_cards_menu_name' ) ):
function get_recommended_cards_menu_name(){
  return get_theme_option(OP_RECOMMENDED_CARDS_MENU_NAME);
}
endif;

//お勧め記事の表示スタイル
define('OP_RECOMMENDED_CARDS_STYLE', 'recommended_cards_style');
if ( !function_exists( 'get_recommended_cards_style' ) ):
function get_recommended_cards_style(){
  return get_theme_option(OP_RECOMMENDED_CARDS_STYLE, RC_DEFAULT);
}
endif;

//お勧め記事の余白は有効か
define('OP_RECOMMENDED_CARDS_MARGIN_ENABLE', 'recommended_cards_margin_enable');
if ( !function_exists( 'is_recommended_cards_margin_enable' ) ):
function is_recommended_cards_margin_enable(){
  return get_theme_option(OP_RECOMMENDED_CARDS_MARGIN_ENABLE);
}
endif;

//お勧め記事エリアの左右余白は有効か
define('OP_RECOMMENDED_CARDS_AREA_BOTH_SIDES_MARGIN_ENABLE', 'recommended_cards_area_both_sides_margin_enable');
if ( !function_exists( 'is_recommended_cards_area_both_sides_margin_enable' ) ):
function is_recommended_cards_area_both_sides_margin_enable(){
  return get_theme_option(OP_RECOMMENDED_CARDS_AREA_BOTH_SIDES_MARGIN_ENABLE);
}
endif;
