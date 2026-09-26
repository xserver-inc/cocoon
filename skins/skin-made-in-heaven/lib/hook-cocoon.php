<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  タブ一覧表示件数変更
//******************************************************************************
add_filter('list_category_tab_args', function($args, $cat_id) {
  $args['posts_per_page'] = 12;
 
  return $args;
}, 10, 2);


//******************************************************************************
//  検索件数表示
//******************************************************************************
add_filter('get_archive_chapter_title', function($chapter_title) {
  global $wp_query;

  $chapter_title = preg_replace('/.*<\/span>/', '', $chapter_title);
  if (is_search()) {
    $count = 0;
    $chapter_title = str_replace('"', '', $chapter_title);
    if (have_posts()) {
      $count =  $wp_query->found_posts;
    }
    $chapter_title = sprintf('%s (%s)', $chapter_title, number_format($count));
  }
  $chapter_title = '<span class="list-title-in"><span>' . $chapter_title . '</span></span>';

  return $chapter_title;
});


//******************************************************************************
//  「もっと見る」「次のページ」「パンくず」テキスト変更
//******************************************************************************
// 「もっと見る」テキスト変更
add_filter('more_button_caption', function($caption) {
  return get_theme_mod('hvn_button_more_setting',  __('もっと見る', THEME_NAME));
});


// 「次のページ」テキスト変更
add_filter('pagination_next_link_caption', function($caption) {
  return get_theme_mod('hvn_button_next_setting',  __('次のページ', THEME_NAME));
});


// 「パンくず」テキスト変更
add_filter('breadcrumbs_single_root_text' , 'hvn_breadcrumbs_root_text_custom');


// 「パンくず」テキスト変更
add_filter('breadcrumbs_page_root_text'   , 'hvn_breadcrumbs_root_text_custom');


//******************************************************************************
//  モバイルSNSボタンカラー変更
//******************************************************************************
// モバイルSNSシェアボタンカラー変更
add_filter('get_additional_sns_share_button_classes', 'hvn_sns_brand_color_replace');


// モバイルSNSフォローボタンカラー変更
add_filter('get_additional_sns_follow_button_classes', 'hvn_sns_brand_color_replace');


//******************************************************************************
//  PV数表示変更
//******************************************************************************
add_filter('popular_entry_card_pv_text', function($pv_text, $pv, $pv_unit) {
  return $pv;
}, 10, 3);


//******************************************************************************
//  PV位置変更
//******************************************************************************
add_filter('cocoon_part__tmp/popular-card', function($html) {
  $dom = new DOMDocument();
  libxml_use_internal_errors(true);
  $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  $xpath = new DOMXPath($dom);

  $pv_node   = $xpath->query('//span[contains(@class, "popular-entry-card-pv")]')->item(0);
  $info_node = $xpath->query('//div[contains(@class, "popular-entry-card-info")]')->item(0);

  // 要素を移動
  if ($pv_node && $info_node) {
    $info_node->appendChild($pv_node);
    $html = $dom->saveHTML();
  }

  libxml_clear_errors();
  return $html;
});


//******************************************************************************
//  bodyクラス追加
//******************************************************************************
add_filter('body_class_additional', function($classes) {
  $classes[] = 'hvn';

  // 「設定値 => [クラス名, デフォルト値]」のマッピング
  $settings = [
    'hvn_toc_setting'     => ['hvn-scroll-toc'    , false],
    'hvn_border_setting'  => ['hvn-card-border'   , true],
    'hvn_content_setting' => ['hvn-content-border', true],
  ];

  foreach ($settings as $mod => $data) {
    $class   = $data[0];
    $default = $data[1];

    if (get_theme_mod($mod, $default)) {
      $classes[] = $class;
    }
  }

  if (!get_theme_mod('hvn_border_radius_setting')) {
    $classes[] = 'hvn-radius';
  }

  return $classes;
}, 999);


