<?php //固定ページ用のパンくずリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php //パンくずリストを表示するとき
if (is_page_breadcrumbs_visible()): ?>
<?php if ( !is_front_page() ): //個別ページでパンくずリストを表示する場合?>
<div id="breadcrumb" class="breadcrumb breadcrumb-page<?php echo get_additional_page_breadcrumbs_classes(); ?>" itemscope itemtype="https://schema.org/BreadcrumbList">
  <?php $count = 0;
  $per_ids = array_reverse(get_post_ancestors($post->ID));
   ?><div class="breadcrumb-home" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-home fa-fw"></span><a href="<?php echo home_url(); ?>" itemprop="item"><span itemprop="name"><?php _e( 'ホーム', THEME_NAME ) ?></span></a><meta itemprop="position" content="1" /><?php echo (count($per_ids) == 0 && !is_page_breadcrumbs_include_post()) ? '' : '<span class="sp"><span class="fa fa-angle-right"></span></span>' ?></div>
  <?php foreach ( $per_ids as $par_id ){
    $count += 1;
    ?><div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw"></span><a href="<?php echo get_page_link( $par_id );?>" itemprop="item"><span itemprop="name"><?php echo get_page($par_id)->post_title; ?></span></a><meta itemprop="position" content="<?php echo $count + 1; ?>" /><?php echo (count($per_ids) == $count && !is_page_breadcrumbs_include_post()) ? '' : '<span class="sp"><span class="fa fa-angle-right"></span></span>' ?></div>
  <?php } ?>
  <?php //ページタイトルを含める場合
  if (is_page_breadcrumbs_include_post()):
    /*
    $count += 1;
    <div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw"></span><span itemprop="item"><span itemprop="name"><?php the_title(); ?></span></span><meta itemprop="position" content="<?php echo $count + 1; ?>" /></div>
    */
    ?><div class="breadcrumb-item"><span class="fa fa-file-o fa-fw"></span><span><?php the_title(); ?></span></div>
  <?php endif ?>
</div><!-- /#breadcrumb -->
<?php endif; ?>
<?php endif //is_page_breadcrumbs_visible ?>
