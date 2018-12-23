/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
wp.domReady(function () {
    // add classes
    const addClasses = function () {
        $('#editor .editor-block-list__layout').addClass('article');
    };
    addClasses();

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

// (function($){
//   //タイマーを使ったあまり美しくない方法
//   //tiny_mce_before_initフック引数配列のbody_classがなかったもので。
//   //もしWordPressフックを使った方法や、もうちょっと綺麗なjQueryの書き方があればフォーラムで教えていただければ幸いです。
//   //https://wp-cocoon.com/community/cocoon-theme/
//   setInterval(function(){
//     $('.mce-content-body').addClass('article');
//   },1000);

// })(jQuery);