<?php //PWA設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PWAを有効にする
define('OP_PWA_ENABLE', 'pwa_enable');
if ( !function_exists( 'is_pwa_enable' ) ):
function is_pwa_enable(){
  return get_theme_option(OP_PWA_ENABLE);
}
endif;

//管理者にPWAを有効にする
define('OP_PWA_ADMIN_ENABLE', 'pwa_admin_enable');
if ( !function_exists( 'is_pwa_admin_enable' ) ):
function is_pwa_admin_enable(){
  return get_theme_option(OP_PWA_ADMIN_ENABLE);
}
endif;

//PWAアプリ名
define('OP_PWA_NAME', 'pwa_name');
if ( !function_exists( 'get_pwa_name' ) ):
function get_pwa_name(){
  return stripslashes_deep(trim(get_theme_option(OP_PWA_NAME, get_bloginfo('name'))));
}
endif;

//PWAホーム画面に表示されるアプリ名
define('OP_PWA_SHORT_NAME', 'pwa_short_name');
if ( !function_exists( 'get_pwa_short_name' ) ):
function get_pwa_short_name(){
  $default_name = get_simplified_site_name();
  $default_name = $default_name ? $default_name : get_bloginfo('name');
  return stripslashes_deep(trim(get_theme_option(OP_PWA_SHORT_NAME, $default_name)));
}
endif;

//PWAアプリの説明
define('OP_PWA_DESCRIPTION', 'pwa_description');
if ( !function_exists( 'get_pwa_description' ) ):
function get_pwa_description(){
  return stripslashes_deep(trim(get_theme_option(OP_PWA_DESCRIPTION, get_bloginfo('description'))));
}
endif;

//PWAテーマカラー
define('OP_PWA_THEME_COLOR', 'pwa_theme_color');
if ( !function_exists( 'get_pwa_theme_color' ) ):
function get_pwa_theme_color(){
  return get_theme_option(OP_PWA_THEME_COLOR, '#19448e');
}
endif;

//PWA背景色
define('OP_PWA_BACKGROUND_COLOR', 'pwa_background_color');
if ( !function_exists( 'get_pwa_background_color' ) ):
function get_pwa_background_color(){
  return get_theme_option(OP_PWA_BACKGROUND_COLOR, '#ffffff');
}
endif;

//PWA表示モード
define('OP_PWA_DISPLAY', 'pwa_display');
if ( !function_exists( 'get_pwa_display' ) ):
function get_pwa_display(){
  return get_theme_option(OP_PWA_DISPLAY, 'minimal-ui');
}
endif;

//PWA画面の向き
define('OP_PWA_ORIENTATION', 'pwa_orientation');
if ( !function_exists( 'get_pwa_orientation' ) ):
function get_pwa_orientation(){
  return get_theme_option(OP_PWA_ORIENTATION, 'any');
}
endif;

//SサイズのサイトアイコンURLを取得
if ( !function_exists( 'get_site_icon_url_s' ) ):
function get_site_icon_url_s(){
  $icon_url = get_site_icon_url(192);
  if (empty($icon_url)) {
    $icon_url = DEFAULT_SITE_ICON_192;
  }
  return $icon_url;
}
endif;

//LサイズのサイトアイコンURLを取得
if ( !function_exists( 'get_site_icon_url_l' ) ):
function get_site_icon_url_l(){
  $icon_url = get_site_icon_url(512);
  if (empty($icon_url)) {
    $icon_url = DEFAULT_SITE_ICON_270;
  }
  return $icon_url;
}
endif;

//サイトアイコンURLからサイズの取得
if ( !function_exists( 'get_site_icon_size_text' ) ):
function get_site_icon_size_text($url){
  $size = null;
  $res = preg_match('/(\d+?x\d+?)[\.\-]/', $url, $m);
  if (isset($m[1])) {
    $size = $m[1];
  }
  return $size;
}
endif;

