<?php //目次設定に必要な定数や関数

//目次の表示
define('OP_TOC_VISIBLE', 'toc_visible');
if ( !function_exists( 'is_toc_visible' ) ):
function is_toc_visible(){
  return get_theme_option(OP_TOC_VISIBLE, 1);
}
endif;

//目次タイトル
define('OP_TOC_TITLE', 'toc_title');
if ( !function_exists( 'get_toc_title' ) ):
function get_toc_title(){
  return get_theme_option(OP_TOC_TITLE, __( '目次', THEME_NAME ));
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

//目次を広告の手前に表示
define('OP_TOC_BEFORE_ADS', 'toc_before_ads');
if ( !function_exists( 'is_toc_before_ads' ) ):
function is_toc_before_ads(){
  return get_theme_option(OP_TOC_BEFORE_ADS);
}
endif;
