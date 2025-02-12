
Written by Anonymous

<?php // カテゴリ用のパンくずリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if ( !defined( 'ABSPATH' ) ) exit; // WordPress の直接実行を防止

// パンくずリストの表示条件をチェック
if (is_single_breadcrumbs_visible() && (is_single() || is_tax() || is_category())) {
  $cat = null;

  // 現在の投稿タイプを取得
  $post_type = get_post_type();

  // 投稿タイプに関連付けられたタクソノミーを取得
  $taxonomies = get_object_taxonomies($post_type);

 // 階層型タクソノミーページでない場合（タグ）
 if (!is_taxonomy_hierarchical($taxonomies[0]) && is_tax()) return;

  // 投稿ページの場合
  if (is_single()) {
    $args = array(
        'order'   => 'ASC', // 昇順に並べる
        'orderby' => 'name', // 名前順で並べる
    );

    $cats = wp_get_post_terms($post->ID, $taxonomies, $args);
    if (!empty($cats)) {
      // 最初のタクソノミーを取得
      $cat = $cats[0];
    }

    // 投稿にメインカテゴリー設定の場合
    $main_cat_id = get_the_page_main_category();
    if ($main_cat_id && in_category($main_cat_id)) {
      $cat = get_category($main_cat_id);
    }
  } else {
   // 現在のクエリオブジェクトを取得
    $cat = get_queried_object();
  }

  if ($cat && !is_wp_error($cat)) {
    // パンくずルート表示
    $echo = '';
    $root_text = __( 'ホーム', THEME_NAME );
    $root_text = apply_filters('breadcrumbs_single_root_text', $root_text);


    echo '<div id="breadcrumb" class="breadcrumb breadcrumb-category'.get_additional_single_breadcrumbs_classes().'" itemscope itemtype="https://schema.org/BreadcrumbList">';
    echo '<div class="breadcrumb-home" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-home fa-fw" aria-hidden="true"></span><a href="'.esc_url(get_home_url()).'" itemprop="item"><span itemprop="name" class="breadcrumb-caption">'.esc_html($root_text).'</span></a><meta itemprop="position" content="1" /><span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span></div>';

    $count = 1;

    // 階層型タクソノミー（カテゴリー）、カテゴリーの場合
    if ((is_taxonomy_hierarchical($taxonomies[0]) && (is_tax() || 'post' !== get_post_type()))  || is_category() || 'post' === get_post_type()) {
       // 親カテゴリーを取得
      $par = get_term($cat->parent, get_query_var('taxonomy', 'category'));

      $cats = [];

      // 親のカテゴリーを取得
      while ($par && !is_wp_error($par) && $par->term_id != 0) {
        $cats[] = $par;
        $par = get_term($par->parent, get_query_var('taxonomy', 'category'));
      }

      // 先祖順に並べ替え
      $cats = array_reverse($cats);

      // 先祖カテゴリーを表示
      foreach ($cats as $par) {
        ++$count;
        $echo .= '<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw" aria-hidden="true"></span><a href="'.esc_url(get_category_link($par->term_id)).'" itemprop="item"><span itemprop="name" class="breadcrumb-caption">'.esc_html($par->name).'</span></a><meta itemprop="position" content="'.$count.'" /><span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span></div>';
      }

      // 現在のカテゴリーを表示
      echo $echo.'<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-folder fa-fw" aria-hidden="true"></span><a href="'.esc_url(get_category_link($cat->term_id)).'" itemprop="item"><span itemprop="name" class="breadcrumb-caption">'.esc_html($cat->name).'</span></a><meta itemprop="position" content="'.$count.'" />';

      // ページタイトルを含める場合、セパレータ表示
      if (is_single_breadcrumbs_include_post() && is_single()) {
        echo '<span class="sp"><span class="fa fa-angle-right" aria-hidden="true"></span></span>';
      }
      echo '</div>';
    }

    // 投稿タイトルの表示
    if (is_single_breadcrumbs_include_post() && is_singular()) {
      ++$count;
      echo '<div class="breadcrumb-item" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span class="fa fa-file-o fa-fw" aria-hidden="true"></span><span class="breadcrumb-caption" itemprop="name">'.esc_html(get_the_title()).'</span><meta itemprop="position" content="'.$count.'" /></div>';
    }

    echo '</div>';
  }
}

