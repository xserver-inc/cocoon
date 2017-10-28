
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

$(".carousel-area").on("click", function() {
  $(function(){
    $('.slick-arrow').delay(10).queue(function(){
      $(this).click();
    });
  });
});
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