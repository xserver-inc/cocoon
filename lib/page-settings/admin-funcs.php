<?php //管理画面設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アドミンバーに独自管理メニューを表示
define('OP_ADMIN_TOOL_MENU_VISIBLE', 'admin_tool_menu_visible');
if ( !function_exists( 'is_admin_tool_menu_visible' ) ):
function is_admin_tool_menu_visible(){
  return get_theme_option(OP_ADMIN_TOOL_MENU_VISIBLE, 1);
}
endif;

//投稿一覧に作成者を表示する
define('OP_ADMIN_LIST_AUTHOR_VISIBLE', 'admin_list_author_visible');
if ( !function_exists( 'is_admin_list_author_visible' ) ):
function is_admin_list_author_visible(){
  return get_theme_option(OP_ADMIN_LIST_AUTHOR_VISIBLE, 1);
}
endif;

//投稿一覧にカテゴリーを表示する
define('OP_ADMIN_LIST_CATEGORIES_VISIBLE', 'admin_list_categories_visible');
if ( !function_exists( 'is_admin_list_categories_visible' ) ):
function is_admin_list_categories_visible(){
  return get_theme_option(OP_ADMIN_LIST_CATEGORIES_VISIBLE, 1);
}
endif;

//投稿一覧にタグを表示する
define('OP_ADMIN_LIST_TAGS_VISIBLE', 'admin_list_tags_visible');
if ( !function_exists( 'is_admin_list_tags_visible' ) ):
function is_admin_list_tags_visible(){
  return get_theme_option(OP_ADMIN_LIST_TAGS_VISIBLE, 1);
}
endif;

//投稿一覧にコメントを表示する
define('OP_ADMIN_LIST_COMMENTS_VISIBLE', 'admin_list_comments_visible');
if ( !function_exists( 'is_admin_list_comments_visible' ) ):
function is_admin_list_comments_visible(){
  return get_theme_option(OP_ADMIN_LIST_COMMENTS_VISIBLE, 1);
}
endif;

//投稿一覧に日付を表示する
define('OP_ADMIN_LIST_DATE_VISIBLE', 'admin_list_date_visible');
if ( !function_exists( 'is_admin_list_date_visible' ) ):
function is_admin_list_date_visible(){
  return get_theme_option(OP_ADMIN_LIST_DATE_VISIBLE, 1);
}
endif;

//投稿一覧にアイキャッチを表示する
define('OP_ADMIN_LIST_EYECATCH_VISIBLE', 'admin_list_eyecatch_visible');
if ( !function_exists( 'is_admin_list_eyecatch_visible' ) ):
function is_admin_list_eyecatch_visible(){
  return get_theme_option(OP_ADMIN_LIST_EYECATCH_VISIBLE, 1);
}
endif;

//投稿IDを表示する
define('OP_ADMIN_LIST_POST_ID_VISIBLE', 'admin_list_post_id_visible');
if ( !function_exists( 'is_admin_list_post_id_visible' ) ):
function is_admin_list_post_id_visible(){
  return get_theme_option(OP_ADMIN_LIST_POST_ID_VISIBLE, 1);
}
endif;

//投稿一覧に文字数を表示する
define('OP_ADMIN_LIST_WORD_COUNT_VISIBLE', 'admin_list_word_count_visible');
if ( !function_exists( 'is_admin_list_word_count_visible' ) ):
function is_admin_list_word_count_visible(){
  return get_theme_option(OP_ADMIN_LIST_WORD_COUNT_VISIBLE, 1);
}
endif;

//投稿一覧にメモを表示する
define('OP_ADMIN_LIST_MEMO_VISIBLE', 'admin_list_memo_visible');
if ( !function_exists( 'is_admin_list_memo_visible' ) ):
function is_admin_list_memo_visible(){
  return get_theme_option(OP_ADMIN_LIST_MEMO_VISIBLE);
}
endif;


///////////////////////////////////////
// 管理者パネル
///////////////////////////////////////

//管理者パネルを表示タイプ
define('OP_ADMIN_PANEL_DISPLAY_TYPE', 'admin_panel_display_type');
if ( !function_exists( 'get_admin_panel_display_type' ) ):
function get_admin_panel_display_type(){
  return get_theme_option(OP_ADMIN_PANEL_DISPLAY_TYPE, 'pc_only');
}
endif;
if ( !function_exists( 'is_admin_panel_all_visible' ) ):
function is_admin_panel_all_visible(){
  return get_admin_panel_display_type() == 'all';
}
endif;
if ( !function_exists( 'is_admin_panel_pc_only_visible' ) ):
function is_admin_panel_pc_only_visible(){
  return get_admin_panel_display_type() == 'pc_only';
}
endif;
if ( !function_exists( 'is_admin_panel_mobile_only_visible' ) ):
function is_admin_panel_mobile_only_visible(){
  return get_admin_panel_display_type() == 'mobile_only';
}
endif;
if ( !function_exists( 'is_admin_panel_visible' ) ):
function is_admin_panel_visible(){
  return get_admin_panel_display_type() != 'none';
}
endif;

