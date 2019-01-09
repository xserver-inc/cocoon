<?php //グローバルナビ設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//グローバルナビ背景色
update_theme_option(OP_GLOBAL_NAVI_BACKGROUND_COLOR);

//グローバルナビ文字色
update_theme_option(OP_GLOBAL_NAVI_TEXT_COLOR);

// //グローバルナビホバー背景色
// update_theme_option(OP_GLOBAL_NAVI_HOVER_BACKGROUND_COLOR);

//グローバルナビメニュー幅
update_theme_option(OP_GLOBAL_NAVI_MENU_WIDTH);

//グローバルメニュー幅をテキストの幅にする
update_theme_option(OP_GLOBAL_NAVI_MENU_TEXT_WIDTH_ENABLE);

//グローバルナビサブメニュー幅
update_theme_option(OP_GLOBAL_NAVI_SUB_MENU_WIDTH);

//グローバルナビメニューの固定
update_theme_option(OP_GLOBAL_NAVI_FIXED);
