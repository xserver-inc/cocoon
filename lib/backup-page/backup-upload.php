<?php
//外部のphpからWordpress のAPIを扱う
require_once('../../../../../wp-load.php');
require_once('../_defins.php');
//管理者権限を持っているログインユーザーかどうか
if (is_user_logged_in() && current_user_can('manage_options')) {
  global $wpdb;
  $option_name = 'theme_mods_'.THEME_NAME;
  $res = $wpdb->get_row("
    SELECT * FROM {$wpdb->options}
    WHERE option_name = '{$option_name}';
  ");

  // 4.1.0より前のPHPでは$FILESの代わりに$HTTP_POST_FILESを使用する必要
  // があります。

  $uploaddir = wp_upload_dir();
  var_dump($uploaddir);
  $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

  echo '<pre>';
  if (move_uploaded_file($_FILES['settings']['tmp_name'], $uploadfile)) {
      echo "File is valid, and was successfully uploaded.\n";
  } else {
      echo "Possible file upload attack!\n";
  }

  echo 'Here is some more debugging info:';
  print_r($_FILES);

  print "</pre>";


}
exit;

?>