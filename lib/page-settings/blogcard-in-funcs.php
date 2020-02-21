<?php //内部ブログカード設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//内部ブログカードが有効
define('OP_INTERNAL_BLOGCARD_ENABLE', 'internal_blogcard_enable');
if ( !function_exists( 'is_internal_blogcard_enable' ) ):
function is_internal_blogcard_enable(){
  return get_theme_option(OP_INTERNAL_BLOGCARD_ENABLE, 1);
}
endif;

//コメント内部ブログカードが有効
define('OP_COMMENT_INTERNAL_BLOGCARD_ENABLE', 'comment_internal_blogcard_enable');
if ( !function_exists( 'is_comment_internal_blogcard_enable' ) ):
function is_comment_internal_blogcard_enable(){
  return get_theme_option(OP_COMMENT_INTERNAL_BLOGCARD_ENABLE);
}
endif;

//内部ブログカードのサムネイル設定
define('OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE', 'internal_blogcard_thumbnail_style');
if ( !function_exists( 'get_internal_blogcard_thumbnail_style' ) ):
function get_internal_blogcard_thumbnail_style(){
  return get_theme_option(OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE, 'left');
}
endif;

//内部ブログカードの月表示
define('OP_INTERNAL_BLOGCARD_DATE_TYPE', 'internal_blogcard_date_type');
if ( !function_exists( 'get_internal_blogcard_date_type' ) ):
function get_internal_blogcard_date_type(){
  return get_theme_option(OP_INTERNAL_BLOGCARD_DATE_TYPE, 'post_date');
}
endif;
if ( !function_exists( 'is_internal_blogcard_date_visible' ) ):
function is_internal_blogcard_date_visible(){
  return get_internal_blogcard_date_type() != 'none';
}
endif;
if ( !function_exists( 'is_internal_blogcard_date_type_post_date' ) ):
function is_internal_blogcard_date_type_post_date(){
  return get_internal_blogcard_date_type() == 'post_date';
}
endif;
if ( !function_exists( 'is_internal_blogcard_date_type_up_date' ) ):
function is_internal_blogcard_date_type_up_date(){
  return get_internal_blogcard_date_type() == 'up_date';
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
