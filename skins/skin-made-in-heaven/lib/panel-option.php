<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（オプション）
//******************************************************************************
if (!function_exists('hvn_option')):
function hvn_option($wp_customize) {
  $section = 'option';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    [
      'title'     =>  __('オプション', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 4,
    ]
  );


//******************************************************************************
//  オプション
//******************************************************************************
  hvn_panel_label($wp_customize, $section,  __('オプション', THEME_NAME), 1);

  $input_attrs = [
    'input_attrs' => [
      'min'       => 0,
      'max'       => 5,
      'required'  => '',
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_index_new_setting'     , ['default' => 0, 'sanitize_callback' => 'hvn_sanitize_number_range'], '', __('NEWマーク(0～5日、0はオフ)', THEME_NAME), $input_attrs, 'number');
  hvn_panel_control($wp_customize, $section, 'hvn_like_setting'          , ['default' =>false], __('いいねボタン', THEME_NAME),'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_eyecatch_setting'      , ['default' =>false], __('縦アイキャッチ背景ぼかし', THEME_NAME)  ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_swiper_auto_setting'   , ['default' =>false], __('オートプレイ', THEME_NAME)              ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_accordion_setting'     , ['default' =>false], __('アコーディオン化', THEME_NAME)          ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_notice_setting'        , ['default' =>false], __('通知エリア固定', THEME_NAME)            ,'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_notice_scroll_setting' , ['default' =>false], __('通知メッセージ横スクロール', THEME_NAME),'', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_star_setting'          , ['default' =>false], __('評価・ランキングハート', THEME_NAME)    ,'', [], 'checkbox');

  $label_array = [
    'wide'          => '9:6',
    'golden_ratio'  => __('約5:8', THEME_NAME),
    'postcard'      => '2:3',
    'silver_ratio'  => __('約5:7', THEME_NAME),
    'standard'      => '3:4',
    'square'        => '1:1'
  ];
  $thumb = get_theme_mod('thumbnail_image_type', 'wide');
  $label = sprintf(__('サムネイル画像の比率（%s）に従う', THEME_NAME), $label_array[$thumb]);
  hvn_panel_control($wp_customize, $section, 'hvn_thumb_option_setting'  , ['default' => false], $label, '', [], 'checkbox');

  // 隠しオプション
  if (defined('HVN_OPTION') && HVN_OPTION) {
    hvn_panel_control($wp_customize, $section, 'hvn_orderby_option_setting', ['default' => false], __('並び替え選択', THEME_NAME), '', [], 'checkbox');
  }


//******************************************************************************
//  テキスト
//******************************************************************************
  hvn_panel_label($wp_customize, $section,  __('テキスト', THEME_NAME), 2);

  hvn_panel_control($wp_customize, $section, 'hvn_button_more_setting', ['default' => __('もっと見る', THEME_NAME), 'sanitize_callback' => 'hvn_sanitize_text'], '', __('「もっと見る」テキスト', THEME_NAME), ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_button_next_setting', ['default' => __('次のページ', THEME_NAME), 'sanitize_callback' => 'hvn_sanitize_text'], '', __('「次のページ」テキスト', THEME_NAME), ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_breadcrumbs_setting', ['default' => __('ホーム'    , THEME_NAME), 'sanitize_callback' => 'hvn_sanitize_text'], '', __('パンくずリストテキスト', THEME_NAME), ['required'  => ''], 'text');

  // タイトル
  hvn_panel_control($wp_customize, $section, 'hvn_tcheck_option_setting'             , ['default' =>false], __('フロントページタイトル変更', THEME_NAME), '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_title_new_option_setting'          , ['default' => 'New Post'                                  , 'sanitize_callback' => 'hvn_sanitize_text'], __('新着記事', THEME_NAME)       , __('タイトル', THEME_NAME), ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_title_new_sub_option_setting'      , ['default' =>  __('新着・更新された記事です', THEME_NAME) , 'sanitize_callback' => 'hvn_sanitize_text'], ''                               , __('説明文', THEME_NAME)  , ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_title_popular_option_setting'      , ['default' => 'Popular'                                   , 'sanitize_callback' => 'hvn_sanitize_text'], __('人気記事', THEME_NAME)       , __('タイトル', THEME_NAME), ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_title_popular_sub_option_setting'  , ['default' => __('本日読まれている記事です', THEME_NAME)  , 'sanitize_callback' => 'hvn_sanitize_text'], ''                               , __('説明文', THEME_NAME)  , ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_title_category_option_setting'     , ['default' => 'Category'                                  , 'sanitize_callback' => 'hvn_sanitize_text'], __('カテゴリーごと', THEME_NAME) , __('タイトル', THEME_NAME), ['required'  => ''], 'text');
  hvn_panel_control($wp_customize, $section, 'hvn_title_category_sub_option_setting' , ['default' => __('カテゴリーから記事を探す', THEME_NAME)  , 'sanitize_callback' => 'hvn_sanitize_text'], ''                               , __('説明文', THEME_NAME)  , ['required'  => ''], 'text');


//******************************************************************************
//  コメント
//******************************************************************************
  hvn_panel_label($wp_customize, $section,  __('コメント', THEME_NAME), 3);

  hvn_panel_control($wp_customize, $section, 'hvn_comment_setting', ['default' => false], __('コメントアイコン選択・表示', THEME_NAME), '', [], 'checkbox');
  for ($i=1; $i<=3; $i++) {
    $label = null;
    if ($i == 1) {
      $label =  __('画像', THEME_NAME);
    }
    hvn_panel_control($wp_customize, $section, "hvn_comment_img{$i}_setting", [], $label, __('画像', THEME_NAME) . "[{$i}]", [], 'image');
  }
}
endif;
