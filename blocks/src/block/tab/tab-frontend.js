// // obtain tab labels from DOM
// const tabLabels = document.querySelectorAll('.tab-label');
// // obtain tab contents from DOM
// const tabContentGroup = document.getElementsByClassName('tab-content-group')[0];
// const tabContents = tabContentGroup.children;

// // add tab-content class
// // for (index = 0; index < tabLabels.length; index++) {
// //   tabContents[index].classList.add("tab-content");
// // }

// // select first tab
// if (tabLabels.length > 0) {
//   tabLabels[0].classList.add("is-active");
//   tabContents[0].classList.add("is-active");
// }

// // add click event to each tab label
// tabLabels.forEach((tabLabel) => {
//   tabLabel.addEventListener('click', tabChanged);
// })

// // switch tab
// function tabChanged(e) {
//   // remove is-active from label and content
//   tabLabels.forEach((tabLabel) => {
//     tabLabel.classList.remove("is-active");
//   });
//   for (index = 0; index < tabLabels.length; index++) {
//     tabContents[index].classList.remove("is-active");
//   }

//   // check which label is clicked
//   const target = e.target;
//   var index;
//   for (index = 0; index < tabLabels.length; index++) {
//     if (target === tabLabels[index]) {
//       break;
//     }
//   }

//   // add is-active to label and content
//   tabLabels[index].classList.add("is-active");
//   tabContents[index].classList.add("is-active");
// }

(function($){
  setInterval(function(){
    $('.cocoon-block-tab').each(function( i, block ) {
      const isActive = $(block).find('.is-active');
      //タブにアクティブが何も設定されていない時
      if (isActive.length === 0) {
        const label = $(block).find('.tab-label');
        if (label) {
          //最初のラベルアクティブに
          label.eq(0).addClass('is-active');
        }
        const content = $(block).find('.tab-content');
        if (content) {
          //最初のラベルをアクティブに
          content.eq(0).addClass('is-active');
        }
      }
    });

    //タブのラベル要素をクリックしたとき
    $('.tab-label').on('click', function() {

      //タブラベルのクリックからタブブロック要素を取得する
      const tabBlock = $(this).parent().parent();
      //アクティブを削除する
      tabBlock.find('.tab-label').removeClass('is-active');
      tabBlock.find('.tab-content').removeClass('is-active');

      //ラベルをアクティブにする
      $(this).addClass('is-active');
      //内容をアクティブにする
      const index = $(this).index();
      console.log(this);
      console.log(tabBlock.find('.tab-content').eq(index));
      console.log(index);
      tabBlock.find('.tab-content').eq(index).addClass('is-active');
    });
  },1000);
})(jQuery);