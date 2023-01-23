<?php //投稿設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
///////////////////////////////////////
// 関連記事
///////////////////////////////////////

//カテゴリー・タグ表示タイプ
update_theme_option(OP_CATEGORY_TAG_DISPLAY_TYPE);

//カテゴリー・タグ表示位置
update_theme_option(OP_CATEGORY_TAG_DISPLAY_POSITION);

//関連記事の表示
update_theme_option(OP_RELATED_ENTRIES_VISIBLE);

//関連記事の関連性
update_theme_option(OP_RELATED_ASSOCIATION_TYPE);

//関連記事のタイトル
update_theme_option(OP_RELATED_ENTRY_HEADING);

//関連記事のサブタイトル
update_theme_option(OP_RELATED_ENTRY_SUB_HEADING);

//関連記事の表示数
update_theme_option(OP_RELATED_ENTRY_COUNT);

//関連記事取得期間
update_theme_option(OP_RELATED_ENTRY_PERIOD);

//関連記事の表示タイプ
update_theme_option(OP_RELATED_ENTRY_TYPE);

//関連記事枠線の表示
update_theme_option(OP_RELATED_ENTRY_BORDER_VISIBLE);

//関連記事抜粋文の最大文字数
update_theme_option(OP_RELATED_EXCERPT_MAX_LENGTH);

//スニペットを表示
update_theme_option(OP_RELATED_ENTRY_CARD_SNIPPET_VISIBLE);

//スマートフォンスニペット表示
update_theme_option(OP_SMARTPHONE_RELATED_ENTRY_CARD_SNIPPET_VISIBLE);

//投稿日を表示
update_theme_option(OP_RELATED_ENTRY_CARD_POST_DATE_VISIBLE);

//投稿日を表示しない場合、更新日がなければ投稿日を表示
update_theme_option(OP_RELATED_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE);

//更新日を表示
update_theme_option(OP_RELATED_ENTRY_CARD_POST_UPDATE_VISIBLE);

//投稿者を表示
update_theme_option(OP_RELATED_ENTRY_CARD_POST_AUTHOR_VISIBLE);
///////////////////////////////////////
// ページ送りナビ
///////////////////////////////////////

//ページ送りナビの表示
update_theme_option(OP_POST_NAVI_VISIBLE);

//ページ送りナビの表示タイプ
update_theme_option(OP_POST_NAVI_TYPE);

//ページ送りナビの表示位置
update_theme_option(OP_POST_NAVI_POSITION);

//ページ送りナビに表示するカテゴリーは同一にする
update_theme_option(OP_POST_NAVI_SAME_CATEGORY_ENABLE);

//ページ送りナビで除外するカテゴリーID
update_theme_option(OP_POST_NAVI_EXCLUDE_CATEGORY_IDS);

//ページ送りナビ枠線の表示
update_theme_option(OP_POST_NAVI_BORDER_VISIBLE);


///////////////////////////////////////
// コメント
///////////////////////////////////////

//コメント表示
update_theme_option(OP_SINGLE_COMMENT_VISIBLE);

///////////////////////////////////////
// パンくずリスト
//////////////////////////////////////

//パンくずリストの位置
update_theme_option(OP_SINGLE_BREADCRUMBS_POSITION);

//パンくずリストに当該記事を含めるか
update_theme_option(OP_SINGLE_BREADCRUMBS_INCLUDE_POST);
