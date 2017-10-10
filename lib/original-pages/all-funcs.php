<?php //全体設定に必要な定数や関数

//フォント
define('OP_SITE_FONT_FAMILY', 'site_font_family');
if ( !function_exists( 'get_site_font_family' ) ):
function get_site_font_family(){
  return get_option(OP_SITE_FONT_FAMILY, 'yu_gothic');
}
endif;
if ( !function_exists( 'is_site_font_family_local' ) ):
function is_site_font_family_local(){
  switch (get_site_font_family()) {
    case 'yu_gothic':
    case 'meiryo':
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
  return get_option(OP_SITE_FONT_SIZE, '18px');
}
endif;

//サイト背景色
define('OP_SITE_BACKGROUND_COLOR', 'site_background_color');
if ( !function_exists( 'get_site_background_color' ) ):
function get_site_background_color(){
  return get_option(OP_SITE_BACKGROUND_COLOR);
}
endif;

//サイト背景画像
define('OP_SITE_BACKGROUND_IMAGE_URL', 'site_background_image_url');
if ( !function_exists( 'get_site_background_image_url' ) ):
function get_site_background_image_url(){
  return get_option(OP_SITE_BACKGROUND_IMAGE_URL);
}
endif;

//サイト幅を揃える
define('OP_ALIGN_SITE_WIDTH', 'align_site_width');
if ( !function_exists( 'is_align_site_width' ) ):
function is_align_site_width(){
  return get_option(OP_ALIGN_SITE_WIDTH);
}
endif;

//サイドバーの表示タイプ
define('OP_SIDEBAR_POSITION', 'sidebar_position');
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

//サイトアイコン
define('OP_SITE_ICON_URL', 'site_icon_url');
//Wordpressデフォルトのget_site_icon_url関数とかぶるため名前変更
if ( !function_exists( 'get_site_icon_url2' ) ):
function get_site_icon_url2(){
  return get_option(OP_SITE_ICON_URL, get_template_directory_uri().'/images/site-icon.png');
}
endif;