// //管理者パネルを表示
// define('OP_ADMIN_PANEL_VISIBLE', 'admin_panel_visible');
// if ( !function_exists( 'is_admin_panel_visible' ) ):
// function is_admin_panel_visible(){
//   return get_theme_option(OP_ADMIN_PANEL_VISIBLE, 1);
// }
// endif;

//管理者パネルのPVを表示
define('OP_ADMIN_PANEL_PV_AREA_VISIBLE', 'admin_panel_pv_area_visible');
if ( !function_exists( 'is_admin_panel_pv_area_visible' ) ):
function is_admin_panel_pv_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_PV_AREA_VISIBLE, 1);
}
endif;

//管理者パネルのPV取得方法
define('OP_ADMIN_PANEL_PV_TYPE', 'admin_panel_pv_type');
if ( !function_exists( 'get_admin_panel_pv_type' ) ):
function get_admin_panel_pv_type(){
  return get_theme_option(OP_ADMIN_PANEL_PV_TYPE, THEME_NAME);
}
endif;

//管理者パネル編集エリアの表示
define('OP_ADMIN_PANEL_EDIT_AREA_VISIBLE', 'admin_panel_edit_area_visible');
if ( !function_exists( 'is_admin_panel_edit_area_visible' ) ):
function is_admin_panel_edit_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE, 1);
}
endif;

//管理者パネルWordpress編集の表示
define('OP_ADMIN_PANEL_WP_EDIT_VISIBLE', 'admin_panel_wp_edit_visible');
if ( !function_exists( 'is_admin_panel_wp_edit_visible' ) ):
function is_admin_panel_wp_edit_visible(){
  return get_theme_option(OP_ADMIN_PANEL_WP_EDIT_VISIBLE, 1);
}
endif;

//管理者パネルWindows Live Writer編集の表示
define('OP_ADMIN_PANEL_WLW_EDIT_VISIBLE', 'admin_panel_wlw_edit_visible');
if ( !function_exists( 'is_admin_panel_wlw_edit_visible' ) ):
function is_admin_panel_wlw_edit_visible(){
  return get_theme_option(OP_ADMIN_PANEL_WLW_EDIT_VISIBLE);
}
endif;

//管理者パネルAMPエリアの表示
define('OP_ADMIN_PANEL_AMP_AREA_VISIBLE', 'admin_panel_amp_area_visible');
if ( !function_exists( 'is_admin_panel_amp_area_visible' ) ):
function is_admin_panel_amp_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_AMP_AREA_VISIBLE, 1);
}
endif;

//Google AMPテストリンクの表示
define('OP_ADMIN_GOOGLE_AMP_TEST_VISIBLE', 'admin_google_amp_test_visible');
if ( !function_exists( 'is_admin_google_amp_test_visible' ) ):
function is_admin_google_amp_test_visible(){
  return get_theme_option(OP_ADMIN_GOOGLE_AMP_TEST_VISIBLE, 1);
}
endif;

//The AMP Validatorリンクの表示
define('OP_ADMIN_THE_AMP_VALIDATOR_VISIBLE', 'admin_the_amp_validator_visible');
if ( !function_exists( 'is_admin_the_amp_validator_visible' ) ):
function is_admin_the_amp_validator_visible(){
  return get_theme_option(OP_ADMIN_THE_AMP_VALIDATOR_VISIBLE, 1);
}
endif;

//AMPBenchリンクの表示
define('OP_ADMIN_AMPBENCH_VISIBLE', 'admin_ampbench_visible');
if ( !function_exists( 'is_admin_ampbench_visible' ) ):
function is_admin_ampbench_visible(){
  return get_theme_option(OP_ADMIN_AMPBENCH_VISIBLE, 1);
}
endif;


//管理者パネルチェックツールエリアの表示
define('OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE', 'admin_panel_check_tools_area_visible');
if ( !function_exists( 'is_admin_panel_check_tools_area_visible' ) ):
function is_admin_panel_check_tools_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE);
}
endif;

//PageSpeed Insightsリンクの表示
define('OP_ADMIN_PAGESPEED_INSIGHTS_VISIBLE', 'admin_pagespeed_insights_visible');
if ( !function_exists( 'is_admin_pagespeed_insights_visible' ) ):
function is_admin_pagespeed_insights_visible(){
  return get_theme_option(OP_ADMIN_PAGESPEED_INSIGHTS_VISIBLE, 1);
}
endif;

//GTmetrixリンクの表示
define('OP_ADMIN_GTMETRIX_VISIBLE', 'admin_gtmetrix_visible');
if ( !function_exists( 'is_admin_gtmetrix_visible' ) ):
function is_admin_gtmetrix_visible(){
  return get_theme_option(OP_ADMIN_GTMETRIX_VISIBLE, 1);
}
endif;

//モバイルフレンドリーリンクの表示
define('OP_ADMIN_MOBILE_FRIENDLY_TEST_VISIBLE', 'admin_mobile_friendly_test_visible');
if ( !function_exists( 'is_admin_mobile_friendly_test_visible' ) ):
function is_admin_mobile_friendly_test_visible(){
  return get_theme_option(OP_ADMIN_MOBILE_FRIENDLY_TEST_VISIBLE, 1);
}
endif;

//構造化チェックリンクの表示
define('OP_ADMIN_STRUCTURED_DATA_VISIBLE', 'admin_structured_data_visible');
if ( !function_exists( 'is_admin_structured_data_visible' ) ):
function is_admin_structured_data_visible(){
  return get_theme_option(OP_ADMIN_STRUCTURED_DATA_VISIBLE, 1);
}
endif;

//HTML5チェックリンクの表示
define('OP_ADMIN_NU_HTML_CHECKER_VISIBLE', 'admin_nu_html_checker_visible');
if ( !function_exists( 'is_admin_nu_html_checker_visible' ) ):
function is_admin_nu_html_checker_visible(){
  return get_theme_option(OP_ADMIN_NU_HTML_CHECKER_VISIBLE, 1);
}
endif;

//SEOチェキリンクの表示
define('OP_ADMIN_SEOCHEKI_VISIBLE', 'admin_seocheki_visible');
if ( !function_exists( 'is_admin_seocheki_visible' ) ):
function is_admin_seocheki_visible(){
  return get_theme_option(OP_ADMIN_SEOCHEKI_VISIBLE, 1);
}
endif;

//HTML5アウトラインチェックリンクの表示
define('OP_ADMIN_HTML5_OUTLINER_VISIBLE', 'admin_html5_outliner_visible');
if ( !function_exists( 'is_admin_html5_outliner_visible' ) ):
function is_admin_html5_outliner_visible(){
  return get_theme_option(OP_ADMIN_HTML5_OUTLINER_VISIBLE, 1);
}
endif;

//ツイートチェックリンクの表示
define('OP_ADMIN_TWEET_CHECK_VISIBLE', 'admin_tweet_check_visible');
if ( !function_exists( 'is_admin_tweet_check_visible' ) ):
function is_admin_tweet_check_visible(){
  return get_theme_option(OP_ADMIN_TWEET_CHECK_VISIBLE, 1);
}
endif;

//管理者パネルレスポンシブツールエリアの表示
define('OP_ADMIN_PANEL_RESPONSIVE_TOOLS_AREA_VISIBLE', 'admin_panel_responsive_tools_area_visible');
if ( !function_exists( 'is_admin_panel_responsive_tools_area_visible' ) ):
function is_admin_panel_responsive_tools_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_RESPONSIVE_TOOLS_AREA_VISIBLE, 1);
}
endif;

//Responsinatorリンクの表示
define('OP_ADMIN_RESPONSINATOR_VISIBLE', 'admin_responsinator_visible');
if ( !function_exists( 'is_admin_responsinator_visible' ) ):
function is_admin_responsinator_visible(){
  return get_theme_option(OP_ADMIN_RESPONSINATOR_VISIBLE, 1);
}
endif;

//Sizzyリンクの表示
define('OP_ADMIN_SIZZY_VISIBLE', 'admin_sizzy_visible');
if ( !function_exists( 'is_admin_sizzy_visible' ) ):
function is_admin_sizzy_visible(){
  return get_theme_option(OP_ADMIN_SIZZY_VISIBLE, 1);
}
endif;

//Multi_Screen_Resolution_Testリンクの表示
define('OP_ADMIN_MULTI_SCREEN_RESOLUTION_TEST_VISIBLE', 'admin_multi_screen_resolution_test_visible');
if ( !function_exists( 'is_admin_multi_screen_resolution_test_visible' ) ):
function is_admin_multi_screen_resolution_test_visible(){
  return get_theme_option(OP_ADMIN_MULTI_SCREEN_RESOLUTION_TEST_VISIBLE, 1);
}
endif;

//投稿・固定ページで管理パネルを表示してよいか
if ( !function_exists( 'is_admin_panel_singular_page_visible' ) ):
function is_admin_panel_singular_page_visible(){
  return is_singular() && (
       is_admin_panel_pv_area_visible()
    || is_admin_panel_edit_area_visible()
    || is_admin_panel_amp_area_visible()
    || is_admin_panel_check_tools_area_visible()
    || is_admin_panel_responsive_tools_area_visible()
  );
}
endif;

//投稿・固定ページ以外で管理パネルを表示してよいか
if ( !function_exists( 'is_admin_panel_not_singular_page_visible' ) ):
function is_admin_panel_not_singular_page_visible(){
  return !is_singular() && (
       is_admin_panel_check_tools_area_visible()
    || is_admin_panel_responsive_tools_area_visible()
  );
}
endif;
