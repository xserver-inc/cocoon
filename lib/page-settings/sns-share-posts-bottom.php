<?php //SNSシェア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//シェアボタンの表示
update_theme_option(OP_SNS_BOTTOM_SHARE_BUTTONS_VISIBLE);
//SNSシェアメッセージ
update_theme_option(OP_SNS_BOTTOM_SHARE_MESSAGE);
//Twitterシェアボタンの表示
update_theme_option(OP_BOTTOM_TWITTER_SHARE_BUTTON_VISIBLE);
//Facebookシェアボタンの表示
update_theme_option(OP_BOTTOM_FACEBOOK_SHARE_BUTTON_VISIBLE);
//はてなブックマークシェアボタンの表示
update_theme_option(OP_BOTTOM_HATEBU_SHARE_BUTTON_VISIBLE);
//Google+シェアボタンの表示
update_theme_option(OP_BOTTOM_GOOGLE_PLUS_SHARE_BUTTON_VISIBLE);
//Pocketシェアボタンの表示
update_theme_option(OP_BOTTOM_POCKET_SHARE_BUTTON_VISIBLE);
//LINE@シェアボタンの表示
update_theme_option(OP_BOTTOM_LINE_AT_SHARE_BUTTON_VISIBLE);
//ボトムPinterestシェアボタンの表示
update_theme_option(OP_BOTTOM_PINTEREST_SHARE_BUTTON_VISIBLE);
//コピーシェアボタンの表示
update_theme_option(OP_BOTTOM_COPY_SHARE_BUTTON_VISIBLE);
//SNSシェアボタンカラー
update_theme_option(OP_SNS_BOTTOM_SHARE_BUTTON_COLOR);
//シェアボタンのカラム数
update_theme_option(OP_SNS_BOTTOM_SHARE_COLUMN_COUNT);
//SNSシェアボタンのロゴとキャプションの位置
update_theme_option(OP_SNS_BOTTOM_SHARE_LOGO_CAPTION_POSITION);
//SNSボトムシェア数の表示
update_theme_option(OP_SNS_BOTTOM_SHARE_BUTTONS_COUNT_VISIBLE);
// //ツイートにメンションを含める
// update_theme_option(OP_TWITTER_ID_INCLUDE);
// //ツイート後にフォローを促す
// update_theme_option(OP_TWITTER_RELATED_FOLLOW_ENABLE);

