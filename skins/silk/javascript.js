(function ($) {
  //記事本文内スクロール
  $('.entry-content a[href^="#"]')
    .not(".smooth-scroll-off a")
    .click(function () {
      let link = $(this).attr("href");
      let id = $(link);
      if (link !== "#" && id[0]) {
        $("html,body").animate(
          {
            scrollTop: id.offset().top,
          },
          800
        );
        return false;
      }
    });

  //アコーディオン
  $(".is-style-toggle-accordion .toggle-checkbox").click(function () {
    let checked = $(this).prop("checked");
    console.log(checked);
    let tab = $(this).closest(".is-style-toggle-accordion");
    tab.find(".toggle-checkbox").prop("checked", false);
    if (checked) {
      $(this).prop("checked", true);
    }
  });
})(jQuery);
