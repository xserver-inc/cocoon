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
  //ボタン(.go-to-top-button)のクリックイベント
  $('.go-to-top-button').click(function(){
  //ページトップへ移動する
    $('body,html').animate({
            scrollTop: 1
        }, 800);
  });
})(jQuery,document,window);