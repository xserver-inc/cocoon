<?php //OGP関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Facebook OGPタグを出力
if ( is_facebook_ogp_enable() && !is_wpforo_plugin_page() ) {
  add_action( 'wp_head', 'the_facebook_ogp_tag', 1 );
}
if ( !function_exists( 'the_facebook_ogp_tag' ) ):
function the_facebook_ogp_tag() {
  get_template_part('tmp/header-ogp');
}
endif;

//Twitterカードタグを出力
if ( is_twitter_card_enable() && !is_wpforo_plugin_page() ) {
  add_action( 'wp_head', 'the_twitter_card_tag', 1 );
}
if ( !function_exists( 'the_twitter_card_tag' ) ):
function the_twitter_card_tag() {
  get_template_part('tmp/header-twitter-card');
}
endif;

//OGPとTwitterカードが有効な場合、JetpackのOGP情報を削除
if (is_facebook_ogp_enable() && is_twitter_card_enable() && !is_wpforo_plugin_page()) {
  add_filter( 'jetpack_enable_open_graph', '__return_false' );
}
