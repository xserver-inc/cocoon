<?php //404ページ設定に必要な定数や関数

//404ページ画像
define('OP_404_IMAGE_URL', '404_image_url');
if ( !function_exists( 'get_404_image_url' ) ):
function get_404_image_url(){
  return get_theme_option(OP_404_IMAGE_URL, get_default_404_image_url());
}
endif;
if ( !function_exists( 'get_default_404_image_url' ) ):
function get_default_404_image_url(){
  return get_template_directory_uri().'/images/404.png';
}
endif;
