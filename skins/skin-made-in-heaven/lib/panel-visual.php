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
    array(
      'title'     => __('メインビジュアル', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 3,
    )
  );


  // コントロール
  hvn_panel_label($wp_customize, $section, __('ヘッダーロゴ', THEME_NAME), 1);

  $wp_customize->add_setting('the_site_logo_url');
  $wp_customize->add_control(
    new WP_Customize_Image_Control(
      $wp_customize,
      'the_site_logo_url',
      array(
        'description' => __('画像', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'the_site_logo_url',
        'mime_type'   => 'image',
      )
    )
  );


  $wp_customize->add_setting('hvn_header_logo_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_logo_setting',
      array(
        'label'   => __('メインビジュアル表示', THEME_NAME),
        'section' => "hvn_{$section}_section",
        'settings'=> 'hvn_header_logo_setting',
        'type'    => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_header_option_setting', array('default' => true));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_option_setting',
      array(
        'label'     => __('ヘッダー表示', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_header_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('メインビジュアル', THEME_NAME), 2);

  $wp_customize->add_setting('hvn_header_setting', array('default' => 'none'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_setting',
      array(
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_header_setting',
        'type'      => 'select',
        'choices'   => array(
          'none'    => __('なし', THEME_NAME),
          'video'   => __('動画', THEME_NAME),
          'image'   => __('画像', THEME_NAME),
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_header_message_setting', array(
    'default' => '',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_message_setting',
      array(
        'description' => __('タイトルテキスト', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_header_message_setting',
        'type'        => 'text',
      )
    )
  );


  $wp_customize->add_setting('hvn_appea_font_size_setting', array(
    'default' => 40,
    'sanitize_callback' => 'hvn_sanitize_number_range',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_appea_font_size_setting',
      array(
        'description' => __('フォントサイズ', THEME_NAME) . '(10～50px)',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_appea_font_size_setting',
        'type'        => 'number',
        'input_attrs' => array(
          'min'       => 10,
          'max'       => 50,
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_header_vertival_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_vertival_setting',
      array(
        'label'   => __('テキスト縦書き', THEME_NAME),
        'section' => "hvn_{$section}_section",
        'settings'=> 'hvn_header_vertival_setting',
        'type'    => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_header_scroll_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_scroll_setting',
      array(
        'description' => __('Scrollボタン', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_header_scroll_setting',
        'type'        => 'select',
        'choices'     => array(
          '0'         => __('なし', THEME_NAME),
          '1'         => __('線'  , THEME_NAME),
          '2'         => __('矢印', THEME_NAME),
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_header_wave_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_wave_setting',
      array(
        'label'   => __('波線', THEME_NAME),
        'section' => "hvn_{$section}_section",
        'settings'=> 'hvn_header_wave_setting',
        'type'    => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_header_video_setting');
  $wp_customize->add_control(
    new WP_Customize_Media_Control(
      $wp_customize,
      'hvn_header_video_setting',
      array(
        'label'     => __('動画', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_header_video_setting',
        'mime_type' => 'video',
      )
    )
  );


  for ($i=1; $i<=3; $i++) {
    $label = null;
    if ($i == 1) {
      $label = __('画像', THEME_NAME);
    }
    $wp_customize->add_setting('hvn_header_img' . $i . '_setting');
    $wp_customize->add_control(
      new WP_Customize_Media_Control(
        $wp_customize,
        'hvn_header_img' . $i . '_setting',
        array(
          'label'       => $label,
          'description' => __('画像', THEME_NAME) . "[{$i}]",
          'section'     => "hvn_{$section}_section",
          'settings'    => 'hvn_header_img'. $i . '_setting',
          'mime_type'   => 'image',
        )
      )
    );
  }


  hvn_panel_label($wp_customize, $section, __('スライドオプション', THEME_NAME), 3);

  $wp_customize->add_setting('hvn_header_fade_setting', array('default' => 'fade'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_fade_setting',
      array(
        'description' => __('スライド切り替え', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_header_fade_setting',
        'type'        => 'radio',
        'choices'     => array(
          'fade'      => __('フェード', THEME_NAME),
          'horizontal'=> __('横', THEME_NAME),
          'vertical'  => __('縦', THEME_NAME),
          'h-split'   => __('横分割', THEME_NAME),
          'v-split'   => __('縦分割', THEME_NAME),
        ),
      )
    )
  );


  $wp_customize->add_setting('hvn_header_animation_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_animation_setting',
      array(
        'description' => __('スライド中のズーム', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_header_animation_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0' => __('なし', THEME_NAME),
          '1' => __('イン', THEME_NAME),
          '2' => __('アウト', THEME_NAME),
        )
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('フィルター', THEME_NAME), 4);

  $wp_customize->add_setting('hvn_header_filter_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_filter_setting',
      array(
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_header_filter_setting',
        'type'      => 'select',
        'choices'   => array(
          '0' => __('なし',     THEME_NAME),
          '1' => __('ドット黒', THEME_NAME),
          '2' => __('ドット白', THEME_NAME),
          '3' => __('走査線横', THEME_NAME),
          '4' => __('モノクロ', THEME_NAME),
        )
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('オーバーレイ', THEME_NAME), 5);

  $wp_customize->add_setting('hvn_header_color_setting');
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'hvn_header_color_setting',
      array(
        'description' => __('メインビジュアルに被せるカラーレイヤー', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_header_color_setting',
      )
    )
  );


  $wp_customize->add_setting('hvn_header_opacity_setting', array(
    'default' => 0,
    'sanitize_callback' => 'hvn_sanitize_number_range',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_opacity_setting',
      array(
        'description' => __('不透明度(opacity値0～50%)', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_header_opacity_setting',
        'type'        => 'number',
        'input_attrs' => array(
          'step'      => 10,
          'min'       => 0,
          'max'       => 50,
          'required'  => '',
        )
      )
    )
  );
}
endif;
