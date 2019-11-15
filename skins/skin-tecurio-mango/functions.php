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
