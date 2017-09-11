
(function($){
  /////////////////////////////////
  //管理タブの何番目がクリックされたか
  /////////////////////////////////
  $("#tabs li").click(function () {
    var index = $("#tabs li").index(this);
    $('#select_index').val(index);
  });

})(jQuery);