<?php get_header(); ?>

<?php get_template_part('tmp/content') ?>


<div class="under-entry-content">
  <?php get_template_part('tmp/related-entries'); //関連記事 ?>

  <?php get_template_part('tmp/pager-post-navi'); //投稿ナビ ?>

  <?php comments_template(); //コメントテンプレート?>
</div>

<?php get_template_part('tmp/breadcrumbs') ?>

<?php get_footer(); ?>