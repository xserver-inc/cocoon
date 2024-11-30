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
  // 画像
  ///////////////////////////////////////////
  register_block_style('core/image', array(
    'name' => 'filter-clarendon',
    'label' => __('Clarendon', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-gingham',
    'label' => __('Gingham', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-moon',
    'label' => __('Moon', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-lark',
    'label' => __('Lark', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-reyes',
    'label' => __('Reyes', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-juno',
    'label' => __('Juno', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-slumber',
    'label' => __('Slumber', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-crema',
    'label' => __('Crema', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-ludwig',
    'label' => __('Ludwig', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-aden',
    'label' => __('Aden', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-perpetua',
    'label' => __('Perpetua', THEME_NAME),
  ));
  register_block_style('core/image', array(
    'name' => 'filter-monochrome',
    'label' => __('Mono', THEME_NAME),
  ));
});