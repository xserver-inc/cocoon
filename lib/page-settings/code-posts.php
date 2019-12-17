<?php //アクセス解析設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ソースコードをハイライト表示するか
update_theme_option(OP_CODE_HIGHLIGHT_ENABLE);

//ソースコードの行番号を表示するか
update_theme_option(OP_CODE_ROW_NUMBER_ENABLE);

//ソースコードのライブラリ
update_theme_option(OP_CODE_HIGHLIGHT_PACKAGE);

//ソースコードのハイライトスタイル
update_theme_option(OP_CODE_HIGHLIGHT_STYLE);

//ソースコードをハイライト表示するCSSセレクタ
update_theme_option(OP_CODE_HIGHLIGHT_CSS_SELECTOR);
