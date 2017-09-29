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

//ヘッダーの種類
define('OP_HEADER_TYPE', 'header_type');
if ( !function_exists( 'get_header_type' ) ):
function get_header_type(){
  return get_option(OP_HEADER_TYPE);
}
endif;
