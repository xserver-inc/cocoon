<?php //ブロックエディタースタイル関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('after_setup_theme', function(){
  ///////////////////////////////////////////
  // シンプル
  ///////////////////////////////////////////
  register_block_style(
    'core/list',
    array(
      'name'  => 'solid-line',
      'label' => __( '枠線', THEME_NAME ),
    )
  );

  register_block_style(
    'core/list',
    array(
      'name'  => 'gray-back',
      'label' => __( '背景色', THEME_NAME ),
    )
  );

  register_block_style(
    'core/list',
    array(
      'name'  => 'solid-back',
      'label' => __( '枠線・背景色', THEME_NAME ),
    )
  );

  register_block_style(
    'core/list',
    array(
      'name'  => 'solid-cross',
      'label' => __( '交差線', THEME_NAME ),
    )
  );
});