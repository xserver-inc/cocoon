document.addEventListener('DOMContentLoaded', function() {
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
    tabBlock.find('.tab-content').eq(index).addClass('is-active');
  });
});
