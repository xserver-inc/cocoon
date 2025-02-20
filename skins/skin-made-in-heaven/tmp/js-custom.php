<?php
if (!defined('ABSPATH')) exit;

global $_IS_SWIPER_ENABLE;
global $_HVN_NOTICE;

$n = HVN_COUNT;
$button = '<button class="sub-item" aria-label="'. __('もっと見る', THEME_NAME) . '"></button>';


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
//  「新着記事」タブ表示
//******************************************************************************
if (is_front_top_page() &&  !get_theme_mod('hvn_front_none_setting', true)) {
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
  // 見出し判定位置調整（font-size）
  const val = 50;
  let footerTop;
  let lastBodyHeight = 0;

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
    lastBodyHeight = $('body').height();
  }

  // ハイライト表示
  function currentCheck() {
    if ($('body').height() != lastBodyHeight) {
      init();
    }

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
//  メインビジュアル（画像）
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
      localStorage.setItem('like_page', cookArry);
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
    cook = localStorage.getItem('like_page') ? JSON.parse(localStorage.getItem('like_page')) : [];

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
    $('#navi-menu-input').prop("checked", false);
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

  // 835px以上?
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

    var codeText = $(this).siblings('pre').find('code').text();

    navigator.clipboard.writeText(codeText).then(() => {
      $(".code-copy").text("COPIED");
      setTimeout(function() {
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
      return trigger.nextElementSibling.querySelector('code');
    },
  });

  clip.on("success", function(event) {
    $(".code-copy").text("COPIED");
    setTimeout(function() {
      $(".code-copy").text("COPY");
    }, 1000);
    event.clearSelection();
  });
})(jQuery);

EOF;
}


//******************************************************************************
//  アコーディオン化
//******************************************************************************
if (get_theme_mod('hvn_accordion_setting')) {
  echo <<< EOF
// アーカイブ
(function($) {
  var html;
  var li;
  var pcount;
  var year;
  var byear;

  $(".widget_archive").each(function() {
    html = '';
    li = '';
    pcount = 0;
    year   = 0;
    byear  = 0;

    $('li', this).each(function() {
      var y = $(this).text().match(/(\d+)-/);
      year = y[1];
      if (byear == '') {
        byear = y[1];
      }

      if (year != byear) {
        out_html();
        byear = y[1];
        li = '';
        pcount = 0;
      }
      li += '<li>' + $(this).html().replace(/(\d+)-/, '') + '</li>';
      pcount += Number($('.post-count', this).text());
    });

    out_html();
    $('ul', this).html(html);
  });

  function out_html() {
    var p = '';
    if (pcount != 0) {
      p = '<span class="post-count">' + pcount + '</span>';
    }
    html += '<li><a><span class="list-item-caption">' + byear + '</span>' + p + '</a><ul class="children">' + li + '</ul></li>';
  }
})(jQuery);


// カテゴリー、固定ページ
(function($) {
  $(".widget").each(function() {
    $('.children', this).hide();
　  $('.children', this).before('{$button}');

    $('.sub-item', this).click(function() {
      $(this).next('ul').toggle();
      $(this).toggleClass('active');
    });
  });
})(jQuery);


// タグクラウド
(function($) {
  var n = {$n};

  $('.sidebar .widget_tag_cloud').each(function() {
    var elm = $('.tagcloud a', this);
    var c   = elm.length;
    if (c > n) {
      elm.slice({$n}).hide();
      $(this).append('{$button}');
    }

    $('button', this).click(function() {
      elm.slice({$n}).toggle();
      $(this).toggleClass('active');
    });
  });
})(jQuery);

EOF;
}


//******************************************************************************
//  ダークモード
//******************************************************************************
echo <<< EOF
(function($) {
  const btn = $('#hvn-dark');
  btn.click(function() {
   $('body').toggleClass('hvn-dark');
   if (btn.prop('checked')) {
      localStorage.setItem('hvn-dark', 'dark');
    } else {
      localStorage.removeItem('hvn-dark');
    }
  });

  //ローカルストレージ判定
  if (localStorage.getItem('hvn-dark') ==='dark') {
    $('body').addClass('hvn-dark');
    btn.prop("checked", true);
  } else {
    $('body').removeClass('hvn-dark');
  }
  $('body').css('visibility', 'visible');
})(jQuery);
EOF;


//******************************************************************************
//  通知エリア
//******************************************************************************
if ($_HVN_NOTICE) {
  $direction = 'vertical';
  $delay = 8000;
  $speed = 2000;
  if (get_theme_mod('hvn_notice_scroll_setting')) {
    $direction = 'horizontal';
    $delay = 0;
    $speed = 15000;
  }

  echo <<< EOF
const noticeSwiper = new Swiper(".notice-area-message .swiper",{
  loop: true,
  direction: "{$direction}",
  autoplay: {
    delay: {$delay},
  },
  speed: {$speed},
});

EOF;
}


//******************************************************************************
//  目次ボタン
//******************************************************************************
if (get_theme_mod('hvn_toc_fix_setting')) {
  echo <<< EOF
(function($) {
  if (!($('.main .toc').length)) {
    return;
  }
  $('.hvn-open-btn').addClass('active');
  $('#hvn-toc a').click(function() {
    $('#hvn-close').prop('checked', true);
  });
})(jQuery);

EOF;
}


//******************************************************************************
//  ブログスタイル
//******************************************************************************
echo <<< EOF
(function($) {
  $('.is-style-hvn-text').each(function(){
    $('.blogcard-wrap', this).html($('.blogcard-title', this));
    
  });
})(jQuery);

EOF;


//******************************************************************************
//  見出しボックスアコーディオン
//******************************************************************************
echo <<< EOF
(function($) {
  $('.is-style-accordion.cocoon-block-caption-box > .caption-box-content').hide();
  $('.is-style-accordion.cocoon-block-caption-box > .caption-box-label').click(function() {
    $(this).next('.caption-box-content').toggle();
    $(this).toggleClass('active');
  });
})(jQuery);

EOF;


//******************************************************************************
//  目次省略表示
//******************************************************************************
switch(get_theme_mod('hvn_toc_hidden_setting')) {
  case '1':
    echo <<< EOF
(function($) {
  var n = ${n}

  var elm = $('.main .toc-content li');
  var c = elm.length;
  if (c > n) {
    elm.slice({$n}).hide();
    $('.main .toc-content').append('{$button}');
  }

  $('.toc button').click(function() {
    elm.slice({$n}).toggle();
    $(this).toggleClass('active');
  });
})(jQuery);

EOF;
    break;

  case '2':
    echo <<< EOF
(function($) {
  var elm = $('.main .toc-content ul ul');
  var c = elm.length;
  if (c > 0) {
    elm.hide();
    $('.main .toc-content').append('{$button}');
  }

  $('.toc button').click(function() {
    elm.toggle();
    $(this).toggleClass('active');
  });
})(jQuery);

EOF;
    break;
}


//******************************************************************************
//  タブブロックスクロール表示
//******************************************************************************
echo <<< EOF
(function($){
  new ScrollHint(".tab-label-group", {
    suggestClass: 'is-scroll-tab',
  });
})(jQuery);

EOF;
