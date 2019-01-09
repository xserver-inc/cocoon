<?php //インデックス設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// リスト表示
///////////////////////////////////////
//インデックスの並び順
define('OP_INDEX_SORT_ORDERBY', 'index_sort_orderby');
if ( !function_exists( '' ) ):
function get_index_sort_orderby(){
  return get_theme_option(OP_INDEX_SORT_ORDERBY);
}
endif;
//インデックスの並び順は更新日順か
if ( !function_exists( 'is_get_index_sort_orderby_modified' ) ):
function is_get_index_sort_orderby_modified(){
  return get_index_sort_orderby() == 'modified';
}
endif;

//エントリーカードタイプ
define('OP_ENTRY_CARD_TYPE', 'entry_card_type');
if ( !function_exists( 'get_entry_card_type' ) ):
function get_entry_card_type(){
  return get_theme_option(OP_ENTRY_CARD_TYPE, 'entry_card');
}
endif;
if ( !function_exists( 'is_entry_card_type_entry_card' ) ):
function is_entry_card_type_entry_card(){
  return get_entry_card_type() == 'entry_card';
}
endif;
if ( !function_exists( 'is_entry_card_type_vertical_card_2' ) ):
function is_entry_card_type_vertical_card_2(){
  return get_entry_card_type() == 'vertical_card_2';
}
endif;
if ( !function_exists( 'is_entry_card_type_vertical_card_3' ) ):
function is_entry_card_type_vertical_card_3(){
  return get_entry_card_type() == 'vertical_card_3';
}
endif;
if ( !function_exists( 'is_entry_card_type_tile_card_2' ) ):
function is_entry_card_type_tile_card_2(){
  return get_entry_card_type() == 'tile_card_2';
}
endif;
if ( !function_exists( 'is_entry_card_type_tile_card_3' ) ):
function is_entry_card_type_tile_card_3(){
  return get_entry_card_type() == 'tile_card_3';
}
endif;
if ( !function_exists( 'is_entry_card_type_tile_card' ) ):
function is_entry_card_type_tile_card(){
  return (get_entry_card_type() == 'tile_card_2') || (get_entry_card_type() == 'tile_card_3');
}
endif;
if ( !function_exists( 'is_entry_card_type_2_columns' ) ):
function is_entry_card_type_2_columns(){
  return (get_entry_card_type() == 'vertical_card_2') || (get_entry_card_type() == 'tile_card_2');
}
endif;
if ( !function_exists( 'is_entry_card_type_3_columns' ) ):
function is_entry_card_type_3_columns(){
  return (get_entry_card_type() == 'vertical_card_3') || (get_entry_card_type() == 'tile_card_3');
}
endif;

//スマートフォンのエントリーカードを1カラムに
define('OP_SMARTPHONE_ENTRY_CARD_1_COLUMN', 'smartphone_entry_card_1_column');
if ( !function_exists( 'is_smartphone_entry_card_1_column' ) ):
function is_smartphone_entry_card_1_column(){
  return get_theme_option(OP_SMARTPHONE_ENTRY_CARD_1_COLUMN);
}
endif;

//エントリーカード枠線の表示
define('OP_ENTRY_CARD_BORDER_VISIBLE', 'entry_card_border_visible');
if ( !function_exists( 'is_entry_card_border_visible' ) ):
function is_entry_card_border_visible(){
  return get_theme_option(OP_ENTRY_CARD_BORDER_VISIBLE);
}
endif;

//エントリーカード抜粋文の最大文字数
define('OP_ENTRY_CARD_EXCERPT_MAX_LENGTH', 'entry_card_excerpt_max_length');
if ( !function_exists( 'get_entry_card_excerpt_max_length' ) ):
function get_entry_card_excerpt_max_length(){
  return get_theme_option(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH, 120);
}
endif;

//エントリーカード抜粋文の最大文字数を超えたときの文字列
define('OP_ENTRY_CARD_EXCERPT_MORE', 'entry_card_excerpt_more');
if ( !function_exists( 'get_entry_card_excerpt_more' ) ):
function get_entry_card_excerpt_more(){
  return get_theme_option(OP_ENTRY_CARD_EXCERPT_MORE, __( '...', THEME_NAME ));
}
endif;


//スニペットを表示
define('OP_ENTRY_CARD_SNIPPET_VISIBLE', 'entry_card_snippet_visible');
if ( !function_exists( 'is_entry_card_snippet_visible' ) ):
function is_entry_card_snippet_visible(){
  return get_theme_option(OP_ENTRY_CARD_SNIPPET_VISIBLE, 1);
}
endif;

//スマートフォンスニペット表示
define('OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE', 'smartphone_entry_card_snippet_visible');
if ( !function_exists( 'is_smartphone_entry_card_snippet_visible' ) ):
function is_smartphone_entry_card_snippet_visible(){
  return get_theme_option(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE);
}
endif;

//投稿日を表示
define('OP_ENTRY_CARD_POST_DATE_VISIBLE', 'entry_card_post_date_visible');
if ( !function_exists( 'is_entry_card_post_date_visible' ) ):
function is_entry_card_post_date_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_DATE_VISIBLE, 1);
}
endif;

//投稿日を表示しない場合、更新日がなければ投稿日を表示
define('OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE', 'entry_card_post_date_or_update_visible');
if ( !function_exists( 'is_entry_card_post_date_or_update_visible' ) ):
function is_entry_card_post_date_or_update_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE, 1);
}
endif;

//更新日を表示
define('OP_ENTRY_CARD_POST_UPDATE_VISIBLE', 'entry_card_post_update_visible');
if ( !function_exists( 'is_entry_card_post_update_visible' ) ):
function is_entry_card_post_update_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_UPDATE_VISIBLE);
}
endif;

//投稿者を表示
define('OP_ENTRY_CARD_POST_AUTHOR_VISIBLE', 'entry_card_post_author_visible');
if ( !function_exists( 'is_entry_card_post_author_visible' ) ):
function is_entry_card_post_author_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_AUTHOR_VISIBLE);
}
endif;

//コメント数を表示
define('OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE', 'entry_card_post_comment_count_visible');
if ( !function_exists( 'is_entry_card_post_comment_count_visible' ) ):
function is_entry_card_post_comment_count_visible(){
  return get_theme_option(OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE);
}
endif;
