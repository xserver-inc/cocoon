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

//ビジュアルエディタースタイル
update_theme_option(OP_VISUAL_EDITOR_STYLE_ENABLE);

//エディター背景色
update_theme_option(OP_EDITOR_BACKGROUND_COLOR);

//エディター文字色
update_theme_option(OP_EDITOR_TEXT_COLOR);

//ルビボタン有効
update_theme_option(OP_BLOCK_EDITOR_RUBY_BUTTON_VISIBLE);

//ブロックエディターインラインスタイルドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_LETTER_STYLE_DROPDOWN_VISIBLE);

//ブロックエディターマーカースタイルドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_MARKER_STYLE_DROPDOWN_VISIBLE);

//ブロックエディターバッジスタイルドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_BADGE_STYLE_DROPDOWN_VISIBLE);

//ブロックエディター文字サイズスタイルドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_FONT_SIZE_STYLE_DROPDOWN_VISIBLE);

//ブロックエディター汎用ショートコードドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_GENERAL_SHORTCODE_DROPDOWN_VISIBLE);

//ブロックエディターテンプレートショートコードドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_TEMPLATE_SHORTCODE_DROPDOWN_VISIBLE);

//ブロックエディターアフィリエイトショートコードドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_AFFILIATE_SHORTCODE_DROPDOWN_VISIBLE);

//ブロックエディターランキングショートコードドロップダウン有効
update_theme_option(OP_BLOCK_EDITOR_RANKING_SHORTCODE_DROPDOWN_VISIBLE);

//タイトル等の文字数カウンター表示
update_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE);

//ページ公開前に確認アラートを出す
update_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH);
