<?php //SNS設定に必要な定数や関数

//SNSフォローメッセージ
define('OP_SNS_FOLLOW_MESSAGE', 'SNS_FOLLOW_MESSAGE');
if ( !function_exists( 'get_sns_follow_message' ) ):
function get_sns_follow_message(){
  return get_option(OP_SNS_FOLLOW_MESSAGE, 'フォローする');
}
endif;
