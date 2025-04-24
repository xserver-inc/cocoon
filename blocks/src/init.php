<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */
/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit();
}

//個々でフックに対してis_admin()で条件分岐しているのは、このようにしないとwpForoフォーラム画面が真っ白になってしまうため
//何か不都合がありましたら以下まで。
//https://wp-cocoon.com/community/

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
// Hook: Frontend assets.
if (is_admin()) {
	add_action('enqueue_block_assets', 'cocoon_blocks_cgb_block_assets');
}
function cocoon_blocks_cgb_block_assets()
{
	// phpcs:ignore
	//Font Awesome
	wp_enqueue_style_font_awesome();

  //Google Fonts
  wp_enqueue_google_fonts();

  //サイトフォントの設定を反映するクラスを付与
  add_filter( 'admin_body_class', function($classes){
    $classes .= ' wp-admin-'.get_site_font_family_class();
    $classes .= ' wp-admin-'.get_site_font_size_class();
    $classes .= ' wp-admin-'.get_site_font_weight_class();
    return $classes;
  });
}

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
// Hook: Editor assets.
if (is_admin()) {
	add_action(
		'enqueue_block_editor_assets',
		'cocoon_blocks_cgb_editor_assets',
		9
	);
}
function cocoon_blocks_cgb_editor_assets()
{

  $asset_file = require get_template_directory() . '/blocks/dist/blocks.build.asset.php';
	// phpcs:ignore
	// Scripts.
	wp_enqueue_script(
		'cocoon-blocks-js', // Handle.
		get_template_directory_uri() . '/blocks/dist/blocks.build.js',
		//plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
    $asset_file['dependencies'],
    // const xxx = wp.xxx型式の古い記述で有効 // Dependencies, defined above.
    $asset_file['version']
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		// true // Enqueue the script in the footer.
	);

	// ブロックエディターに翻訳ファイルを読み込む
	wp_set_script_translations(
		'cocoon-blocks-js',
		THEME_NAME,
		get_template_directory() . '/languages'
	);

	//ショートコードオブジェクトの取得
	$balloons = get_speech_balloons(null, 'title');
	$colors = ['keyColor' => get_editor_key_color()];
	$templates = get_function_texts(null, 'title');
	$affiliates = get_affiliate_tags(null, 'title');
	$rankings = get_item_rankings(null, 'title');

	$is_templates_visible =
		has_valid_shortcode_item($templates) &&
		is_block_editor_template_shortcode_dropdown_visible()
			? 1
			: 0;
	$is_affiliates_visible =
		has_valid_shortcode_item($affiliates) &&
		is_block_editor_affiliate_shortcode_dropdown_visible()
			? 1
			: 0;
	$is_rankings_visible =
		has_valid_shortcode_item($rankings) &&
		is_block_editor_ranking_shortcode_dropdown_visible()
			? 1
			: 0;
	global $wp_version;
	$gutenberg_settings = [
		'isRubyVisible' => is_block_editor_ruby_button_visible() ? 1 : 0,
		'isClearFormatVisible' => is_block_editor_clear_format_button_visible()
			? 1
			: 0,
		'isLetterVisible' => is_block_editor_letter_style_dropdown_visible()
			? 1
			: 0,
		'isMarkerVisible' => is_block_editor_marker_style_dropdown_visible()
			? 1
			: 0,
		'isBadgeVisible' => is_block_editor_badge_style_dropdown_visible()
			? 1
			: 0,
		'isFontSizeVisible' => is_block_editor_font_size_style_dropdown_visible()
			? 1
			: 0,
		'isGeneralVisible' => is_block_editor_general_shortcode_dropdown_visible()
			? 1
			: 0,
		'isTemplateVisible' => $is_templates_visible,
		'isAffiliateVisible' => $is_affiliates_visible,
		'isRankingVisible' => $is_rankings_visible,
		'isSpeechBalloonEnable' => $balloons ? 1 : 0,
		'isAdsVisible' => is_ads_visible() ? 1 : 0,
		'isAdShortcodeEnable' => is_ad_shortcode_enable() ? 1 : 0,
		'speechBalloonDefaultIconUrl' =>
			get_template_directory_uri() . '/images/anony.png',
		'siteIconFont' => ' ' . get_site_icon_font_class(),
		'pageTypeClass' => get_editor_page_type_class(),
		'isDebugMode' => DEBUG_MODE,
		'wpVersion' => $wp_version,
		'templateUrl' => get_template_directory_uri(),
		'customTextCount' => apply_filters( 'cocoon_custom_text_count', 2),
	];

	// _v(is_block_editor_template_shortcode_dropdown_visible());
	// _v( $is_templates_visible);
	///////////////////////////////////////////
	// 表示
	///////////////////////////////////////////
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbSettings', //任意のオブジェクト名
		$gutenberg_settings //プロバティ
	);

	///////////////////////////////////////////
	// オブジェクト渡し
	///////////////////////////////////////////
	//吹き出し情報を渡す
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbSpeechBalloons', //任意のオブジェクト名
		$balloons //プロバティ
	);
	//テーマのキーカラーを渡す
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbColors', //任意のオブジェクト名
		$colors //プロバティ
	);
	//テンプレート情報を渡す
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbTemplates', //任意のオブジェクト名
		$templates //プロバティ
	);

	//アフィリエイト情報を渡す
	if ($is_affiliates_visible) {
		wp_localize_script(
			'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
			'gbAffiliateTags', //任意のオブジェクト名
			$affiliates //プロバティ
		);
	}

	//ランキング情報を渡す
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbItemRankings', //任意のオブジェクト名
		$rankings //プロバティ
	);

	//メニュー情報を渡す
	$menus = wp_get_nav_menus();
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbNavMenus', //任意のオブジェクト名
		$menus //プロバティ
	);

	//プロフィール情報を渡す
	$users = get_users(['fields' => ['ID', 'display_name']]);
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbUsers', //任意のオブジェクト名
		$users //プロバティ
	);

	//カラーパレット情報渡し
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'cocoonPaletteColors', //任意のオブジェクト名
		get_cocoon_editor_color_palette_colors() //カラーパレット
	);

	//言語情報渡し
	wp_localize_script(
		'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
		'gbCodeLanguages', //任意のオブジェクト名
		get_block_editor_code_languages() //カラーパレット
	);
}

