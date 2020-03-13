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


    //barba.js遷移を無効化する
    function barbaPrevent() {
      //管理パネルはbarba.js動作から除外する
      let wpadminbar = document.getElementById("wpadminbar");
      if (wpadminbar) {
        wpadminbar.setAttribute("data-barba-prevent", "all");
      }
    }
    barbaPrevent();

    //head内タグのの移し替え
    function replaceHeadTags(target) {
      let head = document.head;
      let targetHead = target.html.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0];
      //console.log(targetHead);
      let newPageHead = document.createElement('head');
      newPageHead.innerHTML = targetHead;
      // console.log(newPageHead);
      //SEOに関係ありそうなタグ
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
        // "script[type='application/ld+json']",
      ].join(',');
      //前のページの古いタグを削除
      let headTags = [...head.querySelectorAll(removeHeadTags)];
      //console.log(headTags)
      headTags.forEach(item => {
        head.removeChild(item);
      });
      //新しいページの新しいタグを追加
      let newHeadTags = [...newPageHead.querySelectorAll(removeHeadTags)];
      //console.log(newHeadTags)
      newHeadTags.forEach(item => {
        head.appendChild(item);
      });
    }

    //アンカーリンクを考慮したスクロール
    //参考：https://leap-in.com/ja/notes-when-you-use-barba-js-2/
    function pageScroll(){
      let headerFixed = <?php echo is_header_fixed() ? 'true' : 'false'; ?>;
      // check if 「#」 exists
      if(location.hash){
        let anchor = document.querySelector( location.hash );
        if(anchor){
          let rect = anchor.getBoundingClientRect();
          //console.log(rect);
          let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          //let scrollElem = document.scrollingElement || document.documentElement;

          //console.log(scrollTop);
          let top = rect.top + scrollTop;
          // let top = $(location.hash).offset().top;
          //console.log($(location.hash).offset().top);
          // if(headerFixed){
          //   let header = document.getElementById('header-container');
          //   if(header){
          //     top = top + header.clientHeight;
          //   }
          // }
          window.scrollTo(0, top);
        }else{
          // no anchor, go to top position
          window.scrollTo(0, 0);
        }
      }else{
        // no anchor, go to top position
        window.scrollTo(0, 0);
      }
    }

    function footerTagsLoad(target) {
      let footerHtml = target.html.match(/<div id="go-to-top" class="go-to-top">([\s\S.]*)$/i)[0];
      //console.log(footerHtml);
      let footerScripts = footerHtml.match(/<script[^>]*>([\s\S.]*?)<\/script>/ig);
      //console.log(footerScripts);
      //$('script').delete();
      footerScripts.forEach(script => {
        if (!script.match(/barba/)) {
          //console.log(script);
          //$(script).delete();
          //script属性にsrcがある場合
          let res = script.match(/ src="(.+?)"/);
          let scriptTag = document.createElement("script");

          if (res) {
            //console.log(res[1]);
            scriptTag.async = true;
            // scriptTag.defer = true;
            scriptTag.src = res[1];
          } else {
            //script内にコードがある場合
            let code = script.match(/<script[^>]*>([\s\S.]+?)<\/script>/i);
            //console.log(script);
            if (code) {
              //console.log(code[1]);
              // scriptTag.async = true;
              scriptTag.innerHTML = code[1];
            }
          }
          document.getElementById("container").appendChild(scriptTag);
          // let url = script.match(/ src="(.+?)"/)[1];
          // console.log(url);
          // if (url) {

          // } else {

          // }
          // console.log($(script));
          // $('#container').append(script);
          //document.getElementById("container").appendChild($(script));
        }
        //
      });
      //console.log(footerScripts);
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
    //http://absg.hatenablog.com/entry/2018/08/31/230703
    function tweetLoad() {
      let tweet = document.getElementsByClassName('twitter-tweet');
      if (tweet) {
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
    //Instagramスクリプトの呼び出し
    function instagramLoad() {
      let im = document.getElementsByClassName('instagram-media');
      if (im) {
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

    //LinkSwitch
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

    //barba.jsの実行
    barba.init({
      prevent: function (e) {
        // //同一リンクの遷移防止（ページ移動できず混乱を招くので不要かも？）
        // if (e.href === location.href) {
        //   //リンクを動作させない
        //   e.el.setAttribute('href','javascript:void(0)');
        //   //CSSで対応する場合
        //   //e.el.classList.add('click-prevention');
        // }
        <?php
        $urls = list_text_to_array(get_highspeed_mode_exclude_list());
        $joined_urls = implode(',', $urls);
        ?>
        // console.log(e);
        $res = false;
        let joinedUrls = '<?php echo $joined_urls;?>';
        if (joinedUrls) {
          let urls = joinedUrls.split(',');
          // console.log(urls);
          urls.forEach(item => {
            // console.log(item+' == '+e.href+' => '+e.href.includes(item));
            $res = $res || e.href.includes(item);
          });
        }
        return $res;
      },
      transitions: [
        {
          before({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_before'); ?>
          },
          beforeLeave({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_before_leave'); ?>
          },
          leave({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_leave'); ?>
          },
          afterLeave({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_after_leave'); ?>
          },
          beforeEnter({ current, next, trigger }) {

            //headタグ変換
            replaceHeadTags(next);

            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_before_enter'); ?>
          },
          enter({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_enter'); ?>
          },
          afterEnter({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_after_enter'); ?>
          },
          after({ current, next, trigger }) {
            //console.log(current);
            <?php if ($analytics_tracking_id && is_analytics()): ?>
              //Google Analytics
              gaPush(location.pathname);
            <?php endif; ?>
            //ツイート埋め込み
            tweetLoad();
            //instagram埋め込み
            instagramLoad();
            //footerTagsLoad(current);

            //LinkSwitch
            LinkSwitchLoad();

            <?php //フッタースクリプトの読み込み
            //wp_footer()コードの再読み込み
            global $_WP_FOOTER;
            generate_baruba_js_scripts($_WP_FOOTER);
            //テンプレートのスクリプトも再読み込み
            ob_start();
            get_template_part('tmp/footer-scripts');
            $scripts = ob_get_clean();
            generate_baruba_js_scripts($scripts);
            ?>

            //アンカーリンク対応
            pageScroll();
            //pjax防止リンクの設定
            barbaPrevent();

            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_after'); ?>
          }
        }
      ]
    });

  })($);
  </script>
  <?php endif; ?>
<?php endif; ?>
