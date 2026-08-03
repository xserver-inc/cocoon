<?php
// サニタイズ
function sanitize_shadow($input) {
    $valid = array(
        'shadow0' => 'shadow0',
        'shadow1' => 'shadow1',
        'shadow2' => 'shadow2',
    );

    if (array_key_exists($input, $valid)) {
        return $input;
    } else {
        return '';
    }
}
function sanitize_radius($input) {
    $valid = array(
        'radius0' => 'radius0',
        'radius1' => 'radius1',
        'radius2' => 'radius2',
    );

    if (array_key_exists($input, $valid)) {
        return $input;
    } else {
        return '';
    }
}
function sanitize_font($input) {
    $valid = array(
        'Fugaz One' => 'Fugaz One',
        'Saira' => 'Saira',
        'Alice' => 'Alice',
        'Comfortaa' => 'Comfortaa',
        'Quicksand' => 'Quicksand',
        'Roboto' => 'Roboto',
        'none'=>'none',
    );

    if (array_key_exists($input, $valid)) {
        return $input;
    } else {
        return '';
    }
}
function mytheme_sanitize_float_range( $number, $setting ) {
  // デフォルト値を取得
  $default = $setting->default;

  // 空白の場合はデフォルト値を返す
  if ( empty( $number ) && $number !== '0' ) {
    return $default;
  }

  // 浮動小数点数に変換
  $number = floatval( $number );

  // 0〜1の範囲に収める
  $number = max( 0, min( 1, $number ) );

  return $number;
}

//チェックボックス
function nagi_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

if (!function_exists('nagi_sanitize_number_range')):
  function nagi_sanitize_number_range($number, $setting) {
    $number = absint($number);
    $atts   = $setting->manager->get_control($setting->id)->input_attrs;
    $min  = (isset($atts['min'])  ? $atts['min'] : $number);
    $max  = (isset($atts['max'])  ? $atts['max'] : $number);
    $step = (isset($atts['step']) ? $atts['step'] : 1);

    return ($min <= $number && $number <= $max && is_int($number / $step) ? $number : $setting->default);
  }
  endif;

function nagi_customize_register( $wp_customize ) {

    $wp_customize->add_panel( 'nagi_customizer_panel', array(
        'title'    => __( 'スキンNAGIの設定', THEME_NAME ),
        'priority' => 1

    ));
    $wp_customize->add_section( 'nagi_customizer_sct', array(
      'title'    => __( '全体設定', THEME_NAME ),
      'description' => __( '設定の詳細は<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-zentai/">こちら</a>', THEME_NAME ),
      'priority' => 1,
      'panel'=>'nagi_customizer_panel'
    ));

  $wp_customize->add_setting(
    'shadow_radio',
    array(
      'default' => 'shadow1',
      'priority' => 1000,
      'sanitize_callback' => 'sanitize_shadow',
    )
  );

  $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
    'nagi_shadow_radio',
    array(
      'section'     => 'nagi_customizer_sct',
      'settings'    => 'shadow_radio',
      'label'       => __( '影をつけるか', THEME_NAME ),
      'type'        => 'radio',
      'choices'     => array(
        'shadow0' => __( '影をつけない', THEME_NAME ),
        'shadow1' => __( 'ちょっぴり影をつける', THEME_NAME ),
        'shadow2' => __( 'くっきり影をつける（背景が暗い場合はこれがおすすめ）', THEME_NAME ),
      ),
    ) )
  );
  $wp_customize->add_setting(
    'radius_radio',
    array(
      'default' => 'radius1',
      'priority' => 1000,
      'sanitize_callback' => 'sanitize_radius',
    )
  );

  $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
    'nagi_radius_radio',
    array(
      'section'     => 'nagi_customizer_sct',
      'settings'    => 'radius_radio',
      'label'       => __( '丸みをつけるか', THEME_NAME ),
      'type'        => 'radio',
      'choices'     => array(
        'radius0' => __( '丸みをつけない', THEME_NAME ),
        'radius1' => __( 'ちょっぴり丸みをつける', THEME_NAME ),
        'radius2' => __( 'くっきり丸みをつける', THEME_NAME ),
      ),
    ) )
  );
  $wp_customize->add_setting(
    'gray_zone',
    array(
      'default'           => '#efefef',
      'priority'          => 1001,
      'sanitize_callback' => 'sanitize_hex_color'
    )
  );
$wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'gray_zone_ctr',
      array(
        'section'     => 'nagi_customizer_sct',
        'settings'    => 'gray_zone',
        'label'       => __( '薄グレーゾーンの色', THEME_NAME ),
        'description' => __( '<small>目次やシェアボタン、フォローボタン等の背景色です。薄グレーゾーンといってますが、何色でもいいです。<br>※35%の透過となります。</small>', THEME_NAME )
      )
    )
  );

  $wp_customize->add_setting(
    'sidebar_use_gray',
    array(
      'default' => true,
      'priority' => 1001,
      'sanitize_callback' => 'nagi_sanitize_checkbox',
    )
  );

  $wp_customize->add_control(
    'sidebar_use_gray',
    array(
      'section'     => 'nagi_customizer_sct',
      'settings'    => 'sidebar_use_gray',
      'label'       => __( 'サイドバーにもこの背景色をつける', THEME_NAME ),
      'type'        => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'sns_fix',
    array(
      'default' => true,
      'priority' => 1001,
      'sanitize_callback' => 'nagi_sanitize_checkbox',
    )
  );

  $wp_customize->add_control(
    'sns_fix',
    array(
      'section'     => 'nagi_customizer_sct',
      'settings'    => 'sns_fix',
      'label'       => __( 'SNSトップシェアボタンを画面左に固定する', THEME_NAME ),
      'description' => __( '<small>PC表示でのみ固定。Cocoon設定で「メインカラムトップシェアボタンを表示する」にチェックを入れる必要があります。</small>', THEME_NAME ),
      'type'        => 'checkbox'
    )
  );
  $wp_customize->add_setting(
    'cat_accordion',
    array(
      'default' => true,
      'priority' => 1001,
      'sanitize_callback' => 'nagi_sanitize_checkbox',
    )
  );

  $wp_customize->add_control(
    'cat_accordion',
    array(
      'section'     => 'nagi_customizer_sct',
      'settings'    => 'cat_accordion',
      'label'       => __( 'カテゴリーウィジェットの子カテゴリーをアコーディオン形式で開閉する', THEME_NAME ),
      'type'        => 'checkbox'
    )
  );

// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーインデックスタブの設定
$wp_customize->add_section( 'tab_customizer', array(
        'title'    => __( 'フロントページのタブ一覧の設定', THEME_NAME ),
        'description' => __( 'フロントページのタブ一覧のデザインを変更したり、各タブにアイコンを設定できます</br>
        詳しくは<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-tab/">こちら</a>', THEME_NAME ),
        'priority' => 1,
        'panel'=>'nagi_customizer_panel'

    ));
    $wp_customize->add_setting(
      'tab_icon_ori', // 設定ID
      array(
        'default' => true,
        'priority' => 1001,
        'sanitize_callback' => 'nagi_sanitize_checkbox',
      )
    );

    $wp_customize->add_control(
      'tab_icon_ori',
      array(
        'section'     => 'tab_customizer',
        'settings'    => 'tab_icon_ori',
        'label'       => __( 'フロントページのタブ一覧のデザインをオリジナルのものにする', THEME_NAME ),
        'description' => __( '<small>アイコンの設定が必須です。</small>', THEME_NAME ),
        'type'        => 'checkbox'
      )
    );

    $wp_customize->add_setting(
      'tab1_icon', // 設定ID
      array(
        'default' => 'f15c',
        'priority' => 1000,
        'sanitize_callback' => 'sanitize_text_field'
      )
    );

    $wp_customize->add_control(
      'tab1_icon_control',
      array(
        'section'     => 'tab_customizer',  // 紐づけるセクションIDを指定
        'settings'    => 'tab1_icon',  // 紐づける設定IDを指定
        'label'       => __( '1つ目のタブのアイコン', THEME_NAME ),
        'description' => __( '<small>FontAwesomeのUnicodeを入力します。</br>（例　f15c　）</small>', THEME_NAME ),
        'type'        => 'text'
      )
    );



    $wp_customize->add_setting(
      'tab2_icon', // 設定ID
      array(
        'default' => 'f164',
        'priority' => 1000,
        'sanitize_callback' => 'sanitize_text_field'
      )
    );

    $wp_customize->add_control(
      'tab2_icon_control',
      array(
        'section'     => 'tab_customizer',  // 紐づけるセクションIDを指定
        'settings'    => 'tab2_icon',  // 紐づける設定IDを指定
        'label'       => __( '2つ目のタブのアイコン', THEME_NAME ),
        'description' => __( '<small>FontAwesomeのUnicodeを入力します。</br>（例　f164　）</small>', THEME_NAME ),
        'type'        => 'text'
      )
    );

    $wp_customize->add_setting(
      'tab3_icon', // 設定ID
      array(
        'default' => 'f164',
        'priority' => 1000,
        'sanitize_callback' => 'sanitize_text_field'
      )
    );

    $wp_customize->add_control(
      'tab3_icon_control',
      array(
        'section'     => 'tab_customizer',  // 紐づけるセクションIDを指定
        'settings'    => 'tab3_icon',  // 紐づける設定IDを指定
        'label'       => __( '3つ目のタブのアイコン', THEME_NAME ),
        'description' => __( '<small>FontAwesomeのUnicodeを入力します。</br>（例　f164　）</small>', THEME_NAME ),
        'type'        => 'text'
      )
    );


    $wp_customize->add_setting(
      'tab4_icon', // 設定ID
      array(
        'default' => 'f164',
        'priority' => 1000,
        'sanitize_callback' => 'sanitize_text_field'
      )
    );

    $wp_customize->add_control(
      'tab4_icon_control',
      array(
        'section'     => 'tab_customizer',  // 紐づけるセクションIDを指定
        'settings'    => 'tab4_icon',  // 紐づける設定IDを指定
        'label'       => __( '4つ目のタブのアイコン', THEME_NAME ),
        'description' => __( '<small>FontAwesomeのUnicodeを入力します。</br>（例　f164　）</small>', THEME_NAME ),
        'type'        => 'text'
      )
    );
    $wp_customize->add_setting(
      'top_tab_bg',
      array(
        'default'           => '#474a56',
        'priority'          => 1001,
        'sanitize_callback' => 'sanitize_hex_color'
      )
    );
$wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'top_tab_bg',
        array(
          'section'     => 'tab_customizer',
          'settings'    => 'top_tab_bg',
          'label'       => __( 'タブの背景色', THEME_NAME ),
        )
      )
    );
