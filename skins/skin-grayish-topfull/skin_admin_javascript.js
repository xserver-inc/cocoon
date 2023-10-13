
// ---------------------------------------------
// Cocoon設定のプレビュー　iframe内ではアドミンバーなしに見せる
// ref:cocoon-master/js/admin-javascript.js
// ---------------------------------------------

(function ($) {
  function delete_wp_adminbar_skinadd(selector) {
    $(selector).on('load', function () {
      $(selector).contents().find('.skin-grayish.front-top-page.admin-bar .navi').css({ 'cssText': 'top: 0px;' });
      $(selector).contents().find('.skin-grayish:not(.front-top-page).admin-bar .header-container').css({ 'cssText': 'top: 0px;display: block;' });
      $(selector).contents().find('.skin-grayish.admin-bar .widget-sidebar-scroll:first-child').css({ 'cssText': 'padding-top: var(--ohterHeaderLogosize);' });
      $(selector).contents().find('.skin-grayish.admin-bar:where(.mblt-header-mobile-buttons, .mblt-header-and-footer-mobile-buttons) .mobile-header-menu-buttons.mobile-menu-buttons').css({ 'cssText': 'margin-top: 0px; justify-content: flex-start;' });
      $(selector).contents().find('.skin-grayish.admin-bar:where(.mblt-header-mobile-buttons, .mblt-header-and-footer-mobile-buttons) .menu-content').css({ 'cssText': 'top: 0px;' });


    });
  }
  delete_wp_adminbar_skinadd('.iframe-demo');

})(jQuery);
