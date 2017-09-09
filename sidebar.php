<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
<div id="sidebar" class="sidebar cf" role="complementary">

  <?php //サイドバー上の広告表示
  if (is_ad_pos_index_sidebar_top_visible()){
    //レスポンシブ広告のフォーマットにrectangleを指定する
    get_template_part_with_ad_format(DATA_AD_FORMAT_RECTANGLE, 'ad-sidebar-top');
  }; ?>

	<?php dynamic_sidebar( 'sidebar' ); ?>

  <?php //サイドバー下の広告表示
  if (is_ad_pos_index_sidebar_bottom_visible()){
    //レスポンシブ広告のフォーマットにrectangleを指定する
    get_template_part_with_ad_format(DATA_AD_FORMAT_RECTANGLE, 'ad-sidebar-bottom');
  }; ?>

</div>
<?php endif; ?>