<?php //SNS設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// SNSシェアボタンの設定
///////////////////////////////////////

//トップシェアボタン関数
require_once abspath(__FILE__).'sns-share-funcs-top.php';
//ボトムシェアボタン関数
require_once abspath(__FILE__).'sns-share-funcs-bottom.php';

//ツイートにメンションを含める
define('OP_TWITTER_ID_INCLUDE', 'twitter_id_include');
if ( !function_exists( 'is_twitter_id_include' ) ):
function is_twitter_id_include(){
  return get_theme_option(OP_TWITTER_ID_INCLUDE);
}
endif;

//ツイート後にフォローを促す
define('OP_TWITTER_RELATED_FOLLOW_ENABLE', 'twitter_related_follow_enable');
if ( !function_exists( 'is_twitter_related_follow_enable' ) ):
function is_twitter_related_follow_enable(){
  return false;
  // return get_theme_option(OP_TWITTER_RELATED_FOLLOW_ENABLE, 1);
}
endif;

//ツイートに含めらハッシュタグ
define('OP_TWITTER_HASH_TAG', 'twitter_hash_tag');
if ( !function_exists( 'get_twitter_hash_tag' ) ):
function get_twitter_hash_tag(){
  return get_theme_option(OP_TWITTER_HASH_TAG, '');
}
endif;

//Facebookのアクセストークン
define('OP_FACEBOOK_ACCESS_TOKEN', 'facebook_access_token');
if ( !function_exists( 'get_facebook_access_token' ) ):
function get_facebook_access_token(){
  return get_theme_option(OP_FACEBOOK_ACCESS_TOKEN, '');
}
endif;

//写真をPinterestで共有する
define('OP_PINTEREST_SHARE_PIN_VISIBLE', 'pinterest_share_button_visible');
if ( !function_exists( 'is_pinterest_share_pin_visible' ) ):
function is_pinterest_share_pin_visible(){
  return get_theme_option(OP_PINTEREST_SHARE_PIN_VISIBLE);
}
endif;

//SNSシェア数キャッシュ有効
define('OP_SNS_SHARE_COUNT_CACHE_ENABLE', 'sns_share_count_cache_enable');
if ( !function_exists( 'is_sns_share_count_cache_enable' ) ):
function is_sns_share_count_cache_enable(){
  return get_theme_option(OP_SNS_SHARE_COUNT_CACHE_ENABLE, 1);
}
endif;

//SNSシェア数キャッシュ取得間隔
define('OP_SNS_SHARE_COUNT_CACHE_INTERVAL', 'sns_share_count_cache_interval');
if ( !function_exists( 'get_sns_share_count_cache_interval' ) ):
function get_sns_share_count_cache_interval(){
  return intval(get_theme_option(OP_SNS_SHARE_COUNT_CACHE_INTERVAL, 6));
}
endif;

//別スキームのSNSシェア数を取得するか
define('OP_ANOTHER_SCHEME_SNS_SHARE_COUNT', 'another_scheme_sns_share_count');
if ( !function_exists( 'is_another_scheme_sns_share_count' ) ):
function is_another_scheme_sns_share_count(){
  return intval(get_theme_option(OP_ANOTHER_SCHEME_SNS_SHARE_COUNT));
}
endif;
