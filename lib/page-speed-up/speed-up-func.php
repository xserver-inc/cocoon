<?php //高速化設定関数

require_once 'minfify-css.php';
require_once 'minfify-js.php';
require_once 'minfify-html.php';

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
