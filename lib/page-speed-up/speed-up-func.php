<?php //高速化設定関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

require_once abspath(__FILE__).'minify-css.php';
require_once abspath(__FILE__).'minify-js.php';
require_once abspath(__FILE__).'minify-html.php';

//ブラウザキャッシュを有効にするか
define('OP_BROWSER_CACHE_ENABLE', 'browser_cache_enable');
if ( !function_exists( 'is_browser_cache_enable' ) ):
function is_browser_cache_enable(){
  return get_theme_option(OP_BROWSER_CACHE_ENABLE);
}
endif;

//HTMLを縮小化するか
define('OP_HTML_MINIFY_ENABLE', 'html_minify_enable');
if ( !function_exists( 'is_html_minify_enable' ) ):
function is_html_minify_enable(){
  return get_theme_option(OP_HTML_MINIFY_ENABLE);
}
endif;

//AMP HTMLを縮小化するか
define('OP_HTML_MINIFY_AMP_ENABLE', 'html_minify_amp_enable');
if ( !function_exists( 'is_html_minify_amp_enable' ) ):
function is_html_minify_amp_enable(){
  return get_theme_option(OP_HTML_MINIFY_AMP_ENABLE);
}
endif;

//CSSを縮小化するか
define('OP_CSS_MINIFY_ENABLE', 'css_minify_enable');
if ( !function_exists( 'is_css_minify_enable' ) ):
function is_css_minify_enable(){
  return get_theme_option(OP_CSS_MINIFY_ENABLE);
}
endif;

//CSS縮小化除外ファイルリスト
define('OP_CSS_MINIFY_EXCLUDE_LIST', 'css_minify_exclude_list');
if ( !function_exists( 'get_css_minify_exclude_list' ) ):
function get_css_minify_exclude_list(){
  return stripslashes_deep(get_theme_option(OP_CSS_MINIFY_EXCLUDE_LIST));
}
endif;

//JSを縮小化するか
define('OP_JS_MINIFY_ENABLE', 'js_minify_enable');
if ( !function_exists( 'is_js_minify_enable' ) ):
function is_js_minify_enable(){
  return get_theme_option(OP_JS_MINIFY_ENABLE);
}
endif;

//JS縮小化除外ファイルリスト
define('OP_JS_MINIFY_EXCLUDE_LIST', 'js_minify_exclude_list');
if ( !function_exists( 'get_js_minify_exclude_list' ) ):
function get_js_minify_exclude_list(){
  return stripslashes_deep(get_theme_option(OP_JS_MINIFY_EXCLUDE_LIST));
}
endif;

//Lazy Load
define('OP_LAZY_LOAD_ENABLE', 'lazy_load_enable');
if ( !function_exists( 'is_lazy_load_enable' ) ):
function is_lazy_load_enable(){
  return get_theme_option(OP_LAZY_LOAD_ENABLE);
}
endif;

//Lazy Load除外文字列リスト
define('OP_LAZY_LOAD_EXCLUDE_LIST', 'lazy_load_exclude_list');
if ( !function_exists( 'get_lazy_load_exclude_list' ) ):
function get_lazy_load_exclude_list(){
  return stripslashes_deep(get_theme_option(OP_LAZY_LOAD_EXCLUDE_LIST));
}
endif;

//GoogleフォントのLazy Load
define('OP_GOOGLE_FONT_LAZY_LOAD_ENABLE', 'google_font_lazy_load_enable');
if ( !function_exists( 'is_google_font_lazy_load_enable' ) ):
function is_google_font_lazy_load_enable(){
  return get_theme_option(OP_GOOGLE_FONT_LAZY_LOAD_ENABLE);
}
endif;

//WEBフォントのLazy Load
define('OP_WEB_FONT_LAZY_LOAD_ENABLE', 'web_font_lazy_load_enable');
if ( !function_exists( 'is_web_font_lazy_load_enable' ) ):
function is_web_font_lazy_load_enable(){
  return get_theme_option(OP_WEB_FONT_LAZY_LOAD_ENABLE);
}
endif;

//ブラウザキャッシュを.htaccessに追加する
if ( !function_exists( 'add_browser_cache_to_htaccess' ) ):
function add_browser_cache_to_htaccess(){
  $resoce_file = get_template_directory().'/configs/browser-cache.conf';
  $begin = THEME_HTACCESS_BEGIN;
  $end = THEME_HTACCESS_END;
  $reg = THEME_HTACCESS_REG;
  add_code_to_htaccess($resoce_file, $begin, $end, $reg);
}
endif;

//.htaccessからブラウザキャッシュコードを削除する
if ( !function_exists( 'remove_browser_cache_from_htacccess' ) ):
function remove_browser_cache_from_htacccess(){
  $reg = THEME_HTACCESS_REG;
  remove_code_from_htacccess($reg);
}
endif;

//スクリプトをフッターで読み込む
define('OP_FOOTER_JAVASCRIPT_ENABLE', 'footer_javascript_enable');
if ( !function_exists( 'is_footer_javascript_enable' ) ):
function is_footer_javascript_enable(){
  return get_theme_option(OP_FOOTER_JAVASCRIPT_ENABLE, 1);
}
endif;
