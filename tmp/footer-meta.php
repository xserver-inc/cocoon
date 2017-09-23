<?php //本文下部分、投稿者など ?>
<div class="footer-meta">
  <span class="post-author vcard author">
    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="author-link"><?php the_author(); ?></a>
  </span>
</div>