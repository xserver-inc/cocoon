<?php //barba.js処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp()): ?>
  <?php if (is_highspeed_mode_enable()): ?>
  <script>
  (function($){
    //管理パネルはbarba.js動作から除外する
    var d = document.getElementById("wpadminbar");
    if (d) {
      d.setAttribute("data-barba-prevent", "all");
    }

    //barba.js
    //barba.use(barbaPrefetch);

    //head内タグのの移し替え
    function replaceHeadTags(target) {
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
      });
    }

    <?php
    $analytics_tracking_id = get_google_analytics_tracking_id();
    if ($analytics_tracking_id && is_analytics()): ?>
    //Google Analytics
    function gaPush(pagename) {
      //古いAnalyticsコード
      if (typeof ga === 'function') {
        ga('send', 'pageview', pagename);
      }
      //gtag.js（公式）
      if (typeof gtag === 'function') {
        //console.log('gtag');
        gtag('config', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
      }
      //ga-lite.min.js（高速化）
      if (typeof galite === 'function') {
        galite('create', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
        galite('send', 'pageview');
      }
    }
    <?php endif; ?>

    //Twitterスクリプトの呼び出し
    function tweetLoad() {
      if (typeof twttr === 'undefined') {
        let twitterjs = document.createElement("script");
        twitterjs.async = true;
        twitterjs.src = '//platform.twitter.com/widgets.js';
        document.getElementsByTagName('body')[0].appendChild(twitterjs);
      } else {
        twttr.widgets.load();
      }
    }
    //Instagramスクリプトの呼び出し
    function instagramLoad() {
      if (typeof window.instgrm === 'undefined') {
					let instagramjs = document.createElement("script");
					instagramjs.async = true;
					instagramjs.src = '//www.instagram.com/embed.js';
					document.getElementsByTagName('body')[0].appendChild(instagramjs);
				} else {
					window.instgrm.Embeds.process();
        }
    }

    //barba.jsの実行
    barba.init({
      transitions: [
        {
          before({ current, next, trigger }) {
            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_before'); ?>
            // console.log(current.url.href);
            // console.log(next.url.href);
            // if (current.url.href == next.url.href) {
            //   return false;
            //   exit;
            // }
          },
          beforeLeave({ current, next, trigger }) {
            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_before_leave'); ?>
          },
          leave({ current, next, trigger }) {
            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_leave'); ?>
          },
          afterLeave({ current, next, trigger }) {
            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_after_leave'); ?>
          },
          beforeEnter({ current, next, trigger }) {
            //console.log('beforeEnter');
            //console.log(next);
            //headタグ変換
            replaceHeadTags(next);
            <?php if ($analytics_tracking_id && is_analytics()): ?>
            //Google Analytics
            gaPush(location.pathname);
            <?php endif; ?>
            //ツイート埋め込み
            tweetLoad();
            //instagram埋め込み
            instagramLoad();
            // //ページトップに移動
            // const scrollElem = document.scrollingElement || document.documentElement;
            // scrollElem.scrollTop = 0;
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

            //URLのアンカー（?以降の部分）を取得、加工してアンカーに移動する
            //var urlSearch = location.search;
            //urlSearch = getGET(); //「?」を除去
            // console.log(current);

            // const scrollElem = document.scrollingElement || document.documentElement;
            // //console.log(tgt);
            // var hash = location.hash;
            // // console.log(hash);
            // // console.log(current);
            // //ハッシュ値がある場合
            // if (hash) {
            //   // var anchor = document.getElementById(hash);
            //   // console.log(anchor);
            //   const target = $(hash).offset().top; //アンカーの位置情報を取得
            //   //console.log(target);
            //   scrollElem.scrollTop = Math.floor(target);
            // } else {
            //   scrollElem.scrollTop = 0;
            // }

            //window.history.pushState(null, null, pagelinkHref);

            // // ブラウザがpushStateに対応しているかチェック
            // if (window.history && window.history.pushState){
            //   $(window).on("popstate",function(event){
            //     console.log(event);
            //     // if (!event.originalEvent.state) return; // 初回アクセス時対策
            //     // var state = event.originalEvent.state; // stateオブジェクト
            //   });
            // }

            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_before_enter'); ?>
          },
          enter({ current, next, trigger }) {
            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_enter'); ?>
          },
          afterEnter({ current, next, trigger }) {
            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_after_enter'); ?>
          },
          after({ current, next, trigger }) {
            // $(".carousel-content").slick({
            //   dots: true,
            //   infinite: true,
            //   slidesToShow: 6,
            //   slidesToScroll: 6,
            //   responsive: [
            //       {
            //         breakpoint: 1240,
            //         settings: {
            //           slidesToShow: 5,
            //           slidesToScroll: 5
            //         }
            //       },
            //       {
            //         breakpoint: 1023,
            //         settings: {
            //           slidesToShow: 4,
            //           slidesToScroll: 4
            //         }
            //       },
            //       {
            //         breakpoint: 834,
            //         settings: {
            //           slidesToShow: 3,
            //           slidesToScroll: 3
            //         }
            //       },
            //       {
            //         breakpoint: 480,
            //         settings: {
            //           slidesToShow: 2,
            //           slidesToScroll: 2
            //         }
            //       }
            //     ]
            // });

            <?php //一応PHPからも操作出来るようにフック
            do_action('barba_init_transitions_after'); ?>
          }
        }
      ]
    });

    // const eventDelete = e => {
    //   if (e.currentTarget.href === window.location.href) {
    //     // console.log(e.currentTarget.href);
    //     // console.log(window.location.href);
    //     e.preventDefault();
    //     e.stopPropagation();
    //     return;
    //   }
    // };

    // const links = [...document.querySelectorAll('a[href]')];
    // //console.log(links);
    // links.forEach(link => {
    //   link.addEventListener('click', e => {
    //     //console.log('click');
    //     eventDelete(e);
    //   }, false);
    // });
  })($);
  </script>
  <?php endif; ?>
<?php endif; ?>
