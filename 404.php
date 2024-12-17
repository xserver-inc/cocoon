<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php get_header(); ?>

<article class="post article">
  <!--ループ開始-->
  <?php if (get_404_page_title()): ?>
    <h1 class="entry-title"><?php echo get_404_page_title(); ?></h1>
  <?php endif; ?>

  <img class="not-found" src="<?php echo get_404_image_url(); ?>" alt="404 Not Found" />

  <?php echo do_shortcode(wpautop(get_404_page_message())); ?>

  <?php //404ページウィジェット
  if ( is_active_sidebar( '404-page' ) ): ?>
    <?php dynamic_sidebar( '404-page' ); ?>
  <?php endif; ?>

</article>
<!-- END div.post -->
<?php get_footer(); ?>
