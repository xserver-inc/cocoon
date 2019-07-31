<?php //アピールエリア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//お勧め記事の表示
update_theme_option(OP_RECOMMENDED_CARDS_DISPLAY_TYPE);

//お勧め記事のメニュー名
update_theme_option(OP_RECOMMENDED_CARDS_MENU_NAME);

//お勧め記事のタイトルを表示するか
update_theme_option(OP_RECOMMENDED_CARDS_TITLE_VISIBLE);
