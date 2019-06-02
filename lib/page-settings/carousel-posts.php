<?php //アピールエリア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//カルーセルの表示
update_theme_option(OP_CAROUSEL_DISPLAY_TYPE);

//カルーセルをスマホで表示する
update_theme_option(OP_CAROUSEL_SMARTPHONE_VISIBLE);

//カルーセルに表示するカテゴリID
update_theme_option(OP_CAROUSEL_CATEGORY_IDS);

//カルーセルに表示するタグID
update_theme_option(OP_CAROUSEL_TAG_IDS);

//カルーセルの表示順
update_theme_option(OP_CAROUSEL_ORDERBY);

//カルーセルに表示する最大数
update_theme_option(OP_CAROUSEL_MAX_COUNT);

//カードの枠線を表示する
update_theme_option(OP_CAROUSEL_CARD_BORDER_VISIBLE);

//カルーセルオートプレイ
update_theme_option(OP_CAROUSEL_AUTOPLAY_ENABLE);

//カルーセルオートプレイインターバル
update_theme_option(OP_CAROUSEL_AUTOPLAY_INTERVAL);
