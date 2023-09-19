<?php //AMPページでは呼び出さない（通常ページのみで呼び出す）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp()): ?>


  <?php //AdSense非同期スクリプトを出力

  //広告の存在を確認するグローバル変数
  global $_IS_ADSENSE_EXIST;

  //アドセンス共通スクリプトコード
  define('ADSENSE_SCRIPT_CODE', '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.get_adsense_data_ad_client().'" crossorigin="anonymous"></script>');
  //if ($_IS_ADSENSE_EXIST && !is_customize_preview() && !is_cocoon_settings_preview()) {
  if (is_ads_visible() && $_IS_ADSENSE_EXIST && !is_customize_preview()) {
    echo ADSENSE_SCRIPT_CODE;
  } //AdSense非同期スクリプトを出力
  ?>


  <?php //Pinterestシェア用のスクリプト
  if (is_singular() && is_pinterest_share_pin_visible()): ?>
  <script async defer data-pin-height="28" data-pin-hover="true" src="//assets.pinterest.com/js/pinit.js"></script>
  <?php endif //Pinterestシェア用のスクリプト ?>


  <?php //Pinterestシェアボタン用のスクリプト
  if (is_singular() && (is_top_pinterest_share_button_visible() || is_bottom_pinterest_share_button_visible())): ?>
  <script>!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="//assets.pinterest.com/js/pinit_main.js";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pinterest-btn-js");</script>
  <?php endif //Pinterestシェアボタン用のスクリプト ?>


  <?php //コピーシェアボタン用のスクリプト
  global $_MOBILE_COPY_BUTTON;
  if ($_MOBILE_COPY_BUTTON): ?>
  <div class="copy-info"><?php _e('タイトルとURLをコピーしました', THEME_NAME); ?></div>
    <?php //https環境ではブラウザのクリップボードAPIを利用する
    if (is_ssl()): ?>
      <script>
      (function($){
        const selector = '.copy-button';//clipboardで使う要素を指定
        $(selector).click(function(event){
          //クリック動作をキャンセル
          event.preventDefault();
          //クリップボード動作
          navigator.clipboard.writeText($(selector).attr('data-clipboard-text')).then(
            () => {
              $('.copy-info').fadeIn(500).delay(1000).fadeOut(500);
            });
        });
      })(jQuery);
      </script>
    <?php else: // httpの際?>
      <script src="//cdn.jsdelivr.net/clipboard.js/1.5.13/clipboard.min.js"></script>
      <script>
      (function($){
        const selector = '.copy-button';//clipboardで使う要素を指定
        $(selector).click(function(event){
          //クリック動作をキャンセル
          event.preventDefault();
        });

        //クリップボード動作
        const clipboard = new Clipboard(selector);
        clipboard.on('success', function(e) {
          $('.copy-info').fadeIn(500).delay(1000).fadeOut(500);

          e.clearSelection();
        });
      })(jQuery);
      </script>
    <?php endif; ?>

  <?php endif //コピーシェアボタン用のスクリプト ?>

  <?php //カルーセルが表示されている時
  if (false && is_carousel_visible() && get_carousel_category_ids()): ?>
  <script>
  (function($){
    //カルーセルの表示
    $('.carousel').fadeIn();
  })(jQuery);
  </script>
  <?php endif //カルーセルが表示されている時?>


  <?php //本文中のJavaScriptをまとめて出力
  global $_THE_CONTENT_SCRIPTS;
  if ($_THE_CONTENT_SCRIPTS): ?>
  <script><?php echo $_THE_CONTENT_SCRIPTS; ?></script>
  <?php endif //本文中のJavaScriptをまとめて出力 ?>


  <?php /*固定ヘッダー*/
  if (is_header_fixed()): ?>
  <script>
  (function($) {
    /*固定ヘッダー化*/
    function stickyHeader() {
      if (!$("#header-container").hasClass("fixed-header")) {
        /* 余白調整用の空クラスを追加する */
        $('#header-container').after('<div id="header-fixed"></div>');

        /* ヘッダーの高さの変化分、paddingで調整しスクロール位置を止まらせる */
        $("#header-fixed").css({
          'padding-top': `${threashold}px`,
        });

        /* トップメニュータイプに変更する */
        $("#header-container-in").removeClass('hlt-center-logo hlt-center-logo-top-menu').addClass("hlt-top-menu wrap");
        $("#header-container").addClass("fixed-header");
        $("#header-container").css({
          'position': 'fixed',
          'top': '-100px',
          'left': '0',
          'width': '100%',
        });

        const wpadminbar = document.getElementById('wpadminbar');
        const headerContainerTop = wpadminbar ? wpadminbar.clientHeight : 0;

        $('#header-container').animate(
          {
            top: headerContainerTop,
          },
          500
        );
      }
    }

    /*固定ヘッダーの解除*/
    function staticHeader() {
      if ($("#header-container").hasClass("fixed-header")) {
        /*センターロゴタイプに戻す*/
        $("#header-container-in").removeClass("hlt-top-menu hlt-tm-right hlt-tm-small hlt-tm-small wrap").addClass("<?php echo get_additional_header_container_classes(); ?>");
        $("#header-container").removeClass("fixed-header");
        $("#header-container").css({
          'position': 'static',
          'top': 'auto',
          'left': 'auto',
          'width': 'auto',
        });

        /* ヘッダーの高さの戻る分、padding削除しスクロール位置を止まらせる */
        $("#header-fixed").css({
          'padding-top': '0',
        });

        $("#header-fixed").remove();
      }
    }


    /* 境界値をヘッダーコンテナに設定 */
    var threashold  = $('#header-container').height();

    var prevScrollTop = -1;
    var $window = $(window);
    var mobileWidth = 1023;

    $window.scroll(function() {
      var scrollTop = $window.scrollTop();

      var s1 = (prevScrollTop > threashold);
      var s2 = (scrollTop > threashold);
      var w = $window.width();

      /*スクロールエリアの位置調整*/
      function adjustScrollArea(selector) {
        if ($(selector) && $(selector).offset()) {
          offset = $(selector).offset().top;

          h = $("#header-container").height();

          pt = $(selector).css('padding-top');

          if (pt) {
            pt = pt.replace('px', '');
          } else {
            pt = 0;
          }
          if ((scrollTop >= offset - h) && (w > mobileWidth)) {
            if ((pt <= 1) && $("#header-container").hasClass('fixed-header')) {
              $(selector).css({
                'padding-top': h + 'px',
              });
            }
          } else {
            if (pt > 0) {
              $(selector).css({
                'padding-top': 0,
              });
            }
          }
        }
      }

      /*スクロール追従エリアの調整*/
      function adjustScrollAreas() {
        adjustScrollArea('#sidebar-scroll');
        adjustScrollArea('#main-scroll');
      }

      /*固定ヘッダーのスタイル決め*/
      function adjustFixedHeaderStyle(s1, s2, w, scrollTop, mobileWidth) {
        if (s1 ^ s2) {
          if (s2 && (w > mobileWidth)) {
            stickyHeader();
          }
        }

        /* 境界値に達したら固定化 */
        if (scrollTop <= threashold || w <= mobileWidth) {
          staticHeader();
        }
      }
      adjustFixedHeaderStyle(s1, s2, w, scrollTop, mobileWidth);
      adjustScrollAreas();
      prevScrollTop = scrollTop;
    });

    /*ウインドウがリサイズされたら発動*/
    $window.resize(function () {
      /*ウインドウの幅を変数に格納*/
      var w = $window.width();
      if (w <= mobileWidth) { /*モバイル端末の場合*/
        staticHeader();
      } else { /*パソコン端末の場合*/
        var scrollTop = $window.scrollTop();
        if (scrollTop >= 50) {
          stickyHeader();
        }
      }
    });
  })(jQuery);
  </script>
  <?php endif //固定ヘッダー ?>

  <?php //Swiper
  global $_IS_SWIPER_ENABLE;
  if ($_IS_SWIPER_ENABLE): ?>
  <link rel='stylesheet' id='swiper-style-css' href='https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css' />
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
  <script>
  const mySwiper = new Swiper('.is-list-horizontal.swiper', {
    // Optional parameters
    slidesPerView: 'auto',
    spaceBetween: 4,
    navigation: {
      prevEl: '.is-list-horizontal .swiper-button-prev',
      nextEl: '.is-list-horizontal .swiper-button-next',
    },
  });
  </script>
  <?php endif; //Swiper ?>

  <?php //数式表示
  if (is_formula_enable() && is_math_shortcode_exist()): ?>
  <script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
    MathJax.Hub.Config({

    });
  </script>
  <?php endif; //数式表示 ?>

<?php endif //!is_amp() ?>
