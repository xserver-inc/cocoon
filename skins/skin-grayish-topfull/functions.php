<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
if (!defined('ABSPATH')) exit;

// -----------------------------------------------------------------------------
// skin独自のCocoon設定のデフォルト変更など：
// -----------------------------------------------------------------------------
// site_font_size

// font_awesome_5 default
if (!function_exists('get_site_icon_font')) :
  function get_site_icon_font()
  {
    // return get_theme_option(OP_SITE_ICON_FONT, 'font_awesome_5');
    return 'font_awesome_5';
  }
endif;


// -----------------------------------------------------------------------------
// skin独自のclass追加：
// -----------------------------------------------------------------------------
// body
add_filter('body_class_additional', function ($classes) {
  $classes[] = 'skin-grayish';

  return $classes;
});

// -----------------------------------------------------------------------------
// skin独自のメニュー追加：
// -----------------------------------------------------------------------------
// グローバルナビの最後にモバイル用検索BOX流用し、入れる
add_filter('wp_nav_menu_items', 'add_header_spsearchform', 10, 2);

if (!function_exists('add_header_spsearchform')) :
  function add_header_spsearchform($navi, $args)
  {
    ob_start();
    get_template_part('skins/skin-grayish-topfull/tmp-grayish/cstm-sns-follow-buttons');
    get_template_part('skins/skin-grayish-topfull/tmp-grayish/cstm-mobile-search-button');
    $searchform = ob_get_contents();
    ob_end_clean();
    if (($args->theme_location == NAV_MENU_HEADER) || ($args->theme_location == NAV_MENU_MOBILE_SLIDE_IN)) {
      $navi .= $searchform;
    }
    return $navi;
  }
endif;

// -----------------------------------------------------------------------------
// skin独自のテーマカスタマイザー：
// -----------------------------------------------------------------------------
// radio, select 共通サニタイズ
function skin_grayish_sanitize_select($input, $setting)
{
  // $input = sanitize_key($input);
  $choices = $setting->manager->get_control($setting->id)->choices;
  return (array_key_exists($input, $choices) ? $input : $setting->default);
}
// number
function skin_grayish_sanitize_number_range($number, $setting)
{
  $number = absint($number);
  $atts = $setting->manager->get_control($setting->id)->input_attrs;
  $min = (isset($atts['min']) ? $atts['min'] : $number);
  $max = (isset($atts['max']) ? $atts['max'] : $number);
  $step = (isset($atts['step']) ? $atts['step'] : 1);
  return ($min <= $number && $number <= $max && is_int($number / $step) ? $number : $setting->default);
}

// アルファ値付きカラーコード変換　てがきノートさんのコード参照
function color_code($key_color, $key_opacity)
{
  $code_red   = hexdec(substr($key_color, 1, 2));
  $code_green = hexdec(substr($key_color, 3, 2));
  $code_blue  = hexdec(substr($key_color, 5, 2));
  // $opacity = intval($key_opacity) / 100;
  $opacity = $key_opacity;
  return 'rgba(' . $code_red . ',' . $code_green . ',' . $code_blue . ',' . $opacity . ')';
}


// 英字フォントとセットになる日本語フォント Cocoon設定のフォントを設定
//フォント 親テーマ all-forms.phpより参考
// $options = array(
//   'hiragino' => __('ヒラギノ角ゴ, メイリオ', THEME_NAME) . __('（デフォルト）', THEME_NAME),
//   'meiryo' => __('メイリオ, ヒラギノ角ゴ', THEME_NAME),
//   'yu_gothic' => __('游ゴシック体, ヒラギノ角ゴ', THEME_NAME),
//   'ms_pgothic' => __('ＭＳ Ｐゴシック, ヒラギノ角ゴ', THEME_NAME),
//   'noto_sans_jp' => __('Noto Sans JP（WEBフォント）', THEME_NAME),
//   'noto_serif_jp' => __('Noto Serif JP（WEBフォント）', THEME_NAME),
//   'mplus_1p' => __('Mplus 1p（WEBフォント）', THEME_NAME),
//   'rounded_mplus_1c' => __('Rounded Mplus 1c（WEBフォント）', THEME_NAME),
//   'kosugi' => __('小杉ゴシック（WEBフォント）', THEME_NAME),
//   'kosugi_maru' => __('小杉丸ゴシック（WEBフォント）', THEME_NAME),
//   // 'hannari' => __( 'はんなり明朝（WEBフォント）', THEME_NAME ),
//   // 'kokoro' => __( 'こころ明朝（WEBフォント）', THEME_NAME ),
//   'sawarabi_gothic' => __('さわらびゴシック（WEBフォント）', THEME_NAME),
//   'sawarabi_mincho' => __('さわらび明朝（WEBフォント）', THEME_NAME),
//   '' => __('指定なし', THEME_NAME),
// );

if (!function_exists('skin_get_site_font_family')) :
  function skin_get_site_font_family()
  {
    // return get_theme_option(OP_SITE_FONT_FAMILY, 'hiragino');
    // Cocoon設定のフォント情報をget
    $skin_font_set = get_theme_option(OP_SITE_FONT_FAMILY, 'hiragino');
    // echo $skin_font_set;
    switch ($skin_font_set) {
      case 'hiragino':
        return 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"';
        break;
      case 'meiryo':
        return 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"';
        break;
      case 'yu_gothic':
        return 'YuGothic, "Yu Gothic", Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"';
        break;
      case 'ms_pgothic':
        return '"MS PGothic", "Hiragino Kaku Gothic ProN", "Hiragino Sans"';
        break;
      case 'noto_sans_jp':
        return '"Noto Sans JP"';
        break;
      case 'noto_serif_jp':
        return '"Noto Serif JP"';
        break;
      case 'mplus_1p':
        return '"M PLUS 1p"';
        break;
      case 'rounded_mplus_1c':
        return '"M PLUS Rounded 1c"';
        break;
      case 'kosugi':
        return '"Kosugi"';
        break;
      case 'kosugi_maru':
        return '"Kosugi Maru"';
        break;
      case 'sawarabi_gothic':
        return '"Sawarabi Gothic"';
        break;
      case 'sawarabi_mincho':
        return '"Sawarabi Mincho"';
        break;
      default:
        return 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"';
    }
  }
endif;


// Googleフォント読み込みの変更
if (!function_exists('enqueue_skin_grayish_google_fonts')) :
  function enqueue_skin_grayish_google_fonts()
  {
    wp_enqueue_style('skin_grayish-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto+Slab:200,400|Spectral:200,400|Inknut+Antiqua:300,400|Jost:300,400|Lato:300,400|Lora|Montserrat:200,400&display=swap');
  }
endif;

add_action('wp_enqueue_scripts', 'enqueue_skin_grayish_google_fonts');
add_action('enqueue_block_editor_assets', 'enqueue_skin_grayish_google_fonts');

// -----------------------------------------------------------------------------
// テーマカスタマイザー　タイトル：見出しのGoogleFontの選択を可能に：
// ゆうそうとさんの てがきノートスキン (https://usort.jp/cocoon/)を参考にさせていただきました
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_font_customize')) :
  function skin_grayish_font_customize($wp_customize)
  {

    $wp_customize->add_panel(
      'font_pat_panel',
      array(
        'title'    => __('skin-grayish: カスタマイズ', THEME_NAME),
        'priority' => 1000,
      )
    );

    $wp_customize->add_section(
      'font_pat_section',
      array(
        'title'    => __('全体：英字フォント設定', THEME_NAME),
        'panel'    => 'font_pat_panel',
        'priority' => 1000,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/site-title-set/" target="_blank">'.__('英字フォントの適用箇所についてはこちらを参照', THEME_NAME).'</a>',
      )
    );

    $wp_customize->add_setting('font_pat_control_radio', array(
      'default' => 'font_Montserrat',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'font_pat_control_radio',
        array(
          'label' => __('英字フォント設定 選択', THEME_NAME),
          'description' => __('ロゴテキスト・グローバルナビメニューや、フロントページ・サイドバーの各見出しに使用する英字フォントを選択できます。', THEME_NAME).__('※固定ページや投稿のコンテンツは除く。「設定なし」にするとCocoon設定 > 全体設定 > サイトフォント の設定を継承します。', THEME_NAME),
          'section'  => 'font_pat_section',
          'settings' => 'font_pat_control_radio',
          'type'     => 'radio',
          'choices'  => array(
            'font_Montserrat' => 'Montserrat'.__('（デフォルト）', THEME_NAME),
            'font_Lato' => 'Lato',
            'font_InknutAntiqua' => 'Inknut Antiqua'.__('（細タイプなし）', THEME_NAME),
            'font_Spectral' => 'Spectral',
            'font_Lora' => 'Lora'.__('（細タイプなし）', THEME_NAME),
            'font_Jost' => 'Jost',
            'font_RobotoSlab' => 'Roboto Slab',
            'font_none' => __('設定なし', THEME_NAME),
          ),
        )
      )
    );

    $wp_customize->add_setting('title_font_weight_radio', array(
      'default' => 'font_weight_Thin',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'title_font_weight_radio',
        array(
          'label' => __('サイトロゴ（テキスト時）のフォント太さ 選択', THEME_NAME),
          'description' => __('ロゴテキストの英字フォントの太さを細（デフォルト）又は普通どちらか選択できます。', THEME_NAME).__('※フォントによって細いタイプがない場合があります。', THEME_NAME),
          'section'  => 'font_pat_section',
          'settings' => 'title_font_weight_radio',
          'type'     => 'radio',
          'choices'  => array(
            'font_weight_Thin' => __('細', THEME_NAME).__('（デフォルト）', THEME_NAME),
            'font_weight_Normal' => __('普通', THEME_NAME),
          ),
        )
      )
    );
  }
endif;

add_action('customize_register', 'skin_grayish_font_customize');

// head内にCSSを追加　英字Google Font
add_action('wp_head', 'skin_grayish_font_css');
if (!function_exists('skin_grayish_font_css')) :
  function skin_grayish_font_css()
  {
    $cocoon_site_font = skin_get_site_font_family();
    $style_template = '
		<style>
    .skin-grayish .navi, .skin-grayish .site-name-text, .skin-grayish .sub-caption, .skin-grayish .mobile-menu-buttons .menu-caption, .skin-grayish .navi-footer, .skin-grayish .menu-drawer, .skin-grayish .logo-menu-button, .skin-grayish .list-new-entries h1, .skin-grayish .list-columns h1, .skin-grayish .list-columns::after, .skin-grayish .related-entry-heading, .skin-grayish .comment-title, .skin-grayish .comment-btn, .skin-grayish .sidebar h3, .skin-grayish .footer h3, .skin-grayish .main-widget-label, .blank-box.bb-tab .bb-label {
				font-family: %s;
          font-weight: 400;
  letter-spacing: 0.1rem;
			}
		</style>
	';
    $style_font = '';
    if (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Montserrat') {
      $style_font = '"Montserrat", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Lato') {
      $style_font = '"Lato",' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_InknutAntiqua') {
      $style_font = '"Inknut Antiqua", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Spectral') {
      $style_font = '"Spectral", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Lora') {
      $style_font = '"Lora", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Jost') {
      $style_font = '"Jost", ' . $cocoon_site_font . ', var(--skin-grayish-default-font),sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_RobotoSlab') {
      $style_font = '"Roboto Slab", ' . $cocoon_site_font . ', var(--skin-grayish-default-font),sans-serif';
    } else {
      $style_font = 'inherit';
    }
    echo sprintf($style_template, $style_font);
    // CSS変数で出力
    echo '<style>:root {--skin-grayish-style-font: ' . $style_font . ';
                        --skin-get-site-font: ' . $cocoon_site_font . ', var(--skin-grayish-default-font),sans-serif' . ';
          }</style>';
  }
