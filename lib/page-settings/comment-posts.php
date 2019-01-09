<?php //コメント設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//コメント表示形式
update_theme_option(OP_COMMENT_DISPLAY_TYPE);

//コメントの見出し
update_theme_option(OP_COMMENT_HEADING);

//コメントのサブ見出し
update_theme_option(OP_COMMENT_SUB_HEADING);

//コメント入力欄の表示タイプ
update_theme_option(OP_COMMENT_FORM_DISPLAY_TYPE);

//コメント入力欄の見出し
update_theme_option(OP_COMMENT_FORM_HEADING);

//コメント案内メッセージ
update_theme_option(OP_COMMENT_INFORMATION_MESSAGE);

//ウェブサイト入力欄表示
update_theme_option(OP_COMMENT_WEBSITE_VISIBLE);

//コメント送信ボタンのラベル
update_theme_option(OP_COMMENT_SUBMIT_LABEL);
