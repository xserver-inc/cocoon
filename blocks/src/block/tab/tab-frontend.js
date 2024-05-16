// document.addEventListener('DOMContentLoaded', function() {
//   $('.cocoon-block-tab').each(function( i, block ) {
//     const isActive = $(block).find('.is-active');
//     //タブにアクティブが何も設定されていない時
//     if (isActive.length === 0) {
//       const label = $(block).find('.tab-label');
//       if (label) {
//         //最初のラベルアクティブに
//         label.eq(0).addClass('is-active');
//       }
//       const content = $(block).find('.tab-content');
//       if (content) {
//         //最初のラベルをアクティブに
//         content.eq(0).addClass('is-active');
//       }
//     }
//   });

//   //タブのラベル要素をクリックしたとき
//   $('.tab-label').on('click', function() {

//     //タブラベルのクリックからタブブロック要素を取得する
//     const tabBlock = $(this).parent().parent();
//     //アクティブを削除する
//     tabBlock.find('.tab-label').removeClass('is-active');
//     tabBlock.find('.tab-content').removeClass('is-active');

//     //ラベルをアクティブにする
//     $(this).addClass('is-active');
//     //内容をアクティブにする
//     const index = $(this).index();
//     tabBlock.find('.tab-content').eq(index).addClass('is-active');
//   });
// });

document.addEventListener('DOMContentLoaded', function() {
  function firstTabActive($selector) {
    const cocoonBlocks = document.querySelectorAll($selector);
    cocoonBlocks.forEach(function(block) {
      const isActive = block.querySelector('.is-active');
      // タブにアクティブが何も設定されていない時
      if (!isActive) {
        const label = block.querySelector('.tab-label');
        if (label) {
          // 最初のラベルアクティブに
          label.classList.add('is-active');
        }
        const content = block.querySelector('.tab-content');
        if (content) {
          // 最初のラベルをアクティブに
          content.classList.add('is-active');
        }
      }
    });
  }

  //ページ読み込み時3ブロックの最初のタブをアクティブにする
  firstTabActive('.cocoon-block-tab');

  // タブのラベル要素をクリックしたとき
  const tabLabels = document.querySelectorAll('.tab-label');
  tabLabels.forEach(function(label) {
    label.addEventListener('click', function() {
      // タブラベルのクリックからタブブロック要素を取得する
      const tabBlock = this.parentElement.parentElement;
      // アクティブを削除する
      tabBlock.querySelectorAll('.tab-label').forEach(function(lbl) {
        lbl.classList.remove('is-active');
      });
      tabBlock.querySelectorAll('.tab-content').forEach(function(cnt) {
        cnt.classList.remove('is-active');
      });

      // ラベルをアクティブにする
      this.classList.add('is-active');
      // 内容をアクティブにする
      const index = Array.from(tabBlock.querySelectorAll('.tab-label')).indexOf(this);
      const tabContentGroup = tabBlock.querySelectorAll('.tab-content-group')[0];
      if (tabContentGroup) {
        tabContentGroup.children[index].classList.add('is-active');
        firstTabActive('.tab-content.is-active');
      }
    });
  });
});

