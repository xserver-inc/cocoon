<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（エディター）
//******************************************************************************
if (!function_exists('hvn_editor')):
function hvn_editor($wp_customize) {
  $section = 'editor';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    [
      'title'     => __('エディター', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 5,
    ]
  );


//******************************************************************************
//  文字
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('文字', THEME_NAME));

  $text = [
    ['赤色', '#e60033'],
    ['青色', '#0095d9'],
    ['緑色', '#3eb370'],
  ];
  foreach ($text as $i => $data) {
    hvn_panel_control($wp_customize, $section, "hvn_rich_text_color{$i}_setting", ['default' => $data[1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($data[0], THEME_NAME), [], 'color');
  }


//******************************************************************************
//  マーカー
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('マーカー', THEME_NAME));

  $marker = [
    ['黄色', '#ffff99'],
    ['赤色', '#ffd0d1'],
    ['青色', '#a8dafb'],
  ];
  foreach ($marker as $i => $data) {
    hvn_panel_control($wp_customize, $section, "hvn_marker_color{$i}_setting", ['default' => $data[1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($data[0], THEME_NAME), [], 'color');
  }

  hvn_panel_control($wp_customize, $section, 'hvn_marker_color_set1_setting', ['default' => false], __('ストライプ', THEME_NAME), '', [], 'checkbox');


//******************************************************************************
//  バッジ
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('バッジ', THEME_NAME));

  $badge = [
    ['オレンジ' , '#f39800'],
    ['赤色'     , '#e60033'],
    ['ピンク'   , '#e95295'],
    ['紫色'     , '#884898'],
    ['青色'     , '#0095d9'],
    ['緑色'     , '#3eb370'],
    ['黄色'     , '#ffd900'],
    ['茶色'     , '#954e2a'],
    ['灰色'     , '#949495']
  ];
  foreach ($badge as $i => $data) {
    hvn_panel_control($wp_customize, $section, "hvn_badge_color{$i}_setting", ['default' => $data[1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($data[0], THEME_NAME), [], 'color');
  }


//******************************************************************************
//  インラインボタン
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('インラインボタン', THEME_NAME));

  $inline_button = [
    ['黒色', '#333333'],
    ['赤色', '#e60033'],
    ['青色', '#0095d9'],
    ['緑色', '#007b43']
  ];
  foreach ($inline_button as $i => $data) {
    hvn_panel_control($wp_customize, $section, "hvn_inline_button_color{$i}_setting", ['default' => $data[1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($data[0], THEME_NAME), [], 'color');
  }

  $set = [
    __('円形にする' , THEME_NAME),
    __('光らせる'   , THEME_NAME),
    __('立体にする' , THEME_NAME)
  ];
  foreach ($set as $i => $label) {
    hvn_panel_control($wp_customize, $section, 'hvn_inline_button_set' . ($i + 1) . '_setting', ['default' => false], $label, '', [], 'checkbox');
  }


//******************************************************************************
//  リスト丸数字
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('リスト丸数字', THEME_NAME));

  hvn_panel_control($wp_customize, $section, 'hvn_numeric_list_set1_setting', ['default' => '#47585c', 'sanitize_callback' => 'hvn_sanitize_color'], '', __('背景カラー', THEME_NAME), [], 'color');

  $input_attrs = [
    'choices' => [
      '0' => __('丸'  , THEME_NAME),
      '1' => __('四角', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_numeric_list_set2_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'select');


//******************************************************************************
//  アイコンボックス
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('アイコンボックス', THEME_NAME));

  $input_attrs = [
    'choices' => [
      '0' => __('塗りつぶし', THEME_NAME),
      '1' => __('枠'        , THEME_NAME),
      '2' => __('付箋'      , THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_icon_box_set1_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'radio');


//******************************************************************************
//  タブ見出しボックス
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('タブ見出しボックス', THEME_NAME));

  $input_attrs = [
    'choices' => [
      '0' => __('見出し(標準)', THEME_NAME),
      '1' => __('見出し(枠上)', THEME_NAME),
      '2' => __('見出し(枠中)', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_tab_box_set1_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'radio');


//******************************************************************************
//  FAQ
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('FAQ', THEME_NAME));

  $input_attrs = [
    'choices' => [
      '0' => __('標準'      , THEME_NAME),
      '1' => __('角型ラベル', THEME_NAME),
      '2' => __('丸型ラベル', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_faq_set1_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'radio');


//******************************************************************************
//  アイキャッチ自動生成
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('タイトルからアイキャッチを生成する', THEME_NAME));

  $thumb = [
    [__('背景カラー'    , THEME_NAME), '#ffffff'],
    [__('テキストカラー', THEME_NAME), '#333333'],
    [__('ボーダーカラー', THEME_NAME), '#a2d7dd'],
  ];
  foreach ($thumb as $i => $data) {
    hvn_panel_control($wp_customize, $section, "hvn_thumb_color{$i}_setting", ['default' => $data[1], 'sanitize_callback' => 'hvn_sanitize_color'], '', $data[0], [], 'color');
  }
}
endif;
