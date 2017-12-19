<?php
//グローバル変数の呼び出し
global $g_widget_mode;//ウィジェットモード（全て表示するか、カテゴリ別に表示するか）
global $g_entry_count;
$args = array(
  'posts_per_page' => $g_entry_count,
);
$cat_ids = get_category_ids();//カテゴリ配列の取得
$has_cat_ids = $cat_ids && ($g_widget_mode == 'category');
if ( $has_cat_ids ) {
  $args += array('category__in' => $cat_ids);
}
query_posts( $args ); //クエリの作成?>
<div class="new-entriy-cards widget-entriy-cards cf<?php echo get_additional_new_entriy_cards_classes(); ?>">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<a href="<?php the_permalink(); ?>" class="new-entry-card-link widget-entry-card-link a-wrap" title="<?php the_title(); ?>">
  <div class="new-entry-card widget-entry-card e-card cf">
    <figure class="new-entry-card-thumb widget-entry-card-thumb card-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているときの処理 ?>
      <?php the_post_thumbnail( array(320, 180), array('alt' => '') ); ?>
    <?php else: // サムネイルを持っていないときの処理 ?>
      <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="no-image new-entry-card-thumb-no-image widget-entry-card-thumb-no-image" width="320" height="180" />
    <?php endif; ?>
    </figure><!-- /.new-entry-card-thumb -->

    <div class="new-entry-card-content widget-entry-card-content card-content">
      <div class="new-entry-card-title widget-entry-card-title card-title"><?php the_title();?></div>
    </div><!-- /.new-entry-content -->
  </div><!-- /.new-entry-card -->
</a><!-- /.new-entry-card-link -->
<?php endwhile;
else :
  echo '<p>'.__( '新着記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
endif; ?>
<?php wp_reset_query(); ?>
</div>