//.htaccessにHTTPSリダイレクトルールを書き込む
if ( !function_exists( 'add_https_rewriterule_to_htaccess' ) ):
function add_https_rewriterule_to_htaccess(){
  $resoce_file = get_template_directory().'/configs/https-rewriterule.conf';
  $begin = THEME_HTTPS_REDIRECT_HTACCESS_BEGIN;
  $end = THEME_HTTPS_REDIRECT_HTACCESS_END;
  $reg = THEME_HTTPS_REDIRECT_HTACCESS_REG;
  add_code_to_htaccess($resoce_file, $begin, $end, $reg);
}
endif;

//.htaccessからHTTPSリダイレクトルールを削除する
if ( !function_exists( 'remove_https_rewriterule_from_htacccess' ) ):
function remove_https_rewriterule_from_htacccess(){
  $reg = THEME_HTTPS_REDIRECT_HTACCESS_REG;
  remove_code_from_htacccess($reg);
}
endif;

//PWA関連ファイルの作成
add_action( 'publish_post', 'manage_cocoon_pwa_files');
if ( !function_exists( 'manage_cocoon_pwa_files' ) ):
function manage_cocoon_pwa_files(){
  //PWAが有効な時
  if (is_pwa_enable()) {
    $name = get_double_quotation_escape(get_pwa_name());
    $short_name = get_double_quotation_escape(mb_substr(get_pwa_short_name(), 0, 12));
    $description = get_double_quotation_escape(get_pwa_description());
    $start_url = home_url('/');
    $offline_page = $start_url;
    $display = get_pwa_display();
    $orientation = get_pwa_orientation();
    $theme_color = get_pwa_theme_color();
    $background_color = get_pwa_background_color();

    $icon_url_s  = get_site_icon_url_s();
    $icon_size_s = '192x192';//get_site_icon_size_text($icon_url_s);

    $icon_url_l  = get_site_icon_url_l();
    $icon_size_l = '512x512';//get_site_icon_size_text($icon_url_l);
    $manifest =
"{
\"name\": \"{$name}\",
\"short_name\": \"{$short_name}\",
\"description\": \"{$description}\",
\"start_url\": \"{$start_url}\",
\"display\": \"{$display}\",
\"lang\": \"ja\",
\"dir\": \"auto\",
\"orientation\": \"{$orientation}\",
\"theme_color\": \"{$theme_color}\",
\"background_color\": \"{$background_color}\",
\"icons\": [
  {
    \"src\": \"{$icon_url_s}\",
    \"type\": \"image/png\",
    \"sizes\": \"{$icon_size_s}\"
  },
  {
    \"src\": \"{$icon_url_l}\",
    \"type\": \"image/png\",
    \"sizes\": \"{$icon_size_l}\"
  }
]
}";
    //マニフェストファイルの作成
    $manifest_file = get_theme_pwa_manifest_json_file();
    wp_filesystem_put_contents($manifest_file, $manifest, 0);

    $modified_date = null;
    $args = array(
      'orderby' => 'modified',
      'posts_per_page' => 1,
    );
    $query = new WP_Query( $args );
    if (isset($query->post->post_modified)) {
      $modified_date = '_'.date_i18n('YmdHis', strtotime($query->post->post_modified));
    }
    // $modified_date = '_'.date_i18n('YmdHis');

    //service-worker.js
    $service_worker_ver = THEME_NAME.'_'.PWA_SERVICE_WORKER_VERSION.$modified_date; //PWAに変更を加えたらバージョン変更
    $site_logo = get_the_site_logo_url();
    $jquery_core_url = get_jquery_core_url(get_jquery_version());
    $jquery_migrate_url = get_jquery_migrate_url(get_jquery_migrate_version());
    $theme_js_url = THEME_JS_URL;
    $theme_child_js_url = THEME_CHILD_JS_URL;
    $font_awesome_url = get_site_icon_font_url();
    $font_icomoon_url = FONT_ICOMOON_URL;

    //Service Worker
    //参考：https://github.com/SuperPWA/Super-Progressive-Web-Apps/blob/master/public/sw.php @Super PWA GitHub repository
    $service_worker =
