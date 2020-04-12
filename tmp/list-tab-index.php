<?php //タブインデックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$cat_ids = get_tab_index_category_ids();
$cat_ids = apply_filters('tab_index_category_ids', $cat_ids);
$list_classes = 'list'.get_additional_entry_card_classes();
$list_classes = apply_filters('tab_index_list_classes', $list_classes);
?>

<div id="tab-index" class="tab-index">
  <input id="tab1" type="radio" name="tab_item" checked>
  <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
  $number = $i + 2; ?>
  <input id="tab<?php echo $number; ?>" type="radio" name="tab_item">
  <?php endfor; ?>
  <div class="top-cate-btn">
    <label class="tab-btn" for="tab1"><?php _e('新着記事', THEME_NAME); ?></label>
    <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
    $number = $i + 2; ?>
    <label class="tab-btn" for="tab<?php echo $number; ?>"><?php echo get_category_name_by_id($cat_ids[$i]); ?></label>
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
          $arg = array(
              //表示件数（WordPressの表示設定に準拠）
              'posts_per_page' => get_option_posts_per_page(),
              //投稿日順か更新日順か
              'orderby' => is_get_index_sort_orderby_modified() ? get_index_sort_orderby() : 'date',
              //降順
              'order' => 'DESC',
              //カテゴリーをIDで指定
              'category' => $cat_ids[$i]
          );
          $posts = get_posts( $arg );
          if( $posts ): ?>
      <div class="<?php echo $list_classes; ?>">
          <?php //var_dump(count($posts));
              foreach ( $posts as $post ) :
              setup_postdata( $post ); ?>
                  <?php get_template_part('tmp/entry-card'); ?>
          <?php endforeach; wp_reset_postdata(); ?>
      </div>
      <div class="wp-block-cocoon-blocks-button-1 aligncenter button-block">
      <!-- リンクのアドレスを任意の物に -->
          <a href="<?php bloginfo('url') ?>/category/○○" class="btn btn-l btn-circle" target="_self">コンテンツ<?php echo $number; ?>をもっと見る</a>
      </div>
      <?php endif; ?>
  </div>
  <?php endfor; ?>
</div>




<?php if (false): ?>
<div class="tab-wrap">
  <input id="TAB-01" type="radio" name="TAB" class="tab-switch" checked="checked" />
  <label class="tab-label" for="TAB-01"><?php _e('新着記事', THEME_NAME); ?></label>
  <div class="tab-content">
    <div class="<?php echo $list_classes; ?>">
      <?php get_template_part('tmp/list-index'); ?>
    </div>
    <?php
    ////////////////////////////
    //ページネーション
    ////////////////////////////
    get_template_part('tmp/pagination');
      ?>
  </div>
  <?php //カテゴリー表示
  for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
  $number = $i + 2; ?>
    <input id="TAB-<?php echo $number; ?>" type="radio" name="TAB" class="tab-switch" />
    <label class="tab-label" for="TAB-<?php echo $number; ?>"><?php echo get_category_name_by_id($cat_ids[$i]); ?></label>
    <div class="tab-content">
      <?php
      $arg = array(
        //表示件数（WordPressの表示設定に準拠）
        'posts_per_page' => get_option_posts_per_page(),
        //投稿日順か更新日順か
        'orderby' => is_get_index_sort_orderby_modified() ? get_index_sort_orderby() : 'date',
        //降順
        'order' => 'DESC',
        //カテゴリーをIDで指定
        'category' => $cat_ids[$i]
      );
      $posts = get_posts( $arg );
      if( $posts ): ?>
      <div class="<?php echo $list_classes; ?>">
        <?php //var_dump(count($posts));
          foreach ( $posts as $post ) :
          setup_postdata( $post ); ?>
            <?php get_template_part('tmp/entry-card'); ?>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
      <div class="wp-block-cocoon-blocks-button-1 aligncenter button-block">
        <!-- リンクのアドレスを任意の物に -->
        <a href="<?php bloginfo('url') ?>/category/○○" class="btn btn-l btn-circle" target="_self">コンテンツ<?php echo $number; ?>をもっと見る</a>
      </div>
      <?php endif; ?>
    </div>
  <?php endfor; ?>
</div>
<?php endif; ?>
