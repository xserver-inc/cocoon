<?php //PWA設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PWAを有効にする
define('OP_PWA_ENABLE', 'pwa_enable');
if ( !function_exists( 'is_pwa_enable' ) ):
function is_pwa_enable(){
  return get_theme_option(OP_PWA_ENABLE);
}
endif;
