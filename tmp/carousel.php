<?php //カルーセル
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_carousel_visible() && !is_amp() && apply_filters('carousel_visible', true)): ?>
<?php //カルーセルに関連付けられた投稿の取得
$max_count = get_carousel_max_count();
if (empty($max_count)) {
  $max_count = CAROUSEL_MAX_COUNT;
}
$args = array(
  'orderby' => get_carousel_orderby(), //ランダム表示
  'no_found_rows' => true,
  'posts_per_page' => $max_count,
);
//人気記事が有効の場合
if ( is_carousel_popular_posts_enable()) {
  $days = get_carousel_popular_posts_count_days();
  $limit = intval($max_count) * 5;
  $records = get_access_ranking_records($days, $limit, 'post');
  $post_ids = array();
  //取得した投稿IDをセット
  foreach ($records as $post) {
    $post_ids[] = $post->ID; // 配列に追加
  }
  $args += array('post__in' => $post_ids);
} elseif (get_carousel_category_ids()) {//カテゴリー
  $args += array('category__in' => get_carousel_category_ids());
} else {//タグ
  $args += array('tag__in' => get_carousel_tag_ids());
}
$args = apply_filters('cocoon_carousel_args', $args);
$query = new WP_Query( $args );
// var_dump($query -> have_posts());
// var_dump($query);
  if( $query -> have_posts() ): //カルーセルが設定されているとき
?>
<div id="carousel" class="carousel<?php echo get_additional_carousel_area_classes(); ?>">
  <div id="carousel-in" class="carousel-in wrap">
    <div class="carousel-content cf">
      <?php while ($query -> have_posts()) : $query -> the_post(); ?>
        <?php //カルーセルカードの取得
        cocoon_template_part('tmp/carousel-entry-card'); ?>
      <?php endwhile;?>
    </div>
  </div>
</div>
<?php
endif;
wp_reset_postdata();
?>
<?php endif ?>
