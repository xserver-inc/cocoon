<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（拡張）
//******************************************************************************
if (!function_exists('hvn_main')):
function hvn_main($wp_customize) {
  $section = 'main';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    [
      'title'     => __('拡張', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 2,
    ]
  );


//******************************************************************************
//  ローディン画面
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('ローディン画面', THEME_NAME), 1);

  hvn_panel_control($wp_customize, $section, 'hvn_front_loading_setting', ['default' => 'none'], '', '', hvn_menu_setting('l'), 'select');


//******************************************************************************
//  全体
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('全体', THEME_NAME), 2);

  $input_attrs = [
    'choices' => [
      'index'              => __('一覧', THEME_NAME) . __('（デフォルト）', THEME_NAME),
      'tab_index'          => __('タブ一覧', THEME_NAME),
      'category'           => __('カテゴリーごと', THEME_NAME),
      'category_2_columns' => __('カテゴリーごと', THEME_NAME) . __('（2カラム）', THEME_NAME),
      'category_3_columns' => __('カテゴリーごと', THEME_NAME) . __('（3カラム）', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'front_page_type'            , ['default' => 'index'], __('フロントページタイプ', THEME_NAME), '', $input_attrs, 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_card_expansion_setting' , ['default' => false]  , __('拡張タイプ', THEME_NAME)                            , '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_front_none_setting'     , ['default' => true ]  , __('「新着記事」タブ表示', THEME_NAME)                  , '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_categories_card_setting', ['default' => false]  , __('カテゴリーごと（2、3カラム）縦型カード', THEME_NAME), '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_category_color_setting' , ['default' => false]  , __('カテゴリーごと背景色', THEME_NAME)                  , '', [], 'checkbox');

  $input_attrs = [
    'choices' => [
      'entry_card'      => __('エントリーカード（デフォルト）', THEME_NAME),
      'big_card_first'  => __('大きなカード（先頭のみ）', THEME_NAME),
      'big_card'        => __('大きなカード', THEME_NAME),
      'vertical_card_2' => __('縦型カード2列', THEME_NAME),
      'vertical_card_3' => __('縦型カード3列', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'entry_card_type' , ['default' => 'entry_card'],  __('カードタイプ', THEME_NAME) , '', $input_attrs, 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_border_radius_setting', ['default' => false], __('カード四角', THEME_NAME)  , '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_border_setting'       , ['default' => true ], __('カード枠', THEME_NAME)    , '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_content_setting'      , ['default' => true ], __('コンテンツ枠', THEME_NAME), '', [], 'checkbox');


//******************************************************************************
//  目次
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('目次', THEME_NAME), 4);

  $input_attrs =[
   'choices' => [
      '0' => __('シンプル', THEME_NAME),
      '1' => __('ボックス', THEME_NAME),
      '2' => __('上下線'  , THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_toc_style_setting', ['default' => '0'], '', __('スタイル', THEME_NAME), $input_attrs, 'select');

  $input_attrs = [
    'choices' => [
      '0' => __('なし', THEME_NAME),
      '1' => (HVN_COUNT + 1) . __('行以上', THEME_NAME),
      '2' => __('見出しH3', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_toc_hidden_setting' , ['default' => '0'], '', __('省略表示', THEME_NAME), $input_attrs, 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_toc_setting'        , ['default' =>false], __('スクロール追従ハイライト', THEME_NAME) ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_toc_fix_setting'    , ['default' =>false], __('目次ボタン', THEME_NAME)               ,'', [], 'checkbox');


//******************************************************************************
//  プロフィール
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('プロフィール', THEME_NAME), 6);

  hvn_panel_control($wp_customize, $section, 'hvn_prof_setting'            , [], '', __('背景画像', THEME_NAME), [], 'image');
  hvn_panel_control($wp_customize, $section, 'hvn_site_date_setting'       , [], __('サイト開設日', THEME_NAME), '', [], 'date');
  hvn_panel_control($wp_customize, $section, 'hvn_site_date_onoff_setting' , ['default' => false], __('サイト開設経過日数', THEME_NAME)     ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_profile_btn_setting'     , ['default' => false], __('プロフィールボタン表示', THEME_NAME) ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_profile_follows_setting' , ['default' => true ], __('SNSフォロー表示', THEME_NAME)        ,'', [], 'checkbox');
}
endif;
