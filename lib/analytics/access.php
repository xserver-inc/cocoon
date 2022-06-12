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
  $post_id = !empty($_GET['post_id']) ? $_GET['post_id'] : null;
  $post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : null;
  if ($post_id && $post_type) {
    logging_page_access($post_id, $post_type);
  }
}