//******************************************************************************
//  サイト開設経過日数追加
//******************************************************************************
add_filter('the_author_box_description', function($description, $user_id) {
  $date = get_theme_mod('hvn_site_date_setting');

  if ($date && get_theme_mod('hvn_site_date_onoff_setting')) {
    // 現在のタイムスタンプを取得
    $current_time = time();
    $start_time = strtotime($date);

    // 経過日数を算出
    $day = number_format(ceil(($current_time - $start_time) / DAY_IN_SECONDS));
    $description .= '<p class="hvn_site_date">' . sprintf(__('%s開設から%s日目です。', THEME_NAME), esc_html($date), $day) . '</p>';
  }

  if (get_theme_mod('hvn_profile_btn_setting')) {
    $url = get_the_author_profile_page_url($user_id);
    if ($url) {
      $description .= '<div class="hvn-profile-btn"><a class="key-btn" href="' . esc_url($url) . '"></a></div>';
    }
  }

  return $description;
}, 10, 2);


//******************************************************************************
//  エントリーカードいいねボタン追加
//******************************************************************************
add_action('entry_card_snippet_after', function($post_ID) {
  echo hvn_like_tag($post_ID);
});


// 投稿ページいいねボタン追加
add_filter('cocoon_part__tmp/date-tags', function($content) {
  if (!is_single()) return $content;

  $post_ID = get_the_ID();
  $comment_tag = '';

  $like_tag = hvn_like_tag($post_ID);

  if (is_single_comment_visible()) {
    $num = get_comments_number($post_ID);
    $comment_tag = '<span class="post-comment-count"><span class="fa fa-comment-o comment-icon" aria-hidden="true"></span>' . $num . '</span>';
  }

  $new_content = '<div class="date-tags">' . $like_tag . '<div class="e-card-info">' . get_the_date_tags() . $comment_tag . '</div></div>';

  return $new_content;
});


// ウィジェットいいねボタン追加
add_action('widget_entry_card_date_before', function($prefix, $post_ID) {
  if ($prefix == WIDGET_NEW_ENTRY_CARD_PREFIX) {
    $post_ID = get_the_ID();
  }
  echo hvn_like_tag($post_ID);
}, 10, 2);


//******************************************************************************
//  メインビジュアル追加
//******************************************************************************
add_action('cocoon_part_before__tmp/appeal', function() {
  if (
    get_theme_mod('hvn_header_setting', 'none') != 'none' &&
    is_front_top_page() &&
    !is_singular_page_type_content_only()
  ) {
    $html = hvn_add_header();
    echo '<div class="hvn-header">' . $html . '</div><div id="hvn-anchor"></div>';
  }
});


//******************************************************************************
//  CocoonカスタムCSS変更
//******************************************************************************
add_filter('cocoon_part__tmp/css-custom', function($content) {
  $h = get_entry_content_margin_hight();

  $maps = [
    "margin-bottom: {$h}em" => 'margin-bottom:' . HVN_GAP . 'px',
    '67.4%'                 => 'calc(70% - var(--gap30))',
  ];
  $content = str_replace(array_keys($maps), array_values($maps), $content);

  return $content;
});


//******************************************************************************
//  Swiperマージン変更
//******************************************************************************
add_filter('cocoon_part__tmp/footer-javascript', function($content) {
  $content = str_replace('spaceBetween: 4', 'spaceBetween: ' . HVN_GAP, $content);

  return $content;
});


//******************************************************************************
//  おすすめカードSwiper変更
//******************************************************************************
add_filter('recommend_cards_navi_list_atts', function($atts) {
  if (!is_admin()) {
    $atts['type'] = ET_LARGE_THUMB;
    $atts['horizontal'] = 1;
  }
  return $atts;
});


// おすすめカードSwiper自動再生
add_filter('cocoon_part__tmp/recommended-cards', function($html) {
  if (
    !is_admin() &&
    is_recommended_cards_visible() &&
    get_theme_mod('hvn_swiper_auto_setting')
  ) {
    $maps = [
      '/(<div class="swiper-button-prev">)/' => '<div class="swiper-pagination"></div>$1',
      '/is-list-horizontal/'                 => 'is-auto-horizontal'
    ];

    $html = preg_replace(array_keys($maps), array_values($maps), $html);
  }

  return $html;
});


//******************************************************************************
//  カスタムJS追加
//******************************************************************************
add_action('cocoon_part_after__tmp/footer-javascript', function() {
  ob_start();
  cocoon_template_part(HVN_SKIN . 'tmp/js-custom');
  $js = ob_get_clean();
  echo '<script id="hvn-custom-js">' . $js . '</script>';
});


