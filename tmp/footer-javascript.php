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
  global $_IS_ADSENSE_EXIST;
  //if ($_IS_ADSENSE_EXIST && !is_customize_preview() && !is_cocoon_settings_preview()) {
  if ($_IS_ADSENSE_EXIST && !is_customize_preview()) {
    echo ADSENSE_SCRIPT_CODE;
  }
  ?>
  <?php //Pinterestシェア用のスクリプト
  if (is_singular() && is_pinterest_share_pin_visible()): ?>
  <script async defer data-pin-height="28" data-pin-hover="true" src="//assets.pinterest.com/js/pinit.js"></script>
  <?php endif ?>
  <?php //Pinterestシェアボタン用のスクリプト
  if (is_singular() && (is_top_pinterest_share_button_visible() || is_bottom_pinterest_share_button_visible())): ?>
  <script>!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="//assets.pinterest.com/js/pinit_main.js";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pinterest-btn-js");</script>
  <?php endif ?>
  <?php //コピーシェアボタン用のスクリプト
  global $_MOBILE_COPY_BUTTON;
  if (is_top_copy_share_button_visible() || is_bottom_copy_share_button_visible() || $_MOBILE_COPY_BUTTON): ?>
  <div class="copy-info"><?php _e('タイトルとURLをコピーしました', THEME_NAME); ?></div>
  <script src="//cdn.jsdelivr.net/clipboard.js/1.5.13/clipboard.min.js"></script>
  <script>
  (function($){
    selector = '.copy-button';//clipboardで使う要素を指定
    $(selector).click(function(event){
      //クリック動作をキャンセル
      event.preventDefault();
      //クリップボード動作
      var clipboard = new Clipboard(selector);
      clipboard.on('success', function(e) {
        $('.copy-info').fadeIn(500).delay(1000).fadeOut(500);

        e.clearSelection();
      });
    });
  })(jQuery);
  </script>
  <?php endif ?>
  <?php //カルーセルが表示されている時
  if (false && is_carousel_visible() && get_carousel_category_ids()): ?>
  <script>
  (function($){
    //カルーセルの表示
    $('.carousel').fadeIn();
  });
  </script>
  <?php endif ?>
  <?php //本文中のJavaScriptをまとめて出力
  global $_THE_CONTENT_SCRIPTS;
  if ($_THE_CONTENT_SCRIPTS): ?>
  <script><?php echo $_THE_CONTENT_SCRIPTS; ?></script>
  <?php endif ?>
