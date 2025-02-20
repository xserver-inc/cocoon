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
    $chapter_title = $chapter_title . ' : ' . sprintf(__('%s件ヒット', THEME_NAME), $count);
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
add_filter('breadcrumbs_single_root_text', 'breadcrumbs_root_text_custom');
add_filter('breadcrumbs_page_root_text', 'breadcrumbs_root_text_custom');


// 「ホーム」テキスト変更
if (!function_exists('breadcrumbs_root_text_custom')):
function breadcrumbs_root_text_custom(){
  return get_theme_mod('hvn_breadcrumbs_setting', __('ホーム', THEME_NAME));
}
endif;


//******************************************************************************
//  bodyクラス追加
//******************************************************************************
add_filter('body_class_additional', function($classes) {
  $classes[] = 'hvn';
  if (get_theme_mod('hvn_toc_setting')) {
    $classes[] = 'hvn-scroll-toc';
  }

  if (get_theme_mod('hvn_border_setting', true)) {
    $classes[] = 'hvn-card-border';
  }

  if (get_theme_mod('hvn_content_setting', true)) {
    $classes[] = 'hvn-content-border';
  }

  if (!get_theme_mod('hvn_border_radius_setting', false)) {
    $classes[] = 'hvn-radius';
  }

  return $classes;
}, 999);


//******************************************************************************
//  SNSシェアボタン変更
//******************************************************************************
add_filter('get_additional_sns_share_button_classes', function($classes) {
  $classes = str_replace('bc-brand-color ', 'bc-brand-color-white ', $classes);

  return $classes;
});


//******************************************************************************
//  SNSフォローボタン変更
//******************************************************************************
add_filter('get_additional_sns_follow_button_classes', function($classes) {
  $classes = str_replace('bc-brand-color ', 'bc-brand-color-white ', $classes);

  return $classes;
});


//******************************************************************************
//  サイト開設経過日数
//******************************************************************************
add_filter('the_author_box_description', function($description, $user_id) {
  $date = get_theme_mod('hvn_site_date_setting');
  if ($date  && get_theme_mod('hvn_site_date_onoff_setting')) {
    $day = number_format(ceil(date_i18n('U') - strtotime($date)) / (24 * 60 * 60));
    $description .= "<p class=hvn_site_date>" . sprintf(__('%s開設から%s日目です。', THEME_NAME), $date, $day) . "</p>";
  }

  if (get_theme_mod('hvn_profile_btn_setting')) {
    $url = get_the_author_profile_page_url($user_id);
    if ($url) {
      $description .= "<div class=hvn-profile-btn><a class=key-btn href=\"{$url}\">" . __('プロフィール', THEME_NAME) . "</a></div>";
    }
  }

  return $description;
}, 10, 2);


//******************************************************************************
//  エントリーカードいいねボタン
//******************************************************************************
add_action('entry_card_snippet_after', function($post_ID) {
  if (get_theme_mod('hvn_like_setting')) {
    echo hvn_like_tag($post_ID);
  }
});


// 投稿ページいいねボタン追加
add_filter('cocoon_part__tmp/date-tags', function($content) {
  if (is_single()  && get_theme_mod('hvn_like_setting')) {
    $post_ID = get_the_ID();
    $html = hvn_like_tag($post_ID);

    $content = str_replace('</div>', '</div></div>', $content);
    $content = str_replace('<div class="date-tags">', '<div class="date-tags">' . $html . '<div class="e-card-info">', $content);
  }

  return $content;
});


// ウィジェットいいねボタン追加
add_action('widget_entry_card_date_before', function($prefix, $post_ID) {
  if (get_theme_mod('hvn_like_setting')) {
    if ($prefix == WIDGET_NEW_ENTRY_CARD_PREFIX) {
      $post_ID = get_the_ID();
    }
    echo hvn_like_tag($post_ID);
  }
}, 10, 2);


//******************************************************************************
//  PV数表示変更
//******************************************************************************
add_filter('popular_entry_card_pv_text', function($pv_text, $pv, $pv_unit) {
  return $pv;
}, 10, 3);


//******************************************************************************
//  メインビジュアル追加
//******************************************************************************
add_action('cocoon_part_before__tmp/appeal', function() {
  if ((get_theme_mod('hvn_header_setting', 'none') != 'none') && is_front_top_page() && !is_singular_page_type_content_only()) {
    $html = hvn_add_header();
    echo "<div class=hvn-header>{$html}</div>";
  }
});


//******************************************************************************
//  CocoonカスタムCSS変更
//******************************************************************************
add_filter('cocoon_part__tmp/css-custom', function($content) {
  $h = get_entry_content_margin_hight();
  $content = str_replace("margin-bottom: {$h}em", 'margin-bottom:' . HVN_GAP . 'px', $content);
  $content = str_replace('67.4%', 'calc(70% - var(--gap30))', $content);

  return $content;
});


//******************************************************************************
//  Swiper変更
//******************************************************************************
add_filter('cocoon_part__tmp/footer-javascript', function($content) {
  $content = str_replace('spaceBetween: 4', 'spaceBetween: ' . HVN_GAP , $content);

  return $content;
});


//******************************************************************************
//  ナビカードオートプレイ
//******************************************************************************
add_filter('cocoon_part__tmp/body-top', function($content) {
  if (get_theme_mod('hvn_swiper_auto_setting')) {
    $content = preg_replace('/(<div class="swiper-button-prev">)/', '<div class="swiper-pagination"></div>$1', $content);

    $w = new WP_HTML_Tag_Processor($content);
    while ($w->next_tag(array('class_name' => 'navi-entry-cards',))) {
      $w->add_class('is-auto-horizontal');
      $w->remove_class('is-list-horizontal');
    }
    return $w->get_updated_html();
  }

  return $content;
});


//******************************************************************************
//  カスタムJS追加
//******************************************************************************
add_action('cocoon_part_after__tmp/footer-javascript', function() {
  global $_IS_SWIPER_ENABLE;
  global $_HVN_NOTICE;

  if ((!$_IS_SWIPER_ENABLE)
   && (($_HVN_NOTICE)
    || (hvn_image_count() > 1  && get_theme_mod('hvn_header_setting') == 'image' && is_front_top_page()))) {
    echo <<< EOF
    <link rel='stylesheet' id='swiper-style-css' href='https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css' />
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

EOF;
  }

  ob_start();
  cocoon_template_part(HVN_SKIN . 'tmp/js-custom');
  $js = ob_get_clean();
  echo '<script id="hvn-custom-js">' . $js . '</script>';
});


//******************************************************************************
//  オリジナルレイアウト変更
//******************************************************************************
add_filter('cocoon_part__tmp/list-category-columns', function($content) {
  if (get_theme_mod('hvn_card_expansion_setting')) {
    if (is_entry_card_type_vertical_card_2()
     || is_entry_card_type_vertical_card_3()) {
      ob_start();
      cocoon_template_part(HVN_SKIN . 'tmp/list-category-columns');
      $content = ob_get_clean();
    }
  }

  return $content;
});


//******************************************************************************
//  カテゴリーごと間波線追加
//******************************************************************************
add_filter('cocoon_part__tmp/list', function($content) {
  $cat_ids = get_index_list_category_ids();
  if (count($cat_ids)
   && get_theme_mod('hvn_category_color_setting')
   && get_theme_mod('hvn_header_wave_setting')
   && (!is_the_page_sidebar_visible())) {
    $html = hvn_wave("hvn-wave-category");
    $content = str_replace('<div id="list-columns"', $html . '<div id="list-columns"', $content);
  }
  return $content;
});


//******************************************************************************
//  カテゴリーごと（2、3カード）縦型カード
//******************************************************************************
add_filter('index_widget_entry_card_type', function($type, $cat_id) {
  if (get_theme_mod('hvn_categories_card_setting')) {
    $type = 'large_thumb';
  }
  return $type;
}, 2, 10);


//******************************************************************************
//  SNSフォロー表示
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
//  ダークモード
//******************************************************************************
add_filter('cocoon_part__tmp/footer-bottom', function($content) {
  $html = <<<EOF
<span class="hvn-dark-switch">
  <input type="checkbox" name="hvn-dark" id="hvn-dark">
  <label for="hvn-dark"></label>
</span>
EOF;
  $content =  preg_replace('/(class="source-org copyright">.*)<\/div>/', "$1$html</div>", $content);
  return $content; 
});


//******************************************************************************
//  タイトルとURLをコピー
//******************************************************************************
add_filter('cocoon_part__tmp/sns-share-buttons', function($content) {
  $before ='/data-clipboard-text=".*" title/';
  $after = 'data-clipboard-text="&lt;a href=' . get_the_permalink() . '&gt;' . get_share_page_title() . '&lt;/a&gt;" title';
  $content = preg_replace($before, $after, $content);

  return $content;
});


//******************************************************************************
//  エントリーカードにリボンを追加
//******************************************************************************
add_action('entry_card_snippet_after',function($post_ID) {
  $memo = get_post_meta($post_ID, 'the_page_memo', true);
  preg_match('/ribbon-color-[1-5]/', $memo,$class);
  if ($class) {
    echo '<div class="ribbon ribbon-top-left ' . $class[0] . '"></div>';
  }
});


//******************************************************************************
//  SNSシェアコメントボタン表示
//******************************************************************************
add_filter('is_comment_share_button_visible', function($res, $option) {
  if (!is_single_comment_visible()) {
    $res = false;
  }
  return $res;
}, 10, 2);


