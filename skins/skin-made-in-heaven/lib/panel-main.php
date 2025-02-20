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
    array(
      'title'     => __('拡張', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 2,
    )
  );


  // コントロール
  hvn_panel_label($wp_customize, $section, __('ローディン画面', THEME_NAME), 1);

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


  hvn_panel_label($wp_customize, $section, __('全体', THEME_NAME), 2);

  $wp_customize->add_setting('front_page_type', array('default' => 'index'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'front_page_type',
      array(
        'label'     => __('フロントページタイプ', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'front_page_type',
        'type'      => 'select',
        'choices'   => array(
          'index'     => __('一覧', THEME_NAME).__('（デフォルト）', THEME_NAME),
          'tab_index' => __('タブ一覧', THEME_NAME),
          'category'  => __('カテゴリーごと', THEME_NAME),
          'category_2_columns'  => __( 'カテゴリーごと', THEME_NAME) . __('（2カラム）', THEME_NAME),
          'category_3_columns'  => __( 'カテゴリーごと', THEME_NAME) . __('（3カラム）', THEME_NAME),
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
        'label'     => __('拡張タイプ', THEME_NAME),
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
        'label'       => __('「新着記事」タブ表示', THEME_NAME),
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
        'label'     => __('カテゴリーごと（2、3カラム）縦型カード', THEME_NAME),
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
        'label'       => __('カテゴリーごと背景色', THEME_NAME),
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
        'label'     => __('カードタイプ', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'entry_card_type',
        'type'      => 'select',
        'choices'   => array(
          'entry_card'      => __('エントリーカード（デフォルト）', THEME_NAME),
          'big_card_first'  => __('大きなカード（先頭のみ）', THEME_NAME),
          'big_card'        => __('大きなカード', THEME_NAME),
          'vertical_card_2' => __('縦型カード2列', THEME_NAME),
          'vertical_card_3' => __('縦型カード3列', THEME_NAME),
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
        'label'     => __('カード四角', THEME_NAME),
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
        'label'     => __('カード枠', THEME_NAME),
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
        'label'     => __('コンテンツ枠', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_content_setting',
        'type'      => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('目次', THEME_NAME), 4);

  $wp_customize->add_setting('hvn_toc_style_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_toc_style_setting',
      array(
        'description' => __('スタイル', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_toc_style_setting',
        'type'        => 'select',
        'choices' => array(
          '0'     => __('シンプル', THEME_NAME),
          '1'     => __('ボックス', THEME_NAME),
          '2'     => __('上下線'  , THEME_NAME),
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
        'description' => __('省略表示', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_toc_hidden_setting',
        'type'        => 'select',
        'choices' => array(
          '0'     => __('なし', THEME_NAME),
          '1'     => (HVN_COUNT + 1) . __('行以上', THEME_NAME),
          '2'     => __('見出しH3', THEME_NAME),
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
        'label'     => __('スクロール追従ハイライト', THEME_NAME),
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
        'label'     => __('目次ボタン', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_toc_fix_setting',
        'type'      => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('プロフィール', THEME_NAME), 6);

  $wp_customize->add_setting('hvn_prof_setting');
  $wp_customize->add_control(
    new WP_Customize_Media_Control(
      $wp_customize,
      'hvn_prof_setting',
      array(
        'description' => __('背景画像', THEME_NAME),
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
        'label'     => __('サイト開設日', THEME_NAME),
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
        'label'     => __('サイト開設経過日数', THEME_NAME),
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
        'label'     => __('プロフィールボタン表示', THEME_NAME),
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
        'label'     => __('SNSフォロー表示', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_profile_follows_setting',
        'type'      => 'checkbox',
      )
    )
  );
}
endif;
