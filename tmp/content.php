<article id="post-<?php the_ID(); ?>" <?php post_class() ?>>
  <header>
    <h1 class="entry-title" itemprop="headline" rel="bookmark"><?php the_title() ?></h1>

    <?php get_template_part('tmp/eye-catch');//アイキャッチ挿入機能?>
  </header>

</article>
