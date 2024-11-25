<?php //タイトル設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//フロントページのタイトルタイプ
update_theme_option(OP_FRONT_PAGE_TITLE_FORMAT);

//自由形式のフロントページのイトル
update_theme_option(OP_FREE_FRONT_PAGE_TITLE);

//フロントページにメタディスクリプションを含める
update_theme_option(OP_META_DESCRIPTION_TO_FRONT_PAGE);

//フロントページのメタディスクリプション
update_theme_option(OP_FRONT_PAGE_META_DESCRIPTION);

//フロントページにメタキーワードを含める
update_theme_option(OP_META_KEYWORDS_TO_FRONT_PAGE);

//フロントページのメタディスクリプション
update_theme_option(OP_FRONT_PAGE_META_KEYWORDS);

//投稿・固定ページのタイトル
update_theme_option(OP_SINGULAR_PAGE_TITLE_FORMAT);

//投稿・固定ページにメタディスクリプションを含める
update_theme_option(OP_META_DESCRIPTION_TO_SINGULAR);

//投稿・固定ページにメタキーワードを含める
update_theme_option(OP_META_KEYWORDS_TO_SINGULAR);

//カテゴリーページのタイトル
update_theme_option(OP_CATEGORY_PAGE_TITLE_FORMAT);

//カテゴリーページにメタディスクリプションを含める
update_theme_option(OP_META_DESCRIPTION_TO_CATEGORY);

//カテゴリーページにメタキーワードを含める
update_theme_option(OP_META_KEYWORDS_TO_CATEGORY);

//検索エンジンに知らせる日付
update_theme_option(OP_SEO_DATE_TYPE);

//簡略化したサイト名
update_theme_option(OP_SIMPLIFIED_SITE_NAME);

//タイトルセパレーター
update_theme_option(OP_TITLE_SEPARATOR);
