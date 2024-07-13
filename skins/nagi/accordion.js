jQuery(document).ready(function($) {
    // 子カテゴリーを持つ親カテゴリーをキャッシュ
    var $catItems = $('li.cat-item:has(> ul.children)');

    // アコーディオンボタンの追加
    $catItems.append('<span class="cat_open_btn"></span>');

    // アコーディオンボタンクリックイベント
    $(document).on('click', '.cat_open_btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        $this.prev('ul.children').slideToggle('slow');
        $this.toggleClass('open');
    });
});
