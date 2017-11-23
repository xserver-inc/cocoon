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
  // _edump(
  //   array('value' => 'code_mintify_call_back', 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
  if (is_admin()) {
    return $buffer;
  }

  //HTMLの縮小化
  if (is_html_mintify_enable()) {
    $buffer = minify_html($buffer);
  }

  /*
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
  */

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
  //   array('value' => 'code_mintify_buffer_end', 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
}
endif;




// //CSSファイルの縮小化
// if (is_css_mintify_enable()) {
//   //add_filter( 'style_loader_src', 'mintify_all_css', 9999);
//   //add_filter( 'print_styles_array', 'mintify_all_css', 9999);
// }
// if ( !function_exists( 'mintify_all_css' ) ):
// function mintify_all_css( $src ) {
//   //除外設定
//   if (
//     //ベーススタイルは除外
//     (strpos($src, 'css/base-style.css') !== false) ||
//     //アドミンバースタイルのときは除外
//     (strpos($src, 'admin-bar.min.css') !== false) ||
//     //ダッシュアイコンは除外
//     (strpos($src, 'dashicons.min.css') !== false)
//   ) {
//     return $src;
//   }

// //_v($src);
//   //読み込まれるCSSファイルがサイト内ファイルであるとき
//   if(strpos($src, site_url()) !== false){
//     $removed_src = remove_query_arg( 'ver', $src );
//     _v($removed_src);
//     $local_src = str_replace(site_url(), ABSPATH, $removed_src);
//     $local_src = str_replace('//', '/', $local_src);
//     $local_src = str_replace('\\', '/', $local_src);
//     //_v($local_src);

//     if ( WP_Filesystem() && file_exists($local_src) ) {//WP_Filesystemの初期化
//       global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
//       $css = $wp_filesystem->get_contents($local_src);
//       //文字セットの除去
//       $css = preg_replace('{@charset[^;]+?;}i', '', $css);
//       //コメントの除去
//       $css = preg_replace('{/\*.+?\*/}is', '', $css);

//       //CSSファイルの置いてあるパス取得
//       $css_path = str_replace(basename($removed_src), '', $removed_src);
//       //_v($css_path);

//       //CSS内容を縮小化して書式を統一化する
//       $css = minify_css($css);

//       //url(./xxxxxx)をurl(xxxxxx)に統一化
//       $css = str_replace('url(./', 'url(', $css);
//       $css = str_replace('url(/', 'url(', $css);

//       $pattern = '{url\((.+?)\)}i';
//       $subject = $css;
//       $res = preg_match_all($pattern, $subject, $m);
//       if ($res && $m[0] ) {
//         foreach ($m[0] as $match) {
//           if (
//             //url()中にURLが指定されていない
//             //url(http://xxxxx)形式でない
//             !preg_match('{https?://}i', $match) &&
//             //URIスキームの指定ではない
//             //url(data:XXXXX)形式でない
//             !preg_match('{data:}i', $match)
//           ) {
//             //url(xxxxx)をurl(http://xxxxx)に変更
//             $url = str_replace('url(', 'url('.$css_path, $match);
//             //_v($url);
//             //縮小化したCSSのurl(xxxxx)を置換
//             $css = str_replace($match, $url, $css);
//           }
//         }
//       }


//       //_v($m);
//       // _v($local_src);
//       // _v($css_path);
//       // _v($local_src);
//       // _v($removed_src);
//       // _v(file_exists($local_src));
//       //_v($css);
//       wp_enqueue_style( 'base-style', get_template_directory_uri() . '/css/base-style.css' );
//       wp_add_inline_style( 'base-style', $css );
//       $src = null;


//     }
//   }
//   //_v($src);
//   return $src;
// }
// endif;
// //add_filter( 'style_loader_src', 'mintify_all_css', 9999);
// //add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );


///////////////////////////////////////
// 出力フィルタリングフック
///////////////////////////////////////

// wp_head 出力フィルタリング・ハンドラ追加
add_action( 'wp_head', 'wp_head_buffer_start', 1 );
add_action( 'wp_head', 'wp_head_buffer_end', 9999 );
// wp_footer 出力フィルタリング・ハンドラ追加
add_action( 'wp_footer', 'wp_footer_buffer_start', 1 );
add_action( 'wp_footer', 'wp_footer_buffer_end', 9999 );


///////////////////////////////////////
// バッファリング開始
///////////////////////////////////////
if ( !function_exists( 'wp_head_buffer_start' ) ):
function wp_head_buffer_start() {
  ob_start( 'wp_head_minify' );
}
endif;
if ( !function_exists( 'wp_footer_buffer_start' ) ):
function wp_footer_buffer_start() {
  ob_start( 'wp_footer_minify' );
}
endif;

