<?php //全体設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//サイトキーカラー
update_theme_option(OP_SITE_KEY_COLOR);

//サイトキーテキストカラー
update_theme_option(OP_SITE_KEY_TEXT_COLOR);

//フォント
update_theme_option(OP_SITE_FONT_FAMILY);

//フォントサイズ
update_theme_option(OP_SITE_FONT_SIZE);

//モバイルフォントサイズ
update_theme_option(OP_MOBILE_SITE_FONT_SIZE);

//サイト文字色
update_theme_option(OP_SITE_TEXT_COLOR);

//フォントウエイト
update_theme_option(OP_SITE_FONT_WEIGHT);

//サイトアイコンフォント
update_theme_option(OP_SITE_ICON_FONT);

//サイト背景色
update_theme_option(OP_SITE_BACKGROUND_COLOR);

//サイトリンク色
update_theme_option(OP_SITE_LINK_COLOR);

//サイト選択文字色
update_theme_option(OP_SITE_SELECTION_COLOR);

//サイト選択文字背景色
update_theme_option(OP_SITE_SELECTION_BACKGROUND_COLOR);

//サイト背景画像
update_theme_option(OP_SITE_BACKGROUND_IMAGE_URL);

//サイト幅を揃える
update_theme_option(OP_ALIGN_SITE_WIDTH);

//サイドバーの位置
update_theme_option(OP_SIDEBAR_POSITION);

//サイドバーの表示状態の設定
update_theme_option(OP_SIDEBAR_DISPLAY_TYPE);

// //サイトアイコン
// update_theme_option(OP_SITE_ICON_URL);

//全てのサムネイル表示
update_theme_option(OP_ALL_THUMBNAIL_VISIBLE);

//日付フォーマット
update_theme_option(OP_SITE_DATE_FORMAT);
