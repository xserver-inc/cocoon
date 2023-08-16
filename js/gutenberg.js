/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//  console.log('1');
document.addEventListener('DOMContentLoaded', function() {
  // add classes
  const addClasses = function () {
    // console.log('2');
    // console.log(jQuery('.block-editor-writing-flow'));
    // add body class
    // jQuery('#editor .block-editor-writing-flow').wrap('<div>');
    // jQuery('#editor .block-editor-writing-flow').parent().addClass('cocoon-block-wrap body article admin-page' + gbSettings['siteIconFont'] + gbSettings['pageTypeClass']);

    jQuery('#editor .block-editor-writing-flow').addClass('cocoon-block-wrap body article admin-page' + gbSettings['siteIconFont'] + gbSettings['pageTypeClass']);
    // console.log(jQuery('#editor .block-editor-writing-flow'));
  };
  setTimeout(addClasses, 1000);

});

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
