(function($){
  /////////////////////////////////
  //管理タブの何番目がクリックされたか
  /////////////////////////////////
  $("#tabs > ul > li").click(function () {
    var index = $("#tabs > ul > li").index(this);
    $('#select_index').val(index);
  });



  /////////////////////////////////
  //トグルスイッチ
  /////////////////////////////////
  $('.toggle-link').click(function(){
    $(this).next('.toggle-content').toggle();
  });

  //$(".display-widgets-toggle").insertBefore(".widget-control-save");

  /////////////////////////////////
  //カルーセルエリアの不具合処理（無理やり）
  /////////////////////////////////
  $(".carousel-area").on("click", function() {
    $(function(){
      $('.slick-arrow').delay(10).queue(function(){
        $(this).click();
      });
    });
  });

  function delete_wp_adminbar(selector) {
    $(selector).on('load', function(){
      $(selector).contents().find('#wpadminbar').hide();
      $(selector).contents().find('.admin-panel').hide();
      $(selector).contents().find('html').css({'cssText': 'margin-top: 0px !important;'});
    });
  }
  delete_wp_adminbar('.iframe-demo');
  // delete_wp_adminbar('#all-demo');
  // delete_wp_adminbar('#mobile-demo');
  // delete_wp_adminbar('#page-404-demo');

// $('#all-demo').on('load', function(){
//   $('#all-demo').contents().find('#wpadminbar').hide();
//   $('#all-demo').contents().find('html').css({'cssText': 'margin-top: 0px !important;'});
// });


// $(function(){
//     setInterval(function(){
//         $('.slick-arrow').click();
//     },1000);
// });
// $(function(){

//       if (timer !== false) {
//         clearTimeout(timer);
//       }
//       timer = setTimeout(function(){
//       $('.slick-arrow').click();
//     },1000);
//   });

})(jQuery);