"const CACHE_NAME = '{$service_worker_ver}';
const urlsToCache = [
  '{$start_url}',
  '{$icon_url_s}',
  '{$icon_url_l}',
  '{$theme_js_url}',
  '{$theme_child_js_url}',
  '{$font_awesome_url}',
  '{$font_icomoon_url}'
];

self.addEventListener('install', function(event) {
  // インストール処理
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      console.log('PWA cache opened');
      return cache.addAll(urlsToCache);
    })
  );
});

// Activate
self.addEventListener('activate', function(e) {
	console.log('PWA service worker activation');
	e.waitUntil(
		caches.keys().then(function(keyList) {
			return Promise.all(keyList.map(function(key) {
				if ( key !== CACHE_NAME ) {
					console.log('PWA old cache removed', key);
					return caches.delete(key);
				}
			}));
		})
	);
	return self.clients.claim();
});

// Fetch
self.addEventListener('fetch', function(e) {

  // 管理画面はキャッシュを使用しない
  if (/\/wp-admin|\/wp-login|preview=true/.test(e.request.url)) {
    return;
  }

  // POSTの場合はキャッシュを使用しない
  if ('POST' === e.request.method) {
    return;
  }

	// URLプロトコルがhttpもしくはHTTPSでないときはキャッシュを使用しない
	if ( ! e.request.url.match(/^(http|https):\/\//i) )
		return;

  // リクエストURLが外部ドメインだったときはキャッシュを使用しない
	if ( new URL(e.request.url).origin !== location.origin )
		return;

    // POSTリクエストのとき、Cacheを使用しないときオフラインキャッシュを返す（上にPOST用の処理があるので不要かも）
	if ( e.request.method !== 'GET' ) {
		e.respondWith(
			fetch(e.request).catch( function() {
				return caches.match('{$offline_page}');
			})
		);
		return;
	}

	// Revving strategy
	if ( e.request.mode === 'navigate' && navigator.onLine ) {
		e.respondWith(
			fetch(e.request).then(function(response) {
				return caches.open(CACHE_NAME).then(function(cache) {
					cache.put(e.request, response.clone());
					return response;
				});
			})
		);
		return;
	}

	e.respondWith(
		caches.match(e.request).then(function(response) {
			return response || fetch(e.request).then(function(response) {
				return caches.open(CACHE_NAME).then(function(cache) {
					cache.put(e.request, response.clone());
					return response;
				});
			});
		}).catch(function() {
			return caches.match('{$offline_page}');
		})
	);
});
";
    //サービスワーカーファイルの作成
    $service_worker_file = get_theme_pwa_service_worker_js_file();
    wp_filesystem_put_contents($service_worker_file, $service_worker, 0);
    //_v($service_worker);

    //HTTPSリダイレクトの書き込み
    if (file_exists(get_abs_htaccess_file())){
      if ($current_htaccess = @wp_filesystem_get_contents(get_abs_htaccess_file())){
        //HTTPSリダイレクトの書き込みが.htaccessに存在するか
        $res = preg_match(THEME_HTTPS_REWRITERULE_REG, $current_htaccess, $m);
        //リダイレクト書き込むが存在しない場合
        if (!$res) {
          //SSL化してある場合のみ書き込む
          if (is_ssl()) {
            add_https_rewriterule_to_htaccess();
          } else {
            remove_https_rewriterule_from_htacccess();
          }
        }
      }
    }
  } else {
    //マニフェストファイルの削除
    $manifest_file = get_theme_pwa_manifest_json_file();
    if (file_exists($manifest_file)) {
      wp_delete_file($manifest_file);
    }
    //サービスワーカーファイルの削除
    $service_worker_file = get_theme_pwa_service_worker_js_file();
    if (file_exists($service_worker_file)) {
      wp_delete_file($service_worker_file);
    }
    //.htaccessからリダイレクトルールの削除
    remove_https_rewriterule_from_htacccess();
  }
}
endif;
