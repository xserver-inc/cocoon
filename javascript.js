/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
/////////////////////////////////
// JavaScriptコード
/////////////////////////////////
(function($){
  /////////////////////////////////
  //TOPへ戻るボタン
  /////////////////////////////////
  var prevScrollTop = -1;
  var $window = $(window);
  $window.scroll(function(){
    //最上部から現在位置までの距離を取得して、変数[scrollTop]に格納
    var scrollTop = $window.scrollTop();
    var threashold = 600;
    var s1 = (prevScrollTop > threashold);
    var s2 = (scrollTop > threashold);

    // スレッショルドを跨いだ時のみ処理をする
    if (s1 ^ s2) {
      if (s2) {
        //[.go-to-to]をゆっくりフェードインする
        $('.go-to-top').fadeIn('slow');
      } else {
        //それ以外だったらフェードアウトする
        $('.go-to-top').fadeOut('slow');
      }
    }

    prevScrollTop = scrollTop;
  });
  //ボタン(.go-to-top-common)のクリックイベント
  $('.go-to-top-common').click(function(){
  //ページトップへ移動する
    $('body,html').animate({
            scrollTop: 1
        }, 800);
  });


  //下にスクロールで管理パネルを隠す
  //上にスクロールで管理パネルを表示
  var panel = $("#admin-panel");
  var menuHeight = panel.height()*2;
  var startPos = 0;
  $(window).scroll(function(){
    var currentPos = $(this).scrollTop();
    if (currentPos > startPos) {
      if($(window).scrollTop() >= 200) {
        //console.log(currentPos);
        panel.css("bottom", "-" + menuHeight + "px");
      }
    } else {
      panel.css("bottom", 0 + "px");
    }
    startPos = currentPos;
  });

  //コメントボタンがクリックされたとき
  $('#comment-reply-btn, .comment-reply-link').click(function() {
    //$('#respond').slideToggle();
    $('#comment-reply-btn').slideUp();
    $('#respond').slideDown();
  });

  //カレーセルの表示
  $('.carousel').fadeIn(1000);

  //Google検索ボタン
  $('.sbtn').click(function(){
		var w = $(this).prev('.sform').text();
		if(w) window.open('https://www.google.co.jp/search?q='+encodeURIComponent(w),'_blank');
	});
})(jQuery);

/*
* Cocoon WordPress Theme incorporates code from "Youtube SpeedLoad" WordPress Plugin, Copyright 2017 Alexufo[http://habrahabr.ru/users/alexufo/]
"Youtube SpeedLoad" WordPress Plugin is distributed under the terms of the GNU GPL v2
*/
(function(){
    var f = document.querySelectorAll(".video-click");
    for (var i = 0; i < f.length; ++i) {
      f[i].onclick = function () {
        var iframe = this.getAttribute("data-iframe");
        this.parentElement.innerHTML = '<div class="video">' + iframe + '</div>';
      }
    }
})();
