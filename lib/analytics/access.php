<?php //アクセスをカウントする
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

$wp_load = '../../../../../wp-load.php';
if (!file_exists($wp_load)) {
  //AWS Bitnamiパッケージのマルチサイト構成の場合
  $wp_load = '/opt/bitnami/wordpress/wp-load.php';
}
//wp-load.phpが存在する時だけ実行
if (file_exists($wp_load)) {
  require_once($wp_load);
  $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
  $post_type = isset($_GET['post_type']) && is_string($_GET['post_type']) ? sanitize_key($_GET['post_type']) : null;
  // 不正なID・未公開記事・異なる投稿タイプ・GET以外のリクエストの除外
  if (cocoon_analytics_is_valid_page_access($post_id, $post_type)) {
    logging_page_access($post_id, $post_type);
  }
}
