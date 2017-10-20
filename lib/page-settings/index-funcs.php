<?php //インデックス設定に必要な定数や関数
///////////////////////////////////////
// リスト表示
///////////////////////////////////////
//リスト表示タイプ
define('OP_INDEX_LIST_TYPE', 'index_list_type');
if ( !function_exists( 'get_index_list_type' ) ):
function get_index_list_type(){
  return get_theme_option(OP_INDEX_LIST_TYPE, 'entry_card');
}
endif;
