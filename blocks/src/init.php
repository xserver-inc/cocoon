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
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		true // Enqueue the script in the footer.
  );
  //ショートコードオブジェクトの取得
  $baloons = get_speech_balloons();
  $templates = get_function_texts();
  $affiliates = get_affiliate_tags();
  $rankings = get_item_rankings();
  $is_templates_visible = (has_valid_shortcode_item($templates) && is_block_editor_template_shortcode_dropdown_visible()) ? 1 : 0;
  $is_affiliates_visible = (has_valid_shortcode_item($affiliates) && is_block_editor_affiliate_shortcode_dropdown_visible()) ? 1 : 0;
  $is_rankings_visible = (has_valid_shortcode_item($rankings) && is_block_editor_ranking_shortcode_dropdown_visible()) ? 1 : 0;
  $dropdowns = array(
    'isRubyVisible' => is_block_editor_ruby_button_visible() ? 1 : 0,
    'isLetterVisible' => is_block_editor_letter_style_dropdown_visible() ? 1 : 0,
    'isMarkerVisible' => is_block_editor_marker_style_dropdown_visible() ? 1 : 0,
    'isBadgeVisible'  => is_block_editor_badge_style_dropdown_visible() ? 1 : 0,
    'isFontSizeVisible' => is_block_editor_font_size_style_dropdown_visible() ? 1 : 0,
    'isGeneralVisible' => is_block_editor_general_shortcode_dropdown_visible() ? 1 : 0,
    'isTemplateVisible' => $is_templates_visible,
    'isAffiliateVisible' => $is_affiliates_visible,
    'isRankingVisible' => $is_rankings_visible,
  );


  // _v(is_block_editor_template_shortcode_dropdown_visible());
  // _v( $is_templates_visible);
  ///////////////////////////////////////////
  // 表示
  ///////////////////////////////////////////
  wp_localize_script(
    'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
    'dropdowns', //任意のオブジェクト名
    $dropdowns //プロバティ
  );

  ///////////////////////////////////////////
  // オブジェクト渡し
  ///////////////////////////////////////////
  //吹き出し情報を渡す
  //_v($baloons);
  wp_localize_script(
    'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
    'speechBaloons', //任意のオブジェクト名
    $baloons //プロバティ
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
      'templates', //任意のオブジェクト名
      $templates //プロバティ
    );
  }

  //アフィリエイト情報を渡す
  //_v($affiliates);
  if ($is_affiliates_visible) {
    wp_localize_script(
      'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
      'affiliateTags', //任意のオブジェクト名
      $affiliates //プロバティ
    );
  }

  //ランキング情報を渡す
  //_v($rankings);
  if ($is_rankings_visible) {
    wp_localize_script(
      'cocoon-blocks-js', //値を渡すjsファイルのハンドル名
      'itemRankings', //任意のオブジェクト名
      $rankings //プロバティ
    );
  }



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
	return array_merge(
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
    // カラーパレットの設定
    add_theme_support('editor-color-palette', array(
        array(
            'name' => __( 'キーカラー', THEME_NAME ),
            'slug' => 'key-color',
            'color' => get_editor_key_color(),
        ),
        array(
            'name' => __( '赤色', THEME_NAME ),
            'slug' => 'red',
            'color' => '#e60033',
        ),
        array(
            'name' => __( 'ピンク', THEME_NAME ),
            'slug' => 'pink',
            'color' => '#e95295',
        ),
        array(
            'name' => __( '紫色', THEME_NAME ),
            'slug' => 'purple',
            'color' => '#884898',
        ),
        array(
            'name' => __( '深紫色', THEME_NAME ),
            'slug' => 'deep',
            'color' => '#55295b',
        ),
        array(
            'name' => __( '紺色', THEME_NAME ),
            'slug' => 'indigo',
            'color' => '#1e50a2',
        ),
        array(
            'name' => __( '青色', THEME_NAME ),
            'slug' => 'blue',
            'color' => '#0095d9',
        ),
        array(
            'name' => __( '天色', THEME_NAME ),
            'slug' => 'light-blue',
            'color' => '#2ca9e1',
        ),
        array(
            'name' => __( '浅葱色', THEME_NAME ),
            'slug' => 'cyan',
            'color' => '#00a3af',
        ),
        array(
            'name' => __( '深緑色', THEME_NAME ),
            'slug' => 'teal',
            'color' => '#007b43',
        ),
        array(
            'name' => __( '緑色', THEME_NAME ),
            'slug' => 'green',
            'color' => '#3eb370',
        ),
        array(
            'name' => __( '黄緑色', THEME_NAME ),
            'slug' => 'light-green',
            'color' => '#8bc34a',
        ),
        array(
            'name' => __( 'ライム', THEME_NAME ),
            'slug' => 'lime',
            'color' => '#c3d825',
        ),
        array(
            'name' => __( '黄色', THEME_NAME ),
            'slug' => 'yellow',
            'color' => '#ffd900',
        ),
        array(
            'name' => __( 'アンバー', THEME_NAME ),
            'slug' => 'amber',
            'color' => '#ffc107',
        ),
        array(
            'name' => __( 'オレンジ', THEME_NAME ),
            'slug' => 'orange',
            'color' => '#f39800',
        ),
        array(
            'name' => __( 'ディープオレンジ', THEME_NAME ),
            'slug' => 'deep-orange',
            'color' => '#ea5506',
        ),
        array(
            'name' => __( '茶色', THEME_NAME ),
            'slug' => 'brown',
            'color' => '#954e2a',
        ),
        array(
            'name' => __( '灰色', THEME_NAME ),
            'slug' => 'grey',
            'color' => '#949495',
        ),
        array(
            'name' => __( '黒', THEME_NAME ),
            'slug' => 'black',
            'color' => '#333',
        ),
        array(
            'name' => __( '白', THEME_NAME ),
            'slug' => 'white',
            'color' => '#fff',
        ),
    ));
    // 自由色選択を無効
    add_theme_support('disable-custom-colors');
}
endif;
