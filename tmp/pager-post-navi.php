<div class="navigation">
<div id="prev-next" class="clearfix">
<?php
$prevpost = get_adjacent_post(false, '', true); //前の記事
$nextpost = get_adjacent_post(false, '', false); //次の記事
if( $prevpost or $nextpost ){ //前の記事、次の記事いずれか存在しているとき
?>
<?php
function get_post_navi_thumbnail($id){
  $thumb = get_the_post_thumbnail( $id, 'thumb100', array('alt' => '') );
  if ( !$thumb ) {
    $thumb = '<img src="'.get_template_directory_uri().'/images/no-image.png" alt="NO IMAGE" class="no-image post-navi-no-image" />';
  }
  return $thumb;
}
if ( $prevpost ) { //前の記事が存在しているとき
  echo '<a href="' . get_permalink($prevpost->ID) . '" title="' . get_the_title($prevpost->ID) . '" id="prev" class="clearfix">
        <div id="prev-title"><span class="fa fa-arrow-left pull-left"></span></div>
        ' .
        get_post_navi_thumbnail( $prevpost->ID ).
        //get_the_post_thumbnail( $prevpost->ID, 'thumb100', array('alt' => get_the_title( $prevpost->ID )) ) .
        '
        <p>' . get_the_title($prevpost->ID) . '</p></a>';
} else { //前の記事が存在しないとき
echo  '<div id="prev-no"><a href="' .home_url('/'). '"><div id="prev-next-home"><span class="fa fa-home"></span>
  </div></a></div>';
}
if ( $nextpost ) { //次の記事が存在しているとき
  echo '<a href="' . get_permalink($nextpost->ID) . '" title="'. get_the_title($nextpost->ID) . '" id="next" class="clearfix">
        <div id="next-title"><span class="fa fa-arrow-right pull-left"></span></div>
        ' .
        get_post_navi_thumbnail( $nextpost->ID ).
        //get_the_post_thumbnail( $nextpost->ID, 'thumb100', array('alt' => get_the_title( $nextpost->ID )) ) .
        '
<p>'. get_the_title($nextpost->ID) . '</p></a>';
} else { //次の記事が存在しないとき
echo '<div id="next-no"><a href="' .home_url('/'). '"><div id="prev-next-home"><span class="fa fa-home"></span>
</div></a></div>';
}
?>
<?php } ?>
</div>
</div><!-- /.navigation -->