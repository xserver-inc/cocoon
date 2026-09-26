<?php
if (!defined('ABSPATH')) exit;


$cat_ids = get_index_list_category_ids();

$columns = is_entry_card_type_vertical_card_3() ? 3 : 2;
$fpt_columns = is_front_page_type_category_3_columns() ? 3 : 2;

$comment = is_entry_card_post_comment_count_visible() ? 1 : 0;
if (
  is_entry_card_type_big_card() ||
  is_entry_card_type_vertical_card_2() ||
  is_entry_card_type_vertical_card_3()
) {
  $type = ET_LARGE_THUMB;
  $class = 'ect-vertical-card';
} else {
  $type = ET_DEFAULT;
  $class = '';
}
?>

<div id="list-wrap" class="list-wrap front-page-type-category-<?php echo $columns; ?>-columns">
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
        <?php echo do_shortcode("[new_list type='large_thumb' date=1 comment={$comment} class='list ect-vertical-card-{$columns} ect-vertical-card ect-{$columns}-columns' count=4]"); ?>
        <?php if (get_query_var('has_entries')) : ?>
          <div class="list-more-button-wrap">
            <a href="<?php echo home_url('/?cat=0'); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __('もっと見る', THEME_NAME)); ?></a>
          </div>
        <?php endif; ?>
      </div>
      <div class="tab-cont tb2">
        <?php echo do_shortcode("[new_list type='large_thumb' date=1 comment={$comment} class='list ect-vertical-card-{$columns} ect-vertical-card ect-{$columns}-columns' count=4 modified=1]"); ?>
        <?php if (get_query_var('has_entries')) : ?>
          <div class="list-more-button-wrap">
            <a href="<?php echo home_url('/?cat=0&orderby=modified'); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __('もっと見る', THEME_NAME)); ?></a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="list-column list-popular">
    <h1 class="list-popular-title list-title">
      <span class="list-title-in"><?php echo apply_filters('hvn_popular_caption', __('本日読まれている記事', THEME_NAME)); ?></span>
    </h1>
    <?php echo do_shortcode("[popular_list type='large_thumb' date=1 comment={$comment} class='list ect-vertical-card-{$columns} ect-vertical-card ect-{$columns}-columns' count=4 days=1]"); ?>
  </div>

  <div id="list-columns" class="list-columns fpt-columns fpt-<?php echo $fpt_columns; ?>-columns">
    <?php foreach ($cat_ids as $cat_id) : ?>
      <?php if (is_category_exist($cat_id)) : ?>
        <div class="list-category-<?php echo $cat_id; ?>-column list-column">
          <h2 class="list-category-<?php echo $cat_id; ?>-column-title list-title">
            <span class="list-title-in"><?php echo get_category_name_by_id($cat_id); ?></span>
          </h2>
          <div class="list <?php echo $class; ?>">
            <?php echo do_shortcode("[new_list date=1 count=4 comment={$comment} cats={$cat_id} type={$type}]"); ?>
          </div>
          <?php if (get_query_var('has_entries')) : ?>
            <div class="list-more-button-wrap">
              <a href="<?php echo get_category_link($cat_id); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __('もっと見る', THEME_NAME)); ?></a>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
