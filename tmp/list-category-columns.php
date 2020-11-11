<?php //カテゴリーカラムインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//インデックスカテゴリを読み込む
$cat_ids = get_index_list_category_ids();
$count = get_index_category_entry_card_count();
?>
<div id="list-wrap" class="list-wrap">
  <!-- 新着記事 -->
  <?php get_template_part('tmp/list-new-entries'); ?>

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
  };

  //フロントページタイプのカラムによる変更
  $ect_columns = 'ect-2-columns';
  if (is_front_page_type_category_3_columns()) {
    $ect_columns = 'ect-3-columns';
  }
  ?>

  <div id="list-columns" class="list-columns ect-vertical-card <?php echo $ect_columns; ?>">
    <?php //カテゴリの表示
    for ($i=0; $i < count($cat_ids); $i++):
      $cat_id = $cat_ids[$i]; ?>
      <?php if (is_category_exist($cat_id)): ?>
      <div class="list-category-<?php echo $cat_id; ?>-column list-column">
        <h1 class="list-category-<?php echo $cat_id; ?>-column-title list-title">
          <span class="list-title-in">
            <?php echo get_category_name_by_id($cat_id); ?>
          </span>
        </h1>
        <div class="list">
          <?php
          $atts = array(
            'entry_count' => apply_filters('index_widget_entry_card_count', $count, $cat_id),
            'cat_ids' => $cat_id,
            'type' => apply_filters('index_widget_entry_card_type', ET_DEFAULT, $cat_id),
            'arrow' => apply_filters('index_widget_entry_card_arrow', 1, $cat_id),
            'sticky' => 0,
            'include_children' => 1,
          );
          if (is_index_sort_orderby_ramdom()) {
            $atts += array(
              'random' => 1,
            );
          }
          //新着記事リストの作成
          generate_widget_entries_tag($atts);
          ?>
        </div><!-- .list -->
        <?php if($cat = get_category($cat_id)): ?>
          <div class="list-more-button-wrap">
              <a href="<?php echo get_category_link($cat_id); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __( 'もっと見る', THEME_NAME )); ?></a>
          </div>
        <?php endif; ?>
      </div><!-- .list-column -->
      <?php endif; ?>

    <?php endfor; ?>
  </div><!-- .list-categories -->
</div><!-- .list-wrap -->
