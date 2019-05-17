<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
/*
///////////////////////////////////////////
// 設定操作サンプル
// lib\page-settings\内のXXXXX-funcs.phpに書かれている
// 定義済み関数をオーバーライドして設定を上書きできます。
// 関数をオーバーライドする場合は必ず!function_existsで
// 存在を確認してください。
///////////////////////////////////////////
//ヘッダーロゴを「トップメニューにするコードサンプル
if ( !function_exists( 'get_header_layout_type' ) ):
function get_header_layout_type(){
  return 'top_menu';
}
endif;

//メインカラム幅を680pxにするサンプル
if ( !function_exists( 'get_main_column_contents_width' ) ):
function get_main_column_contents_width(){
  return 680;
}
endif;

//エントリーカードの枠線を表示するサンプル
if ( !function_exists( 'is_entry_card_border_visible' ) ):
function is_entry_card_border_visible(){
  return true;
}
endif;

*/

global $_THEME_OPTIONS;
$_THEME_OPTIONS =
array(
  'entry_card_type' => 'entry_card',
  'entry_card_border_visible' => 0,
	'smartphone_entry_card_1_column' => 0,
	'entry_card_excerpt_max_length' => 60,
	'related_excerpt_max_length' => 50,
	'related_entry_border_visible' =>0,
	'related_entry_type' => 'entry_card',
  'amp_skin_style_enable' => 0,
);
