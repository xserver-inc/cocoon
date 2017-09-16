<?php //SNS設定に必要な定数や関数


///////////////////////////////////////
// SNSフォローボタンの設定
///////////////////////////////////////
//本文下のフォローボタンの表示
define('OP_SNS_FOLLOW_BUTTONS_VISIBLE', 'sns_follow_buttons_visible');
if ( !function_exists( 'is_sns_follow_buttons_visible' ) ):
function is_sns_follow_buttons_visible(){
  return get_option(OP_SNS_FOLLOW_BUTTONS_VISIBLE, 1);
}
endif;


//SNSフォローメッセージ
define('OP_SNS_FOLLOW_MESSAGE', 'SNS_FOLLOW_MESSAGE');
if ( !function_exists( 'get_sns_follow_message' ) ):
function get_sns_follow_message(){
  return get_option(OP_SNS_FOLLOW_MESSAGE, __( '%sをフォローする', THEME_NAME ));
}
endif;

//feedlyフォローボタンの表示
define('OP_FEEDLY_FOLLOW_BUTTON_VISIBLE', 'feedly_follow_button_visible');
if ( !function_exists( 'is_feedly_follow_button_visible' ) ):
function is_feedly_follow_button_visible(){
  return get_option(OP_FEEDLY_FOLLOW_BUTTON_VISIBLE, 1);
}
endif;

//RSS購読ボタンの表示
define('OP_RSS_FOLLOW_BUTTON_VISIBLE', 'rss_follow_button_visible');
if ( !function_exists( 'is_rss_follow_button_visible' ) ):
function is_rss_follow_button_visible(){
  return get_option(OP_RSS_FOLLOW_BUTTON_VISIBLE, 1);
}
endif;

//ボタンカラー
define('OP_SNS_FOLLOW_BUTTON_COLOR', 'sns_follow_button_color');
if ( !function_exists( 'get_sns_follow_button_color' ) ):
function get_sns_follow_button_color(){
  return get_option(OP_SNS_FOLLOW_BUTTON_COLOR, 'brand_color');
}
endif;

//デフォルトフォローユーザー
define('OP_SNS_DEFAULT_FOLLOW_USER', 'sns_default_follow_user');
if ( !function_exists( 'get_sns_default_follow_user' ) ):
function get_sns_default_follow_user(){
  return get_option(OP_SNS_DEFAULT_FOLLOW_USER, wp_get_current_user()->ID);
}
endif;