$wp_customize->add_setting(
      'top_tab_color',
      array(
        'default'           => '#ffffff',
        'priority'          => 1001,
        'sanitize_callback' => 'sanitize_hex_color'
      )
    );
$wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'top_tab_color',
        array(
          'section'     => 'tab_customizer',
          'settings'    => 'top_tab_color',
          'label'       => __( 'タブの文字色', THEME_NAME ),
        )
      )
    );
    $wp_customize->add_setting(
        'tab_bg_1',
        array(
          'default'           => '#00d4ff',
          'priority'          => 1001,
          'sanitize_callback' => 'sanitize_hex_color'
        )
      );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
          $wp_customize,
          'tab_bg_1_ctl',
          array(
            'section'     => 'tab_customizer',
            'settings'    => 'tab_bg_1',
            'label'       => __( 'アクティブなタブの背景色①', THEME_NAME ),
            'description' => __( '<small>①と②を違う色にすることでグラデーションになります</small>', THEME_NAME )
          )
        )
      );
      $wp_customize->add_setting(
        'tab_bg_2',
        array(
          'default'           => '#e9ff00',
          'priority'          => 1001,
          'sanitize_callback' => 'sanitize_hex_color'
        )
      );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
          $wp_customize,
          'tab_bg_2_ctl',
          array(
            'section'     => 'tab_customizer',
            'settings'    => 'tab_bg_2',
            'label'       => __( 'アクティブなタブの背景色②', THEME_NAME ),
            'description' => __( '<small>①と②を違う色にすることでグラデーションになります</small>', THEME_NAME )
          )
        )
      );
