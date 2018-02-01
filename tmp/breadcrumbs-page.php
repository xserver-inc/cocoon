<?php //固定ページ用のパンくずリスト ?>
<?php //パンくずリストを表示しするとき
if (is_page_breadcrumbs_visible()): ?>
<?php if ( !is_front_page() ): //個別ページでパンくずリストを表示する場合?>
<div id="breadcrumb" class="breadcrumb breadcrumb-page<?php echo get_additional_page_breadcrumbs_classes(); ?>" itemscope itemtype="http://schema.org/BreadcrumbList">
  <?php $count = 0;
  $per_ids = array_reverse(get_post_ancestors($post->ID)); ?>
  <div class="breadcrumb-home" itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-home fa-fw"></span><a href="<?php echo home_url(); ?>" itemprop="item"><span itemprop="name"><?php _e( 'ホーム', THEME_NAME ) ?></span></a><meta itemprop="position" content="1" /><?php echo (count($per_ids) == 0) ? '' : '<span class="sp"><span class="fa fa-angle-right"></span></span>' ?></div>
  <?php foreach ( $per_ids as $par_id ){
    $count += 1;?>
    <div class="breadcrumb-item" itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw"></span><a href="<?php echo get_page_link( $par_id );?>" itemprop="item"><span itemprop="name"><?php echo get_page($par_id)->post_title; ?></span></a><meta itemprop="position" content="<?php echo $count + 1; ?>" /><?php echo (count($per_ids) == $count) ? '' : '<span class="sp"><span class="fa fa-angle-right"></span></span>' ?></div>
  <?php } ?>
</div><!-- /#breadcrumb -->
<?php endif; ?>
<?php endif //is_page_breadcrumbs_visible ?>