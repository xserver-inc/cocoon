<?php //コメント設定に必要な定数や関数

//コメントの見出し
define('OP_COMMENT_HEADING', 'comment_heading');
if ( !function_exists( 'get_comment_heading' ) ):
function get_comment_heading(){
  return get_theme_option(OP_COMMENT_HEADING, __( 'コメント', THEME_NAME ));
}
endif;

//コメントのサブ見出し
define('OP_COMMENT_SUB_HEADING', 'comment_sub_heading');
if ( !function_exists( 'get_comment_sub_heading' ) ):
function get_comment_sub_heading(){
  return get_theme_option(OP_COMMENT_SUB_HEADING);
}
endif;

//コメント入力欄の見出し
define('OP_COMMENT_FORM_HEADING', 'comment_form_heading');
if ( !function_exists( 'get_comment_form_heading' ) ):
function get_comment_form_heading(){
  return get_theme_option(OP_COMMENT_FORM_HEADING, __( 'コメントをどうぞ', THEME_NAME ));
}
endif;

//ウェブサイト入力欄表示
define('OP_COMMENT_WEBSITE_VISIBLE', 'comment_website_visible');
if ( !function_exists( 'is_comment_website_visible' ) ):
function is_comment_website_visible(){
  return get_theme_option(OP_COMMENT_WEBSITE_VISIBLE, 1);
}
endif;

//コメント送信ボタンのラベル
define('OP_COMMENT_SUBMIT_LABEL', 'comment_submit_label');
if ( !function_exists( 'get_comment_submit_label' ) ):
function get_comment_submit_label(){
  return get_theme_option(OP_COMMENT_SUBMIT_LABEL, __( 'コメントを送信', THEME_NAME ));
}
endif;