//******************************************************************************
//  オリジナルレイアウト変更
//******************************************************************************
add_filter('front_page_type_map', function($template_map) {
  if (
    get_theme_mod('hvn_card_expansion_setting') &&
    (is_entry_card_type_vertical_card_2() || is_entry_card_type_vertical_card_3())
  ) {
    $template_map['category_2_columns'] = HVN_SKIN . 'tmp/list-category-columns';
    $template_map['category_3_columns'] = HVN_SKIN . 'tmp/list-category-columns';
  }

  return $template_map;
});


//******************************************************************************
//  カテゴリーごと間波線追加
//******************************************************************************
add_filter('cocoon_part__tmp/list', function($content) {
  $cat_ids = get_index_list_category_ids();
  if (
    count($cat_ids) &&
    get_theme_mod('hvn_front_none_setting', true) &&
    get_theme_mod('hvn_category_color_setting') &&
    get_theme_mod('hvn_header_wave_setting') &&
    !is_the_page_sidebar_visible()
  ) {
    $html = hvn_wave('hvn-wave-category');
    $content = str_replace('<div id="list-columns"', $html . '<div id="list-columns"', $content);
  }

  return $content;
});


//******************************************************************************
//  カテゴリーごと（2、3カード）変更
//******************************************************************************
add_filter('list_category_column_atts', function($atts, $cat_id) {
  if (
    is_entry_card_type_big_card() ||
    is_entry_card_type_vertical_card_2() ||
    is_entry_card_type_vertical_card_3()
  ) {
    $atts['type'] = ET_LARGE_THUMB;
  }
  $atts['comment'] = is_entry_card_post_comment_count_visible();
  $atts['date'] = 1;

  return $atts;
}, 2, 10);


//******************************************************************************
//  プロフィールSNSフォロー表示
//******************************************************************************
add_filter('cocoon_part__tmp/sns-follow-buttons', function($content) {
  if (!get_theme_mod('hvn_profile_follows_setting', true)) {
    if (get_query_var('option') == 'sf-profile') {
      $content = null;
    }
  }

  return $content;
});


//******************************************************************************
//  ダークモードボタン追加
//******************************************************************************
add_filter('cocoon_part__tmp/footer-bottom', function($content) {
  if (get_theme_mod('hvn_darkmode_setting')) {
    $html = '<div id="hvn-dark-toggle" class="hvn-dark-switch"><i class="far fa-moon"></i></div>';
    $content =  preg_replace('/(class="source-org copyright">.*)<\/div>/', "$1$html</div>", $content);
  }
  return $content; 
});


//******************************************************************************
//  通知エリア更新
//******************************************************************************
add_filter('get_notice_area_message', function($msg) {
  global $_THEME_OPTIONS;

  $GLOBALS['hvn_notice'] = false;

  if (is_admin()) return $msg;

  // メニューを取得
  $locations = get_nav_menu_locations();
  if (empty($locations['hvn-notice-menu'])) return $msg;

  // メニューアイテムを取得
  $menu_items = wp_get_nav_menu_items($locations['hvn-notice-menu']);
  if (empty($menu_items)) return $msg;

  // リンクの開き方を設定
  $target_attr = is_notice_link_target_blank() ? ' target="_blank" rel="noopener"' : '';
  $items_html = [];

  foreach ($menu_items as $item) {
    $url = trim($item->url);
    // リンク設定の場合
    if (!empty($url) && $url !== '#') {
      $items_html[] = '<a href="' . esc_url($url) . '"' . $target_attr . '>' . $item->title . '</a>';
    } else {
      $items_html[] = $item->title;
    }
  }

  // メニューがない場合
  $count = count($items_html);
  if ($count === 0) return $msg;

  // メニューが1件の場合
  if ($count === 1) {
    $msg = $items_html[0];
  } else {
    $GLOBALS['hvn_notice'] = true;
    $html = '';
    foreach ($items_html as $item_html) {
      $html .= '<div class="swiper-slide">' . $item_html . '</div>';
    }

    $swiper_vertical = !get_theme_mod('hvn_notice_scroll_setting') ? 'swiper-vertical' : '';
    $msg = '<div class="swiper ' . $swiper_vertical . '"><div class="swiper-wrapper">' . $html . '</div></div>';
  }

  // 通知URLを無効
  $_THEME_OPTIONS['notice_area_url'] = '';

  return $msg;
});


