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

})(jQuery);