<?php //固定ページ用のパンくずリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php //パンくずリストを表示するとき
if (is_page_breadcrumbs_visible() && is_page()): ?>
<?php if ( !is_front_page() ): //個別ページでパンくずリストを表示する場合
$root_text = __( 'ホーム', THEME_NAME );
$root_text = apply_filters('breadcrumbs_page_root_text', $root_text);
?>
<div id="breadcrumb" class="breadcrumb breadcrumb-page<?php echo get_additional_page_breadcrumbs_classes(); ?>" itemscope itemtype="https://schema.org/BreadcrumbList">
  <?php $count = 1;
  $per_ids = array_reverse(get_post_ancestors($post->ID));
   ?><div class="breadcrumb-home" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-home fa-fw" aria-hidden="true"></span><a href="<?php echo esc_url(get_home_url()); ?>" itemprop="item"><span itemprop="name" class="breadcrumb-caption"><?php echo esc_html($root_text); ?></span></a><meta itemprop="position" content="<?php echo $count; ?>" /><?php echo (count($per_ids) == 0 && !is_page_breadcrumbs_include_post()) ? '' : '<span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span>' ?></div>
  <?php foreach ( $per_ids as $par_id ){
    $count++;
    $page = get_post($par_id);
    $post_title = $page->post_title;
    $post_title = apply_filters('breadcrumbs_page_title', $post_title, $page);
    ?><div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw" aria-hidden="true"></span><a href="<?php echo esc_url(get_page_link( $par_id ));?>" itemprop="item"><span itemprop="name" class="breadcrumb-caption"><?php echo esc_html($post_title); ?></span></a><meta itemprop="position" content="<?php echo $count; ?>" /><?php echo (count($per_ids) < $count && !is_page_breadcrumbs_include_post()) ? '' : '<span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span>' ?></div>
  <?php } ?>
  <?php //ページタイトルを含める場合
  if (is_page_breadcrumbs_include_post()):
    $count++;
    ?><div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw" aria-hidden="true"></span><span class="breadcrumb-caption" itemprop="name"><?php echo esc_html(get_the_title()); ?></span><meta itemprop="position" content="<?php echo $count; ?>" /></div>
  <?php endif ?>
</div><!-- /#breadcrumb -->
<?php endif; ?>
<?php endif //is_page_breadcrumbs_visible ?>
