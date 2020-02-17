<?php //ヘッダー設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ヘッダーの種類
define('OP_HEADER_LAYOUT_TYPE', 'header_layout_type');
if ( !function_exists( 'get_header_layout_type' ) ):
function get_header_layout_type(){
  return get_theme_option(OP_HEADER_LAYOUT_TYPE, 'center_logo');
}
endif;
if ( !function_exists( 'get_header_layout_type_top' ) ):
function get_header_layout_type_top(){
  switch (get_header_layout_type()) {
    case 'top_menu':
    case 'top_menu_right':
    case 'top_menu_small':
    case 'top_menu_small_right':
      return true;
      break;
  }
  return false;
}
endif;
if ( !function_exists( 'get_header_layout_type_center_logo' ) ):
function get_header_layout_type_center_logo(){
  return !get_header_layout_type_top();
}
endif;

//ヘッダーの固定
define('OP_HEADER_FIXED', 'header_fixed');
if ( !function_exists( 'is_header_fixed' ) ):
function is_header_fixed(){
  return get_theme_option(OP_HEADER_FIXED);
}
endif;

//ヘッダーの高さ
define('OP_HEADER_AREA_HEIGHT', 'header_area_height');
if ( !function_exists( 'get_header_area_height' ) ):
function get_header_area_height(){
  return get_theme_option(OP_HEADER_AREA_HEIGHT);
}
endif;

//モバイルヘッダーの高さ
define('OP_MOBILE_HEADER_AREA_HEIGHT', 'mobile_header_area_height');
if ( !function_exists( 'get_mobile_header_area_height' ) ):
function get_mobile_header_area_height(){
  return get_theme_option(OP_MOBILE_HEADER_AREA_HEIGHT);
}
endif;

//サイトロゴ
define('OP_THE_SITE_LOGO_URL', 'the_site_logo_url');
if ( !function_exists( 'get_the_site_logo_url' ) ):
function get_the_site_logo_url(){
  return get_theme_option(OP_THE_SITE_LOGO_URL, '');
}
endif;

//サイトロゴ幅
define('OP_THE_SITE_LOGO_WIDTH', 'the_site_logo_width');
if ( !function_exists( 'get_the_site_logo_width' ) ):
function get_the_site_logo_width(){
  return get_theme_option(OP_THE_SITE_LOGO_WIDTH);
}
endif;

//サイトロゴ高さ
define('OP_THE_SITE_LOGO_HEIGHT', 'the_site_logo_height');
if ( !function_exists( 'get_the_site_logo_height' ) ):
function get_the_site_logo_height(){
  return get_theme_option(OP_THE_SITE_LOGO_HEIGHT);
}
endif;

//キャッチフレーズ位置
define('OP_TAGLINE_POSITION', 'tagline_position');
if ( !function_exists( 'get_tagline_position' ) ):
function get_tagline_position(){
  return get_theme_option(OP_TAGLINE_POSITION, 'header_top');
}
endif;
if ( !function_exists( 'is_tagline_visible' ) ):
function is_tagline_visible(){
  return get_tagline_position() != 'none';
}
endif;
if ( !function_exists( 'is_tagline_position_header_top' ) ):
function is_tagline_position_header_top(){
  return get_tagline_position() == 'header_top';
}
endif;
if ( !function_exists( 'is_tagline_position_header_bottom' ) ):
function is_tagline_position_header_bottom(){
  return get_tagline_position() == 'header_bottom';
}
endif;

//ヘッダー背景イメージ
define('OP_HEADER_BACKGROUND_IMAGE_URL', 'header_background_image_url');
if ( !function_exists( 'get_header_background_image_url' ) ):
function get_header_background_image_url(){
  return get_theme_option(OP_HEADER_BACKGROUND_IMAGE_URL, '');
}
endif;

//ヘッダー背景を固定にするか
define('OP_HEADER_BACKGROUND_ATTACHMENT_FIXED', 'header_background_attachment_fixed');
if ( !function_exists( 'is_header_background_attachment_fixed' ) ):
function is_header_background_attachment_fixed(){
  return get_theme_option(OP_HEADER_BACKGROUND_ATTACHMENT_FIXED);
}
endif;

//ヘッダー全体の背景色
define('OP_HEADER_CONTAINER_BACKGROUND_COLOR', 'header_container_background_color');
if ( !function_exists( 'get_header_container_background_color' ) ):
function get_header_container_background_color(){
  return get_theme_option(OP_HEADER_CONTAINER_BACKGROUND_COLOR);
}
endif;

//ヘッダー全体の文字色
define('OP_HEADER_CONTAINER_TEXT_COLOR', 'header_container_text_color');
if ( !function_exists( 'get_header_container_text_color' ) ):
function get_header_container_text_color(){
  return get_theme_option(OP_HEADER_CONTAINER_TEXT_COLOR);
}
endif;

//ヘッダー背景色
define('OP_HEADER_BACKGROUND_COLOR', 'header_background_color');
if ( !function_exists( 'get_header_background_color' ) ):
function get_header_background_color(){
  return get_theme_option(OP_HEADER_BACKGROUND_COLOR);
}
endif;

//ヘッダーテキスト色
define('OP_HEADER_TEXT_COLOR', 'header_text_color');
if ( !function_exists( 'get_header_text_color' ) ):
function get_header_text_color(){
  return get_theme_option(OP_HEADER_TEXT_COLOR);
}
endif;
