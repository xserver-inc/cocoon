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
  //ボタン(.go-to-toc-common)のクリックイベント
  $('.go-to-toc-common').click(function(){
  //目次へ移動する
    $('body,html').animate({
            scrollTop: $('.entry-content .toc').offset().top
        }, 800);
  });

  //検索ボタンクリックでフォーカスを入力エリアに移す
  $('#search-menu-input').change(function(e){
    var searchEdit = $('#search-menu-content .search-edit').first();
    if( e.target.checked ) {
      searchEdit.focus();
    } else {
      searchEdit.blur();
    }
  });

  //下にスクロールで管理パネルを隠す
  //上にスクロールで管理パネルを表示
  var adminMenu = $("#admin-panel");
  var adminHeight = adminMenu.outerHeight();
  var adminStartPos = 0;
  $(window).scroll(function(){
    var adminCurrentPos = $(this).scrollTop();
    if (adminCurrentPos > adminStartPos) {
      if(adminCurrentPos >= 200) {
        adminMenu.css("bottom", "-" + adminHeight + "px");
      }
    } else {
      adminMenu.css("bottom", 0);
    }
    adminStartPos = adminCurrentPos;
  });

  //モバイルボタンが固定じゃない場合
  if (cocoon_localize_script_options.is_fixed_mobile_buttons_enable != 1) {
    //ヘッダーモバイルメニュー
    var headerMenu = $('.mobile-header-menu-buttons');
    var headerHight = headerMenu.outerHeight();
    var headerStartPos = 0;
    $(window).scroll(function() {
      var headerCurrentPos = $(this).scrollTop();
      if ( headerCurrentPos > headerStartPos ) {
        if(headerCurrentPos >= 100) {
          headerMenu.css('top', '-' + headerHight + 'px');
        }
      } else {
        headerMenu.css('top', 0);
      }
      headerStartPos = headerCurrentPos;
    });

    //フッターモバイルメニュー
    var footerMenu = $(".mobile-footer-menu-buttons");
    var footerHeight = footerMenu.outerHeight();
    var footerStartPos = 0;
    $(window).scroll(function(){
      var footerCurrentPos = $(this).scrollTop();
      if (footerCurrentPos > footerStartPos) {
        if(footerCurrentPos >= 100) {
          footerMenu.css("bottom", "-" + footerHeight + "px");
        }
      } else {
        footerMenu.css("bottom", 0);
      }
      footerStartPos = footerCurrentPos;
    });

    var headerButtons = $(".mobile-header-menu-buttons");
    var footerButtons = $(".mobile-footer-menu-buttons");
    headerButtons.click(function() {
      headerButtons.css("z-index", "3");
      footerButtons.css("z-index", "2");
    });

    footerButtons.click(function() {
      headerButtons.css("z-index", "2");
      footerButtons.css("z-index", "3");
    })
  }



  //コメントボタンがクリックされたとき
  $('#comment-reply-btn, .comment-reply-link').click(function() {
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

  //スライドインサイドバーのアーカイブセレクトボックス選択処理
  $('.sidebar-menu-content .widget_archive select').change(function(){
		document.location.href = this.options[this.selectedIndex].value;
  });

  //スライドインサイドバーのカテゴリーセレクトボックス選択処理
  $('.sidebar-menu-content .widget_categories select').change(function(){
    if ( this.options[ this.selectedIndex ].value > 0 ) {
      this.parentNode.submit();
    }
  });

  function drawerCloser(selecter, checkbox){
    $(selecter).click(function() {
      href = $(this).attr('href');
      url = location.href;
      url = url.replace(/#.*$/, '');
      if ((href.indexOf(url) != -1) && href.match(/#/)) {
        $(checkbox).prop('checked', false);
      }
    })
  }

  //モバイルメニューをクリックしたら閉じる
  drawerCloser('.menu-drawer .menu-item a', '#navi-menu-input');

  //モバイルサイドバーをクリックしたら閉じる
  drawerCloser('#slide-in-sidebar a', '#sidebar-menu-input');

  //モバイルヘッダーメニューのロゴ処理
  //console.log($('.mobile-menu-buttons'));
  $('.mobile-menu-buttons').each(function(){
    if ($(this).has('.logo-menu-button').length) {
      $(this).addClass('has-logo-button');
    }
  });

/*
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
*/
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

