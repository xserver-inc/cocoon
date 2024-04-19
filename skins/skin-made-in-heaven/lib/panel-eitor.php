<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー(メインビジュアル)
//******************************************************************************
if (!function_exists('hvn_editor')):
function hvn_editor($wp_customize) {

  // セクション
  $wp_customize->add_section(
    'hvn_editor_section',
    array(
      'title'     => 'エディター',
      'panel'     => 'hvn_cocoon',
      'priority'  => 4,
    )
  );


  // コントロール
  $wp_customize->add_setting('hvn_label1_editor_setting');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label1_editor_setting',
      array(
        'label'       => '■ マーカー',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_label1_editor_setting',
        'type'        => 'hidden',
      )
    )
  );

  // マーカー
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
          'description' => $marker[$i][0],
          'section'     => 'hvn_editor_section',
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
        'label'   => 'ストライプ',
        'section' => 'hvn_editor_section',
        'settings'=> 'hvn_marker_color_set1_setting',
        'type'    => 'checkbox',
      )
    )
  );


  // インラインボタン
  $wp_customize->add_setting('hvn_label2_editor_setting');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label2_editor_setting',
      array(
        'label'       => '■ インラインボタン',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_label2_editor_setting',
        'type'        => 'hidden',
      )
    )
  );


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
          'description' => $inline_button[$i][0],
          'section'     => 'hvn_editor_section',
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
          'label'   => $set[$i],
          'section' => 'hvn_editor_section',
          'settings'=> 'hvn_inline_button_set' . ($i + 1) . '_setting',
          'type'    => 'checkbox',
        )
      )
    );
  }


  // リスト丸数字
  $wp_customize->add_setting('hvn_label3_editor_setting');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label3_editor_setting',
      array(
        'label'       => '■ リスト丸数字',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_label3_editor_setting',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting("hvn_numeric_list_set1_setting", array('default' => '#47585c'));
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      "hvn_numeric_list_set1_setting",
      array(
        'description' => '背景カラー',
        'section'     => 'hvn_editor_section',
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
        'description' => 'スタイル',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_numeric_list_set2_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => '丸',
          '1'   => '四角',
        ),
      )
    )
  );


  // アイコンボックス
  $wp_customize->add_setting('hvn_label4_editor_setting');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label4_editor_setting',
      array(
        'label'       => '■ アイコンボックス',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_label4_editor_setting',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting('hvn_icon_box_set1_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_icon_box_set1_setting',
      array(
        'description' => 'スタイル',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_icon_box_set1_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => '塗りつぶし',
          '1'   => '枠',
          '2'   => '付箋',
        ),
      )
    )
  );

  // タブボックス
  $wp_customize->add_setting('hvn_label5_editor_setting');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label5_editor_setting',
      array(
        'label'       => '■ タブボックス',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_label5_editor_setting',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting('hvn_tab_box_set1_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_tab_box_set1_setting',
      array(
        'description' => 'スタイル',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_tab_box_set1_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => '見出し(標準)',
          '1'   => '見出し(枠上)',
          '2'   => '見出し(枠中)',
        ),
      )
    )
  );


  // FAQ
  $wp_customize->add_setting('hvn_label6_editor_setting');
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_label6_editor_setting',
      array(
        'label'       => '■ FAQ',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_label6_editor_setting',
        'type'        => 'hidden',
      )
    )
  );


  $wp_customize->add_setting('hvn_faq_set1_setting', array('default' => '0'));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_faq_set1_setting',
      array(
        'description' => 'スタイル',
        'section'     => 'hvn_editor_section',
        'settings'    => 'hvn_faq_set1_setting',
        'type'        => 'radio',
        'choices'     => array(
          '0'   => '標準',
          '1'   => '角型ラベル',
          '2'   => '丸型ラベル',
        ),
      )
    )
  );
}
endif;
