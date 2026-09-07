<?php
/**
 * Cocoon設定のタブ定義と拡張用フック。
 *
 * @package Cocoon
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 標準タブの表示名とフォームを一か所で管理する（保存処理は別に管理する）。
if ( !function_exists( 'cocoon_get_default_settings_tabs' ) ):
function cocoon_get_default_settings_tabs() {
  $tabs = array(
    'skin' => array('label' => __('スキン', THEME_NAME), 'forms' => array('skin-forms.php')),
    'all' => array('label' => __('全体', THEME_NAME), 'forms' => array('all-forms.php')),
    'theme-header' => array('label' => __('ヘッダー', THEME_NAME), 'forms' => array('header-forms.php')),
    'ads' => array('label' => __('広告', THEME_NAME), 'forms' => array('ads-forms.php')),
    'title' => array('label' => __('タイトル', THEME_NAME), 'forms' => array('title-forms.php')),
    'seo' => array('label' => __('SEO', THEME_NAME), 'forms' => array('seo-forms.php')),
    'ogp' => array('label' => __('OGP', THEME_NAME), 'forms' => array('ogp-forms.php')),
    'analytics' => array('label' => __('アクセス解析・認証', THEME_NAME), 'forms' => array('analytics-forms.php')),
    'column' => array('label' => __('カラム', THEME_NAME), 'forms' => array('column-forms.php')),
    'index-page' => array('label' => __('インデックス', THEME_NAME), 'forms' => array('index-forms.php')),
    'single-page' => array('label' => __('投稿', THEME_NAME), 'forms' => array('single-forms.php')),
    'page-page' => array('label' => __('固定ページ', THEME_NAME), 'forms' => array('page-forms.php')),
    'content-page' => array('label' => __('本文', THEME_NAME), 'forms' => array('content-forms.php')),
    'toc-page' => array('label' => __('目次', THEME_NAME), 'forms' => array('toc-forms.php')),
    'sns-share' => array('label' => __('SNSシェア', THEME_NAME), 'forms' => array('sns-share-forms.php')),
    'sns-follow' => array('label' => __('SNSフォロー', THEME_NAME), 'forms' => array('sns-follow-forms.php')),
    'image' => array('label' => __('画像', THEME_NAME), 'forms' => array('image-forms.php')),
    'blog-card' => array('label' => __('ブログカード', THEME_NAME), 'forms' => array('blogcard-in-forms.php', 'blogcard-out-forms.php')),
    'code-highlight' => array('label' => __('コード', THEME_NAME), 'forms' => array('code-forms.php')),
    'comment' => array('label' => __('コメント', THEME_NAME), 'forms' => array('comment-forms.php')),
    'notice-area' => array('label' => __('通知', THEME_NAME), 'forms' => array('notice-forms.php')),
    'appeal-area' => array('label' => __('アピールエリア', THEME_NAME), 'forms' => array('appeal-forms.php')),
    'recommended' => array('label' => __('おすすめカード', THEME_NAME), 'forms' => array('recommended-forms.php')),
    'carousel' => array('label' => __('カルーセル', THEME_NAME), 'forms' => array('carousel-forms.php')),
    'footer' => array('label' => __('フッター', THEME_NAME), 'forms' => array('footer-forms.php')),
    'buttons' => array('label' => __('ボタン', THEME_NAME), 'forms' => array('buttons-forms.php')),
    'mobile-buttons' => array('label' => __('モバイル', THEME_NAME), 'forms' => array('mobile-buttons-forms.php')),
    'page-404' => array('label' => __('404ページ', THEME_NAME), 'forms' => array('404-forms.php')),
    'amp' => array('label' => __('AMP', THEME_NAME), 'forms' => array('amp-forms.php')),
    'pwa' => array('label' => __('PWA', THEME_NAME), 'forms' => array('pwa-forms.php')),
    'admin' => array('label' => __('管理者画面', THEME_NAME), 'forms' => array('admin-forms.php')),
    'widget' => array('label' => __('ウィジェット', THEME_NAME), 'forms' => array('widget-forms.php')),
    'widget-area' => array('label' => __('ウィジェットエリア', THEME_NAME), 'forms' => array('widget-area-forms.php')),
    'editor' => array('label' => __('エディター', THEME_NAME), 'forms' => array('editor-forms.php')),
    'apis' => array('label' => __('API', THEME_NAME), 'forms' => array('apis-forms.php')),
    'others' => array('label' => __('その他', THEME_NAME), 'forms' => array('others-forms.php')),
    'reset' => array('label' => __('リセット', THEME_NAME), 'forms' => array('reset-forms.php')),
    'about' => array('label' => __('テーマ情報', THEME_NAME), 'forms' => array('about-forms.php')),
  );

  // 非表示の機能も、既存の保存処理や関連ファイルの管理は従来どおり実行する。
  if (!is_amp_enable()) unset($tabs['amp']);
  if (!is_pwa_enable()) unset($tabs['pwa']);

  return $tabs;
}
endif;

// HTMLのID、CSSのセレクター、フック名にそのまま使える識別子だけを許可する。
if ( !function_exists( 'cocoon_is_settings_tab_id' ) ):
function cocoon_is_settings_tab_id($tab_id) {
  return is_string($tab_id) && preg_match('/^[a-z][a-z0-9_-]*$/D', $tab_id) === 1;
}
endif;

// 拡張側には表示名だけを公開し、標準フォームの読み込み先は本体で管理する。
if ( !function_exists( 'cocoon_get_settings_tabs' ) ):
function cocoon_get_settings_tabs() {
  global $_THEME_OPTIONS;
  // 表示時と保存時で判定が変わらないよう、入力欄と同じくスキンの固定値を外す。
  $skin_options = $_THEME_OPTIONS;
  $_THEME_OPTIONS = array();
  try {
    $defaults = cocoon_get_default_settings_tabs();
    $definitions = array();
    foreach ($defaults as $tab_id => $tab) {
      $definitions[$tab_id] = array('label' => $tab['label']);
    }

    /**
     * タブの表示名・順序の変更と独自タブの追加。
     *
     * 値は array('label' => '表示名')。内容と保存処理はタブ別アクションで追加する。
     * 標準タブを省略しても、入力欄と保存値を守るため末尾に補完する。
     *
     * @param array $definitions タブIDをキーにした表示定義。
     */
    $definitions = apply_filters('cocoon_settings_tabs', $definitions);
    if (!is_array($definitions)) return $defaults;

    $tabs = array();
    foreach ($definitions as $tab_id => $tab) {
      if (!cocoon_is_settings_tab_id($tab_id) || !is_array($tab) ||
          !isset($tab['label']) || !is_string($tab['label']) || trim($tab['label']) === '') {
        continue;
      }

      // 無効になっている標準機能のIDを独自タブとして再利用させない。
      if (in_array($tab_id, array('amp', 'pwa'), true) && !isset($defaults[$tab_id])) continue;

      $tabs[$tab_id] = array(
        'label' => $tab['label'],
        'forms' => isset($defaults[$tab_id]) ? $defaults[$tab_id]['forms'] : array(),
      );
    }

    // 省略や不正な定義で標準の入力欄が消え、未送信値が保存されることを防ぐ。
    return $tabs + $defaults;
  } finally {
    // フィルターで例外が起きた場合も、後続処理へスキンの状態を戻す。
    $_THEME_OPTIONS = $skin_options;
  }
}
endif;

