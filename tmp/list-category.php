<?php //カテゴリーインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//インデックスカテゴリーを読み込む
$cat_ids = get_index_list_category_ids();
$count = get_index_category_entry_card_count();
?>
<div id="list-wrap" class="list-wrap <?php echo get_front_page_type_class(); ?>">
  <!-- 新着記事 -->
  <?php cocoon_template_part('tmp/list-new-entries'); ?>

  <?php //広告表示
  //インデックスミドルに広告を表示してよいかの判別
  if (is_ad_pos_index_middle_visible() && is_all_adsenses_visible()) {
    get_template_part_with_ad_format(get_ad_pos_index_middle_format(), 'ad-index-middle', is_ad_pos_index_middle_label_visible());
  }

  ////////////////////////////
  //インデックスリストミドルウィジェット
  ////////////////////////////
  if (is_active_sidebar( 'index-middle' )){
    dynamic_sidebar( 'index-middle' );
  }; ?>

  <div id="list-columns" class="list-columns">
    <?php //カテゴリーの表示
    for ($i=0; $i < count($cat_ids); $i++):
      $cat_id = $cat_ids[$i]; ?>
      <?php if (is_category_exist($cat_id)): ?>
      <div class="list-category-<?php echo $cat_id; ?> list-column">
        <h1 class="list-category-<?php echo $cat_id; ?>-title list-title">
          <span class="list-title-in">
            <?php echo get_category_name_by_id($cat_id); ?>
          </span>
        </h1>
        <div class="<?php echo get_index_list_classes(); ?>">
          <?php echo get_category_index_list_entry_card_tag($cat_id, $count); ?>
        </div><!-- .list -->
        <?php if($cat = get_category($cat_id)): ?>
          <div class="list-more-button-wrap">
              <a href="<?php echo get_category_link($cat_id); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __( 'もっと見る', THEME_NAME )); ?></a>
          </div>
        <?php endif; ?>
      </div><!-- .list-category- -->
      <?php endif; ?>
    <?php endfor; ?>
  </div><!-- .list-columns -->
</div><!-- .list-wrap -->
