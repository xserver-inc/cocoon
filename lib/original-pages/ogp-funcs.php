<?php //OGP設定に必要な定数や関数

//Facebook OGPを有効
define('OP_FACEBOOK_OGP_ENABLE', 'facebook_ogp_enable');
if ( !function_exists( 'is_facebook_ogp_enable' ) ):
function is_facebook_ogp_enable(){
  return get_option(OP_FACEBOOK_OGP_ENABLE, 1);
}
endif;

//Facebook App-ID
define('OP_FACEBOOK_APP_ID', 'facebook_app_id');
if ( !function_exists( 'get_facebook_app_id' ) ):
function get_facebook_app_id(){
  return get_option(OP_FACEBOOK_APP_ID);
}
endif;

//Twitterカードを有効
define('OP_TWITTER_CARD_ENABLE', 'twitter_card_enable');
if ( !function_exists( 'is_twitter_card_enable' ) ):
function is_twitter_card_enable(){
  return get_option(OP_TWITTER_CARD_ENABLE, 1);
}
endif;

//Twitterカードタイプ
define('OP_TWITTER_CARD_TYPE', 'twitter_card_type');
if ( !function_exists( 'get_twitter_card_type' ) ):
function get_twitter_card_type(){
  return get_option(OP_TWITTER_CARD_TYPE, 'summary');
}
endif;

