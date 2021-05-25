<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
if ( !defined( 'ABSPATH' ) ) exit;
///////////////////////////////////////////
// 設定操作サンプル
// lib\page-settings\内のXXXXX-funcs.phpに書かれている
// 定義済み関数をオーバーライドして設定を上書きできます。
// 関数をオーバーライドする場合は必ず!function_existsで
// 存在を確認してください。
///////////////////////////////////////////

//スキン制御
global $_THEME_OPTIONS;
$_THEME_OPTIONS =
array(
	'site_key_color' => '',
	'site_background_color' => '',
	'site_background_image_url' => '',
	'header_container_background_color' => '',
	'header_background_color' => '',
	'global_navi_background_color' => '',
	'main_column_border_color' => '',
	'sidebar_border_color' => '',
	'appeal_area_background_color' => '',
	'footer_background_color' => ''
);

//全体色と角丸のデフォルト値設定
const S_DEFAULT = array(
        'default'       => array(
                'bg'                  => '#d8eeff',
                'corner'                  => '15',
        ),
);
//角丸調節バー
if( class_exists( 'WP_Customize_Control' ) ) {
    class WP_Customize_Range extends WP_Customize_Control {
        public $type = 'range';

        public function __construct( $manager, $id, $args = array() ) {
            parent::__construct( $manager, $id, $args );
            $defaults = array(
                'min' => 0,
                'max' => 10,
                'step' => 1
            );
            $args = wp_parse_args( $args, $defaults );

            $this->min = $args['min'];
            $this->max = $args['max'];
            $this->step = $args['step'];
        }

        public function render_content() {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <input class='range-slider' min="<?php echo $this->min ?>" max="<?php echo $this->max ?>" step="<?php echo $this->step ?>" type='range' <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" oninput="jQuery(this).next('input').val( jQuery(this).val() )">
            <input onKeyUp="jQuery(this).prev('input').val( jQuery(this).val() )" type='text' value='<?php echo esc_attr( $this->value() ); ?>'>

        </label>
        <?php
        }
    }
}

function customizer_color( $wp_customize ) {
//セクション追加
	$wp_customize->add_section(
		'section_neumorphism',
		array(
			'title'    => 'Neumorphism',
			'priority' => 30,
		)
	);
//角丸設定追加
$wp_customize->add_setting( 'customizable_range' , array(
    'default'     => 15,
    'type' => 'theme_mod'
  ) 
);
//「セクション」と「テーマ設定」を紐づけてコントロールを出力
$wp_customize->add_control(
  new WP_Customize_Range(
    $wp_customize,
    'ctrl_customizable_range',
    array(
      'label'   => '角の丸さ調節',
      'min' => 0,
      'max' => 30,
      'step' => 1,
      'section' => 'section_neumorphism',
      'settings'   => 'customizable_range',
    )
  )
);
//全体色設定追加
	$wp_customize->add_setting(
		'color_bg',
		array(
			'default'           => '#d8eeff', // デフォルト値を設定
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);
//「セクション」と「テーマ設定」を紐づけてコントロールを出力
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'color_bg',
			array(
				'section'  => 'section_neumorphism',
				'settings' => 'color_bg',
				'label'    => '色',
			)
		)
	);
}

add_action( 'customize_register', 'customizer_color' );

//取得した色をhead内にCSS変数にして入れ込み
function root_customize_css(){
	$color_bg = esc_html( get_theme_mod( 'color_bg', S_DEFAULT['default']['bg'] ) );
	$color_ki = generate_new_color( $color_bg, -0.7 );
	$color_us = generate_new_color( $color_bg, 0.07 );
	$color_hk = generate_new_color( $color_bg, 0.18 );
	$light_color = generate_new_color( $color_bg, 0.1 );
	$dark_color  = generate_new_color( $color_bg, -0.15 );
	$customizable_range = esc_html( get_theme_mod( 'customizable_range', S_DEFAULT['default']['corner'] ) );

    ?>
        <style type="text/css">
                :root{ 
                        --color-bg:<?php echo $color_bg ?>;
						--color-ki:<?php echo $color_ki ?>;
						--color-us:<?php echo $color_us ?>;
						--color-hk:<?php echo $color_hk ?>;
						--color-shadow-light:<?php echo $light_color ?>;
						--color-shadow-dark :<?php echo $dark_color ?>;
						--corner :<?php echo $customizable_range,'px' ?>;
        }
        </style>
    <?php
}
add_action( 'wp_head', 'root_customize_css');

//ブロックエディターにもスキンCSSを適用
add_action( 'admin_head', 'root_customize_css');

function block_editor_style_setup() {
  add_theme_support( 'editor-styles' );
  add_editor_style( 'skins/skin-neumorphism/style.css' );
}
add_action( 'after_setup_theme', 'block_editor_style_setup' );

//影色生成
function generate_new_color( $hex, $luminance ) {
	if ( ! preg_match( '/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $hex ) ) {
		return $hex;
	}

	$hex = substr( $hex, 1 );

	if ( strlen( $hex ) === 3 ) {
		$hex = substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) . substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) . substr( $hex, 2, 1 ) . substr( $hex, 2, 1 );
	}

	$new_hex = '#';

	for ( $i = 0; $i < 3; $i++ ) {
		$color_pair  = hexdec( substr( $hex, $i * 2, 2 ) );
		$color_pair += $color_pair * $luminance;
		$color_pair  = dechex( round( min( 245, max( 10, $color_pair ) ) ) );
		$color_pair  = str_pad( $color_pair, 2, '0', STR_PAD_LEFT );
		$new_hex    .= $color_pair;
	}

	return $new_hex;
}

//テーマカスタマイザのプレビュー画面でのみcustomizer-preview.jsを読み込み
function neumorphism_customize_preview_init() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_script( 'neumorphism-customize-preview', get_theme_file_uri( 'skins/skin-neumorphism/customizer-preview.js' ), array( 'customize-preview', 'jquery' ), $theme_version, true );
}

add_action( 'customize_preview_init', 'neumorphism_customize_preview_init' );