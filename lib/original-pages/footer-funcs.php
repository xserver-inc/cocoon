<?php //管理画面設定に必要な定数や関数

//アドミンバーに独自管理メニューを表示
define('OP_ADMIN_TOOL_MENU_VISIBLE', 'admin_tool_menu_visible');
if ( !function_exists( 'is_admin_tool_menu_visible' ) ):
function is_admin_tool_menu_visible(){
  return get_option(OP_ADMIN_TOOL_MENU_VISIBLE, 1);
}
endif;

//ページ公開前に確認アラートを出す
define('OP_CONFIRMATION_BEFORE_PUBLISH', 'confirmation_before_publish');
if ( !function_exists( 'is_confirmation_before_publish' ) ):
function is_confirmation_before_publish(){
  return get_option(OP_CONFIRMATION_BEFORE_PUBLISH, 1);
}
endif;

//タイトル等の文字数カウンター表示
define('OP_ADMIN_EDITOR_COUNTER_VISIBLE', 'admin_editor_counter_visible');
if ( !function_exists( 'is_admin_editor_counter_visible' ) ):
function is_admin_editor_counter_visible(){
  return get_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE, 1);
}
endif;

