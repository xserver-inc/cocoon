<?php //SNS設定に必要な定数や関数
///////////////////////////////////////
// SNSシェアボタンの設定
///////////////////////////////////////

//トップシェアボタン関数
require_once 'sns-share-funcs-top.php';
//ボトムシェアボタン関数
require_once 'sns-share-funcs-bottom.php';

//ツイートにメンションを含める
define('OP_TWITTER_ID_INCLUDE', 'twitter_id_include');
if ( !function_exists( 'is_twitter_id_include' ) ):
function is_twitter_id_include(){
  return get_option(OP_TWITTER_ID_INCLUDE);
}
endif;

//ツイート後にフォローを促す
define('OP_TWITTER_RELATED_FOLLOW_ENABLE', 'twitter_related_follow_enable');
if ( !function_exists( 'is_twitter_related_follow_enable' ) ):
function is_twitter_related_follow_enable(){
  return get_option(OP_TWITTER_RELATED_FOLLOW_ENABLE, 1);
}
endif;
