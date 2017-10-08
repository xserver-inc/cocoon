<?php //全体設定に必要な定数や関数

//サイト幅を揃える
define('OP_ALIGN_SITE_WIDTH', 'align_site_width');
if ( !function_exists( 'is_align_site_width' ) ):
function is_align_site_width(){
  return get_option(OP_ALIGN_SITE_WIDTH);
}
endif;

//サイドバーの表示タイプ
define('OP_align_site_width', 'sidebar_position');
if ( !function_exists( 'get_sidebar_position' ) ):
function get_sidebar_position(){
  return get_option(OP_SIDEBAR_POSITION, 'sidebar_right');
}
endif;

//サイドバーの表示状態の設定
define('OP_SIDEBAR_DISPLAY_TYPE', 'sidebar_display_type');
if ( !function_exists( 'get_sidebar_display_type' ) ):
function get_sidebar_display_type(){
  return get_option(OP_SIDEBAR_DISPLAY_TYPE, 'display_all');
}
endif;

