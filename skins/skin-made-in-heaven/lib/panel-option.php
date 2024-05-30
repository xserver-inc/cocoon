<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー(隠し)
//******************************************************************************
if (!function_exists('hvn_option')):
function hvn_option($wp_customize) {
  $section = 'option';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    array(
      'title'     => 'オプション',
      'panel'     => 'hvn_cocoon',
      'priority'  => 5,
    )
  );


  // コントロール
  hvn_panel_label($wp_customize, $section, '全体', 1);

  $wp_customize->add_setting('hvn_margin_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_margin_option_setting',
      array(
        'label'     => '余白を変更',
        'section'   => "hvn_{$section}_section",
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
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_orderby_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, 'フロントページ', 2);

  $wp_customize->add_setting('hvn_header_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_header_option_setting',
      array(
        'label'     => 'ヘッダーロゴ非表示',
        'section'   => "hvn_{$section}_section",
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
        'section'   => "hvn_{$section}_section",
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
        'section'     => "hvn_{$section}_section",
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
        'section'     => "hvn_{$section}_section",
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
        'section'     => "hvn_{$section}_section",
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
        'section'     => "hvn_{$section}_section",
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
        'section'     => "hvn_{$section}_section",
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
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_title_category_sub_option_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  hvn_panel_label($wp_customize, $section, 'コメント', 3);

  $wp_customize->add_setting('hvn_comment_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_comment_setting',
      array(
        'label'     => 'コメントアイコン選択・表示',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_comment_setting',
        'type'      => 'checkbox',
      )
    )
  );


  for ($i=1; $i<=3; $i++) {
    $label = null;
    if ($i == 1) {
      $label = '画像';
    }
    $wp_customize->add_setting('hvn_comment_img' . $i . '_setting');
    $wp_customize->add_control(
      new WP_Customize_Media_Control(
        $wp_customize,
        'hvn_comment_img' . $i . '_setting',
        array(
          'label'       => $label,
          'description' => '画像[' . $i . ']',
          'section'     => "hvn_{$section}_section",
          'settings'    => 'hvn_comment_img'. $i . '_setting',
          'mime_type'   => 'image',
        )
      )
    );
  }
}
endif;
