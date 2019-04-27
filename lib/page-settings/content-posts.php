<?php //コンテンツ設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// 本文行間
///////////////////////////////////////
//行の高さ
update_theme_option(OP_ENTRY_CONTENT_LINE_HIGHT);

//行の余白
update_theme_option(OP_ENTRY_CONTENT_MARGIN_HIGHT);

///////////////////////////////////////
// 外部リンク
///////////////////////////////////////

//外部リンクの開き方
update_theme_option(OP_EXTERNAL_LINK_OPEN_TYPE);

//外部リンクのフォロータイプ
update_theme_option(OP_EXTERNAL_LINK_FOLLOW_TYPE);

//noopener
update_theme_option(OP_EXTERNAL_LINK_NOOPENER_ENABLE);

//target="_blank"のnoopener
update_theme_option(OP_EXTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE);

//noreferrer
update_theme_option(OP_EXTERNAL_LINK_NOREFERRER_ENABLE);

//target="_blank"のnoreferrer
update_theme_option(OP_EXTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE);

//external
update_theme_option(OP_EXTERNAL_LINK_EXTERNAL_ENABLE);

//外部リンクアイコン表示
update_theme_option(OP_EXTERNAL_LINK_ICON_VISIBLE);

//外部リンクアイコン
update_theme_option(OP_EXTERNAL_LINK_ICON);

///////////////////////////////////////
// 内部リンク
///////////////////////////////////////

//内部リンクの開き方
update_theme_option(OP_INTERNAL_LINK_OPEN_TYPE);

//内部リンクのフォロータイプ
update_theme_option(OP_INTERNAL_LINK_FOLLOW_TYPE);

//noopener
update_theme_option(OP_INTERNAL_LINK_NOOPENER_ENABLE);

//target="_blank"のnoopener
update_theme_option(OP_INTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE);

//noreferrer
update_theme_option(OP_INTERNAL_LINK_NOREFERRER_ENABLE);

//target="_blank"のnoreferrer
update_theme_option(OP_INTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE);

//内部リンクアイコン表示
update_theme_option(OP_INTERNAL_LINK_ICON_VISIBLE);

//内部リンクアイコン
update_theme_option(OP_INTERNAL_LINK_ICON);

///////////////////////////////////////
// テーブル
///////////////////////////////////////

//レスポンシブテーブル
update_theme_option(OP_RESPONSIVE_TABLE_ENABLE);

///////////////////////////////////////
// 投稿情報の表示
///////////////////////////////////////

//投稿日を表示
update_theme_option(OP_POST_DATE_VISIBLE);

//更新日を表示
update_theme_option(OP_POST_UPDATE_VISIBLE);

//投稿者を表示
update_theme_option(OP_POST_AUTHOR_VISIBLE);

//記事を読む時間表示
update_theme_option(OP_CONTENT_READ_TIME_VISIBLE);