//******************************************************************************
//  タイトルとURLをコピー変更
//******************************************************************************
add_filter('cocoon_part__tmp/sns-share-buttons', function($content) {
  $before = '/data-clipboard-text="[^"]*" title/';
  $after = 'data-clipboard-text="&lt;a href=' . get_share_page_url() . '&gt;' . get_share_page_title() . '&lt;/a&gt;" title';
  $content = preg_replace($before, $after, $content);

  return $content;
});


//******************************************************************************
//  アイキャッチ自動生成
//******************************************************************************
// アイキャッチ自動生成（背景カラー）
add_filter('featured_image_background_color_code', function($color) {
  return get_theme_mod('hvn_thumb_color0_setting', '#ffffff');
});


// アイキャッチ自動生成（テキストカラー）
add_filter('featured_image_text_color_code', function($color) {
  return get_theme_mod('hvn_thumb_color1_setting', '#333333');
});


// アイキャッチ自動生成（枠カラー）
add_filter('featured_image_border_color_code', function($color) {
  return get_theme_mod('hvn_thumb_color2_setting', '#a2d7dd');
});


//******************************************************************************
//  プロフィール背景動画追加
//******************************************************************************
add_filter('cocoon_part__tmp/author-box', function($content) {
  $video_id = get_theme_mod('hvn_prof_video_setting');
  $video_url = $video_id ? wp_get_attachment_url($video_id) : '';

  if ($video_url) {
    $video_tag = '<video class="hvn-author-video" autoplay muted loop playsinline><source src="' . esc_url($video_url) . '" type="video/mp4"></video>';
    $search = '<figure class="author-thumb';

    $content = str_replace($search, $video_tag . $search, $content);
  }
  return $content;
});


//******************************************************************************
//  Cocoon独自メニュー「テンプレート」削除
//******************************************************************************
add_filter('cocoon_menu_definitions', function($definitions) {
  if (isset($definitions['theme-func-text'])) {
    unset($definitions['theme-func-text']);
  }
  return $definitions;
});


//******************************************************************************
//  Cocoon独自メニューに「ログアウト」追加
//******************************************************************************
add_filter('cocoon_admin_bar_menus', function($menus, $wp_admin_bar) {
  $menus[] = [
    'parent' => 'dashboard_menu',
    'id'     => 'dashboard_menu-logout',
    'title'  => __('ログアウト', THEME_NAME),
    'href'   => wp_logout_url(),
  ];
  return $menus;
}, 10, 2);


//******************************************************************************
//  新着記事にコメント数順を追加
//******************************************************************************
add_filter('widget_entries_args', function($args) {
  if (!empty($args['action']) && $args['action'] === 'comment') {
    $args['orderby'] = [
      'comment_count' => 'DESC',
      'date'          => 'DESC',
    ];
    $args['hvn_comments'] = true;
  }
  return $args;
});


//******************************************************************************
//  タブ一覧「新着記事」を非表示
//******************************************************************************
add_filter('cocoon_part__tmp/list-tab-index', function($html) {
  $maps = [
    'checked'           => '',
    'id="index-tab-2"'  => 'id="index-tab-2" checked'
  ];

  if (!get_theme_mod('hvn_front_none_setting', true)) {
    $html = str_replace(array_keys($maps), array_values($maps), $html);
  }

  return $html;
});


//******************************************************************************
//  ナビカードリボン設定より投稿を優先し設定
//******************************************************************************
add_filter('cocoon_part_args__tmp/widget-entry-card', function($args) {
  if ($args['prefix'] === 'navi') {
    $post_id = url_to_postid($args['url']);

    if ($post_id) {
      $memo = get_post_meta($post_id, 'the_page_memo', true);

      if (preg_match('/ribbon-color-[1-5]/', $memo, $class)) {
        $args['div_class'] = str_replace('e-card', 'e-card ' . $class[0] , $args['div_class']);
        $args['ribbon_class'] = '';
      }
    }
  }

  return $args;
});


//******************************************************************************
//  独自ブロックカテゴリー追加
//******************************************************************************
add_filter('cocoon_theme_block_categories', function($block_categories) {
  $block_categories[] = [
    'slug'  => 'hvn-block',
    'title' => __('メイド・イン・ヘブン', THEME_NAME),
  ];
  return $block_categories;
});


