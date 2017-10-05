(function($){
  //////////////////////////////////////
  // サイドバーの追従
  //////////////////////////////////////
  function get_width($ele, $css) {
    return parseInt($ele.css($css).replace('px', ''));
  }

  function main_other_height() {
    $main = $('.main');
    return get_width($main, 'padding-top') + get_width($main, 'padding-bottom') + get_width($main, 'border-top-width') + get_width($main, 'border-bottom-width');
  }

  function sidebar_other_height() {
    $sidebar = $('.sidebar');
    return get_width($sidebar, 'padding-top') + get_width($sidebar, 'padding-bottom') + get_width($sidebar, 'border-top-width') + get_width($sidebar, 'border-bottom-width');
  }

  function adjust_sidebar_height() {
    $main = $('.main');
    $sidebar = $('.sidebar');
    $main_h = $main.height() + main_other_height();
    $sidebar_h = $sidebar.height() + sidebar_other_height();
    $diff = main_other_height() - sidebar_other_height();

    if ($main_h > $sidebar_h) {
      $sidebar.height($main.height() + $diff);
    }
  }
  adjust_sidebar_height();
  setInterval(function(){
    adjust_sidebar_height();
  },1000);

})(jQuery);