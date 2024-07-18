/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

(function($){
  //タイマーを使ったあまり美しくない方法
  //tiny_mce_before_initフック引数配列のbody_classがなかったもので。
  //もしWordPressフックを使った方法や、もうちょっと綺麗なjQueryの書き方があればフォーラムで教えていただければ幸いです。
  //https://wp-cocoon.com/community/cocoon-theme/
  setInterval(function(){
    //ブロックエディターのラップ要素に必要なクラスを追加する
    const wrapClass = '.block-editor-writing-flow';
    // const addClasses = 'cocoon-block-wrap body article admin-page' + gbSettings['siteIconFont'] + gbSettings['pageTypeClass'];
    let addClasses = ['cocoon-block-wrap', 'body', 'article', 'admin-page'];

    if (typeof gbSettings !== 'undefined') {
      if (gbSettings['siteIconFont']) {
        addClasses.push(gbSettings['siteIconFont'].replace(/\s/g, ""));
      }
      if (gbSettings['pageTypeClass']) {
        addClasses.push(gbSettings['pageTypeClass'].replace(/\s/g, ""));
      }
    }

    let writingFlow = document.querySelector(wrapClass);

    if (writingFlow && !writingFlow.classList.contains('article')) {
      writingFlow.classList.add(...addClasses);
    }

    let iframe = document.querySelectorAll('iframe[name="editor-canvas"]');

    if (iframe.length > 0) {
      let iframeContent = iframe[0].contentDocument || iframe[0].contentWindow.document;
      let element = iframeContent.querySelector('.is-root-container');

      if (element && !element.classList.contains('article')) {
        element.classList.add(...addClasses);
      }
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
