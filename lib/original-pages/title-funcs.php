<?php //アクセス解析設定に必要な定数や関数

//タイトルセパレーター
define('OP_TITLE_SEPARATOR', 'title_separator');
if ( !function_exists( 'get_title_separator' ) ):
function get_title_separator(){
  return get_option(OP_TITLE_SEPARATOR);
}
endif;

//フロントページのタイトル
define('OP_FRONT_PAGE_TITLE_FORMAT', 'front_page_title_format');
if ( !function_exists( 'get_front_page_title_format' ) ):
function get_front_page_title_format(){
  return get_option(OP_FRONT_PAGE_TITLE_FORMAT);
}
endif;

//フロントページのメタディスクリプション
define('OP_FRONT_PAGE_META_DESCRIPTION', 'front_page_meta_description');
if ( !function_exists( 'get_front_page_meta_description' ) ):
function get_front_page_meta_description(){
  return get_option(OP_FRONT_PAGE_META_DESCRIPTION);
}
endif;

//フロントページのメタディスクリプション
define('OP_FRONT_PAGE_META_KEYWORDS', 'front_page_meta_keywords');
if ( !function_exists( 'get_front_page_meta_keywords' ) ):
function get_front_page_meta_keywords(){
  return get_option(OP_FRONT_PAGE_META_KEYWORDS);
}
endif;

//投稿・固定ページのタイトル
define('OP_SINGULAR_PAGE_TITLE_FORMAT', 'singular_page_title_format');
if ( !function_exists( 'get_singular_page_title_format' ) ):
function get_singular_page_title_format(){
  return get_option(OP_SINGULAR_PAGE_TITLE_FORMAT);
}
endif;

//投稿・固定ページにメタディスクリプションを含める
define('OP_INCLUDE_META_DESCRIPTION_TO_SINGULAR', 'include_meta_description_to_singular');
if ( !function_exists( 'is_include_meta_description_to_singular' ) ):
function is_include_meta_description_to_singular(){
  return get_option(OP_INCLUDE_META_DESCRIPTION_TO_SINGULAR);
}
endif;

//投稿・固定ページにメタキーワードを含める
define('OP_INCLUDE_KEYWORDS_TO_SINGULAR', 'include_keywords_to_singular');
if ( !function_exists( 'is_include_keywords_to_singular' ) ):
function is_include_keywords_to_singular(){
  return get_option(OP_INCLUDE_KEYWORDS_TO_SINGULAR);
}
endif;

//カテゴリページのタイトル
define('OP_CATEGORY_PAGE_TITLE_FORMAT', 'category_page_title_format');
if ( !function_exists( 'get_category_page_title_format' ) ):
function get_category_page_title_format(){
  return get_option(OP_CATEGORY_PAGE_TITLE_FORMAT);
}
endif;

//カテゴリページにメタディスクリプションを含める
define('OP_INCLUDE_META_DESCRIPTION_TO_CATEGORY', 'include_meta_description_to_category');
if ( !function_exists( 'is_include_meta_description_to_category' ) ):
function is_include_meta_description_to_category(){
  return get_option(OP_INCLUDE_META_DESCRIPTION_TO_CATEGORY);
}
endif;

//カテゴリページにメタキーワードを含める
define('OP_INCLUDE_KEYWORDS_TO_CATEGORY', 'include_keywords_to_category');
if ( !function_exists( 'is_include_keywords_to_category' ) ):
function is_include_keywords_to_category(){
  return get_option(OP_INCLUDE_KEYWORDS_TO_CATEGORY);
}
endif;
