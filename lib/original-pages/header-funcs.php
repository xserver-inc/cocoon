<?php //アクセス解析設定に必要な定数や関数

//サイトロゴ
define('OP_THE_SITE_LOGO_URL', 'the_site_logo_url');
if ( !function_exists( 'get_the_site_logo_url' ) ):
function get_the_site_logo_url(){
  return get_option(OP_THE_SITE_LOGO_URL);
}
endif;

//ヘッダー背景イメージ
define('OP_HEADER_BACKGROUND_IMAGE_URL', 'header_background_image_url');
if ( !function_exists( 'get_header_background_image_url' ) ):
function get_header_background_image_url(){
  return get_option(OP_HEADER_BACKGROUND_IMAGE_URL);
}
endif;

//ヘッダー背景を固定にするか
define('OP_HEADER_BACKGROUND_ATTACHMENT_FIXED', 'header_background_attachment_fixed');
if ( !function_exists( 'is_header_background_attachment_fixed' ) ):
function is_header_background_attachment_fixed(){
  return get_option(OP_HEADER_BACKGROUND_ATTACHMENT_FIXED);
}
endif;

//ヘッダーの種類
define('OP_HEADER_LAYOUT_TYPE', 'header_layout_type');
if ( !function_exists( 'get_header_layout_type' ) ):
function get_header_layout_type(){
  return get_option(OP_HEADER_LAYOUT_TYPE, 'center_logo');
}
endif;

//ヘッダー背景色
define('OP_HEADER_BACKGROUND_COLOR', 'header_background_color');
if ( !function_exists( 'get_header_background_color' ) ):
function get_header_background_color(){
  return get_option(OP_HEADER_BACKGROUND_COLOR, 'transparent');
}
endif;

//ヘッダーテキスト色
define('OP_HEADER_TEXT_COLOR', 'header_text_color');
if ( !function_exists( 'get_header_text_color' ) ):
function get_header_text_color(){
  return get_option(OP_HEADER_TEXT_COLOR, 'transparent');
}
endif;

