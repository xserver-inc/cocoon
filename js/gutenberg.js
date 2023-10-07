/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

wp.domReady(function () {
  // subscribe switch editor mode
  wp.data.subscribe(function (selector, listener) {
    let previousValue = selector();
    return function () {
      let selectedValue = selector();
      if (selectedValue !== previousValue) {
        previousValue = selectedValue;
        listener(selectedValue);
      }
    };
  }(function () {
      return wp.data.select('core/edit-post').getEditorMode();
  }, function () {
  }));
});

(function($){
  //タイマーを使ったあまり美しくない方法
  //tiny_mce_before_initフック引数配列のbody_classがなかったもので。
  //もしWordPressフックを使った方法や、もうちょっと綺麗なjQueryの書き方があればフォーラムで教えていただければ幸いです。
  //https://wp-cocoon.com/community/cocoon-theme/
  setInterval(function(){
    //ブロックエディターのラップ要素に必要なクラスを追加する
    const wrapClass = '.block-editor-writing-flow';
    const addClasses = 'cocoon-block-wrap body article admin-page' + gbSettings['siteIconFont'] + gbSettings['pageTypeClass']
    let writingFlow = jQuery(wrapClass);
    if (!writingFlow.hasClass('article')) {
      writingFlow.addClass(addClasses);
    }
    let iframe = jQuery('iframe[name="editor-canvas"]');

    if (iframe.length > 0) {
      let iframeContent = iframe.contents();
      let element = iframeContent.find('.is-root-container');

      if (!element.hasClass('article')) {
        element.addClass(addClasses);
      }

      // //ヘッダーにFont Awesome様を挿入する
      // let iframeHead = iframe.contents().find("head");

      // if (iframeHead.has("#font-awesome-style-css-iframe").length == 0) {
      //   const templateUrl = gbSettings['templateUrl'];
      //   if (gbSettings['siteIconFont'].trim() == 'font-awesome-4') {
      //     // Font Awesome4の場合
      //     // リンクタグを作成
      //     let link1 = $("<link>", {
      //       rel: "stylesheet",
      //       id: "font-awesome-style-css-iframe",
      //       href: templateUrl + "/webfonts/fontawesome/css/font-awesome.min.css",
      //       media: "all"
      //     });

      //     // リンクタグをiframe内のhead要素に挿入
      //     iframeHead.append(link1);
      //   } else {
      //     // Font Awesome5の場合

      //     // リンクタグを作成
      //     let link1 = $("<link>", {
      //       rel: "stylesheet",
      //       id: "font-awesome-style-css-iframe",
      //       href: templateUrl + "/webfonts/fontawesome5/css/all.min.css",
      //       media: "all"
      //     });
      //     let link2 = $("<link>", {
      //       rel: "stylesheet",
      //       id: "font-awesome5-update-style-css-iframe",
      //       href: templateUrl + "/css/fontawesome5.css",
      //       media: "all"
      //     });

      //     // リンクタグをiframe内のhead要素に挿入
      //     iframeHead.append(link1);
      //     iframeHead.append(link2);
      //   }
      // }
    }

    //グループボックスのスタイルプレビューに余計なstyle属性が入り込んでしまうのを削除
    //もっと良い方法があるのかもしれない
    jQuery('.block-editor-block-preview__content .wp-block-group').removeAttr('style');
    jQuery('.btn-wrap-block a').click(function(event) {
      event.preventDefault();
    });

  },2000);

  setInterval(function(){
    jQuery("button:contains('HTML挿入')").addClass('html-insert-button cocoon-donation-privilege');
    jQuery("button:contains('ページの更新日')").addClass('shortcode-updated-button cocoon-donation-privilege');
  },100);

})(jQuery);
