<?php //オリジナルテーマ設定

//Wordpress管理画面にオリジナルメニューを追加する
add_action('admin_menu', 'add_original_menu_in_admin_page');
if ( !function_exists( 'add_original_menu_in_admin_page' ) ):
function add_original_menu_in_admin_page() {
    //トップレベルメニューを追加する
    add_menu_page(SETTING_NAME_TOP, SETTING_NAME_TOP, 'manage_options', 'theme-settings', 'add_theme_settings_page' );
    //var_dump('aaaaaaaa');

    // カスタムトップレベルメニューにサブメニューを追加
    add_submenu_page('theme-settings', __('バックアップ', THEME_NAME), __('バックアップ', THEME_NAME), 'manage_options', 'theme-backup', 'add_theme_backup_page');

    // // 新しいトップレベルメニューを追加 (あれだけ言ったのに...)
    // add_menu_page(__('Test Toplevel','menu-test'), __('Test Toplevel','menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );

    // // カスタムトップレベルメニューにサブメニューを追加
    // add_submenu_page('mt-top-level-handle', __('Test Sublevel','menu-test'), __('Test Sublevel','menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');


    // // 新しいトップレベルメニューを追加 (あれだけ言ったのに...)
    // add_menu_page(__('Test Toplevel','menu-test'), __('Test Toplevel','menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );

    // // カスタムトップレベルメニューにサブメニューを追加
    // add_submenu_page('mt-top-level-handle', __('Test Sublevel','menu-test'), __('Test Sublevel','menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');

    // // カスタムトップレベルメニューに2番目のサブメニューを追加
    // add_submenu_page('mt-top-level-handle', __('Test Sublevel 2','menu-test'), __('Test Sublevel 2','menu-test'), 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}
endif;


//テーマ用のセッティングページメニューのページコンテンツを表示
if ( !function_exists( 'add_theme_settings_page' ) ):
function add_theme_settings_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する
  require_once 'page-settings/_top-page.php';
}
endif;

//テーマ用のセッティングページメニューのページコンテンツを表示
if ( !function_exists( 'add_theme_backup_page' ) ):
function add_theme_backup_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する
  require_once 'page-settings/_top-page.php';
}
endif;