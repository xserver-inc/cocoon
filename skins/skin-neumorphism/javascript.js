//スクロールしたら影付け
(function() {
  const list = document.querySelectorAll('.a-wrap,.sns-buttons,figure');
  
  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.intersectionRatio > 0) {
        entry.target.classList.add('effect');
        observer.unobserve(entry.target);
      }
    });
  });
  
  Array.prototype.forEach.call(list, function(card) {
    observer.observe(card);
  });
})();

//目次へスクロールで移動
  $(function(){
    // #で始まるアンカーをクリックした場合に処理
    $('a[href^=#]').click(function() {
      // スクロールの速度
      var speed = 800; // ミリ秒
      // アンカーの値取得
      var href= $(this).attr("href");
      // 移動先を取得
      var target = $(href == "#" || href == "" ? 'html' : href);
      // 移動先を数値で取得
      var position = target.offset().top;
      // スムーススクロール
      $('body,html').animate({scrollTop:position}, speed, 'swing');
      return false;
    });
  });