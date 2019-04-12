<?php //カテゴリ情報から関連記事をランダムに呼び出す
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//フックによるrelated-list.phpテンプレートの表示制御
if (apply_filters('cocoon_template_part_related_list', true)) {
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
}
?>
