<?php //インデックス設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//フロントページタイプ
update_theme_option(OP_FRONT_PAGE_TYPE);

//タブインデックスカテゴリー
update_theme_option(OP_INDEX_CATEGORY_IDS);

//タブインデックスカテゴリー（カンマテキスト）
update_theme_option(OP_INDEX_CATEGORY_IDS_COMMA_TEXT);

//インデックス新着エントリーカード数
update_theme_option(OP_INDEX_NEW_ENTRY_CARD_COUNT);

//インデックスカテゴリーエントリーカード数
update_theme_option(OP_INDEX_CATEGORY_ENTRY_CARD_COUNT);

//インデックスの並び順
update_theme_option(OP_INDEX_SORT_ORDERBY);

//エントリーカードタイプ
update_theme_option(OP_ENTRY_CARD_TYPE);

//スマートフォンのエントリーカードを1カラムに
update_theme_option(OP_SMARTPHONE_ENTRY_CARD_1_COLUMN);

//エントリーカード枠線の表示
update_theme_option(OP_ENTRY_CARD_BORDER_VISIBLE);

//エントリーカード抜粋文の最大文字数
update_theme_option(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH);

//エントリーカード抜粋文の最大文字数を超えたときの文字列
update_theme_option(OP_ENTRY_CARD_EXCERPT_MORE);

///////////////////////////////////////
// 投稿情報の表示
///////////////////////////////////////

//スニペットを表示
update_theme_option(OP_ENTRY_CARD_SNIPPET_VISIBLE);

//スマートフォンスニペット表示
update_theme_option(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE);

//投稿日を表示
update_theme_option(OP_ENTRY_CARD_POST_DATE_VISIBLE);

//投稿日を表示しない場合、更新日がなければ投稿日を表示
update_theme_option(OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE);

//更新日を表示
update_theme_option(OP_ENTRY_CARD_POST_UPDATE_VISIBLE);

//投稿者を表示
update_theme_option(OP_ENTRY_CARD_POST_AUTHOR_VISIBLE);

//コメント数を表示
update_theme_option(OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE);

//インデックスリストに表示しないカテゴリーID
update_theme_option(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS);
