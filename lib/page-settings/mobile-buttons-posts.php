<?php //モバイルボタン設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//モバイルボタンレイアウト
update_theme_option(OP_MOBILE_BUTTON_LAYOUT_TYPE);

//モバイルボタンを固定表示するか
update_theme_option(OP_FIXED_MOBILE_BUTTONS_ENABLE);

//スライドインメニュー表示の際にメインコンテンツ下にサイドバーを表示するか
update_theme_option(OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE);
