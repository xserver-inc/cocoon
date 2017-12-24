<?php //カラム設定に必要な定数や関数
///////////////////////////////////////
// メインカラム
///////////////////////////////////////

//メインカラム幅
define('OP_MAIN_COLUMN_CONTENTS_WIDTH', 'main_column_contents_width');
if ( !function_exists( 'get_main_column_contents_width' ) ):
function get_main_column_contents_width(){
  return get_theme_option(OP_MAIN_COLUMN_CONTENTS_WIDTH);
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
define('OP_SIDEBAR_CONTENTS_WIDTH', 'sidebar_contents_width');
if ( !function_exists( 'get_sidebar_contents_width' ) ):
function get_sidebar_contents_width(){
  return get_theme_option(OP_SIDEBAR_CONTENTS_WIDTH);
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

//メインカラムとサイドバーの間隔
define('OP_MAIN_SIDEBAR_MARGIN', 'main_sidebar_margin');
if ( !function_exists( 'get_main_sidebar_margin' ) ):
function get_main_sidebar_margin(){
  return get_theme_option(OP_MAIN_SIDEBAR_MARGIN);
}
endif;

//カラムに変更があったか
if ( !function_exists( 'is_clumns_changed' ) ):
function is_clumns_changed(){
  return get_main_column_contents_width() ||
         get_main_column_padding() ||
         get_main_column_border_width() ||
         get_sidebar_contents_width() ||
         get_sidebar_padding() ||
         get_sidebar_border_width() ||
         get_main_sidebar_margin();
}
endif;

if ( !function_exists( 'get_main_column_width' ) ):
function get_main_column_width(){
  $main_column_contents_width = get_main_column_contents_width() ? get_main_column_contents_width() : 800;
  $main_column_padding = get_main_column_padding() ? get_main_column_padding() : 29;
  $main_column_border_width = get_main_column_border_width() ? get_main_column_border_width() : 1;
  return intval($main_column_contents_width) +
         (intval($main_column_padding) * 2) +
         (intval($main_column_border_width) * 2);
}
endif;

if ( !function_exists( 'get_sidebar_width' ) ):
function get_sidebar_width(){
  $sidebar_contents_width = get_sidebar_contents_width() ? get_sidebar_contents_width() : 336;
  $sidebar_padding = get_sidebar_padding() ? get_sidebar_padding() : 9;
  $sidebar_border_width = get_sidebar_border_width() ? get_sidebar_border_width() : 1;
  return intval($sidebar_contents_width) +
         (intval($sidebar_padding) * 2) +
         (intval($sidebar_border_width) * 2);
}
endif;

if ( !function_exists( 'get_site_wrap_width' ) ):
function get_site_wrap_width(){
  // _v(get_main_column_width());
  // _v(get_sidebar_width());
  $main_sidebar_margin = get_main_sidebar_margin() ? get_main_sidebar_margin() : 20;
  return get_main_column_width() +
         get_sidebar_width() +
         intval($main_sidebar_margin) + 2;
}
endif;