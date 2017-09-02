<div class="pager-post-navi cf">
<div id="prev-next" class="prev-next cf">
<?php
$prevpost = get_adjacent_post(false, '', true); //前の記事
$nextpost = get_adjacent_post(false, '', false); //次の記事
if( $prevpost or $nextpost ){ //前の記事、次の記事いずれか存在しているとき
?>
<?php
if ( $prevpost ) { //前の記事が存在しているとき
  echo '<a href="' . get_permalink($prevpost->ID) . '" title="' . get_the_title($prevpost->ID) . '" class="prev-post cf">
        <div class="prev-post-icon"><span class="fa fa-angle-left"></span></div>
        ' .
        get_post_navi_thumbnail_tag( $prevpost->ID ).
        '
        <div class="prev-post-title">' . get_the_title($prevpost->ID) . '</div></a>';
} else { //前の記事が存在しないとき
echo  '<div id="prev-no"><a href="' .home_url('/'). '"><div id="prev-next-home" class="prev-next-home"><span class="fa fa-home"></span>
  </div></a></div>';
}
if ( $nextpost ) { //次の記事が存在しているとき
  echo '<a href="' . get_permalink($nextpost->ID) . '" title="'. get_the_title($nextpost->ID) . '" class="next-post cf">
        <div  class="next-post-icon"><span class="fa fa-angle-right"></span></div>
        ' .
        get_post_navi_thumbnail_tag( $nextpost->ID ).
        '
<div class="next-post-title">'. get_the_title($nextpost->ID) . '</div></a>';
} else { //次の記事が存在しないとき
echo '<div class="next-no"><a href="' .home_url('/'). '"><div id="prev-next-home" class="prev-next-home"><span class="fa fa-home"></span>
</div></a></div>';
}
?>
<?php } ?>
</div>
</div><!-- /.navigation -->