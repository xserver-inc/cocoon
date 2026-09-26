<?php
if (!defined('ABSPATH')) exit;


global $_IS_SWIPER_ENABLE;

$n = HVN_COUNT;
$button = '<button class="sub-item" aria-label="'. __('もっと見る', THEME_NAME) . '"></button>';


//******************************************************************************
//  ローディング画面
//******************************************************************************
if (is_front_top_page() && (get_theme_mod('hvn_front_loading_setting', 'none') != 'none')) {
  echo <<<EOF
jQuery(function($) {
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
});

EOF;
}


//******************************************************************************
//  メインビジュアルy座標取得
//******************************************************************************
echo <<<EOF
jQuery(function($) {
  function setHeight() {
    var a = 0;
    if ($('.hvn-header').length) {
      a = $('.hvn-header').offset().top;
    }
    $('html').css('--ah', a + 'px');
    $('body').addClass('hvn-ready');
  }

  $(window).resize(function() {
    // メニュー解除
    $('#navi-menu-input').prop("checked", false);
    setHeight();
  });

  setHeight();
});

EOF;


//******************************************************************************
//  テーブルの1列目を固定表示の問題対策
//******************************************************************************
if (is_responsive_table_first_column_sticky_enable()) {
  echo <<<EOF
jQuery(function($) {
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
});

EOF;
}


//******************************************************************************
//  「FAQ」ブロック（アコーディオン）のアニメ無効
//******************************************************************************
echo <<<EOF
jQuery(function($) {
  $('.is-style-accordion > .faq > .faq-question').off('click').click(function() {
    $(this).next('.is-style-accordion .faq-answer').slideToggle(0);
    $(this).toggleClass('active');
  });
});

EOF;