// ---------------------------------------------------------------------------------------------フォントの設定
$wp_customize->add_section( 'font_sct', array(
        'title'    => __( 'フォント', THEME_NAME ),
        'priority' => 1,
        'panel'=>'nagi_customizer_panel'
));
$wp_customize->add_setting(
    'font_radio',
    array(
      'default' => 'Quicksand',
      'priority' => 1000,
      'sanitize_callback' => 'sanitize_font',
    )
  );

  $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
    'font_radio',
    array(
      'section'     => 'font_sct',
      'settings'    => 'font_radio',
      'label'       => __( 'グーグルフォントを選択する', THEME_NAME ),
      'type'        => 'radio',
      'choices'     => array(
        'Fugaz One' => sprintf( 'Fugaz One (%s)', __( 'font-weight: 400 のみ', THEME_NAME ) ),
        'Saira' => 'Saira',
        'Alice' => sprintf( 'Alice (%s)', __( 'font-weight: 400 のみ', THEME_NAME ) ),
        'Comfortaa' => 'Comfortaa',
        'Quicksand' => 'Quicksand',
        'Roboto' => 'Roboto',
        'none'=> __( '指定なし', THEME_NAME ),
      ),
    ) )
  );
// --------------------------------------------------------------------------フッター固定CTA
  $wp_customize->add_section( 'cta_customizer', array(
    'title'    => __( 'フッター固定CTAの色', THEME_NAME ),
    'description' => __( 'フッター固定CTAの背景色などを変更できます。</br>設定の詳細は<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">こちら</a>', THEME_NAME ),
    'priority' => 1,
    'panel'=>'nagi_customizer_panel'

));
$wp_customize->add_setting(
  'cta_bg',
  array(
    'default'           => '#000000',
    'priority'          => 1001,
    'sanitize_callback' => 'sanitize_hex_color'
  )
);
$wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize,
    'cta_bg',
    array(
      'section'     => 'cta_customizer',
      'settings'    => 'cta_bg',
      'label'       => __( 'フッター固定CTAの背景色', THEME_NAME ),
    )
  )
);
$wp_customize->add_setting(
  'cta_bg_opa',
  array(
    'default'           => '0.9',
    'priority'          => 1001,
    'sanitize_callback' => 'mytheme_sanitize_float_range',

  )
);
$wp_customize->add_control(
    'cta_bg_opa',
    array(
      'section'     => 'cta_customizer',
      'settings'    => 'cta_bg_opa',
      'description' => __( '0から1まで0.01単位で設定できます<br>0に近くなるほど透明になります', THEME_NAME ),
      'label'       => __( 'フッター固定CTAの背景色の透明度', THEME_NAME ),
      'type'     => 'number',
      'input_attrs' => array(
          'min' => 0,
          'max' => 1,
          'step' => 0.01,
      ),
    )
);

$wp_customize->add_setting(
  'cta_color',
  array(
    'default'           => '#ffffff',
    'priority'          => 1001,
    'sanitize_callback' => 'sanitize_hex_color'
  )
);
$wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize,
    'cta_color',
    array(
      'section'     => 'cta_customizer',
      'settings'    => 'cta_color',
      'label'       => __( 'フッター固定CTAのマイクロコピーの色', THEME_NAME ),
    )
  )
);
$wp_customize->add_setting(
  'cta_red',
  array(
    'default'           => '#ff3131',
    'priority'          => 1001,
    'sanitize_callback' => 'sanitize_hex_color'
  )
);
$wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize,
    'cta_red',
    array(
      'section'     => 'cta_customizer',
      'settings'    => 'cta_red',
      'label'       => __( '赤系ボタン', THEME_NAME ),
    )
  )
);
$wp_customize->add_setting(
  'cta_blue',
  array(
    'default'           => '#0076ff',
    'priority'          => 1001,
    'sanitize_callback' => 'sanitize_hex_color'
  )
);
$wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize,
    'cta_blue',
    array(
      'section'     => 'cta_customizer',
      'settings'    => 'cta_blue',
      'label'       => __( '青系ボタン', THEME_NAME ),
    )
  )
);
$wp_customize->add_setting(
  'cta_green',
  array(
    'default'           => '#42d800',
    'priority'          => 1001,
    'sanitize_callback' => 'sanitize_hex_color'
  )
);
$wp_customize->add_control(
  new WP_Customize_Color_Control(
    $wp_customize,
    'cta_green',
    array(
      'section'     => 'cta_customizer',
      'settings'    => 'cta_green',
      'label'       => __( '緑系ボタン', THEME_NAME ),
    )
  )
);

