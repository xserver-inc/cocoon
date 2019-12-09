<?php //SNS設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// SNSフォローボタンの設定
///////////////////////////////////////
//本文下のフォローボタンの表示
define('OP_SNS_FOLLOW_BUTTONS_VISIBLE', 'sns_follow_buttons_visible');
if ( !function_exists( 'is_sns_follow_buttons_visible' ) ):
function is_sns_follow_buttons_visible(){
  return get_theme_option(OP_SNS_FOLLOW_BUTTONS_VISIBLE, 1);
}
endif;


//SNSフォローメッセージ
define('REP_AUTHOR', '#{author}');
define('OP_SNS_FOLLOW_MESSAGE', 'sns_follow_message');
if ( !function_exists( 'get_sns_follow_message' ) ):
function get_sns_follow_message(){
  return stripslashes_deep(get_theme_option(OP_SNS_FOLLOW_MESSAGE, REP_AUTHOR.__( 'をフォローする', THEME_NAME )));
}
endif;
if ( !function_exists( 'get_sns_follow_display_message' ) ):
function get_sns_follow_display_message(){
  return str_replace(REP_AUTHOR, get_the_author_meta('display_name', get_the_posts_author_id()), get_sns_follow_message());
}
endif;

//フロントページシェアボタンの表示
define('OP_SNS_FRONT_PAGE_FOLLOW_BUTTONS_VISIBLE', 'sns_front_page_follow_buttons_visible');
if ( !function_exists( 'is_sns_front_page_follow_buttons_visible' ) ):
function is_sns_front_page_follow_buttons_visible(){
  return get_theme_option(OP_SNS_FRONT_PAGE_FOLLOW_BUTTONS_VISIBLE);
}
endif;

//投稿シェアボタンの表示
define('OP_SNS_SINGLE_FOLLOW_BUTTONS_VISIBLE', 'sns_single_follow_buttons_visible');
if ( !function_exists( 'is_sns_single_follow_buttons_visible' ) ):
function is_sns_single_follow_buttons_visible(){
  return get_theme_option(OP_SNS_SINGLE_FOLLOW_BUTTONS_VISIBLE, 1);
}
endif;

//固定ページシェアボタンの表示
define('OP_SNS_PAGE_FOLLOW_BUTTONS_VISIBLE', 'sns_page_follow_buttons_visible');
if ( !function_exists( 'is_sns_page_follow_buttons_visible' ) ):
function is_sns_page_follow_buttons_visible(){
  return get_theme_option(OP_SNS_PAGE_FOLLOW_BUTTONS_VISIBLE, 1);
}
endif;

//カテゴリーシェアボタンの表示
define('OP_SNS_CATEGORY_FOLLOW_BUTTONS_VISIBLE', 'sns_category_follow_buttons_visible');
if ( !function_exists( 'is_sns_category_follow_buttons_visible' ) ):
function is_sns_category_follow_buttons_visible(){
  return get_theme_option(OP_SNS_CATEGORY_FOLLOW_BUTTONS_VISIBLE);
}
endif;

//タグシェアボタンの表示
define('OP_SNS_TAG_FOLLOW_BUTTONS_VISIBLE', 'sns_tag_follow_buttons_visible');
if ( !function_exists( 'is_sns_tag_follow_buttons_visible' ) ):
function is_sns_tag_follow_buttons_visible(){
  return get_theme_option(OP_SNS_TAG_FOLLOW_BUTTONS_VISIBLE);
}
endif;


//feedlyフォローボタンの表示
define('OP_FEEDLY_FOLLOW_BUTTON_VISIBLE', 'feedly_follow_button_visible');
if ( !function_exists( 'is_feedly_follow_button_visible' ) ):
function is_feedly_follow_button_visible(){
  return get_theme_option(OP_FEEDLY_FOLLOW_BUTTON_VISIBLE, 1);
}
endif;

//RSS購読ボタンの表示
define('OP_RSS_FOLLOW_BUTTON_VISIBLE', 'rss_follow_button_visible');
if ( !function_exists( 'is_rss_follow_button_visible' ) ):
function is_rss_follow_button_visible(){
  return get_theme_option(OP_RSS_FOLLOW_BUTTON_VISIBLE, 1);
}
endif;

//ボタンカラー
define('OP_SNS_FOLLOW_BUTTON_COLOR', 'sns_follow_button_color');
if ( !function_exists( 'get_sns_follow_button_color' ) ):
function get_sns_follow_button_color(){
  return get_theme_option(OP_SNS_FOLLOW_BUTTON_COLOR, 'brand_color');
}
endif;

//デフォルトフォローユーザー
define('OP_SNS_DEFAULT_FOLLOW_USER', 'sns_default_follow_user');
if ( !function_exists( 'get_sns_default_follow_user' ) ):
function get_sns_default_follow_user(){
  return get_theme_option(OP_SNS_DEFAULT_FOLLOW_USER, wp_get_current_user()->ID);
}
endif;

//本文下のフォローボタンシェア数の表示
define('OP_SNS_FOLLOW_BUTTONS_COUNT_VISIBLE', 'sns_follow_buttons_count_visible');
if ( !function_exists( 'is_sns_follow_buttons_count_visible' ) ):
function is_sns_follow_buttons_count_visible(){
  return get_theme_option(OP_SNS_FOLLOW_BUTTONS_COUNT_VISIBLE);
}
endif;

//feedlyの購読者数入力
define('OP_SNS_FEEDLY_FOLLOW_COUNT', 'sns_feedly_follow_count');
if ( !function_exists( 'get_sns_feedly_follow_count' ) ):
function get_sns_feedly_follow_count(){
  return get_theme_option(OP_SNS_FEEDLY_FOLLOW_COUNT, 0);
}
endif;


//SNSフォロー数キャッシュ有効
define('OP_SNS_FOLLOW_COUNT_CACHE_ENABLE', 'sns_follow_count_cache_enable');
if ( !function_exists( 'is_sns_follow_count_cache_enable' ) ):
function is_sns_follow_count_cache_enable(){
  return get_theme_option(OP_SNS_FOLLOW_COUNT_CACHE_ENABLE, 1);
}
endif;

//SNSフォロー数キャッシュ取得間隔
define('OP_SNS_FOLLOW_COUNT_CACHE_INTERVAL', 'sns_follow_count_cache_interval');
if ( !function_exists( 'get_sns_follow_count_cache_interval' ) ):
function get_sns_follow_count_cache_interval(){
  return intval(get_theme_option(OP_SNS_FOLLOW_COUNT_CACHE_INTERVAL, 12));
}
endif;

//別スキームのSNSフォロー数を取得するか
define('OP_ANOTHER_SCHEME_SNS_FOLLOW_COUNT', 'another_scheme_sns_follow_count');
if ( !function_exists( 'is_another_scheme_sns_follow_count' ) ):
function is_another_scheme_sns_follow_count(){
  return intval(get_theme_option(OP_ANOTHER_SCHEME_SNS_FOLLOW_COUNT));
}
endif;
