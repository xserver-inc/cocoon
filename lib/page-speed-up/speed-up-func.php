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

//CSSS縮小化除外ファイルリスト
define('OP_CSS_MINTIFY_EXCLUDE_LIST', 'css_mintify_exclude_list');
if ( !function_exists( 'get_css_mintify_exclude_list' ) ):
function get_css_mintify_exclude_list(){
  return get_theme_option(OP_CSS_MINTIFY_EXCLUDE_LIST);
}
endif;

//JSを縮小化するか
define('OP_JS_MINTIFY_ENABLE', 'js_mintify_enable');
if ( !function_exists( 'is_js_mintify_enable' ) ):
function is_js_mintify_enable(){
  return get_theme_option(OP_JS_MINTIFY_ENABLE);
}
endif;

//JS縮小化除外ファイルリスト
define('OP_JS_MINTIFY_EXCLUDE_LIST', 'js_mintify_exclude_list');
if ( !function_exists( 'get_js_mintify_exclude_list' ) ):
function get_js_mintify_exclude_list(){
  return get_theme_option(OP_JS_MINTIFY_EXCLUDE_LIST);
}
endif;

define('HTACCESS_FILE', ABSPATH.'.htaccess');
define('THEME_HTACCESS_BEGIN', '#BEGIN '.THEME_NAME_UPPER.' HTACCESS');
define('THEME_HTACCESS_END',   '#END '  .THEME_NAME_UPPER.' HTACCESS');
define('THEME_HTACCESS_REG', '{\n'.THEME_HTACCESS_BEGIN.'.+?'.THEME_HTACCESS_END.'}s');





//ブラウザキャッシュを.htaccessに追加する
if ( !function_exists( 'add_browser_cache_to_htaccess' ) ):
function add_browser_cache_to_htaccess(){
  if ( WP_Filesystem() ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し

    $resoce_file = 'browser-cache.conf';
    // //リソースファイルがない場合はfalseを返す
    // if (!file_exists($resoce_file)) {
    //   return false;
    // }
    // _v('r');
    ob_start();
    require_once($resoce_file);
    $browser_cache = ob_get_clean();
    $new_browser_cache = THEME_HTACCESS_BEGIN.PHP_EOL.
                         $browser_cache.PHP_EOL.
                         THEME_HTACCESS_END;

    //.htaccessファイルが存在する場合
    if (file_exists(HTACCESS_FILE)) {
      //書き込む前にバックアップファイルを用意する
      $htaccess_backup_file = HTACCESS_FILE.'.'.THEME_NAME;
      if (copy(HTACCESS_FILE, $htaccess_backup_file)) {
        //$wp_filesystemオブジェクトのメソッドとしてファイルを取得する
        $current_htaccess = @$wp_filesystem->get_contents(HTACCESS_FILE);

        //$pattern = '{'.THEME_HTACCESS_BEGIN.'.+?'.THEME_HTACCESS_END.'}s';
        $res = preg_match(THEME_HTACCESS_REG, $current_htaccess, $m);
        //_v($m);

        //テーマファイル用のブラウザキャッシュコードが書き込まれている場合
        if ($res && isset($m[0])) {
          //正規表現にマッチした.htaccessに書き込まれている現在のブラウザキャッシュを取得
          $current_browser_cache = $m[0];
          //現在のブラウザキャッシュと新しいブラウザキャッシュが違えば置換する
          if ($current_browser_cache != $new_browser_cache) {
            //新しいブラウザキャッシュで古いブラウザキャッシュを置換する
            $last_htaccess = str_replace($current_browser_cache, $new_browser_cache, $current_htaccess);
            //ブラウザキャッシュを.htaccessファイルに書き込む
            $wp_filesystem->put_contents(
              HTACCESS_FILE,
               $last_htaccess,
              0644
            );
          } else {
            //新しいブラウザキャッシュと書き込まれたブラウザキャッシュが同じなら何もしない
            //_v('何もしない');
            //$last_htaccess = $current_htaccess;
          }
        } else {//書き込まれていない場合
          //.htaccessにブラウザキャッシュの書き込みがなかった場合には単に追記する
          $last_htaccess = $current_htaccess.PHP_EOL.
                                $new_browser_cache;
          //ブラウザキャッシュを.htaccessファイルに書き込む
          $wp_filesystem->put_contents(
            HTACCESS_FILE,
            $last_htaccess,
            0644
          );
        }
      }

    } else {//.htaccessが存在しない場合
      //.htaccessファイルがない場合は、新しく生成したブラウザキャッシュが最終.htaccess書き込みファイルになる
      $last_htaccess = $new_browser_cache;
      //ブラウザキャッシュを.htaccessファイルに書き込む
      $wp_filesystem->put_contents(
        HTACCESS_FILE,
        $last_htaccess,
        0644
      );
    }//file_exists(HTACCESS_FILE)
    //_v($last_htaccess);;
  }
}
endif;

//.htaccessからブラウザキャッシュコードを削除する
if ( !function_exists( 'remove_browser_cache_from_htacccess' ) ):
function remove_browser_cache_from_htacccess(){
  if ( WP_Filesystem() ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    //.htaccessファイルが存在しているとき
    if (file_exists(HTACCESS_FILE)) {
      $current_htaccess = @$wp_filesystem->get_contents(HTACCESS_FILE);
      $res = preg_match(THEME_HTACCESS_REG, $current_htaccess, $m);
      //書き込まれたブラウザキャッシュが見つかった場合
      if ($res && $m[0]) {
        //正規表現にマッチした.htaccessに書き込まれている現在のブラウザキャッシュを取得
        $current_browser_cache = $m[0];
        //正規表現で見つかったブラウザキャッシュコードを正規表現で削除
        $last_htaccess = str_replace($current_browser_cache, '', $current_htaccess);
        //_v($last_htaccess);
        //ブラウザキャッシュを削除したコードを.htaccessファイルに書き込む
        $wp_filesystem->put_contents(
          HTACCESS_FILE,
          $last_htaccess,
          0644
        );
      }//$res && $[0]
    }//file_exists(HTACCESS_FILE)
  }//WP_Filesystem
}
endif;



// echo('<pre>');
// var_dump($browser_cache_code);
// echo('</pre>');