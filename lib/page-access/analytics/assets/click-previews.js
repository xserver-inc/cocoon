// 未確認画像の明示操作による読み込み
(function () {
  'use strict';
  document.addEventListener('click', function (event) {
    const button = event.target.closest('.cocoon-click-load-image');
    if (!button || button.disabled) {return;}
    const container = button.closest('.cocoon-click-image-consent');
    if (!container) {return;}
    let url;
    try {url = new URL(button.dataset.imageUrl, document.baseURI);} catch (error) {return;}
    if (!/^https?:$/.test(url.protocol) || url.username || url.password) {return;}
    const error = container.querySelector('.cocoon-click-image-error');
    const wasFocused = document.activeElement === button;
    error.hidden = true;
    button.disabled = true;
    const image = document.createElement('img');
    image.className = 'cocoon-click-thumbnail';
    image.alt = '';
    image.decoding = 'async';
    image.referrerPolicy = 'no-referrer';
    image.addEventListener('load', function () {
      // 操作中のボタン消失によるキーボードフォーカスの喪失防止
      const caption = container.closest('.cocoon-click-link-preview').querySelector('.cocoon-click-link-caption');
      const restoreFocus = wasFocused && (document.activeElement === button || document.activeElement === document.body);
      container.replaceWith(image);
      if (restoreFocus && caption) {caption.focus();}
    }, {once: true});
    image.addEventListener('error', function () {
      error.hidden = false;
      button.disabled = false;
      if (wasFocused && document.activeElement === document.body) {button.focus();}
    }, {once: true});
    // 管理者によるボタン操作後に限定したsrcの設定
    image.src = url.href;
  });
})();
