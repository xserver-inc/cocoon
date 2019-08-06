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

//お勧め記事の表示スタイル
update_theme_option(OP_RECOMMENDED_CARDS_STYLE);

//お勧め記事の余白は有効か
update_theme_option(OP_RECOMMENDED_CARDS_MARGIN_ENABLE);

//お勧め記事エリアの左右余白は有効か
update_theme_option(OP_RECOMMENDED_CARDS_AREA_BOTH_SIDES_MARGIN_ENABLE);
