<?php //マルチページ用のページャーリンク
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$args = array(
  'before' => '<div class="pager-links pager-prev-next">',
  'after' => '</div>',
  'link_before' => '<span class="page-numbers page-prev-next">',
  'link_after' => '</span>',
  'next_or_number' => 'next',
  'previouspagelink' => __( '前へ', THEME_NAME ),
  'nextpagelink' => __( '次へ', THEME_NAME ),
  'separator' => '',
);
wp_link_pages($args);

$args = array(
  'before' => '<div class="pager-links pager-numbers">',
  'after' => '</div>',
  'link_before' => '<span class="page-numbers">',
  'link_after' => '</span>',
  'next_or_number' => 'number',
  'separator' => '',
);
wp_link_pages($args); ?>
