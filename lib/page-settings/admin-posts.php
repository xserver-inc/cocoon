<?php //管理画面設定をデータベースに保存

//アドミンバーに独自管理メニューを表示
update_theme_option(OP_ADMIN_TOOL_MENU_VISIBLE);

//ページ公開前に確認アラートを出す
update_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH);

//タイトル等の文字数カウンター表示
update_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE);

///////////////////////////////////////
// 管理者パネル
///////////////////////////////////////
//管理者パネルを表示
update_theme_option(OP_ADMIN_PANEL_VISIBLE);

//管理者パネルのPVを表示
update_theme_option(OP_ADMIN_PANEL_PV_AREA_VISIBLE);

//管理者パネルのPV取得方法
update_theme_option(OP_ADMIN_PANEL_PV_TYPE);

//管理者パネル編集エリアの表示
update_theme_option(OP_ADMIN_PANEL_EDIT_AREA_VISIBLE);

//管理者パネルWordpress編集の表示
update_theme_option(OP_ADMIN_PANEL_WP_EDIT_VISIBLE);

//管理者パネルWindows Live Writer編集の表示
update_theme_option(OP_ADMIN_PANEL_WLW_EDIT_VISIBLE);

//管理者パネルAMPエリアの表示
update_theme_option(OP_ADMIN_PANEL_AMP_AREA_VISIBLE);

//管理者パネルチェックツールエリアの表示
update_theme_option(OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE);