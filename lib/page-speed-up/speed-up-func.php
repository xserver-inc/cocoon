<?php //高速化設定関数


//ブラウザキャッシュを有効にするか
define('OP_BROWSER_CACHE_ENABLE', 'browser_cache_enable');
if ( !function_exists( 'is_browser_cache_enable' ) ):
function is_browser_cache_enable(){
  return get_theme_option(OP_BROWSER_CACHE_ENABLE);
}
endif;

//HTMLを縮小化するか
define('OP_HTML_MINTIFY_ENABLE', 'html_mintify_enable');
if ( !function_exists( 'is_html_mintify_enable' ) ):
function is_html_mintify_enable(){
  return get_theme_option(OP_HTML_MINTIFY_ENABLE);
}
endif;

//CSSを縮小化するか
define('OP_CSS_MINTIFY_ENABLE', 'css_mintify_enable');
if ( !function_exists( 'is_css_mintify_enable' ) ):
function is_css_mintify_enable(){
  return get_theme_option(OP_CSS_MINTIFY_ENABLE);
}
endif;

//JSを縮小化するか
define('OP_JS_MINTIFY_ENABLE', 'js_mintify_enable');
if ( !function_exists( 'is_js_mintify_enable' ) ):
function is_js_mintify_enable(){
  return get_theme_option(OP_JS_MINTIFY_ENABLE);
}
endif;

//ソースコードの縮小化
if ( !function_exists( 'code_mintify_call_back' ) ):
function code_mintify_call_back($buffer) {
  if (is_admin()) {
    return $buffer;
  }

  //HTMLの縮小化
  if (is_html_mintify_enable()) {
    $buffer = minify_html($buffer);
  }


  //CSSの縮小化
  if (is_css_mintify_enable()) {
    $pattern = '{<style[^>]*?>(.*?)</style>}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m[1]);
    if ($res && isset($m[1])) {
      foreach ($m[1] as $match) {
        if (empty($match)) {
          continue;
        }
        $buffer = str_replace($match, minify_css($match), $buffer);
        //_v($match);
      }
    }
  }


  //JavaScriptの縮小化
  if (is_js_mintify_enable()) {
    $pattern = '{<script[^>]*?>(.*?)</script>}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m);
    if ($res && isset($m[1])) {
      foreach ($m[1] as $match) {
        if (empty($match)) {
          continue;
        }
        //_v($match);
        $buffer = str_replace($match, minify_js($match), $buffer);
      }
    }
  }

  //_v($buffer);
  return $buffer;
}
endif;

//最終HTML取得開始
add_action('after_setup_theme', 'code_mintify_buffer_start');
if ( !function_exists( 'code_mintify_buffer_start' ) ):
function code_mintify_buffer_start() {
  // _edump(
  //   array('value' => 1, 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
  ob_start('code_mintify_call_back');
}
endif;
//最終HTML取得終了
add_action('shutdown', 'code_mintify_buffer_end');
if ( !function_exists( 'code_mintify_buffer_end' ) ):
function code_mintify_buffer_end() {
  if (ob_get_length()){
    ob_end_flush();
  }

  // _edump(
  //   array('value' => 2, 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
}
endif;




//CSSファイルの縮小化
add_filter( 'style_loader_src', 'mintify_all_css', 9999);
if ( !function_exists( 'mintify_all_css' ) ):
function mintify_all_css( $src ) {
  if (is_css_mintify_enable()) {
    //除外設定
    if (
      //ベーススタイルは除外
      (strpos($src, 'css/base-style.css') !== false) ||
      //アドミンバースタイルのときは除外
      (strpos($src, 'admin-bar.min.css') !== false) ||
      //ダッシュアイコンは除外
      (strpos($src, 'dashicons.min.css') !== false)
    ) {
      return $src;
    }

  //_v($src);
    //読み込まれるCSSファイルがサイト内ファイルであるとき
    if(strpos($src, site_url()) !== false){
      $removed_src = remove_query_arg( 'ver', $src );
      _v($removed_src);
      $local_src = str_replace(site_url(), ABSPATH, $removed_src);
      $local_src = str_replace('//', '/', $local_src);
      $local_src = str_replace('\\', '/', $local_src);
      //_v($local_src);

      if ( WP_Filesystem() && file_exists($local_src) ) {//WP_Filesystemの初期化
        global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
        $css = $wp_filesystem->get_contents($local_src);
        //文字セットの除去
        $css = preg_replace('{@charset[^;]+?;}i', '', $css);
        //コメントの除去
        $css = preg_replace('{/\*.+?\*/}is', '', $css);

        //CSSファイルの置いてあるパス取得
        $css_path = str_replace(basename($removed_src), '', $removed_src);
        //_v($css_path);

        //CSS内容を縮小化して書式を統一化する
        $css = minify_css($css);

        //url(./xxxxxx)をurl(xxxxxx)に統一化
        $css = str_replace('url(./', 'url(', $css);
        $css = str_replace('url(/', 'url(', $css);

        $pattern = '{url\((.+?)\)}i';
        $subject = $css;
        $res = preg_match_all($pattern, $subject, $m);
        if ($res && $m[0] ) {
          foreach ($m[0] as $match) {
            if (
              //url()中にURLが指定されていない
              //url(http://xxxxx)形式でない
              !preg_match('{https?://}i', $match) &&
              //URIスキームの指定ではない
              //url(data:XXXXX)形式でない
              !preg_match('{data:}i', $match)
            ) {
              //url(xxxxx)をurl(http://xxxxx)に変更
              $url = str_replace('url(', 'url('.$css_path, $match);
              //_v($url);
              //縮小化したCSSのurl(xxxxx)を置換
              $css = str_replace($match, $url, $css);
            }
          }
        }


        //_v($m);
        // _v($local_src);
        // _v($css_path);
        // _v($local_src);
        // _v($removed_src);
        // _v(file_exists($local_src));
        //_v($css);
        wp_enqueue_style( 'base-style', get_template_directory_uri() . '/css/base-style.css' );
        wp_add_inline_style( 'base-style', $css );
        $src = null;


      }
    }
  }
    //_v($src);
  return $src;
}
endif;
//add_filter( 'style_loader_src', 'mintify_all_css', 9999);
//add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
