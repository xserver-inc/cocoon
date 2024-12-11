<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- テーマ情報 -->
<div id="theme-about" class="postbox">
  <h2 class="hndle"><?php _e( '環境情報', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php echo THEME_NAME_CAMEL; ?><?php _e( '環境に関する情報です。', THEME_NAME ) ?></p>

    <?php //https環境ではブラウザのクリップボードAPIを利用する
    if (is_ssl()): ?>
      <p><button class="copy-button button"><?php _e( '環境情報をコピー', THEME_NAME ) ?></button></p>
      <div class="copy-info"><?php _e('環境情報をコピーしました', THEME_NAME); ?></div>
    <?php endif; ?>
    <?php
    $sep = '----------------------------------------------'.PHP_EOL;
    $all = $sep;

    //サイト情報
    $all .= __( 'サイト名：', THEME_NAME ).get_bloginfo('name').PHP_EOL;
    $all .= __( 'サイトURL：', THEME_NAME ).site_url().PHP_EOL;
    $all .= __( 'ホームURL：', THEME_NAME ).home_url().PHP_EOL;
    $all .= __( 'コンテンツURL：', THEME_NAME ).get_remove_home_url(content_url()).PHP_EOL;
    $all .= __( 'インクルードURL：', THEME_NAME ).get_remove_home_url(includes_url()).PHP_EOL;
    $all .= __( 'テンプレートURL：', THEME_NAME ).get_remove_home_url(get_template_directory_uri()).PHP_EOL;
    $all .= __( 'スタイルシートURL：', THEME_NAME ).get_remove_home_url(get_stylesheet_directory_uri()).PHP_EOL;
    //親テーマ
    $all .= __( '親テーマスタイル：', THEME_NAME ).get_remove_home_url(PARENT_THEME_STYLE_CSS_URL).PHP_EOL;
    //子テーマ
    if (is_child_theme()) {
      $all .= __( '子テーマスタイル：', THEME_NAME ).get_remove_home_url(CHILD_THEME_STYLE_CSS_URL).PHP_EOL;
    }
    //スキン
    if (get_skin_url()) {
      $all .= __( 'スキン：', THEME_NAME ).get_remove_home_url(get_skin_url()).PHP_EOL;
    }
    $ip = @$_SERVER['REMOTE_ADDR'];
    if ($ip) {
      //IP形式の場合は表示しない
      if (!preg_match('{^[0-9\.]+$}i', $ip)) {
        $host = gethostbyaddr($ip);
        $all .= __( 'サーバー：', THEME_NAME ).$host.PHP_EOL;
      }
    }
    $all .= __( 'WordPressバージョン：', THEME_NAME ).get_bloginfo('version').PHP_EOL;
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
    $file = PARENT_THEME_STYLE_CSS_FILE;
    $info = get_wp_theme_info($file);
    if ($info) {
      if (isset($info['theme_name'])) {
        $all .= __( 'テーマ名：', THEME_NAME ).$info['theme_name'].PHP_EOL;
      }
      if (isset($info['version'])) {
        $all .= __( 'バージョン：', THEME_NAME ).$info['version'].PHP_EOL;
      }
      //カテゴリー数
      $args = array(
        'get' => 'all',
        'hide_empty' => 0
      );
      $categories = get_categories( $args );
      $all .= __( 'カテゴリー数：', THEME_NAME ).count($categories).PHP_EOL;

      $tags = get_tags( $args );
      $all .= __( 'タグ数：', THEME_NAME ).count($tags).PHP_EOL;

      $all .= __( 'ユーザー数：', THEME_NAME ).count(get_users()).PHP_EOL;

      $all .= $sep;
    }

    //子テーマ
    if (is_child_theme()) {
      $file = CHILD_THEME_STYLE_CSS_FILE;
      $info = get_wp_theme_info($file);
      if ($info) {
        if (isset($info['theme_name'])) {
          $all .= __( '子テーマ名：', THEME_NAME ).$info['theme_name'].PHP_EOL;
        }
        if (isset($info['version'])) {
          $all .= __( 'バージョン：', THEME_NAME ).$info['version'].PHP_EOL;
        }

        //CSSサイズ
        $css = wp_filesystem_get_contents($file);
        $all .= __( 'style.cssサイズ：', THEME_NAME ).strlen($css).' '.__( 'バイト', THEME_NAME ).PHP_EOL;

        //functions.phpサイズ
        $functions_file = get_stylesheet_directory().'/functions.php';
        if (file_exists($functions_file)) {
          $php = wp_filesystem_get_contents($functions_file);
          $all .= __( 'functions.phpサイズ：', THEME_NAME ).strlen($php).' '.__( 'バイト', THEME_NAME ).PHP_EOL;
        }
        $all .= $sep;
      }
    }


    //Cocoon設定
    $all .= __( 'Gutenberg：', THEME_NAME ).intval(get_env_info_option_value(OP_GUTENBERG_EDITOR_ENABLE, 1)).PHP_EOL;
    if (is_amp_enable()) {
      $all .= __( 'AMP：', THEME_NAME ).intval(get_env_info_option_value(OP_AMP_ENABLE)).PHP_EOL;
    }
    if (is_pwa_enable()) {
      $all .= __( 'PWA：', THEME_NAME ).intval(get_env_info_option_value(OP_PWA_ENABLE)).PHP_EOL;
    }
    $all .= __( 'Font Awesome：', THEME_NAME ).str_replace('font_awesome_', '', get_env_info_option_value(OP_SITE_ICON_FONT, SITE_ICON_FONT_DEFAULT)).PHP_EOL;
    $all .= __( 'Auto Post Thumbnail：', THEME_NAME ).intval(get_env_info_option_value(OP_AUTO_POST_THUMBNAIL_ENABLE)).PHP_EOL;
    $all .= __( 'Retina：', THEME_NAME ).intval(get_env_info_option_value(OP_RETINA_THUMBNAIL_ENABLE)).PHP_EOL;
    $all .= __( 'ホームイメージ：', THEME_NAME ).get_remove_home_url(get_env_info_option_value(OP_OGP_HOME_IMAGE_URL, OGP_HOME_IMAGE_URL_DEFAULT)).PHP_EOL;
    $all .= $sep;

    //高速化設定
    $all .= __( 'ブラウザキャッシュ有効化：', THEME_NAME ).intval(is_browser_cache_enable()).PHP_EOL;
    $all .= __( 'HTML縮小化：', THEME_NAME ).intval(is_html_minify_enable()).PHP_EOL;
    $all .= __( 'CSS縮小化：', THEME_NAME ).intval(is_css_minify_enable()).PHP_EOL;
    $all .= __( 'JavaScript縮小化：', THEME_NAME ).intval(is_js_minify_enable()).PHP_EOL;
    $all .= __( 'Lazy Load：', THEME_NAME ).intval(is_lazy_load_enable()).PHP_EOL;
    $all .= $sep;

    //plugin.phpを読み込む
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $plugins = get_plugins();
    if (!empty($plugins)) {
      $all .= __('利用中のプラグイン：').PHP_EOL;
      foreach ($plugins as $path => $plugin) {
        if (is_plugin_active( $path )) {
          $all .= $plugin['Name'];
          $all .= ' '.$plugin['Version'].PHP_EOL;
        }
      }
      $all .= $sep;
    }

    //var_dump($all);
     ?>
    <pre class="env-info"><?php echo $all; ?></pre>
    <p><?php _e( '不具合報告の際には上記の情報を添えてもらうと助かります。', THEME_NAME ) ?></p>
    <?php //https環境ではブラウザのクリップボードAPIを利用する
    if (is_ssl()): ?>
      <p><button class="copy-button button"><?php _e( '環境情報をコピー', THEME_NAME ) ?></button></p>
      <div class="copy-info"><?php _e('環境情報をコピーしました', THEME_NAME); ?></div>
      <script>
      (function($){
        const selector = '.copy-button';//clipboardで使う要素を指定
        $(selector).click(function(event){
          //クリック動作をキャンセル
          event.preventDefault();
          //クリップボード動作
          navigator.clipboard.writeText($('.env-info').text()).then(
            () => {
              $('.copy-info').fadeIn(500).delay(1000).fadeOut(500);
            });
        });
      })(jQuery);
      </script>
    <?php endif; ?>
    <!-- <textarea style="width: 100%;height: 400px"><?php echo $all; ?></textarea> -->


  </div>
</div>

</div><!-- /.metabox-holder -->
