
  const CACHE_NAME = 'cocoon_ver_1.0.0';
  const urlsToCache = [
    '/',
    'https://cocoon.local/wp-content/uploads/2018/12/cropped-pexels-photo-1144439-192x192.jpeg',
    'https://cocoon.local/wp-content/uploads/2018/12/cropped-pexels-photo-1144439.jpeg',
    'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.4.1/jquery-migrate.min.js',
    'https://cocoon.local/wp-content/themes/cocoon-master/javascript.js',
    'https://cocoon.local/wp-content/themes/cocoon-child/javascript.js'
  ];

  self.addEventListener('install', function(event) {
    // インストール処理
    event.waitUntil(
      caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
    );
  });

  self.addEventListener('activate', function(event) {
    const cacheWhitelist = [CACHE_NAME];

    event.waitUntil(
      caches.keys().then(function(cacheNames) {
        return Promise.all(
          cacheNames.map(function(cacheName) {
            if (cacheWhitelist.indexOf(cacheName) === -1) {
              return caches.delete(cacheName);
            }
          })
        );
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

    // 管理画面にログイン時はキャッシュを使用しない
    console.log(event);

    event.respondWith(
      caches.match(event.request)
      .then(function(response) {
        // キャッシュがあったら、そのレスポンスを返す
        if (response) {
          return response;
        }

        // 重要：リクエストをcloneする。リクエストはStreamなので
        // 一度しか処理できない。ここではキャッシュ用、fetch用と2回
        // 必要なのでリクエストはcloneしないといけない
        const fetchRequest = event.request.clone();

        return fetch(fetchRequest,{credentials: 'include'}).then(
          function(response) {
            // レスポンスが正しいかをチェック
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // 重要：レスポンスを clone する。レスポンスは Stream で
            // ブラウザ用とキャッシュ用の2回必要。なので clone して
            // 2つの Stream があるようにする
            const responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(function(cache) {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        );
      })
    );
  });
  