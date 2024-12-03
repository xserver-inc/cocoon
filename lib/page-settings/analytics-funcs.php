<?php //アクセス解析設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//サイト管理者を解析するか
define('OP_ANALYTICS_ADMIN_INCLUDE', 'analytics_admin_include');
if ( !function_exists( 'is_analytics_admin_include' ) ):
function is_analytics_admin_include(){
  return get_theme_option(OP_ANALYTICS_ADMIN_INCLUDE, 1);
}
endif;

//Google Tag ManagerのトラッキングID
define('OP_GOOGLE_TAG_MANAGER_TRACKING_ID', 'google_tag_manager_tracking_id');
if ( !function_exists( 'get_google_tag_manager_tracking_id' ) ):
function get_google_tag_manager_tracking_id(){
  return get_theme_option(OP_GOOGLE_TAG_MANAGER_TRACKING_ID, '');
}
endif;

//GA4測定ID
define('OP_GA4_TRACKING_ID', 'ga4_tracking_id');
if ( !function_exists( 'get_ga4_tracking_id' ) ):
function get_ga4_tracking_id(){
  return get_theme_option(OP_GA4_TRACKING_ID, '');
}
endif;

//Google Search ConsoleのID
define('OP_GOOGLE_SEARCH_CONSOLE_ID', 'google_search_console_id');
if ( !function_exists( 'get_google_search_console_id' ) ):
function get_google_search_console_id(){
  return get_theme_option(OP_GOOGLE_SEARCH_CONSOLE_ID, '');
}
endif;

//ClarityのプロジェクトID
define('OP_CLARITY_PROJECT_ID', 'clarity_project_id');
if ( !function_exists( 'get_clarity_project_id' ) ):
function get_clarity_project_id(){
  return get_theme_option(OP_CLARITY_PROJECT_ID, '');
}
endif;

//その他のアクセス解析<head></head>内タグ
define('OP_OTHER_ANALYTICS_HEAD_TAGS', 'other_analytics_head_tags');
if ( !function_exists( 'get_other_analytics_head_tags' ) ):
function get_other_analytics_head_tags(){
  return stripslashes_deep(get_theme_option(OP_OTHER_ANALYTICS_HEAD_TAGS, ''));
}
endif;

//その他のアクセス解析ヘッダー（body直後）タグ
define('OP_OTHER_ANALYTICS_HEADER_TAGS', 'other_analytics_header_tags');
if ( !function_exists( 'get_other_analytics_header_tags' ) ):
function get_other_analytics_header_tags(){
  return stripslashes_deep(get_theme_option(OP_OTHER_ANALYTICS_HEADER_TAGS, ''));
}
endif;

//その他のアクセス解析フッター（/body直前）タグ
define('OP_OTHER_ANALYTICS_FOOTER_TAGS', 'other_analytics_footer_tags');
if ( !function_exists( 'get_other_analytics_footer_tags' ) ):
function get_other_analytics_footer_tags(){
  return stripslashes_deep(get_theme_option(OP_OTHER_ANALYTICS_FOOTER_TAGS, ''));
}
endif;
