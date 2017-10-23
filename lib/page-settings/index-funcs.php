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
