<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// 投稿ページのコンテンツ
///////////////////////////////////////?>
<?php //パンくずリストがメイントップの場合
if (is_single_breadcrumbs_position_main_top()){
  cocoon_template_part('tmp/breadcrumbs');
} ?>

<?php //本文テンプレート
cocoon_template_part('tmp/content') ?>


<div class="under-entry-content">

  <?php //関連記事上ページ送りナビ
  if (is_post_navi_position_over_related()) {
    cocoon_template_part('tmp/pager-post-navi');
  } ?>

  <?php //投稿関連記事上ウィジェット
  if ( is_active_sidebar( 'above-single-related-entries' ) ): ?>
    <?php dynamic_sidebar( 'above-single-related-entries' ); ?>
  <?php endif; ?>

  <?php cocoon_template_part('tmp/related-entries'); //関連記事 ?>

  <?php //関連記事下の広告表示
  if (is_ad_pos_below_related_posts_visible() && is_all_adsenses_visible()){
    get_template_part_with_ad_format(get_ad_pos_below_related_posts_format(), 'ad-below-related-posts', is_ad_pos_below_related_posts_label_visible());
  }; ?>

  <?php //投稿関連記事下ウィジェット
  if ( is_active_sidebar( 'below-single-related-entries' ) ): ?>
    <?php dynamic_sidebar( 'below-single-related-entries' ); ?>
  <?php endif; ?>

  <?php //ページ送りナビ
  if (is_post_navi_position_under_related()) {
    cocoon_template_part('tmp/pager-post-navi');
  } ?>

  <?php //コメント上ウィジェット
  if ( is_active_sidebar( 'above-single-comment-aria' ) ): ?>
    <?php dynamic_sidebar( 'above-single-comment-aria' ); ?>
  <?php endif; ?>

  <?php //コメントを表示する場合
  if (is_single_comment_visible() && !post_password_required( $post )) {
    comments_template(); //コメントテンプレート
  } ?>

  <?php //コメントフォーム下ウィジェット
  if ( is_active_sidebar( 'below-single-comment-form' ) ): ?>
    <?php dynamic_sidebar( 'below-single-comment-form' ); ?>
  <?php endif; ?>

  <?php //コメント下ページ送りナビ
  if (is_post_navi_position_under_comment()) {
    cocoon_template_part('tmp/pager-post-navi');
  } ?>

</div>

<?php //パンくずリストがメインボトムの場合
if (is_single_breadcrumbs_position_main_bottom()){
  cocoon_template_part('tmp/breadcrumbs');
} ?>

<?php //メインカラム追従領域
cocoon_template_part('tmp/main-scroll'); ?>
