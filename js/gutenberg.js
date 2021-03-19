/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//  console.log('1');

wp.domReady(function () {
    // add classes
    const addClasses = function () {
        // console.log('2');
        // console.log(jQuery('.block-editor-writing-flow'));
        // add body class
        jQuery('#editor .block-editor-writing-flow').wrap('<div>');
        jQuery('#editor .block-editor-writing-flow').parent().addClass('cocoon-block-wrap body article' + gbSettings['siteIconFont'] + gbSettings['pageTypeClass']);

        // add title class
        // jQuery('#editor .editor-post-title__input').addClass('entry-title');
    };
    setTimeout(addClasses, 100);
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
    //$('#editor .block-editor-writing-flow').addClass('body main article page-body ' + gbSettings['siteIconFont']);

    //グループボックスのスタイルプレビューに余計なstyle属性が入り込んでしまうのを削除
    //もっと良い方法があるのかもしれない
    jQuery('.block-editor-block-preview__content .wp-block-group').removeAttr('style');
  },1000);

})(jQuery);
