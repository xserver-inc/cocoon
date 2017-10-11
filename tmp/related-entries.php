<?php if (is_related_entries_visible()): ?>
<aside id="related-entries" class="related-entries">
  <h2 class="related-entry-title"><?php _e( '関連記事', THEME_NAME ) ?></h2>
  <?php //カテゴリ情報から関連記事をランダムに呼び出す
  $args = get_related_wp_query_args();
  $query = new WP_Query( $args ); ?>
    <?php if( $query -> have_posts() && !empty($args) ): //関連記事があるとき?>
    <?php while ($query -> have_posts()) : $query -> the_post(); ?>
      <?php //関連記事表示タイプ
      get_template_part('tmp/related-entry-card'); ?>
    <?php endwhile;?>

    <?php else:?>
    <p><?php _e( '関連記事は見つかりませんでした。', THEME_NAME ) ?></p>
    <?php
  endif;
  wp_reset_postdata();
  ?>
</aside>
<?php endif //is_related_entries_visible ?>
