<?php if (is_related_entries_visible()): ?>
<aside id="related-entries" class="related-entries<?php echo get_additional_related_entries_classes(); ?>">
  <h2 class="related-entry-heading">
    <?php echo get_related_entry_heading(); ?>
    <?php if (get_related_entry_sub_heading()): ?>
      <span class="related-entry-sub-heading sub-caption"><?php echo get_related_entry_sub_heading(); ?></span>
    <?php endif ?>
  </h2>
  <div class="related-list">
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
  </div>
</aside>
<?php endif //is_related_entries_visible ?>
