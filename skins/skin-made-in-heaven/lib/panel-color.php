<?php
if(!defined('ABSPATH'))exit;


//******************************************************************************
//  カスタマイザー（基本カラー）
//******************************************************************************
if (!function_exists('hvn_color')):
function hvn_color($wp_customize) {
  $section = 'decoration';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    [
      'title'     => __('基本カラー', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 1,
    ]
  );


//******************************************************************************
//  基本カラー
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('基本カラー', THEME_NAME), 1);

  hvn_panel_control($wp_customize, $section, 'hvn_main_color_setting', ['default' => HVN_MAIN_COLOR, 'sanitize_callback' => 'hvn_sanitize_color'], '', __('サイトカラー'  , THEME_NAME), [], 'color');
  hvn_panel_control($wp_customize, $section, 'hvn_body_color_setting', ['default' => HVN_BODY_COLOR, 'sanitize_callback' => 'hvn_sanitize_color'], '', __('背景カラー'    , THEME_NAME), [], 'color');
  hvn_panel_control($wp_customize, $section, 'hvn_text_color_setting', ['default' => HVN_TEXT_COLOR, 'sanitize_callback' => 'hvn_sanitize_color'], '', __('テキストカラー', THEME_NAME), [], 'color');


//******************************************************************************
//  個別カラー
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('個別カラー', THEME_NAME), 2);

  hvn_panel_control($wp_customize, $section, 'header_background_color'      , ['sanitize_callback' => 'hvn_sanitize_color'], '', __('ヘッダー背景カラー'           , THEME_NAME), [], 'color');
  hvn_panel_control($wp_customize, $section, 'header_text_color'            , ['sanitize_callback' => 'hvn_sanitize_color'], '', __('ヘッダーテキストカラー'       , THEME_NAME), [], 'color');
  hvn_panel_control($wp_customize, $section, 'global_navi_background_color' , ['sanitize_callback' => 'hvn_sanitize_color'], '', __('グローバルナビ背景カラー'     , THEME_NAME), [], 'color');
  hvn_panel_control($wp_customize, $section, 'global_navi_text_color'       , ['sanitize_callback' => 'hvn_sanitize_color'], '', __('グローバルナビテキストカラー' , THEME_NAME), [], 'color');
  hvn_panel_control($wp_customize, $section, 'footer_background_color'      , ['sanitize_callback' => 'hvn_sanitize_color'], '', __('フッター背景カラー'           , THEME_NAME), [], 'color');


//******************************************************************************
//  グローバルナビメニューデザイン
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('グローバルナビメニューデザイン', THEME_NAME), 3);

  $input_attrs = [
    'choices' => [
        '0' => __('背景明るく'        , THEME_NAME),
        '1' => __('ライン（中央から）', THEME_NAME),
        '2' => __('ライン（左から）'  , THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_navi_setting', ['default' => '0'], '', __('hoverエフェクト', THEME_NAME), $input_attrs, 'select');


//******************************************************************************
//  見出しデザイン
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('見出しデザイン', THEME_NAME), 4);

  hvn_panel_control($wp_customize, $section, 'hvn_h2_css_setting',      ['default' => '0'], '', 'H2', hvn_menu_setting('h2'), 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_h3_css_setting',      ['default' => '0'], '', 'H3', hvn_menu_setting('h3'), 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_h4_css_setting',      ['default' => '0'], '', 'H4', hvn_menu_setting('h4'), 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_widget_css_setting',  ['default' => '0'], '', __('ウィジェット', THEME_NAME), hvn_menu_setting('w'), 'select');

  $input_attrs = [
    'input_attrs' => [
      'required'  => '',
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_mobile_text_setting', ['default' => __('メニュー', THEME_NAME), 'sanitize_callback' => 'hvn_sanitize_text'], '', __('スライドインメニュータイトル', THEME_NAME), $input_attrs, 'text');
}
endif;
