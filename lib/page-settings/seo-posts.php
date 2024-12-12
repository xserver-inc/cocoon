<?php //SEO設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//canonicalタグを追加する
update_theme_option(OP_CANONICAL_TAG_ENABLE);

//分割ページにrel="next"/"prev"タグを追加する
update_theme_option(OP_PREV_NEXT_ENABLE);

//カテゴリーページをnoindexとする
update_theme_option(OP_CATEGORY_PAGE_NOINDEX);

//カテゴリーページの2ページ目以降をnoindexとする
update_theme_option(OP_PAGED_CATEGORY_PAGE_NOINDEX);

//タグページをnoindexとする
update_theme_option(OP_TAG_PAGE_NOINDEX);

//タグページの2ページ目以降をnoindexとする
update_theme_option(OP_PAGED_TAG_PAGE_NOINDEX);

//その他のアーカイブページをnoindexとする
update_theme_option(OP_OTHER_ARCHIVE_PAGE_NOINDEX);

//添付ファイルページをnoindexとする
update_theme_option(OP_ATTACHMENT_PAGE_NOINDEX);

//JSON-LDを出力する
update_theme_option(OP_JSON_LD_TAG_ENABLE);

//メタディスクリプションのリファラ
update_theme_option(OP_META_REFERRER_CONTENT);
