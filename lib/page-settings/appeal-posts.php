<?php //アピールエリア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アピールエリアの表示
update_theme_option(OP_APPEAL_AREA_DISPLAY_TYPE);

//アピールエリアの高さ
update_theme_option(OP_APPEAL_AREA_HEIGHT);

//アピールエリア画像
update_theme_option(OP_APPEAL_AREA_IMAGE_URL);

//アピールエリア背景色
update_theme_option(OP_APPEAL_AREA_BACKGROUND_COLOR);

//アピールエリア背景を固定にするか
update_theme_option(OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED);

//アピールエリアタイトル
update_theme_option(OP_APPEAL_AREA_TITLE);

//アピールエリアメッセージ
update_theme_option(OP_APPEAL_AREA_MESSAGE);

//アピールエリアボタンメッセージ
update_theme_option(OP_APPEAL_AREA_BUTTON_MESSAGE);

//アピールエリアボタンURL
update_theme_option(OP_APPEAL_AREA_BUTTON_URL);

//アピールエリアボタンのブラウザでの開き方
update_theme_option(OP_APPEAL_AREA_BUTTON_TARGET);

//アピールエリアボタン色
update_theme_option(OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR);
