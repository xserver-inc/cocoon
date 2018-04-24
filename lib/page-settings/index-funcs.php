<?php //インデックス設定に必要な定数や関数
///////////////////////////////////////
// リスト表示
///////////////////////////////////////
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
  return get_theme_option(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE, 1);
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