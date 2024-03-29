<?php //JavaScript縮小化用
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// JSファイルとインラインJSの縮小化
///////////////////////////////////////
if ( !function_exists( 'tag_code_to_minify_js' ) ):
function tag_code_to_minify_js($buffer) {
  //Aurora Heatmapプラグインの分析画面の場合
  if (isset($_GET['aurora-heatmap'])) {
    return $buffer;
  }

  if (is_admin()) {
    return $buffer;
  }

  if (is_js_minify_enable()) {
    //JSファイルパターン
    $js_file_pattern = '<script[^>]+?javascript[^>]+?src=[\'"]([^\'"]+?)[\'"][^>]*?></script>';
    //JSインラインパターン
    $js_inline_pattern = '<script([^>]*?)>(.*?)</script>';
    //JS正規表現パターン
    $pattern = '{'.$js_file_pattern.'|'.$js_inline_pattern.'}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    $all = 0;  //scriptタグ全体にマッチ
    $flie = 1; //src内のファイルURLにマッチ
    $attr = 2; //scriptタグの属性
    $code = 3; //scriptタグ内のコードにマッチ
    if ($res && isset($m[$all], $m[$flie], $m[$code])) {
      $i = 0;
      foreach ($m[$all] as $match) {
        //scriptタグ全体
        $script_tag = $match;
        //_v($script_tag);
        //JSファイルURL
        $url = $m[$flie][$i];
        //インラインscriptタグの属性群
        $attr_code = $m[$attr][$i];
        //JSコード
        $js_code = $m[$code][$i];
        //_v($script_link_tag);
        ++$i;


        //ファイルタイプのscriptタグだった場合
        if ($url) {//continue;
          //サイトURLが含まれているものだけ処理
          if (includes_site_url($url)) {
            //除外処理
            if (
              //jQueryは除外
              //(strpos($url, 'js/jquery/jquery.js') !== false) ||
              //アドミンバーのJSは除外
              includes_string($url, 'js/admin-bar.min.js')
              //プレイリストのJSは除外
              || includes_string($url, '/mediaelement-and-player.min.js')
              || includes_string($url, '/wp-includes/js/underscore.min.js')
              //WordPress6.7からのエラー対応
              || includes_string($url, '/wp-includes/js/dist/')
              //Stripe Paymentsの除外
              || includes_string($url, 'stripe-payments/')
              //コードハイライト
              || includes_string($url, '/plugins/highlight-js/highlight')
              || includes_string($url, '/plugins/spotlight-master/dist/spotlight')
              || includes_string($url, '/plugins/ip-geo-block/')
              //wpForo除外
              || includes_string($url, '/plugins/wpforo/')
              //Quiz Maker除外
              || includes_string($url, '/plugins/quiz-maker/')
              //wpForoなどでも使われるエディターのスクリプト
              || includes_string($url, '/wp-includes/js/tinymce/')
              //wpForo（1.9.1.2以下）
              || is_buddypress_page()
              //bbPress
              || is_bbpress_page()
              || is_wpforo_plugin_page()
            ) {
              continue;
            }

            //除外リストにマッチするJavascript URLは縮小化しない
            if (has_match_list_text($url, get_js_minify_exclude_list())) {
              continue;
            }

            //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
            $url = preg_replace('/\?.*$/m', '', $url);
            //_v($url);//JSコード変換するURL

            //JS URLからJSコードの取得
            $js = js_url_to_js_minify_code( $url );

            //縮小化可能なJSな時
            if ($js !== false) {
              //_v($js);//変換したJSコード

              //JSを縮小化したJSファイルURL linkタグをインラインにする
              $buffer = str_replace($script_tag, '<script type="text/javascript">'.$js.'</script>', $buffer);

            }//$js

          }//URLが存在するか
        }//url


        //インラインタイプのJavaScriptコードだった場合
        if ($js_code) {
          $js = minify_js($js_code);

          //インラインタイプのscriptタグを縮小化して置換する
          $buffer = str_replace($script_tag, '<script'.$attr_code.'>'.$js.'</script>', $buffer);
        }

      }//foreach
    }//$res && isset($m[1])
  }//is_js_minify_enable()

  //対症療法
  $buffer = str_replace("'s our plugin doing the blocking", '', $buffer);

  return apply_filters('tag_code_to_minify_js', $buffer);
}
endif;


//JavaScript URLからコードを取り出して縮小化コードを返す
if ( !function_exists( 'js_url_to_js_minify_code' ) ):
function js_url_to_js_minify_code( $url ) {
  $js = false;
  //URLファイルをローカルファイルパスに変更
  $local_file = url_to_local($url);

  //if ( WP_Filesystem() && file_exists($local_file) ) {//WP_Filesystemの初期化
  if ( file_exists($local_file) && $js = wp_filesystem_get_contents($local_file) ) {//WP_Filesystemの初期化

    //コメントの除去
    $js = remove_code_comments($js);
    //CSS内容を縮小化して書式を統一化する
    $js = minify_js($js);
    //コード内scriptタグの処理
    $js = str_replace('"<script>"', '"</script"+">"', $js);
    $js = str_replace("'<script>'", "'</script'+'>'", $js);
    $js = str_replace('"</script>"', '"</script"+">"', $js);
    $js = str_replace("'</script>'", "'</script'+'>'", $js);
    $js = preg_replace('{"<([^>]+?)>"}i', '"<$1"+">"', $js);
    $js = preg_replace("{'<([^>]+?)>'}i", "'<$1'+'>'", $js);


  }//WP_Filesystem
  return apply_filters('js_url_to_js_minify_code', $js, $url);
}
endif;

if ( !function_exists( 'remove_code_comments' ) ):
function remove_code_comments($code){
  $code = preg_replace('{/\*.+?\*/}is', '', $code);
  $code = preg_replace('{^\s+//.+$}im', '', $code);
  return apply_filters('remove_code_comments', $code);
}
endif;

/*スクリプトの特性を変更する（async defer ）*/
add_filter( 'script_loader_tag', 'change_script_tag_attrs', 10, 3 );
if ( !function_exists( 'change_script_tag_attrs' ) ):
function change_script_tag_attrs( $tag, $handle, $src ) {
  $async_defers = array(
    // 'jquery',
    // 'jquery-migrate',
    // 'jquery-core',
  );
  $asyncs = array(
    //'crayon_js',
  );
  $defers = array(
    //'code-highlight-js',
  );
  $crossorigin_anonymouses = array(
    //
  );

  if ( in_array( $handle, $async_defers ) ) {
      return '<script async defer src="' . esc_url($src) . '"></script>' . PHP_EOL;
  }
  if ( in_array( $handle, $asyncs ) ) {
      return '<script async src="' . esc_url($src) . '"></script>' . PHP_EOL;
  }
  if ( in_array( $handle, $defers ) ) {
      return '<script defer src="' . esc_url($src) . '"></script>' . PHP_EOL;
  }
  if ( in_array( $handle, $crossorigin_anonymouses ) ) {
      return '<script crossorigin="anonymous" src="' . esc_url($src) . '"></script>' . PHP_EOL;
  }

  return $tag;
}
endif;
