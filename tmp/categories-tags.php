<?php //カテゴリタグの取得
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_filter('cocoon_part__tmp/categories-tags', function($content) {
  $post_type = get_post_type();

  // カスタム投稿の場合
  if ($post_type !== 'post') {

    // タクソノミーを取得
    $taxonomies = get_object_taxonomies($post_type);
    // タームを取得

    $args = array(
      'order'   => 'ASC',
      'orderby' => 'name',
    );
    $terms = wp_get_post_terms(get_the_ID(), $taxonomies, $args);

    // 階層的なタクソノミーか判定
    if (is_taxonomy_hierarchical($taxonomies[0])) {
      $cat_tag = 'cat';
      $icon = '<span class="fa fa-folder cat-icon tax-icon" aria-hidden="true"></span>';
    } else {
      $cat_tag = 'tag';
      $icon = '<span class="fa fa-tag tag-icon tax-icon" aria-hidden="true"></span>';
    }

    if ($terms && !is_wp_error($terms)) {
      $html = '';
      foreach ($terms as $term) {
        $html .= '<a class="' . $cat_tag . '-link ' . $cat_tag . '-link-' . $term->term_id . '" href="' . esc_url(get_term_link($term->slug, $term->taxonomy)) . '">' . $icon . esc_html($term->name) . '</a>';
      }

      // 階層的なタクソノミーはカテゴリーとする
      if ($cat_tag == 'cat') {
        $html = '<div class="entry-categories">' . $html . '</div>';
      } else {
        $html = '<div class="entry-tags">' . $html . '</div>';
      }
      $content = '<div class="entry-categories-tags ' . esc_attr(get_additional_categories_tags_area_classes() ?: '') . '">' . $html . '</div>';
    }
  }

  return $content;
});