//******************************************************************************
//  オートプレイ
//******************************************************************************
if (get_theme_mod('hvn_swiper_auto_setting') && is_recommended_cards_visible()) {
  echo <<<EOF
const autoSwiper = new Swiper('.is-auto-horizontal.swiper', {
  slidesPerView: 'auto',
  spaceBetween: 30,
  loop: true,
  speed: 1000,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
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


//******************************************************************************
//  目次ハイライト
//******************************************************************************
if (get_theme_mod('hvn_toc_setting')) {
  echo <<<EOF
jQuery(function($) {
  const val = 50;
  let pos = [];
  let ticking = false;

  const tocItems = $('.sidebar-scroll .toc-widget-box li:not(.display-none) > a');

  // 目次の範囲設定
  function updatePositions() {
    pos = [];
    tocItems.each(function() {
      const a = $(this);
      const id = a.attr('href');
      if (id && id.startsWith('#') && $(id).length) {
        pos.push({
          top: $(id).offset().top - val,
          bottom: 0,
          element: a.parent()
        });
      }
    });

    const len = pos.length;
    // 次の見出しまでの範囲設定
    for (let i = 0; i < len - 1; i++) {
      pos[i].bottom = pos[i + 1].top;
    }

    // 最後の見出し
    if (len > 0) {
      pos[len - 1].bottom = $('.article').offset().top + $('.article').outerHeight();
    }
  }

  // ハイライト表示
  function currentCheck() {
    const scrollTop = $(window).scrollTop();
    let found = false;

    for (let i=pos.length - 1; i>=0; i--) {
      if (
        scrollTop >= pos[i].top &&
        scrollTop < pos[i].bottom
      ) {
        tocItems.parent().removeClass('current');
        pos[i].element.addClass('current');
        found = true;
        break;
      }
    }
    if (!found) {
      tocItems.parent().removeClass('current');
    }
  }

  // 初期化
  if ($('.sidebar-scroll .toc-widget-box').length) {

    updatePositions();
    currentCheck();

    // スクロール監視
    $(window).on('scroll', function() {
      // 1フレーム1回に制限
      if (!ticking) {
        window.requestAnimationFrame(function() {
          currentCheck();
          ticking = false;
        });
        ticking = true;
      }
    });

    // リサイズ時
    $(window).on('resize', function() {
      updatePositions();
      currentCheck();
    });

    // .mainのサイズ変化を監視
    const mainElement = document.querySelector('.main');
    if (mainElement) {
      const resizeObserver = new ResizeObserver(function() {
        updatePositions();
        currentCheck();
      });
      resizeObserver.observe(mainElement);
    }
  }
});

EOF;
}


//******************************************************************************
//  メインビジュアル（画像）
//******************************************************************************
if (hvn_image_count() > 1 && get_theme_mod('hvn_header_setting', 'none') == 'image' && is_front_top_page()) {
  $speed = 2000;
  if (get_theme_mod('hvn_header_fade_setting', 'fade') != 'fade') {
    $speed = 1;
  }

  echo <<<EOF
const swiper = new Swiper('.hvn-swiper', {
  speed: {$speed},
  loop: true,
  effect: 'fade',
  slidesPerView: 1,
  allowTouchMove: false,
  autoplay: {
    delay: 8000,
  },
  on: {
    slideChange: function() {
      // 初期表示の1枚目に対するスライド抑止
      if (this.realIndex > 0) {
        this.el.classList.add('is-changed');
      }
    }
  }
});

EOF;
}


//******************************************************************************
//  いいねボタン
//******************************************************************************
if (get_theme_mod('hvn_like_setting')) {
  $url =  esc_html(admin_url('admin-ajax.php'));
  $nonce = wp_create_nonce('hvn_like_nonce');

  echo <<<EOF
jQuery(function($) {
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

      // ローカルストレージ更新
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
          _ajax_nonce: '{$nonce}'
        },
        success: function(data) {
          // 同じ投稿IDのボタンのカウンター更新
          $('.like .button[data-id="' + id + '"]').each(function() {
            $(this).next().text(data);
          });
        }
      });
    });
  });

  // Cookieチェック処理
  function check_cookie(elm, id) {
    // ローカルストレージ取得
    cook = localStorage.getItem('like_page') ? JSON.parse(localStorage.getItem('like_page')) : [];

    // 同じ投稿IDのボタンを全部チェック
    $('.like .button[data-id="' + id + '"]').each(function() {
      if (cook.indexOf(id) > -1) {
        $(this).addClass('active');
      } else {
        $(this).removeClass('active');
      }
    });
  }
});

EOF;
}


//******************************************************************************
//  スクロール量
//******************************************************************************
echo <<<EOF
jQuery(function($) {
  let ticking = false;

  // スクロール量取得
  function updateScroll() {
    var scroll = $(window).scrollTop();
    var height = $(document).height() - $(window).height();
    var per = Math.floor(scroll / height * 100);

    $('html').css('--per', per + '%');
  }

  $(window).on('load', updateScroll);

  $(window).on('scroll', function() {
    if (!ticking) {
      window.requestAnimationFrame(function() {
        updateScroll();
        ticking = false;
      });
      ticking = true;
    }
  });
});

EOF;


//******************************************************************************
//  コードコピーボタンを追加
//******************************************************************************
if (is_ssl()) {
  echo <<<EOF
jQuery(function($) {
  $(".wp-block-code").wrap('<div class="pre-wrap"></div>').before('<button class="code-copy">COPY</button>');

  $('.code-copy').click(function(event) {
    event.preventDefault();

    var \$btn = $(this);
    var codeText = \$btn.siblings('pre').find('code').text();

    navigator.clipboard.writeText(codeText).then(() => {
      \$btn.text("COPIED");
      setTimeout(function() {
        \$btn.text("COPY");
      }, 1000);
    });
  });
});

EOF;

} else {
  echo <<<EOF
jQuery(function($) {
  $(".wp-block-code").wrap('<div class="pre-wrap"></div>').before('<button class="code-copy">COPY</button>');

  const clip = new Clipboard(".code-copy", {
    target: function (trigger) {
      return trigger.nextElementSibling.querySelector('code');
    },
  });

  clip.on("success", function(event) {
    var \$btn = $(event.trigger);
    \$btn.text("COPIED");
    setTimeout(function() {
      \$btn.text("COPY");
    }, 1000);
    event.clearSelection();
  });
});

EOF;
}


