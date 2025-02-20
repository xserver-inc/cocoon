<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（メインビジュアル）
//******************************************************************************
if (!function_exists('hvn_editor')):
function hvn_editor($wp_customize) {
  $section = 'editor';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    array(
      'title'     => __('エディター', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 5,
    )
  );


  // コントロール
  hvn_panel_label($wp_customize, $section, __('文字', THEME_NAME), 1);

  $text = [
    ['赤色', '#e60033'],
    ['青色', '#0095d9'],
    ['緑色', '#3eb370'],
  ];
  for ($i=0; $i<count($text); $i++) {
    $wp_customize->add_setting("hvn_rich_text_color{$i}_setting", array('default' => $text[$i][1]));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        "hvn_rich_text_color{$i}_setting",
        array(
          'description' => __($text[$i][0], THEME_NAME),
          'section'     => "hvn_{$section}_section",
          'settings'    => "hvn_rich_text_color{$i}_setting",
        )
      )
    );
  }


  hvn_panel_label($wp_customize, $section, __('マーカー', THEME_NAME), 2);

  $marker = [
    ['黄色', '#ffff99'],
    ['赤色', '#ffd0d1'],
    ['青色', '#a8dafb'],
  ];
  for ($i=0; $i<count($marker); $i++) {
    $wp_customize->add_setting("hvn_marker_color{$i}_setting", array('default' => $marker[$i][1]));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        "hvn_marker_color{$i}_setting",
        array(
          'description' => __($marker[$i][0], THEME_NAME),
          'section'     => "hvn_{$section}_section",
          'settings'    => "hvn_marker_color{$i}_setting",
        )
      )
    );
  }


  $wp_customize->add_setting('hvn_marker_color_set1_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_marker_color_set1_setting',
      array(
        'label'   => __('ストライプ', THEME_NAME),
        'section' => "hvn_{$section}_section",
        'settings'=> 'hvn_marker_color_set1_setting',
        'type'    => 'checkbox',
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('バッジ', THEME_NAME), 3);

  $badge = [
    ['オレンジ' , '#f39800'],
    ['赤色'     , '#e60033'],
    ['ピンク'   , '#e95295'],
    ['紫色'     , '#884898'],
    ['青色'     , '#0095d9'],
    ['緑色'     , '#3eb370'],
    ['黄色'     , '#ffd900'],
    ['茶色'     , '#954e2a'],
    ['灰色'     , '#949495']
  ];
  for ($i=0; $i<count($badge); $i++) {
    $wp_customize->add_setting("hvn_badge_color{$i}_setting", array('default' => $badge[$i][1]));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        "hvn_badge_color{$i}_setting",
        array(
          'description' => __($badge[$i][0], THEME_NAME),
          'section'     => "hvn_{$section}_section",
          'settings'    => "hvn_badge_color{$i}_setting",
        )
      )
    );
  }


  hvn_panel_label($wp_customize, $section, __('インラインボタン', THEME_NAME), 4);

  $inline_button = [
    ['黒色', '#333333'],
    ['赤色', '#e60033'],
    ['青色', '#0095d9'],
    ['緑色', '#007b43']
  ];
  for ($i=0; $i<count($inline_button); $i++) {
    $wp_customize->add_setting("hvn_inline_button_color{$i}_setting", array('default' => $inline_button[$i][1]));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        "hvn_inline_button_color{$i}_setting",
        array(
          'description' => __($inline_button[$i][0], THEME_NAME),
          'section'     => "hvn_{$section}_section",
          'settings'    => "hvn_inline_button_color{$i}_setting",
        )
      )
    );
  }


  $set = ['円形にする', '光らせる', '立体にする'];
  for ($i=0; $i<count($set); $i++) {
    $wp_customize->add_setting('hvn_inline_button_set' . ($i + 1) . '_setting', array('default' => false));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'hvn_inline_button_set' . ($i + 1) . '_setting',
        array(
          'label'   => __($set[$i], THEME_NAME),
          'section' => "hvn_{$section}_section",
          'settings'=> 'hvn_inline_button_set' . ($i + 1) . '_setting',
          'type'    => 'checkbox',
        )
      )
    );
  }


  hvn_panel_label($wp_customize, $section, __('リスト丸数字', THEME_NAME), 5);

  $wp_customize->add_setting("hvn_numeric_list_set1_setting", array('default' => '#47585c'));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      "hvn_numeric_list_set1_setting",
      array(
        'description' => __('背景カラー', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => "hvn_numeric_list_set1_setting",
      )
    )
  );


  $wp_customize->add_setting('hvn_numeric_list_set2_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_numeric_list_set2_setting',
      array(
        'description' => __('スタイル', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_numeric_list_set2_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => __('丸',   THEME_NAME),
          '1'   => __('四角', THEME_NAME),
        ),
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('アイコンボックス', THEME_NAME), 6);

  $wp_customize->add_setting('hvn_icon_box_set1_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_icon_box_set1_setting',
      array(
        'description' => __('スタイル', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_icon_box_set1_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => __('塗りつぶし', THEME_NAME),
          '1'   => __('枠',         THEME_NAME),
          '2'   => __('付箋',       THEME_NAME),
        ),
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('タブボックス', THEME_NAME), 7);

  $wp_customize->add_setting('hvn_tab_box_set1_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_tab_box_set1_setting',
      array(
        'description' => __('スタイル', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_tab_box_set1_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => __('見出し(標準)', THEME_NAME),
          '1'   => __('見出し(枠上)', THEME_NAME),
          '2'   => __('見出し(枠中)', THEME_NAME),
        ),
      )
    )
  );


  hvn_panel_label($wp_customize, $section, __('FAQ', THEME_NAME), 8);

  $wp_customize->add_setting('hvn_faq_set1_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_faq_set1_setting',
      array(
        'description' => __('スタイル', THEME_NAME),
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_faq_set1_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => __('標準',       THEME_NAME),
          '1'   => __('角型ラベル', THEME_NAME),
          '2'   => __('丸型ラベル', THEME_NAME),
        ),
      )
    )
  );
}
endif;
