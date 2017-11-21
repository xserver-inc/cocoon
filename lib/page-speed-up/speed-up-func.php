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
  $buffer = minify_html($buffer);

  //CSSの縮小化
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

  //JavaScriptの縮小化
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
  //$buffer = minify_js($buffer);
  //$buffer = minify_css($buffer);
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






