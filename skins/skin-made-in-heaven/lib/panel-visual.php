<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（メインビジュアル）
//******************************************************************************
if (!function_exists('hvn_header')):
function hvn_header($wp_customize) {
  $section = 'header';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    [
      'title'     => __('メインビジュアル', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 3,
    ]
  );


//******************************************************************************
//  ヘッダーロゴ
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('ヘッダーロゴ', THEME_NAME), 1);

  hvn_panel_control($wp_customize, $section, 'the_site_logo_url'        , [], '', __('画像', THEME_NAME), [], 'i_image');
  hvn_panel_control($wp_customize, $section, 'hvn_header_logo_setting'  , ['default' => false], __('メインビジュアル表示', THEME_NAME), '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_header_option_setting', ['default' => true ], __('ヘッダー表示', THEME_NAME)        , '', [], 'checkbox');


//******************************************************************************
//  メインビジュアル
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('メインビジュアル', THEME_NAME), 2);

  $input_attrs = [
    'choices' => [
      'none'  => __('なし', THEME_NAME),
      'video' => __('動画', THEME_NAME),
      'image' => __('画像', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_header_setting'        , ['default' => 'none'], '', '', $input_attrs, 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_header_message_setting', [], '', __('タイトルテキスト', THEME_NAME), [], 'text');

  $input_attrs = [
    'input_attrs' => [
      'min'       => 10,
      'max'       => 50,
      'required'  => '',
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_appea_font_size_setting', ['default' => 40, 'sanitize_callback' => 'hvn_sanitize_number_range'], '', __('フォントサイズ', THEME_NAME) . '(10～50px)', $input_attrs, 'number');
  hvn_panel_control($wp_customize, $section, 'hvn_header_vertival_setting', ['default' => false] , __('テキスト縦書き', THEME_NAME), '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_header_scroll_setting'  , ['default' => '0'], '', __('Scrollボタン', THEME_NAME), hvn_menu_setting('s'), 'select');
  hvn_panel_control($wp_customize, $section, 'hvn_header_wave_setting'    , ['default' => false] , __('波線', THEME_NAME), '', [], 'checkbox');
  hvn_panel_control($wp_customize, $section, 'hvn_header_video_setting'   , [], __('動画', THEME_NAME), '', [], 'video');

  for ($i=1; $i<=3; $i++) {
    $label = null;
    if ($i == 1) {
      $label = __('画像', THEME_NAME);
    }
    hvn_panel_control($wp_customize, $section, 'hvn_header_img' . $i . '_setting', [], $label, __('画像', THEME_NAME) . "[{$i}]", [], 'image');
  }


//******************************************************************************
//  スライドオプション
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('スライドオプション', THEME_NAME), 3);

  $input_attrs = [
    'choices' => [
      'fade'      => __('フェード', THEME_NAME),
      'horizontal'=> __('横', THEME_NAME),
      'vertical'  => __('縦', THEME_NAME),
      'h-split'   => __('横分割', THEME_NAME),
      'v-split'   => __('縦分割', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_header_fade_setting', ['default' => 'fade'], '', __('スライド切り替え', THEME_NAME), $input_attrs, 'radio');

  $input_attrs = [
    'choices' => [
      '0' => __('なし', THEME_NAME),
      '1' => __('イン', THEME_NAME),
      '2' => __('アウト', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_header_animation_setting', ['default' => 0], '', __('スライド中のズーム', THEME_NAME), $input_attrs, 'radio');


//******************************************************************************
//  フィルター
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('フィルター', THEME_NAME), 4);

  $input_attrs = [
    'choices' => [
      '0' => __('なし',     THEME_NAME),
      '1' => __('ドット黒', THEME_NAME),
      '2' => __('ドット白', THEME_NAME),
      '3' => __('走査線横', THEME_NAME),
      '4' => __('モノクロ', THEME_NAME),
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_header_filter_setting', ['default' => '0'], '', '', $input_attrs, 'select');


//******************************************************************************
//  オーバーレイ
//******************************************************************************
  hvn_panel_label($wp_customize, $section, __('オーバーレイ', THEME_NAME), 5);

  hvn_panel_control($wp_customize, $section, 'hvn_header_color_setting', ['sanitize_callback' => 'hvn_sanitize_color'], '', __('メインビジュアルに被せるカラーレイヤー', THEME_NAME), [], 'color');

  $input_attrs = [
    'input_attrs' => [
      'step'      => 10,
      'min'       => 0,
      'max'       => 50,
      'required'  => '',
    ]
  ];
  hvn_panel_control($wp_customize, $section, 'hvn_header_opacity_setting', ['default' => 0, 'sanitize_callback' => 'hvn_sanitize_number_range'], '', __('不透明度(opacity値0～50%)', THEME_NAME), $input_attrs, 'number');
}
endif;
