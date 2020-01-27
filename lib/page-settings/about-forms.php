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
    $info = get_theme_info($file);
    if ($info) {
      if (isset($info['theme_name'])) {
        $all .= __( 'テーマ名：', THEME_NAME ).$info['theme_name'].PHP_EOL;
      }
      if (isset($info['version'])) {
        $all .= __( 'バージョン：', THEME_NAME ).$info['version'].PHP_EOL;
      }
      //カテゴリ数
      $args = array(
        'get' => 'all',
        'hide_empty' => 0
      );
      $categories = get_categories( $args );
      $all .= __( 'カテゴリ数：', THEME_NAME ).count($categories).PHP_EOL;

      $tags = get_tags( $args );
      $all .= __( 'タグ数：', THEME_NAME ).count($tags).PHP_EOL;

      $all .= __( 'ユーザー数：', THEME_NAME ).count(get_users()).PHP_EOL;

      $all .= $sep;
    }

    //子テーマ
    if (is_child_theme()) {
      $file = CHILD_THEME_STYLE_CSS_FILE;
      $info = get_theme_info($file);
      if ($info) {
        if (isset($info['theme_name'])) {
          $all .= __( '子テーマ名：', THEME_NAME ).$info['theme_name'].PHP_EOL;
        }
        if (isset($info['version'])) {
          $all .= __( 'バージョン：', THEME_NAME ).$info['version'].PHP_EOL;
        }

        //CSSサイズ
        $css = wp_filesystem_get_contents($file);
        $all .= __( 'style.cssサイズ：', THEME_NAME ).strlen($css).__( 'バイト', THEME_NAME ).PHP_EOL;

        //functions.phpサイズ
        $functions_file = get_stylesheet_directory().'/functions.php';
        if (file_exists($functions_file)) {
          $php = wp_filesystem_get_contents($functions_file);
          $all .= __( 'functions.phpサイズ：', THEME_NAME ).strlen($php).__( 'バイト', THEME_NAME ).PHP_EOL;
        }
        $all .= $sep;
      }
    }

    //Cocoon設定
    $all .= __( 'Gutenberg：', THEME_NAME ).intval(is_gutenberg_editor_enable()).PHP_EOL;
    $all .= __( 'AMP：', THEME_NAME ).intval(is_amp_enable()).PHP_EOL;
    $all .= __( 'PWA：', THEME_NAME ).intval(is_pwa_enable()).PHP_EOL;
    $all .= __( 'Font Awesome：', THEME_NAME ).str_replace('font_awesome_', '', get_site_icon_font()).PHP_EOL;
    $all .= __( 'Auto Post Thumbnail：', THEME_NAME ).intval(is_auto_post_thumbnail_enable()).PHP_EOL;
    $all .= __( 'Retina：', THEME_NAME ).intval(is_retina_thumbnail_enable()).PHP_EOL;
    $all .= __( 'ホームイメージ：', THEME_NAME ).get_remove_home_url(get_ogp_home_image_url()).PHP_EOL;
    $all .= $sep;

    //高速化設定
    $all .= __( 'ブラウザキャッシュ有効化：', THEME_NAME ).intval(is_browser_cache_enable()).PHP_EOL;
    $all .= __( 'HTML縮小化：', THEME_NAME ).intval(is_html_minify_enable()).PHP_EOL;
    $all .= __( 'CSS縮小化：', THEME_NAME ).intval(is_css_minify_enable()).PHP_EOL;
    $all .= __( 'JavaScript縮小化：', THEME_NAME ).intval(is_js_minify_enable()).PHP_EOL;
    $all .= __( 'Lazy Load：', THEME_NAME ).intval(is_lazy_load_enable()).PHP_EOL;
    $all .= __( 'WEBフォントLazy Load：', THEME_NAME ).intval(is_web_font_lazy_load_enable()).PHP_EOL;
    $all .= __( 'JavaScript（フッター）：', THEME_NAME ).intval(is_footer_javascript_enable()).PHP_EOL;
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
    <pre><?php echo $all; ?></pre>
    <p><?php _e( '不具合報告の際には以下の情報を添えてもらうと助かります。', THEME_NAME ) ?></p>
    <textarea style="width: 100%;height: 400px"><?php echo $all; ?></textarea>


  </div>
</div>

</div><!-- /.metabox-holder -->
