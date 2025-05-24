<?php //404ページ設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//404ページ画像
define('OP_404_IMAGE_URL', '404_image_url');
if ( !function_exists( 'get_404_image_url' ) ):
function get_404_image_url(){
  $url = get_theme_option(OP_404_IMAGE_URL, get_default_404_image_url());
  $url = trim($url);
  if (empty($url)) {
    $url = get_default_404_image_url();
  }
  return apply_filters('get_404_image_url', $url);
}
endif;
if ( !function_exists( 'get_default_404_image_url' ) ):
function get_default_404_image_url(){
  return get_cocoon_template_directory_uri().'/images/404.png';
}
endif;

//404ページタイトル
define('OP_404_PAGE_TITLE', '404_page_title');
if ( !function_exists( 'get_404_page_title' ) ):
function get_404_page_title(){
  return stripslashes_deep(get_theme_option(OP_404_PAGE_TITLE, T404_PAGE_TITLE));
}
endif;

//404ページメッセージ
define('OP_404_PAGE_MESSAGE', '404_page_message');
if ( !function_exists( 'get_404_page_message' ) ):
function get_404_page_message(){
  return stripslashes_deep(get_theme_option(OP_404_PAGE_MESSAGE, T404_PAGE_MESSAGE));
}
endif;
