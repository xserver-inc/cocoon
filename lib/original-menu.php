<?php //オリジナルテーマ設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// Cocoon固有のメニュー定義
if ( !function_exists( 'get_cocoon_original_menu_definitions' ) ):
function get_cocoon_original_menu_definitions() {
  $definitions = [
    'theme-settings'      => ['title' => __('Cocoon 設定', THEME_NAME)           , 'callback' => 'add_theme_settings_page', 'icon' => '/images/admin-menu-logo.png'],
    'speech-balloon'      => ['title' => __('吹き出し', THEME_NAME)             , 'callback' => 'add_theme_speech_balloon_page'],
    'theme-func-text'     => ['title' => __('テンプレート', THEME_NAME)         , 'callback' => 'add_theme_func_text_page'],
    'theme-affiliate-tag' => ['title' => __('アフィリエイトタグ', THEME_NAME)   , 'callback' => 'add_theme_affiliate_tag_page'],
    'theme-ranking'       => ['title' => __('ランキング', THEME_NAME)           , 'callback' => 'add_theme_item_ranking_page'],
    'theme-access'        => ['title' => __('アクセス集計', THEME_NAME)         , 'callback' => 'add_theme_access_page'],
    'theme-speed-up'      => ['title' => __('高速化', THEME_NAME)               , 'callback' => 'add_theme_speed_up_page'],
    'theme-backup'        => ['title' => __('バックアップ', THEME_NAME)         , 'callback' => 'add_theme_backup_page'],
    'theme-cache'         => ['title' => __('キャッシュ削除', THEME_NAME)       , 'callback' => 'add_theme_cache_page'],
  ];
  return apply_filters('cocoon_menu_definitions', $definitions);
}
endif;

//WordPress管理画面にオリジナルメニューを追加する
add_action('admin_menu', 'add_original_menu_in_admin_page');
if ( !function_exists( 'add_original_menu_in_admin_page' ) ):
function add_original_menu_in_admin_page() {
  $definitions = get_cocoon_original_menu_definitions();

  //セパレーターの挿入
  add_admin_menu_separator(apply_filters('cocoon_add_theme_settings_page_separator_position', 29));

  foreach ($definitions as $slug => $info) {
    $capability = isset($info['capability']) ? $info['capability'] : 'manage_options';

    if ($slug === THEME_SETTINGS_PAGE) {
      // 親メニュー（Cocoon 設定）
      add_menu_page(
        isset($info['title']) ? $info['title'] : '',
        isset($info['title']) ? $info['title'] : '',
        $capability,
        THEME_SETTINGS_PAGE,
        isset($info['callback']) ? $info['callback'] : '',
        isset($info['icon']) ? get_cocoon_template_directory_uri() . $info['icon'] : '',
        apply_filters('cocoon_add_theme_settings_page_position', 29)
      );
    } else {
      // 子メニュー
      add_submenu_page(
        THEME_SETTINGS_PAGE,
        isset($info['title']) ? $info['title'] : '',
        isset($info['title']) ? $info['title'] : '',
        $capability,
        $slug,
        isset($info['callback']) ? $info['callback'] : ''
      );
    }
  }
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
