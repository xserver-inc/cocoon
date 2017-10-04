<?php //フッター設定に必要な定数や関数

//フッターの表示タイプ
define('OP_FOOTER_DISPLAY_TYPE', 'footer_display_type');
if ( !function_exists( 'get_footer_display_type' ) ):
function get_footer_display_type(){
  return get_option(OP_FOOTER_DISPLAY_TYPE, 'logo_enable');
}
endif;

//サイト開始日
define('OP_SITE_INITIATION_YEAR', 'site_initiation_year');
if ( !function_exists( 'get_site_initiation_year' ) ):
function get_site_initiation_year(){
  return get_option(OP_SITE_INITIATION_YEAR, date('Y'));
}
endif;

//クレジット表記
define('OP_CREDIT_NOTATION', 'credit_notation');
if ( !function_exists( 'get_credit_notation' ) ):
function get_credit_notation(){
  return get_option(OP_CREDIT_NOTATION, 'simple');
}
endif;

//ユーザークレジット表記
define('OP_USER_CREDIT_NOTATION', 'user_credit_notation');
if ( !function_exists( 'get_user_credit_notation' ) ):
function get_user_credit_notation(){
  return get_option(OP_USER_CREDIT_NOTATION);
}
endif;
