<?php //外部ブログカード設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//外部ブログカードが有効
define('OP_EXTERNAL_BLOGCARD_ENABLE', 'external_blogcard_enable');
if ( !function_exists( 'is_external_blogcard_enable' ) ):
function is_external_blogcard_enable(){
  return get_theme_option(OP_EXTERNAL_BLOGCARD_ENABLE, 1);
}
endif;

//コメント外部ブログカードが有効
define('OP_COMMENT_EXTERNAL_BLOGCARD_ENABLE', 'comment_external_blogcard_enable');
if ( !function_exists( 'is_comment_external_blogcard_enable' ) ):
function is_comment_external_blogcard_enable(){
  return get_theme_option(OP_COMMENT_EXTERNAL_BLOGCARD_ENABLE);
}
endif;

//外部ブログカードのサムネイル設定
define('OP_EXTERNAL_BLOGCARD_THUMBNAIL_STYLE', 'external_blogcard_thumbnail_style');
if ( !function_exists( 'get_external_blogcard_thumbnail_style' ) ):
function get_external_blogcard_thumbnail_style(){
  return get_theme_option(OP_EXTERNAL_BLOGCARD_THUMBNAIL_STYLE, 'left');
}
endif;

//外部ブログカードを新しいタブで開くか（※廃止予定機能）
define('OP_EXTERNAL_BLOGCARD_TARGET_BLANK', 'external_blogcard_target_blank');
if ( !function_exists( 'is_external_blogcard_target_blank' ) ):
function is_external_blogcard_target_blank(){
  //return false;
  return get_theme_option(OP_EXTERNAL_BLOGCARD_TARGET_BLANK, 1);
}
endif;

//外部ブログカードキャッシュの保存期間
define('OP_EXTERNAL_BLOGCARD_CACHE_RETENTION_PERIOD', 'external_blogcard_cache_retention_period');
if ( !function_exists( 'get_external_blogcard_cache_retention_period' ) ):
function get_external_blogcard_cache_retention_period(){
  return get_theme_option(OP_EXTERNAL_BLOGCARD_CACHE_RETENTION_PERIOD, 365);
}
endif;

//外部ブログカードがリフレッシュモードか
define('OP_EXTERNAL_BLOGCARD_REFRESH_MODE', 'external_blogcard_refresh_mode');
if ( !function_exists( 'is_external_blogcard_refresh_mode' ) ):
function is_external_blogcard_refresh_mode(){
  return get_theme_option(OP_EXTERNAL_BLOGCARD_REFRESH_MODE);
}
endif;
