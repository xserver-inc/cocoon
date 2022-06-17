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
  //関連記事数をカウント
  $post_count = 0;
  //有線表示記事URLを取得
  $urls = list_text_to_array(get_priority_related_post_url_list());
  $url_ids = array();
  //URLをIDに変換
  foreach ($urls as $url) {
    $id = url_to_postid(trim($url));
    if ($id !== 0) {
      $url_ids[] = $id;
    }
  }
  //表示数
  $disp_count = intval(get_related_entry_count());
  //指名関連記事
  $args = array( 
    'post__in' => $url_ids, 
    'orderby' => 'post__in',
    'posts_per_page' => $disp_count,
  );
  $query = new WP_Query( $args ); 
  $post_count += count($query->posts);
  ?>
  <?php if( $query -> have_posts() && !empty($args) ): //関連記事があるとき?>
    <?php while ($query -> have_posts()) : $query -> the_post(); ?>
      <?php //関連記事表示タイプ
      get_template_part('tmp/related-entry-card'); ?>
    <?php endwhile;?>
    <?php
  endif;
  wp_reset_postdata();

  //通常の関連記事
  $args = get_related_wp_query_args();
  //優先関連記事を除外する
  $args['post__not_in'] = array_merge($args['post__not_in'], $url_ids);
  //関連記事数から追加した優先関連記事数を引く
  $args['posts_per_page'] = $disp_count - count($url_ids);
  // _v($args);
  $query = new WP_Query( $args ); 
  $post_count += count($query->posts);
  ?>
  <?php if( $query -> have_posts() && !empty($args) ): //関連記事があるとき?>
    <?php while ($query -> have_posts()) : $query -> the_post(); ?>
      <?php //関連記事表示タイプ
      get_template_part('tmp/related-entry-card'); ?>
    <?php endwhile;?>
  <?php
  endif;
  wp_reset_postdata();
  if ($post_count <= 0) {?>
    <p><?php _e( '関連記事は見つかりませんでした。', THEME_NAME ) ?></p>
  <?php
  }
}
?>
