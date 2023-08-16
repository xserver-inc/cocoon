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
      setTimeout(addClasses, 1);
  }));
});

(function($){
  //タイマーを使ったあまり美しくない方法
  //tiny_mce_before_initフック引数配列のbody_classがなかったもので。
  //もしWordPressフックを使った方法や、もうちょっと綺麗なjQueryの書き方があればフォーラムで教えていただければ幸いです。
  //https://wp-cocoon.com/community/cocoon-theme/
  setInterval(function(){
    //ブロックエディターのラップ要素に必要なクラスを追加する
    let writingFlow = jQuery('#editor .block-editor-writing-flow');
    if (!writingFlow.hasClass('article')) {
      writingFlow.addClass('cocoon-block-wrap body article admin-page' + gbSettings['siteIconFont'] + gbSettings['pageTypeClass']);
    }

    //グループボックスのスタイルプレビューに余計なstyle属性が入り込んでしまうのを削除
    //もっと良い方法があるのかもしれない
    jQuery('.block-editor-block-preview__content .wp-block-group').removeAttr('style');
    jQuery('.btn-wrap-block a').click(function(event) {
      event.preventDefault();
    });

  },1000);

  setInterval(function(){
    jQuery("button:contains('HTML挿入')").addClass('html-insert-button cocoon-donation-privilege');
    jQuery("button:contains('ページの更新日')").addClass('shortcode-updated-button cocoon-donation-privilege');
  },100);

})(jQuery);
