<?php //カラム設定に必要な定数や関数
///////////////////////////////////////
// メインカラム
///////////////////////////////////////

//メインカラム幅
define('OP_MAIN_COLUMN_WIDTH', 'main_column_width');
if ( !function_exists( 'get_main_column_width' ) ):
function get_main_column_width(){
  return get_theme_option(OP_MAIN_COLUMN_WIDTH);
}
endif;

//メインカラム外側余白
define('OP_MAIN_COLUMN_MARGIN', 'main_column_margin');
if ( !function_exists( 'get_main_column_margin' ) ):
function get_main_column_margin(){
  return get_theme_option(OP_MAIN_COLUMN_MARGIN);
}
endif;

//メインカラム内側余白
define('OP_MAIN_COLUMN_PADDING', 'main_column_padding');
if ( !function_exists( 'get_main_column_padding' ) ):
function get_main_column_padding(){
  return get_theme_option(OP_MAIN_COLUMN_PADDING);
}
endif;

//メインカラム枠線幅
define('OP_MAIN_COLUMN_BORDER_WIDTH', 'main_column_border_width');
if ( !function_exists( 'get_main_column_border_width' ) ):
function get_main_column_border_width(){
  return get_theme_option(OP_MAIN_COLUMN_BORDER_WIDTH);
}
endif;

//メインカラム枠線色
define('OP_MAIN_COLUMN_BORDER_COLOR', 'main_column_border_color');
if ( !function_exists( 'get_main_column_border_color' ) ):
function get_main_column_border_color(){
  return get_theme_option(OP_MAIN_COLUMN_BORDER_COLOR);
}
endif;

///////////////////////////////////////
// サイドバー
///////////////////////////////////////

//サイドバー幅
define('OP_SIDEBAR_WIDTH', 'sidebar_width');
if ( !function_exists( 'get_sidebar_width' ) ):
function get_sidebar_width(){
  return get_theme_option(OP_SIDEBAR_WIDTH);
}
endif;

//サイドバー外側余白
define('OP_SIDEBAR_MARGIN', 'sidebar_margin');
if ( !function_exists( 'get_sidebar_margin' ) ):
function get_sidebar_margin(){
  return get_theme_option(OP_SIDEBAR_MARGIN);
}
endif;

//サイドバー内側余白
define('OP_SIDEBAR_PADDING', 'sidebar_padding');
if ( !function_exists( 'get_sidebar_padding' ) ):
function get_sidebar_padding(){
  return get_theme_option(OP_SIDEBAR_PADDING);
}
endif;

//サイドバー枠線幅
define('OP_SIDEBAR_BORDER_WIDTH', 'sidebar_border_width');
if ( !function_exists( 'get_sidebar_border_width' ) ):
function get_sidebar_border_width(){
  return get_theme_option(OP_SIDEBAR_BORDER_WIDTH);
}
endif;

//サイドバー枠線色
define('OP_SIDEBAR_BORDER_COLOR', 'sidebar_border_color');
if ( !function_exists( 'get_sidebar_border_color' ) ):
function get_sidebar_border_color(){
  return get_theme_option(OP_SIDEBAR_BORDER_COLOR);
}
endif;