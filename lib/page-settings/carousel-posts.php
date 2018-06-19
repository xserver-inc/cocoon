<?php //アピールエリア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//カルーセルの表示
update_theme_option(OP_CAROUSEL_DISPLAY_TYPE);

//カルーセルに表示するカテゴリID
update_theme_option(OP_CAROUSEL_CATEGORY_IDS);

//カルーセルオートプレイ
update_theme_option(OP_CAROUSEL_AUTOPLAY_ENABLE);

//カルーセルオートプレイインターバル
update_theme_option(OP_CAROUSEL_AUTOPLAY_INTERVAL);