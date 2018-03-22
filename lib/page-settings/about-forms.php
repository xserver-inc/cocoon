<div class="metabox-holder">

<!-- テーマ情報 -->
<div id="theme-about" class="postbox">
  <h2 class="hndle"><?php _e( '環境情報', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php echo THEME_NAME_CAMEL; ?><?php _e( '環境に関する情報です。', THEME_NAME ) ?></p>
    <?php
    $sep = '----------------------------------------------'.PHP_EOL;
    $all = $sep;


    //サイト情報
    $all .= __( 'サイト名：', THEME_NAME ).get_bloginfo('name').PHP_EOL;
    $all .= __( 'サイトURL：', THEME_NAME ).home_url().PHP_EOL;
    $all .= __( 'Wordpressバージョン：', THEME_NAME ).get_bloginfo('version').PHP_EOL;
    $all .= __( 'PHPバージョン：', THEME_NAME ).phpversion().PHP_EOL;
    if (isset($_SERVER['HTTP_USER_AGENT']))
      $all .= __( 'ブラウザ：', THEME_NAME ).$_SERVER['HTTP_USER_AGENT'].PHP_EOL;
    if (isset($_SERVER['SERVER_SOFTWARE']))
      $all .= __( 'サーバーソフト：', THEME_NAME ).$_SERVER['SERVER_SOFTWARE'].PHP_EOL;
    if (isset($_SERVER['SERVER_PROTOCOL']))
      $all .= __( 'サーバープロトコル：', THEME_NAME ).$_SERVER['SERVER_PROTOCOL'].PHP_EOL;
    if (isset($_SERVER['HTTP_ACCEPT_CHARSET']))
      $all .= __( '文字セット：', THEME_NAME ).$_SERVER['HTTP_ACCEPT_CHARSET'].PHP_EOL;
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
      $all .= __( 'エンコーディング：', THEME_NAME ).$_SERVER['HTTP_ACCEPT_ENCODING'].PHP_EOL;
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
      $all .= __( '言語：', THEME_NAME ).$_SERVER['HTTP_ACCEPT_LANGUAGE'].PHP_EOL;

    $all .= $sep;

    //親テーマ
    $file = get_template_directory().'/style.css';
    $info = get_theme_info($file);
    if ($info) {
      if (isset($info['theme_name'])) {
        $all .= __( 'テーマ名：', THEME_NAME ).$info['theme_name'].PHP_EOL;
      }
      if (isset($info['version'])) {
        $all .= __( 'バージョン：', THEME_NAME ).$info['version'].PHP_EOL;
      }
      $all .= $sep;
    }

    //子テーマ
    if (is_child_theme()) {
      $file = get_stylesheet_directory().'/style.css';
      $info = get_theme_info($file);
      if ($info) {
        if (isset($info['theme_name'])) {
          $all .= __( '子テーマ名：', THEME_NAME ).$info['theme_name'].PHP_EOL;
        }
        if (isset($info['version'])) {
          $all .= __( 'バージョン：', THEME_NAME ).$info['version'].PHP_EOL;
        }
        $all .= $sep;
      }
    }


    //var_dump($all);
     ?>
    <pre><?php echo $all; ?></pre>
    <p><?php _e( '不具合報告の際には以下の情報を添えてもらうと助かります。', THEME_NAME ) ?></p>
    <textarea style="width: 100%;height: 400px"><?php echo $all; ?></textarea>


  </div>
</div>

</div><!-- /.metabox-holder -->