<?php //カルーセル設定に必要な定数や関数

//カルーセルの表示
define('OP_CAROUSEL_DISPLAY_TYPE', 'carousel_display_type');
if ( !function_exists( 'get_carousel_display_type' ) ):
function get_carousel_display_type(){
  return get_theme_option(OP_CAROUSEL_DISPLAY_TYPE, 'none');
}
endif;
if ( !function_exists( 'is_carousel_visible' ) ):
function is_carousel_visible(){
  return get_carousel_display_type() != 'none';
}
endif;

//カルーセルに表示するカテゴリID
define('OP_CAROUSEL_CATEGORY_IDS', 'carousel_category_ids');
if ( !function_exists( 'get_carousel_category_ids' ) ):
function get_carousel_category_ids(){
  return get_theme_option(OP_CAROUSEL_CATEGORY_IDS, array());
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