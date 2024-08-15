<?php //カテゴリーカラムインデックス
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
  };

  //フロントページタイプのカラムによる変更
  $columns = 'fpt-2-columns';
  if (is_front_page_type_category_3_columns()) {
    $columns = 'fpt-3-columns';
  }
  ?>

  <div id="list-columns" class="list-columns fpt-columns <?php echo $columns; ?>">
    <?php //カテゴリーの表示
    for ($i=0; $i < count($cat_ids); $i++):
      $cat_id = $cat_ids[$i]; ?>
      <?php if (is_category_exist($cat_id)): ?>
      <div class="list-category-<?php echo $cat_id; ?>-column list-column">
        <h1 class="list-category-<?php echo $cat_id; ?>-column-title list-title">
          <span class="list-title-in">
            <?php echo get_category_name_by_id($cat_id); ?>
          </span>
        </h1>
          <?php
          $type = ET_DEFAULT;
          //[Cocoon設定]→[インデックス]→枠線の表示「カードの枠線を表示する」が有効になっている場合は枠線を表示する
          if (is_entry_card_border_visible()) {
            $type = ET_BORDER_SQUARE;
          }
          $atts = array(
            'entry_count' => apply_filters('index_widget_entry_card_count', $count, $cat_id),
            'cat_ids' => $cat_id,
            'type' => apply_filters('index_widget_entry_card_type', $type, $cat_id),
            'arrow' => apply_filters('index_widget_entry_card_arrow', 1, $cat_id),
            'sticky' => 0,
            'include_children' => 1,
          );
          if (is_index_sort_orderby_ramdom()) {
            $atts += array(
              'random' => 1,
            );
          }
          $atts = apply_filters('list_category_column_atts', $atts, $cat_id);

          $class = 'ect-entry-card';
          if (strpos($atts['type'], 'large_thumb') !== false) {
            $class = 'ect-vertical-card';
          }

          ?>
        <div class="list <?php echo $class; ?>">
          <?php
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