///////////////////////////////////////
// バッファリング終了
///////////////////////////////////////
if ( !function_exists( 'wp_head_buffer_end' ) ):
function wp_head_buffer_end() {
  if (ob_get_length()) ob_end_flush();
}
endif;
if ( !function_exists( 'wp_footer_buffer_end' ) ):
function wp_footer_buffer_end() {
  if (ob_get_length())  ob_end_flush();
}
endif;


///////////////////////////////////////
// フィルター
///////////////////////////////////////
if ( !function_exists( 'wp_head_minify' ) ):
function wp_head_minify($buffer) {

  //ヘッダーコードのCSS縮小化
  if (is_css_mintify_enable()) {
    $buffer = tag_code_to_mintify_css($buffer);
  }

  //ヘッダーコードのJS縮小化
  if (is_js_mintify_enable()) {
    $buffer = tag_code_to_mintify_js($buffer);
  }

  _v($buffer);
  return $buffer;
}
endif;

if ( !function_exists( 'wp_footer_minify' ) ):
function wp_footer_minify($buffer) {

  //ヘッダーコードのCSS縮小化
  if (is_css_mintify_enable()) {
    //$buffer = tag_code_to_mintify_css($buffer);
  }

  //ヘッダーコードのJS縮小化
  if (is_js_mintify_enable()) {
    //$buffer = tag_code_to_mintify_js($buffer);
  }

  //_v($buffer);
  return $buffer;
}
endif;


///////////////////////////////////////
// CSSファイルとインラインCSSの縮小化
///////////////////////////////////////
if ( !function_exists( 'tag_code_to_mintify_css' ) ):
function tag_code_to_mintify_css($buffer) {
  if (is_css_mintify_enable()) {
    //最終出力縮小化CSSコード
    $last_minfified_css = null;

    //CSSファイル
    $pattern = '{<link.+?stylesheet.+?href=[\'"]([^\'"]+?)[\'"][^>]+?>}i';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    if ($res && isset($m[1])) {
      $i = 0;
      foreach ($m[1] as $match) {
        //CSSファイルURL
        $url = $match;
        //CSSファイル読み込みタグ（<link rel='stylesheet'href='http://xxx/style.css?ver=4.9' type='text/css' media='all' />）
        $style_link_tag = $m[0][$i];
        //_v($link_tag);
        ++$i;

        //サイトのURLが含まれているものだけ処理
        if (strpos($url, site_url()) !== false) {
          if (
            //アドミンバースタイルは除外
            (strpos($url, 'admin-bar.min.css') !== false) ||
            //ダッシュアイコンは除外
            (strpos($url, 'dashicons.min.css') !== false)) {
            continue;
          }
          //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
          $url = preg_replace('/\?.*$/m', '', $url);
          //_v($url);//CSSコード変換するURL

          //CSS URLからCSSコードの取得
          $css = css_url_to_css_mintify_code( $url );
          //縮小化可能ななCSSだと時
          if ($css) {
            //_v($css);//変換したCSSコード

            //CSSを縮小化したCSSファイルURL linkタグを削除する
            $buffer = str_replace($style_link_tag, '', $buffer);


            $last_minfified_css .= $css;
          }

        }

      }
    }
    //_v($m);

    //CSSインラインスタイル
    $pattern = '{<style[^>]*?>(.*?)</style>}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m[1]);
    if ($res && isset($m[1])) {
      $i = 0;
      foreach ($m[1] as $match) {
        //CSSコード
        $css = $match;
        //CSSタグ（<style type="text/css"></style>）
        $style_tag = $m[0][$i];
        //_v($style_tag);
        ++$i;

        //除外インラインCSSコード（プリント用のスタイルは除外）
        if (preg_match('{media=[\'"].*?print.*?[\'"]}i', $style_tag)) {
          continue;
        }

        //空の場合は除外
        if (empty($css)) {
          continue;
        }
        //最終出力縮小化CSSコードに縮小化したCSSコードを加える
        $last_minfified_css .= minify_css($css);
        //ヘッダー出力コードからstyleタグを削除
        $buffer = str_replace($style_tag, '', $buffer);
        //_v($match);
      }
    }
    //縮小化したCSSをデータの最後に付け加える
    $buffer = $buffer.PHP_EOL.'<style type="text/css">'.$last_minfified_css.'</style>';
  }//is_css_mintify_enable()

  return $buffer;
}
endif;

