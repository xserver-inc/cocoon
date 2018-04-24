<?php if (is_carousel_visible() && get_carousel_category_ids() && !is_amp()): ?>
<?php //カルーセルに関連付けられた投稿の取得
$args = array(
  'cat' => get_carousel_category_ids(),
  'orderby' => 'rand', //ランダム表示
  'no_found_rows' => true,
);
$query = new WP_Query( $args );
// var_dump($query -> have_posts());
// var_dump($query);
  if( $query -> have_posts() ): //カルーセルが設定されているとき
?>
<div id="carousel" class="carousel">
  <div id="carousel-in" class="carousel-in wrap">
    <div class="carousel-content cf">
      <?php while ($query -> have_posts()) : $query -> the_post(); ?>
        <?php //カルーセルカードの取得
        get_template_part('tmp/carousel-entry-card'); ?>
      <?php endwhile;?>
    </div>
  </div>
</div>
<?php
endif;
wp_reset_postdata();
?>
<?php endif ?>
