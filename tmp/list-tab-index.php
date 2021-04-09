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
//インデックスリスト用のクラス取得
$list_classes = get_index_list_classes();
?>

<div id="index-tab-wrap" class="index-tab-wrap">
  <input id="index-tab-1" type="radio" name="tab_item" checked>
  <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
  $number = $i + 2; ?>
  <input id="index-tab-<?php echo $number; ?>" type="radio" name="tab_item">
  <?php endfor; ?>
  <div class="index-tab-buttons">
    <label class="index-tab-button" for="index-tab-1"><?php echo apply_filters('new_entries_caption', __( '新着記事', THEME_NAME )); ?></label>
    <?php for ($i=0; $i < count($cat_ids) && $i < 3; $i++):
    $number = $i + 2;
    $cat_id = $cat_ids[$i]; ?>
        <?php if (is_category_exist($cat_id)): ?>
        <label class="index-tab-button" for="index-tab-<?php echo $number; ?>"><?php echo get_category_name_by_id($cat_id);//echo $cat_ids[$i]; ?></label>
        <?php endif; ?>
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
 //var_dump($cat_ids);
  $number = $i + 2;
  $cat_id = $cat_ids[$i];
   ?>
    <?php if (is_category_exist($cat_id)): ?>
    <div class="tab-cont tb<?php echo $number; ?>">
        <!-- <?php echo $number; ?>つ目のコンテンツ -->
        <?php
            $arg = array(
                //表示件数（WordPressの表示設定に準拠）
                'posts_per_page' => get_option_posts_per_page(),
                //投稿日順か更新日順か
                'orderby' => !is_index_sort_orderby_date() ? get_index_sort_orderby() : 'date',
                //降順
                'order' => 'DESC',
                //カテゴリーをIDで指定
                'category' => $cat_id,
                //アーカイブに表示しないページのID
                'post__not_in' =>  get_archive_exclude_post_ids(),
            );
            $posts = get_posts( $arg );
            if( $posts ): ?>
        <div class="<?php echo $list_classes; ?>">
            <?php
                $count = 0;
                foreach ( $posts as $post ) :
                setup_postdata( $post ); ?>
                    <?php
                    $count++;
                    //エントリーカウントをテンプレートファイルに渡す
                    set_query_var('count', $count);
                    get_template_part('tmp/entry-card');
                    ?>
            <?php endforeach; wp_reset_postdata();
            $count = 0; ?>
        </div>
        <?php if($cat = get_category($cat_id)): ?>
            <div class="list-more-button-wrap">
                <a href="<?php echo get_category_link($cat_id); ?>" class="list-more-button"><?php echo apply_filters('more_button_caption', __( 'もっと見る', THEME_NAME )); ?></a>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
  <?php endfor; ?>
</div>
