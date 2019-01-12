<?php //フッター設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//フッターカラー
define('OP_FOOTER_BACKGROUND_COLOR', 'footer_background_color');
if ( !function_exists( 'get_footer_background_color' ) ):
function get_footer_background_color(){
  return get_theme_option(OP_FOOTER_BACKGROUND_COLOR);
}
endif;

//フッターテキストカラー
define('OP_FOOTER_TEXT_COLOR', 'footer_text_color');
if ( !function_exists( 'get_footer_text_color' ) ):
function get_footer_text_color(){
  return get_theme_option(OP_FOOTER_TEXT_COLOR);
}
endif;

//フッターの表示タイプ
define('OP_FOOTER_DISPLAY_TYPE', 'footer_display_type');
if ( !function_exists( 'get_footer_display_type' ) ):
function get_footer_display_type(){
  return get_theme_option(OP_FOOTER_DISPLAY_TYPE, 'logo_enable');
}
endif;

//フッターロゴ
define('OP_FOOTER_LOGO_URL', 'footer_logo_url');
if ( !function_exists( 'get_footer_logo_url' ) ):
function get_footer_logo_url(){
  return get_theme_option(OP_FOOTER_LOGO_URL, '');
}
endif;

//サイト開始日
define('OP_SITE_INITIATION_YEAR', 'site_initiation_year');
if ( !function_exists( 'get_site_initiation_year' ) ):
function get_site_initiation_year(){
  return get_theme_option(OP_SITE_INITIATION_YEAR, get_first_post_year() ? get_first_post_year() : date_i18n('Y'));
}
endif;

//著作権者表記
define('OP_COPYRIGHT_NAME', 'copyright_name');
if ( !function_exists( 'get_copyright_name' ) ):
function get_copyright_name(){
  return stripslashes_deep(get_theme_option(OP_COPYRIGHT_NAME));
}
endif;

//クレジット表記
define('OP_CREDIT_NOTATION', 'credit_notation');
if ( !function_exists( 'get_credit_notation' ) ):
function get_credit_notation(){
  return get_theme_option(OP_CREDIT_NOTATION, 'simple');
}
endif;

//ユーザークレジット表記
define('OP_USER_CREDIT_NOTATION', 'user_credit_notation');
if ( !function_exists( 'get_user_credit_notation' ) ):
function get_user_credit_notation(){
  return stripslashes_deep(get_theme_option(OP_USER_CREDIT_NOTATION));
}
endif;

//著作権者名表記の取得
if ( !function_exists( 'get_copyright_display_name' ) ):
function get_copyright_display_name(){
  if ($copyright_name = get_copyright_name()) {
    return $copyright_name;
  } else {
    return get_bloginfo('name');
  }
}
endif;

//サイトクレジット表記取得関数
if ( !function_exists( 'get_the_site_credit' ) ):
function get_the_site_credit(){
  $credit = null;
  switch (get_credit_notation()) {
    case 'simple':
      $credit = '© '.get_site_initiation_year().' '.get_copyright_display_name().'.';
      break;
    // case 'simple_year':
    //   $credit = '© '.get_site_initiation_year().' '.get_copyright_display_name().'.';
    //   break;
    case 'simple_year_begin_to_now':
      $credit = '© '.get_site_initiation_year().'-'.date_i18n('Y').' '.get_copyright_display_name().'.';
      break;
    case 'full':
      $credit = 'Copyright © '.get_site_initiation_year().' '.get_copyright_display_name().' All Rights Reserved.';
      break;
    case 'full_year_begin_to_now':
      $credit = 'Copyright © '.get_site_initiation_year().'-'.date_i18n('Y').' '.get_copyright_display_name().' All Rights Reserved.';
      break;
    default:
      $credit = get_user_credit_notation();
      break;
  }
  return $credit;
}
endif;

//フッターメニュー幅
define('OP_FOOTER_NAVI_MENU_WIDTH', 'footer_navi_menu_width');
if ( !function_exists( 'get_footer_navi_menu_width' ) ):
function get_footer_navi_menu_width(){
  return get_theme_option(OP_FOOTER_NAVI_MENU_WIDTH);
}
endif;

//フッターメニュー幅をテキストの幅にする
define('OP_FOOTER_NAVI_MENU_TEXT_WIDTH_ENABLE', 'footer_navi_menu_text_width_enable');
if ( !function_exists( 'is_footer_navi_menu_text_width_enable' ) ):
function is_footer_navi_menu_text_width_enable(){
  return get_theme_option(OP_FOOTER_NAVI_MENU_TEXT_WIDTH_ENABLE, 1);
}
endif;
