<?php //フッター設定に必要な定数や関数

//フッターの表示タイプ
define('OP_FOOTER_DISPLAY_TYPE', 'footer_display_type');
if ( !function_exists( 'get_footer_display_type' ) ):
function get_footer_display_type(){
  return get_theme_option(OP_FOOTER_DISPLAY_TYPE, 'logo_enable');
}
endif;

//サイト開始日
define('OP_SITE_INITIATION_YEAR', 'site_initiation_year');
if ( !function_exists( 'get_site_initiation_year' ) ):
function get_site_initiation_year(){
  return get_theme_option(OP_SITE_INITIATION_YEAR, get_first_post_year() ? get_first_post_year() : date('Y'));
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

//サイトクレジット表記取得関数
if ( !function_exists( 'get_the_site_credit' ) ):
function get_the_site_credit(){
  $credit = null;
  switch (get_credit_notation()) {
    case 'simple':
      $credit = '© '.get_site_initiation_year().' '.get_bloginfo('name').'.';
      break;
    // case 'simple_year':
    //   $credit = '© '.get_site_initiation_year().' '.get_bloginfo('name').'.';
    //   break;
    case 'simple_year_begin_to_now':
      $credit = '© '.get_site_initiation_year().'-'.date('Y').' '.get_bloginfo('name').'.';
      break;
    case 'full':
      $credit = 'Copyright © '.get_site_initiation_year().' '.get_bloginfo('name').' All Rights Reserved.';
      break;
    case 'full_year_begin_to_now':
      $credit = 'Copyright © '.get_site_initiation_year().'-'.date('Y').' '.get_bloginfo('name').' All Rights Reserved.';
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
  return get_theme_option(OP_FOOTER_NAVI_MENU_TEXT_WIDTH_ENABLE);
}
endif;