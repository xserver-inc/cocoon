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
if ( !defined( 'ABSPATH' ) ) exit;

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
	add_action( 'enqueue_block_assets', 'cocoon_blocks_cgb_block_assets' );
}
function cocoon_blocks_cgb_block_assets() { // phpcs:ignore
	// Styles.
	wp_enqueue_style(
		'cocoon_blocks-cgb-style-css', // Handle.
		get_template_directory_uri().'/blocks/dist/blocks.style.build.css',
		//plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		array( 'wp-editor' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	//Font Awesome
	if (apply_filters( 'cocoon_blocks_wp_enqueue_script_fontawesome', true )) {
		wp_enqueue_script(
			'cocoon_blocks-fontawesome5-js',
			'https://use.fontawesome.com/releases/v5.6.3/js/all.js',
			array(),
			'5.6.3',
			true
		);
	}
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
	add_action( 'enqueue_block_editor_assets', 'cocoon_blocks_cgb_editor_assets' );
}
function cocoon_blocks_cgb_editor_assets() { // phpcs:ignore
	// Scripts.
	wp_enqueue_script(
		'cocoon-blocks-js', // Handle.
		get_template_directory_uri().'/blocks/dist/blocks.build.js',
		//plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ) // Dependencies, defined above.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		// true // Enqueue the script in the footer.
  );
  //ショートコードオブジェクトの取得
  $balloons = get_speech_balloons(null, 'title');
  $templates = get_function_texts(null, 'title');
  $affiliates = get_affiliate_tags(null, 'title');
  $rankings = get_item_rankings(null, 'title');
  $is_templates_visible = (has_valid_shortcode_item($templates) && is_block_editor_template_shortcode_dropdown_visible()) ? 1 : 0;
  $is_affiliates_visible = (has_valid_shortcode_item($affiliates) && is_block_editor_affiliate_shortcode_dropdown_visible()) ? 1 : 0;
  $is_rankings_visible = (has_valid_shortcode_item($rankings) && is_block_editor_ranking_shortcode_dropdown_visible()) ? 1 : 0;
  $gutenberg_settings = array(
    'isRubyVisible' => is_block_editor_ruby_button_visible() ? 1 : 0,
    'isLetterVisible' => is_block_editor_letter_style_dropdown_visible() ? 1 : 0,
    'isMarkerVisible' => is_block_editor_marker_style_dropdown_visible() ? 1 : 0,
    'isBadgeVisible'  => is_block_editor_badge_style_dropdown_visible() ? 1 : 0,
    'isFontSizeVisible' => is_block_editor_font_size_style_dropdown_visible() ? 1 : 0,
    'isGeneralVisible' => is_block_editor_general_shortcode_dropdown_visible() ? 1 : 0,
    'isTemplateVisible' => $is_templates_visible,
    'isAffiliateVisible' => $is_affiliates_visible,
    'isRankingVisible' => $is_rankings_visible,
    'isSpeechBalloonEnable' => $balloons ? 1 : 0,
    'speechBalloonDefaultIconUrl' => get_template_directory_uri().'/images/anony.png',
    'siteIconFont' => get_site_icon_font_class(),
    'isDebugMode' => DEBUG_MODE,
  );


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
  //_v($balloons);
  wp_localize_script(
    'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
    'gbSpeechBalloons', //任意のオブジェクト名
    $balloons //プロバティ
  );
  //テーマのキーカラーを渡す
  wp_localize_script(
    'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
    'keyColor', //任意のオブジェクト名
     get_editor_key_color()//プロバティ
  );
  //テンプレート情報を渡す
  //_v($templates);
  if ($is_templates_visible) {
    wp_localize_script(
      'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
      'gbTemplates', //任意のオブジェクト名
      $templates //プロバティ
    );
  }

  //アフィリエイト情報を渡す
  //_v($affiliates);
  if ($is_affiliates_visible) {
    wp_localize_script(
      'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
      'gbAffiliateTags', //任意のオブジェクト名
      $affiliates //プロバティ
    );
  }

  //ランキング情報を渡す
  //_v($rankings);
  if ($is_rankings_visible) {
    wp_localize_script(
      'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
      'gbItemRankings', //任意のオブジェクト名
      $rankings //プロバティ
    );
  }

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


  // Styles.
  wp_enqueue_style(
      'cocoon_blocks-cgb-block-editor-css', // Handle.
      get_template_directory_uri().'/blocks/dist/blocks.editor.build.css',
      //plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
      array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
      // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
  );
}

//Cocoonカテゴリーを追加
if (is_admin()) {
	add_filter( 'block_categories', 'add_cocoon_theme_block_categories', 10, 2 );
}
if ( !function_exists( 'add_cocoon_theme_block_categories' ) ):
function add_cocoon_theme_block_categories( $categories, $post ){
  $block_categories = array_merge(
		$categories,
		array(
			array(
				'slug' => THEME_NAME.'-block',
        'title' => __( 'Cocoonブロック', THEME_NAME ),
        //'icon' => 'heart',
			),
			array(
				'slug' => THEME_NAME.'-universal-block',
        'title' => __( 'Cocoon汎用ブロック', THEME_NAME ),
        //'icon' => 'heart',
			),
			array(
				'slug' => THEME_NAME.'-micro',
        'title' => __( 'Cocoonマイクロコピー', THEME_NAME ),
        //'icon' => 'heart',
			),
      array(
        'slug' => THEME_NAME.'-layout',
        'title' => __( 'Cocoonレイアウト', THEME_NAME ),
        //'icon' => 'heart',
      ),
      array(
        'slug' => THEME_NAME.'-shortcode',
        'title' => __( 'Cocoonショートコード', THEME_NAME ),
        //'icon' => 'heart',
      ),
			array(
				'slug' => THEME_NAME.'-old',
        'title' => __( 'Cocoon旧ブロック（非推奨）', THEME_NAME ),
        //'icon' => 'heart',
			),
		)
  );
  //ブロックカテゴリーのフィルターフック
  $block_categories = apply_filters('cocoon_theme_block_categories', $block_categories);
	return $block_categories;
}
endif;

//許可するブロックを配列で返す（ホワイトリスト形式で実用性がない…）
add_filter( 'allowed_block_types', 'cocoon_allowed_block_types_custom' );
if ( !function_exists( 'cocoon_allowed_block_types_custom' ) ):
function cocoon_allowed_block_types_custom( $allowed_block_types ) {
  return $allowed_block_types;
}
endif;

//カラーパレット
add_action('after_setup_theme', 'cocoon_editor_color_palette_setup');
if ( !function_exists( 'cocoon_editor_color_palette_setup' ) ):
function cocoon_editor_color_palette_setup() {
    $colors = get_cocoon_editor_color_palette_colors();
    // カラーパレットの設定
    add_theme_support('editor-color-palette', $colors);
    // カスタム色を無効
    add_theme_support('disable-custom-colors');
    // カスタムフォントサイズを無効
    add_theme_support('disable-custom-font-sizes');
    return $colors;
}
endif;
