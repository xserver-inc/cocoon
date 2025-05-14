<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（メインビジュアル）
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
  hvn_panel_label($wp_customize, $section, __('文字', THEME_NAME), 1);

  $text = [
    ['赤色', '#e60033'],
    ['青色', '#0095d9'],
    ['緑色', '#3eb370'],
  ];
  for ($i=0; $i<count($text); $i++) {
    hvn_panel_control($wp_customize, $section, "hvn_rich_text_color{$i}_setting", ['default' => $text[$i][1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($text[$i][0], THEME_NAME), [], 'color');
  }


//******************************************************************************
//  マーカー
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('マーカー', THEME_NAME), 2);

  $marker = [
    ['黄色', '#ffff99'],
    ['赤色', '#ffd0d1'],
    ['青色', '#a8dafb'],
  ];
  for ($i=0; $i<count($text); $i++) {
    hvn_panel_control($wp_customize, $section, "hvn_marker_color{$i}_setting", ['default' => $marker[$i][1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($marker[$i][0], THEME_NAME), [], 'color');
  }

  hvn_panel_control($wp_customize, $section, 'hvn_marker_color_set1_setting', ['default' => false], __('ストライプ', THEME_NAME),'', [], 'checkbox');


//******************************************************************************
//  バッジ
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('バッジ', THEME_NAME), 3);

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
  for ($i=0; $i<count($badge); $i++) {
    hvn_panel_control($wp_customize, $section, "hvn_badge_color{$i}_setting", ['default' => $badge[$i][1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($badge[$i][0], THEME_NAME), [], 'color');
  }


//******************************************************************************
//  インラインボタン
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('インラインボタン', THEME_NAME), 4);

  $inline_button = [
    ['黒色', '#333333'],
    ['赤色', '#e60033'],
    ['青色', '#0095d9'],
    ['緑色', '#007b43']
  ];
  for ($i=0; $i<count($inline_button); $i++) {
    hvn_panel_control($wp_customize, $section, "hvn_inline_button_color{$i}_setting", ['default' => $inline_button[$i][1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($inline_button[$i][0], THEME_NAME), [], 'color');
  }

  $set = [__('円形にする', THEME_NAME), __('光らせる', THEME_NAME), __('立体にする', THEME_NAME)];
  for ($i=0; $i<count($set); $i++) {
    hvn_panel_control($wp_customize, $section, 'hvn_inline_button_set' . ($i + 1) . '_setting', ['default' => false], $set[$i], '', [], 'checkbox');
  }


//******************************************************************************
//  リスト丸数字
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('リスト丸数字', THEME_NAME), 5);

  hvn_panel_control($wp_customize, $section, 'hvn_numeric_list_set1_setting', ['default' => '#47585c', 'sanitize_callback' => 'hvn_sanitize_color'], '',  __('背景カラー', THEME_NAME), [], 'color');

  $input_attrs =[
    'choices' => [
      '0'   => __('丸',   THEME_NAME),
      '1'   => __('四角', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_numeric_list_set2_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'select');


//******************************************************************************
//  アイコンボックス
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('アイコンボックス', THEME_NAME), 6);

  $input_attrs = [
    'choices' => [
      '0' => __('塗りつぶし', THEME_NAME),
      '1' => __('枠',         THEME_NAME),
      '2' => __('付箋',       THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section,'hvn_icon_box_set1_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'radio');


//******************************************************************************
//  タブボックス
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('タブボックス', THEME_NAME), 7);

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
  hvn_panel_label($wp_customize, $section, __('FAQ', THEME_NAME), 8);

  $input_attrs = [
    'choices' => [
      '0' => __('標準',       THEME_NAME),
      '1' => __('角型ラベル', THEME_NAME),
      '2' => __('丸型ラベル', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_faq_set1_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'radio');


//******************************************************************************
//  アイキャッチ自動生成
//******************************************************************************
  hvn_panel_label($wp_customize, $section,  __('タイトルからアイキャッチを生成する', THEME_NAME), 9);

  $thumb = [
    [__('背景カラー'    , THEME_NAME), '#ffffff'],
    [__('テキストカラー', THEME_NAME), '#333333'],
    [__('ボーダーカラー', THEME_NAME), '#a2d7dd'],
  ];
  for ($i=0; $i<count($thumb); $i++) {
    hvn_panel_control($wp_customize, $section, "hvn_thumb_color{$i}_setting", ['default' => $thumb[$i][1], 'sanitize_callback' => 'hvn_sanitize_color'], '', __($thumb[$i][0], THEME_NAME), [], 'color');
  }
}
endif;
