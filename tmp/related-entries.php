<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_related_entries_visible()): ?>
<aside id="related-entries" class="related-entries<?php echo get_additional_related_entries_classes(); ?>">
  <h2 class="related-entry-heading">
    <span class="related-entry-main-heading main-caption">
      <?php echo get_related_entry_heading(); ?>
    </span>
    <?php if (get_related_entry_sub_heading()): ?>
      <span class="related-entry-sub-heading sub-caption"><?php echo get_related_entry_sub_heading(); ?></span>
    <?php endif ?>
  </h2>
  <div class="related-list">
  <?php //関連記事
    cocoon_template_part('tmp/related-list');
  ?>
  </div>
</aside>
<?php endif //is_related_entries_visible ?>
