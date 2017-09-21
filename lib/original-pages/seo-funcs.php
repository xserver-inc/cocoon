<?php //アクセス解析設定に必要な定数や関数

//canonicalタグの追加
define('OP_CANONICAL_TAG_ENABLE', 'canonical_tag_enable');
if ( !function_exists( 'is_canonical_tag_enable' ) ):
function is_canonical_tag_enable(){
  return get_option(OP_CANONICAL_TAG_ENABLE, 1);
}
endif;

//分割ページにrel="next"/"prev"タグの追加
define('OP_PREV_NEXT_ENABLE', 'prev_next_enable');
if ( !function_exists( 'is_prev_next_enable' ) ):
function is_prev_next_enable(){
  return get_option(OP_PREV_NEXT_ENABLE, 1);
}
endif;

//カテゴリページの2ページ目以降をnoindexとする 
define('OP_PAGED_CATEGORY_PAGE_NOINDEX', 'paged_category_page_noindex');
if ( !function_exists( 'is_paged_category_page_noindex' ) ):
function is_paged_category_page_noindex(){
  return get_option(OP_PAGED_CATEGORY_PAGE_NOINDEX);
}
endif;
