<?php //カルーセル設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//カルーセルの表示
define('OP_CAROUSEL_DISPLAY_TYPE', 'carousel_display_type');
if ( !function_exists( 'get_carousel_display_type' ) ):
function get_carousel_display_type(){
  return get_theme_option(OP_CAROUSEL_DISPLAY_TYPE, 'none');
}
endif;
if ( !function_exists( 'is_carousel_visible' ) ):
function is_carousel_visible(){
  return (get_carousel_category_ids() || get_carousel_tag_ids()) &&
    (
      is_carousel_display_type_all_page() ||
      (is_front_top_page() && is_carousel_display_type_front_page_only()) ||
      (!is_singular() && is_carousel_display_type_not_singular()) ||
      (is_singular() && is_carousel_display_type_singular_only()) ||
      (is_single() && is_carousel_display_type_single_only()) ||
      (is_page() && is_carousel_display_type_page_only()) ||
      (is_admin() && get_carousel_display_type() != 'none') //設定プレビュー
    );
}
endif;
if ( !function_exists( 'is_carousel_display_type_all_page' ) ):
function is_carousel_display_type_all_page(){
  return get_carousel_display_type() == 'all_page';
}
endif;
if ( !function_exists( 'is_carousel_display_type_front_page_only' ) ):
function is_carousel_display_type_front_page_only(){
  return get_carousel_display_type() == 'front_page_only';
}
endif;
if ( !function_exists( 'is_carousel_display_type_not_singular' ) ):
function is_carousel_display_type_not_singular(){
  return get_carousel_display_type() == 'not_singular';
}
endif;
if ( !function_exists( 'is_carousel_display_type_singular_only' ) ):
function is_carousel_display_type_singular_only(){
  return get_carousel_display_type() == 'singular_only';
}
endif;
if ( !function_exists( 'is_carousel_display_type_single_only' ) ):
function is_carousel_display_type_single_only(){
  return get_carousel_display_type() == 'single_only';
}
endif;
if ( !function_exists( 'is_carousel_display_type_page_only' ) ):
function is_carousel_display_type_page_only(){
  return get_carousel_display_type() == 'page_only';
}
endif;

//カルーセルをスマホで表示する
define('OP_CAROUSEL_SMARTPHONE_VISIBLE', 'carousel_smartphone_visible');
if ( !function_exists( 'is_carousel_smartphone_visible' ) ):
function is_carousel_smartphone_visible(){
  return get_theme_option(OP_CAROUSEL_SMARTPHONE_VISIBLE, 1);
}
endif;

//カルーセルに表示するカテゴリID
define('OP_CAROUSEL_CATEGORY_IDS', 'carousel_category_ids');
if ( !function_exists( 'get_carousel_category_ids' ) ):
function get_carousel_category_ids(){
  return get_theme_option(OP_CAROUSEL_CATEGORY_IDS, array());
}
endif;

//カルーセルに表示するタグID
define('OP_CAROUSEL_TAG_IDS', 'carousel_tag_ids');
if ( !function_exists( 'get_carousel_tag_ids' ) ):
function get_carousel_tag_ids(){
  return get_theme_option(OP_CAROUSEL_TAG_IDS, array());
}
endif;

//カルーセルの表示順
define('OP_CAROUSEL_ORDERBY', 'carousel_orderby');
if ( !function_exists( 'get_carousel_orderby' ) ):
function get_carousel_orderby(){
  return get_theme_option(OP_CAROUSEL_ORDERBY, 'rand');
}
endif;

//カルーセルに表示する最大数
define('OP_CAROUSEL_MAX_COUNT', 'carousel_max_count');
if ( !function_exists( 'get_carousel_max_count' ) ):
function get_carousel_max_count(){
  return get_theme_option(OP_CAROUSEL_MAX_COUNT, 18);
}
endif;

//カードの枠線を表示する
define('OP_CAROUSEL_CARD_BORDER_VISIBLE', 'carousel_card_border_visible');
if ( !function_exists( 'is_carousel_card_border_visible' ) ):
function is_carousel_card_border_visible(){
  return get_theme_option(OP_CAROUSEL_CARD_BORDER_VISIBLE);
}
endif;

//カルーセルオートプレイ
define('OP_CAROUSEL_AUTOPLAY_ENABLE', 'carousel_autoplay_enable');
if ( !function_exists( 'is_carousel_autoplay_enable' ) ):
function is_carousel_autoplay_enable(){
  return get_theme_option(OP_CAROUSEL_AUTOPLAY_ENABLE);
}
endif;

//カルーセルオートプレイインターバル
define('OP_CAROUSEL_AUTOPLAY_INTERVAL', 'carousel_autoplay_interval');
if ( !function_exists( 'get_carousel_autoplay_interval' ) ):
function get_carousel_autoplay_interval(){
  return get_theme_option(OP_CAROUSEL_AUTOPLAY_INTERVAL, 5);
}
endif;
