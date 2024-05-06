<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー(隠し)
//******************************************************************************
if (!function_exists('hvn_option')):
function hvn_option($wp_customize) {

  // セクション
  $wp_customize->add_section(
    'hvn_option_section',
    array(
      'title'     => 'オプション',
      'panel'     => 'hvn_cocoon',
      'priority'  => 5,
    )
  );


  // コントロール
  $wp_customize->add_setting('hvn_label1_option_section');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label1_option_section',
      array(
        'label'       => '■ 全体',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_label1_option_section',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting('hvn_margin_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_margin_option_setting',
      array(
        'label'     => '余白を変更',
        'section'   => 'hvn_option_section',
        'settings'  => 'hvn_margin_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_orderby_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_orderby_option_setting',
      array(
        'label'     => '並び替え選択',
        'section'   => 'hvn_option_section',
        'settings'  => 'hvn_orderby_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_label2_option_section');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label2_option_section',
      array(
        'label'       => '■ メインビジュアル',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_label2_option_section',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting('hvn_scroll_color_option_setting', array(
    'default' => '#ffffff',
    'sanitize_callback' => 'hvn_sanitize_color',
  ));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'hvn_scroll_color_option_setting',
      array(
        'description' => 'テキストカラー',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_scroll_color_option_setting',
      )
    )
  );


  $wp_customize->add_setting('hvn_wave_color_category_setting', array(
    'default' => get_theme_mod('hvn_main_color_setting', HVN_MAIN_COLOR),
    'sanitize_callback' => 'hvn_sanitize_color',
  ));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'hvn_wave_color_category_setting',
      array(
        'description' => 'カテゴリー波線カラー',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_wave_color_category_setting',
      )
    )
  );


  $wp_customize->add_setting('hvn_main_wave_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_main_wave_option_setting',
      array(
        'label'     => 'メインビジュアル波線非表示',
        'section'   => 'hvn_option_section',
        'settings'  => 'hvn_main_wave_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_category_wave_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_category_wave_option_setting',
      array(
        'label'     => 'カテゴリーごと波線非表示',
        'section'   => 'hvn_option_section',
        'settings'  => 'hvn_category_wave_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_label3_option_section');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label3_option_section',
      array(
        'label'       => '■ フロントページ',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_label3_option_section',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting('hvn_header_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_option_setting',
      array(
        'label'     => 'ヘッダーロゴ非表示',
        'section'   => 'hvn_option_section',
        'settings'  => 'hvn_header_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_tcheck_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_tcheck_option_setting',
      array(
        'label'     => 'タイトル変更',
        'section'   => 'hvn_option_section',
        'settings'  => 'hvn_tcheck_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_title_new_option_setting', array(
    'default' => 'NewPost',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_title_new_option_setting',
      array(
        'label'       => '新着記事',
        'description' => 'タイトル',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_title_new_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_title_new_sub_option_setting', array(
    'default' => '新着・更新された記事です',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_title_new_sub_option_setting',
      array(
        'description' => '説明文',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_title_new_sub_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_title_popular_option_setting', array(
    'default' => 'Popular',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_title_popular_option_setting',
      array(
        'label'       => '人気記事',
        'description' => 'タイトル',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_title_popular_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_title_popular_sub_option_setting', array(
    'default' => '本日読まれている記事です',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_title_popular_sub_option_setting',
      array(
        'description' => '説明文',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_title_popular_sub_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_title_category_option_setting', array(
    'default' => 'Category',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_title_category_option_setting',
      array(
        'label'       => 'カテゴリーごと',
        'description' => 'タイトル',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_title_category_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_title_category_sub_option_setting', array(
    'default' => 'カテゴリーから記事を探す',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_title_category_sub_option_setting',
      array(
        'description' => '説明文',
        'section'     => 'hvn_option_section',
        'settings'    => 'hvn_title_category_sub_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );
}
endif;