//******************************************************************************
//  目次表示チェック
//******************************************************************************
add_filter('get_toc_tag', function($html, $harray, $is_widget) {
  // 展開済みの本文を取得
  $content = get_toc_expanded_content();

  // 本文内のH2タグを順番に検索
  if (preg_match_all('/<h2\b[^>]*>(.*?)<\/h2>/is', $content ?? '', $matches)) {
    // 最初に見つかったH2タグをチェック
    $first_h2 = $matches[0][0];

    // 最初に見つかったH2が「FAQ」または「タイムライン」のクラスを持っている場合
    if (strpos($first_h2, 'faq-question-content') !== false ||
        strpos($first_h2, 'timeline-item-title')  !== false) {

      // 特殊ブロックの見出しが先頭にある場合の目次出力抑止
      return null;
    }
  }

  return $html;
}, 10, 3);


//******************************************************************************
//  フロントCSS追加
//******************************************************************************
add_action('wp_enqueue_scripts_before_skin_style', function() {
  hvn_h2_h4_css(['cocoon-style', 'cocoon-skin-style']);
  wp_dequeue_style('scrollhint-style');
  wp_enqueue_script('scrollhint-js', get_cocoon_template_directory_uri() . '/plugins/scroll-hint-master/js/scroll-hint.min.js', ['jquery'], false, true);
}, 999);


//******************************************************************************
//  エディター設定CSSをGutenbergエディターにインライン追加
//******************************************************************************
add_filter('cocoon_gutenberg_stylesheets', function($stylesheets) {
  $url = local_to_url(hvn_editor_css_cache_file());

  // スキンCSSの位置を取得
  $skins_index = false;
  foreach ($stylesheets as $index => $style_url) {
    if (strpos($style_url, '/skins/') !== false) {
      $skins_index = $index;
      break;
    }
  }

  if ($skins_index !== false) {
    array_splice($stylesheets, $skins_index + 1, 0, $url);
  }

  return $stylesheets;
}, 999);


//******************************************************************************
//  分割ページのページネーションのデザイン変更
//******************************************************************************
add_filter('cocoon_part__tmp/pager-page-links', function() {
  global $page, $numpages, $multipage;

  // 複数ページ以外の場合
  if (!$multipage) return;

  $mid_size = 2;  // 現在ページの前後に表示するページ数
  $dot  = false;
  $html = '';

  // 分割ページのURLを取得し、指定されたクラスを付与した<a>タグを生成
  $get_clean_link = function($n, $classes) {
    // _wp_link_page()で得たHTMLから、正規表現を用いて href属性のみを抽出
    preg_match('/href=["\']([^"\']+)["\']/', _wp_link_page($n), $matches);
    $url = !empty($matches[1]) ? esc_url(htmlspecialchars_decode($matches[1])) : '#';

    return '<a href="' . $url . '" class="' . $classes . '">';
  };

  // 前へボタンの追加
  if ($page > 1) {
    $html .= $get_clean_link($page - 1, 'prev page-numbers') . '<span class="screen-reader-text">' . __('前へ', THEME_NAME) . '</span><span class="fas fa-angle-left" aria-hidden="true"></span></a>';
  }

  // ページ番号の生成
  for ($n=1; $n<=$numpages; $n++) {
    if ($n == $page) {
      // 現在のページにはクラスcurrentを追加
      $html .= '<span aria-current="page" class="page-numbers current">' . $n .'</span>';
      $dot = true;
    } else {
      if ($n == 1 || ($page - $mid_size <= $n && $n <= $page + $mid_size) || $n == $numpages) {
        $html .= $get_clean_link($n, 'page-numbers') . $n . '</a>';
        $dot = true;
      } elseif ($dot) {
        // ドットの表示
        $html .= '<span class="page-numbers dots">&hellip;</span>';
        $dot = false;
      }
    }
  }

  // 次へボタンの追加
  if ($page < $numpages) {
    $html .= $get_clean_link($page + 1, 'next page-numbers') . '<span class="screen-reader-text">' . __('次へ', THEME_NAME) . '</span><span class="fas fa-angle-right" aria-hidden="true"></span></a>';
  }

  return '<div class="pager-numbers">' . $html . '</div>';
});
