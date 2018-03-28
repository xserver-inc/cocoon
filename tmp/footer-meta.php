<?php //本文下部分、投稿者など
if (get_the_author()) {
  $url = get_author_posts_url( get_the_author_meta( 'ID' ) );
  $name = get_the_author();
} else {
  $url = home_url();
  $name = get_bloginfo('name');
}
 ?>
<div class="footer-meta">
  <div class="author-info">
    <a href="<?php echo $url; ?>" class="author-link">
      <span class="post-author vcard author" itemprop="editor author creator copyrightHolder" itemscope itemtype="http://schema.org/Person">
        <span class="author-name fn" itemprop="name"><?php echo $name; ?></span>
      </span>
    </a>
  </div>
</div>