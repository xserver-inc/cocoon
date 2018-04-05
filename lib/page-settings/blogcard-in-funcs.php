<?php //内部ブログカード設定に必要な定数や関数


//内部ブログカードが有効
define('OP_INTERNAL_BLOGCARD_ENABLE', 'internal_blogcard_enable');
if ( !function_exists( 'is_internal_blogcard_enable' ) ):
function is_internal_blogcard_enable(){
  return get_theme_option(OP_INTERNAL_BLOGCARD_ENABLE, 1);
}
endif;

//内部ブログカードのサムネイル設定
define('OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE', 'internal_blogcard_thumbnail_style');
if ( !function_exists( 'get_internal_blogcard_thumbnail_style' ) ):
function get_internal_blogcard_thumbnail_style(){
  return get_theme_option(OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE, 'left');
}
endif;

//内部ブログカードを新しいタブで開くか（※廃止予定機能）
define('OP_INTERNAL_BLOGCARD_TARGET_BLANK', 'internal_blogcard_target_blank');
if ( !function_exists( 'is_internal_blogcard_target_blank' ) ):
function is_internal_blogcard_target_blank(){
  //return false;
  return get_theme_option(OP_INTERNAL_BLOGCARD_TARGET_BLANK);
}
endif;
