<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー(拡張)
//******************************************************************************
if (!function_exists('hvn_main')):
function hvn_main($wp_customize) {
  $section = 'main';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    array(
      'title'     => '拡張',
      'panel'     => 'hvn_cocoon',
      'priority'  => 2,
    )
  );


  // コントロール
  hvn_panel_label($wp_customize, $section, 'ローディン画面', 1);

  $wp_customize->add_setting('hvn_front_loading_setting', array('default' => 'none'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_front_loading_setting',
      array(
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_front_loading_setting',
        'type'      => 'select',
        'choices'   => hvn_menu_setting('l'),
      )
    )
  );


  hvn_panel_label($wp_customize, $section, '全体', 2);

  $wp_customize->add_setting('front_page_type', array('default' => 'index'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'front_page_type',
      array(
        'label'     => 'フロントページタイプ',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'front_page_type',
        'type'      => 'select',
        'choices'   => array(
          'index'     => '一覧',
          'tab_index' => 'タブ一覧',
          'category'  => 'カテゴリーごと',
          'category_2_columns'  => 'カテゴリーごと(2カラム)',
          'category_3_columns'  => 'カテゴリーごと(3カラム)',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_card_expansion_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_card_expansion_setting',
      array(
        'label'     => '拡張タイプ',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_card_expansion_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_front_none_setting', array('default' => true));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_front_none_setting',
      array(
        'label'       => '「新着記事」タブ表示',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_front_none_setting',
        'type'        => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_categories_card_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_categories_card_setting',
      array(
        'label'     => 'カテゴリーごと(2、3カラム)縦型カード',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_categories_card_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_category_color_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_category_color_setting',
      array(
        'label'       => 'カテゴリーごと背景色',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_category_color_setting',
        'type'        => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('entry_card_type', array('default' => 'entry_card'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'entry_card_type',
      array(
        'label'     => 'カードタイプ',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'entry_card_type',
        'type'      => 'select',
        'choices'   => array(
          'entry_card'      => 'エントリーカード',
          'big_card_first'  => '大きなカード(先頭のみ)',
          'big_card'        => '大きなカード',
          'vertical_card_2' => '縦型カード2列',
          'vertical_card_3' => '縦型カード3列',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_border_radius_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_border_radius_setting',
      array(
        'label'     => 'カード四角',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_border_radius_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_border_setting', array('default' => true));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_border_setting',
      array(
        'label'     => 'カード枠',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_border_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_content_setting', array('default' => true));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_content_setting',
      array(
        'label'     => 'コンテンツ枠',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_content_setting',
        'type'      => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, '目次', 4);

  $wp_customize->add_setting('hvn_toc_style_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_toc_style_setting',
      array(
        'description' => 'スタイル',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_toc_style_setting',
        'type'        => 'select',
        'choices' => array(
          '0'     => 'シンプル',
          '1'     => 'ボックス',
          '2'     => '上下線',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_toc_hidden_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_toc_hidden_setting',
      array(
        'description' => '省略表示',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_toc_hidden_setting',
        'type'        => 'select',
        'choices' => array(
          '0'     => 'なし',
          '1'     => (HVN_COUNT + 1) . '行以上',
          '2'     => '見出しH3',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_toc_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_toc_setting',
      array(
        'label'     => 'スクロール追従ハイライト',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_toc_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_toc_fix_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_toc_fix_setting',
      array(
        'label'     => '目次ボタン',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_toc_fix_setting',
        'type'      => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, 'プロフィール', 6);

  $wp_customize->add_setting('hvn_prof_setting');
  $wp_customize->add_control(
    new WP_Customize_Media_Control(
      $wp_customize,
      'hvn_prof_setting',
      array(
        'description' => '背景画像',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_prof_setting',
        'mime_type'   => 'image',
      )
    )
  );


  $wp_customize->add_setting('hvn_site_date_setting');
  $wp_customize->add_control(
    new WP_Customize_Media_Control(
      $wp_customize,
      'hvn_site_date_setting',
      array(
        'label'     => 'サイト開設日',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_site_date_setting',
        'type'      => 'date',
      )
    )
  );


  $wp_customize->add_setting('hvn_site_date_onoff_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_site_date_onoff_setting',
      array(
        'label'     => 'サイト開設経過日数',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_site_date_onoff_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_profile_btn_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_profile_btn_setting',
      array(
        'label'     => 'プロフィールボタン表示',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_profile_btn_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_profile_follows_setting', array('default' => true));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_profile_follows_setting',
      array(
        'label'     => 'SNSフォロー表示',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_profile_follows_setting',
        'type'      => 'checkbox',
      )
    )
  );
}
endif;
