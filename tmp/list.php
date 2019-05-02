<?php //インデックス一覧
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

////////////////////////////
//アーカイブのタイトル
////////////////////////////
if ( is_category() && !is_paged() ){
  ////////////////////////////
  //カテゴリページのコンテンツ
  ////////////////////////////
  get_template_part('tmp/category-content');
} elseif ( is_tag() && !is_paged() ) {
  get_template_part('tmp/tag-content');
} elseif (!is_home()) {
  //それ以外
  get_template_part('tmp/list-title');
}

////////////////////////////
//インデクストップ広告
////////////////////////////
if (is_ad_pos_index_top_visible() && is_all_adsenses_visible()){
  //レスポンシブ広告
  get_template_part_with_ad_format(get_ad_pos_index_top_format(), 'ad-index-top', is_ad_pos_index_top_label_visible());
};

////////////////////////////
//インデックスリストトップウィジェット
////////////////////////////
if ( is_active_sidebar( 'index-top' ) ){
  dynamic_sidebar( 'index-top' );
}; ?>

<div id="list" class="list<?php echo get_additional_entry_card_classes(); ?>">
<?php
////////////////////////////
//一覧の繰り返し処理
////////////////////////////
$count = 0;
if (have_posts()) : // WordPress ループ
  while (have_posts()) : the_post(); // 繰り返し処理開始
    $count++;
    set_query_var( 'count', $count );
    get_template_part('tmp/entry-card');

    //インデックスミドルに広告を表示してよいかの判別
    if (is_ad_pos_index_middle_visible() && is_index_middle_ad_visible($count) && is_all_adsenses_visible()) {
      get_template_part_with_ad_format(get_ad_pos_index_middle_format(), 'ad-index-middle', is_ad_pos_index_middle_label_visible());
    }

    ////////////////////////////
    //インデックスリストミドルウィジェット
    ////////////////////////////
    if ( is_active_sidebar( 'index-middle' ) && is_index_middle_widget_visible($count) ){
      dynamic_sidebar( 'index-middle' );
    };

  endwhile; // 繰り返し処理終了 ?>
<?php else : // ここから記事が見つからなかった場合の処理
  get_template_part('tmp/list-not-found-posts');
endif;
?>
</div><!-- .list -->

<?php
////////////////////////////
//インデクスボトム広告
////////////////////////////
if (is_ad_pos_index_bottom_visible() && is_all_adsenses_visible()){
  //レスポンシブ広告のフォーマットにrectangleを指定する
  get_template_part_with_ad_format(get_ad_pos_index_bottom_format(), 'ad-index-bottom', is_ad_pos_index_bottom_label_visible());
}; ?>

<?php
////////////////////////////
//インデックスリストボトムウィジェット
////////////////////////////
if ( is_active_sidebar( 'index-bottom' ) ){
  dynamic_sidebar( 'index-bottom' );
}; ?>

<?php
////////////////////////////
//ページネーション
////////////////////////////
get_template_part('tmp/pagination'); ?>

<?php
////////////////////////////
//メインカラム追従領域
////////////////////////////
get_template_part('tmp/main-scroll'); ?>
