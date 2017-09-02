<?php get_header(); ?>

<?php get_template_part('tmp/content') ?>

<?php get_template_part('tmp/breadcrumbs') ?>


<div class="under-entry-content">
  <?php get_template_part('tmp/related-entries'); //関連記事の呼び出し ?>
</div>

<?php comments_template(); //コメントテンプレート?>

<?php get_footer(); ?>