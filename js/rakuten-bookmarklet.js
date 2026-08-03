javascript: (function() {
  var messagesByLanguage = {
    ja: {
      unsupported: 'Error:ブックマークレットのサポート範囲外のページです。',
      copy: 'コピーしてください。',
      notFound: 'Error:コードが見つかりませんでした。'
    },
    en: {
      unsupported: 'Error: This page is not supported by the bookmarklet.',
      copy: 'Copy the following.',
      notFound: 'Error: The code could not be found.'
    },
    de: {
      unsupported: 'Fehler: Diese Seite wird vom Bookmarklet nicht unterstützt.',
      copy: 'Bitte kopieren.',
      notFound: 'Fehler: Der Code wurde nicht gefunden.'
    },
    es: {
      unsupported: 'Error: Esta página no es compatible con el bookmarklet.',
      copy: 'Copie lo siguiente.',
      notFound: 'Error: No se encontró el código.'
    },
    fr: {
      unsupported: 'Erreur : cette page n’est pas prise en charge par le bookmarklet.',
      copy: 'Copiez le texte suivant.',
      notFound: 'Erreur : le code est introuvable.'
    },
    ko: {
      unsupported: '오류: 북마클릿에서 지원하지 않는 페이지입니다.',
      copy: '복사해 주세요.',
      notFound: '오류: 코드를 찾을 수 없습니다.'
    },
    pt: {
      unsupported: 'Erro: Esta página não é suportada pelo marcador.',
      copy: 'Copie o seguinte.',
      notFound: 'Erro: Não foi possível encontrar o código.'
    },
    'zh-CN': {
      unsupported: '错误：此页面不受书签脚本支持。',
      copy: '请复制以下内容。',
      notFound: '错误：未找到代码。'
    },
    'zh-TW': {
      unsupported: '錯誤：此頁面不受書籤小程式支援。',
      copy: '請複製以下內容。',
      notFound: '錯誤：找不到代碼。'
    }
  };
  var browserLanguage = navigator.language || 'en';
  // 中国語の地域コードと文字体系から、簡体字・繁体字を判定する
  var normalizedBrowserLanguage = browserLanguage.toLowerCase().replace(/_/g, '-');
  var languageKey = normalizedBrowserLanguage.indexOf('zh') === 0
    ? (normalizedBrowserLanguage.indexOf('hant') !== -1 || /(?:^|-)(?:tw|hk|mo)(?:-|$)/.test(normalizedBrowserLanguage) ? 'zh-TW' : 'zh-CN')
    : browserLanguage.slice(0, 2).toLowerCase();
  var messages = messagesByLanguage[languageKey] || messagesByLanguage.en;
  var url = location.href;
  var host = location.host;
  var item_code = null;
  var shortcode = null;
  var title = null;
  var purchase_type = null;
  if(host == 'item.rakuten.co.jp') {
    if (document.querySelector('tr[irc="ItemPriceNormalSubscription"]')) {
      purchase_type = 1;
    }
    var scripts = document.getElementsByTagName('script');
    for(var i = 0; i < scripts.length; i++) {
        var script = scripts[i].innerHTML;
        if (script) {
          var m = script.match(/itemid:\['(.+?)'\],/);
          if(m) {
            item_code = m[1];
          }
        }
    }
    var metas = document.getElementsByTagName('meta');
    for(var i = 0; i < metas.length; i++) {
      if(metas[i].getAttribute('name') == 'twitter:title') {
        title = metas[i].getAttribute('content').replace(/\r?\n|[\[\]]/g, '');
      }
    }
    var purchase_type_code = '';
    if (purchase_type) {
      purchase_type_code = ' purchase_type="1"';
    }
    shortcode = '[rakuten id="' + item_code + '" kw="' + title + '"' + purchase_type_code + ']'
  } else if(host == 'product.rakuten.co.jp') {
    var elements = document.getElementsByClassName('topProduct__specsInfo');
    var code_no = null;
    for(var i = 0; i < elements.length; i++) {
      code_no = elements[i].innerText.trim();
      if(code_no.match(/(EAN|JAN|ISBN|UPC)/)) {
        item_code = code_no.replace(/[A-Z]+?: /, '');
        break;
      }
    }
    var elements = document.getElementsByClassName('topProduct__title');
    for(var i = 0; i < elements.length; i++) {
      var spans = elements[i].getElementsByTagName('span');
      for(var j = 0; j < spans.length; j++) {
        if(spans[j].getAttribute('itemprop') == 'name') {
          title = spans[j].innerText.trim().replace(/\r?\n|[\[\]]/g, '');
        }
      }
    }
    shortcode = '[rakuten no="' + item_code + '" kw="' + title + '"]'
  } else if(url.match(/(books\.rakuten\.co\.jp\/(rb|rk|rd)|(biccamera|brandavenue)\.rakuten\.co\.jp\/item)\//)) {
    var element_shop_code = document.getElementById('ratShopUrl');
    shop_code = element_shop_code.getAttribute('value');
    var element_item_code = document.getElementById('ratItemId');
    item_code = element_item_code.getAttribute('value');
    item_code = item_code.split('/')[1];
    title = document.getElementById('productTitle') || document.getElementsByClassName('p-productDetail__title')[0] || document.getElementsByClassName('item-name')[0];
    title = (title ? title.innerText : '').trim().replace(/\r?\n|[\[\]]/g, '');
    shortcode = '[rakuten id="' + shop_code + ':' + item_code + '" kw="' + title + '"]'
  } else {
    alert(messages.unsupported)
  }
  if(shortcode) {
    if(item_code) {
        if(shortcode = prompt(messages.copy, shortcode)) {
        var r = document.createRange();
        var text = document.createTextNode(shortcode);
        r.selectNode(document.body.appendChild(text));
        window.getSelection().addRange(r);
        document.execCommand('copy');
        text.remove();
      }
    } else alert(messages.notFound)
  }
})();
void(0);
