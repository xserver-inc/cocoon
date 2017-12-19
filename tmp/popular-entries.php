<?php
//グローバル変数の呼び出し
global $_WIDGET_MODE;//ウィジェットモード（全て表示するか、カテゴリ別に表示するか）
global $_ENTRY_COUNT;
global $_COUNT_DAYS;
$cat_ids = array();

// _v(is_category());
// _v(get_category_ids());
if ($_WIDGET_MODE == 'category') {
  $cat_ids = get_category_ids();//カテゴリ配列の取得
}
//var_dump($cat_ids);

// $time_start = microtime(true);
$records = get_access_ranking_records($_COUNT_DAYS, $_ENTRY_COUNT, $cat_ids);
// $time = microtime(true) - $time_start;
// var_dump($time);

//var_dump($records);
?>
<div class="popular-entriy-cards widget-entriy-cards cf<?php echo get_additional_popular_entriy_cards_classes(); ?>">
<?php if ( $records ) :
  foreach ($records as $post):
    $permalink = get_permalink( $post->ID );
    $title = $post->post_title;
    $no_thumbnail_url = get_template_directory_uri().'/images/no-image-320.png';
    $post_thumbnail = get_the_post_thumbnail( $post->ID, array(320, 180), array('alt' => '') );
    if ($post_thumbnail) {
      $post_thumbnail_img = $post_thumbnail;
    } else {
      $post_thumbnail_img = '<img src="'.$no_thumbnail_url.'" alt="NO IMAGE" class="no-image popular-entry-card-thumb-no-image widget-entry-card-thumb-no-image" width="320" height="180" />';
    }

    //_v($post_thumbnail_img);

    //var_dump($permalink);
    ?>
<a href="<?php echo $permalink; ?>" class="popular-entry-card-link a-wrap" title="<?php echo $title; ?>">
  <div class="popular-entry-card widget-entriy-cards e-card cf">
    <figure class="popular-entry-card-thumb widget-entry-card-thumb card-thumb">
    <?php echo $post_thumbnail_img; ?>
    </figure><!-- /.popular-entry-card-thumb -->

    <div class="popular-entry-card-content widget-entry-card-content card-content">
      <div class="popular-entry-card-title widget-entry-card-title card-title"><?php echo $title;?></div>
    </div><!-- /.popular-entry-content -->
  </div><!-- /.popular-entry-card -->
</a><!-- /.popular-entry-card-link -->

<?php endforeach;
else :
  echo '<p>'.__( '人気記事は見つかりませんでした。', THEME_NAME ).'</p>';//見つからない時のメッセージ
endif; ?>
</div>
