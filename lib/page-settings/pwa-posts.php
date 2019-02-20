<?php //PWA設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PWAを有効にする
update_theme_option(OP_PWA_ENABLE);

//管理者にPWAを有効にする
update_theme_option(OP_PWA_ADMIN_ENABLE);

//PWAアプリ名
update_theme_option(OP_PWA_NAME);

//PWAホーム画面に表示されるアプリ名
update_theme_option(OP_PWA_SHORT_NAME);

//PWAアプリの説明
update_theme_option(OP_PWA_DESCRIPTION);

//PWAテーマカラー
update_theme_option(OP_PWA_THEME_COLOR);

//PWA背景色
update_theme_option(OP_PWA_BACKGROUND_COLOR);

//PWA表示モード
update_theme_option(OP_PWA_DISPLAY);

//PWA画面の向き
update_theme_option(OP_PWA_ORIENTATION);

//PWAが有効な時
if (is_pwa_enable()) {
  $name = get_double_quotation_escape(get_pwa_name());
  $short_name = get_double_quotation_escape(mb_substr(get_pwa_short_name(), 0, 12));
  $description = get_double_quotation_escape(get_pwa_description());
  $start_url = '/';
  //$start_url = '/?utm_source=homescreen&utm_medium=pwa';
  $display = get_pwa_display();
  $orientation = get_pwa_orientation();
  $theme_color = get_pwa_theme_color();
  $background_color = get_pwa_background_color();

  // $icon_url_s = get_site_icon_url(192);
  // if (empty($icon_url_s)) {
  //   $icon_url_s = DEFAULT_SITE_ICON_192;
  // }
  $icon_url_s  = get_site_icon_url_s();
  $icon_size_s = get_site_icon_size_text($icon_url_s);

  // $icon_url_l = get_site_icon_url(512);
  // if (empty($icon_url_l)) {
  //   $icon_url_l = DEFAULT_SITE_ICON_270;
  // }
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

  //service-worker.js
  $service_worker_ver = THEME_NAME.'_20190220_1'; //PWAに変更を加えたらバージョン変更
  $site_logo = get_the_site_logo_url();
  $jquery_core_url = get_jquery_core_url(get_jquery_version());
  $jquery_migrate_url = get_jquery_migrate_url(get_jquery_migrate_version());
  $theme_js_url = THEME_JS_URL;
  $theme_child_js_url = THEME_CHILD_JS_URL;
  $font_awesome4_url = FONT_AWESOME4_URL;
  $font_aicomoon_url = FONT_AICOMOON_URL;

  // '{$jquery_core_url}',
  // '{$jquery_migrate_url}',

  //Service Worker
  $service_worker =
"const CACHE_NAME = '{$service_worker_ver}';
const urlsToCache = [
  '/',
  '{$icon_url_s}',
  '{$icon_url_l}',
  '{$theme_js_url}',
  '{$theme_child_js_url}',
  '{$font_awesome4_url}',
  '{$font_aicomoon_url}',
  '/wp-includes/js/jquery/jquery.js',
  '/wp-includes/js/jquery/jquery-migrate.min.js',
  '/wp-content/themes/cocoon-master/webfonts/fontawesome/fonts/fontawesome-webfont.woff2',
  '/wp-content/themes/cocoon-master/webfonts/icomoon/fonts/icomoon.ttf',
  '/wp-content/themes/cocoon-master/plugins/highlight-js/highlight.min.js'
];

self.addEventListener('install', function(event) {
  // インストール処理
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      // console.log('Opened cache');
      return cache.addAll(urlsToCache);
    })
  );
});

self.addEventListener('activate', function(event) {
  var cacheWhitelist = [CACHE_NAME];

  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(cacheNames.map(function(cacheName) {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
      }));
    })
  );
});

self.addEventListener('fetch', function(event) {

  // 管理画面はキャッシュを使用しない
  if (/\/wp-admin|\/wp-login|preview=true/.test(event.request.url)) {
    return;
  }

  // POSTの場合はキャッシュを使用しない
  if ('POST' === event.request.method) {
    return;
  }

  event.respondWith(
    caches.match(event.request).then(function(response) {
      if (response) {
        return response;
      }

      // リクエストのクローンを作成する
      let ReqClone = event.request.clone();
      return fetch(ReqClone).then(function(response) {
        if (!response ||
            response.status !== 200 ||
            response.type !== 'basic') {
          return response;
        }

        // レスポンスのクローンを作成する
        let ResClone = response.clone();
        caches.open(CACHE_NAME).then(function(cache) {
          cache.put(event.request, ResClone);
        });
        return response;
      });
    })
  );
});";
  //マニフェストファイルの作成
  $service_worker_file = get_theme_pwa_service_worker_js_file();
  wp_filesystem_put_contents($service_worker_file, $service_worker, 0);
  //_v($service_worker);

  //HTTPSリダイレクトの書き込み
  if (file_exists(HTACCESS_FILE)){
    if ($current_htaccess = @wp_filesystem_get_contents(HTACCESS_FILE)){
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
  //.htaccessからリダイレクトルールの削除
  remove_https_rewriterule_from_htacccess();
}
