<?php //キャッシュ系の処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//テーマを変更時にテーマのリソースキャッシュを削除
//add_action('switch_theme', 'delete_theme_resource_caches');
if ( !function_exists( 'delete_theme_resource_caches' ) ):
function delete_theme_resource_caches() {
  //ブログカードキャッシュの削除
  delete_blogcard_cache_transients();
  //シェア・フォローカウントキャッシュの削除
  delete_sns_cache_transients();
  //キャッシュ用リソースフォルダの削除
  remove_all_directory(get_theme_resources_path());
}
endif;

//transientSNSキャッシュの削除
if ( !function_exists( 'delete_blogcard_cache_transients' ) ):
function delete_blogcard_cache_transients(){
  global $wpdb;
  //ブログカードキャッシュの削除（bcc = Blog Card Cache)
  $wpdb->query("DELETE FROM $wpdb->options WHERE (`option_name` LIKE '%_transient_bcc_%') OR (`option_name` LIKE '%_transient_timeout_bcc_%')");
}
endif;
//delete_blogcard_cache_transients();

//transientSNSキャッシュの削除
if ( !function_exists( 'delete_sns_cache_transients' ) ):
function delete_sns_cache_transients(){
  global $wpdb;
  //シェアカウントキャッシュの削除
  $wpdb->query("DELETE FROM $wpdb->options WHERE (`option_name` LIKE '%_transient_".TRANSIENT_SHARE_PREFIX."%') OR (`option_name` LIKE '%_transient_timeout_".TRANSIENT_SHARE_PREFIX."%')");
  //フォローカントキャッシュの削除
  $wpdb->query("DELETE FROM $wpdb->options WHERE (`option_name` LIKE '%_transient_".TRANSIENT_FOLLOW_PREFIX."%') OR (`option_name` LIKE '%_transient_timeout_".TRANSIENT_FOLLOW_PREFIX."%')");
}
endif;
//delete_sns_cache_transients();



//AMP個別キャシュの削除
add_action( 'publish_post', 'delete_amp_page_cache');
if ( !function_exists( 'delete_amp_page_cache' ) ):
function delete_amp_page_cache($id){
  if (is_user_administrator()) {
    $transient_id = TRANSIENT_AMP_PREFIX.$id;
    $transient_file = get_theme_amp_cache_path().$transient_id;
    if (file_exists($transient_file)) {
      return wp_filesystem_delete($transient_file) &&
             delete_db_cache_records(TRANSIENT_AMP_PREFIX.$id);
    }
  }
}
endif;
