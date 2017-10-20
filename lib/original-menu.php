<?php //オリジナルテーマ設定

//Wordpress管理画面にオリジナルメニューを追加する
add_action('admin_menu', 'add_original_menu_in_admin_page');
if ( !function_exists( 'add_original_menu_in_admin_page' ) ):
function add_original_menu_in_admin_page() {
    //トップレベルメニューを追加する
    add_menu_page(SETTING_NAME_TOP, SETTING_NAME_TOP, 'manage_options', 'theme-settings', 'add_theme_settings_page' );
    //var_dump('aaaaaaaa');

    //吹き出しサブメニューを追加
    add_submenu_page('theme-settings', __('吹き出し', THEME_NAME), __('吹き出し', THEME_NAME), 'manage_options', 'theme-balloon', 'add_theme_balloon_page');

    //使いまわしテキストサブメニューを追加
    add_submenu_page('theme-settings', __('使いまわしテキスト', THEME_NAME), __('使いまわしテキスト', THEME_NAME), 'manage_options', 'theme-text-func', 'add_theme_text_func_page');

    //アクセス分析サブメニューを追加
    add_submenu_page('theme-settings', __('アクセス分析', THEME_NAME), __('アクセス分析', THEME_NAME), 'manage_options', 'theme-analytics', 'add_theme_analytics_page');

    //ソーシャルカウントサブメニューを追加
    add_submenu_page('theme-settings', __('ソーシャルカウント', THEME_NAME), __('ソーシャルカウント', THEME_NAME), 'manage_options', 'theme-sns-count', 'add_theme_sns_count_page');

    //ランキング作成サブメニューを追加
    add_submenu_page('theme-settings', __('ランキング作成', THEME_NAME), __('ランキング作成', THEME_NAME), 'manage_options', 'theme-ranking', 'add_theme_ranking_page');

    //CTAサブメニューを追加
    add_submenu_page('theme-settings', __('CTA', THEME_NAME), __('CTA', THEME_NAME), 'manage_options', 'theme-cta', 'add_theme_cta_page');

    //高速化サブメニューを追加
    add_submenu_page('theme-settings', __('高速化', THEME_NAME), __('高速化', THEME_NAME), 'manage_options', 'theme-speed-up', 'add_theme_speed_up_page');

    //バックアップサブメニューを追加
    add_submenu_page('theme-settings', __('バックアップ', THEME_NAME), __('バックアップ', THEME_NAME), 'manage_options', 'theme-backup', 'add_theme_backup_page');

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
  require_once 'page-backup/_top-page.php';
}
endif;