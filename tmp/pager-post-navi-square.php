<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_post_navi_visible()): ?>
<div id="pager-post-navi" class="pager-post-navi<?php echo get_additional_post_navi_classes(); ?> cf">
<?php
$prevpost = get_adjacent_post(false, '', true); //前の記事
$nextpost = get_adjacent_post(false, '', false); //次の記事
if( $prevpost or $nextpost ){ //前の記事、次の記事いずれか存在しているとき
?>
<?php
if ( $prevpost ) { //前の記事が存在しているとき
  echo '<a href="' . get_permalink($prevpost->ID) . '" title="' . esc_attr(get_the_title($prevpost->ID)) . '" class="prev-post a-wrap border-element cf">
        <figure class="prev-post-thumb">' .
        get_post_navi_thumbnail_tag( $prevpost->ID ).
        '</figure>
        <div class="prev-post-title">' . get_the_title($prevpost->ID) . '</div></a>';
}

if ( $nextpost ) { //次の記事が存在しているとき
  echo '<a href="' . get_permalink($nextpost->ID) . '" title="'. esc_attr(get_the_title($nextpost->ID)) . '" class="next-post a-wrap cf">
        <figure class="next-post-thumb">
        ' .
        get_post_navi_thumbnail_tag( $nextpost->ID ).
        '</figure>
<div class="next-post-title">'. get_the_title($nextpost->ID) . '</div></a>';
}
?>
<?php } ?>
</div><!-- /.pager-post-navi -->
<?php endif //is_post_navi_visible ?>
