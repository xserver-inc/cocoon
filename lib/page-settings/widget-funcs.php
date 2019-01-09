<?php //ウィジェット設定用関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//除外ウィジェット
define('OP_EXCLUDE_WIDGET_CLASSES', 'exclude_widget_classes');
if ( !function_exists( 'get_exclude_widget_classes' ) ):
function get_exclude_widget_classes(){
  $classes = get_theme_option(OP_EXCLUDE_WIDGET_CLASSES, array());
  if (!is_array($classes)) {
    $classes = array();
  }
  return $classes;
}
endif;

//ウィジェットの除外
add_action( 'widgets_init', 'unregister_exclude_widgets', 20 );
if ( !function_exists( 'unregister_exclude_widgets' ) ):
function unregister_exclude_widgets() {
  if (!is_admin_php_page()) {
    $classes = get_exclude_widget_classes();
    foreach ($classes as $class) {
      unregister_widget($class);//クラスの除外
    }
  }
}

endif;