// -----------------------カルーセル
$wp_customize->add_section( 'carousel_customizer', array(
  'title'    => __( 'カルーセルの設定', THEME_NAME ),
  'description' => __( 'カルーセルの表示件数などを変更できます。詳しくは<a target="_blank" href="https://go-blogs.com/cocoon/carousel-setting/">こちら</a>', THEME_NAME ),
  'priority' => 1,
  'panel'=>'nagi_customizer_panel'

));


/////////////////////////////////////
//1241以上
/////////////////////////////////////
$wp_customize->add_setting(
  'carousel_1241over',
  array(
    'default'           => '4',
    'priority'          => 1001,
    'sanitize_callback' => 'nagi_sanitize_number_range',

  )
  );
  $wp_customize->add_control(
    'carousel_1241over',
    array(
      'section'     => 'carousel_customizer',
      'settings'    => 'carousel_1241over',
      'description' => __( '2～6枚で設定可', THEME_NAME ),
      'label'       => __( '画面幅 1241px以上での表示枚数', THEME_NAME ),
      'type'     => 'number',
      'input_attrs' => array(
          'min' => 2,
          'max' => 6,
          'step' => 1,
      ),
    )
  );

  $wp_customize->add_setting(
    'slide_1241over',
    array(
      'default'           => 'true',
      'priority'          => 1001,
      'sanitize_callback' => 'nagi_sanitize_number_range',

    )
    );
    $wp_customize->add_control(
      'slide_1241over',
      array(
        'section'     => 'carousel_customizer',
        'settings'    => 'slide_1241over',
        'description' => __( 'チェックなしの場合は表示枚数と同じ枚数スライドします', THEME_NAME ),
        'label'       => __( '1枚ずつスライドさせる', THEME_NAME ),
        'type'     => 'checkbox',
      )
    );

//-----------------------------------------
//1024-1240
//-----------------------------------------
$wp_customize->add_setting(
'carousel_1024_1240',
array(
  'default'           => '4',
  'priority'          => 1001,
  'sanitize_callback' => 'nagi_sanitize_number_range',

)
);
$wp_customize->add_control(
  'carousel_1024_1240',
  array(
    'section'     => 'carousel_customizer',
    'settings'    => 'carousel_1024_1240',
    'description' => __( '2～6枚で設定可', THEME_NAME ),
    'label'       => __( '画面幅 1024px～1240pxでの表示枚数', THEME_NAME ),
    'type'     => 'number',
    'input_attrs' => array(
        'min' => 2,
        'max' => 6,
        'step' => 1,
    ),
  )
);

$wp_customize->add_setting(
  'slide_1024_1240',
  array(
    'default'           => '1',
    'priority'          => 1001,
    'sanitize_callback' => 'nagi_sanitize_number_range',

  )
  );
  $wp_customize->add_control(
    'slide_1300',
    array(
      'section'     => 'carousel_customizer',
      'settings'    => 'slide_1024_1240',
      'description' => __( 'チェックなしの場合は表示枚数と同じ枚数スライドします', THEME_NAME ),
        'label'       => __( '1枚ずつスライドさせる', THEME_NAME ),
        'type'     => 'checkbox',
      )
    );

