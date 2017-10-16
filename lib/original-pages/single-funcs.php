<?php //投稿設定に必要な定数や関数
///////////////////////////////////////
// 関連記事
///////////////////////////////////////

//関連記事の表示
define('OP_RELATED_ENTRIES_VISIBLE', 'related_entries_visible');
if ( !function_exists( 'is_related_entries_visible' ) ):
function is_related_entries_visible(){
  return get_option(OP_RELATED_ENTRIES_VISIBLE, 1);
}
endif;

//関連記事の見出し
define('OP_RELATED_ENTRY_HEADING', 'related_entry_heading');
if ( !function_exists( 'get_related_entry_heading' ) ):
function get_related_entry_heading(){
  return get_option(OP_RELATED_ENTRY_HEADING, __( '関連記事', THEME_NAME ));
}
endif;

//関連記事のサブ見出し
define('OP_RELATED_ENTRY_SUB_HEADING', 'related_entry_sub_heading');
if ( !function_exists( 'get_related_entry_sub_heading' ) ):
function get_related_entry_sub_heading(){
  return get_option(OP_RELATED_ENTRY_SUB_HEADING);
}
endif;

//関連記事の表示タイプ
define('OP_RELATED_ENTRY_TYPE', 'related_entry_type');
if ( !function_exists( 'get_related_entry_type' ) ):
function get_related_entry_type(){
  return get_option(OP_RELATED_ENTRY_TYPE, 'entry_card');
}
endif;

//関連記事の表示数
define('OP_RELATED_ENTRY_COUNT', 'related_entry_count');
if ( !function_exists( 'get_related_entry_count' ) ):
function get_related_entry_count(){
  return get_option(OP_RELATED_ENTRY_COUNT, 6);
}
endif;

//関連記事枠線の表示
define('OP_RELATED_ENTRY_BORDER_VISIBLE', 'related_entry_border_visible');
if ( !function_exists( 'is_related_entry_border_visible' ) ):
function is_related_entry_border_visible(){
  return get_option(OP_RELATED_ENTRY_BORDER_VISIBLE);
}
endif;

//関連記事抜粋文の最大文字数
define('OP_RELATED_EXCERPT_MAX_LENGTH', 'related_excerpt_max_length');
if ( !function_exists( 'get_related_excerpt_max_length' ) ):
function get_related_excerpt_max_length(){
  return get_option(OP_RELATED_EXCERPT_MAX_LENGTH, 120);
}
endif;

///////////////////////////////////////
// ページ送りナビ
///////////////////////////////////////

//ページ送りナビの表示
define('OP_POST_NAVI_VISIBLE', 'post_navi_visible');
if ( !function_exists( 'is_post_navi_visible' ) ):
function is_post_navi_visible(){
  return get_option(OP_POST_NAVI_VISIBLE, 1);
}
endif;

//ページ送りナビの表示タイプ
define('OP_POST_NAVI_TYPE', 'post_navi_type');
if ( !function_exists( 'get_post_navi_type' ) ):
function get_post_navi_type(){
  return get_option(OP_POST_NAVI_TYPE, 'default');
}
endif;
if ( !function_exists( 'is_post_navi_type_spuare' ) ):
function is_post_navi_type_spuare(){
  return get_post_navi_type() == 'square';
}
endif;

//ページ送りナビ枠線の表示
define('OP_POST_NAVI_BORDER_VISIBLE', 'post_navi_border_visible');
if ( !function_exists( 'is_post_navi_border_visible' ) ):
function is_post_navi_border_visible(){
  return get_option(OP_POST_NAVI_BORDER_VISIBLE);
}
endif;


///////////////////////////////////////
// コメント
///////////////////////////////////////

//コメント表示
define('OP_SINGLE_COMMENT_VISIBLE', 'single_comment_visible');
if ( !function_exists( 'is_single_comment_visible' ) ):
function is_single_comment_visible(){
  return get_option(OP_SINGLE_COMMENT_VISIBLE, 1);
}
endif;

//コメントの見出し
define('OP_SINGLE_COMMENT_HEADING', 'single_comment_heading');
if ( !function_exists( 'get_single_comment_heading' ) ):
function get_single_comment_heading(){
  return get_option(OP_SINGLE_COMMENT_HEADING, __( 'コメント', THEME_NAME ));
}
endif;

//コメントのサブ見出し
define('OP_SINGLE_COMMENT_SUB_HEADING', 'single_comment_sub_heading');
if ( !function_exists( 'get_single_comment_sub_heading' ) ):
function get_single_comment_sub_heading(){
  return get_option(OP_SINGLE_COMMENT_SUB_HEADING);
}
endif;

//コメント入力欄の見出し
define('OP_SINGLE_COMMENT_FORM_HEADING', 'single_comment_form_heading');
if ( !function_exists( 'get_single_comment_form_heading' ) ):
function get_single_comment_form_heading(){
  return get_option(OP_SINGLE_COMMENT_FORM_HEADING, __( 'コメントをどうぞ', THEME_NAME ));
}
endif;

//ウェブサイト入力欄表示
define('OP_SINGLE_COMMENT_WEBSITE_VISIBLE', 'single_comment_website_visible');
if ( !function_exists( 'is_single_comment_website_visible' ) ):
function is_single_comment_website_visible(){
  return get_option(OP_SINGLE_COMMENT_WEBSITE_VISIBLE, 1);
}
endif;