//******************************************************************************
//  一覧ページに表示順フォームを追加
//******************************************************************************
add_action('cocoon_part_before__tmp/list-index', function() {
  if (defined('HVN_OPTION') && HVN_OPTION) {
    if (!is_home() || !get_theme_mod('hvn_orderby_option_setting')) return;

    $orderby = isset($_GET['orderby-switch']) ? esc_html($_GET['orderby-switch']) : null;
    if (isset($_COOKIE['orderby-switch'])) {
      $orderby = $_COOKIE['orderby-switch'];
    }

    ob_start();
?>
<div class="orderby">
  <span class="sort-title"><i class="fas fa-sort-amount-down"></i>並び替え</span>
  <span class="sort-select">
    <select id="orderby-switch" class="orderby-switch-dropdown" 
      onchange="
        var selectedValue = this.options[this.selectedIndex].value;
        var currentUrl = window.location.href;
        currentUrl = currentUrl.replace(/[&?]time=\d+/, '');

        var timestamp = new Date().getTime();
        if (currentUrl.indexOf('?') > -1) {
          currentUrl += '&time=' + timestamp;
        } else {
          currentUrl += '?time=' + timestamp;
        }
        document.cookie = selectedValue + ';path=/';
        window.document.location.href = currentUrl;
      ">
      <option value="orderby-switch="         <?php the_option_selected($orderby, '');          ?>><?php echo __('新着順', THEME_NAME) ?></option>
      <option value="orderby-switch=modified" <?php the_option_selected($orderby, 'modified');  ?>><?php echo __('更新順', THEME_NAME) ?></option>
      <option value="orderby-switch=popular"  <?php the_option_selected($orderby, 'popular');   ?>><?php echo __('人気順', THEME_NAME) ?></option>
      <option value="orderby-switch=comment"  <?php the_option_selected($orderby, 'comment');   ?>><?php echo __('コメント数順', THEME_NAME) ?></option>
    </select>
  </span>
</div>
<?php
    echo ob_get_clean();
  }
});


//******************************************************************************
//  表示順を設定
//******************************************************************************
add_action('pre_get_posts',function($query) {
  // 一覧ページのみ並び替え
  if (is_admin() || !is_home() || !$query->is_main_query()) {
    return;
  }

  // cooki更新
  $ck = isset($_COOKIE['orderby-switch']) ? $_COOKIE['orderby-switch'] : null;
  if (isset($_GET['orderby-switch'])) {
    setcookie('orderby-switch', esc_html($_GET['orderby-switch']), time() + 60 * 60 * 24,'/');
  }

  // 順序設定
  $gt = isset($_GET['orderby-switch']) ? $_GET['orderby-switch'] : null;
  $st = empty($ck) ? $gt : $ck;

  switch($st) {
    // 人気順
    case 'popular':
      $records = get_access_ranking_records('all', 3000, 'post');
      $post_ids = array();
      foreach ($records as $post) {
        $post_ids[] = $post->ID;
      }
      $query->set('post__in', $post_ids);
      $query->set('orderby', 'post__in');
      break;

    // コメント数
    case 'comment':
      $query->set( 'orderby', array(
        'comment_count' => 'DESC',
        'date'          => 'DESC'
      ));
      break;

    default:
      $query->set('orderby', $st);
  }
});


//******************************************************************************
//  目次ボタン追加
//******************************************************************************
add_action('cocoon_part_after__tmp/button-go-to-top', function() {
  if (get_theme_mod('hvn_toc_fix_setting')) {
    $html = do_shortcode('[toc]');
    $title = __('目次', THEME_NAME);
    echo <<< EOF
<div id="hvn-toc">
  <label for="hvn-open" class="hvn-open-btn"><i class="fas fa-list"></i></label>
  <input type="radio" id="hvn-close" class="display-none" name="hvn-trigger">
  <input type="radio" id="hvn-open"  class="display-none" name="hvn-trigger">
  <div class="hvn-modal">
    <div class="hvn-content-wrap">
      <div class="hvn-title">{$title}</div>
      {$html}
    </div>
    <label for="hvn-close"><div class="hvn-background"></div></label>
  </div>
</div>

EOF;
  }
});


//******************************************************************************
//  通知エリア更新
//******************************************************************************
add_filter('get_notice_area_message', function($msg) {
  global $_HVN_NOTICE;
  global $_THEME_OPTIONS;

  $_HVN_NOTICE = false;

  $msg = stripslashes_deep(get_theme_option(OP_NOTICE_AREA_MESSAGE));

  if (!is_admin()) {
    if (strpos($msg, '[pattern ') !== false) {
      $msg = do_shortcode($msg);
    }

    $msg = apply_filters('hvn_notice_message', $msg);

    // メッセージにリンク含む場合、通知URL無効
    if (strpos($msg, 'href=') !== false) {
      $_THEME_OPTIONS['notice_area_url'] = '';
    }

    // 改行コード除去
    $msg = preg_replace('/\r\n|\n|\r/', '', $msg);
    $msg_array =  explode(',', $msg);

    // 空メッセージ除去
    $msg_array = array_filter($msg_array, function($value) {
      return $value !== '';
    });
    $msg_array = array_values($msg_array);

    if (count($msg_array) > 1) {
      if (is_notice_area_visible()) {
        $_HVN_NOTICE = true;
      }
    }

    $html = '';
    for ($i=0; $i<count($msg_array); $i++) {
      $html .= "<div class=swiper-slide>{$msg_array[$i]}</div>";
    }

    if ($html) {
      $msg = "<div class=swiper><div class=swiper-wrapper>{$html}</div></div>";
    } else {
      $msg = '';
    }
  }

  return $msg;
});
