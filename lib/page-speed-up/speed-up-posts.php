<?php //高速化設定保存

//ブラウザキャッシュの有効化
update_theme_option(OP_BROWSER_CACHE_ENABLE);
//HTMLを縮小化するか
update_theme_option(OP_HTML_MINTIFY_ENABLE);
//CSSを縮小化するか
update_theme_option(OP_CSS_MINTIFY_ENABLE);
//CSSS縮小化除外ファイルリスト
update_theme_option(OP_CSS_MINTIFY_EXCLUDE_LIST);
//JSを縮小化するか
update_theme_option(OP_JS_MINTIFY_ENABLE);
//JS縮小化除外ファイルリスト
update_theme_option(OP_JS_MINTIFY_EXCLUDE_LIST);

//ブラウザキャッシュが有効な時
if (isset($_POST[OP_BROWSER_CACHE_ENABLE])){
  add_browser_cache_to_htaccess();
} else {//ブラウザキャッシュが無効な時
  remove_browser_cache_from_htacccess();
}
