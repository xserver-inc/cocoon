<?php //ウィジェット設定用関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//除外ウィジェット
define('OP_EXCLUDE_WIDGET_CLASSES', 'exclude_widget_classes');
if ( !function_exists( 'get_exclude_widget_classes' ) ):
function get_exclude_widget_classes(){
  return get_theme_option(OP_EXCLUDE_WIDGET_CLASSES, array());
}
endif;