endif;

// Block Editor編集画面 タブボックスにもカスタマイザーで設定したGoogle Fontを当てる
if (!function_exists('skin_grayish_font_blkeditor')) :
  function skin_grayish_font_blkeditor()
  {
    $cocoon_site_font = skin_get_site_font_family();
    $style_template = '
.editor-styles-wrapper .blank-box.bb-tab::before {
  font-family: "Font Awesome 5 Free", %s!important;
  }
	';
    $style_template_amazon = '
.editor-styles-wrapper .blank-box.bb-amazon::before {
  font-family: "Font Awesome 5 Brands", %s!important;
  }
	';
    $style_font = '';
    if (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Montserrat') {
      $style_font = '"Montserrat", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Lato') {
      $style_font = '"Lato",' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_InknutAntiqua') {
      $style_font = '"Inknut Antiqua", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Spectral') {
      $style_font = '"Spectral", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Lora') {
      $style_font = '"Lora", ' . $cocoon_site_font . ', var(--skin-grayish-default-font), sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_Jost') {
      $style_font = '"Jost", ' . $cocoon_site_font . ', var(--skin-grayish-default-font),sans-serif';
    } elseif (get_theme_mod('font_pat_control_radio', 'font_Montserrat') === 'font_RobotoSlab') {
      $style_font = '"Roboto Slab", ' . $cocoon_site_font . ', var(--skin-grayish-default-font),sans-serif';
    } else {
      $style_font = 'inherit';
    }
    // echo sprintf($style_template, $style_font);
    $style_fontfamily = sprintf($style_template, $style_font);
    wp_add_inline_style('wp-block-editor', $style_fontfamily);

    $style_fontfamily_amazon = sprintf($style_template_amazon, $style_font);
    wp_add_inline_style('wp-block-editor', $style_fontfamily_amazon);
    // ブロックエディタの中にもCSS変数で出力
    $style_fontfamily_cstm =
      '
      :root {--skin-grayish-style-font: ' . $style_font . ';
        --skin-get-site-font: ' . $cocoon_site_font . ', var(--skin-grayish-default-font),sans-serif' . ';
        }	';
    wp_add_inline_style('wp-block-editor', $style_fontfamily_cstm);
  }
endif;
add_action('enqueue_block_editor_assets', 'skin_grayish_font_blkeditor');


// head内にCSSを追加　タイトルフォントの太さ
add_action('wp_head', 'skin_grayish_titlefont_weight');
if (!function_exists('skin_grayish_titlefont_weight')) :
  function skin_grayish_titlefont_weight()
  {
    $style_template = '
		<style>
    .skin-grayish .header-in .site-name-text {
          font-weight: %s;
			}
		</style>
	';
    $style_font_weight = '';
    if (get_theme_mod('title_font_weight_radio', 'font_weight_Thin') === 'font_weight_Thin') {
      $style_font_weight = '200';
    } elseif (get_theme_mod('title_font_weight_radio', 'font_weight_Thin') === 'font_weight_Normal') {
      $style_font_weight = '400';
    }
    echo sprintf($style_template, $style_font_weight);
  }
endif;


