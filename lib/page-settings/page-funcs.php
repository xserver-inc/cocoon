<?php //固定ページ設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// コメント
///////////////////////////////////////

//コメント表示
define('OP_PAGE_COMMENT_VISIBLE', 'page_comment_visible');
if ( !function_exists( 'is_page_comment_visible' ) ):
function is_page_comment_visible(){
  return get_theme_option(OP_PAGE_COMMENT_VISIBLE);
}
endif;

///////////////////////////////////////
// パンくずリスト
//////////////////////////////////////

//パンくずリストの位置
define('OP_PAGE_BREADCRUMBS_POSITION', 'page_breadcrumbs_position');
if ( !function_exists( 'get_page_breadcrumbs_position' ) ):
function get_page_breadcrumbs_position(){
  return get_theme_option(OP_PAGE_BREADCRUMBS_POSITION, 'main_bottom');
}
endif;
if ( !function_exists( 'is_page_breadcrumbs_visible' ) ):
function is_page_breadcrumbs_visible(){
  return get_page_breadcrumbs_position() != 'none';
}
endif;
if ( !function_exists( 'is_page_breadcrumbs_position_main_before' ) ):
function is_page_breadcrumbs_position_main_before(){
  return get_page_breadcrumbs_position() == 'main_before';
}
endif;
if ( !function_exists( 'is_page_breadcrumbs_position_main_top' ) ):
function is_page_breadcrumbs_position_main_top(){
  return get_page_breadcrumbs_position() == 'main_top';
}
endif;
if ( !function_exists( 'is_page_breadcrumbs_position_main_bottom' ) ):
function is_page_breadcrumbs_position_main_bottom(){
  return get_page_breadcrumbs_position() == 'main_bottom';
}
endif;
if ( !function_exists( 'is_page_breadcrumbs_position_footer_before' ) ):
function is_page_breadcrumbs_position_footer_before(){
  return get_page_breadcrumbs_position() == 'footer_before';
}
endif;

//パンくずリストに当該記事を含めるか
define('OP_PAGE_BREADCRUMBS_INCLUDE_POST', 'page_breadcrumbs_include_post');
if ( !function_exists( 'is_page_breadcrumbs_include_post' ) ):
function is_page_breadcrumbs_include_post(){
  return get_theme_option(OP_PAGE_BREADCRUMBS_INCLUDE_POST);
}
endif;
