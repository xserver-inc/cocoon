<?php //404ページ設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//404ページ画像
update_theme_option(OP_404_IMAGE_URL);

//404ページタイトル
update_theme_option(OP_404_PAGE_TITLE);

//404ページメッセージ
update_theme_option(OP_404_PAGE_MESSAGE);
