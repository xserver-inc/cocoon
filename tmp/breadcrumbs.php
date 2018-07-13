<?php //カテゴリ用のパンくずリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if (is_single_breadcrumbs_visible() && is_single()):
$cat = get_the_category();
if($cat && !is_wp_error($cat)){
  $echo = null;
  $par = get_category($cat[0]->parent);
  echo '<div id="breadcrumb" class="breadcrumb breadcrumb-category'.get_additional_single_breadcrumbs_classes().'" itemscope itemtype="https://schema.org/BreadcrumbList">';
  echo '<div class="breadcrumb-home" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-home fa-fw"></span><a href="'.home_url().'" itemprop="item"><span itemprop="name">'.__( 'ホーム', THEME_NAME ).'</span></a><meta itemprop="position" content="1" /><span class="sp"><span class="fa fa-angle-right"></span></span></div>';
  $count = 1;
  while($par && !is_wp_error($par) && $par->term_id != 0){
    ++$count;
    $echo = '<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw"></span><a href="'.get_category_link($par->term_id).'" itemprop="item"><span itemprop="name">'.$par->name.'</span></a><meta itemprop="position" content="'.$count.'" /><span class="sp"><span class="fa fa-angle-right"></span></span></div>'.$echo;
    $par = get_category($par->parent);
  }
  // // 親カテゴリまで表示する場合
  // ++$count;
  // echo $echo.'<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw"></span><a href="'.get_category_link($cat[0]->term_id).'" itemprop="item"><span itemprop="name">'.$cat[0]->name.'</span></a><meta itemprop="position" content="'.$count.'" /></div>';

  // 親カテゴリの出力
  ++$count;
  echo $echo.'<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw"></span><a href="'.get_category_link($cat[0]->term_id).'" itemprop="item"><span itemprop="name">'.$cat[0]->name.'</span></a><meta itemprop="position" content="'.$count.'" />';
  //ページタイトルを含める場合はセパレーターを表示
  if (is_single_breadcrumbs_include_post()) {
    echo '<span class="sp"><span class="fa fa-angle-right"></span></span>';
  }
  echo '</div>';
  //ページタイトルを含める場合
  if (is_single_breadcrumbs_include_post()) {
    ++$count;
    echo '<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw"></span><span itemprop="item"><span itemprop="name">'.get_the_title().'</span></span><meta itemprop="position" content="'.$count.'" /></div>';
  }

  echo '</div><!-- /#breadcrumb -->';
}
endif //is_single_breadcrumbs_visible ?>
