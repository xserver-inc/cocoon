<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー（オプション）
//******************************************************************************
if (!function_exists('hvn_option')):
function hvn_option($wp_customize) {
  $section = 'option';

  // セクション
  $wp_customize->add_section(
    "hvn_{$section}_section",
    array(
      'title'     =>  __('オプション', THEME_NAME),
      'panel'     => 'hvn_cocoon',
      'priority'  => 4,
    )
  );


  hvn_panel_label($wp_customize, $section,  __('オプション', THEME_NAME), 1);

  $wp_customize->add_setting('hvn_index_new_setting', array(
    'default' => 0,
    'sanitize_callback' => 'hvn_sanitize_number_range',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_index_new_setting',
      array(
        'description' =>  __('NEWマーク(0～5日、0はオフ)', THEME_NAME),
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
        'label'     =>  __('いいねボタン', THEME_NAME),
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
        'label'     =>  __('縦アイキャッチ背景ぼかし', THEME_NAME),
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
        'label'     =>  __('オートプレイ', THEME_NAME),
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
        'label'     =>  __('アコーディオン化', THEME_NAME),
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
        'label'     =>  __('通知エリア固定', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_notice_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $wp_customize->add_setting('hvn_notice_scroll_setting', array('default' => false));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_notice_scroll_setting',
      array(
        'label'     =>  __('通知メッセージ横スクロール', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_notice_scroll_setting',
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
        'label'     =>  __('評価・ランキングハート', THEME_NAME),
        'section'   => "hvn_{$section}_section",
        'settings'  => 'hvn_star_setting',
        'type'      => 'checkbox',
      )
    )
  );


  $label_array = [
    'wide'          => '9:6',
    'golden_ratio'  => __('約5:8', THEME_NAME),
    'postcard'      => '2:3',
    'silver_ratio'  => __('約5:7', THEME_NAME),
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
        'label'     =>  __('サムネイル画像の比率（'. $label . '）に従う', THEME_NAME),
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
          'label'     =>  __('並び替え選択', THEME_NAME),
          'section'   => "hvn_{$section}_section",
          'settings'  => 'hvn_orderby_option_setting',
          'type'      => 'checkbox',
        )
      )
    );
  }


  hvn_panel_label($wp_customize, $section,  __('テキスト', THEME_NAME), 2);

  $wp_customize->add_setting('hvn_button_more_setting', array(
    'default' =>  __('もっと見る', THEME_NAME),
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_button_more_setting',
      array(
        'description' =>  __('「もっと見る」テキスト', THEME_NAME),
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
    'default' =>  __('次のページ', THEME_NAME),
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_button_next_setting',
      array(
        'description' =>  __('「次のページ」テキスト', THEME_NAME),
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
    'default' =>  __('ホーム', THEME_NAME),
    'sanitize_callback' => 'hvn_sanitize_text',
  ));
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'hvn_breadcrumbs_setting',
      array(
        'description' =>  __('パンくずリストテキスト', THEME_NAME),
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
          'label'     =>  __('フロントページタイトル変更', THEME_NAME),
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
          'label'       =>  __('新着記事', THEME_NAME),
          'description' =>  __('タイトル', THEME_NAME),
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
      'default' =>  __('新着・更新された記事です', THEME_NAME),
      'sanitize_callback' => 'hvn_sanitize_text',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'hvn_title_new_sub_option_setting',
        array(
          'description' =>  __('説明文', THEME_NAME),
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
          'label'       =>  __('人気記事', THEME_NAME),
          'description' =>  __('タイトル', THEME_NAME),
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
      'default' =>  __('本日読まれている記事です', THEME_NAME),
      'sanitize_callback' => 'hvn_sanitize_text',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'hvn_title_popular_sub_option_setting',
        array(
          'description' =>  __('説明文', THEME_NAME),
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
          'label'       =>  __('カテゴリーごと', THEME_NAME),
          'description' =>  __('タイトル', THEME_NAME),
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
      'default' =>  __('カテゴリーから記事を探す', THEME_NAME),
      'sanitize_callback' => 'hvn_sanitize_text',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'hvn_title_category_sub_option_setting',
        array(
          'description' =>  __('説明文', THEME_NAME),
          'section'     => "hvn_{$section}_section",
          'settings'    => 'hvn_title_category_sub_option_setting',
          'type'        => 'text',
          'input_attrs' => array(
            'required'  => '',
          )
        )
      )
    );


    hvn_panel_label($wp_customize, $section,  __('コメント', THEME_NAME), 3);

    $wp_customize->add_setting('hvn_comment_setting', array('default' => false));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'hvn_comment_setting',
        array(
          'label'     =>  __('コメントアイコン選択・表示', THEME_NAME),
          'section'   => "hvn_{$section}_section",
          'settings'  => 'hvn_comment_setting',
          'type'      => 'checkbox',
        )
      )
    );


    for ($i=1; $i<=3; $i++) {
      $label = null;
      if ($i == 1) {
        $label =  __('画像', THEME_NAME);
      }
      $wp_customize->add_setting('hvn_comment_img' . $i . '_setting');
      $wp_customize->add_control(
        new WP_Customize_Media_Control(
        $wp_customize,
          'hvn_comment_img' . $i . '_setting',
          array(
            'label'       => $label,
            'description' => __('画像', THEME_NAME) . "[{$i}]",
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
