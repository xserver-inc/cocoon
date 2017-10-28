<?php //カルーセル ?>
<div id="carousel" class="carousel">
  <div id="carousel-in" class="carousel-in wrap">
    <div class="carousel-content">
    <?php //カルーセルに設定された
    $args = array(
      'cat' => array(1),
    );
    $query = new WP_Query( $args ); ?>
      <?php if( $query -> have_posts() && !empty($args) ): //カルーセルが設定されているとき ?>
      <?php while ($query -> have_posts()) : $query -> the_post(); ?>
        <?php //関連記事表示タイプ
        get_template_part('tmp/carousel-entry-card'); ?>
      <?php endwhile;?>

      <?php
    endif;
    wp_reset_postdata();
    ?>
    </div>
  </div>
</div>