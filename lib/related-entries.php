<?php //関連記事関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//関連記事の共通引数を取得
if ( !function_exists( 'get_common_related_args' ) ):
function get_common_related_args($post_id){
  global $post;
  $post_type = get_post_type( $post );
  $related_args = array(
    'post_type' => $post_type,
    'post__not_in' => array($post_id),
    'posts_per_page'=> intval(get_related_entry_count()),
    'orderby' => 'rand',
    'no_found_rows' => true,
  );
  //取得期間指定
  if (get_related_entry_period()) {
    $ago = date_i18n('Y-m-d 0:0:0', strtotime(get_related_entry_period()));
    $related_args['date_query'] = array(
      array(
        'after'     => $ago,
        'inclusive' => true
      ),
    );
  }
  return $related_args;
}
endif;

//WP_Queryの引数を取得
if ( !function_exists( 'get_related_wp_query_args' ) ):
function get_related_wp_query_args(){
  global $post;
  if (!$post) {
    $post = get_random_posts(1);
  }
  $args = array();
  $set_args = get_common_related_args($post->ID);

  if ( is_related_association_type_category() ) {
    $set_args['category__in'] = get_the_category_ids($post->ID);
  } else {
    $set_args['tag__in'] = get_the_tag_ids($post->ID);
  }

  //除外投稿
  $exclude_post_ids = get_archive_exclude_post_ids();
  if ($exclude_post_ids && is_array($exclude_post_ids)) {
    // $set_args['post__not_in'] = $exclude_post_ids;
    foreach ($exclude_post_ids as $exclude_post_id) {
      array_push($set_args['post__not_in'], $exclude_post_id);
    }
  }
  // _v($set_args['post__not_in']);
  // _v($exclude_post_ids);
  // _v($set_args);

  $args = $set_args;

  return apply_filters('get_related_wp_query_args', $args);
}
endif;

//投稿に属するカテゴリーIDを取得
if ( !function_exists( 'get_the_category_ids' ) ):
function get_the_category_ids($post_id){
  $categories = get_the_category($post_id);
  $category_IDs = array();
  $cat_count = 0;
  foreach($categories as $category):
    $cat_count += (intval($category->count) - 1);
  endforeach ;
  if ($cat_count > 0) {
    foreach($categories as $category):
      array_push( $category_IDs, $category->cat_ID);
    endforeach ;
  }
  return $category_IDs;
}
endif;

//投稿に属するタグIDを取得
if ( !function_exists( 'get_the_tag_ids' ) ):
function get_the_tag_ids($post_id){
  $tags = wp_get_post_tags($post_id);
  $tag_IDs = array();
  foreach($tags as $tag):
    array_push( $tag_IDs, $tag->term_id);
  endforeach ;
  return $tag_IDs;
}
endif;

//カテゴリーやタグの$argsが空のとき
add_filter('get_related_wp_query_args', 'get_additional_related_wp_query_args');
if ( !function_exists( 'get_additional_related_wp_query_args' ) ):
function get_additional_related_wp_query_args($args) {
  global $post;

  $set_args = get_common_related_args($post->ID);
  if ( is_related_association_type_category() ) {
    if (empty($args['category__in'])) {
      //有効なカテゴリー投稿が見つからなかった場合はタグと関連付ける
      $set_args['tag__in'] = get_the_tag_ids($post->ID);
      $args = $set_args;
    }
  } else {
    if (empty($args['tag__in'])) {
      //有効なタグ投稿が見つからなかった場合はタグと関連付ける
      $set_args['category__in'] = get_the_category_ids($post->ID);
      $args = $set_args;
    }
  }

  return apply_filters('get_additional_related_wp_query_args', $args);
}
endif;

//関連記事エントリーカードのサムネイルサイズ
if ( !function_exists( 'get_related_entry_card_thumbnail_size' ) ):
  function get_related_entry_card_thumbnail_size(){
    $thumbnail_size = null;
    //適切なサムネイルサイズの選択
    switch (get_related_entry_type()) {
      case 'vertical_card_3':
        $thumbnail_size = THUMB320;
        break;
      case 'mini_card':
        $thumbnail_size = THUMB120;
        break;
      default:
        $thumbnail_size = THUMB160;
        break;
    }
    return apply_filters('get_related_entry_card_thumbnail_size', $thumbnail_size);
  }
  endif;
