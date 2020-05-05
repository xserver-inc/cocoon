<?php //タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//インデックスカテゴリを読み込む
$cat_ids = get_index_list_category_ids();
$count = 6;
?>
<div id="list-categories" class="list-categories">
  <div class="list-new-entries">
    <h1 class="list-new-entries-title list-title">
      <?php _e('新着記事', THEME_NAME); ?>
    </h1>
    <div class="<?php echo get_index_list_classes(); ?>">
      <?php echo get_category_index_list_entry_card_tag(null, $count); ?>
    </div><!-- .list -->
    <div class="list-more-button-wrap">
        <a href="<?php echo trailingslashit(get_bloginfo('url')) ?>?cat=0" class="list-more-button"><?php echo __( 'もっと見る', THEME_NAME ); ?></a>
    </div>
  </div><!-- .list-new-entries -->

  <?php //カテゴリの表示
  for ($i=0; $i < count($cat_ids); $i++):
    $cat_id = $cat_ids[$i]; ?>
    <div class="list-category-<?php echo $cat_id; ?>">
      <h1 class="list-category-<?php echo $cat_id; ?> list-title">
        <?php echo get_category_name_by_id($cat_id); ?>
      </h1>
      <div class="<?php echo get_index_list_classes(); ?>">
      <?php echo get_category_index_list_entry_card_tag($cat_id, $count); ?>
      </div><!-- .list -->
      <?php if($cat = get_category($cat_id)): ?>
        <div class="list-more-button-wrap">
            <a href="<?php echo trailingslashit(get_bloginfo('url')) ?>archives/category/<?php echo $cat->slug ?>" class="list-more-button"><?php echo __( 'もっと見る', THEME_NAME ); ?></a>
        </div>
      <?php endif; ?>
    </div><!-- .list-category- -->
  <?php endfor; ?>
</div><!-- .list-categories -->
