<?php //アクセスをカウントする
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

require_once('../../../../../wp-load.php');
//_v($_GET);
$post_id = !empty($_GET['post_id']) ? $_GET['post_id'] : null;
$post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : null;
if ($post_id && $post_type) {
  logging_page_access($post_id, $post_type);
}
