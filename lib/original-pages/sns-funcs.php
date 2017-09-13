<?php //SNS設定に必要な定数や関数

//SNSフォローメッセージ
define('OP_SNS_FOLLOW_MESSAGE', 'SNS_FOLLOW_MESSAGE');
if ( !function_exists( 'get_sns_follow_message' ) ):
function get_sns_follow_message(){
  return get_option(OP_SNS_FOLLOW_MESSAGE, 'フォローする');
}
endif;

//投稿・固定ページ以外で表示する
define('OP_SNS_DEFAULT_FOLLOW_USER', 'sns_default_follow_user');
if ( !function_exists( 'get_sns_default_follow_user' ) ):
function get_sns_default_follow_user(){
  return get_option(OP_SNS_DEFAULT_FOLLOW_USER, wp_get_current_user()->ID);
}
endif;
