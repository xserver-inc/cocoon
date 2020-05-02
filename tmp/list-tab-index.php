<?php //タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//チェックリストのカテゴリを読み込む
$cat_ids = get_index_category_ids();
//順番を変更したい場合はカンマテキストのほうを読み込む
$cat_comma = trim(get_index_category_ids_comma_text());
if ($cat_comma) {
  $cat_ids = explode(',', $cat_comma);
}
//カテゴリーをPHP独自カスタマイズで制御したい人用のフック
$cat_ids = apply_filters('index_category_ids', $cat_ids);
$list_classes = 'list'.get_additional_entry_card_classes();
//タブインデックスのクラス名をPHP独自カスタマイズで制御したい人用のフック
$list_classes = apply_filters('tab_index_list_classes', $list_classes);
?>

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
