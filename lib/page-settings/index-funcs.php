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
