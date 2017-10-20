<?php //インデックス一覧 ?>
<?php
////////////////////////////
//アーカイブのタイトル
////////////////////////////
if (!is_home() && !is_search()) { ?>
  <h1 id="archive-title" class="archive-title"><?php echo get_archive_chapter_text(); ?></h1>
<?php } ?>

<?php
////////////////////////////
//インデクストップ広告
////////////////////////////
if (is_ad_pos_index_top_visible() && is_all_adsenses_visible()){
  //レスポンシブ広告
  get_template_part_with_ad_format(get_ad_pos_index_top_format(), 'ad-index-top');
}; ?>

<div id="list" class="list<?php echo get_additional_entry_card_classes(); ?>">
<?php
////////////////////////////
//一覧の繰り返し処理
////////////////////////////
$count = 0;
if (have_posts()) : // WordPress ループ
  while (have_posts()) : the_post(); // 繰り返し処理開始
    $count++;
    get_template_part('tmp/entry-card');

    //インデックスミドルに広告を表示してよいかの判別
    if (is_ad_pos_index_middle_visible() && is_index_middle_ad_visible($count) && is_all_adsenses_visible()) {
      get_template_part_with_ad_format(get_ad_pos_index_middle_format(), 'ad-index-middle');
    }
  endwhile; // 繰り返し処理終了 ?>
  <div class="clear"></div>
<?php else : // ここから記事が見つからなかった場合の処理  ?>
  <div class="post">
    <h2>NOT FOUND</h2>
    <p><?php echo __( '投稿が見つかりませんでした。', THEME_NAME );//見つからない時のメッセージ ?></p>
  </div>
<?php
endif;
?>
</div><!-- .list -->