//Cocoonカテゴリーを追加
if (is_admin()) {
	if (is_wp_5_8_or_over()) {
		add_filter(
			'block_categories_all',
			'add_cocoon_theme_block_categories',
			10,
			2
		);
	} else {
		add_filter(
			'block_categories',
			'add_cocoon_theme_block_categories',
			10,
			2
		);
	}
}
if (!function_exists('add_cocoon_theme_block_categories')):
	function add_cocoon_theme_block_categories($categories, $post)
	{
		$block_categories = array_merge($categories, [
			[
				'slug' => THEME_NAME . '-block',
				'title' => __('Cocoonブロック', THEME_NAME),
				//'icon' => 'heart',
			],
			[
				'slug' => THEME_NAME . '-universal-block',
				'title' => __('Cocoon汎用ブロック', THEME_NAME),
				//'icon' => 'heart',
			],
			[
				'slug' => THEME_NAME . '-micro',
				'title' => __('Cocoonマイクロコピー', THEME_NAME),
				//'icon' => 'heart',
			],
			[
				'slug' => THEME_NAME . '-layout',
				'title' => __('Cocoonレイアウト', THEME_NAME),
				//'icon' => 'heart',
			],
			[
				'slug' => THEME_NAME . '-shortcode',
				'title' => __('Cocoonショートコード', THEME_NAME),
				//'icon' => 'heart',
			],
			[
				'slug' => THEME_NAME . '-old',
				'title' => __('Cocoon旧ブロック（非推奨）', THEME_NAME),
				//'icon' => 'heart',
			],
		]);
		//ブロックカテゴリーのフィルターフック
		$block_categories = apply_filters(
			'cocoon_theme_block_categories',
			$block_categories
		);
		return $block_categories;
	}
endif;