//******************************************************************************
//  アコーディオン化
//******************************************************************************
if (get_theme_mod('hvn_accordion_setting')) {
  echo <<<EOF
// カテゴリー、固定ページ
jQuery(function($) {
  $(".widget").each(function() {
    $('.children', this).hide();
    $('.children', this).before('{$button}');

    $('.sub-item', this).click(function() {
      $(this).next('ul').toggle();
      $(this).toggleClass('active');
    });
  });
});

// タグクラウド
jQuery(function($) {
  var n = {$n};

  $('.widget_tag_cloud').each(function() {
    var elm = $('.tagcloud a', this);
    var c   = elm.length;
    if (c > n) {
      elm.slice(n).hide();
      $(this).append('{$button}');
    }

    $('button', this).click(function() {
      elm.slice(n).toggle();
      $(this).toggleClass('active');
    });
  });
});

EOF;
}


//******************************************************************************
//  ダークモード
//******************************************************************************
if (get_theme_mod('hvn_darkmode_setting')) {
  echo <<<EOF
jQuery(function($) {
  const toggle = $('#hvn-dark-toggle');

  // クリックで切り替え
  toggle.on('click', function() {
    const isDark = $('html').toggleClass('hvn-dark').hasClass('hvn-dark');

    // localStorageへの保存
    if (isDark) {
      localStorage.setItem('hvn-dark', 'dark');
    } else {
      localStorage.removeItem('hvn-dark');
    }
  });
});

EOF;
}


//******************************************************************************
//  通知エリアスクロール
//******************************************************************************
if ($GLOBALS['hvn_notice']) {
  $direction = 'vertical';
  $delay = 8000;
  $speed = 2000;
  if (get_theme_mod('hvn_notice_scroll_setting')) {
    $direction = 'horizontal';
    $delay = 0;
    $speed = 15000;
  }

  echo <<<EOF
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
//  「見出しボックス」ブロック（アコーディオン）
//******************************************************************************
echo <<<EOF
jQuery(function($) {
  $('.is-style-accordion.cocoon-block-caption-box > .caption-box-content').hide();
  $('.is-style-accordion.cocoon-block-caption-box > .caption-box-label').click(function() {
    $(this).next('.caption-box-content').toggle();
    $(this).toggleClass('active');
  });
});

EOF;


//******************************************************************************
//  目次省略表示
//******************************************************************************
switch(get_theme_mod('hvn_toc_hidden_setting', '0')) {
  case '1':
    echo <<<EOF
jQuery(function($) {
  var n = {$n};

  $('.main .toc-content').each(function() {
    var toc = $(this);
    var elm = toc.find('li');
    var c = elm.length;

    if (c > n) {
      elm.slice(n).hide();
      toc.append('{$button}');
      toc.find('button').on('click', function() {
        elm.slice(n).toggle();
        $(this).toggleClass('active');
      });
    }
  });
});

EOF;
    break;

  case '2':
    echo <<<EOF
jQuery(function($) {
  $('.main .toc-content').each(function() {
    var toc = $(this);
    var elm = toc.find('ul ul');
    if (elm.length > 0) {
      elm.hide();
      toc.append('{$button}');
      toc.find('button').on('click', function() {
        elm.toggle();
        $(this).toggleClass('active');
      });
    }
  });
});

EOF;
    break;
}


//******************************************************************************
//  「タブ」ブロックのスクロールヒント表示
//******************************************************************************
echo <<<EOF
jQuery(function($){
  new ScrollHint(".tab-label-group", {
    suggestClass: 'is-scroll-tab',
  });
});

EOF;
