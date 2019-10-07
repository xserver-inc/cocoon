<?php //カテゴリ用のパンくずリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_single_breadcrumbs_visible() && (is_single() || is_category())){
  $cats = get_the_category();
  $cat = (is_single() && isset($cats[0])) ? $cats[0] : get_category(get_query_var("cat"));
  if($cat && !is_wp_error($cat)){
    $echo = null;
    $root_text = __( 'ホーム', THEME_NAME );
    $root_text = apply_filters('breadcrumbs_single_root_text', $root_text);
    //var_dump($par);
    echo '<div id="breadcrumb" class="breadcrumb breadcrumb-category'.get_additional_single_breadcrumbs_classes().'" itemscope itemtype="https://schema.org/BreadcrumbList">';
    echo '<div class="breadcrumb-home" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-home fa-fw" aria-hidden="true"></span><a href="'.esc_url(get_home_url()).'" itemprop="item"><span itemprop="name">'.esc_html($root_text).'</span></a><meta itemprop="position" content="1" /><span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span></div>';
    $count = 1;
    $par = get_category($cat->parent);
    //カテゴリ情報の取得
    $cats = array();
    while($par && !is_wp_error($par) && $par->term_id != 0){
      $cats[] = $par;
      $par = get_category($par->parent);
    }
    //カテゴリの順番入れ替え
    $cats = array_reverse($cats);
    //先祖カテゴリの出力
    foreach ($cats as $par) {
      ++$count;
      //var_dump($par->name);
      $echo .= '<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw" aria-hidden="true"></span><a href="'.esc_url(get_category_link($par->term_id)).'" itemprop="item"><span itemprop="name">'.esc_html($par->name).'</span></a><meta itemprop="position" content="'.$count.'" /><span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span></div>';
    }
    // 現カテゴリの出力
    ++$count;
    echo $echo.'<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw" aria-hidden="true"></span><a href="'.esc_url(get_category_link($cat->term_id)).'" itemprop="item"><span itemprop="name">'.esc_html($cat->name).'</span></a><meta itemprop="position" content="'.$count.'" />';
    //ページタイトルを含める場合はセパレーターを表示
    if (is_single_breadcrumbs_include_post() && is_singular()) {
      echo '<span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span>';
    }
    echo '</div>';
    //ページタイトルを含める場合
    if (is_single_breadcrumbs_include_post() && is_singular()) {
      echo '<div class="breadcrumb-item"><span class="fa fa-file-o fa-fw" aria-hidden="true"></span><span>'.esc_html(get_the_title()).'</span></div>';
    }

    echo '</div><!-- /#breadcrumb -->';
  }
} //is_single_breadcrumbs_visible ?>
