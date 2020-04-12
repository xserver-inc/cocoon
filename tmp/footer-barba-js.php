<?php /*barba.js処理*/
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
ob_start();

if (!is_amp()): ?>
  <?php if (is_highspeed_mode_enable()): ?>
  <script>
  (function($){


    /*barba.js遷移を無効化する*/
    function barbaPrevent() {
      /*管理パネルはbarba.js動作から除外する*/
      let wpadminbar = document.getElementById("wpadminbar");
      if (wpadminbar) {
        wpadminbar.setAttribute("data-barba-prevent", "all");
      }
    }
    barbaPrevent();

    /*head内タグのの移し替え*/
    function replaceHeadTags(next) {
      let head = document.head;
      let nextHead = next.html.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0];
      //console.log(nextHead);
      let newPageHead = document.createElement('head');
      newPageHead.innerHTML = nextHead;

      /*SEOに関係ありそうなタグ*/
      let removeHeadTags = [
        "style",
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
        "script",
        /* "script[type='application/ld+json']",*/
      ].join(',');
      /*前のページの古いタグを削除*/
      let headTags = [...head.querySelectorAll(removeHeadTags)];
      headTags.forEach(item => {
        head.removeChild(item);
      });

      /*新しいページの新しいタグを追加*/
      let newHeadTags = [...newPageHead.querySelectorAll(removeHeadTags)];
      newHeadTags.forEach(item => {
        head.appendChild(item);
      });

      let linkStyleTags = [
        "link[rel='stylesheet']",
      ].join(',');

      /*古いlink[rel='stylesheet'*/
      let oldLinkStyleTags = [...head.querySelectorAll(linkStyleTags)];
      /*新しいlink[rel='stylesheet'*/
      let newLinkStyleTags = [...newPageHead.querySelectorAll(linkStyleTags)];

      let appendLinkStyleTags = [];
      newLinkStyleTags.forEach(newItem => {
        let isAppend = true;
        oldLinkStyleTags.forEach(oldwItem => {
          if (newItem.href == oldwItem.href) {
            isAppend = false;
          }
        });
        if (isAppend) {
          head.appendChild(newItem);
        }
      });
    }

    /*body内タグのの移し替え*/
    function replaceBodyTags(next) {
      let head = document.head;
      let body = document.body;
      let nextBody = next.html.match(/<body[^>]*>([\s\S.]*)<\/body>/i)[0];
      let newPageBody = document.createElement('body');
      newPageBody.innerHTML = nextBody;

      let scriptTags = [
        "script",
      ].join(',');


      /*新しいページの新しいタグを追加*/
      let newBodyTags = [...newPageBody.querySelectorAll(scriptTags)];
      newBodyTags.forEach(item => {
        body.appendChild(item);

        /*
        scriptTag = document.createElement("script");
        if (item.src) {
          scriptTag.src = item.src;
          body.appendChild(scriptTag);
        } else {
          if (!item.innerHTML.match('baruba') && !item.innerHTML.match('tiny')) {
            eval(item.innerHTML);
            console.log(item.innerHTML);
          }

          if (!scriptTag.innerHTML.match('baruba')) {
            console.log(scriptTag);
            scriptTag.async = true;
            body.appendChild(scriptTag);
            body.appendChild(item);
          }
        }
        */

      });
    }

    /*アンカーリンクを考慮したスクロール
    参考：https://leap-in.com/ja/notes-when-you-use-barba-js-2/*/
    function pageScroll(){
      let headerFixed = <?php echo is_header_fixed() ? 1 : 0; ?>;
      let headerContainer = document.getElementById('header-container');
      let fixedHeaderClass = 'fixed-header';

      /* check if 「#」 exists */
      if(location.hash){
        let anchor = document.querySelector( location.hash );
        if(anchor){
          if (headerFixed) {
            headerContainer.classList.remove(fixedHeaderClass);
          }

          let rect = anchor.getBoundingClientRect();
          let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

          let top = rect.top + scrollTop;
          if(headerFixed){
            if(headerContainer){
              /*ヘッダーが固定されている場合の高さ調整*/
              top = top - headerContainer.clientHeight - 60;
              /*60はみやすくするための調整値*/
            }
          }

          window.scrollTo(0, top);
        }else{
          /* no anchor, go to top position */
          window.scrollTo(0, 0);
        }
      }else{
        /* no anchor, go to top position */
        window.scrollTo(0, 0);
      }
    }


    <?php
    $analytics_tracking_id = get_google_analytics_tracking_id();
    if ($analytics_tracking_id && is_analytics()): ?>
    /*Google Analytics*/
    function gaPush(pagename) {
      /*古いAnalyticsコード*/
      if (typeof ga === 'function') {
        ga('send', 'pageview', pagename);
      }
      /*gtag.js（公式）*/
      if (typeof gtag === 'function') {
        gtag('config', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
      }
      /*ga-lite.min.js（高速化）*/
      if (typeof galite === 'function') {
        galite('create', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
        galite('send', 'pageview');
      }
    }
    <?php endif; ?>

    /*Twitterスクリプトの呼び出し
    http://absg.hatenablog.com/entry/2018/08/31/230703*/
    function tweetLoad() {
      let tweets = document.getElementsByClassName('twitter-tweet');
      if (tweets.length > 0) {
        if (typeof twttr === 'undefined') {
          let twitterjs = document.createElement("script");
          twitterjs.async = true;
          twitterjs.src = '//platform.twitter.com/widgets.js';
          document.getElementById("container").appendChild(twitterjs);
        } else {
          twttr.widgets.load();
        }
      }
    }

    /*Instagramスクリプトの呼び出し*/
    function instagramLoad() {
      let instagrams = document.getElementsByClassName('instagram-media');
      if (instagrams.length > 0) {
        if (typeof window.instgrm === 'undefined') {
          let instagramjs = document.createElement("script");
          instagramjs.async = true;
          instagramjs.src = '//www.instagram.com/embed.js';
          document.getElementById("container").appendChild(instagramjs);
        } else {
          window.instgrm.Embeds.process();
        }
      }
    }

    /*AdSense*/
    function adsenseLoad() {
      const adsenseClass = '.adsbygoogle';
      const ads = document.body.querySelectorAll(adsenseClass);
      if (ads.length > 0) {
        ads.forEach(function (ad) {

          parent = $(ad).parent();
          script = parent.children('script');
          parent.children(adsenseClass).remove();
          parent.children('script').remove();
          parent.append($(ad));
          parent.append($(script));

        });
      }
    };

    /*
    document.addEventListener('load', function () {
      var ads = document.querySelectorAll('.adsbygoogle');

      if (ads.length > 0) {
        ads.forEach(function (ad) {
          if (ad.firstChild) {
            ad.removeChild(ad.firstChild);
          }
        });
        ads.forEach(function() {
          window.adsbygoogle = window.adsbygoogle || [];
          window.adsbygoogle.push({});
        });
      }
    });
    */

    /*LinkSwitch*/
    function LinkSwitchLoad() {
      if  (typeof VcDal === 'function') {
        let vcdalObj = new VcDal;
        vcdalObj.finishLoad();
      }

      if (typeof myLinkBoxDal === 'function'){
        let mlbObj = new myLinkBoxDal;
        mlbObj.finishLoad();
      }
    }

    /*barba.jsの実行*/
    barba.init({
      prevent: function (e) {
        /*
        //同一リンクの遷移防止（ページ移動できず混乱を招くので不要かも？）
        if (e.href === location.href) {
          //リンクを動作させない
          e.el.setAttribute('href','javascript:void(0)');
          //CSSで対応する場合
          //e.el.classList.add('click-prevention');
        }
        */
        <?php
        $urls = list_text_to_array(get_highspeed_mode_exclude_list());
        $joined_urls = implode(',', $urls);
        ?>
        $res = false;
        let joinedUrls = '<?php echo $joined_urls;?>';
        if (joinedUrls) {
          let urls = joinedUrls.split(',');
          urls.forEach(item => {
            $res = $res || e.href.includes(item);
          });
        }
        return $res;
      },
      transitions: [
        {
          before({ current, next, trigger }) {
            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_before'); ?>
          },
          beforeLeave({ current, next, trigger }) {
            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_before_leave'); ?>
          },
          leave({ current, next, trigger }) {
            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_leave'); ?>
          },
          afterLeave({ current, next, trigger }) {
            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_after_leave'); ?>
          },
          beforeEnter({ current, next, trigger }) {
            /*headタグ変換*/
            replaceHeadTags(next);

            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_before_enter'); ?>
          },
          enter({ current, next, trigger }) {
            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_enter'); ?>
          },
          afterEnter({ current, next, trigger }) {
            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_after_enter'); ?>
          },
          after({ current, next, trigger }) {
            <?php if ($analytics_tracking_id && is_analytics()): ?>
              /*Google Analytics*/
              gaPush(location.pathname);
            <?php endif; ?>
            /*ツイート埋め込み*/
            tweetLoad();

            /*instagram埋め込み*/
            instagramLoad();

            /*AdSense
            adsenseLoad();*/

            /*LinkSwitch*/
            LinkSwitchLoad();

            //scriptタグの付け替え
            // replaceBodyTags(next);

            <?php
            /*ヘッダーの読み込み*/
            // ob_start();
            // get_template_part('header');
            // $head = ob_get_clean();
            // _v($head);

            /*フッタースクリプトの読み込み*/
            /*wp_footer()コードの再読み込み*/
            global $_WP_FOOTER;
            generate_baruba_js_scripts($_WP_FOOTER);
            /*テンプレートのスクリプトも再読み込み*/
            ob_start();
            get_template_part('tmp/footer-scripts');
            $scripts = ob_get_clean();
            generate_baruba_js_scripts($scripts);
            ?>

            /*アンカーリンク対応*/
            pageScroll();
            /*pjax防止リンクの設定*/
            barbaPrevent();

            <?php /*一応PHPからもスクリプトを挿入できるようにフック*/
            do_action('barba_init_transitions_after'); ?>
          }
        }
      ]
    });

  })(jQuery);
  </script>
  <?php endif; ?>
<?php endif; ?>
<?php
$buffer = ob_get_clean();
/*JS縮小化*/
if (is_js_minify_enable()) {
  //$buffer = tag_code_to_minify_js($buffer);
}
echo $buffer;
?>
