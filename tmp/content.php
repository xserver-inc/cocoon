<article id="post-<?php the_ID(); ?>" <?php post_class() ?> role="article" itemscope="" itemprop="blogPost" itemtype="http://schema.org/BlogPosting">
  <?php
  if ( have_posts() ) {
    while ( have_posts() ) {
      the_post();?>

      <header class="article-header entry-header">
        <h1 class="entry-title" itemprop="headline" rel="bookmark"><?php the_title() ?></h1>

        <?php get_template_part('tmp/eye-catch');//アイキャッチ挿入機能?>
      </header>

      <div class="entry-content cf" itemprop="articleBody">
      <?php //記事本文の表示
        the_content(); ?>
      </div>

      <footer class="article-footer entry-footer">
        <div class="entry-categories-tags">

        </div>
      </footer>

    <?php
    } // end while
  } //have_posts end if?>
</article>

<div class="under-entry-content">

</div>
