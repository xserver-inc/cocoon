<?php //管理画面設定をデータベースに保存

//アドミンバーに独自管理メニューを表示
update_theme_option(OP_ADMIN_TOOL_MENU_VISIBLE);

//ページ公開前に確認アラートを出す
update_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH);

//タイトル等の文字数カウンター表示
update_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE);