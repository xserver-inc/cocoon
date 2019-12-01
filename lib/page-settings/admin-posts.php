<?php //管理画面設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アドミンバーに独自管理メニューを表示
update_theme_option(OP_ADMIN_TOOL_MENU_VISIBLE);

//インデックスのエントリーカードにPV数を表示
update_theme_option(OP_ADMIN_INDEX_PV_VISIBLE);

///////////////////////////////////////
// 投稿リスト
///////////////////////////////////////
//投稿一覧に作成者を表示する
update_theme_option(OP_ADMIN_LIST_AUTHOR_VISIBLE);

//投稿一覧にカテゴリーを表示する
update_theme_option(OP_ADMIN_LIST_CATEGORIES_VISIBLE);

//投稿一覧にタグを表示する
update_theme_option(OP_ADMIN_LIST_TAGS_VISIBLE);

//投稿一覧にコメントを表示する
update_theme_option(OP_ADMIN_LIST_COMMENTS_VISIBLE);

//投稿一覧に日付を表示する
update_theme_option(OP_ADMIN_LIST_DATE_VISIBLE);

//投稿IDを表示する
update_theme_option(OP_ADMIN_LIST_POST_ID_VISIBLE);

//投稿一覧に文字数を表示する
update_theme_option(OP_ADMIN_LIST_WORD_COUNT_VISIBLE);

//投稿一覧にアイキャッチを表示する
update_theme_option(OP_ADMIN_LIST_EYECATCH_VISIBLE);

//投稿一覧にメモを表示する
update_theme_option(OP_ADMIN_LIST_MEMO_VISIBLE);

///////////////////////////////////////
// 管理者パネル
///////////////////////////////////////

//管理者パネルを表示タイプ
update_theme_option(OP_ADMIN_PANEL_DISPLAY_TYPE);

//管理者パネルのPVを表示
update_theme_option(OP_ADMIN_PANEL_PV_AREA_VISIBLE);

//管理者パネルのPV取得方法
update_theme_option(OP_ADMIN_PANEL_PV_TYPE);

//管理者パネル編集エリアの表示
update_theme_option(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE);

//管理者パネルWordPress編集の表示
update_theme_option(OP_ADMIN_PANEL_WP_EDIT_VISIBLE);

//管理者パネルWindows Live Writer編集の表示
update_theme_option(OP_ADMIN_PANEL_WLW_EDIT_VISIBLE);

//管理者パネルAMPエリアの表示
update_theme_option(OP_ADMIN_PANEL_AMP_AREA_VISIBLE);

//Google AMPテストリンクの表示
update_theme_option(OP_ADMIN_GOOGLE_AMP_TEST_VISIBLE);

//The AMP Validatorリンクの表示
update_theme_option(OP_ADMIN_THE_AMP_VALIDATOR_VISIBLE);

//AMPBenchリンクの表示
update_theme_option(OP_ADMIN_AMPBENCH_VISIBLE);

//管理者パネルチェックツールエリアの表示
update_theme_option(OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE);

//PageSpeed Insightsリンクの表示
update_theme_option(OP_ADMIN_PAGESPEED_INSIGHTS_VISIBLE);

//GTmetrixsリンクの表示
update_theme_option(OP_ADMIN_GTMETRIX_VISIBLE);

//モバイルフレンドリーリンクの表示
update_theme_option(OP_ADMIN_MOBILE_FRIENDLY_TEST_VISIBLE);

//構造化チェックリンクの表示
update_theme_option(OP_ADMIN_STRUCTURED_DATA_VISIBLE);

//HTML5チェックリンクの表示
update_theme_option(OP_ADMIN_NU_HTML_CHECKER_VISIBLE);

//HTML5アウトラインチェックリンクの表示
update_theme_option(OP_ADMIN_HTML5_OUTLINER_VISIBLE);

//SEOチェキリンクの表示
update_theme_option(OP_ADMIN_SEOCHEKI_VISIBLE);

//ツイートチェックリンクの表示
update_theme_option(OP_ADMIN_TWEET_CHECK_VISIBLE);

//管理者パネルレスポンシブツールエリアの表示
update_theme_option(OP_ADMIN_PANEL_RESPONSIVE_TOOLS_AREA_VISIBLE);

//Responsinatorリンクの表示
update_theme_option(OP_ADMIN_RESPONSINATOR_VISIBLE);

//Sizzyリンクの表示
update_theme_option(OP_ADMIN_SIZZY_VISIBLE);

//Multi_Screen_Resolution_Testリンクの表示
update_theme_option(OP_ADMIN_MULTI_SCREEN_RESOLUTION_TEST_VISIBLE);
