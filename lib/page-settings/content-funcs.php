<?php //コンテンツ設定に必要な定数や関数

//外部リンクの開き方
define('OP_EXTERNAL_LINK_OPEN_TYPE', 'external_link_open_type');
if ( !function_exists( 'get_external_link_open_type' ) ):
function get_external_link_open_type(){
  return get_theme_option(OP_EXTERNAL_LINK_OPEN_TYPE, 'defalt');
}
endif;
if ( !function_exists( 'get_external_link_open_type_default' ) ):
function get_external_link_open_type_default(){
  return get_external_link_open_type() == 'defalt';
}
endif;

//外部リンクのフォロータイプ
define('OP_EXTERNAL_LINK_FOLLOW_TYPE', 'external_link_follow_type');
if ( !function_exists( 'get_external_link_follow_type' ) ):
function get_external_link_follow_type(){
  return get_theme_option(OP_EXTERNAL_LINK_FOLLOW_TYPE, 'defalt');
}
endif;


//外部リンクアイコン表示
define('OP_EXTERNAL_LINK_ICON_VISIBLE', 'external_link_icon_visible');
if ( !function_exists( 'is_external_link_icon_visible' ) ):
function is_external_link_icon_visible(){
  return get_theme_option(OP_EXTERNAL_LINK_ICON_VISIBLE, 'none');
}
endif;
//外部リンクアイコン
define('OP_EXTERNAL_LINK_ICON', 'external_link_icon');
if ( !function_exists( 'get_external_link_icon' ) ):
function get_external_link_icon(){
  return get_theme_option(OP_EXTERNAL_LINK_ICON, 'none');
}
endif;