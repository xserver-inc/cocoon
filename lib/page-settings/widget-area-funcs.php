<?php //ウィジェットエリア設定用関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//除外ウィジェットエリアのID
define('OP_EXCLUDE_WIDGET_AREA_IDS', 'exclude_widget_area_ids');
if ( !function_exists( 'get_exclude_widget_area_ids' ) ):
function get_exclude_widget_area_ids(){
  $ids = get_theme_option(OP_EXCLUDE_WIDGET_AREA_IDS, array());
  if (!is_array($ids)) {
    $ids = array();
  }
  return $ids;
}
endif;

//ウィジェットの除外
add_action( 'widgets_init', 'unregister_exclude_widget_areas', 20 );
if ( !function_exists( 'unregister_exclude_widget_areas' ) ):
function unregister_exclude_widget_areas() {
  if (!is_admin_php_page()) {
    $ids = get_exclude_widget_area_ids();
    foreach ($ids as $id) {
      unregister_sidebar($id);//クラスの除外
    }
  }
}

endif;


