<?php //その他設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Gutenbergエディターの有効化
update_theme_option(OP_GUTENBERG_EDITOR_ENABLE);

//タイトル等の文字数カウンター表示
update_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE);

//ビジュアルエディタースタイル
update_theme_option(OP_VISUAL_EDITOR_STYLE_ENABLE);

//ページ公開前に確認アラートを出す
update_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH);
