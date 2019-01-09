<?php //ボタン設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//トップへ戻るボタンの表示
update_theme_option(OP_GO_TO_TOP_BUTTON_VISIBLE);

//トップへ戻るボタンのアイコンフォント
update_theme_option(OP_GO_TO_TOP_BUTTON_ICON_FONT);

//トップへ戻るボタンの画像
update_theme_option(OP_GO_TO_TOP_BUTTON_IMAGE_URL);

//ボタン背景色
update_theme_option(OP_GO_TO_TOP_BACKGROUND_COLOR);

//ボタン文字色
update_theme_option(OP_GO_TO_TOP_TEXT_COLOR);
