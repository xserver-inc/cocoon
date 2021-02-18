<?php //テーマ情報取得
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'get_wp_theme_info' ) ):
function get_wp_theme_info($file){
  if (file_exists($file)) {
    $text = wp_filesystem_get_contents($file);
    if ($text) {
      $info = array();
      if (preg_match('/Theme Name: *(.+)/i', $text, $m)) {
        $info['theme_name'] = $m[1];
      }
      if (preg_match('/Version: *(.+)/i', $text, $m)) {
        $info['version'] = $m[1];
      }

      return $info;
    }
  }
}
endif;
