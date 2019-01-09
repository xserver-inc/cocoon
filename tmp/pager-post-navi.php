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
$width  = THUMB120WIDTH;
$height = THUMB120HEIGHT;
switch (get_post_navi_type()) {
  case 'square':
    $width  = THUMB150WIDTH;
    $height = THUMB150HEIGHT;
    break;
}
if( $prevpost or $nextpost ){ //前の記事、次の記事いずれか存在しているとき
?>
<?php
if ( $prevpost ) { //前の記事が存在しているとき
  echo '<a href="' . get_permalink($prevpost->ID) . '" title="' . esc_attr(get_the_title($prevpost->ID)) . '" class="prev-post a-wrap border-element cf">
        <figure class="prev-post-thumb card-thumb">' .
        get_post_navi_thumbnail_tag( $prevpost->ID, $width, $height ).
        '</figure>
        <div class="prev-post-title">' . get_the_title($prevpost->ID) . '</div></a>';
} else { //前の記事が存在しないとき
  if (is_post_navi_type_spuare()) {
    echo  '<a href="' .home_url('/'). '" id="prev-next-home" class="prev-next-home a-wrap"><span class="fa fa-home"></span></a>';
  }
}
if ( $nextpost ) { //次の記事が存在しているとき
  echo '<a href="' . get_permalink($nextpost->ID) . '" title="'. esc_attr(get_the_title($nextpost->ID)) . '" class="next-post a-wrap cf">
        <figure class="next-post-thumb card-thumb">
        ' .
        get_post_navi_thumbnail_tag( $nextpost->ID, $width, $height ).
        '</figure>
<div class="next-post-title">'. get_the_title($nextpost->ID) . '</div></a>';
} else { //次の記事が存在しないとき
  if (is_post_navi_type_spuare()) {
    echo '<a href="' .home_url('/'). '" id="prev-next-home" class="prev-next-home a-wrap"><span class="fa fa-home"></span></a>';
  }

}
?>
<?php } ?>
</div><!-- /.pager-post-navi -->
<?php endif //is_post_navi_visible ?>
