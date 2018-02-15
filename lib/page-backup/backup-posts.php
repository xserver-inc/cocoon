<?php //リセットの実行
//管理者権限を持っているログインユーザーかどうか
if (is_user_administrator()) {

  require_once(ABSPATH . 'wp-admin/includes/file.php');//WP_Filesystemの使用

  if ( WP_Filesystem() ) {//WP_Filesystemの初期化

    // 4.1.0より前のPHPでは$FILESの代わりに$HTTP_POST_FILESを使用する必要
    // があります。

    //テーマ用のキャッシュディレクトリの取得
    $uploaddir = get_theme_cache_dir();
    //キャシュディレクトリのアップロードファイルパス
    $uploadfile = $uploaddir .'/'. basename($_FILES['settings']['name']);

    //var_dump($uploadfile);
    //echo '<pre>';
    if (move_uploaded_file($_FILES['settings']['tmp_name'], $uploadfile)) {
      //echo "File is valid, and was successfully uploaded.\n";
      global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
      //$wp_filesystemオブジェクトのメソッドとして呼び出す
      $text = $wp_filesystem->get_contents($uploadfile);
      //var_dump($text);
      global $wpdb;
      //$option_name = 'theme_mods_'.THEME_NAME;
      $option_name = get_theme_mods_option_name();
      $wpdb->update(
        $wpdb->options,
        array(
          'option_value' => $text,
        ),
        array( 'option_name' => $option_name ),
        array(
          '%s',
        )
      );
      $wp_filesystem->delete($uploadfile);
    } else {
      //_e( 'レストアできませんでした。', THEME_NAME ):
    }

  // echo 'Here is some more debugging info:';
  //var_dump($_FILES);

  // print "</pre>";


  }
}