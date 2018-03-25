<?php //HTML縮小化用


//HTMLソースコードの縮小化
if ( !function_exists( 'code_minify_call_back' ) ):
function code_minify_call_back($buffer) {
  // _edump(
  //   array('value' => 'code_minify_call_back', 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
  if (is_admin()) {
    return $buffer;
  }

  // if (is_amp() && !is_html_minify_amp_enable()) {
  //   return $buffer;
  // }

  //何故かa開始タグがpタグでラップされるのを修正
  $buffer = preg_replace('{<p>(<a[^>]+?>)</p>}i', "$1", $buffer);
  //何故かa終了タグがpタグでラップされるのを修正
  $buffer = preg_replace('{<p>(</a>)</p>}i', "$1", $buffer);

  //_v('$buffer');
  //HTMLの縮小化
  if (is_html_minify_enable()) {
    $buffer = minify_html($buffer);
  }

  // if (is_amp() && is_js_minify_enable()) {
  //   $buffer = minify_js($buffer);
  // }

  //「Warning: Attribute aria-required is unnecessary for elements that have attribute required.」対策
  $buffer = str_replace('aria-required="true" required>', 'aria-required="true">', $buffer);
  $buffer = str_replace('aria-required="true" required="required">', 'aria-required="true">', $buffer);

  // //Wordpressが出力する type='text/javascript'を削除
  // $buffer = str_replace(" type='text/javascript'", '', $buffer);
  // $buffer = str_replace(' type="text/javascript"', '', $buffer);
  // // $buffer = str_replace("<script type='text/javascript'", '<script', $buffer);
  // // $buffer = str_replace('<script type="text/javascript"', '<script', $buffer);

  // //Wordpressが出力する type='text/css'を削除
  // $buffer = str_replace(" type='text/css'", '', $buffer);
  // $buffer = str_replace(' type="text/css"', '', $buffer);
  // // $buffer = str_replace("<style type='text/css'", '<style', $buffer);
  // // $buffer = str_replace('<style type="text/css"', '<style', $buffer);
  // // $buffer = str_replace(" type='text/css'>", '>', $buffer);
  // // $buffer = str_replace(' type="text/css">', '>', $buffer);

  ///////////////////////////////////////
  // HTML5エラー除外
  ///////////////////////////////////////
  //Alt属性がないIMGタグにalt=""を追加する
  $buffer = preg_replace('/<img((?![^>]*alt=)[^>]*)>/i', '<img alt=""${1}>', $buffer);
  //画像タグの border="0"を削除する
  $buffer = str_replace(' border="0"', '', $buffer);

  //wpForoのHTML5エラー
  if (is_wpforo_exist()) {
    $buffer = str_replace(' id="wpf-widget-recent-replies"', '', $buffer);
  }
  //BuddyPressのHTML5エラー
  if (is_buddypress_exist()) {
    //$buffer = str_replace('<input name="rememberme" type="checkbox" value="forever" />', '<input id="bp-login-widget-rememberme" name="rememberme" type="checkbox" value="forever" />', $buffer);
    $buffer = str_replace('<label for="bp-login-widget-rememberme">', '<label>', $buffer);
  }

  ///////////////////////////////////////
  // CSSのダイエット（遅い）
  ///////////////////////////////////////
  // if (is_css_minify_enable()) {
  // $body_tag = null;
  //   //ボディータグの取得
  //   if (preg_match('{<body .+</html>}is', $buffer, $m)) {
  //     if (isset($m[0])) {
  //       $body_tag = $m[0];
  //     }
  //   }

  //   //_v(strlen($buffer));
  //   //CSSスタイルの取得
  //   if (preg_match('{<style>.+</style>}is', $buffer, $m)) {
  //     if (isset($m[0])) {
  //       $default_style_tag = minify_css($m[0]);
  //       //_v($default_style_tag);
  //       //_v(strlen($default_style_tag));
  //       // $time_start = microtime(true);
  //       //不要なCSSを削除してサイズ削減
  //       $dieted_style_tag = get_dieted_amp_css_tag($default_style_tag, $body_tag);
  //       // $time = microtime(true) - $time_start;
  //       // var_dump($time);
  //       // var_dump('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
  //       //_v(strlen($dieted_style_tag));
  //       //_v($dieted_style_tag);
  //       //ヘッダーの<style>をサイズ削減したものに入れ替える
  //       // $buffer = str_replace($default_style_tag, $dieted_style_tag, $buffer);
  //       // $s = $buffer;
  //       // _v(strlen($s));
  //     }
  //   }
  // }


  /*
  //CSSの縮小化
  if (0&&is_css_minify_enable()) {
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


  */
  //JavaScriptの縮小化
  if (is_amp() && is_js_minify_enable()) {
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
add_action('after_setup_theme', 'code_minify_buffer_start', 99999999);
if ( !function_exists( 'code_minify_buffer_start' ) ):
function code_minify_buffer_start() {
  // _edump(
  //   array('value' => 1, 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
  ob_start('code_minify_call_back');
}
endif;
//最終HTML取得終了
add_action('shutdown', 'code_minify_buffer_end');
if ( !function_exists( 'code_minify_buffer_end' ) ):
function code_minify_buffer_end() {
  if (ob_get_length()){
    ob_end_flush();
  }

  // _edump(
  //   array('value' => 'code_minify_buffer_end', 'file' => __FILE__, 'line' => __LINE__),
  //   'label', 'tag', 'ade5ac'
  // );
}
endif;


///////////////////////////////////////
// 出力フィルタリングフック
///////////////////////////////////////

// wp_head 出力フィルタリング・ハンドラ追加
add_action( 'wp_head', 'wp_head_buffer_start', 1 );
add_action( 'wp_head', 'wp_head_buffer_end', 99999999 );
// wp_footer 出力フィルタリング・ハンドラ追加
add_action( 'wp_footer', 'wp_footer_buffer_start', 1 );
add_action( 'wp_footer', 'wp_footer_buffer_end', 99999999 );


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
  if (is_css_minify_enable()) {
    $buffer = tag_code_to_minify_css($buffer);
  }

  //ヘッダーコードのJS縮小化
  if (is_js_minify_enable()) {
    $buffer = tag_code_to_minify_js($buffer);
  }
  //Wordpressが出力する type='text/javascript'を削除
  $buffer = str_replace(" type='text/javascript'", '', $buffer);
  $buffer = str_replace(' type="text/javascript"', '', $buffer);
  //Wordpressが出力する type='text/css'を削除
  $buffer = str_replace(" type='text/css'", '', $buffer);
  $buffer = str_replace(' type="text/css"', '', $buffer);

  //_v($buffer);
  return $buffer;
}
endif;

if ( !function_exists( 'wp_footer_minify' ) ):
function wp_footer_minify($buffer) {

  //フッターコードのCSS縮小化
  if (is_css_minify_enable()) {
    $buffer = tag_code_to_minify_css($buffer);
  }

  //フッターコードのJS縮小化
  if (is_js_minify_enable()) {
    $buffer = tag_code_to_minify_js($buffer);
  }

  //Wordpressが出力する type='text/javascript'を削除
  $buffer = str_replace(" type='text/javascript'", '', $buffer);
  $buffer = str_replace(' type="text/javascript"', '', $buffer);

  //_v($buffer);
  return $buffer;
}
endif;

if ( !function_exists( 'is_url_matche_list' ) ):
function is_url_matche_list($url, $list){
  //除外リストにマッチするCSS URLは縮小化しない
  $excludes = list_text_to_array($list);
  foreach ($excludes as $exclude_str) {
    if (strpos($url, $exclude_str) !== false) {
    // _v($url);
    // _v($exclude_str);
    // _v(strpos($url, $exclude_str) !== false);
      return true;
    }
  }
}
endif;