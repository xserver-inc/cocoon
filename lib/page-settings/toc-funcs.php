<?php //目次設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//目次の表示
define('OP_TOC_VISIBLE', 'toc_visible');
if ( !function_exists( 'is_toc_visible' ) ):
function is_toc_visible(){
  return get_theme_option(OP_TOC_VISIBLE, 1);
}
endif;

//投稿ページで目次の表示
define('OP_SINGLE_TOC_VISIBLE', 'single_toc_visible');
if ( !function_exists( 'is_single_toc_visible' ) ):
function is_single_toc_visible(){
  return get_theme_option(OP_SINGLE_TOC_VISIBLE, 1);
}
endif;

//固定ページで目次の表示
define('OP_PAGE_TOC_VISIBLE', 'page_toc_visible');
if ( !function_exists( 'is_page_toc_visible' ) ):
function is_page_toc_visible(){
  return get_theme_option(OP_PAGE_TOC_VISIBLE, 1);
}
endif;

//カテゴリーページで目次の表示
define('OP_CATEGORY_TOC_VISIBLE', 'category_toc_visible');
if ( !function_exists( 'is_category_toc_visible' ) ):
function is_category_toc_visible(){
  return get_theme_option(OP_CATEGORY_TOC_VISIBLE, 1);
}
endif;

//タグページで目次の表示
define('OP_TAG_TOC_VISIBLE', 'tag_toc_visible');
if ( !function_exists( 'is_tag_toc_visible' ) ):
function is_tag_toc_visible(){
  return get_theme_option(OP_TAG_TOC_VISIBLE, 1);
}
endif;

//目次タイトル
define('OP_TOC_TITLE', 'toc_title');
if ( !function_exists( 'get_toc_title' ) ):
function get_toc_title(){
  return get_theme_option(OP_TOC_TITLE, __( '目次', THEME_NAME ));
}
endif;

//目次の表示切替
define('OP_TOC_TOGGLE_SWITCH_ENABLE', 'toc_toggle_switch_enable');
if ( !function_exists( 'is_toc_toggle_switch_enable' ) ):
function is_toc_toggle_switch_enable(){
  return get_theme_option(OP_TOC_TOGGLE_SWITCH_ENABLE, 1);
}
endif;

//目次を開くキャプション
define('OP_TOC_OPEN_CAPTION', 'toc_open_caption');
if ( !function_exists( 'get_toc_open_caption' ) ):
function get_toc_open_caption(){
  return stripslashes_deep(get_theme_option(OP_TOC_OPEN_CAPTION, __( '開く', THEME_NAME )));
}
endif;

//目次を閉じるキャプション
define('OP_TOC_CLOSE_CAPTION', 'toc_close_caption');
if ( !function_exists( 'get_toc_close_caption' ) ):
function get_toc_close_caption(){
  return stripslashes_deep(get_theme_option(OP_TOC_CLOSE_CAPTION, __( '閉じる', THEME_NAME )));
}
endif;

//目次内容の表示
define('OP_TOC_CONTENT_VISIBLE', 'toc_content_visible');
if ( !function_exists( 'is_toc_content_visible' ) ):
function is_toc_content_visible(){
  return get_theme_option(OP_TOC_CONTENT_VISIBLE, 1);
}
endif;

//目次表示条件（数）
define('OP_TOC_DISPLAY_COUNT', 'toc_display_count');
if ( !function_exists( 'get_toc_display_count' ) ):
function get_toc_display_count(){
  return get_theme_option(OP_TOC_DISPLAY_COUNT, 2);
}
endif;

//目次を表示する深さ
define('OP_TOC_DEPTH', 'toc_depth');
if ( !function_exists( 'get_toc_depth' ) ):
function get_toc_depth(){
  return get_theme_option(OP_TOC_DEPTH, 0);
}
endif;

//目次の数字の表示
define('OP_TOC_NUMBER_TYPE', 'toc_number_type');
if ( !function_exists( 'get_toc_number_type' ) ):
function get_toc_number_type(){
  return get_theme_option(OP_TOC_NUMBER_TYPE, 'number');
}
endif;
if ( !function_exists( 'is_toc_number_visible' ) ):
function is_toc_number_visible(){
  return get_toc_number_type() != 'none';
}
endif;

//目次の中央表示
define('OP_TOC_POSITION_CENTER', 'toc_position_center');
if ( !function_exists( 'is_toc_position_center' ) ):
function is_toc_position_center(){
  return get_theme_option(OP_TOC_POSITION_CENTER, 1);
}
endif;

//目次を広告の手前に表示
define('OP_TOC_BEFORE_ADS', 'toc_before_ads');
if ( !function_exists( 'is_toc_before_ads' ) ):
function is_toc_before_ads(){
  return get_theme_option(OP_TOC_BEFORE_ADS);
}
endif;

//見出し内のHTMLタグを有効にする
define('OP_TOC_HEADING_INNER_HTML_TAG_ENABLE', 'toc_heading_inner_html_tag_enable');
if ( !function_exists( 'is_toc_heading_inner_html_tag_enable' ) ):
function is_toc_heading_inner_html_tag_enable(){
  return get_theme_option(OP_TOC_HEADING_INNER_HTML_TAG_ENABLE);
}
endif;
