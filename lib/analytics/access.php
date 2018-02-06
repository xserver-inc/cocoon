<?php //アクセスをカウントする
require_once('../../../../../wp-load.php');
//_v($_GET);
$post_id = !empty($_GET['post_id']) ? $_GET['post_id'] : null;
$post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : null;
if ($post_id && $post_type) {
  logging_page_access($post_id, $post_type);
}
