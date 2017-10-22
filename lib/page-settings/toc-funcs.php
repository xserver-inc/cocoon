<?php //目次設定に必要な定数や関数

//目次の表示
define('OP_TOC_VISIBLE', 'toc_visible');
if ( !function_exists( 'is_toc_visible' ) ):
function is_toc_visible(){
  return get_theme_option(OP_TOC_VISIBLE);
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
  return get_theme_option(OP_TOC_DEPTH, 2);
}
endif;

//目次の数字の表示
define('OP_TOC_NUMBER_VISIBLE', 'toc_number_visible');
if ( !function_exists( 'is_toc_number_visible' ) ):
function is_toc_number_visible(){
  return get_theme_option(OP_TOC_NUMBER_VISIBLE, 1);
}
endif;
