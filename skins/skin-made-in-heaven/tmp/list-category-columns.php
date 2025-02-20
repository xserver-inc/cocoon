<?php
if (!defined('ABSPATH')) exit;


$cat_ids = get_index_list_category_ids();
$count = get_index_category_entry_card_count();

$columns = 2;
if (is_entry_card_type_vertical_card_3()) {
  $columns = 3;
}

$fpt_columns = 2;
if (is_front_page_type_category_3_columns()) {
  $fpt_columns = 3;
}

$snippet = is_entry_card_snippet_visible();
?>

<div id="list-wrap" class="list-wrap list-wrap front-page-type-category-<?php echo $columns; ?>-columns">
  <div class="list-new-entries">
    <h1 class="list-new-entries-title list-title">
      <span class="list-title-in"><?php echo apply_filters('new_entries_caption', __('新着記事', THEME_NAME)); ?></span>
    </h1>
    <div class="index-tab-wrap">
      <input id="index-tab-1" type="radio" name="tab_item" checked>
      <input id="index-tab-2" type="radio" name="tab_item">
      <div class="index-tab-buttons">
        <label class="index-tab-button" for="index-tab-1"><?php echo __('新着記事', THEME_NAME); ?></label>
        <label class="index-tab-button" for="index-tab-2"><?php echo __('更新記事', THEME_NAME); ?></label>
      </div>
      <div class="tab-cont tb1">
        <?php echo do_shortcode('[new_list type="large_thumb" snippet=' . $snippet . ' class="list ect-vertical-card-' . $columns . ' ect-vertical-card ect-' . $columns . '-columns" count=4]'); ?>
      </div>
      <div class="tab-cont tb2">
        <?php echo do_shortcode('[new_list type="large_thumb" snippet=' . $snippet . ' class="list ect-vertical-card-' . $columns . ' ect-vertical-card ect-' . $columns . '-columns" count=4 modified=1]'); ?>
      </div>
    </div>
    <div class="list-more-button-wrap">
      <a href="<?php echo trailingslashit(get_bloginfo('url')) ?>?cat=0" class="list-more-button"><?php echo apply_filters('more_button_caption', __('もっと見る', THEME_NAME)); ?></a>
    </div>
  </div>


  <div class="list-columns list-popular">
    <h1 class="list-popular-title list-title">
      <span class="list-title-in"><?php echo apply_filters('hvn_popular_caption', __('本日読まれている記事', THEME_NAME)); ?></span>
    </h1>
    <?php echo do_shortcode('[popular_list type="large_thumb" snippet=' . $snippet . ' class="list ect-vertical-card-' . $columns . ' ect-vertical-card ect-' . $columns . '-columns" count=4 days=1]'); ?>
  </div>

  <div id="list-columns" class="list-columns fpt-columns fpt-<?php echo $fpt_columns; ?>-columns">
<?php
for ($i=0; $i < count($cat_ids); $i++):
  $cat_id = $cat_ids[$i];
  $type = apply_filters('index_widget_entry_card_type', 'ET_DEFAULT', $cat_id);

  if (is_category_exist($cat_id)):
?>
    <div class="list-category-<?php echo $cat_id; ?>-column list-column">
      <h1 class="list-category-<?php echo $cat_id; ?>-column-title list-title">
        <span class="list-title-in"><?php echo get_category_name_by_id($cat_id); ?></span>
      </h1>
      <div class="list">
        <?php echo do_shortcode('[new_list count=4 cats=' . $cat_id . ' type=' . $type .']'); ?>
      </div>
    <?php if($cat = get_category($cat_id)): ?>
      <div class="list-more-button-wrap">
        <a href="<?php echo get_category_link($cat_id); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __('もっと見る', THEME_NAME)); ?></a>
      </div>
    <?php endif; ?>
      </div>
  <?php endif; ?>
<?php endfor; ?>
  </div>
</div>
