<?php //ブロックエディタースタイル関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('after_setup_theme', function(){
  //FAQ
  register_block_style('cocoon-blocks/faq', array(
    'name' => 'square',
    'label' => __('角型ラベル', THEME_NAME),
  ));
  register_block_style('cocoon-blocks/faq', array(
    'name' => 'accordion',
    'label' => __('アコーディオン', THEME_NAME),
  ));
});