<?php get_header(); ?>

<article class="post article">
  <!--ループ開始-->
  <h1 class="entry-title"><?php _e( '404 NOT FOUND', THEME_NAME ) ?></h1>
  <?php if ( 0/*get_404_image()*/ ): ?>
    <img class="not-found" src="<?php //echo get_404_image(); ?>" alt="404 Not Found" />
  <?php else: ?>
    <img class="not-found" src="<?php echo get_template_directory_uri() ?>/images/404.png" alt="404 Not Found" />
  <?php endif ?>

  <p><?php _e( 'ページが見つかりませんでした。', THEME_NAME ) ?></p>

  <?php //404ページウィジェット
  if ( is_active_sidebar( '404-page' ) ): ?>
    <?php dynamic_sidebar( '404-page' ); ?>
  <?php endif; ?>

</article>
<!-- END div.post -->
<?php get_footer(); ?>