//-----------------------------------------
//835-1023
//-----------------------------------------
$wp_customize->add_setting(
  'carousel_835_1023',
  array(
    'default'           => '3',
    'priority'          => 1001,
    'sanitize_callback' => 'nagi_sanitize_number_range',

  )
  );
  $wp_customize->add_control(
    'carousel_835_1023',
    array(
      'section'     => 'carousel_customizer',
      'settings'    => 'carousel_835_1023',
      'description' => __( '2～6枚で設定可', THEME_NAME ),
      'label'       => __( '画面幅 835px～1023pxでの表示枚数', THEME_NAME ),
      'type'     => 'number',
      'input_attrs' => array(
          'min' => 2,
          'max' => 6,
          'step' => 1,
      ),
    )
  );

  $wp_customize->add_setting(
    'slide_835_1023',
    array(
      'default'           => '1',
      'priority'          => 1001,
      'sanitize_callback' => 'nagi_sanitize_number_range',

    )
    );
    $wp_customize->add_control(
      'slide_835_1023',
      array(
        'section'     => 'carousel_customizer',
        'settings'    => 'slide_835_1023',
        'description' => __( 'チェックなしの場合は表示枚数と同じ枚数スライドします', THEME_NAME ),
        'label'       => __( '1枚ずつスライドさせる', THEME_NAME ),
        'type'     => 'checkbox',
      )
    );

//-----------------------------------------
//481-834
//-----------------------------------------
  $wp_customize->add_setting(
    'carousel_481_834',
    array(
      'default'           => '2',
      'priority'          => 1001,
      'sanitize_callback' => 'nagi_sanitize_number_range',

    )
    );
    $wp_customize->add_control(
      'carousel_481_834',
      array(
        'section'     => 'carousel_customizer',
        'settings'    => 'carousel_481_834',
      'description' => __( '2～4枚で設定可', THEME_NAME ),
      'label'       => __( '画面幅 481px～834pxでの表示枚数', THEME_NAME ),
        'type'     => 'number',
        'input_attrs' => array(
            'min' => 2,
            'max' => 4,
            'step' => 1,
        ),
      )
    );


    $wp_customize->add_setting(
      'slide_481_834',
      array(
        'default'           => '1',
        'priority'          => 1001,
        'sanitize_callback' => 'nagi_sanitize_number_range',

      )
      );
      $wp_customize->add_control(
        'slide_481_834',
        array(
          'section'     => 'carousel_customizer',
          'settings'    => 'slide_481_834',
        'description' => __( 'チェックなしの場合は表示枚数と同じ枚数スライドします', THEME_NAME ),
        'label'       => __( '1枚ずつスライドさせる', THEME_NAME ),
        'type'     => 'checkbox',
      )
    );

//-----------------------------------------
//480以下
//-----------------------------------------
    $wp_customize->add_setting(
      'carousel_under480',
      array(
        'default'           => '1',
        'priority'          => 1001,
        'sanitize_callback' => 'nagi_sanitize_number_range',

      )
      );
      $wp_customize->add_control(
        'carousel_under480',
        array(
          'section'     => 'carousel_customizer',
          'settings'    => 'carousel_under480',
      'description' => __( '1～2枚で設定可', THEME_NAME ),
      'label'       => __( '画面幅 480px以下での表示枚数', THEME_NAME ),
          'type'     => 'number',
          'input_attrs' => array(
              'min' => 1,
              'max' => 2,
              'step' => 1,
          ),
        )
      );


     $wp_customize->add_setting(
       'slide_under480',
       array(
         'default'           => '1',
         'priority'          => 1001,
         'sanitize_callback' => 'nagi_sanitize_number_range',
        )
        );
      $wp_customize->add_control(
         'slide_under480',
         array(
           'section'     => 'carousel_customizer',
           'settings'    => 'slide_under480',
        'description' => __( 'チェックなしの場合は表示枚数と同じ枚数スライドします', THEME_NAME ),
        'label'       => __( '1枚ずつスライドさせる', THEME_NAME ),
        'type'     => 'checkbox',
      )
    );


    }



add_action( 'customize_register', 'nagi_customize_register' );

