<?php //SNS設定に必要な定数や関数


///////////////////////////////////////
// SNSシェアボタンの設定
///////////////////////////////////////

//シェアボタンの表示
define('OP_SNS_SHARE_BUTTONS_VISIBLE', 'sns_share_buttons_visible');
if ( !function_exists( 'is_sns_share_buttons_visible' ) ):
function is_sns_share_buttons_visible(){
  return get_option(OP_SNS_SHARE_BUTTONS_VISIBLE, 1);
}
endif;


//SNSシェァメッセージ
define('OP_SNS_SHARE_MESSAGE', 'sns_share_message');
if ( !function_exists( 'get_sns_share_message' ) ):
function get_sns_share_message(){
  return get_option(OP_SNS_SHARE_MESSAGE, 'シェァする');
}
endif;

//Twitterシェアボタンの表示
define('OP_TWITTER_SHARE_BUTTON_VISIBLE', 'twitter_share_button_visible');
if ( !function_exists( 'is_twitter_share_button_visible' ) ):
function is_twitter_share_button_visible(){
  return get_option(OP_TWITTER_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//Facebookシェアボタンの表示
define('OP_FACEBOOK_SHARE_BUTTON_VISIBLE', 'facebook_share_button_visible');
if ( !function_exists( 'is_facebook_share_button_visible' ) ):
function is_facebook_share_button_visible(){
  return get_option(OP_FACEBOOK_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//はてなブックマークシェアボタンの表示
define('OP_HATEBU_SHARE_BUTTON_VISIBLE', 'hatebu_share_button_visible');
if ( !function_exists( 'is_hatebu_share_button_visible' ) ):
function is_hatebu_share_button_visible(){
  return get_option(OP_HATEBU_SHARE_BUTTON_VISIBLE, 1);
}
endif;

// //デフォルトフォローユーザー
// define('OP_SNS_DEFAULT_FOLLOW_USER', 'sns_default_follow_user');
// if ( !function_exists( 'get_sns_default_follow_user' ) ):
// function get_sns_default_follow_user(){
//   return get_option(OP_SNS_DEFAULT_FOLLOW_USER, wp_get_current_user()->ID);
// }
// endif;
