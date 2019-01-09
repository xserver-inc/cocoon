<?php //通知
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//通知エリアを表示するか
update_theme_option(OP_NOTICE_AREA_VISIBLE);

//通知エリアメッセージ
update_theme_option(OP_NOTICE_AREA_MESSAGE);

//通知エリアURL
update_theme_option(OP_NOTICE_AREA_URL);

//通知リンクを新しいタブで開く
update_theme_option(OP_NOTICE_LINK_TARGET_BLANK);

//通知タイプ
update_theme_option(OP_NOTICE_TYPE);

//通知エリア背景色
update_theme_option(OP_NOTICE_AREA_BACKGROUND_COLOR);

//通知エリア背景色
update_theme_option(OP_NOTICE_AREA_TEXT_COLOR);
