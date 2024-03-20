<?php
if (!defined('ABSPATH')) exit;

global $_IS_SWIPER_ENABLE;


//******************************************************************************
//  ローディング画面
//******************************************************************************
if (is_front_top_page() && (get_theme_mod('hvn_front_loading_setting', 'none') != 'none')) {
  echo <<< EOF
(function($) {
  $('body').css('overflow-y', 'hidden');
  if (sessionStorage.getItem('visited')) {
    $('.loader-bg').remove();
    $('body').css('overflow-y', '');
    $('body').css('visibility', 'visible');
  } else {
    $('body').css('visibility', 'visible');
    setTimeout(function() {
      $('.loader-bg').fadeOut(1000);
      $('body').css('overflow-y', '');
    }, 2000);
    sessionStorage.setItem('visited', 'first');
  }
})(jQuery);

EOF;
}


//******************************************************************************
//  テーブルの1列目を固定表示の問題対策
//******************************************************************************
if (is_responsive_table_first_column_sticky_enable()) {
  echo <<< EOF
(function($) {
  var flag = 0;
  var next = 0;
  $('.stfc-sticky tr').each(function(i) {
    if ($('td', this).eq(0).is('[rowspan]')) {
      cnt = $('td', this).eq(0).attr('rowspan');
      next = Number(cnt) + i;
      flag = 1;
    } else {
      if ((next == i) || (flag == 0)) {
        $('td', this).eq(0).attr('rowspan', '1');
        next = i + 1;
      }
    }
  });
})(jQuery);

EOF;
}


//******************************************************************************
//  FAQアコーディオン無効
//******************************************************************************
echo <<< EOF
(function($) {
  $('.is-style-accordion > .faq > .faq-question').off('click').click(function() {
    $(this).next('.is-style-accordion .faq-answer').slideToggle(0);
    $(this).toggleClass('active');
  });
})(jQuery);


EOF;


//******************************************************************************
//  フロントページから新着記事を除外
//******************************************************************************
if (is_front_top_page() &&  get_theme_mod('hvn_front_none_setting')) {
  echo <<< EOF
(function($) {
  $('#index-tab-2').prop('checked', true);
})(jQuery);

EOF;
}


//******************************************************************************
//  オートプレイ
//******************************************************************************
if (get_theme_mod('hvn_swiper_auto_setting') && $_IS_SWIPER_ENABLE) {
  $js = <<< EOF
    loop: true,
    speed: 1000,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
EOF;
  $swip_count = 0;

  // [C]ナビウィジェットパラメータリスト取得
  $info = get_option('widget_navi_entries');

  // コンテンツ上部ウィジェット取得
  $all_widgets = wp_get_sidebars_widgets();
  $target_widgets = $all_widgets['content-top'];

  // コンテンツ上部の[C]ナビカード数
  $navis = preg_grep('/navi_entries/', $target_widgets);

  if (count($navis)) {
    foreach ($navis as $navi) {
      // ウィジェット番号取得
      $parsed = wp_parse_widget_id($navi);
      $no = $parsed['number'];

      // ウィジェット設定値取得
      $name = $info[$no]['name'];
      $swip = $info[$no]['is_horizontal'];
 
      if ($swip) {
        $swip_count ++;

        // メニュー数チェック
        if (count(wp_get_nav_menu_items($name)) < 3) {
          $js = null;
        }
      }
    }

    // 横型表示数チェック
    if ($swip_count > 1) {
      $js = null;
    }
    echo <<< EOF
const autoSwiper = new Swiper('.is-auto-horizontal.swiper', {
  slidesPerView: 'auto',
  spaceBetween: 30,
  {$js}
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    prevEl: '.is-auto-horizontal .swiper-button-prev',
    nextEl: '.is-auto-horizontal .swiper-button-next',
  },
});

EOF;
  }
}


//******************************************************************************
//  目次ハイライト
//******************************************************************************
if (get_theme_mod('hvn_toc_setting')) {
  echo <<< EOF
(function($) {
  // 見出し判定位置調整(font-size)
  const val = 50;
  let footerTop;

  // 表示対象の目次取得
  const Tocs = $('.sidebar-scroll .toc-widget-box li:not(.display-none) > a');
  let Pos = new Array();

  // 章範囲設定
  function init() {
    for (var i=0; i<Tocs.length; i++) {
      let ID = Tocs.eq(i).attr('href');
      let top = $(ID).offset().top - val;
      let bottom = null;

      Pos[i] = {top: top, bottom: bottom};
      Pos[i].no = i;

      // 次の章までを領域とする
      if (i > 0) {
        Pos[i - 1].bottom = top;
      }
    }

    // 最後の章の下位置を設定
    footerTop = $('.article').offset().top;
    Pos[i - 1].bottom = footerTop + $('.article').height();
  }

  // ハイライト表示
  function currentCheck() {
    let windowScrollTop = $(window).scrollTop();
    Tocs.parent().removeClass('current');

    // 章範囲内チェック
    if (Pos[0].top <= windowScrollTop && Pos[Pos.length - 1].bottom > windowScrollTop) {
      for (var i=0; i<Pos.length; i++) {
        if (Pos[i].top <= windowScrollTop && Pos[i].bottom > windowScrollTop) {
          Tocs.eq(Pos[i].no).parent().addClass('current');
          break;
        }
      }
    }
  }

  if ($('.sidebar-scroll .toc-widget-box').length) {
    init();
    $(window).on('load scroll', currentCheck);
  }
})(jQuery);

EOF;
}


