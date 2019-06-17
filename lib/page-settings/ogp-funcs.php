<?php //OGP設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Facebook OGPを有効
define('OP_FACEBOOK_OGP_ENABLE', 'facebook_ogp_enable');
if ( !function_exists( 'is_facebook_ogp_enable' ) ):
function is_facebook_ogp_enable(){
  return get_theme_option(OP_FACEBOOK_OGP_ENABLE, 1);
}
endif;

//Facebook App-ID
define('OP_FACEBOOK_APP_ID', 'facebook_app_id');
if ( !function_exists( 'get_facebook_app_id' ) ):
function get_facebook_app_id(){
  return get_theme_option(OP_FACEBOOK_APP_ID);
}
endif;

//Twitterカードを有効
define('OP_TWITTER_CARD_ENABLE', 'twitter_card_enable');
if ( !function_exists( 'is_twitter_card_enable' ) ):
function is_twitter_card_enable(){
  return get_theme_option(OP_TWITTER_CARD_ENABLE, 1);
}
endif;

//Twitterカードタイプ
define('OP_TWITTER_CARD_TYPE', 'twitter_card_type');
if ( !function_exists( 'get_twitter_card_type' ) ):
function get_twitter_card_type(){
  return get_theme_option(OP_TWITTER_CARD_TYPE, 'summary_large_image');
}
endif;


//ホームイメージ
define('OP_OGP_HOME_IMAGE_URL', 'ogp_home_image_url');
if ( !function_exists( 'get_ogp_home_image_url' ) ):
function get_ogp_home_image_url(){
  $def_url = get_template_directory_uri().'/screenshot.jpg';
  $url = get_theme_option(OP_OGP_HOME_IMAGE_URL, $def_url);
  $url = trim($url);
  if (empty($url)) {
    $url = $def_url;
  }
  return apply_filters('get_ogp_home_image_url', $url);
}
endif;

