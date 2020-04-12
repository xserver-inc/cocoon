<?php //全体設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//サイトキーカラー
define('OP_SITE_KEY_COLOR', 'site_key_color');
if ( !function_exists( 'get_site_key_color' ) ):
function get_site_key_color($default = null){
  return get_theme_option(OP_SITE_KEY_COLOR, $default);
}
endif;

//サイトキーテキストカラー
define('OP_SITE_KEY_TEXT_COLOR', 'site_key_text_color');
if ( !function_exists( 'get_site_key_text_color' ) ):
function get_site_key_text_color(){
  return get_theme_option(OP_SITE_KEY_TEXT_COLOR);
}
endif;

//フォント
define('OP_SITE_FONT_FAMILY', 'site_font_family');
if ( !function_exists( 'get_site_font_family' ) ):
function get_site_font_family(){
  return get_theme_option(OP_SITE_FONT_FAMILY, 'hiragino');
}
endif;
if ( !function_exists( 'is_site_font_family_local' ) ):
function is_site_font_family_local(){
  switch (get_site_font_family()) {
    case 'meiryo':
    case 'yu_gothic':
    case 'hiragino':
    case 'ms_pgothic':
      return true;
      break;
  }
}
endif;

//フォントサイズ
define('OP_SITE_FONT_SIZE', 'site_font_size');
if ( !function_exists( 'get_site_font_size' ) ):
function get_site_font_size(){
  return get_theme_option(OP_SITE_FONT_SIZE, '18px');
}
endif;

//フォントサイズ
define('OP_MOBILE_SITE_FONT_SIZE', 'mobile_site_font_size');
if ( !function_exists( 'get_mobile_site_font_size' ) ):
function get_mobile_site_font_size(){
  return get_theme_option(OP_MOBILE_SITE_FONT_SIZE, '16px');
}
endif;

//サイト文字色
define('OP_SITE_TEXT_COLOR', 'site_text_color');
if ( !function_exists( 'get_site_text_color' ) ):
function get_site_text_color(){
  return get_theme_option(OP_SITE_TEXT_COLOR);
}
endif;

//フォントウエイト
define('OP_SITE_FONT_WEIGHT', 'site_font_weight');
if ( !function_exists( 'get_site_font_weight' ) ):
function get_site_font_weight(){
  return get_theme_option(OP_SITE_FONT_WEIGHT, 400);
}
endif;

//サイトアイコンフォント
define('OP_SITE_ICON_FONT', 'site_icon_font');
if ( !function_exists( 'get_site_icon_font' ) ):
function get_site_icon_font(){
  return get_theme_option(OP_SITE_ICON_FONT, 'font_awesome_4');
}
endif;
if ( !function_exists( 'is_site_icon_font_font_awesome_4' ) ):
function is_site_icon_font_font_awesome_4(){
  return get_site_icon_font() == 'font_awesome_4';
}
endif;
if ( !function_exists( 'is_site_icon_font_font_awesome_5' ) ):
function is_site_icon_font_font_awesome_5(){
  return get_site_icon_font() == 'font_awesome_5';
}
endif;
if ( !function_exists( 'get_site_icon_font_class' ) ):
function get_site_icon_font_class(){
  return str_replace('_', '-', get_site_icon_font());
}
endif;

//サイト背景色
define('OP_SITE_BACKGROUND_COLOR', 'site_background_color');
if ( !function_exists( 'get_site_background_color' ) ):
function get_site_background_color(){
  return get_theme_option(OP_SITE_BACKGROUND_COLOR);
}
endif;

//サイト背景画像
define('OP_SITE_BACKGROUND_IMAGE_URL', 'site_background_image_url');
if ( !function_exists( 'get_site_background_image_url' ) ):
function get_site_background_image_url(){
  return get_theme_option(OP_SITE_BACKGROUND_IMAGE_URL);
}
endif;

//サイトリンク色
define('OP_SITE_LINK_COLOR', 'site_link_color');
if ( !function_exists( 'get_site_link_color' ) ):
function get_site_link_color(){
  return get_theme_option(OP_SITE_LINK_COLOR);
}
endif;

//サイト選択文字色
define('OP_SITE_SELECTION_COLOR', 'site_selection_color');
if ( !function_exists( 'get_site_selection_color' ) ):
function get_site_selection_color(){
  return get_theme_option(OP_SITE_SELECTION_COLOR);
}
endif;

//サイト選択文字背景色
define('OP_SITE_SELECTION_BACKGROUND_COLOR', 'site_selection_background_color');
if ( !function_exists( 'get_site_selection_background_color' ) ):
function get_site_selection_background_color(){
  return get_theme_option(OP_SITE_SELECTION_BACKGROUND_COLOR);
}
endif;

