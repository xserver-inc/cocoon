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

//ハイスピードモードを有効にするか
define('OP_HIGHSPEED_MODE_ENABLE', 'highspeed_mode_enable');
if ( !function_exists( 'is_highspeed_mode_enable' ) ):
function is_highspeed_mode_enable(){
  return DEBUG_MODE && get_theme_option(OP_HIGHSPEED_MODE_ENABLE);
}
endif;

//ハイスピードモード除外文字列リスト
define('OP_HIGHSPEED_MODE_EXCLUDE_LIST', 'highspeed_mode_exclude_list');
if ( !function_exists( 'get_highspeed_mode_exclude_list' ) ):
function get_highspeed_mode_exclude_list(){
  return stripslashes_deep(get_theme_option(OP_HIGHSPEED_MODE_EXCLUDE_LIST));
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
  return false;//get_theme_option(OP_FOOTER_JAVASCRIPT_ENABLE, 1);
}
endif;

// //フッターJavaScript除外ファイルリスト
// define('OP_FOOTER_JAVASCRIPT_EXCLUDE_LIST', 'footer_javascript_exclude_list');
// if ( !function_exists( 'get_footer_javascript_exclude_list' ) ):
// function get_footer_javascript_exclude_list(){
//   return stripslashes_deep(get_theme_option(OP_FOOTER_JAVASCRIPT_EXCLUDE_LIST));
// }
// endif;

//preconnect dns-prefetchドメインリスト
define('OP_PRE_ACQUISITION_LIST', 'pre_acquisition_list');
if ( !function_exists( 'get_pre_acquisition_list' ) ):
function get_pre_acquisition_list(){
  $list = <<<EOF
www.googletagmanager.com
www.google-analytics.com
ajax.googleapis.com
cdnjs.cloudflare.com
pagead2.googlesyndication.com
googleads.g.doubleclick.net
tpc.googlesyndication.com
ad.doubleclick.net
www.gstatic.com
cse.google.com
fonts.gstatic.com
fonts.googleapis.com
cms.quantserve.com
secure.gravatar.com
cdn.syndication.twimg.com
cdn.jsdelivr.net
images-fe.ssl-images-amazon.com
completion.amazon.com
m.media-amazon.com
i.moshimo.com
aml.valuecommerce.com
dalc.valuecommerce.com
dalb.valuecommerce.com
EOF;
  return stripslashes_deep(get_theme_option(OP_PRE_ACQUISITION_LIST, $list));
}
endif;


//文字列内のスクリプトをbarba.js用に取り出して出力する
if ( !function_exists( 'generate_baruba_js_scripts' ) ):
function generate_baruba_js_scripts($tag){
  if (preg_match_all('#<script[^>]*?>([\s\S.]*?)</script>#i', $tag, $m)) {
    //_v($m);
    if (isset($m[1]) && $m[1]) {
      $tags = $m[0];
      $codes = $m[1];
      $i = 0;
      foreach ($codes as $code) {
        if ($code) {//コードの場合
          //_v($code);
          echo $code.PHP_EOL.PHP_EOL.PHP_EOL;
        } else {//スクリプトファイルの場合
          $tag = $tags[$i];
          if (preg_match('#src=[\'"](.+)[\'"]#i', $tag, $n)) {
            // $src = trim($n[1]);
            // $script = '
            //   //$("script[src=\''.$src.'\']").remove();

            //   scriptTag = document.createElement("script");
            //   // scriptTag.defer = true;
            //   // scriptTag.async = true;
            //   scriptTag.src = "'.$src.'";
            //   //document.head.appendChild(scriptTag);
            //   document.getElementsByTagName("body")[0].appendChild(scriptTag);
            // '.PHP_EOL.PHP_EOL.PHP_EOL;
            // //_v($script);
            // echo $script;

            $src = get_query_removed_url($n[1]);
            if (includes_site_url($src)) {
              $src_file = url_to_local($src);
              if (file_exists($src_file)) {
                $script = wp_filesystem_get_contents($src_file);
                echo $script.PHP_EOL.PHP_EOL.PHP_EOL;
              }
            }
          }
        }
        $i++;
      }
    }
  }
}
endif;
