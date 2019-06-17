<?php
/**
 * Plugin Name: Cocoon Blocks
 * Plugin URI: https://wp-cocoon.com/cocoon-blocks/
 * Description: Cocoonテーマ専用のブロックエディター（Gutenberg）対応プラグイン
 * Author: わいひら
 * Author URI: https://nelog.jp/
 * Version: 1.0.0
 * Text Domain: cocoon-blocks
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Block Initializer.
 */
if ( !function_exists( 'cocoon_blocks_cgb_block_assets' ) ){
  require_once abspath(__FILE__) . 'src/init.php';
}



