<?php //Windows Live Writer・Open Live Writer用のスタイル取得用
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

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
