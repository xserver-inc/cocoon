<?php //クリック解析管理操作
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('admin_post_cocoon_click_delete_data', 'cocoon_click_handle_delete_data');

if ( !function_exists( 'cocoon_click_handle_delete_data' ) ):
function cocoon_click_handle_delete_data(){
  if (!current_user_can('manage_options')) {
    wp_die(__('この操作を行う管理者権限がありません。', THEME_NAME));
  }
  check_admin_referer('cocoon_click_delete_data');
  $deleted = cocoon_click_delete_all_data();
  cocoon_click_analytics_flush_cache();
  $url = add_query_arg(array(
    'page' => 'theme-access',
    'view' => 'settings',
    $deleted ? 'cocoon_click_deleted' : 'cocoon_click_delete_failed' => '1',
  ), admin_url('admin.php'));
  wp_safe_redirect($url);
  exit;
}
endif;
