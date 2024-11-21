<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
if ( !defined( 'ABSPATH' ) ) exit;

//編集用CSSの追加
add_theme_support( 'editor-styles' );
function org_theme_add_editor_styles() {
	$editor_style_url = get_theme_file_uri( '/skins/skin-tegakinote-green-orange/editor-style.css' );
	wp_enqueue_style( 'block-editor-style', $editor_style_url );
}
add_action( 'enqueue_block_editor_assets', 'org_theme_add_editor_styles' );

//スキン制御
global $_THEME_OPTIONS;
$_THEME_OPTIONS =
array(
	'site_background_image_url' => '', //サイト背景画像
);

//Cocoon設定 > 全体 > サイト背景色の適用箇所を変更
add_action('get_template_part_tmp/css-custom','css_custom');
function css_custom() {
	$content_color = get_site_background_color() ?: '#f0f0e8';
	echo '
	.container, .sidebar-menu-content, .navi-menu-content, .mobile-menu-buttons, #navi .navi-in > .menu-mobile li {
		background-color: '.$content_color.';
	}

	.single .main, .page .main, .error404 .main, .list {
		box-shadow: 0 0 0 10px '.$content_color.' inset;
	}

	.single .main:before, .page .main:before, .error404 .main:before, .list:before,
	.single .main:after, .page .main:after, .error404 .main:after, .list:after,
	.widget-sidebar::before, .appeal-content::before {
		border-top-color: '.$content_color.';
	}';
}

/*******************************
* WEBフォント設定
*******************************/
// Googleフォント読み込み（非同期化）
add_action('wp_head', 'adds_head');
function adds_head() {
    $font_family = get_theme_mod('font_pattern_control', 'font_klee');
    $font_url = generate_font_url($font_family);
    if ($font_family !== 'font_none') {
      echo '<link href="' . esc_url($font_url) . '" rel="preload" as="style">'."\n";
      echo '<link href="' . esc_url($font_url) . '" rel="stylesheet" media="print" onload="this.media=\'all\'">'."\n";
    }
}

// フォントのURLを生成する関数
function generate_font_url($font_family) {
    $font_families = array(
        'font_klee' =>'Klee+One:wght@600&display=swap',
        'font_kaisei_decol' => 'Kaisei+Decol:wght@700&display=swap',
        'font_zen_kusenaido' => 'Zen+Kurenaido&display=swap',
        'font_zen_kaku_gothic' => 'Zen+Kaku+Gothic+New:wght@500&display=swap',
        'font_zen_maru_gothic' => 'Zen+Maru+Gothic:wght@500&display=swap',
        'font_kiwi' => 'Kiwi+Maru:wght@500&display=swap',
        'font_none' => '', // 読み込むフォントがない場合は空文字にする
    );

    if (isset($font_families[$font_family]) && $font_family !== 'font_none') {
        return 'https://fonts.googleapis.com/css2?family=' . $font_families[$font_family];
    }

    return ''; // 該当するフォントが見つからない場合も空文字にする
}

/*******************************
* カスタマイザーで
* 日本語フォントを変更
*******************************/
//カスタマイザー設定
add_action('customize_register', 'font_pattern');
function font_pattern($wp_customize) {
	$wp_customize->add_section(
		'font_pattern_section',
		array(
			'title' => '【スキン】日本語フォント設定',
			'priority' => 1000,
		)
	);
	$wp_customize->add_setting(
		'font_pattern_control',
		array(
			'default' => 'font_klee'
		)
	);
	$wp_customize->add_control(
		'font_pattern_control',
		array(
			'label' => 'ロゴフォント設定',
			'description' => 'ロゴテキストや記事タイトルなどのロゴフォントを設定できます。「設定なし」にするとCocoon設定 > 全体設定 > サイトフォント の設定を継承します。',
			'setting' => 'font_pattern_control', //紐づけるセッティングID
			'section' => 'font_pattern_section', //紐づけるセクション名
			'type' => 'radio', //コントロールタイプ
			'choices' => array(
				'font_klee' => 'クレー(デフォルト)',
				'font_zen_kusenaido' => 'ZEN紅道',
				'font_zen_kaku_gothic' => 'ZEN角ゴシック',
				'font_zen_maru_gothic' => 'ZEN丸ゴシック',
				'font_kiwi' => 'キウイ丸',
				'font_none' => '設定なし',
			),
		)
	);
}

