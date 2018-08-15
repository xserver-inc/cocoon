/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
window.addEventListener('DOMContentLoaded', function(){
    [].forEach.call(document.querySelectorAll("div.edit-post-visual-editor"), e => e.classList.add('article'));
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