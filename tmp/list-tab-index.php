<?php // タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// インデックスカテゴリーを読み込む
$cat_ids = get_index_list_category_ids();
// インデックスリスト用のクラス取得
$list_classes = get_index_list_classes();
// 選択可能なカテゴリ数
$cat_count = apply_filters('cocoon_index_max_category_tab_count', 3);
?>

<div id="index-tab-wrap" class="index-tab-wrap list-wrap <?php echo get_front_page_type_class(); ?>">

  <!-- ラジオボタン：新着記事 -->
  <input id="index-tab-1" type="radio" name="tab_item" checked>

  <?php
  $number = 1;
  for ($i = 0; $i < count($cat_ids) && $i < $cat_count; $i++):
    $cat_id = $cat_ids[$i];
    if (is_category_exist($cat_id)):
      $number++;
  ?>
    <input id="index-tab-<?php echo $number; ?>" type="radio" name="tab_item">
  <?php
    endif;
  endfor;
  ?>

  <div class="index-tab-buttons">
    <label class="index-tab-button" for="index-tab-1">
      <?php echo apply_filters('new_entries_caption', __( '新着記事', THEME_NAME )); ?>
    </label>

    <?php
    $number = 1;
    for ($i = 0; $i < count($cat_ids) && $i < $cat_count; $i++):
      $cat_id = $cat_ids[$i];
      if (is_category_exist($cat_id)):
        $number++;
    ?>
      <label class="index-tab-button" for="index-tab-<?php echo $number; ?>">
        <?php echo get_category_name_by_id($cat_id); ?>
      </label>
    <?php
      endif;
    endfor;
    ?>
  </div>

  <!-- 新着記事タブの中身 -->
  <div class="tab-cont tb1">
    <?php cocoon_template_part('tmp/list-index'); ?>
    <?php cocoon_template_part('tmp/pagination'); ?>
  </div>

  <?php
  $number = 1;
  for ($i = 0; $i < count($cat_ids) && $i < $cat_count; $i++):
    $cat_id = $cat_ids[$i];
    if (is_category_exist($cat_id)):
      $number++;
  ?>
    <div class="tab-cont tb<?php echo $number; ?>">
      <?php
      $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => get_option_posts_per_page(),
        'orderby'             => !is_index_sort_orderby_date() ? get_index_sort_orderby() : 'date',
        'order'               => 'DESC',
        'cat'                 => $cat_id,
        'category__not_in'    => get_archive_exclude_category_ids(),
        'post__not_in'        => get_archive_exclude_post_ids(),
        'no_found_rows'       => true,
      );
      $args = apply_filters('list_category_tab_args', $args, $cat_id);
      $query = new WP_Query($args);

      if ($query->have_posts()):
      ?>
        <div class="<?php echo $list_classes; ?>">
          <?php
          $count = 0;
          while ($query->have_posts()):
            $query->the_post();
            $count++;
            set_query_var('count', $count);
            cocoon_template_part('tmp/entry-card');
          endwhile;
          wp_reset_postdata();
          ?>
        </div>
        <div class="list-more-button-wrap">
          <a href="<?php echo get_category_link($cat_id); ?>" class="list-more-button">
            <?php echo apply_filters('more_button_caption', __( 'もっと見る', THEME_NAME )); ?>
          </a>
        </div>
      <?php else: ?>
        <?php cocoon_template_part('tmp/list-not-found-posts'); ?>
      <?php endif; ?>
    </div>
  <?php
    endif;
  endfor;
  ?>
</div>