// -----------------------------------------------------------------------------
// テーマカスタマイザー　カラーの変更可能：
// Skin Name: 凸凹（Skin URI: https://kitatarian.com/cocoon/）を参考にさせていただきました
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_color_customize')) :
  function skin_grayish_color_customize($wp_customize)
  {
    $wp_customize->add_section(
      'color_pat_section',
      array(
        'title'    => __('全体：カラー設定', THEME_NAME),
        'panel'    => 'font_pat_panel',
        'priority' => 2000,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/manual-cstm-th-color/" target="_blank">'.__('各カラーの適用箇所についてはこちらを参照', THEME_NAME).'</a>',
      )
    );


    //テキストカラー --LtGray_S50
    $wp_customize->add_setting('colorpicker_text_gray_S50', array(
      'default' => '#535252',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'colorpicker_text_gray_S50',
        array(
          'label'    => __('テキストカラー', THEME_NAME),
          'description' => __('メニュー・見出し・本文などのテキストカラーを変更。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'color_pat_section',
          'settings' => 'colorpicker_text_gray_S50',
        )
      )
    );


    //メイン --LtBlue_T0
    $wp_customize->add_setting('colorpicker_main_blue_T0', array(
      'default' => '#AAC2D2',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'colorpicker_main_blue_T0',
        array(
          'label'    => __('メインアクセント 一番明るい青色', THEME_NAME),
          'description' => __('サイトロゴテキストや各テキストリンクのホバー時の色、<br>フロントページのNewPostの色、<br>カテゴリタイトル下線、<br>サイドバー・ウィジェット見出し下線の色、<br>フッター内アクセント線の色を変更。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'color_pat_section',
          'settings' => 'colorpicker_main_blue_T0',
        )
      )
    );
    //メイン　薄い青 --LtBlue_T70
    $wp_customize->add_setting('colorpicker_main_blue_T70', array(
      'default' => '#E6EDF2',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'colorpicker_main_blue_T70',
        array(
          'label'    => __('メイン 薄い青色', THEME_NAME),
          'description' => __('グローバルナビの3階層目の背景色、<br>フロントページのMoreボタン、次のページへボタン、タグクラウドのホバー時背景色。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'color_pat_section',
          'settings' => 'colorpicker_main_blue_T70',
        )
      )
    );
    //メイン 一番薄い青 --LtBlue_T90
    $wp_customize->add_setting('colorpicker_main_blue_T90', array(
      'default' => '#F7F9FB',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'colorpicker_main_blue_T90',
        array(
          'label'    => __('メイン 一番薄い青', THEME_NAME),
          'description' => __('フロントページカテゴリー２つ目の背景色、<br>目次・記事下のSNSボタン背景のストライプの青系、<br>一覧ページのページネーションボタン背景色。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'color_pat_section',
          'settings' => 'colorpicker_main_blue_T90',
        )
      )
    );
    //サブ 濃い青 --LtBlue_S30
    $wp_customize->add_setting('colorpicker_main_blue_S30', array(
      'default' => '#778893',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'colorpicker_main_blue_S30',
        array(
          'label'    => __('サブ 濃い青', THEME_NAME),
          'description' => __('プロフィールname・投稿の日付・アイコン・本文目次の上下線・目次テキスト・SNSシェア・Follow・アピールエリアのボタン等。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'color_pat_section',
          'settings' => 'colorpicker_main_blue_S30',
        )
      )
    );
    //サブ もっと濃い青 --LtBlue_S50
    $wp_customize->add_setting('colorpicker_main_blue_S50', array(
      'default' => '#556169',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'colorpicker_main_blue_S50',
        array(
          'label'    => __('サブ もっと濃い青', THEME_NAME),
          'description' => __('カテゴリーラベル背景色・記事内タグの枠線とアイコンの色。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'color_pat_section',
          'settings' => 'colorpicker_main_blue_S50',
        )
      )
    );
  }
endif;
add_action('customize_register', 'skin_grayish_color_customize');



// head内にCSSを追加　CSS変数の上書き
if (!function_exists('skin_grayish_color_customize_css')) :
  function skin_grayish_color_customize_css()
  {
    if (get_theme_mod('colorpicker_text_gray_S50', '#535252')) {
      $text_color = esc_attr(get_theme_mod('colorpicker_text_gray_S50', '#535252'));
    } else {
      $text_color = '#535252';
    }

    if (get_theme_mod('colorpicker_main_blue_T0', '#AAC2D2')) {
      $main_color = esc_attr(get_theme_mod('colorpicker_main_blue_T0', '#AAC2D2'));
    } else {
      $main_color = '#AAC2D2';
    }

    if (get_theme_mod('colorpicker_main_blue_T70', '#E6EDF2')) {
      $main_thin_color = esc_attr(get_theme_mod('colorpicker_main_blue_T70', '#E6EDF2'));
    } else {
      $main_thin_color = '#E6EDF2';
    }

    if (get_theme_mod('colorpicker_main_blue_T90', '#F7F9FB')) {
      $main_exthin_color = esc_attr(get_theme_mod('colorpicker_main_blue_T90', '#F7F9FB'));
    } else {
      $main_exthin_color = '#F7F9FB';
    }

    if (get_theme_mod('colorpicker_main_blue_S30', '#778893')) {
      $sub_color = esc_attr(get_theme_mod('colorpicker_main_blue_S30', '#778893'));
    } else {
      $sub_color = '#778893';
    }

    if (get_theme_mod('colorpicker_main_blue_S50', '#556169')) {
      $sub_cattag_color = esc_attr(get_theme_mod('colorpicker_main_blue_S50', '#556169'));
    } else {
      $sub_cattag_color = '#556169';
    }

?>
    <style type="text/css">
      :root {
        --skin-grayish-site-name-txt: <?php echo $text_color ?>;
        --skin-grayish-a-wrap-txt: <?php echo $text_color ?>;
        --skin-grayish-author-description-txt: <?php echo $text_color ?>;
        --skin-grayish-site-main-hover: <?php echo $main_color ?>;
        --skin-grayish-site-main-thin: <?php echo $main_thin_color ?>;
        --skin-grayish-gradient: <?php echo $main_exthin_color ?>;
        --skin-grayish-site-sub-color: <?php echo $sub_color ?>;
        --skin-grayish-cat-back: <?php echo $sub_cattag_color ?>;
      }
    </style>
    <?php

  }
endif;
add_action('wp_head', 'skin_grayish_color_customize_css');

// Block Editor編集画面にもカスタマイザーで設定したカラーを反映させる
if (!function_exists('skin_grayish_color_customize_blkeditor')) :
  function skin_grayish_color_customize_blkeditor()
  {
    if (get_theme_mod('colorpicker_text_gray_S50', '#535252')) {
      $text_color = esc_attr(get_theme_mod('colorpicker_text_gray_S50', '#535252'));
    } else {
      $text_color = '#535252';
    }
    if (get_theme_mod('colorpicker_main_blue_T0', '#AAC2D2')) {
      $main_color = esc_attr(get_theme_mod('colorpicker_main_blue_T0', '#AAC2D2'));
    } else {
      $main_color = '#AAC2D2';
    }

    if (get_theme_mod('colorpicker_main_blue_T70', '#E6EDF2')) {
      $main_thin_color = esc_attr(get_theme_mod('colorpicker_main_blue_T70', '#E6EDF2'));
    } else {
      $main_thin_color = '#E6EDF2';
    }

    if (get_theme_mod('colorpicker_main_blue_T90', '#F7F9FB')) {
      $main_exthin_color = esc_attr(get_theme_mod('colorpicker_main_blue_T90', '#F7F9FB'));
    } else {
      $main_exthin_color = '#F7F9FB';
    }

    if (get_theme_mod('colorpicker_main_blue_S30', '#778893')) {
      $sub_color = esc_attr(get_theme_mod('colorpicker_main_blue_S30', '#778893'));
    } else {
      $sub_color = '#778893';
    }

    if (get_theme_mod('colorpicker_main_blue_S50', '#556169')) {
      $sub_cattag_color = esc_attr(get_theme_mod('colorpicker_main_blue_S50', '#556169'));
    } else {
      $sub_cattag_color = '#556169';
    }

    $style_template =
      '
      :root {
        --skin-grayish-site-name-txt: %s;
        --skin-grayish-a-wrap-txt: %s;
        --skin-grayish-author-description-txt: %s;
        --skin-grayish-site-main-hover: %s;
        --skin-grayish-site-main-thin: %s;
        --skin-grayish-gradient: %s;
        --skin-grayish-site-sub-color: %s;
        --skin-grayish-cat-back: %s;
      }
	';
    $style_textcolor = sprintf($style_template, $text_color, $text_color, $text_color, $main_color, $main_thin_color, $main_exthin_color, $sub_color, $sub_cattag_color);

    wp_add_inline_style('wp-block-editor', $style_textcolor);
  }
endif;
add_action('enqueue_block_editor_assets', 'skin_grayish_color_customize_blkeditor');

// -----------------------------------------------------------------------------
// テーマカスタマイザー　フロントページ設定：ヘッダー
// ナビの高さ・背景色・メインビジュアル白ドットON/OFF選択可能・白オーバーレイの不透明度調整
// スクロールアニメーション　ON/OFF
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_topmv_dotoverlay_customize')) :
  function skin_grayish_topmv_dotoverlay_customize($wp_customize)
  {
    $wp_customize->add_section(
      'topmv_section',
      array(
        'title'    => __('フロントページ設定：ヘッダー', THEME_NAME),
        'panel'    => 'font_pat_panel',
        'priority' => 4000,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/manual-cstm-pcheader/" target="_blank">'.__('グローバルナビの高さ等変更についてはこちらを参照', THEME_NAME).'</a><br><br><a href="https://cocoon-grayish.na2-factory.com/manual-top-mv/" target="_blank">'.__('メインビジュアルについてはこちらを参照', THEME_NAME).'</a><br><br><a href="https://cocoon-grayish.na2-factory.com/manual-pc-menu/" target="_blank">'.__('グローバルナビのメニュー作成・SNSフォローボタンについてはこちらを参照', THEME_NAME).'</a>',

      )
    );

    //グローバルナビの高さ調整
    $wp_customize->add_setting('top_navi_size', array(
      'default' => '56',
      'sanitize_callback' => 'skin_grayish_sanitize_number_range',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'top_navi_size',
        array(
          'label'    => __('【PC】グローバルナビの高さ' , THEME_NAME),
          'description' => __('フロントページのグローバルナビの高さ。56px〜100pxの範囲で変更可能です。' , THEME_NAME).'
          <br>'.__('※空欄の場合や、範囲外の値の場合は56pxになります。' , THEME_NAME),
          'section'  => 'topmv_section',
          'settings' => 'top_navi_size',
          'type'     => 'number',
          'input_attrs' => array(
            'step'     => '1',
            'min'      => '56',
            'max'      => '100',
          ),
        )
      )
    );

    //グローバルナビの背景色　--LtGray_T70_A50
    $wp_customize->add_setting('top_navibg_colorpicker', array(
      'default' => '#E4E4E3',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'top_navibg_colorpicker',
        array(
          'label'    => __('【PC】グローバルナビの背景色', THEME_NAME),
          'description' => __('グローバルナビの背景色。', THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
          'section'  => 'topmv_section',
          'settings' => 'top_navibg_colorpicker',
        )
      )
    );

    //グローバルナビの背景色 透明度の設定　--LtGray_T70_A50
    $wp_customize->add_setting('top_navibg_alpha', array(
      'default' => 'top_navibg_alpha_5',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'top_navibg_alpha',
        array(
          'label'    => __('【PC】グローバルナビ背景色の不透明度調整', THEME_NAME),
          'description' => __('値が大きくなるほど背景色が濃くなります。', THEME_NAME),
          'section'  => 'topmv_section',
          'settings' => 'top_navibg_alpha',
          'type'     => 'select',
          'choices'  => array(
            'top_navibg_alpha_0' => '0'.__('（背景色なし)', THEME_NAME),
            'top_navibg_alpha_1' => '0.1',
            'top_navibg_alpha_2' => '0.2',
            'top_navibg_alpha_3' => '0.3',
            'top_navibg_alpha_4' => '0.4',
            'top_navibg_alpha_5' => '0.5'.__('（デフォルト）', THEME_NAME),
            'top_navibg_alpha_6' => '0.6',
            'top_navibg_alpha_7' => '0.7',
            'top_navibg_alpha_8' => '0.8',
            'top_navibg_alpha_9' => '0.9',
            'top_navibg_alpha_10' => '1.0'.__('(不透明)', THEME_NAME),
          ),
        )
      )
    );



    //メインビジュアル　ドット
    $wp_customize->add_setting('topmv_dot_radio', array(
      'default' => 'topmv_dot_On',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_dot_radio',
        array(
          'label'    => __('メインビジュアル 白ドットのON/OFF', THEME_NAME),
          'description' => __('メインビジュアルの上に白いドットを重ねるかどうかを選択。解像度が低い画像を使用する場合はON推奨', THEME_NAME),
          'section'  => 'topmv_section',
          'settings' => 'topmv_dot_radio',
          'type'     => 'radio',
          'choices'  => array(
            'topmv_dot_On' => __('ON' , THEME_NAME).__('（デフォルト）', THEME_NAME),
            'topmv_dot_Off' => __('OFF' , THEME_NAME),
          ),
        )
      )
    );

    //メインビジュアル　白のオーバーレイ
    $wp_customize->add_setting('topmv_whovlay_select', array(
      'default' => 'topmv_whoverlay_5',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_whovlay_select',
        array(
          'label'    => __('メインビジュアル 白オーバーレイの不透明度調整', THEME_NAME),
          'description' => __('メインビジュアルの上に重ねる白の不透明度を調整。', THEME_NAME).__('値が大きくなるほど白が濃くなります。', THEME_NAME),
          'section'  => 'topmv_section',
          'settings' => 'topmv_whovlay_select',
          'type'     => 'select',
          'choices'  => array(
            'topmv_whoverlay_0' => __('OFF' , THEME_NAME),
            'topmv_whoverlay_1' => '0.1',
            'topmv_whoverlay_2' => '0.2',
            'topmv_whoverlay_3' => '0.3',
            'topmv_whoverlay_4' => '0.4',
            'topmv_whoverlay_5' => '0.5'.__('（デフォルト）', THEME_NAME),
            'topmv_whoverlay_6' => '0.6',
            'topmv_whoverlay_7' => '0.7',
            'topmv_whoverlay_8' => '0.8',
            'topmv_whoverlay_9' => '0.9',
          ),
        )
      )
    );

    //メインビジュアル下のスクロールアニメーション
    $wp_customize->add_setting('topmv_scrollanim', array(
      'default' => 'topmv_scrollanim_On',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_scrollanim',
        array(
          'label'    => __('メインビジュアル下のスクロールアニメーションのON/OFF', THEME_NAME),
          'description' => __('メインビジュアルの下部にあるスクロールを促すアニメーションが不要の場合ここでOFF。', THEME_NAME).__('PC/SP共通の設定になります。', THEME_NAME),
          'section'  => 'topmv_section',
          'settings' => 'topmv_scrollanim',
          'type'     => 'radio',
          'choices'  => array(
            'topmv_scrollanim_On' => __('ON' , THEME_NAME).__('（デフォルト）', THEME_NAME),
            'topmv_scrollanim_Off' => __('OFF' , THEME_NAME),
          ),
        )
      )
    );


    // SNSフォローボタンの表示
    $settings_data = array(
      array(
        'label'       => __('X（旧Twitter）フォローボタンを表示', THEME_NAME),
        'setting_name' => 'twitter',
      ),
      array(
        'label'       => __('Mastodon フォローボタンを表示', THEME_NAME),
        'setting_name' => 'mastodon',
      ),
      array(
        'label'       => __('Bluesky フォローボタンを表示', THEME_NAME),
        'setting_name' => 'bluesky',
      ),
      array(
        'label'       => __('Misskey フォローボタンを表示', THEME_NAME),
        'setting_name' => 'misskey',
      ),
      array(
        'label'       => __('Facebook フォローボタンを表示', THEME_NAME),
        'setting_name' => 'facebook',
      ),
      array(
        'label'       => __('はてブ フォローボタンを表示', THEME_NAME),
        'setting_name' => 'hatena',
      ),
      array(
        'label'       => __('Instagram フォローボタンを表示', THEME_NAME),
        'setting_name' => 'instagram',
      ),
      array(
        'label'       => __('Pinterest フォローボタンを表示', THEME_NAME),
        'setting_name' => 'pinterest',
      ),
      array(
        'label'       => __('YouTube フォローボタンを表示', THEME_NAME),
        'setting_name' => 'youtube',
      ),
      array(
        'label'       => __('LinkedIn フォローボタンを表示', THEME_NAME),
        'setting_name' => 'linkedin',
      ),
      array(
        'label'       => __('note フォローボタンを表示', THEME_NAME),
        'setting_name' => 'note',
      ),
      array(
        'label'       => __('Flickr フォローボタンを表示', THEME_NAME),
        'setting_name' => 'flickr',
      ),
      array(
        'label'       => __('Amazon欲しい物リスト フォローボタンを表示', THEME_NAME),
        'setting_name' => 'amazon',
      ),
      array(
        'label'       => __('Twitch フォローボタンを表示', THEME_NAME),
        'setting_name' => 'twitch',
      ),
      array(
        'label'       => __('楽天ROOM フォローボタンを表示', THEME_NAME),
        'setting_name' => 'rakuten',
      ),
      array(
        'label'       => __('Slack フォローボタンを表示', THEME_NAME),
        'setting_name' => 'slack',
      ),
      array(
        'label'       => __('GitHub フォローボタンを表示', THEME_NAME),
        'setting_name' => 'github',
      ),
      array(
        'label'       => __('CodePen フォローボタンを表示', THEME_NAME),
        'setting_name' => 'codepen',
      ),
      array(
        'label'       => __('TikTok フォローボタンを表示', THEME_NAME),
        'setting_name' => 'tiktok',
      ),
      array(
        'label'       => __('SoundCloud フォローボタンを表示', THEME_NAME),
        'setting_name' => 'soundcloud',
      ),
      array(
        'label'       => __('LINE フォローボタンを表示', THEME_NAME),
        'setting_name' => 'line',
      ),

    );

    foreach ($settings_data as $data) {
      $setting_name = 'snsbtn_select_' . sanitize_title($data['setting_name']);

      $wp_customize->add_setting($setting_name, array(
        'default' => 'snsbtn_select_Off',
        'sanitize_callback' => 'skin_grayish_sanitize_select',
      ));

      $wp_customize->add_control(
        new WP_Customize_Control(
          $wp_customize,
          $setting_name,
          array(
            'label'       => $data['label'],
            'description' => __('プロフィールページでURLを入力している場合、グローバルナビにフォローボタンを表示します。', THEME_NAME).'
            <br>'.__('※フロントページ以外のグローバルナビにも表示されます。', THEME_NAME),
            'section'     => 'topmv_section',
            'settings'    => $setting_name,
            'type'        => 'select',
            'choices'     => array(
              'snsbtn_select_Off' => __('OFF' , THEME_NAME).__('（デフォルト）', THEME_NAME),
              'snsbtn_select_On'  => __('ON' , THEME_NAME),
            ),
          )
        )
      );
    }
  }
endif;

add_action('customize_register', 'skin_grayish_topmv_dotoverlay_customize');

// head内にCSSを追加
// グローバルナビの高さ調整
add_action('wp_head', 'skin_grayish_top_header_navisize');
if (!function_exists('skin_grayish_top_header_navisize')) :
  function skin_grayish_top_header_navisize()
  {
    if (get_theme_mod('top_navi_size', '56')) {
    ?>
      <style type="text/css">
        :root {
          --topHeaderNavisize: <?php echo esc_attr(get_theme_mod('top_navi_size', '56')) . 'px' ?>;
        }
      </style>
    <?php

    }
  }
endif;

// グローバルナビの背景色
add_action('wp_head', 'skin_grayish_top_header_navibg');
if (!function_exists('skin_grayish_top_header_navibg')) :
  function skin_grayish_top_header_navibg()
  {

    if (get_theme_mod('top_navibg_colorpicker', '#E4E4E3')) {
      $hex_navi_bgcolor = esc_attr(get_theme_mod('top_navibg_colorpicker', '#E4E4E3'));
    } else {
      $hex_navi_bgcolor = '#E4E4E3';
    }

    $style_opacity = '';
    if (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_5') {
      $style_opacity = '0.5';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_0') {
      $style_opacity = '0';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_1') {
      $style_opacity = '0.1';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_2') {
      $style_opacity = '0.2';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_3') {
      $style_opacity = '0.3';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_4') {
      $style_opacity = '0.4';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_6') {
      $style_opacity = '0.6';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_7') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_8') {
      $style_opacity = '0.8';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_9') {
      $style_opacity = '0.9';
    } elseif (get_theme_mod('top_navibg_alpha', 'top_navibg_alpha_5') === 'top_navibg_alpha_10') {
      $style_opacity = '1.0';
    } else {
      $style_opacity = '0.5';
    }
    // hex to dec
    $style_rgba = '';
    $style_rgba = color_code($hex_navi_bgcolor, $style_opacity);

    ?>
    <style type="text/css">
      :root {
        --topHeaderNaviBgColor: <?php echo $style_rgba ?>;
      }
    </style>
    <?php

  }
endif;

// ドット
add_action('wp_head', 'skin_grayish_topmv_dot');
if (!function_exists('skin_grayish_topmv_dot')) :
  function skin_grayish_topmv_dot()
  {
    $style_template = '
		<style>
.body.skin-grayish.front-top-page .container .header-container .header .grayish_topmv_dot {
          opacity: %s;
            visibility: %s;
			}
		</style>
	';
    $style_opacity = '';
    if (get_theme_mod('topmv_dot_radio', 'topmv_dot_On') === 'topmv_dot_On') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('topmv_dot_radio', 'topmv_dot_On') === 'topmv_dot_Off') {
      $style_opacity = '0';
    }
    $style_visibility = '';
    if (get_theme_mod('topmv_dot_radio', 'topmv_dot_On') === 'topmv_dot_On') {
      $style_visibility = 'visible';
    } elseif (get_theme_mod('topmv_dot_radio', 'topmv_dot_On') === 'topmv_dot_Off') {
      $style_visibility = 'hidden';
    }

    echo sprintf($style_template, $style_opacity, $style_visibility);
  }
endif;

// 白オーバーレイ
add_action('wp_head', 'skin_grayish_topmv_whovlay');
if (!function_exists('skin_grayish_topmv_whovlay')) :
  function skin_grayish_topmv_whovlay()
  {
    $style_template = '
		<style>
.body.skin-grayish.front-top-page .container .header-container .header .grayish_topmv_whovlay {
          opacity: %s;
            visibility: %s;
			}
		</style>
	';
    $style_opacity = '';
    if (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_5') {
      $style_opacity = '0.5';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_dot_Off') {
      $style_opacity = '0';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_1') {
      $style_opacity = '0.1';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_2') {
      $style_opacity = '0.2';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_3') {
      $style_opacity = '0.3';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_4') {
      $style_opacity = '0.4';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_6') {
      $style_opacity = '0.6';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_7') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_8') {
      $style_opacity = '0.8';
    } elseif (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_9') {
      $style_opacity = '0.9';
    } else {
      $style_opacity = '0.5';
    }


    $style_visibility = '';
    if (get_theme_mod('topmv_whovlay_select', 'topmv_whoverlay_5') === 'topmv_whoverlay_0') {
      $style_visibility = 'hidden';
    } else {
      $style_visibility = 'visible';
    }

    echo sprintf($style_template, $style_opacity, $style_visibility);
  }

endif;

// メインビジュアル下のスクロールアニメーション
add_action('wp_head', 'skin_grayish_topmv_scrollanim');
if (!function_exists('skin_grayish_topmv_scrollanim')) :
  function skin_grayish_topmv_scrollanim()
  {
    $style_template = '
		<style>
.skin-grayish.front-top-page .skinadd-topmv-scroll {
  display: %s;
}
      </style>
	';
    $style_visibility = '';
    if (get_theme_mod('topmv_scrollanim', 'topmv_scrollanim_On') === 'topmv_scrollanim_On') {
      $style_visibility = 'block';
    } elseif (get_theme_mod('topmv_scrollanim', 'topmv_scrollanim_On') === 'topmv_scrollanim_Off') {
      $style_visibility = 'none';
    }

    echo sprintf($style_template, $style_visibility);
  }
endif;

// PC SNSアイコンの表示 On/Offの設定を保存
add_action('wp_head', 'skin_grayish_gnavi_snsbtn');

if (!function_exists('skin_grayish_gnavi_snsbtn')) :
  function skin_grayish_gnavi_snsbtn()
  {
    global $skin_gnavi_snsbtn_options;
    global $skin_gnavi_snsbtn_On;
    $skin_gnavi_snsbtn_options = array(
      'gnavi_sns_twitter' => get_theme_mod('snsbtn_select_twitter', 'snsbtn_select_Off'),
      'gnavi_sns_mastodon' => get_theme_mod('snsbtn_select_mastodon', 'snsbtn_select_Off'),
      'gnavi_sns_bluesky' => get_theme_mod('snsbtn_select_bluesky', 'snsbtn_select_Off'),
      'gnavi_sns_misskey' => get_theme_mod('snsbtn_select_misskey', 'snsbtn_select_Off'),
      'gnavi_sns_facebook' => get_theme_mod('snsbtn_select_facebook', 'snsbtn_select_Off'),
      'gnavi_sns_hatena' => get_theme_mod('snsbtn_select_hatena', 'snsbtn_select_Off'),
      'gnavi_sns_instagram' => get_theme_mod('snsbtn_select_instagram', 'snsbtn_select_Off'),
      'gnavi_sns_pinterest' => get_theme_mod('snsbtn_select_pinterest', 'snsbtn_select_Off'),
      'gnavi_sns_youtube' => get_theme_mod('snsbtn_select_youtube', 'snsbtn_select_Off'),
      'gnavi_sns_linkedin' => get_theme_mod('snsbtn_select_linkedin', 'snsbtn_select_Off'),
      'gnavi_sns_note' => get_theme_mod('snsbtn_select_note', 'snsbtn_select_Off'),
      'gnavi_sns_flickr' => get_theme_mod('snsbtn_select_flickr', 'snsbtn_select_Off'),
      'gnavi_sns_amazon' => get_theme_mod('snsbtn_select_amazon', 'snsbtn_select_Off'),
      'gnavi_sns_twitch' => get_theme_mod('snsbtn_select_twitch', 'snsbtn_select_Off'),
      'gnavi_sns_rakuten' => get_theme_mod('snsbtn_select_rakuten', 'snsbtn_select_Off'),
      'gnavi_sns_slack' => get_theme_mod('snsbtn_select_slack', 'snsbtn_select_Off'),
      'gnavi_sns_github' => get_theme_mod('snsbtn_select_github', 'snsbtn_select_Off'),
      'gnavi_sns_codepen' => get_theme_mod('snsbtn_select_codepen', 'snsbtn_select_Off'),
      'gnavi_sns_tiktok' => get_theme_mod('snsbtn_select_tiktok', 'snsbtn_select_Off'),
      'gnavi_sns_soundcloud' => get_theme_mod('snsbtn_select_soundcloud', 'snsbtn_select_Off'),
      'gnavi_sns_line' => get_theme_mod('snsbtn_select_line', 'snsbtn_select_Off'),
    );
    // 配列内にOnがあるか
    $skin_gnavi_snsbtn_On = 'false';
    foreach ($skin_gnavi_snsbtn_options as $element) {
      if ($element === 'snsbtn_select_On') {
        $skin_gnavi_snsbtn_On = 'true';
      }
    }
  }
endif;



// -----------------------------------------------------------------------------
// テーマカスタマイザー　全体設定：テキスト変更
// テキスト変更
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_top_contents_customize')) :
  function skin_grayish_top_contents_customize($wp_customize)
  {
    $wp_customize->add_section(
      'topcontents_section',
      array(
        'title'    => __('全体：テキスト変更', THEME_NAME),
        'panel'    => 'font_pat_panel',
        'priority' => 3000,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/manual-cstm-contents-text/" target="_blank">'.__('テキスト変更についてはこちらを参照', THEME_NAME).'</a><br><br>',
      )
    );
    // New Postを好きなテキストに変更できる
    $wp_customize->add_setting('topmv_newpost_text', array(
      'default' => __('New Post' , THEME_NAME),
      'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_newpost_text',
        array(
          'label'    => __('【フロントページ】"New Post"を変更', THEME_NAME),
          'description' => __('New Postを別のテキストに変更できます。', THEME_NAME).'<br>'.__('Cocoon設定>インデックス>カテゴリーごとを選択した場合に表示されます。', THEME_NAME).'
          <br>'.__('※空欄の場合は"New Post"が表示されます。', THEME_NAME),
          'section'  => 'topcontents_section',
          'settings' => 'topmv_newpost_text',
          'type'     => 'text',
        )
      )
    );

    // "最新の記事"を好きなテキストに変更できる
    $wp_customize->add_setting('topmv_newpost_heading_text', array(
      'default' => __('最新の記事', THEME_NAME),
      'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_newpost_heading_text',
        array(
          'label'    => __('【フロントページ】"最新の記事"を変更', THEME_NAME),
          'description' => __('"最新の記事"を別のテキストに変更できます。', THEME_NAME).'<br>'.__('Cocoon設定>インデックス>タブ一覧又はカテゴリーごとを選択した場合に表示されます。', THEME_NAME).'
          <br>'.__('※空欄の場合は"最新の記事"が表示されます。', THEME_NAME),
          'section'  => 'topcontents_section',
          'settings' => 'topmv_newpost_heading_text',
          'type'     => 'text',
        )
      )
    );

    // Categoryを好きなテキストに変更できる
    $wp_customize->add_setting('topmv_category_text', array(
      'default' => 'Category',
      'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_category_text',
        array(
          'label'    => __('【フロントページ】"Category"を変更', THEME_NAME),
          'description' => __('Categoryを別のテキストに変更できます。', THEME_NAME).'<br>'.__('Cocoon設定>インデックス>カテゴリーごとを選択した場合に表示されます。', THEME_NAME).'
          <br>'.__('※空欄の場合は"Category"が表示されます。', THEME_NAME),
          'section'  => 'topcontents_section',
          'settings' => 'topmv_category_text',
          'type'     => 'text',
        )
      )
    );

    // フロントページのMoreボタン テキストをMore以外に書き換え Cocoon設定>インデックス>
    $wp_customize->add_setting('topmv_more_button_caption', array(
      'default' => __('More' , THEME_NAME),
      'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topmv_more_button_caption',
        array(
          'label'    => __('【フロントページ】Moreボタン "More"を変更', THEME_NAME),
          'description' => __('"More"を別のテキストに変更できます。', THEME_NAME).'<br>'.__('Cocoon設定>インデックス>タブ一覧又はカテゴリーごとを選択した場合に表示されます。', THEME_NAME).'<br>
          '.__('※空欄の場合は"More"が表示されます。', THEME_NAME),
          'section'  => 'topcontents_section',
          'settings' => 'topmv_more_button_caption',
          'type'     => 'text',
        )
      )
    );

    // フロントページ・インデックスページのNextボタン テキストを書き換え Cocoon設定>インデックス>デフォルト、タブ一覧
    $wp_customize->add_setting('topindex_next_button_caption', array(
      'default' => __('Next' , THEME_NAME),
      'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'topindex_next_button_caption',
        array(
          'label'    => __('【フロントページ・各一覧ページ】Nextボタン "Next"を変更', THEME_NAME),
          'description' => __('"Next"を別のテキストに変更できます。', THEME_NAME).'<br>'.__('※空欄の場合は"Next"が表示されます。', THEME_NAME),
          'section'  => 'topcontents_section',
          'settings' => 'topindex_next_button_caption',
          'type'     => 'text',
        )
      )
    );

    // パンくずリストのHomeテキスト変更
    $wp_customize->add_setting('breadcrumb_caption', array(
      'default' => __('Home' , THEME_NAME),
      'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'breadcrumb_caption',
        array(
          'label'    => __('【投稿・固定ページ】パンくずリスト "Home"を変更', THEME_NAME),
          'description' => __('"Home"を別のテキストに変更できます。', THEME_NAME).'<br>'.__('※空欄の場合は"Home"が表示されます。', THEME_NAME),
          'section'  => 'topcontents_section',
          'settings' => 'breadcrumb_caption',
          'type'     => 'text',
        )
      )
    );
  }
endif;
add_action('customize_register', 'skin_grayish_top_contents_customize');

// New Postを好きなテキストに変更できる
add_action('wp_head', 'skin_grayish_top_newpost_text');
if (!function_exists('skin_grayish_top_newpost_text')) :
  function skin_grayish_top_newpost_text()
  {
    if (get_theme_mod('topmv_newpost_text', __('New Post' , THEME_NAME))) {
    ?>
      <style type="text/css">
        .skin-grayish .list-new-entries h1::before {
          content: <?php echo '"' . esc_attr(get_theme_mod('topmv_newpost_text', __('New Post' , THEME_NAME))) . '"' ?>;
        }
      </style>
    <?php
    }
  }

endif;

// "最新の記事"を好きなテキストに変更できる
add_filter('new_entries_caption', 'customize_new_entries_caption');

if (!function_exists('customize_new_entries_caption')) :
  function customize_new_entries_caption()
  {
    if (get_theme_mod('topmv_newpost_heading_text', __('最新の記事', THEME_NAME))) {
      return esc_attr(get_theme_mod('topmv_newpost_heading_text', __('最新の記事', THEME_NAME)));
    } else {
      return __('最新の記事', THEME_NAME);
    }
  }

endif;

// Categoryを好きなテキストに変更できる
add_action('wp_head', 'skin_grayish_top_category_text');
if (!function_exists('skin_grayish_top_category_text')) :
  function skin_grayish_top_category_text()
  {
    if (get_theme_mod('topmv_category_text', 'Category')) {
    ?>
      <style type="text/css">
        .skin-grayish.front-top-page .front-page-type-category .list-columns .list-column:first-child h1::before,
        .skin-grayish.front-top-page :where(.front-page-type-category-3-columns, .front-page-type-category-2-columns) .list-columns::after {
          content: <?php echo '"' . esc_attr(get_theme_mod('topmv_category_text', 'Category')) . '"' ?>;
        }

        @media screen and (max-width: 1023px) {
          .skin-grayish.front-top-page :where(.front-page-type-category-3-columns) .list-columns .list-column:first-child h1::before {
            content: <?php echo '"' . esc_attr(get_theme_mod('topmv_category_text', 'Category')) . '"' ?>;
          }
        }

        @media screen and (max-width: 834px) {
          .skin-grayish.front-top-page :where(.front-page-type-category-2-columns) .list-columns .list-column:first-child h1::before {
            content: <?php echo '"' . esc_attr(get_theme_mod('topmv_category_text', 'Category')) . '"' ?>;
          }
        }
      </style>
    <?php
    }
  }

endif;


// フロントページのMoreボタン テキストをMore以外に書き換え
add_filter('more_button_caption', 'customize_more_button_caption');
if (!function_exists('customize_more_button_caption')) :
  function customize_more_button_caption()
  {
    if (get_theme_mod('topmv_more_button_caption', __('More' , THEME_NAME))) {
      return esc_attr(get_theme_mod('topmv_more_button_caption', __('More' , THEME_NAME)));
    } else {
      return __('More' , THEME_NAME);
    }
  }
endif;

// フロントページ・インデックスページのNextボタン
add_filter('pagination_next_link_caption', 'skin_grayish_nextbutton_text');
if (!function_exists('skin_grayish_nextbutton_text')) :
  function skin_grayish_nextbutton_text()
  {
    if (get_theme_mod('topindex_next_button_caption', __('Next' , THEME_NAME))) {
      return esc_attr(get_theme_mod('topindex_next_button_caption', __('Next' , THEME_NAME)));
    } else {
      return __('Next' , THEME_NAME);
    }
  }
endif;

// -----------------------------------------------------------------------------
// テーマカスタマイザー　フロントページ以外のヘッダー設定：
// ロゴの高さ・ヘッダーの背景色
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_ohter_header_customize')) :
  function skin_grayish_ohter_header_customize($wp_customize)
  {
    $wp_customize->add_section(
      'other_section',
      array(
        'title' => __('フロントページ以外の設定:ヘッダー', THEME_NAME),
        'panel' => 'font_pat_panel',
        'priority' => 5000,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/manual-cstm-pcheader/" target="_blank">'.__('グローバルナビの高さ等変更についてはこちらを参照', THEME_NAME).'</a><br><br>',
      )
    );
    //ロゴの高さ
    $wp_customize->add_setting('other_logosize', array(
      'default' => '56',
      'sanitize_callback' => 'skin_grayish_sanitize_number_range',
    ));
    $wp_customize->add_control(new WP_Customize_Control(
      $wp_customize,
      'other_logosize',
      array(
        'label' => __('【PC】フロントページ以外のグローバルナビの高さ', THEME_NAME),
        'description' => __('56px〜100pxの範囲で変更可能です。', THEME_NAME).__('ロゴ画像を使用の場合はロゴ画像の大きさを調整可能です。', THEME_NAME).'
        <br>'.__('※空欄の場合や、範囲外の値の場合は56pxになります。', THEME_NAME),
        'section' => 'other_section',
        'settings' => 'other_logosize',
        'type' => 'number',
        'input_attrs' => array(
          'step' => '1',
          'min' => '56',
          'max' => '100',
        ),
      )
    ));

    //フロントページ以外のヘッダーの背景色　--LtGray_T70_A50
    $wp_customize->add_setting('ohter_headerbg_color', array(
      'default' => '#E4E4E3',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control(
      $wp_customize,
      'ohter_headerbg_color',
      array(
        'label' => __('【PC】フロントページ以外のヘッダーの背景色', THEME_NAME),
        'description' => __('ヘッダーの背景色を変更。', THEME_NAME).'
        <br>'.__('※空欄の場合はデフォルト値になります。', THEME_NAME),
        'section' => 'other_section',
        'settings' => 'ohter_headerbg_color',
      )
    ));

    //フロントページ以外のヘッダーの背景色 透明度の設定　--LtGray_T70_A50
    $wp_customize->add_setting('ohter_headerbg_alpha', array(
      'default' => 'ohter_headerbg_alpha_5',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(new WP_Customize_Control(
      $wp_customize,
      'ohter_headerbg_alpha',
      array(
        'label' => __('【PC】ヘッダー背景色の不透明度調整', THEME_NAME),
        'description' => __('値が大きくなるほど背景色が濃くなります。', THEME_NAME),
        'section' => 'other_section',
        'settings' => 'ohter_headerbg_alpha',
        'type' => 'select',
        'choices' => array(
          'ohter_headerbg_alpha_0' => '0'.__('（背景色なし)', THEME_NAME),
          'ohter_headerbg_alpha_1' => '0.1',
          'ohter_headerbg_alpha_2' => '0.2',
          'ohter_headerbg_alpha_3' => '0.3',
          'ohter_headerbg_alpha_4' => '0.4',
          'ohter_headerbg_alpha_5' => '0.5'.__('（デフォルト）', THEME_NAME),
          'ohter_headerbg_alpha_6' => '0.6',
          'ohter_headerbg_alpha_7' => '0.7',
          'ohter_headerbg_alpha_8' => '0.8',
          'ohter_headerbg_alpha_9' => '0.9',
          'ohter_headerbg_alpha_10' => '1.0'.__('(不透明)', THEME_NAME),
        ),
      )
    ));

    // フロントページ以外のヘッダーロゴを別にしたいとき　画像のアップロード
    $wp_customize->add_setting('otherpage_logo_image', array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(
      new WP_Customize_Image_Control(
        $wp_customize,
        'otherpage_logo_image',
        array(
          'section'     => 'other_section',  // 紐づけるセクションIDを指定
          'settings'    => 'otherpage_logo_image',  // 紐づける設定IDを指定
          'label'       => __('【PC】フロントページ以外のヘッダーロゴ画像アップロード' , THEME_NAME),
          'description' => __('ロゴをフロントページと別画像にしたいとき設定します。' , THEME_NAME)
        )
      )
    );
  }

endif;

add_action('customize_register', 'skin_grayish_ohter_header_customize');

// head内にCSSを追加
add_action('wp_head', 'skin_grayish_ohter_header_logosize');

if (!function_exists('skin_grayish_ohter_header_logosize')) : function skin_grayish_ohter_header_logosize()
  {
    if (get_theme_mod('other_logosize', '56')) {
    ?><style type="text/css">
        :root {
          --ohterHeaderLogosize: <?php echo esc_attr(get_theme_mod('other_logosize', '56')) . 'px' ?>;
        }
      </style>
    <?php

    }
  }
endif;
// フロントページ以外のヘッダーの背景色と不透明度
add_action('wp_head', 'skin_grayish_ohter_headerbg');
if (!function_exists('skin_grayish_ohter_headerbg')) :
  function skin_grayish_ohter_headerbg()
  {
    if (get_theme_mod('ohter_headerbg_color', '#E4E4E3')) {
      $hex_navi_bgcolor = esc_attr(get_theme_mod('ohter_headerbg_color', '#E4E4E3'));
    } else {
      $hex_navi_bgcolor = '#E4E4E3';
    }

    $style_opacity = '';
    if (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_5') {
      $style_opacity = '0.5';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_0') {
      $style_opacity = '0';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_1') {
      $style_opacity = '0.1';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_2') {
      $style_opacity = '0.2';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_3') {
      $style_opacity = '0.3';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_4') {
      $style_opacity = '0.4';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_6') {
      $style_opacity = '0.6';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_7') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_8') {
      $style_opacity = '0.8';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_9') {
      $style_opacity = '0.9';
    } elseif (get_theme_mod('ohter_headerbg_alpha', 'ohter_headerbg_alpha_5') === 'ohter_headerbg_alpha_10') {
      $style_opacity = '1.0';
    } else {
      $style_opacity = '0.5';
    }
    // hex to dec
    $style_rgba = '';
    $style_rgba = color_code($hex_navi_bgcolor, $style_opacity);

    ?>
    <style type="text/css">
      :root {
        --ohterHeaderBgColor: <?php echo $style_rgba ?>;
      }
    </style>
    <?php

  }
endif;

// パンくずリストのHomeテキスト変更
add_filter('breadcrumbs_single_root_text', 'customize_breadcrumbs_single_root_text');
add_filter('breadcrumbs_page_root_text', 'customize_breadcrumbs_single_root_text');

if (!function_exists('customize_breadcrumbs_single_root_text')) :
  function customize_breadcrumbs_single_root_text()
  {
    if (get_theme_mod('breadcrumb_caption', __('Home' , THEME_NAME))) {
      return esc_attr(get_theme_mod('breadcrumb_caption', __('Home' , THEME_NAME)));
    } else {
      return __('Home' , THEME_NAME);
    }
  }
endif;


// -----------------------------------------------------------------------------
// テーマカスタマイザー　コンテンツ下部ウィジェットの設定：
// プロフィールボックスの背景画像とオーバーレイ調整を設定可能
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_profbox_customize')) :
  function skin_grayish_profbox_customize($wp_customize)
  {
    $wp_customize->add_section(
      'under_contents_section',
      array(
        'title' => __('コンテンツ下部ウィジェット：プロフィールボックス' , THEME_NAME),
        'panel' => 'font_pat_panel',
        'priority' => 7500,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/manual-profbox/" target="_blank">'.__('プロフィールボックスの設定についてはこちらを参照' , THEME_NAME).'</a>',
      )
    );

    //プロフィールボックスの白オーバーレイ 透明度の設定　
    $wp_customize->add_setting('undercon_profbg_overlay_alpha', array(
      'default' => 'undercon_profbg_overlay_alpha_5',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(new WP_Customize_Control(
      $wp_customize,
      'undercon_profbg_overlay_alpha',
      array(
        'label' => __('プロフィールボックスの背景画像の白オーバーレイ不透明度調整' , THEME_NAME),
        'description' => __('値が大きくなるほど白色が濃くなります。' , THEME_NAME),
        'section' => 'under_contents_section',
        'settings' => 'undercon_profbg_overlay_alpha',
        'type' => 'select',
        'choices' => array(
          'undercon_profbg_overlay_alpha_0' => '0'.__('（白色なし）' , THEME_NAME),
          'undercon_profbg_overlay_alpha_1' => '0.1',
          'undercon_profbg_overlay_alpha_2' => '0.2',
          'undercon_profbg_overlay_alpha_3' => '0.3',
          'undercon_profbg_overlay_alpha_4' => '0.4',
          'undercon_profbg_overlay_alpha_5' => '0.5'.__('（デフォルト）', THEME_NAME),
          'undercon_profbg_overlay_alpha_6' => '0.6',
          'undercon_profbg_overlay_alpha_7' => '0.7',
          'undercon_profbg_overlay_alpha_8' => '0.8',
          'undercon_profbg_overlay_alpha_9' => '0.9',
        ),
      )
    ));

    // 画像のアップロード
    $wp_customize->add_setting(
      'undercon_profbg_image',
      // array(
      //   'default'           => get_theme_mod('undercon_profbg_image'), // これを入れないとアップロードした画像のプレビューが消えてしまう
      //   // 'priority'          => 1000,
      //   'sanitize_callback' => 'esc_url_raw'
      // )
    );
    $wp_customize->add_control(
      new WP_Customize_Image_Control(
        $wp_customize,
        'undercon_profbg_image',
        array(
          'section'     => 'under_contents_section',  // 紐づけるセクションIDを指定
          'settings'    => 'undercon_profbg_image',  // 紐づける設定IDを指定
          'label'       => __('画像アップロード' , THEME_NAME),
          'description' => __('プロフィールボックスの背景に設定したい画像を選択' , THEME_NAME)
        )
      )
    );
  }
endif;
add_action('customize_register', 'skin_grayish_profbox_customize');

// プロフィールボックス背景 白オーバーレイ
add_action('wp_head', 'skin_grayish_profboxbg_whovlay');
if (!function_exists('skin_grayish_profboxbg_whovlay')) :
  function skin_grayish_profboxbg_whovlay()
  {
    $style_template = '
		<style>
    .skin-grayish .content-bottom .widget_author_box::after {
          opacity: %s;
			}
		</style>
	';
    $style_opacity = '';
    if (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_5') {
      $style_opacity = '0.5';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_0') {
      $style_opacity = '0';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_1') {
      $style_opacity = '0.1';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_2') {
      $style_opacity = '0.2';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_3') {
      $style_opacity = '0.3';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_4') {
      $style_opacity = '0.4';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_6') {
      $style_opacity = '0.6';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_7') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_8') {
      $style_opacity = '0.8';
    } elseif (get_theme_mod('undercon_profbg_overlay_alpha', 'undercon_profbg_overlay_alpha_5') === 'undercon_profbg_overlay_alpha_9') {
      $style_opacity = '0.9';
    } else {
      $style_opacity = '0.5';
    }

    echo sprintf($style_template, $style_opacity);
  }

endif;

// プロフィールボックスの背景画像を設定
add_action('wp_head', 'skin_grayish_profboxbg_img');
if (!function_exists('skin_grayish_profboxbg_img')) :
  function skin_grayish_profboxbg_img()
  {
    if (get_theme_mod('undercon_profbg_image')) {
    ?>
      <style type="text/css">
        .skin-grayish .content-bottom .widget_author_box::before {
          background-image: <?php echo 'url(' . esc_url(get_theme_mod('undercon_profbg_image')) . ')' ?>;
        }
      </style>
    <?php
    }
  }


endif;

// -----------------------------------------------------------------------------
// テーマカスタマイザー　モバイル(画面幅1023px以下）：
// ヘッダーモバイルボタンの背景色・フロントページをメニューアイコンのみにする設定・
// メニューのアイコン下のキャプションのOn/Off・
// -----------------------------------------------------------------------------
if (!function_exists('skin_grayish_mobile_customize')) :
  function skin_grayish_mobile_customize($wp_customize)
  {
    $wp_customize->add_section(
      'mobile_section',
      array(
        'title'    => __('モバイル設定' , THEME_NAME),
        'panel'    => 'font_pat_panel',
        'priority' => 8000,
        'description' => '<a href="https://cocoon-grayish.na2-factory.com/manual-sp-menu/" target="_blank">'.__('ヘッダー・フッターモバイルボタンの設定についてはこちらを参照' , THEME_NAME).'</a>',
      )
    );
    // ヘッダーモバイルボタンの背景色設定
    $wp_customize->add_setting('mobile_headerbg_color', array(
      'default' => '#FFFFFF',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'mobile_headerbg_color',
        array(
          'label'    => __('ヘッダーモバイルボタンの背景色' , THEME_NAME),
          'description' => __('ヘッダーモバイルボタンの背景色を変更。' , THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。' , THEME_NAME).'
          <br>'.__('※「フロントページのみヘッダーモバイルボタンをメニューアイコンのみON」に設定している場合は、フロントページのヘッダーモバイルボタンの背景色は無効になります。' , THEME_NAME),
          'section'  => 'mobile_section',
          'settings' => 'mobile_headerbg_color',
        )
      )
    );

    //ヘッダーモバイルボタンの背景色 透明度の設定　
    $wp_customize->add_setting('mobile_headerbg_alpha', array(
      'default' => 'mobile_headerbg_alpha_5',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'mobile_headerbg_alpha',
        array(
          'label'    => __('ヘッダーモバイルボタン背景色の不透明度調整' , THEME_NAME),
          'description' => __('値が大きくなるほど背景色が濃くなります。' , THEME_NAME).'
          <br>'.__('※「フロントページのみヘッダーモバイルボタンをメニューアイコンのみON」に設定している場合は、フロントページのヘッダーモバイルボタンの背景色の不透明度調整は無効になります。' , THEME_NAME),
          'section'  => 'mobile_section',
          'settings' => 'mobile_headerbg_alpha',
          'type'     => 'select',
          'choices'  => array(
            'mobile_headerbg_alpha_0' => '0'.__('（背景色なし)', THEME_NAME),
            'mobile_headerbg_alpha_1' => '0.1',
            'mobile_headerbg_alpha_2' => '0.2',
            'mobile_headerbg_alpha_3' => '0.3',
            'mobile_headerbg_alpha_4' => '0.4',
            'mobile_headerbg_alpha_5' => '0.5'.__('（デフォルト）', THEME_NAME),
            'mobile_headerbg_alpha_6' => '0.6',
            'mobile_headerbg_alpha_7' => '0.7',
            'mobile_headerbg_alpha_8' => '0.8',
            'mobile_headerbg_alpha_9' => '0.9',
            'mobile_headerbg_alpha_10' => '1.0'.__('(不透明)', THEME_NAME),
          ),
        )
      )
    );

    //フロントページ　メニューアイコンのみOn/Off
    $wp_customize->add_setting('mobile_topheader_menu', array(
      'default' => 'top_menuonly_On',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'mobile_topheader_menu',
        array(
          'label'    => __('フロントページのみヘッダーモバイルボタンをメニューアイコンのみON/OFF' , THEME_NAME),
          'description' => __('フロントページ以外は設定したメニューを表示します。' , THEME_NAME).'
          <br>'.__('※ヘッダーモバイルボタンにオリジナルメニューを作成する場合は、メニューの一番上に#menuを設定してください。' , THEME_NAME),
          'section'  => 'mobile_section',
          'settings' => 'mobile_topheader_menu',
          'type'     => 'radio',
          'choices'  => array(
            'top_menuonly_On' => __('ON' , THEME_NAME).__('（デフォルト）' , THEME_NAME),
            'top_menuonly_Off' => __('OFF' , THEME_NAME),
          ),
        )
      )
    );

    //メニューのアイコン下のキャプションのOn/Off
    $wp_customize->add_setting('mobile_caption_radio', array(
      'default' => 'menu_caption_On',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'mobile_caption_radio',
        array(
          'label'    => __('ヘッダーモバイルボタン＆フッターモバイルボタンのアイコン下のキャプションのON/OFF' , THEME_NAME),
          'description' => __('モバイル時にアイコン下にキャプションを表示' , THEME_NAME),
          'section'  => 'mobile_section',
          'settings' => 'mobile_caption_radio',
          'type'     => 'radio',
          'choices'  => array(
            'menu_caption_On' => __('ON' , THEME_NAME).__('（デフォルト）' , THEME_NAME),
            'menu_caption_Off' => __('OFF' , THEME_NAME),
          ),
        )
      )
    );

    // フッターモバイルボタンの背景色設定
    $wp_customize->add_setting('mobile_footerbg_color', array(
      'default' => '#FFFFFF',
      'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(
      new WP_Customize_Color_Control(
        $wp_customize,
        'mobile_footerbg_color',
        array(
          'label'    => __('フッターモバイルボタンの背景色' , THEME_NAME),
          'description' => __('フッターモバイルボタンの背景色を変更。' , THEME_NAME).'
          <br>'.__('※空欄の場合はデフォルト値になります。' , THEME_NAME),
          'section'  => 'mobile_section',
          'settings' => 'mobile_footerbg_color',
        )
      )
    );

    //フッターモバイルボタンの背景色 透明度の設定　
    $wp_customize->add_setting('mobile_footerbg_alpha', array(
      'default' => 'mobile_footerbg_alpha_5',
      'sanitize_callback' => 'skin_grayish_sanitize_select',
    ));
    $wp_customize->add_control(
      new WP_Customize_Control(
        $wp_customize,
        'mobile_footerbg_alpha',
        array(
          'label'    => __('フッターモバイルボタン背景色の不透明度調整', THEME_NAME),
          'description' => __('値が大きくなるほど背景色が濃くなります。', THEME_NAME),
          'section'  => 'mobile_section',
          'settings' => 'mobile_footerbg_alpha',
          'type'     => 'select',
          'choices'  => array(
            'mobile_footerbg_alpha_0' => '0'.__('（背景色なし)', THEME_NAME),
            'mobile_footerbg_alpha_1' => '0.1',
            'mobile_footerbg_alpha_2' => '0.2',
            'mobile_footerbg_alpha_3' => '0.3',
            'mobile_footerbg_alpha_4' => '0.4',
            'mobile_footerbg_alpha_5' => '0.5'.__('（デフォルト）', THEME_NAME),
            'mobile_footerbg_alpha_6' => '0.6',
            'mobile_footerbg_alpha_7' => '0.7',
            'mobile_footerbg_alpha_8' => '0.8',
            'mobile_footerbg_alpha_9' => '0.9',
            'mobile_footerbg_alpha_10' => '1.0'.__('(不透明)', THEME_NAME),
          ),
        )
      )
    );

    // モバイル時：ヘッダーのロゴを別ファイルにしたいとき　画像のアップロード
    $wp_customize->add_setting('mobile_header_logo_image', array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(
      new WP_Customize_Image_Control(
        $wp_customize,
        'mobile_header_logo_image',
        array(
          'section'     => 'mobile_section',  // 紐づけるセクションIDを指定
          'settings'    => 'mobile_header_logo_image',  // 紐づける設定IDを指定
          'label'       => __('モバイル時のヘッダーロゴ画像アップロード', THEME_NAME),
          'description' => __('モバイル時のロゴを別画像にしたいとき設定します。', THEME_NAME)
        )
      )
    );
  }
endif;
add_action('customize_register', 'skin_grayish_mobile_customize');

// head内にCSSを追加
// モバイルヘッダーの背景色と不透明度
add_action('wp_head', 'skin_grayish_mobile_headerbg');
if (!function_exists('skin_grayish_mobile_headerbg')) :
  function skin_grayish_mobile_headerbg()
  {
    if (get_theme_mod('mobile_headerbg_color', '#FFFFFF')) {
      $hex_navi_bgcolor = esc_attr(get_theme_mod('mobile_headerbg_color', '#FFFFFF'));
    } else {
      $hex_navi_bgcolor = '#FFFFFF';
    }

    $style_opacity = '';
    if (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_5') {
      $style_opacity = '0.5';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_0') {
      $style_opacity = '0';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_1') {
      $style_opacity = '0.1';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_2') {
      $style_opacity = '0.2';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_3') {
      $style_opacity = '0.3';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_4') {
      $style_opacity = '0.4';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_6') {
      $style_opacity = '0.6';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_7') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_8') {
      $style_opacity = '0.8';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_9') {
      $style_opacity = '0.9';
    } elseif (get_theme_mod('mobile_headerbg_alpha', 'mobile_headerbg_alpha_5') === 'mobile_headerbg_alpha_10') {
      $style_opacity = '1.0';
    } else {
      $style_opacity = '0.5';
    }

    // hex to dec
    $style_rgba = '';
    $style_rgba = color_code($hex_navi_bgcolor, $style_opacity);

    // Top(Front)をMenuアイコンのみにするとき、Topは背景透明にする
    $style_top_opacity = '';
    if (get_theme_mod('mobile_topheader_menu', 'top_menuonly_On') === 'top_menuonly_On') {
      $style_top_opacity = '0';
    } else {
      $style_top_opacity = $style_opacity;
    }
    $style_top_rgba = '';
    $style_top_rgba = color_code($hex_navi_bgcolor, $style_top_opacity);


    ?>
    <style type="text/css">
      :root {
        --mobileHeaderBgColor: <?php echo $style_rgba ?>;
        --mobileTopHeaderBgColor: <?php echo $style_top_rgba ?>;
      }
    </style>
  <?php

  }
endif;

//フロントページ　メニューアイコンのみOn/Off
add_action('wp_head', 'skin_grayish_mobile_topmenu');
if (!function_exists('skin_grayish_mobile_topmenu')) :
  function skin_grayish_mobile_topmenu()
  {
    $style_template = "
    <script>
    let mobileHeaderMenuChild_remove_flg = 'on';
    </script>
	";
    $style_template_off = "
    <script>
    let mobileHeaderMenuChild_remove_flg = 'off';
    </script>
	";

    if (get_theme_mod('mobile_topheader_menu', 'top_menuonly_On') === 'top_menuonly_On') {
      echo sprintf($style_template);
    } elseif (get_theme_mod('mobile_topheader_menu', 'top_menuonly_On') === 'top_menuonly_Off') {
      echo sprintf($style_template_off);
    }
  }
endif;



// モバイルメニューキャプション
add_action('wp_head', 'skin_grayish_mobile_caption');
if (!function_exists('skin_grayish_mobile_caption')) :
  function skin_grayish_mobile_caption()
  {
    $style_template = '
		<style>
    @media screen and (max-width: 1023px) {
      .skin-grayish .mobile-menu-buttons .menu-caption {
    display: %s;
  }
}
		</style>
	';
    $style_opacity = '';
    if (get_theme_mod('mobile_caption_radio', 'menu_caption_On') === 'menu_caption_On') {
      $style_opacity = 'block';
    } elseif (get_theme_mod('mobile_caption_radio', 'menu_caption_On') === 'menu_caption_Off') {
      $style_opacity = 'none';
    }

    echo sprintf($style_template, $style_opacity);
  }
endif;

// フッターモバイルボタンの背景色と不透明度
add_action('wp_head', 'skin_grayish_mobile_footerbg');
if (!function_exists('skin_grayish_mobile_footerbg')) :
  function skin_grayish_mobile_footerbg()
  {
    if (get_theme_mod('mobile_footerbg_color', '#FFFFFF')) {
      $hex_navi_bgcolor = esc_attr(get_theme_mod('mobile_footerbg_color', '#FFFFFF'));
    } else {
      $hex_navi_bgcolor = '#FFFFFF';
    }

    $style_opacity = '';
    if (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_5') {
      $style_opacity = '0.5';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_0') {
      $style_opacity = '0';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_1') {
      $style_opacity = '0.1';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_2') {
      $style_opacity = '0.2';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_3') {
      $style_opacity = '0.3';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_4') {
      $style_opacity = '0.4';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_6') {
      $style_opacity = '0.6';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_7') {
      $style_opacity = '0.7';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_8') {
      $style_opacity = '0.8';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_9') {
      $style_opacity = '0.9';
    } elseif (get_theme_mod('mobile_footerbg_alpha', 'mobile_footerbg_alpha_5') === 'mobile_footerbg_alpha_10') {
      $style_opacity = '1.0';
    } else {
      $style_opacity = '0.5';
    }
    // hex to dec
    $style_rgba = '';
    $style_rgba = color_code($hex_navi_bgcolor, $style_opacity);

  ?>
    <style type="text/css">
      :root {
        --mobileFooterBgColor: <?php echo $style_rgba ?>;
      }
    </style>
    <?php

  }
endif;


// -----------------------------------------------------------------------------
// スキン独自機能
// Front 画面一杯のメインビジュアル下にスクロール促しアニメーション
// -----------------------------------------------------------------------------
add_action('wp_header_logo_after_open', 'customize_topmv_scroll');
if (!function_exists('customize_topmv_scroll')) :
  function customize_topmv_scroll()
  {
    echo '
    <div class="skinadd-topmv-scroll"><span class="skinadd-topmv-scroll__txt">Scroll</span>
    </div>';
  }
endif;


// -----------------------------------------------------------------------------
// ブロックエディター編集画面にもスタイルを当てる：cocoon-gutenbergで親テーマのあとに読み込み
// -----------------------------------------------------------------------------
add_theme_support('editor-styles');
add_action('enqueue_block_editor_assets', 'skin_block_editor_style_setup');
if (!function_exists('skin_block_editor_style_setup')) :
  function skin_block_editor_style_setup()
  {
    $cocoon_gutenberg = THEME_NAME . '-gutenberg';
    $editor_style_url = get_theme_file_uri('/skins/skin-grayish-topfull/editor-style.css');
    wp_enqueue_style('block-editor-style', $editor_style_url, array($cocoon_gutenberg));
  }
endif;

// -----------------------------------------------------------------------------
// ブロックエディター:カラーパレットにスキン独自色追加 ※slugに数字が混ざるとプレビューできない注意
// -----------------------------------------------------------------------------
if (!function_exists('skingrayish_block_editor_color_palette_colors')) :
  function skingrayish_block_editor_color_palette_colors($colors)
  {
    $add_colors = array(
      array('name' => __('サイトテキストグレー', THEME_NAME), 'slug' => 'sd-gray', 'color' => '#535252'),
      array('name' => __('ライトグレーT0', THEME_NAME), 'slug' => 'lt-gray', 'color' => '#a5a4a3'),
      array('name' => __('ライトブルーT0', THEME_NAME), 'slug' => 'lt-blue', 'color' => '#aac2d2'),
      array('name' => __('ライトブルーT70', THEME_NAME), 'slug' => 'lt-seven-blue', 'color' => '#e6edf2'),
      array('name' => __('ライトブルーT90', THEME_NAME), 'slug' => 'lt-nine-blue', 'color' => '#f7f9fb'),
      array('name' => __('シェイドブルーS30', THEME_NAME), 'slug' => 'sdblue-thirty', 'color' => '#778893'),
      array('name' => __('シェイドブルーS50', THEME_NAME), 'slug' => 'sdblue-fifty', 'color' => '#556169'),
      array('name' => __('PinkT0', THEME_NAME), 'slug' => 'lt-pink', 'color' => '#debfc2'),
      array('name' => __('PinkT50', THEME_NAME), 'slug' => 'lt-pink-fifty', 'color' => '#efdfe1'),
      array('name' => __('PinkT90', THEME_NAME), 'slug' => 'ltpink-nine', 'color' => '#fcf9f9'),
      array('name' => __('PinkS50', THEME_NAME), 'slug' => 'sd-pink-fifty', 'color' => '#6f6061'),
      array('name' => __('RedT0', THEME_NAME), 'slug' => 'lt-red', 'color' => '#d95959'),
      array('name' => __('RedT50', THEME_NAME), 'slug' => 'lt-red-fifty', 'color' => '#ecacac'),
      array('name' => __('YellowT0', THEME_NAME), 'slug' => 'lt-yel', 'color' => '#dedbbf'),
      array('name' => __('YellowT50', THEME_NAME), 'slug' => 'lt-yel-fifty', 'color' => '#efeddf'),
      array('name' => __('YellowT90', THEME_NAME), 'slug' => 'lt-yel-nine', 'color' => '#fcfbf9'),
      array('name' => __('YellowS50', THEME_NAME), 'slug' => 'sd-yel-fifty', 'color' => '#6f6e60'),
      array('name' => __('BeigeT0', THEME_NAME), 'slug' => 'lt-beig', 'color' => '#e1d1c6'),
      array('name' => __('BeigeT50', THEME_NAME), 'slug' => 'lt-beig-fifty', 'color' => '#f0e8e3'),
      array('name' => __('BeigeT90', THEME_NAME), 'slug' => 'lt-beig-nine', 'color' => '#f6f1ee'),
      array('name' => __('BeigeS50', THEME_NAME), 'slug' => 'sd-beig-fifty', 'color' => '#716963'),
      array('name' => __('GreenT0', THEME_NAME), 'slug' => 'lt-green', 'color' => '#cddab9'),
      array('name' => __('GreenT50', THEME_NAME), 'slug' => 'lt-green-fifty', 'color' => '#e6eddc'),
      array('name' => __('GreenT90', THEME_NAME), 'slug' => 'lt-green-nine', 'color' => '#fafbf8'),
      array('name' => __('GreenS30', THEME_NAME), 'slug' => 'sd-green-thirty', 'color' => '#909982'),
      array('name' => __('GreenS50', THEME_NAME), 'slug' => 'sd-green-fifty', 'color' => '#676d5d'),
    );
    $colors = array_merge($colors, $add_colors);
    return $colors;
  }
  add_filter('block_editor_color_palette_colors', 'skingrayish_block_editor_color_palette_colors');
endif;

// -----------------------------------------------------------------------------
// 管理画面　Cocoon設定のプレビューの表示
// -----------------------------------------------------------------------------
// define("SKINDIR", get_stylesheet_directory_uri());
add_action('admin_print_styles', 'admin_print_styles_skinadd');
if (!function_exists('admin_print_styles_skinadd')) :
  function admin_print_styles_skinadd()
  {
    $skin_admin_js = get_theme_file_uri('/skins/skin-grayish-topfull/skin_admin_javascript.js');
    wp_enqueue_script('admin-skin-javascript', $skin_admin_js, array('admin-javascript'), false, true);
  }
endif;

// -----------------------------------------------------------------------------
// 関連記事用 ScrollHint
// -----------------------------------------------------------------------------
add_action('wp_enqueue_scripts', 'wp_enqueue_scrollhint_skinadd', 99);

if (!function_exists('wp_enqueue_scrollhint_skinadd')) :
  function wp_enqueue_scrollhint_skinadd()
  {
    // if (is_responsive_table_enable() && (is_singular() || (is_category() && !is_paged()) || (is_tag() && !is_paged()))) {
    if (is_single() && is_related_entries_visible()) {
      // レスポンシブtable がoffの場合、ここでlib呼び出し
      if (!is_responsive_table_enable()) {
        //ScrollHintスタイルの呼び出し
        wp_enqueue_style('scrollhint-style', get_template_directory_uri() . '/plugins/scroll-hint-master/css/scroll-hint.css');
        //ScrollHintスクリプトの呼び出し
        wp_enqueue_script('scrollhint-js', get_template_directory_uri() . '/plugins/scroll-hint-master/js/scroll-hint.min.js', array('jquery'), false, true);
      }
      $data = '
          (function($){
            new ScrollHint(".skin-grayish .under-entry-content .related-list", {
              suggestiveShadow: false,
              i18n: {
                scrollable: "' . __('横スクロールできます', THEME_NAME) . '"
              }
            });
          })(jQuery);
        ';
      wp_add_inline_script('scrollhint-js', $data, 'after');
    }
  }
endif;

// add for プロフィールBox move profile-follows
add_filter('code_minify_call_back', function ($html) {

  // 説明文のエリアにSNSボタンを移動
  $pattern = '/<div class="author-description">(.*?)<\/div>\s*<div class="profile-follows author-follows">(.*?)<\/div>/s';
  $replacement = '<div class="author-description">$1<div class="profile-follows author-follows">$2</div></div>';

  return preg_replace($pattern, $replacement, $html);
});


// add for プロフィールBox move author-widget-name
add_filter('code_minify_call_back', function ($html) {

  $pattern_aft = '/<div class="author-box border-element no-icon cf">\s*<div class="author-widget-name">(.*?)<\/div>\s*<figure(.*?)<\/figure>\s*<div class="author-content">\s*<div class="author-name">\s*<a(.*?)<\/a>\s*<\/div>\s*<div class="author-description">(.*?)<\/div>\s*<\/div>\s*<\/div>/s';
  $replacement_aft = '<div class="author-box border-element no-icon cf"><figure$2</figure><div class="author-content"><div class="author-name"><a$3</a><div class="author-widget-name">$1</div></div><div class="author-description">$4</div></div></div>';

  return preg_replace($pattern_aft, $replacement_aft, $html);
});

// front page header
add_filter("cocoon_part__tmp/header-container", function ($content) {
  if (is_front_top_page()) {
    $pattern_aft = '/<header id="header" class="header(.*?)>\s*<div id="header-in" class="header-in wrap cf"(.*?)>\s*<\/div>\s*<\/header>/s';
    $replacement_aft = '<header id="header" class="header$1><div class="grayish_topmv_whovlay"></div><div class="grayish_topmv_dot"></div><div id="header-in" class="header-in wrap cf"$2></div></header>';

    return preg_replace($pattern_aft, $replacement_aft, $content);
  } else {

    return $content;
  }
});

// mobile-header-menu-buttons
add_action(
  "cocoon_part_after__tmp/mobile-header-menu-buttons",
  "mobile_header_buttons_set"
);

if (!function_exists('mobile_header_buttons_set')) :
  function mobile_header_buttons_set()
  {
    if (is_front_top_page()) {

      // frontのみ　JSを実行
    ?>
      <script>
        const mobileHeaderMenuChild_top = document.querySelectorAll('.skin-grayish.front-top-page .mobile-header-menu-buttons > li:not(:first-child)');
        if (mobileHeaderMenuChild_remove_flg === 'on') {
          if (mobileHeaderMenuChild_top.length > 0) {
            mobileHeaderMenuChild_top.forEach(liItem => {
              liItem.remove();
            });
          }
        }
      </script>
<?php
    }
  }
endif;

// 親テーマの関数使用　画像の幅と高さを取得
if (!function_exists('get_image_dimensions')) :
  function get_image_dimensions($image_url)
  {
    $size = get_image_width_and_height($image_url);
    if (!$size) {
      return null;
    }
    $width_attr = null;
    $height_attr = null;
    $w = $size['width'];
    $h = $size['height'];
    if ($w && $h) {
      $width_attr = ' width="' . $w . '"';
      $height_attr = ' height="' . $h . '"';
    }
    return array('width' => $width_attr, 'height' => $height_attr);
  }
endif;
// PC：フロントページ以外は、ロゴを別ファイルにしたいとき
add_filter(
  'the_site_logo_tag',
  function ($all_tag, $is_header, $home_url, $site_logo_text) {
    if (!is_front_top_page() && $is_header) {
      if (get_theme_mod('otherpage_logo_image')) {
        $new_logo_url = get_theme_mod('otherpage_logo_image');
        // $new_logo_urlが存在するか確認->画像の幅と高さを取得し変更
        $dimensions = get_image_dimensions($new_logo_url);
        if ($dimensions !== null && $dimensions['width'] !== null && $dimensions['height'] !== null) {
          // $new_logo_urlが存在する
          $width_attr = $dimensions['width'];
          $height_attr = $dimensions['height'];

          $new_logo_tag = '<img src="' . esc_url($new_logo_url) . '" alt="' . esc_attr($site_logo_text) . '"' . $width_attr .  $height_attr . '>';
          $all_tag_pat = '/<(div|h1) class="logo logo-header logo-image"><a href="(.*?)" class="site-name site-name-text-link" itemprop="url"><span class="site-name-text">(.*?)<meta itemprop="name about" content="(.*?)"><\/span><\/a><\/(div|h1)>/s';
          $all_tag_replacement = function ($matches) use ($home_url, $new_logo_tag) {
            $tag = $matches[1];  // マッチしたタグ（divまたはh1）
            return '<' . $tag . ' class="logo logo-header logo-image"><a href="' . esc_url($home_url) . '" class="site-name site-name-text-link" itemprop="url"><span class="site-name-text">' . $new_logo_tag . '<meta itemprop="name about" content="' . $matches[4] . '"></span></a></' . $tag . '>';
          };

          $all_tag = preg_replace_callback($all_tag_pat, $all_tag_replacement, $all_tag);
          return $all_tag;
        } else {
          // $new_logo_urlが存在しない
          return $all_tag;
        }
      }
      return $all_tag;
    }
    return $all_tag;
  },
  10,
  6
);
// モバイル時：ヘッダーのロゴを別ファイルにしたいとき
add_filter("cocoon_part__tmp/mobile-logo-button", function ($content) {
  if (get_theme_mod('mobile_header_logo_image')) {
    $new_logo_url = get_theme_mod('mobile_header_logo_image');
    // $new_logo_urlが存在するか確認->画像の幅と高さを取得し変更
    $dimensions = get_image_dimensions($new_logo_url);
    if ($dimensions !== null && $dimensions['width'] !== null && $dimensions['height'] !== null) {
      // $new_logo_urlが存在する
      // for alt
      $site_logo_text = apply_filters('site_logo_text', get_bloginfo('name'));

      // 画像の幅と高さを取得
      $width_attr = $dimensions['width'];
      $height_attr = $dimensions['height'];

      $pattern_aft = '/<a href="(.*?)" class="menu-button-in">(.*?)<\/a>/s';
      $replacement_aft = '<a href="$1" class="menu-button-in"><img class="site-logo-image" src="' . esc_url($new_logo_url) . '" alt="' . esc_attr($site_logo_text) . '"' . $width_attr .  $height_attr . '></a>';
      return preg_replace($pattern_aft, $replacement_aft, $content);
    } else {
      // $new_logo_urlが存在しない
      return $content;
    }
  }
  return $content;
});