//******************************************************************************
//  スクロールボタン
//******************************************************************************
if ((get_theme_mod('hvn_header_setting', 'none') != 'none') && is_front_top_page() && !is_singular_page_type_content_only()) {
  echo <<< EOF
(function($){
  if ($('.appeal').length) {
    elm = $('.appeal');
  } else if ($('.recommended').length) {
    elm = $('.recommended ');
  } else if ($('.content-top').length) {  
    elm = $('.content-top');
  } else if ($('.content').length) {
    elm = $('.content');
  }

  $('.scrolldown span').click(function() {
    $('body, html').animate({
      scrollTop: elm.offset().top
    }, 800);
  });
})(jQuery);

EOF;
}


//******************************************************************************
//  メインビジュアル(画像)
//******************************************************************************
if (hvn_image_count() > 1 && get_theme_mod('hvn_header_setting') == 'image' && is_front_top_page()) {
  $speed = 2000;
  if (get_theme_mod('hvn_header_fade_setting', 'fade') != 'fade') {
    $speed = 0;
  }

  echo <<< EOF
const swiper = new Swiper('.hvn-swiper', {
  speed: {$speed},
  loop: true,
  effect: 'fade',
  slidesPerView: 1,
  allowTouchMove: false,
  autoplay: {
    delay: 8000,
  },
});
EOF;
}


//******************************************************************************
//  いいねボタン
//******************************************************************************
if (get_theme_mod('hvn_like_setting')) {
  $url =  esc_html(admin_url('admin-ajax.php'));
  echo <<< EOF
(function($) {
  var cook = [];

  // 各ボタン毎に処理
  $('.like .button').each(function() {
    id = $(this).attr('data-id');
    check_cookie(this, id);

    // ボタンクリック?
    $(this).on('click', function(e) {
      var mode = null;
      var id = $(this).attr('data-id');
      var index = cook.indexOf(id);

      // aタグへの伝搬を無効
      e.preventDefault();

      // cookieに登録済?
      if (index > -1) {
        // 削除
        cook.splice(index, 1);
      } else {
        // 登録
        mode = 'add';
        cook.push(id);
      }

      // Cookie更新
      var cookArry = JSON.stringify(cook);
      $.cookie('like_page', cookArry, { expires: 365, path: '/' });
      check_cookie(this, id);

      // カスタムフィールド更新
      $.ajax({
        type: 'POST',
        url: '{$url}',
        cache: false,
        data: {
          action : 'hvn_like_action',
          id: id,
          mode: mode,
        },
        success: function(data) {
          // カウンター更新
          $(e.target).next().text(data);
        }
      });
    });
  });

  // Cookieチェック処理
  function check_cookie(elm, id) {
    // cookie取得
    cook = $.cookie('like_page') ? JSON.parse( $.cookie('like_page')) : [];

    // 投稿ID登録済?
    if (cook.indexOf(id) > -1) {
      $(elm).addClass('active');
    } else {
      $(elm).removeClass('active');
    }
  }
})(jQuery);

EOF;
}


//******************************************************************************
//  スクロール量
//******************************************************************************
echo <<< EOF
(function($) {
  $(window).on('load scroll', function() {
    var scroll = $(window).scrollTop();
    var height = $(document).height() - $(window).height();
    var per = Math.floor(scroll / height * 100);
    document.documentElement.style.setProperty('--per', `\${per}%`);
  });
})(jQuery);

EOF;


//******************************************************************************
//  メインビジュアルy座標取得
//******************************************************************************
echo <<< EOF
(function($) {
  function setHeight() {
    var a = 0;
    if ($('.hvn-header').length) {
      a = $('.hvn-header').offset().top;
    }
    document.documentElement.style.setProperty('--ah', `\${a}px`);
  }

  $(window).resize(function() {
    // メニュー解除
    if ($('#navi-menu-input').prop("checked")) {
      $('#navi-menu-input').prop("checked", false);
    }
    setHeight();
  });

  setHeight();
})(jQuery);

EOF;


//******************************************************************************
//  プロフィール監視
//******************************************************************************
$size_481 = 481;
$size_835 = 835;
echo <<< EOF
(function($) {
  const size_835 = window.matchMedia("(min-width: ${size_835}px)");
  const size_481 = window.matchMedia("(min-width: ${size_481}px)");

  // 835以上
  const size_835Listener = (event) => {
    if (event.matches) {
      $(".footer").addClass('nwa');
    } else {
      $('.footer').removeClass('nwa');
    }
  };

  // 481px以上?
  const size_481Listener = (event) => {
    if (event.matches) {
      $('.container').removeClass('nwa');
    } else {
      $(".container").addClass('nwa');
    }
  };

  size_835.addEventListener("change", size_835Listener);
  size_481.addEventListener("change", size_481Listener);

  size_835Listener(size_835);
  size_481Listener(size_481);
})(jQuery);

EOF;


//******************************************************************************
//  コードコピーボタンを追加
//******************************************************************************
if (is_ssl()) {
  echo <<< EOF
(function($) {
  $(".wp-block-code").wrap('<div class="pre-wrap"></div>').before('<button class="code-copy">COPY</button>');

  $('.code-copy').click(function(event) {
    event.preventDefault();

    navigator.clipboard.writeText($(this).parent().text()).then(() => {
      $(".code-copy").text("COPIED");
      setTimeout(function(){ 
        $(".code-copy").text("COPY");
      }, 1000);
    });
  });
})(jQuery);

EOF;

} else {
  echo <<< EOF
(function($) {
  $(".wp-block-code").wrap('<div class="pre-wrap"></div>').before('<button class="code-copy">COPY</button>');

  const clip = new Clipboard(".code-copy", {
    target: function (trigger) {
      return trigger.nextElementSibling;
    },
  });

  clip.on("success", function(event) {
    $(".code-copy").text("COPIED");
    setTimeout(function(){ 
      $(".code-copy").text("COPY");
    }, 1000);
    event.clearSelection();
  });
})(jQuery);

EOF;
}
