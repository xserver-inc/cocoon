<?php //オリジナルテーマ設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//WordPress管理画面にオリジナルメニューを追加する
add_action('admin_menu', 'add_original_menu_in_admin_page');
if ( !function_exists( 'add_original_menu_in_admin_page' ) ):
function add_original_menu_in_admin_page() {
  //_v('admin_menu10');
  //_v($GLOBALS['menu']);

  //セパレーターの挿入
  add_admin_menu_separator(apply_filters('cocoon_add_theme_settings_page_separator_position', 28));
  //トップレベルメニューを追加する
  add_menu_page(SETTING_NAME_TOP, SETTING_NAME_TOP, 'manage_options', THEME_SETTINGS_PAFE, 'add_theme_settings_page', get_template_directory_uri().'/images/admin-menu-logo.png', apply_filters('cocoon_add_theme_settings_page_position', 29) );


  //add_menu_page();
  //var_dump('aaaaaaaa');

  //吹き出しサブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('吹き出し', THEME_NAME), __('吹き出し', THEME_NAME), 'manage_options', 'speech-balloon', 'add_theme_speech_balloon_page');

  //テンプレートサブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('テンプレート', THEME_NAME), __('テンプレート', THEME_NAME), 'manage_options', 'theme-func-text', 'add_theme_func_text_page');

  //アフィリエイトタグサブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('アフィリエイトタグ', THEME_NAME), __('アフィリエイトタグ', THEME_NAME), 'manage_options', 'theme-affiliate-tag', 'add_theme_affiliate_tag_page');

  //ランキング作成サブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('ランキング作成', THEME_NAME), __('ランキング作成', THEME_NAME), 'manage_options', 'theme-ranking', 'add_theme_item_ranking_page');

  //アクセス集計サブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('アクセス集計', THEME_NAME), __('アクセス集計', THEME_NAME), 'manage_options', 'theme-access', 'add_theme_access_page');

  //高速化サブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('高速化', THEME_NAME), __('高速化', THEME_NAME), 'manage_options', 'theme-speed-up', 'add_theme_speed_up_page');

  //バックアップサブメニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('バックアップ', THEME_NAME), __('バックアップ', THEME_NAME), 'manage_options', 'theme-backup', 'add_theme_backup_page');

  //キャッシュ削除メニューを追加
  add_submenu_page(THEME_SETTINGS_PAFE, __('キャッシュ削除', THEME_NAME), __('キャッシュ削除', THEME_NAME), 'manage_options', 'theme-cache', 'add_theme_cache_page');
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

  require_once abspath(__FILE__).'page-settings/_top-page.php';
}
endif;

//テーマ用バックアップ設定項目の表示
if ( !function_exists( 'add_theme_backup_page' ) ):
function add_theme_backup_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-backup/_top-page.php';
}
endif;

//吹き出し設定
if ( !function_exists( 'add_theme_speech_balloon_page' ) ):
function add_theme_speech_balloon_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-speech-balloon/_top-page.php';
}
endif;

//テーマ用テンプレートの登録
if ( !function_exists( 'add_theme_func_text_page' ) ):
function add_theme_func_text_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-func-text/_top-page.php';
}
endif;

//アフィリエイトタグの登録
if ( !function_exists( 'add_theme_affiliate_tag_page' ) ):
function add_theme_affiliate_tag_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-affiliate-tag/_top-page.php';
}
endif;

//アフィリエイトタグの登録
if ( !function_exists( 'add_theme_item_ranking_page' ) ):
function add_theme_item_ranking_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-item-ranking/_top-page.php';
}
endif;

//アクセス集計テンプレートの登録
if ( !function_exists( 'add_theme_access_page' ) ):
function add_theme_access_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-access/_top-page.php';
}
endif;

//テーマ用高速化設定項目の表示
if ( !function_exists( 'add_theme_speed_up_page' ) ):
function add_theme_speed_up_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-speed-up/_top-page.php';
}
endif;

//キャッシュ削除設定項目の表示
if ( !function_exists( 'add_theme_cache_page' ) ):
function add_theme_cache_page() {
  // ユーザーが必要な権限を持つか確認する必要がある
  if (!current_user_can('manage_options'))  {
    wp_die( __('このページにアクセスする管理者権限がありません。') );
  }
  //以下のテンプレートファイルで設定ページを作成する

  require_once abspath(__FILE__).'page-cache/_top-page.php';
}
endif;
