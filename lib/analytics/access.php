<?php //アクセスをカウントする
require_once('../../../../../wp-load.php');
//_v($_GET);
$id = !empty($_GET['id']) ? $_GET['id'] : null;
$type = !empty($_GET['type']) ? $_GET['type'] : null;
if ($id && $type) {
  logging_page_access($id, $type);
}
