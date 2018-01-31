<div class="article live-writer">
  <?php
  if ( have_posts() ) {
    while ( have_posts() ) {
      the_post();?>

      <h1 class="entry-title"><?php the_title() ?></h1>

      <div class="entry-content cf">
      <?php //記事本文の表示
        the_content(); ?>
      </div>

    <?php
    } // end while
  } //have_posts end if?>
</div>