// フォーム内の変数がタブIDなどを上書きしないよう、別の変数範囲で読み込む。
if ( !function_exists( 'cocoon_require_settings_tab_form' ) ):
function cocoon_require_settings_tab_form($cocoon_form_file) {
  require_once __DIR__ . '/' . $cocoon_form_file;
}
endif;

// 標準フォームの後ろに、子テーマやプラグインの入力欄を追加できるようにする。
if ( !function_exists( 'cocoon_render_settings_tab_content' ) ):
function cocoon_render_settings_tab_content($tab_id, $tab) {
  foreach ($tab['forms'] as $form_file) {
    cocoon_require_settings_tab_form($form_file);
  }
  do_action("cocoon_settings_tab_content_{$tab_id}");
}
endif;

// 独自タブにも同じ切り替え規則を生成し、JavaScriptなしでも内容を表示する。
if ( !function_exists( 'cocoon_get_settings_tabs_css' ) ):
function cocoon_get_settings_tabs_css($tabs) {
  $selectors = array();
  foreach ($tabs as $tab_id => $tab) {
    if (cocoon_is_settings_tab_id($tab_id)) {
      $selectors[] = "#tabs > #tab-{$tab_id}-input:checked ~ #tab-{$tab_id}-content";
    }
  }
  return $selectors ? implode(",\n", $selectors) . " { display: block; }\n" : '';
}
endif;

// 送信された選択値は、現在存在するタブのIDと一致する場合だけ復元する。
if ( !function_exists( 'cocoon_get_selected_settings_tab' ) ):
function cocoon_get_selected_settings_tab($tabs) {
  if (isset($_POST['tab-input']) && is_string($_POST['tab-input'])) {
    foreach ($tabs as $tab_id => $tab) {
      if ($_POST['tab-input'] === "tab-{$tab_id}-input") return $tab_id;
    }
  }
  return array_key_first($tabs);
}
endif;

// 管理者権限と、この設定画面からの送信を確認できた場合だけ保存を許可する。
if ( !function_exists( 'cocoon_is_settings_post_valid' ) ):
function cocoon_is_settings_post_valid() {
  return current_user_can('manage_options') &&
    isset($_POST[HIDDEN_FIELD_NAME]) && is_string($_POST[HIDDEN_FIELD_NAME]) &&
    (bool) wp_verify_nonce(wp_unslash($_POST[HIDDEN_FIELD_NAME]), 'settings');
}
endif;

// 標準設定の保存後、スキン設定の再読込とキャッシュ生成より前に拡張設定を保存する。
if ( !function_exists( 'cocoon_save_settings_tab_extensions' ) ):
function cocoon_save_settings_tab_extensions($tabs) {
  if (!cocoon_is_settings_post_valid()) return;

  foreach ($tabs as $tab_id => $tab) {
    do_action("cocoon_settings_save_{$tab_id}");
  }
}
endif;
