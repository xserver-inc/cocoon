<?php //本文下部分、投稿者など ?>
<div class="footer-meta">
  <div class="author-info">
    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="author-link">
      <span class="post-author vcard author" itemprop="editor author creator copyrightHolder" itemscope itemtype="http://schema.org/Person">
        <span class="author-name" itemprop="name"><?php the_author(); ?></span>
      </span>
    </a>
  </div>
</div>