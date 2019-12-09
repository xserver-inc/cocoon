<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// SNSトップシェアボタンの設定
///////////////////////////////////////

//SNSトップシェアボタンの表示
define('OP_SNS_TOP_SHARE_BUTTONS_VISIBLE', 'sns_top_share_buttons_visible');
if ( !function_exists( 'is_sns_top_share_buttons_visible' ) ):
function is_sns_top_share_buttons_visible(){
  return get_theme_option(OP_SNS_TOP_SHARE_BUTTONS_VISIBLE, 1);
}
endif;

//SNSトップシェアメッセージ
define('OP_SNS_TOP_SHARE_MESSAGE', 'sns_top_share_message');
if ( !function_exists( 'get_sns_top_share_message' ) ):
function get_sns_top_share_message(){
  return get_theme_option(OP_SNS_TOP_SHARE_MESSAGE, 'シェアする');
}
endif;

//Twitterシェアボタンの表示
define('OP_TOP_TWITTER_SHARE_BUTTON_VISIBLE', 'top_twitter_share_button_visible');
if ( !function_exists( 'is_top_twitter_share_button_visible' ) ):
function is_top_twitter_share_button_visible(){
  return get_theme_option(OP_TOP_TWITTER_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//Facebookシェアボタンの表示
define('OP_TOP_FACEBOOK_SHARE_BUTTON_VISIBLE', 'top_facebook_share_button_visible');
if ( !function_exists( 'is_top_facebook_share_button_visible' ) ):
function is_top_facebook_share_button_visible(){
  return get_theme_option(OP_TOP_FACEBOOK_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//はてなブックマークシェアボタンの表示
define('OP_TOP_HATEBU_SHARE_BUTTON_VISIBLE', 'top_hatebu_share_button_visible');
if ( !function_exists( 'is_top_hatebu_share_button_visible' ) ):
function is_top_hatebu_share_button_visible(){
  return get_theme_option(OP_TOP_HATEBU_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//Google+シェアボタンの表示
define('OP_TOP_GOOGLE_PLUS_SHARE_BUTTON_VISIBLE', 'top_google_plus_share_button_visible');
if ( !function_exists( 'is_top_google_plus_share_button_visible' ) ):
function is_top_google_plus_share_button_visible(){
  return 0;//get_theme_option(OP_TOP_GOOGLE_PLUS_SHARE_BUTTON_VISIBLE, 0);
}
endif;

//Pocketシェアボタンの表示
define('OP_TOP_POCKET_SHARE_BUTTON_VISIBLE', 'top_pocket_share_button_visible');
if ( !function_exists( 'is_top_pocket_share_button_visible' ) ):
function is_top_pocket_share_button_visible(){
  return get_theme_option(OP_TOP_POCKET_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//LINE@シェアボタンの表示
define('OP_TOP_LINE_AT_SHARE_BUTTON_VISIBLE', 'top_line_at_share_button_visible');
if ( !function_exists( 'is_top_line_at_share_button_visible' ) ):
function is_top_line_at_share_button_visible(){
  return get_theme_option(OP_TOP_LINE_AT_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//Pinterestシェアボタンの表示
define('OP_TOP_PINTEREST_SHARE_BUTTON_VISIBLE', 'top_pinterest_share_button_visible');
if ( !function_exists( 'is_top_pinterest_share_button_visible' ) ):
function is_top_pinterest_share_button_visible(){
  return get_theme_option(OP_TOP_PINTEREST_SHARE_BUTTON_VISIBLE);
}
endif;

//LinkedInシェアボタンの表示
define('OP_TOP_LINKEDIN_SHARE_BUTTON_VISIBLE', 'top_linkedin_share_button_visible');
if ( !function_exists( 'is_top_linkedin_share_button_visible' ) ):
function is_top_linkedin_share_button_visible(){
  return get_theme_option(OP_TOP_LINKEDIN_SHARE_BUTTON_VISIBLE);
}
endif;

//コピーシェアボタンの表示
define('OP_TOP_COPY_SHARE_BUTTON_VISIBLE', 'top_copy_share_button_visible');
if ( !function_exists( 'is_top_copy_share_button_visible' ) ):
function is_top_copy_share_button_visible(){
  return get_theme_option(OP_TOP_COPY_SHARE_BUTTON_VISIBLE, 1);
}
endif;

//フロントページシェアボタンの表示
define('OP_SNS_FRONT_PAGE_TOP_SHARE_BUTTONS_VISIBLE', 'sns_front_page_top_share_buttons_visible');
if ( !function_exists( 'is_sns_front_page_top_share_buttons_visible' ) ):
function is_sns_front_page_top_share_buttons_visible(){
  return get_theme_option(OP_SNS_FRONT_PAGE_TOP_SHARE_BUTTONS_VISIBLE);
}
endif;

//投稿シェアボタンの表示
define('OP_SNS_SINGLE_TOP_SHARE_BUTTONS_VISIBLE', 'sns_single_top_share_buttons_visible');
if ( !function_exists( 'is_sns_single_top_share_buttons_visible' ) ):
function is_sns_single_top_share_buttons_visible(){
  return get_theme_option(OP_SNS_SINGLE_TOP_SHARE_BUTTONS_VISIBLE, 1);
}
endif;

//固定ページシェアボタンの表示
define('OP_SNS_PAGE_TOP_SHARE_BUTTONS_VISIBLE', 'sns_page_top_share_buttons_visible');
if ( !function_exists( 'is_sns_page_top_share_buttons_visible' ) ):
function is_sns_page_top_share_buttons_visible(){
  return get_theme_option(OP_SNS_PAGE_TOP_SHARE_BUTTONS_VISIBLE, 1);
}
endif;

//カテゴリーシェアボタンの表示
define('OP_SNS_CATEGORY_TOP_SHARE_BUTTONS_VISIBLE', 'sns_category_top_share_buttons_visible');
if ( !function_exists( 'is_sns_category_top_share_buttons_visible' ) ):
function is_sns_category_top_share_buttons_visible(){
  return get_theme_option(OP_SNS_CATEGORY_TOP_SHARE_BUTTONS_VISIBLE);
}
endif;

//タグシェアボタンの表示
define('OP_SNS_TAG_TOP_SHARE_BUTTONS_VISIBLE', 'sns_tag_top_share_buttons_visible');
if ( !function_exists( 'is_sns_tag_top_share_buttons_visible' ) ):
function is_sns_tag_top_share_buttons_visible(){
  return get_theme_option(OP_SNS_TAG_TOP_SHARE_BUTTONS_VISIBLE);
}
endif;

//SNSトップシェアボタンカラー
define('OP_SNS_TOP_SHARE_BUTTON_COLOR', 'sns_top_share_button_color');
if ( !function_exists( 'get_sns_top_share_button_color' ) ):
function get_sns_top_share_button_color(){
  return get_theme_option(OP_SNS_TOP_SHARE_BUTTON_COLOR, 'brand_color');
}
endif;

//SNSトップシェアボタンのカラム数
define('OP_SNS_TOP_SHARE_COLUMN_COUNT', 'sns_top_share_column_count');
if ( !function_exists( 'get_sns_top_share_column_count' ) ):
function get_sns_top_share_column_count(){
  return get_theme_option(OP_SNS_TOP_SHARE_COLUMN_COUNT, 6);
}
endif;

//SNSトップシェアボタンのロゴとキャプションの位置
define('OP_SNS_TOP_SHARE_LOGO_CAPTION_POSITION', 'sns_top_share_logo_caption_position');
if ( !function_exists( 'get_sns_top_share_logo_caption_position' ) ):
function get_sns_top_share_logo_caption_position(){
  return get_theme_option(OP_SNS_TOP_SHARE_LOGO_CAPTION_POSITION, 'high_and_low_lc');
}
endif;

//SNSトップシェア数の表示
define('OP_SNS_TOP_SHARE_BUTTONS_COUNT_VISIBLE', 'sns_top_share_buttons_count_visible');
if ( !function_exists( 'is_sns_top_share_buttons_count_visible' ) ):
function is_sns_top_share_buttons_count_visible(){
  return get_theme_option(OP_SNS_TOP_SHARE_BUTTONS_COUNT_VISIBLE);
}
endif;