//サイト幅を揃える
define('OP_ALIGN_SITE_WIDTH', 'align_site_width');
if ( !function_exists( 'is_align_site_width' ) ):
function is_align_site_width(){
  return get_theme_option(OP_ALIGN_SITE_WIDTH);
}
endif;

//サイドバーの表示タイプ
define('OP_SIDEBAR_POSITION', 'sidebar_position');
if ( !function_exists( 'get_sidebar_position' ) ):
function get_sidebar_position(){
  return get_theme_option(OP_SIDEBAR_POSITION, 'sidebar_right');
}
endif;
if ( !function_exists( 'is_sidebar_position_right' ) ):
function is_sidebar_position_right(){
  return get_sidebar_position() == 'sidebar_right';
}
endif;

//サイドバーの表示状態の設定
define('OP_SIDEBAR_DISPLAY_TYPE', 'sidebar_display_type');
if ( !function_exists( 'get_sidebar_display_type' ) ):
function get_sidebar_display_type(){
  return get_theme_option(OP_SIDEBAR_DISPLAY_TYPE, 'display_all');
}
endif;

//サイトアイコン
define('OP_SITE_ICON_URL', 'site_icon_url');
//WordPressデフォルトのget_site_icon_url関数とかぶるため名前変更
if ( !function_exists( 'get_site_icon_url2' ) ):
function get_site_icon_url2(){
  //return get_theme_option(OP_SITE_ICON_URL, get_default_site_icon_url());
  return ;
}
endif;

if ( !function_exists( 'get_default_site_icon_url' ) ):
function get_default_site_icon_url(){
  return get_template_directory_uri().'/images/site-icon.png';
}
endif;

//デフォルトサイトアイコンの設定
if (!get_site_icon_url()) {//カスタマイザーでサイトアイコンが設定されてないとき
  add_action( 'wp_head', 'add_default_site_icon_tag' );
  add_action( 'admin_print_styles', 'add_default_site_icon_tag' );
}
if ( !function_exists( 'add_default_site_icon_tag' ) ):
function add_default_site_icon_tag(){
  $tag = '<!-- '.THEME_NAME_CAMEL.' site icon -->'.PHP_EOL;
  $tag .= '<link rel="icon" href="'.DEFAULT_SITE_ICON_32.'" sizes="32x32" />'.PHP_EOL;
  $tag .= '<link rel="icon" href="'.DEFAULT_SITE_ICON_192.'" sizes="192x192" />'.PHP_EOL;
  $tag .= '<link rel="apple-touch-icon" href="'.DEFAULT_SITE_ICON_180.'" />'.PHP_EOL;
  $tag .= '<meta name="msapplication-TileImage" content="'.DEFAULT_SITE_ICON_270.'" />'.PHP_EOL;
  echo apply_filters('add_default_site_icon_tag', $tag);
}
endif;

// //サイトアイコンの設定
// add_action( 'wp_head', 'the_site_icon_tag' );
// add_action( 'admin_print_styles', 'the_site_icon_tag' );
// if ( !function_exists( 'the_site_icon_tag' ) ):
// function the_site_icon_tag(){
//   if (get_site_icon_url()) {
//     $url = get_site_icon_url();
//   } else {
//     $url = get_default_site_icon_url();
//   }
//   if ($url) {
//     $tag = '<!-- '.THEME_NAME_CAMEL.' shortcut icon -->'.PHP_EOL;
//     $tag .= '<link rel="shortcut icon" href="'.$url.'">'.PHP_EOL;
//     echo $tag;
//   }
// }
// endif;

// //サイトアイコン出力の変更
// add_filter('site_icon_meta_tags', 'filter_site_icon_meta_tags');
// function filter_site_icon_meta_tags($meta_tags) {
//   //_v(empty($meta_tags));
//   if (empty($meta_tags)) {
//     $url = get_default_site_icon_url();
//     $meta_tags = array(
//       '<link rel="icon" href="'.$url.'">',
//       '<link rel="apple-touch-icon" href="'.$url.'">',
//       '<meta name="msapplication-TileImage" content="'.$url.'">',
//     );
//    //_v($meta_tags);
//   }
//   return $meta_tags;
// }


//全てのサムネイル表示の設定
define('OP_ALL_THUMBNAIL_VISIBLE', 'all_thumbnail_visible');
if ( !function_exists( 'is_all_thumbnail_visible' ) ):
function is_all_thumbnail_visible(){
  return get_theme_option(OP_ALL_THUMBNAIL_VISIBLE, 1);
}
endif;

//日付フォーマット
define('OP_SITE_DATE_FORMAT', 'site_date_format');
if ( !function_exists( 'get_site_date_format' ) ):
function get_site_date_format(){
  return get_theme_option(OP_SITE_DATE_FORMAT, __( 'Y.m.d', THEME_NAME ));
}
endif;
