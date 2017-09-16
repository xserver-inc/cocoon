
(function($){
  /////////////////////////////////
  //管理タブの何番目がクリックされたか
  /////////////////////////////////
  $("#tabs > ul > li").click(function () {
    var index = $("#tabs > ul > li").index(this);
    $('#select_index').val(index);
  });



    $('.toggle-link').click(function(){
      $(this).next('.toggle-content').toggle();
    });

})(jQuery);