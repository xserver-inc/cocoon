<?php get_header(); ?>

<?php //パンくずリストがメイントップの場合
if (is_single_breadcrumbs_position_main_top()){
  get_template_part('tmp/breadcrumbs');
} ?>

<?php //本文テンプレート
get_template_part('tmp/content') ?>


<div class="under-entry-content">
  <?php get_template_part('tmp/related-entries'); //関連記事 ?>

  <?php //関連記事下の広告表示
  if (is_ad_pos_below_related_posts_visible() && is_all_adsenses_visible()){
    get_template_part_with_ad_format(get_ad_pos_below_related_posts_format(), 'ad-below-related-posts');
  }; ?>

  <?php get_template_part('tmp/pager-post-navi'); //投稿ナビ ?>

  <?php comments_template(); //コメントテンプレート?>
</div>

<?php //パンくずリストがメインボトムの場合
if (is_single_breadcrumbs_position_main_bottom()){
  get_template_part('tmp/breadcrumbs');
} ?>

<?php get_footer(); ?>