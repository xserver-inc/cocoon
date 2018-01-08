<?php //CSS縮小化用


///////////////////////////////////////
// CSSファイルとインラインCSSの縮小化
///////////////////////////////////////
if ( !function_exists( 'tag_code_to_minify_css' ) ):
function tag_code_to_minify_css($buffer) {

  if (is_css_minify_enable()) {
    //最終出力縮小化CSSコード
    $last_minfified_css = null;

    //CSSファイル
    $link_pattern = '<link[^>]+?stylesheet[^>]+?href=[\'"]([^\'"]+?)[\'"][^>]*?>';
    $style_pattern = '<style[^>]*?>(.*?)</style>';
    $pattern = '{'.$link_pattern.'|'.$style_pattern.'}is';

    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    $all = 0;
    $file = 1;
    $code = 2;
    if ($res && isset($m[$all], $m[$file], $m[$code])) {
      //_v($m);
      $i = 0;

      //return $buffer;
      foreach ($m[$all] as $match) {
        //CSS用のタグ
        $tag = $match;
        //CSSファイルURL
        $url = $m[$file][$i];
        //_v($url);
        //CSSコード
        $css_inline_code = $m[$code][$i];
        //_v($css_inline_code);

        ++$i;

        ////////////////////////////////
        //ファイルタイプのCSSのとき
        ////////////////////////////////
        if ($url) {
          //サイトのURLが含まれているものだけ処理
          if (strpos($url, site_url()) !== false) {
            if (
              //アドミンバースタイルは除外
              (strpos($url, 'admin-bar.min.css') !== false) ||
              //ダッシュアイコンは除外
              (strpos($url, 'dashicons.min.css') !== false)
            ) {
              continue;
            }

            //除外リストにマッチするCSS URLは縮小化しない
            if (is_url_matche_list($url, get_css_minify_exclude_list())) {
              continue;
            }

            // $excludes = list_text_to_array(get_css_minify_exclude_list());
            // foreach ($excludes as $exclude_str) {
            //   if (strpos($url, $exclude_str) !== false) {
            //   _v($url);
            //   _v($exclude_str);
            //   _v(strpos($url, $exclude_str) !== false);
            //     continue;
            //   }
            // }

            //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
            $url = preg_replace('/\?.*$/m', '', $url);
            //_v($url);//CSSコード変換するURL

            //CSS URLからCSSコードの取得
            $css = css_url_to_css_minify_code( $url );
            //縮小化可能ななCSSだと時
            if ($css !== false) {
              //_v($css);//変換したCSSコード

              //CSSを縮小化したCSSファイルURL linkタグを削除する
              $buffer = str_replace($tag, '', $buffer);


              $last_minfified_css .= $css;
            }

          }//strpos($url, site_url()) !== false
        }//$url

        ////////////////////////////////
        //CSSがソースコードのとき
        ////////////////////////////////
        if ($css_inline_code) {
          //除外インラインCSSコード（プリント用のスタイルは除外）
          if (preg_match('{media=[\'"].*?print.*?[\'"]}i', $tag)) {
            continue;
          }

          //空の場合は除外
          if (empty($css_inline_code)) {
            continue;
          }
          //最終出力縮小化CSSコードに縮小化したCSSコードを加える
          $last_minfified_css .= minify_css($css_inline_code);
          //ヘッダー出力コードからstyleタグを削除
          $buffer = str_replace($tag, '', $buffer);
          //_v($match);
        }//$css


      }//foreach
    }
    //_v($m);

    /*
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
    */

    //縮小化したCSSをデータの最後に付け加える
    if ($last_minfified_css) {
      $buffer = $buffer.PHP_EOL.'<style type="text/css">'.$last_minfified_css.'</style>';
    }

  }//is_css_minify_enable()
  /*
  if (is_css_minify_enable()) {
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
          $css = css_url_to_css_minify_code( $url );
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
  }//is_css_minify_enable()
  */
  //_v($buffer);
  return $buffer;
}
endif;

//CSS URLからコードを取り出して縮小化コードを返す
if ( !function_exists( 'css_url_to_css_minify_code' ) ):
function css_url_to_css_minify_code( $url ) {
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



// //CSSファイルの縮小化
// if (is_css_minify_enable()) {
//   //add_filter( 'style_loader_src', 'minify_all_css', 9999);
//   //add_filter( 'print_styles_array', 'minify_all_css', 9999);
// }
// if ( !function_exists( 'minify_all_css' ) ):
// function minify_all_css( $src ) {
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
// //add_filter( 'style_loader_src', 'minify_all_css', 9999);
// //add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );


