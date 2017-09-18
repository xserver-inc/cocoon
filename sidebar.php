<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
<div id="sidebar" class="sidebar cf" role="complementary">

  <?php //サイドバー上の広告表示
  if (is_ad_pos_sidebar_top_visible() && is_all_adsenses_visible()){
    get_template_part_with_ad_format(get_ad_pos_sidebar_top_format(), 'ad-sidebar-top');
  }; ?>

	<?php dynamic_sidebar( 'sidebar' ); ?>

  <?php //サイドバー下の広告表示
  if (is_ad_pos_sidebar_bottom_visible() && is_all_adsenses_visible()){
    get_template_part_with_ad_format(get_ad_pos_sidebar_bottom_format(), 'ad-sidebar-bottom');
  }; ?>

</div>
<?php endif; ?>