<?php endif ?>
<?php //固定ヘッダー
if (is_header_fixed()): ?>
<script>
(function($){
  function stickyHeader(){
    if (!$("#header-container").hasClass("fixed-header")) {
      <?php if (get_header_layout_type_center_logo()): ?>
      /*トップメニュータイプに変更する*/
      $("#header-container-in").removeClass('hlt-center-logo hlt-center-logo-top-menu cl-slim').addClass("hlt-top-menu wrap");
      <?php endif; ?>
      $("#header-container").addClass("fixed-header");
      $("#header-container").css({
        'position': 'fixed',
        'top': '-100px',
        'width': '100%',
      });
      $("#header-container").animate({'top': '0'}, 500);
      /*$("#sidebar-scroll, #main-scroll").css({
        'padding-top': $("#header-container").height() + 'px',
      });
      console.log('stickyHeader');*/
    }
  }
  function staticHeader(){
    if ($("#header-container").hasClass("fixed-header")) {
      <?php if (get_header_layout_type_center_logo()): ?>
      /*センターロゴタイプに戻す*/
      $("#header-container-in").removeClass("hlt-top-menu hlt-tm-right hlt-tm-small hlt-tm-small wrap").addClass("<?php echo get_additional_header_container_classes(); ?>");
      <?php endif; ?>
      $("#header-container").removeClass("fixed-header");
      $("#header-container").css({
        'position': 'static',
        'top': 'auto',
        'width': 'auto',
      });
      /*$("#sidebar-scroll, #main-scroll").css({
        'padding-top': 0,
      });
      console.log('staticHeader');*/
    }
  }

  var prevScrollTop = -1;
  var $window = $(window);
  var mobileWidth = 1023;
  $window.scroll(function(){
    var scrollTop = $window.scrollTop();
    var threashold = 600;
    var s1 = (prevScrollTop > threashold);
    var s2 = (scrollTop > threashold);
    var w = $window.width();

    function adjustScrollArea(selector) {
      offset = $(selector).offset().top;
      h = $("#header-container").height();
      pt = $(selector).css('padding-top').replace('px', '');
      /*console.log('of:'+offset);
      console.log('st:'+scrollTop);
      console.log($(selector).css('padding-top'));*/
      if ((scrollTop >= offset - h) && (w >  mobileWidth)) {
        if (pt <= 1) {
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
    function adjustScrollAreas() {
      adjustScrollArea('#sidebar-scroll');
      adjustScrollArea('#main-scroll');
    }

    /*ヘッダーメニューの固定*/
    if (s1 ^ s2) {
      if (s2 && w >  mobileWidth) {
        stickyHeader();
      }
    }
    if (scrollTop == 0 || w <=  mobileWidth) {
      staticHeader();
    }
    adjustScrollAreas();

    prevScrollTop = scrollTop;
  });

  /*ウインドウがリサイズされたら発動*/
  $window.resize(function() {
    /*ウインドウの幅を変数に格納*/
    var w = $window.width();
    if (w <=  mobileWidth) {/*モバイル端末の場合*/
      staticHeader();
    } else {/*パソコン端末の場合*/
      stickyHeader();
    }
  });
})($);
</script>
<?php endif; ?>
<?php if (is_highspeed_mode_enable()): ?>
<script>
(function($){
  //管理パネルはbarba.js動作から除外する
  var d = document.getElementById("wpadminbar");
  d.setAttribute("data-barba-prevent", "all");
  //barba.js
  //barba.use(barbaPrefetch);

  //head内タグのの移し替え
  const replaceHeadTags = target => {
    const head = document.head;
    const targetHead = target.html.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0];
    //console.log(targetHead);
    const newPageHead = document.createElement('head');
    newPageHead.innerHTML = targetHead;
    // console.log(newPageHead);
    //SEOに関係ありそうなタグ
    const removeHeadTags = [
      "meta[name='keywords']",
      "meta[name='description']",
      "meta[property^='fb']",
      "meta[property^='og']",
      "meta[property^='article']",
      "meta[name^='twitter']",
      "meta[property^='twitter']",
      "meta[name='robots']",
      'meta[itemprop]',
      "meta[name='thumbnail']",
      'link[itemprop]',
      "link[rel='alternate']",
      "link[rel='prev']",
      "link[rel='next']",
      "link[rel='canonical']",
      "link[rel='amphtml']",
      "link[rel='shortlink']",
      "script[type='application/ld+json']",
    ].join(',');
    //前のページの古いタグを削除
    const headTags = [...head.querySelectorAll(removeHeadTags)];
    //console.log(headTags)
    headTags.forEach(item => {
      head.removeChild(item);
    });
    //新しいページの新しいタグを追加
    const newHeadTags = [...newPageHead.querySelectorAll(removeHeadTags)];
    //console.log(newHeadTags)
    newHeadTags.forEach(item => {
      head.appendChild(item);
    })
  };
  <?php //Google Analytics
  if ($analytics_tracking_id = get_google_analytics_tracking_id() && is_analytics()): ?>
  const gaPush = pagename => {
    //古いAnalyticsコード
    if (typeof ga === 'function') {
      ga('send', 'pageview', pagename);
    }
    //gtag.js（公式）
    if (typeof gtag === 'function') {
      console.log('gtag');
      gtag('config', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
    }
    //ga-lite.min.js（高速化）
    if (typeof galite === 'function') {
      galite('create', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
      galite('send', 'pageview');
    }
  };
  <?php endif; ?>

  //barba.jsの実行
  barba.init({
    transitions: [
      {
        // before({ current, next, trigger }) {
        //   console.log('before');
        // },
        // beforeLeave({ current, next, trigger }) {
        //   console.log('beforeLeave');
        // },
        // leave({ current, next, trigger }) {
        //   console.log('leave');
        // },
        // afterLeave({ current, next, trigger }) {
        //   console.log('afterLeave');
        // },
        enter({ current, next, trigger }) {
          //console.log('enter');
          //$(".carousel-content").slick();
        },
        beforeEnter({ current, next, trigger }) {
          //console.log('beforeEnter');
          //console.log(next);
          //headタグ変換
          replaceHeadTags(next);
          <?php //Google Analytics
          if ($analytics_tracking_id && is_analytics()): ?>
          gaPush(location.pathname);
          <?php endif; ?>
          //ページトップに移動
          const scrollElem = document.scrollingElement || document.documentElement;
          scrollElem.scrollTop = 0;
          //コメントエリアを開く動作の登録
          //register_comment_area_open();

          // // 外部ファイルの実行(任意の場所に追加)
          // var script = document.createElement('script');
          // script.src = 'https://cocoon.local/plugins/slick/slick.min.js';
          // document.body.appendChild(script);

          //    // 外部ファイルの実行(任意の場所に追加)
          // var script = document.createElement('script');
          // script.innerHTML = '$(".carousel-content").slick();';
          // document.body.appendChild(script);


        },
        afterEnter({ current, next, trigger }) {
          console.log('afterEnter');
        },
        after({ current, next, trigger }) {
          console.log('after');

          $(".carousel-content").slick({
            dots: true,
            infinite: true,
            slidesToShow: 6,
            slidesToScroll: 6,
            responsive: [
                {
                  breakpoint: 1240,
                  settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5
                  }
                },
                {
                  breakpoint: 1023,
                  settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4
                  }
                },
                {
                  breakpoint: 834,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                  }
                },
                {
                  breakpoint: 480,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                  }
                }
              ]
          });

        }
      }
    ]
  });

  const eventDelete = e => {
    if (e.currentTarget.href === window.location.href) {
      // console.log(e.currentTarget.href);
      // console.log(window.location.href);
      e.preventDefault();
      e.stopPropagation();
      return;
    }
  };

  const links = [...document.querySelectorAll('a[href]')];
  //console.log(links);
  links.forEach(link => {
    link.addEventListener('click', e => {
      //console.log('click');
      eventDelete(e);
    }, false);
  });
})($);
</script>
<?php endif; ?>

