<?php //タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//インデックスカテゴリを読み込む
$cat_ids = get_index_list_category_ids();
$count = 6;
?>
<div id="category-list" class="category-list">
  <div class="list-new-entries">
    <h1 class="list-new-entries-title list-title">
      <?php _e('新着記事', THEME_NAME); ?>
    </h1>
    <div class="<?php echo get_index_list_classes(); ?>">

    <?php
    $args = array(
      'posts_per_page' => $count,
      'post__not_in' => get_sticky_post_ids(),
    );
    $query = new WP_Query( $args );
    ////////////////////////////
    //一覧の繰り返し処理
    ////////////////////////////
    if ($query->have_posts()) : //投稿があるとき
      while ($query->have_posts()) : $query->the_post(); // 繰り返し処理開始
        get_template_part('tmp/entry-card');
      endwhile; // 繰り返し処理終了 ?>
    <?php else : // ここから記事が見つからなかった場合の処理
      get_template_part('tmp/list-not-found-posts');
    endif;
    wp_reset_postdata();
    ?>
    </div><!-- .list -->
  </div><!-- .list-new-entries -->


</div><!-- .category-list -->
















<div id="index-tab-wrap" class="index-tab-wrap">
  <input id="index-tab-1" type="radio" name="tab_item" checked>
  <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
  $number = $i + 2; ?>
  <input id="index-tab-<?php echo $number; ?>" type="radio" name="tab_item">
  <?php endfor; ?>
  <div class="index-tab-buttons">
    <label class="index-tab-button" for="index-tab-1"><?php _e('新着記事', THEME_NAME); ?></label>
    <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
    $number = $i + 2; ?>
    <label class="index-tab-button" for="index-tab-<?php echo $number; ?>"><?php echo get_category_name_by_id($cat_ids[$i]); ?></label>
    <?php endfor; ?>
  </div>
  <div class="tab-cont tb1">
      <?php get_template_part('tmp/list-index'); ?>
      <?php
      ////////////////////////////
      //ページネーション
      ////////////////////////////
      get_template_part('tmp/pagination');
       ?>
  </div>
  <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
  $number = $i + 2; ?>
  <div class="tab-cont tb<?php echo $number; ?>">
      <!-- <?php echo $number; ?>つ目のコンテンツ -->
      <?php
      $cat_id = $cat_ids[$i];
          $arg = array(
              //表示件数（WordPressの表示設定に準拠）
              'posts_per_page' => get_option_posts_per_page(),
              //投稿日順か更新日順か
              'orderby' => is_get_index_sort_orderby_modified() ? get_index_sort_orderby() : 'date',
              //降順
              'order' => 'DESC',
              //カテゴリーをIDで指定
              'category' => $cat_id,
          );
          $posts = get_posts( $arg );
          if( $posts ): ?>
      <div class="<?php echo $list_classes; ?>">
          <?php
              foreach ( $posts as $post ) :
              setup_postdata( $post ); ?>
                  <?php get_template_part('tmp/entry-card'); ?>
          <?php endforeach; wp_reset_postdata(); ?>
      </div>
      <?php if($cat = get_category($cat_id)): ?>
        <div class="index-tab-more-button-wrap">
            <a href="<?php echo trailingslashit(get_bloginfo('url')) ?>archives/category/<?php echo $cat->slug ?>" class="index-tab-more-button"><?php echo __( 'もっと見る', THEME_NAME ); ?></a>
        </div>
      <?php endif; ?>
      <?php endif; ?>
  </div>
  <?php endfor; ?>
</div>
