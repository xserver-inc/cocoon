<?php //高速化設定保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ブラウザキャッシュを有効にする
update_theme_option(OP_BROWSER_CACHE_ENABLE);
if (DEBUG_MODE) {
  //ハイスピードモードを有効にするか
  update_theme_option(OP_HIGHSPEED_MODE_ENABLE);
}
//ハイスピードモード除外文字列リスト
update_theme_option(OP_HIGHSPEED_MODE_EXCLUDE_LIST);
//HTMLを縮小化するか
update_theme_option(OP_HTML_MINIFY_ENABLE);
//AMP HTMLを縮小化するか
update_theme_option(OP_HTML_MINIFY_AMP_ENABLE);
//CSSを縮小化するか
update_theme_option(OP_CSS_MINIFY_ENABLE);
//CSSS縮小化除外ファイルリスト
update_theme_option(OP_CSS_MINIFY_EXCLUDE_LIST);
//JSを縮小化するか
update_theme_option(OP_JS_MINIFY_ENABLE);
//JS縮小化除外ファイルリスト
update_theme_option(OP_JS_MINIFY_EXCLUDE_LIST);

//Lazy Load
update_theme_option(OP_LAZY_LOAD_ENABLE);
//Lazy Load除外文字列リスト
update_theme_option(OP_LAZY_LOAD_EXCLUDE_LIST);
//GoogleフォントのLazy Load
update_theme_option(OP_GOOGLE_FONT_LAZY_LOAD_ENABLE);

//preconnect dns-prefetchドメインリスト
update_theme_option(OP_PRE_ACQUISITION_LIST);

//ブラウザキャッシュが有効な時
if (isset($_POST[OP_BROWSER_CACHE_ENABLE])){
  add_browser_cache_to_htaccess();
} else {//ブラウザキャッシュが無効な時
  remove_browser_cache_from_htacccess();
}
