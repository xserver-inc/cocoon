<?php //目次設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//目次の表示
update_theme_option(OP_TOC_VISIBLE);

//投稿ページで目次の表示
update_theme_option(OP_SINGLE_TOC_VISIBLE);

//固定ページで目次の表示
update_theme_option(OP_PAGE_TOC_VISIBLE);

//目次タイトル
update_theme_option(OP_TOC_TITLE);

//目次の表示切替
update_theme_option(OP_TOC_TOGGLE_SWITCH_ENABLE);

//目次を開くキャプション
update_theme_option(OP_TOC_OPEN_CAPTION);

//目次を閉じるキャプション
update_theme_option(OP_TOC_CLOSE_CAPTION);

//目次内容の表示
update_theme_option(OP_TOC_CONTENT_VISIBLE);

//目次表示条件（数）
update_theme_option(OP_TOC_DISPLAY_COUNT);

//目次を表示する深さ
update_theme_option(OP_TOC_DEPTH);

//目次の数字の表示
update_theme_option(OP_TOC_NUMBER_TYPE);

//目次の中央表示
update_theme_option(OP_TOC_POSITION_CENTER);

//目次を広告の手前に表示
update_theme_option(OP_TOC_BEFORE_ADS);

//見出し内のHTMLタグを有効にする
update_theme_option(OP_TOC_HEADING_INNER_HTML_TAG_ENABLE);