// head内にCSSを追加
add_action( 'wp_head', 'font_css');
function font_css() {
	$style_template = '
		<style>
			.logo-text, .logo-menu-button, .appeal-title, .entry-title, .list-title, .entry-card-title, .pagination .page-numbers, .article h2, .article h3, .article h4, .article h5, .article h6, .widget h2, .widget-title, .related-entry-main-heading, .comment-title, .footer-title {
				font-family: %s;
			}
		</style>
	';
	$style_font = '';
	if (get_theme_mod('font_pattern_control','font_klee') === 'font_klee') {
		$style_font = '"Klee One", sans-serif';
	} elseif (get_theme_mod('font_pattern_control','font_klee') === 'font_zen_kusenaido') {
		$style_font = '"Zen Kurenaido", sans-serif';
	} elseif (get_theme_mod('font_pattern_control','font_klee') === 'font_zen_kaku_gothic') {
		$style_font = '"Zen Kaku Gothic New", sans-serif';
	} elseif (get_theme_mod('font_pattern_control','font_klee') === 'font_zen_maru_gothic') {
		$style_font = '"Zen Maru Gothic", sans-serif';
	} elseif (get_theme_mod('font_pattern_control','font_klee') === 'font_kiwi') {
		$style_font = '"Kiwi Maru", sans-serif';
	} else {
		$style_font = 'inherit';
	}
	echo sprintf($style_template, $style_font);
}

/*******************************
* カスタマイザーで
* 背景パターンを変更
*******************************/
//カスタマイザー設定
add_action('customize_register', 'bg_pattern');
function bg_pattern($wp_customize) {
	$wp_customize->add_section(
		'bg_image_section',
		array(
			'title' => '【スキン】背景パターン設定',
			'priority' => 1000,
		)
	);
	$wp_customize->add_setting(
		'bg_image_control',
		array(
			'default' => 'bg_grid'
		)
	);
	$wp_customize->add_control(
		'bg_image_control',
		array(
			'label' => '背景パターン設定',
			'description' => '背景のパターンをお選びください。「設定なし」にすると背景のパターンは削除され色のみになります。',
			'setting' => 'bg_image_control', //紐づけるセッティングID
			'section' => 'bg_image_section', //紐づけるセクション名
			'type' => 'radio', //コントロールタイプ
			'choices' => array(
				'bg_grid' => 'グリッド(デフォルト)',
				'bg_grid_paper' => '方眼紙',
				'bg_line' => '罫線',
				'bg_dot' => 'ドット',
				'bg_img_none' => '設定なし',
			),
		)
	);
}

// head内にCSSを追加
add_action( 'wp_head', 'bg_image_css');
function bg_image_css() {
	$style_template = '
		<style>
			.container, .sidebar-menu-content, .navi-menu-content, .mobile-menu-buttons, #navi .navi-in > .menu-mobile li {
				background-image: %s;
				background-size: %s;
			}
		</style>
	';
	$style_image = '';
	$style_size = '';
	if (get_theme_mod('bg_image_control','bg_grid') === 'bg_grid') {
		$style_image = 'linear-gradient(180deg, rgba(var(--white-color), 0) 93%, rgba(var(--white-color), 1) 100%),linear-gradient(90deg, rgba(var(--white-color), 0) 93%, rgba(var(--white-color), 1) 100%)';
		$style_size= '15px 15px';
	} elseif (get_theme_mod('bg_image_control','bg_grid') === 'bg_grid_paper') {
		$style_image = 'linear-gradient(rgba(var(--white-color), .7) 1%, rgba(var(--white-color), .7) 1%, transparent 1%, transparent 99%, rgba(var(--white-color), .7) 99%, rgba(var(--white-color), .7) 100%), linear-gradient(90deg, rgba(var(--white-color), .7) 1%, rgba(var(--white-color), .7) 1%, transparent 1%, transparent 99%, rgba(var(--white-color), .7) 99%, rgba(var(--white-color), .7) 100%), linear-gradient(transparent, transparent 25%, rgba(var(--white-color), .4) 25%, rgba(var(--white-color), .4) 26%, transparent 26%, transparent 50%, rgba(var(--white-color), .4) 50%, rgba(var(--white-color), .4) 51%, transparent 51%, transparent 75%, rgba(var(--white-color), .4) 75%, rgba(var(--white-color), .4) 76%, transparent 76%, transparent 100%), linear-gradient(90deg, transparent, transparent 25%, rgba(var(--white-color), .4) 25%, rgba(var(--white-color), .4) 26%, transparent 26%, transparent 50%, rgba(var(--white-color), .4) 50%, rgba(var(--white-color), .4) 51%, transparent 51%, transparent 75%, rgba(var(--white-color), .4) 75%, rgba(var(--white-color), .4) 76%, transparent 76%, transparent 100%)';
		$style_size = '80px 80px';
	} elseif (get_theme_mod('bg_image_control','bg_grid') === 'bg_line') {
		$style_image = 'linear-gradient(rgba(var(--white-color), 1) .1px, rgba(var(--white-color), 0) .05em)';
		$style_size = 'auto 1em';
	} elseif (get_theme_mod('bg_image_control','bg_grid') === 'bg_dot') {
		$style_image = 'radial-gradient(rgba(var(--white-color), 1) 3px, rgba(var(--white-color), 0) 3px)';
		$style_size= '25px 25px';
	} else {
		$style_image= 'none';
		$style_size= 'unset';
	}
	echo sprintf($style_template, $style_image, $style_size);
}