//許可するブロックを配列で返す（ホワイトリスト形式で実用性がない…）
if (is_wp_5_8_or_over()) {
	add_filter('allowed_block_types_all', 'cocoon_allowed_block_types_custom');
} else {
	add_filter('allowed_block_types', 'cocoon_allowed_block_types_custom');
}
if (!function_exists('cocoon_allowed_block_types_custom')):
	function cocoon_allowed_block_types_custom($allowed_block_types)
	{
		return $allowed_block_types;
	}
endif;

//カラーパレット
add_action('after_setup_theme', 'cocoon_editor_color_palette_setup');
if (!function_exists('cocoon_editor_color_palette_setup')):
	function cocoon_editor_color_palette_setup()
	{
		$colors = get_cocoon_editor_color_palette_colors();
		// カラーパレットの設定
		add_theme_support('editor-color-palette', $colors);
		// // カスタム色を無効
		// add_theme_support('disable-custom-colors');
		// // カスタムフォントサイズを無効
		// add_theme_support('disable-custom-font-sizes');
		// 行の高さ
		add_theme_support('custom-line-height');
		// 寸法設定
		add_theme_support('custom-spacing');
		// 単位設定
		add_theme_support('custom-units');
		// リンクカラー設定
		add_theme_support( 'link-color' );

		return $colors;
	}
endif;


//「ブロック下余白」のattributesに値格納用のextraBottomMargin追加（「ブロック読み込みエラー: 無効なパラメータ: attributes」エラー対策）
add_filter('register_block_type_args', 'register_block_type_args_custom', 10, 2);
if (!function_exists('register_block_type_args_custom')):
	function register_block_type_args_custom($args, $name)
	{
		$extra_attributes = array(
			"extraBottomMargin" => array(
				"type" => "string",
				"default" => "",
			)
		);
		if (isset($args['attributes']) && is_array($args['attributes'])) {
			$args['attributes'] = array_merge($args['attributes'], $extra_attributes);
		}

		return $args;
	}
endif;

//ブロックの読み込み
require_once abspath(__FILE__) . 'block/balloon/index.php';
require_once abspath(__FILE__) . 'block/blank-box/index.php';
require_once abspath(__FILE__) . 'block/blogcard/index.php';
require_once abspath(__FILE__) . 'block/button/index.php';
require_once abspath(__FILE__) . 'block/button-wrap/index.php';
require_once abspath(__FILE__) . 'block/icon-box/index.php';
require_once abspath(__FILE__) . 'block/icon-list/index.php';
require_once abspath(__FILE__) . 'block/info-box/index.php';
require_once abspath(__FILE__) . 'block/search-box/index.php';
require_once abspath(__FILE__) . 'block/sticky-box/index.php';
require_once abspath(__FILE__) . 'block/tab-box/index.php';
require_once abspath(__FILE__) . 'block/timeline/index.php';
require_once abspath(__FILE__) . 'block/timeline-item/index.php';
require_once abspath(__FILE__) . 'block/toggle-box/index.php';
require_once abspath(__FILE__) . 'block/ranking/index.php';
require_once abspath(__FILE__) . 'block/template/index.php';
require_once abspath(__FILE__) . 'block/box-menu/index.php';
require_once abspath(__FILE__) . 'block/ad/index.php';
require_once abspath(__FILE__) . 'block/profile/index.php';
require_once abspath(__FILE__) . 'block/new-list/index.php';
require_once abspath(__FILE__) . 'block/popular-list/index.php';
require_once abspath(__FILE__) . 'block/info-list/index.php';
require_once abspath(__FILE__) . 'block/navicard/index.php';
require_once abspath(__FILE__) . 'block/tab/index.php';
require_once abspath(__FILE__) . 'block/tab-item/index.php';
require_once abspath(__FILE__) . 'block/cta/index.php';
require_once abspath(__FILE__) . 'block/radar/index.php';

require_once abspath(__FILE__) . 'block-universal/caption-box/index.php';
require_once abspath(__FILE__) . 'block-universal/tab-caption-box/index.php';
require_once abspath(__FILE__) . 'block-universal/label-box/index.php';

require_once abspath(__FILE__) . 'micro/micro-balloon/index.php';
require_once abspath(__FILE__) . 'micro/micro-text/index.php';

