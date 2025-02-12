<?php //カテゴリタグの取得
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="entry-categories-tags<?php echo get_additional_categories_tags_area_classes(); ?>">
  <div class="entry-categories"><?php the_category_links() ?></div>
  <?php if (get_the_tags()): ?>
  <div class="entry-tags"><?php the_tag_links() ?></div>
  <?php endif; ?>
</div>