/*******************************
* カスタマイザーで
* ロゴテキストの傍点変更
*******************************/
//カスタマイザー設定
add_action('customize_register', 'logo_text_dot');
function logo_text_dot($wp_customize) {
	$wp_customize->add_section(
		'logo_text_dot_section',
		array(
			'title' => '【スキン】ロゴテキストの傍点設定',
			'priority' => 1000,
		)
	);
	$wp_customize->add_setting(
		'logo_text_dot_control',
		array(
			'default' => 'dot_point'
		)
	);
	$wp_customize->add_control(
		'logo_text_dot_control',
		array(
			'label' => 'ロゴテキストの傍点デザイン',
			'description' => 'ロゴテキストの傍点をお選びください。「設定なし」にすると傍点は削除されます。',
			'setting' => 'logo_text_dot_control',//紐づけるセッティングID
			'section' => 'logo_text_dot_section', //紐づけるセクション名
			'type' => 'select', //コントロールタイプ
			'choices' => array(
				'dot_point' => '点(デフォルト)',
				'dot_point_open' => '点 白抜き',
				'dot_circle' => '丸',
				'dot_circle_open' => '丸 白抜き',
				'dot_double_circle' => '二重丸',
				'dot_double_circle_open' => '二重丸 白抜き',
				'dot_triangle' => '三角',
				'dot_triangle_open' => '三角 白抜き',
				'dot_sesame' => 'ゴマ',
				'dot_sesame_open' => 'ゴマ 白抜き',
				'dot_none' => '設定なし',
			),
		)
	);
		$wp_customize->add_setting(
		'logo_text_dot_potision',
		array(
			'default' => 'dot_under'
		)
	);
	$wp_customize->add_control(
		'logo_text_dot_potision',
		array(
			'label' => 'ロゴテキストの傍点位置',
			'description' => 'ロゴテキストの傍点の位置をお選びいただけます。',
			'setting' => 'logo_text_dot_potision',//紐づけるセッティングID
			'section' => 'logo_text_dot_section', //紐づけるセクション名
			'type' => 'radio', //コントロールタイプ
			'choices' => array(
				'dot_under' => '下(デフォルト)',
				'dot_over' => '上',
			),
		)
	);
}

// head内にCSSを追加
add_action( 'wp_head', 'logo_text_dot_css');
function logo_text_dot_css() {
	$style_template = '
	<style>
		.logo-text .site-name-text, .mobile-menu-buttons .logo-menu-button > a {
			text-emphasis-style: %s;
			-webkit-text-emphasis-style: %s;
			text-emphasis-position: %s;
			-webkit-text-emphasis-position: %s;
		}
	</style>
	';
	$style_value = '';
	$style_position = '';
	if (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_point') {
		$style_value = 'dot';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_point_open') {
		$style_value = 'open dot';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_circle') {
		$style_value = 'circle';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_circle_open') {
		$style_value = 'open circle';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_double_circle') {
		$style_value = 'double-circle';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_double_circle_open') {
		$style_value = 'open double-circle';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_triangle') {
		$style_value = 'triangle';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_triangle_open') {
		$style_value = 'open triangle';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_sesame') {
		$style_value = 'sesame';
	} elseif (get_theme_mod('logo_text_dot_control','dot_point') === 'dot_sesame_open') {
		$style_value = 'open sesame';
	} else {
		$style_value = 'none';
	}
	if (get_theme_mod('logo_text_dot_potision','dot_under') === 'dot_under') {
		$style_position = 'under left';
	} else {
		$style_position = 'over left';
	}
	echo sprintf($style_template, $style_value, $style_value, $style_position, $style_position);
}