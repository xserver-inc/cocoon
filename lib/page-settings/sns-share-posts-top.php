<?php //SNSシェア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//シェアボタンの表示
update_theme_option(OP_SNS_TOP_SHARE_BUTTONS_VISIBLE);
//SNSシェアメッセージ
update_theme_option(OP_SNS_TOP_SHARE_MESSAGE);
//Twitterシェアボタンの表示
update_theme_option(OP_TOP_TWITTER_SHARE_BUTTON_VISIBLE);
//Facebookシェアボタンの表示
update_theme_option(OP_TOP_FACEBOOK_SHARE_BUTTON_VISIBLE);
//はてなブックマークシェアボタンの表示
update_theme_option(OP_TOP_HATEBU_SHARE_BUTTON_VISIBLE);
//Google+シェアボタンの表示
update_theme_option(OP_TOP_GOOGLE_PLUS_SHARE_BUTTON_VISIBLE);
//Pocketシェアボタンの表示
update_theme_option(OP_TOP_POCKET_SHARE_BUTTON_VISIBLE);
//LINE@シェアボタンの表示
update_theme_option(OP_TOP_LINE_AT_SHARE_BUTTON_VISIBLE);
//Pinterestシェアボタンの表示
update_theme_option(OP_TOP_PINTEREST_SHARE_BUTTON_VISIBLE);
//LinkedInシェアボタンの表示
update_theme_option(OP_TOP_LINKEDIN_SHARE_BUTTON_VISIBLE);
//コピーシェアボタンの表示
update_theme_option(OP_TOP_COPY_SHARE_BUTTON_VISIBLE);
//フロントページシェアボタンの表示
update_theme_option(OP_SNS_FRONT_PAGE_TOP_SHARE_BUTTONS_VISIBLE);
//投稿シェアボタンの表示
update_theme_option(OP_SNS_SINGLE_TOP_SHARE_BUTTONS_VISIBLE);
//固定ページシェアボタンの表示
update_theme_option(OP_SNS_PAGE_TOP_SHARE_BUTTONS_VISIBLE);
//カテゴリーシェアボタンの表示
update_theme_option(OP_SNS_CATEGORY_TOP_SHARE_BUTTONS_VISIBLE);
//タグシェアボタンの表示
update_theme_option(OP_SNS_TAG_TOP_SHARE_BUTTONS_VISIBLE);
//SNSシェアボタンカラー
update_theme_option(OP_SNS_TOP_SHARE_BUTTON_COLOR);
//シェアボタンのカラム数
update_theme_option(OP_SNS_TOP_SHARE_COLUMN_COUNT);
//SNSシェアボタンのロゴとキャプションの位置
update_theme_option(OP_SNS_TOP_SHARE_LOGO_CAPTION_POSITION);
//SNSトップシェア数の表示
update_theme_option(OP_SNS_TOP_SHARE_BUTTONS_COUNT_VISIBLE);
