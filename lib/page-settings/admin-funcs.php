<?php //管理画面設定に必要な定数や関数

//アドミンバーに独自管理メニューを表示
define('OP_ADMIN_TOOL_MENU_VISIBLE', 'admin_tool_menu_visible');
if ( !function_exists( 'is_admin_tool_menu_visible' ) ):
function is_admin_tool_menu_visible(){
  return get_theme_option(OP_ADMIN_TOOL_MENU_VISIBLE, 1);
}
endif;

//ページ公開前に確認アラートを出す
define('OP_CONFIRMATION_BEFORE_PUBLISH', 'confirmation_before_publish');
if ( !function_exists( 'is_confirmation_before_publish' ) ):
function is_confirmation_before_publish(){
  return get_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH, 1);
}
endif;

//タイトル等の文字数カウンター表示
define('OP_ADMIN_EDITOR_COUNTER_VISIBLE', 'admin_editor_counter_visible');
if ( !function_exists( 'is_admin_editor_counter_visible' ) ):
function is_admin_editor_counter_visible(){
  return get_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE, 1);
}
endif;


///////////////////////////////////////
// 管理者パネル
///////////////////////////////////////

//管理者パネルを表示
define('OP_ADMIN_PANEL_VISIBLE', 'admin_panel_visible');
if ( !function_exists( 'is_admin_panel_visible' ) ):
function is_admin_panel_visible(){
  return get_theme_option(OP_ADMIN_PANEL_VISIBLE, 1);
}
endif;

//管理者パネルのPVを表示
define('OP_ADMIN_PANEL_PV_AREA_VISIBLE', 'admin_panel_pv_area_visible');
if ( !function_exists( 'is_admin_panel_pv_area_visible' ) ):
function is_admin_panel_pv_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_PV_AREA_VISIBLE, 1);
}
endif;

//管理者パネルのPV取得方法
define('OP_ADMIN_PANEL_PV_TYPE', 'admin_panel_pv_type');
if ( !function_exists( 'get_admin_panel_pv_type' ) ):
function get_admin_panel_pv_type(){
  return get_theme_option(OP_ADMIN_PANEL_PV_TYPE, THEME_NAME);
}
endif;

//管理者パネル編集エリアの表示
define('OP_ADMIN_PANEL_EDIT_AREA_VISIBLE', 'admin_panel_edit_area_visible');
if ( !function_exists( 'is_admin_panel_edit_area_visible' ) ):
function is_admin_panel_edit_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE, 1);
}
endif;

//管理者パネル編集エリアの表示
define('OP_ADMIN_PANEL_EDIT_AREA_VISIBLE', 'admin_panel_edit_area_visible');
if ( !function_exists( 'is_admin_panel_edit_area_visible' ) ):
function is_admin_panel_edit_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE, 1);
}
endif;

//管理者パネルWordpress編集の表示
define('OP_ADMIN_PANEL_WP_EDIT_VISIBLE', 'admin_panel_wp_edit_visible');
if ( !function_exists( 'is_admin_panel_wp_edit_visible' ) ):
function is_admin_panel_wp_edit_visible(){
  return get_theme_option(OP_ADMIN_PANEL_WP_EDIT_VISIBLE, 1);
}
endif;

//管理者パネルWindows Live Writer編集の表示
define('OP_ADMIN_PANEL_WLW_EDIT_VISIBLE', 'admin_panel_wlw_edit_visible');
if ( !function_exists( 'is_admin_panel_wlw_edit_visible' ) ):
function is_admin_panel_wlw_edit_visible(){
  return get_theme_option(OP_ADMIN_PANEL_WLW_EDIT_VISIBLE);
}
endif;

//管理者パネルAMPエリアの表示
define('OP_ADMIN_PANEL_AMP_AREA_VISIBLE', 'admin_panel_amp_area_visible');
if ( !function_exists( 'is_admin_panel_amp_area_visible' ) ):
function is_admin_panel_amp_area_visible(){
  return get_theme_option(OP_ADMIN_PANEL_AMP_AREA_VISIBLE, 1);
}
endif;