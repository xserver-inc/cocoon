<?php //タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//インデックスカテゴリを読み込む
$count = get_index_new_entry_card_count();
?>
<div class="list-new-entries">
  <h1 class="list-new-entries-title list-title">
    <span class="list-title-in">
      <?php echo apply_filters('new_entries_caption', __( '新着記事', THEME_NAME )); ?>
    </span>
  </h1>
  <div class="<?php echo get_index_list_classes(); ?>">
    <?php echo get_category_index_list_entry_card_tag(null, $count); ?>
  </div><!-- .list -->
  <div class="list-more-button-wrap">
      <a href="<?php echo trailingslashit(get_bloginfo('url')) ?>?cat=0" class="list-more-button"><?php echo apply_filters('more_button_caption', __( 'もっと見る', THEME_NAME )); ?></a>
  </div>
</div><!-- .list-new-entries -->
