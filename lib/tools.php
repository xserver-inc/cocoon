<?php //外部ツールを利用するもの

add_filter( "media_buttons_context", "wp_product_button_context");
function wp_product_button_context ( $context ) {
 $title = __( '商品リンクの追加', THEME_NAME ) ;
 $context = '<a href="#TB_inline?width=600&height=550&inlineId=wp_product_window" id="wp-product-tag" class="button thickbox wp-button" title="'.$title.'"><span class="dashicons dashicons-cart"></span>'.$title.'</a>';

 return $context;
}