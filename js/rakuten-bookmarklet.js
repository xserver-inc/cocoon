javascript: (function() {
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
    alert('Error:ブックマークレットのサポート範囲外のページです。')
  }
  if(shortcode) {
    if(item_code) {
        if(shortcode = prompt('コピーしてください。', shortcode)) {
        var r = document.createRange();
        var text = document.createTextNode(shortcode);
        r.selectNode(document.body.appendChild(text));
        window.getSelection().addRange(r);
        document.execCommand('copy');
        text.remove();
      }
    } else alert('Error:コードが見つかりませんでした。')
  }
})();
void(0);

