<?php //CSS縮小化用
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// CSSファイルとインラインCSSの縮小化
///////////////////////////////////////
if ( !function_exists( 'tag_code_to_minify_css' ) ):
function tag_code_to_minify_css($buffer) {

  if (is_admin()) {
    return $buffer;
  }

  //wpForoページでは縮小化しない（画面が真っ白になる※エラーメッセージは出ないメモリー関係か？）
  if (is_wpforo_plugin_page()) {
    return $buffer;
  }

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
        //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
        $url = preg_replace('/\?.*$/m', '', $url);
        //CSSコード
        $css_inline_code = $m[$code][$i];

        ++$i;

        ////////////////////////////////
        //ファイルタイプのCSSのとき
        ////////////////////////////////
        if ($url) {
          //サイトURLが含まれているものだけ処理
          if (includes_site_url($url)) {
            if (
              //アドミンバースタイルは除外
              // (strpos($url, 'admin-bar.min.css') !== false)
              includes_string($url, 'admin-bar.min.css')
              //ダッシュアイコンは除外
              // || (strpos($url, 'dashicons.min.css') !== false)
              || includes_string($url, 'dashicons.min.css')
              //wpForo除外
              || includes_string($url, '/plugins/wpforo/')
            ) {
              continue;
            }

            //除外リストにマッチするCSS URLは縮小化しない
            if (has_match_list_text($url, get_css_minify_exclude_list())) {
              continue;
            }

            //CSS URLからCSSコードの取得
            $css = css_url_to_css_minify_code( $url );
            //縮小化可能なCSSだったとき
            if ($css !== false) {

              //CSSを縮小化したCSSファイルURL linkタグを削除する
              $buffer = str_replace($tag, '', $buffer);


              $last_minfified_css .= $css;
            }

          } else {

          }//外部URLの場合終了
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
          // //最終出力縮小化CSSコードに縮小化したCSSコードを加える
          // $last_minfified_css .= minify_css($css_inline_code);
          //最終出力縮小化CSSコードにCSSコードを加える
          $last_minfified_css .= $css_inline_code;
          //ヘッダー出力コードからstyleタグを削除
          $buffer = str_replace($tag, '', $buffer);
          //_v($match);
        }//$css


      }//foreach
    }

    //縮小化したCSSをデータの最後に付け加える
    if ($last_minfified_css) {
      $buffer = $buffer.PHP_EOL.'<style>'.minify_css($last_minfified_css).'</style>';
    }

    ///////////////////////////////////////
    // CSSエラー除外
    ///////////////////////////////////////
    //bbPressのCSSエラー
    if (is_bbpress_exist()) {
      $buffer = str_replace('@media screen and (max-device-width:480px),screen and (-webkit-min-device-pixel-ratio:2){-webkit-text-size-adjust:none}', '', $buffer);
    }
    //BuddyPressのCSSエラー
    if (is_buddypress_exist()) {
      $buffer = str_replace('.1s ease-in 0;', '.1s ease-in;', $buffer);
      $buffer = str_replace('#wpadminbar*', '#wpadminbar *', $buffer);
      $buffer = str_replace('*html #wpadminbar', '* html #wpadminbar', $buffer);
      $buffer = str_replace('*html body{', '* html body{', $buffer);
    }


  }//is_css_minify_enable()


  //_v($buffer);
  return apply_filters('tag_code_to_minify_css', $buffer);
}
endif;

//CSS URLからコードを取り出して縮小化コードを返す
if ( !function_exists( 'css_url_to_css_minify_code' ) ):
function css_url_to_css_minify_code( $url ) {
  $css = false;
  //URLファイルをローカルファイルパスに変更
  $local_file = url_to_local($url);

  //if ( WP_Filesystem() && file_exists($local_file) ) {//WP_Filesystemの初期化
  if ( file_exists($local_file) && $css = wp_filesystem_get_contents($local_file) ) {//WP_Filesystemの初期化

    //文字セットの除去
    $css = preg_replace('{@charset[^;]+?;}i', '', $css);
    //コメントの除去
    $css = preg_replace('{/\*.+?\*/}is', '', $css);
    //@importを利用している場合は変換しない
    if (includes_string($css, '@import') && is_amp()) {
      return false;
    }

    //CSSファイルの置いてあるパス取得
    $dir_url = str_replace(basename($url), '', $url);
    //_v($dir_url);

    //CSS内容を縮小化して書式を統一化する
    $css = minify_css($css);

    // //urlにシングルコーテーションやダブルコーテーションが含まれている場合は削除
    // $css = preg_replace('{url\([\'"](.+?)[\'"]\)}', 'url($1)', $css);
    //url(./xxxxxx)をurl(xxxxxx)に統一化
    $searches = ['url(./', 'url(/','url("./', 'url("/', "url('./", "url('/"];
    $replases = ['url(',   'url(', 'url("',   'url("',  "url('",   "url('"];
    $css = str_replace($searches, $replases, $css);
    // $css = str_replace('url(./', 'url(', $css);
    // $css = str_replace('url(/', 'url(', $css);

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
          !preg_match('{data:}i', $match) &&
          //url(#XXXX)形式でない
          !preg_match('{url\(#.+?\)}i', $match)
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
  return apply_filters('css_url_to_css_minify_code', $css, $url);
}
endif;
