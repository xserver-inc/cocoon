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
      'priority'  => 4,
    )
  );


  hvn_panel_label($wp_customize, $section, 'オプション', 1);

  $wp_customize->add_setting('hvn_index_new_setting', array(
    'default' => 0,
    'sanitize_callback' => 'hvn_sanitize_number_range',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_index_new_setting',
      array(
        'description' => 'NEWマーク(0～5日、0はオフ)',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_index_new_setting',
        'type'        => 'number',
        'input_attrs' => array(
          'min'       => 0,
          'max'       => 5,
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_like_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_like_setting',
      array(
        'label'     => 'いいねボタン',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_like_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_eyecatch_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_eyecatch_setting',
      array(
        'label'     => '縦アイキャッチ背景ぼかし',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_eyecatch_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_swiper_auto_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_swiper_auto_setting',
      array(
        'label'     => 'オートプレイ',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_swiper_auto_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_accordion_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_accordion_setting',
      array(
        'label'     => 'アコーディオン化',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_accordion_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_notice_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_notice_setting',
      array(
        'label'     => '通知エリア固定',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_notice_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_star_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_star_setting',
      array(
        'label'     => '評価・ランキングハート',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_star_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $label_array = [
    'wide'          => '9:6',
    'golden_ratio'  => '約5:8',
    'postcard'      => '2:3',
    'silver_ratio'  => '約5:7',
    'standard'      => '3:4',
    'square'        => '1:1'
  ];
  $thumb = get_theme_mod('thumbnail_image_type', 'wide');
  $label = $label_array[$thumb];

  $wp_customize->add_setting('hvn_thumb_option_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_thumb_option_setting',
      array(
        'label'     => 'サムネイル画像の比率('. $label . ')に従う',
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_thumb_option_setting',
        'type'      => 'checkbox',
      )
    )
  );


  // 隠しオプション
  if (defined('HVN_OPTION') && HVN_OPTION) {
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
  }


  hvn_panel_label($wp_customize, $section, 'テキスト', 2);

  $wp_customize->add_setting('hvn_button_more_setting', array(
    'default' => 'もっと見る',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_button_more_setting',
      array(
        'description' => '「もっと見る」テキスト',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_button_more_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_button_next_setting', array(
    'default' => '次のページ',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_button_next_setting',
      array(
        'description' => '「次のページ」テキスト',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_button_next_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  $wp_customize->add_setting('hvn_breadcrumbs_setting', array(
    'default' => 'ホーム',
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_breadcrumbs_setting',
      array(
        'description' => 'パンくずリストテキスト',
        'section'     => "hvn_{$section}_section",
        'settings'    => 'hvn_breadcrumbs_setting',
        'type'        => 'text',
        'input_attrs' => array(
          'required'  => '',
        )
      )
    )
  );


  // 隠しオプション
  if (defined('HVN_OPTION') && HVN_OPTION) {
    $wp_customize->add_setting('hvn_tcheck_option_setting', array('default' => false));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'hvn_tcheck_option_setting',
        array(
          'label'     => 'フロントページタイトル変更',
          'section'   => "hvn_{$section}_section",
          'settings'  => 'hvn_tcheck_option_setting',
          'type'      => 'checkbox',
        'input_attrs' => array(
          'required'  => '',
        )

        )
      )
    );


    $wp_customize->add_setting('hvn_title_new_option_setting', array(
      'default' => 'New Post',
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
}
endif;
