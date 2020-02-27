<?php //タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$cat_ids = get_tab_index_category_ids();
$list_classes = 'list'.get_additional_entry_card_classes();
?>

<div class="front-top-page top-page">
  <input id="tab1" type="radio" name="tab_item" checked>
  <?php for ($i=0; $i < count($cat_ids); $i++):
  $number = $i + 2; ?>
  <input id="tab<?php echo $number; ?>" type="radio" name="tab_item">
  <?php endfor; ?>
  <div class="top-cate-btn">
    <label class="tab-btn" for="tab1">新着記事</label>
    <?php for ($i=0; $i < count($cat_ids); $i++):
    $number = $i + 2; ?>
    <label class="tab-btn" for="tab<?php echo $number; ?>">コンテンツ<?php echo $number; ?>つ目</label>
    <?php endfor; ?>
  </div>
  <div class="tab-cont tb1">
      <!-- 1つ目のコンテンツ -->
      <div class="<?php echo $list_classes; ?>">
          <?php get_template_part('tmp/list-index'); ?>
      </div>
      <?php
      ////////////////////////////
      //ページネーション
      ////////////////////////////
      if (get_tab_index_category_ids()) {
        get_template_part('tmp/pagination');
      } ?>
  </div>
  <?php for ($i=0; $i < count($cat_ids); $i++):
  $number = $i + 2; ?>
  <div class="tab-cont tb<?php echo $number; ?>">
      <!-- <?php echo $number; ?>つ目のコンテンツ -->
      <?php
          $arg = array(
              'posts_per_page' => 8, // 表示させる件数
              'orderby' => 'date',
              'order' => 'DESC',
              'category_name' => '○○' // カテゴリーの指定（スラッグで指定）
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
      <?php endif; ?>
      <div class="wp-block-cocoon-blocks-button-1 aligncenter button-block">
      <!-- リンクのアドレスを任意の物に -->
          <a href="<?php bloginfo('url') ?>/category/○○" class="btn btn-l btn-circle" target="_self">コンテンツ<?php echo $number; ?>をもっと見る</a>
      </div>
  </div>
  <?php endfor; ?>
</div>
