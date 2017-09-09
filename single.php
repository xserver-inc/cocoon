<?php get_header(); ?>

<?php get_template_part('tmp/content') ?>


<div class="under-entry-content">
  <?php get_template_part('tmp/related-entries'); //関連記事 ?>

  <?php //関連記事下の広告表示
  if (is_ad_pos_below_related_posts_visible()){
    //レスポンシブ広告のフォーマットにrectangleを指定する
    get_template_part_with_ad_format(DATA_AD_FORMAT_RECTANGLE, 'ad-below-related-posts');
  }; ?>

  <?php get_template_part('tmp/pager-post-navi'); //投稿ナビ ?>

  <?php comments_template(); //コメントテンプレート?>
</div>

<?php get_template_part('tmp/breadcrumbs') ?>

<?php get_footer(); ?>