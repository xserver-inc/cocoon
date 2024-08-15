<?php
if(!defined('ABSPATH'))exit;


//******************************************************************************
//  カスタマイザー(基本カラー)
//******************************************************************************
if (!function_exists('hvn_color')):
function hvn_color($wp_customize) {
  $section = 'decoration';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    array(
      'title'     => '基本カラー',
      'panel'     => 'hvn_cocoon',
      'priority'  => 1,
    )
  );


  // コントロール
  hvn_panel_label($wp_customize, $section, '基本カラー', 1);

  $wp_customize->add_setting('hvn_main_color_setting', array(
    'default' => HVN_MAIN_COLOR,
    'sanitize_callback' => 'hvn_sanitize_color',
  ));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'hvn_main_color_setting',
      array(
        'description' => 'サイトカラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_main_color_setting',
      )
    )
  );


  $wp_customize->add_setting('hvn_body_color_setting', array(
    'default' => HVN_BODY_COLOR,
    'sanitize_callback' => 'hvn_sanitize_color',
  ));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'hvn_body_color_setting',
      array(
        'description' => '背景カラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_body_color_setting',
      )
    )
  );


  $wp_customize->add_setting('hvn_text_color_setting', array(
    'default' => HVN_TEXT_COLOR,
    'sanitize_callback' => 'hvn_sanitize_color',
  ));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'hvn_text_color_setting',
      array(
        'description' => 'テキストカラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_text_color_setting',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, '個別カラー', 2);

  $wp_customize->add_setting('header_background_color');
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'header_background_color',
      array(
        'description' => 'ヘッダー背景カラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'header_background_color',
      )
    )
  );


  $wp_customize->add_setting('header_text_color');
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'header_text_color',
      array(
        'description' => 'ヘッダーテキストカラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'header_text_color',
      )
    )
  );


  $wp_customize->add_setting('global_navi_background_color');
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'global_navi_background_color',
      array(
        'description' => 'グローバルナビ背景カラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'global_navi_background_color',
      )
    )
  );


  $wp_customize->add_setting('global_navi_text_color');
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'global_navi_text_color',
      array(
        'description' => 'グローバルナビテキストカラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'global_navi_text_color',
      )
    )
  );


  $wp_customize->add_setting('footer_background_color');
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'footer_background_color',
      array(
        'description' => 'フッター背景カラー',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'footer_background_color',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, 'グローバルナビメニューデザイン', 3);

  $wp_customize->add_setting('hvn_navi_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_navi_setting',
      array(
        'description' => 'hoverエフェクト',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_navi_setting',
        'type'        => 'select',
        'choices'     => array(
          '0' => '背景明るく',
          '1' => 'ライン(中央から)',
          '2' => 'ライン(左から)',
        )
      )
    )
  );


  hvn_panel_label($wp_customize, $section, '見出しデザイン', 4);

  $wp_customize->add_setting('hvn_h2_css_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_h2_css_setting',
      array(
        'description' => 'H2',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_h2_css_setting',
        'type'        => 'select',
        'choices'     => hvn_menu_setting('h2'),
      )
    )
  );


  $wp_customize->add_setting('hvn_h3_css_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_h3_css_setting',
      array(
        'description' => 'H3',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_h3_css_setting',
        'type'        => 'select',
        'choices'     => hvn_menu_setting('h3'),
      )
    )
  );


  $wp_customize->add_setting('hvn_h4_css_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_h4_css_setting',
      array(
        'description' => 'H4',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_h4_css_setting',
        'type'        => 'select',
        'choices'     => hvn_menu_setting('h4'),
      )
    )
  );


  $wp_customize->add_setting('hvn_widget_css_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_widget_css_setting',
      array(
        'description' => 'ウィジェット',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_widget_css_setting',
        'type'        => 'select',
        'choices'     => hvn_menu_setting('w'),
      )
    )
  );


  // モバイルメニュータイトル
  $wp_customize->add_setting('hvn_mobile_text_setting', array(
    'default' => 'メニュー',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_mobile_text_setting',
      array(
        'description' => 'スライドインメニュータイトル',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_mobile_text_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );
}
endif;
