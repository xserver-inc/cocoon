<?php //外部ツールを利用するもの
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//商品リンク追加ボタンの作成
//add_filter( "media_buttons_context", "wp_product_button_context");
if ( !function_exists( 'wp_product_button_context' ) ):
function wp_product_button_context ( $context ) {
 $title = __( '商品リンクの追加', THEME_NAME ) ;
 $context = '<a href="#TB_inline?width=600&height=550&inlineId=wp_product_window" id="wp-product-tag" class="button thickbox wp-button" title="'.esc_attr($title).'"><span class="dashicons dashicons-cart"></span>'.$title.'</a>';

 return $context;
}
endif;

//商品リンクポップアップウィンドウ内容
//add_action( 'admin_footer',  'wp_product_popup_content' );
if ( !function_exists( 'wp_product_popup_content' ) ):
function wp_product_popup_content() {
echo <<< EOS
<div id="wp_product_window" style="display:none;">
<h2>タイトル</h2>
<p>内容</p>
</div>
EOS;
}
endif;
