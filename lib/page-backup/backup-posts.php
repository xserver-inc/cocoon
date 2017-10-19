<?php //リセットの実行
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

  $uploaddir = get_theme_cache_dir();
  //$uploaddir = $uploaddir['basedir'].'/';
  $uploadfile = $uploaddir .'/'. basename($_FILES['settings']['name']);

  var_dump($uploadfile);
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