///////////////////////////////////////
// JSファイルとインラインJSの縮小化
///////////////////////////////////////
if ( !function_exists( 'tag_code_to_mintify_js' ) ):
function tag_code_to_mintify_js($buffer) {
  if (is_js_mintify_enable()) {
    //最終出力縮小化JSコード
    $last_minfified_js = null;

    //JSファイル
    $pattern = '{<script.+?javascript.+?src=[\'"]([^\'"]+?)[\'"].*?>.*?</script>}i';//[^>]*?
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m);
    if ($res && isset($m[1])) {
      $i = 0;
      foreach ($m[1] as $match) {
        //JSファイルURL
        $url = $match;
        //JSファイル読み込みタグ（<script type='text/javascript' src='http://xxx/jquery-migrate.min.js?ver=1.4.1'></script>）
        $script_link_tag = $m[0][$i];
        //_v($script_link_tag);
        ++$i;

        //サイトのURLが含まれているものだけ処理
        if (strpos($url, site_url()) !== false) {
          //除外処理
          if (
            //jQueryは除外
            (strpos($url, 'js/jquery/jquery.js') !== false) ||
            (strpos($url, 'plugins/highlight-js/highlight.min.js') !== false) ||
            (strpos($url, 'plugins/baguettebox/dist/baguetteBox.min.js') !== false) ||
            (strpos($url, 'plugins/stickyfill/dist/stickyfill.min.js') !== false) ||
            (strpos($url, 'plugins/slick/slick.min.js') !== false) ||
            (strpos($url, 'js/jquery/jquery.js') !== false) ||
            //アドミンバーのJSは除外
            (strpos($url, 'js/admin-bar.min.js') !== false) //||
            //jQueryマイグレートは除外
            //(strpos($url, 'js/jquery/jquery-migrate.min.js ') !== false)
          ) {
            continue;
          }

          //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
          $url = preg_replace('/\?.*$/m', '', $url);
          _v($url);//JSコード変換するURL

          //JS URLからJSコードの取得
          $js = js_url_to_js_mintify_code( $url );
          //縮小化可能ななJSだと時
          if ($js) {
            //_v($js);//変換したJSコード

            //JSを縮小化したJSファイルURL linkタグを削除する
            $buffer = str_replace($script_link_tag, '', $buffer);


            $last_minfified_js .= $js;
          }//$js

        }//strpos($url, site_url()) !== false

      }//foreach
    }//$res && isset($m[1])


    //JSインラインスタイル
    $pattern = '{<script[^>]*?>(.*?)</script>}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m);
    if ($res && isset($m[1])) {
      $i = 0;
      foreach ($m[1] as $match) {
        //jsコード
        $js = $match;
        //_v($js);
        //JSタグ（<script type="text/javascript"></script>）
        $script_tag = $m[0][$i];
        //_v($script_tag);
        ++$i;
        //_v($match);
        if (empty($js)) {
          continue;
        }

        //最終出力縮小化JSコードに縮小化したJSコードを加える
        $last_minfified_js .= minify_css($js);
        //ヘッダー出力コードからscriptタグを削除
        $buffer = str_replace($script_tag, '', $buffer);
      }
    }

    //縮小化したJavaScriptをデータの最後に付け加える
    $buffer = $buffer.PHP_EOL.'<script type="text/javascript">'.$last_minfified_js.'</script>';
  }//is_js_mintify_enable()
  return $buffer;
}
endif;

if ( !function_exists( 'css_url_to_css_mintify_code' ) ):
function css_url_to_css_mintify_code( $url ) {
  $css = false;
  //URLファイルをローカルファイルパスに変更
  $local_file = url_to_local($url);
  // $local_file = str_replace(site_url(), ABSPATH, $url);
  // $local_file = str_replace('//', '/', $local_file);
  // $local_file = str_replace('\\', '/', $local_file);
  //_v($local_file);

  if ( WP_Filesystem() && file_exists($local_file) ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    $css = $wp_filesystem->get_contents($local_file);

    //文字セットの除去
    $css = preg_replace('{@charset[^;]+?;}i', '', $css);
    //コメントの除去
    $css = preg_replace('{/\*.+?\*/}is', '', $css);
    //@importを利用している場合は変換しない
    if (strpos($css, '@import') !== false) {
      return false;
    }

    //CSSファイルの置いてあるパス取得
    $dir_url = str_replace(basename($url), '', $url);
    //_v($dir_url);

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
          $url = str_replace('url(', 'url('.$dir_url, $match);
          //_v($url);
          //縮小化したCSSのurl(xxxxx)を置換
          $css = str_replace($match, $url, $css);
        }
      }//foreach
    }//$res && $m[0]
  }//WP_Filesystem
  return $css;
}
endif;


if ( !function_exists( 'js_url_to_js_mintify_code' ) ):
function js_url_to_js_mintify_code( $url ) {
  $js = false;
  //URLファイルをローカルファイルパスに変更
  $local_file = url_to_local($url);
  //_v($local_file);

  if ( WP_Filesystem() && file_exists($local_file) ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    $js = $wp_filesystem->get_contents($local_file);

    //CSS内容を縮小化して書式を統一化する
    $js = minify_css($js);

  }//WP_Filesystem
  return $js;
}
endif;