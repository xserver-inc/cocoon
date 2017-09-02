<?php get_header(); ?>

<?php get_template_part('tmp/content') ?>

<?php get_template_part('tmp/breadcrumbs') ?>


<div class="under-entry-content">
  <?php get_template_part('tmp/related-entries'); //関連記事の呼び出し ?>

  <?php get_template_part('tmp/pager-post-navi'); //関連記事の呼び出し ?>

  <?php comments_template(); //コメントテンプレート?>
</div>



<?php get_footer(); ?>