<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー追加
//******************************************************************************
add_action('customize_register', function($wp_customize) {
  $wp_customize->add_panel(
    'hvn_cocoon',
    [
      'title'     => __('メイド・イン・ヘブン設定', THEME_NAME),
      'priority'  => 300,
    ]
  );

  hvn_color($wp_customize);
  hvn_main($wp_customize);
  hvn_header($wp_customize);
  hvn_option($wp_customize);
  hvn_editor($wp_customize);
});


//******************************************************************************
//  adminバー変更
//******************************************************************************
add_action('admin_bar_menu', function($wp_admin_bar) {
  $nodes = [
    'my-account',
    'wp-logo',
    'search',
  ];

  foreach ($nodes as $node) {
    $wp_admin_bar->remove_menu($node);
  }
}, 10000);


//******************************************************************************
//  リンクマネージャ非表示
//******************************************************************************
add_action('after_setup_theme', function(){
  remove_filter('pre_option_link_manager_enabled', '__return_true');
});


//******************************************************************************
//  ダッシュボード投稿・固定ページ一覧を24時間表示
//******************************************************************************
add_filter('post_date_column_time', function($h_time, $post) {
  return get_the_date('Y-m-d H:i');
}, 10, 2);


//******************************************************************************
//  ダッシュボード固定ページ一覧にカラム追加
//******************************************************************************
add_filter('manage_pages_columns', function($columns) {
  $columns['slug'] = __('Slug');
  return $columns;
});


// カラムにデータ出力
add_action('manage_pages_custom_column', 'hvn_custom_columns_content', 10, 2);


//******************************************************************************
//  ダッシュボード投稿一覧にカラム追加
//******************************************************************************
add_filter('manage_post_posts_columns', function($columns) {
  $columns['slug'] = __('Slug');
  $columns['last_modified'] = __('更新日', THEME_NAME);
  $columns['the_page_meta_description'] = __('メタディスクリプション', THEME_NAME);

  return $columns;
});


// 「更新日付」カラムにソート追加
add_filter('manage_edit-post_sortable_columns', function($columns) {
  $columns['last_modified'] = 'modified';

  return $columns;
});


// カラムにデータ出力
add_action('manage_posts_custom_column', 'hvn_custom_columns_content', 10, 2);


//******************************************************************************
// タクソノミー一覧にカラム追加
//******************************************************************************
add_action('admin_init', function() {

  // 全タクソノミー取得
  $taxonomies = get_taxonomies([], 'names');

  foreach ($taxonomies as $taxonomy) {
    // 「アイキャッチ」カラムを追加
    add_filter("manage_edit-{$taxonomy}_columns", function($columns) {
      $columns['thumbnail'] = __('アイキャッチ', THEME_NAME);
      return $columns;
    });

    // 「アイキャッチ」カラムにデータ出力
    add_filter("manage_{$taxonomy}_custom_column", function($content, $column_name, $term_id) {
      switch($column_name) {
        case 'thumbnail':
          $url = get_the_category_eye_catch_url($term_id);
          if (!$url) {
            $url = get_the_tag_eye_catch_url($term_id);
          }
          if ($url) {
            echo '<img src="' . esc_url($url) . '" style="width:75px; height:auto;">';
          } else {
            echo '';
          }
          break;
      }
      return $content;
    }, 10, 3);
  }
});


//******************************************************************************
//  更新日クリア
//******************************************************************************
add_action('admin_post_delete_date', function() {
  global $wpdb;

  // 最終更新日=投稿日に更新
  $id = $_REQUEST['id'];
  $wpdb->query("UPDATE $wpdb->posts SET post_modified=post_date, post_modified_gmt=post_date_gmt WHERE ID=$id");

  // 元ページにリダイレクト
  $url = $_SERVER['HTTP_REFERER'];
  wp_redirect($url);
  exit;
});


//******************************************************************************
//  クイック編集入力フォーム追加
//******************************************************************************
// クイック編集入力フォーム追加
add_action('quick_edit_custom_box', function($column_name, $post_type) {
  static $print_nonce = TRUE;
  if ($print_nonce) {
    $print_nonce = FALSE;
    wp_nonce_field('quick_edit_action', $post_type . '_edit_nonce');
  }

  switch($column_name) {
    case 'the_page_meta_description':
      ?>
      <fieldset class="inline-edit-col-right inline-custom-meta">
        <?php if (get_theme_mod('admin_list_memo_visible')) : ?>
          <div class="inline-edit-col column-column-memo">
            <label class="inline-edit-group">
              <span class="title"><?php echo __('メモ', THEME_NAME) ?></span>
              <textarea name="the_page_memo"></textarea>
            </label>
          </div>
        <?php endif; ?>

        <div class="inline-edit-col column-the_page_meta_description">
          <label class="inline-edit-group">
            <span class="title"><?php echo __('メタディスクリプション', THEME_NAME) ?></span>
            <span class="str-count"><?php echo __('文字数', THEME_NAME) ?>:<span class="meta-description-count">0</span></span>
            <textarea name="the_page_meta_description"></textarea>
          </label>
        </div>
      </fieldset>
      <?php
      break;
  }
}, 10, 2);


// クイック編集値表示
add_action('admin_footer-edit.php', function() {
  global $post_type;
  $slug = 'post';
  if ($post_type == $slug) {
?>
<script>
jQuery(function($) {
  var $wp_inline_edit = inlineEditPost.edit;

  inlineEditPost.edit = function(id) {
    $wp_inline_edit.apply(this, arguments);

    var $post_id = 0;
    if (typeof(id) == 'object') {
      $post_id = parseInt(this.getId(id));
    }

    if ($post_id > 0) {
      var $edit_row = $('#edit-' + $post_id);
      var $post_row = $('#post-' + $post_id);

      // メモ
      var $memo = $('.column-memo', $post_row).html();
      $(':input[name="the_page_memo"]', $edit_row).val($memo)

      // ディスクリプション
      $elm = $(':input[name="the_page_meta_description"]', $edit_row);

      var $the_page_meta_description = $('.column-the_page_meta_description', $post_row).text();
      $elm.val($the_page_meta_description);

      // 文字数表示
      $count = $elm.val().length;
      $('.meta-description-count', $edit_row).text($count);
      $elm.bind("keydown keyup keypress change", function() {
        var $count = $(this).val().length;
        $('.meta-description-count', $edit_row).text($count);
      });
    }
  };
  
});
</script>
<?php
  }
});


//  クイック編集カスタムフィールド更新
add_action('save_post', function($post_id) {
  $slug = 'post';

  if ($slug !== get_post_type($post_id)) {
    return;
  }
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  $_POST += ["{$slug}_edit_nonce" => ''];
  if (!wp_verify_nonce($_POST["{$slug}_edit_nonce"], 'quick_edit_action')) {
    return;
  }

  if (isset($_REQUEST['the_page_memo'])) {
    update_post_meta( $post_id, 'the_page_memo', $_REQUEST['the_page_memo']);
  }

  if (isset($_REQUEST['the_page_meta_description'])) {
    update_post_meta( $post_id, 'the_page_meta_description', $_REQUEST['the_page_meta_description']);
  }
});


//******************************************************************************
//  メインクエリの制御（表示件数・検索・並び替え）
//******************************************************************************
add_action('pre_get_posts', function($query) {
  // フロントエンド側の処理
  if (!is_admin() && $query->is_main_query()) {
    // 表示件数の変更
    $query->set('posts_per_page', 12);


    // 空欄検索時の処理
    if ($query->is_search()) {
      if (empty(trim(get_search_query()))) {
        $query->set('post__in', [0]);
      }
    }

    // 更新記事「もっと見る」
    if (isset($_GET['orderby']) && $_GET['orderby'] === 'modified') {
      $query->set('orderby', ['modified' => 'DESC']);
    }
  }

  // 管理画面側の処理
  if (is_admin() && $query->is_main_query()) {

    // 投稿一覧の人気順並び替え
    if (!empty($_GET['pv_range'])) {

      $days = ($_GET['pv_range'] === 'all') ? 'all' : (int) $_GET['pv_range'];
      $records = get_access_ranking_records($days, -1, 'post');

      $popular_ids = [];
      if (!empty($records)) {
        $popular_ids = array_map('intval', wp_list_pluck($records, 'ID'));
      }

      // 公開済みの全投稿IDを取得
      $published_ids = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'orderby'        => 'date',
        'order'          => 'DESC',
      ]);

      // 人気記事を先頭にしてマージ
      $remaining_ids = array_diff($published_ids, $popular_ids);
      $merged_ids = array_merge($popular_ids, $remaining_ids);

      if (!empty($merged_ids)) {
        $query->set('post__in', $merged_ids);
        $query->set('orderby', 'post__in');
        $query->set('post_status', 'publish');
      }
    }
  }
});


//******************************************************************************
//  検索結果を投稿日順
//******************************************************************************
add_filter('posts_search_orderby', function($search_orderby, $wp_query) {
  return 'post_date desc';
}, 10, 2);


//******************************************************************************
//  ウィジェットタイトルHTML入力
//******************************************************************************
add_filter('widget_title', function($title) {
  return str_replace(
    ['[', ']', '&#8216;', '&#8217;', '&#8221;', '&#8220;'],
    ['<', '>', "'"      , "'"       , '"'     , '"'],
    (string) $title
  );
});


//******************************************************************************
//  テーマ初期化処理
//******************************************************************************
add_action('init', function() {

  // Gutenberg独自スタイル追加
  $block_styles = [
    // タイムライン
    ['name' => 'cocoon-blocks/timeline', 'id' => 'hvn-timeline-mini', 'label' => 'ミニ'],
    ['name' => 'cocoon-blocks/timeline', 'id' => 'hvn-timeline-line', 'label' => 'ライン'],
    ['name' => 'cocoon-blocks/timeline', 'id' => 'hvn-timeline-step', 'label' => 'ステップ'],
    ['name' => 'cocoon-blocks/timeline', 'id' => 'hvn-timeline-big',  'label' => 'ビッグ'],
    ['name' => 'cocoon-blocks/timeline', 'id' => 'hvn-timeline-box',  'label' => 'ボックス'],
    
    // タブ
    ['name' => 'cocoon-blocks/tab', 'id' => 'hvn-tab-balloon', 'label' => '吹き出し'],
    ['name' => 'cocoon-blocks/tab', 'id' => 'hvn-tab-line',    'label' => '下線'],

    // ブログカード
    ['name' => 'cocoon-blocks/blogcard', 'id' => 'hvn-text', 'label' => 'テキスト'],

    // 見出しボックス
    ['name' => 'cocoon-blocks/caption-box-1', 'id' => 'accordion', 'label' => 'アコーディオン'],

    // 新着記事
    ['name' => 'cocoon-blocks/new-list', 'id' => '2-columns', 'label' => '2カラム'],
    ['name' => 'cocoon-blocks/new-list', 'id' => '3-columns', 'label' => '3カラム'],

    // 人気記事
    ['name' => 'cocoon-blocks/popular-list', 'id' => '2-columns', 'label' => '2カラム'],
    ['name' => 'cocoon-blocks/popular-list', 'id' => '3-columns', 'label' => '3カラム'],

    // ナビカード
    ['name' => 'cocoon-blocks/navicard', 'id' => '2-columns', 'label' => '2カラム'],
    ['name' => 'cocoon-blocks/navicard', 'id' => '3-columns', 'label' => '3カラム'],
  ];

  // スタイルの登録実行
  foreach ($block_styles as $style) {
    register_block_style($style['name'], [
      'name'  => $style['id'],
      'label' => __($style['label'], THEME_NAME),
    ]);
  }

  // 独自パターンの登録
  $pattern_dir = url_to_local(HVN_SKIN_URL) . "assets/pattern/";
  $files = glob($pattern_dir . "*.json");

  if ($files) {
    register_block_pattern_category('heaven', ['label' => __('メイド・イン・ヘブン', THEME_NAME)]);

    foreach ($files as $file) {
      $json_data = file_get_contents($file);
      $pattern   = json_decode($json_data, true);

      if ($pattern) {
        $slug = basename($file, '.json');
        register_block_pattern("heaven/{$slug}", $pattern);
      }
    }
  }

  // 回転テキスト」ブロックの登録
  register_block_type('hvn-plugin/hvn-circular-text-block', [
    'title' => __('回転テキスト', THEME_NAME),
    'icon'  => 'update',
    'category'    => 'hvn-block',
    'attributes'  => [
      'image_url'   => ['type' => 'string'  , 'label' => __('画像URL'       , THEME_NAME), 'default' => ''],
      'main_text'   => ['type' => 'string'  , 'label' => __('テキスト'      , THEME_NAME), 'default' => 'HEAVEN'],
      'text_color'  => ['type' => 'string'  , 'label' => __('テキストカラー', THEME_NAME), 'default' => '#333333'],
      'font_size'   => ['type' => 'number'  , 'label' => __('テキストサイズ', THEME_NAME), 'default' => 16],
      'font_weight' => ['type' => 'boolean' , 'label' => __('太字'          , THEME_NAME), 'default' => false],
    ],
    'supports' => [
      'autoRegister' => true,
    ],
    'render_callback' => 'hvn_render_circular_text_block',
  ]);

  // 通知エリア用メニューの位置追加
  register_nav_menus([
    'hvn-notice-menu' => __('通知エリア', THEME_NAME),
  ]);
});


//******************************************************************************
//  管理画面追加
//******************************************************************************
add_action('admin_enqueue_scripts', function() {
  if (!get_theme_mod('hvn_admin_css_setting')) {
    wp_enqueue_style('hvn-admin', HVN_SKIN_URL . 'assets/css/admin.css');
  }
});


add_action('admin_footer', function() {
  // Cocoon設定画面以外の場合
  $screen = get_current_screen();
  if (strpos($screen->id, 'theme-settings') === false) return;

  $link_html = sprintf(
    '<a href="%s" class="hvn-custom-link" style="margin-left:1em"><i class="fa fa-wrench"></i> %s</a>',
    esc_url(admin_url('customize.php?autofocus[panel]=hvn_cocoon')),
    esc_html__('スキンの詳細設定はこちら', THEME_NAME)
  );

  ?>
  <script id="hvn-menu-link">
  jQuery(function($) {
    $(function() {
      const linkHtml = <?php echo wp_json_encode($link_html); ?>;
      const $target = $('#tab-skin-content input[value*="skin-made-in-heaven"]:checked');

      const $label = $('label[for="' + $target.attr('id') + '"]');

      // スキンリンクが未追加の場合
      if ($target.length && !$label.find('.hvn-custom-link').length) {
        $label.append(linkHtml);
      }
    });
  });
  </script>

  <?php
}, 999);


//******************************************************************************
//  GutenbergエディターCSS追加
//******************************************************************************
add_action('enqueue_block_assets', function() {
  if (!is_admin()) return;

  hvn_h2_h4_css(['cocoon-skin-style']);
}, 999);


//******************************************************************************
//  カスタマイザーCSS追加
//******************************************************************************
add_action('customize_controls_enqueue_scripts', function() {
  wp_enqueue_style('hvn-custom', HVN_SKIN_URL . '/assets/css/customize.css' );
});


//******************************************************************************
//  ローディング画面、目次ボタン追加
//******************************************************************************
add_action('wp_body_open', function() {
  $load = get_theme_mod('hvn_front_loading_setting', 'none');
  if (is_front_top_page() && $load != 'none') {
    cocoon_template_part(HVN_SKIN . 'tmp/load/' . $load);
  }

  if (!get_theme_mod('hvn_toc_fix_setting')) return;

  $html = do_shortcode('[toc]');

  if (!$html) return;

  $title = __('目次', THEME_NAME);

  echo <<<EOF
<a href="#hvn-toc" class="hvn-open-btn">
  <i class="fas fa-list"></i>
</a>

<div id="hvn-toc" class="hvn-modal">
  <a href="#-" class="hvn-background"></a>
  <div class="hvn-content-wrap">
    <div class="hvn-title">{$title}</div>
    {$html}
  </div>
</div>

EOF;
});


//******************************************************************************
//  オプション更新
//******************************************************************************
add_action('wp_head', function() {
  global $_THEME_OPTIONS;

  // ダークモード設定
  if (get_theme_mod('hvn_darkmode_setting')) {
    ?>
<script>
(function() {
  if (localStorage.getItem('hvn-dark') === 'dark') {
    document.documentElement.classList.add('hvn-dark');
  }
})();
</script>
    <?php
  }


  // サイト開設年
  $yymmdd = get_theme_mod('hvn_site_date_setting');
  if ($yymmdd) {
    list($yy, $mm, $dd) = explode('-', $yymmdd);
    $_THEME_OPTIONS['site_initiation_year'] = $yy;
  }

  // エントリーカードコメント数を表示
  $_THEME_OPTIONS['entry_card_post_comment_count_visible'] = get_theme_mod('single_comment_visible');


  $_THEME_OPTIONS['front_page_type'] = get_theme_mod('front_page_type', 'index');
  $_THEME_OPTIONS['entry_card_type'] = get_theme_mod('entry_card_type', 'entry_card');

  // サイドバー変更
  if ((get_entry_card_type() == 'vertical_card_3')
   || (get_front_page_type() == 'category_3_columns')) {
      $_THEME_OPTIONS['sidebar_display_type'] = 'no_display_index_pages';
  } else {
    // 3列解除
    if (get_entry_card_type() == 'vertical_card_3') {
      remove_theme_mod('entry_card_type');
    }
    if (get_front_page_type() == 'category_3_columns') {
      remove_theme_mod('front_page_type');
    }
  }

  switch(get_entry_card_type()) {
    case 'tile_card_2':
    case 'tile_card_3':
      // タイルカード無効
      remove_theme_mod('entry_card_type');
      $_THEME_OPTIONS['entry_card_type'] = 'entry_card';
      break;

    case 'big_card':
      if (get_theme_mod('hvn_card_expansion_setting')) {
        $_THEME_OPTIONS['entry_card_snippet_visible'] = 1;
      }
      break;

    case 'big_card_first':
    case 'vertical_card_2':
      if (get_theme_mod('hvn_card_expansion_setting') &&
          strpos(get_front_page_type(), 'category') !== false
      ) {
        // 新着記事数変更
        $_THEME_OPTIONS['index_new_entry_card_count'] = 5;

        // カテゴリーごと記事数変更
        $_THEME_OPTIONS['index_category_entry_card_count'] = 5;
      }
      break;
  }
}, 999);


//******************************************************************************
//  エントリーカードいいねボタン
//******************************************************************************
add_action('wp_ajax_hvn_like_action', 'hvn_like_ajax');
add_action('wp_ajax_nopriv_hvn_like_action', 'hvn_like_ajax');


//******************************************************************************
//  日付形式変更
//******************************************************************************
// アーカイブ日付をY-m形式変更
add_filter('get_archives_link', function($html, $url, $text, $format, $before, $after, $selected) {
  $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
  $is_widget = false;

  foreach ($backtrace as $step) {
    if (isset($step['class']) && $step['class'] === 'WP_Widget_Archives') {
      $is_widget = true;
      break;
    }
  }

  // 呼び出し元が「アーカイブ」ウィジェットの場合
  if ($is_widget) {
    $html = preg_replace_callback('/(\d+)年(\d+)月/', function($matches) {
      return $matches[1] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT);
    }, $html);
  }

  return $html;
}, 10, 7);


//  カレンダー日付をY-m形式変更
add_filter('get_calendar', function($html) {
  $html = preg_replace_callback('/\s?(\d+)日\s?/', function($matches) {
    return '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
  }, $html);

  $html = preg_replace_callback('/\s?(\d+)月\s?/', function($matches) {
    return str_pad($matches[1], 2, '0', STR_PAD_LEFT);
  }, $html);

  $html = preg_replace('/(\d{4})年/', '$1-', $html);

  $maps = [
    '&laquo;' => '<i class="fas fa-angle-left"></i>',
    '&raquo;' => '<i class="fas fa-angle-right"></i>',
  ];
  $html = str_replace(array_keys($maps), array_values($maps), $html);

  return $html;
});


//  RSS日付Y-m-d形式変更
add_filter('option_date_format', function($option){
  if (!is_admin()) {
    $option = 'Y-m-d';
  }

  return $option;
});


add_filter('option_time_format', function($option){
  if (!is_admin()) {
    $option = 'H:i';
  }

  return $option;
});


//******************************************************************************
//  NEWマーク、リボン追加
//******************************************************************************
add_filter('post_class', function($classes, $class, $post_id) {

  $memo = get_post_meta($post_id, 'the_page_memo', true);
  preg_match('/ribbon-color-[1-5]/', $memo, $class);
  if ($class) {
    $classes[] = $class[0];
  }

  $days = get_theme_mod('hvn_index_new_setting');
  if ($days == 0) {
    return $classes;
  }

  $now = time();
  $last_threshold = $now - ($days * DAY_IN_SECONDS);

  $post_time = get_post_time('U', true, $post_id);
  $mod_time  = get_post_modified_time('U', true, $post_id);

  if ($post_time > $last_threshold) {
    $classes[] = 'new-post';
  } elseif ($mod_time > $last_threshold) {
    $classes[] = 'up-post';
  }

  return $classes;
}, 10, 3);


//******************************************************************************
//  モバイルメニュー見出し追加
//******************************************************************************
add_filter('wp_nav_menu', function($nav_menu, $args) {
  if ((($args->theme_location == NAV_MENU_HEADER)
   ||  ($args->theme_location == NAV_MENU_MOBILE_SLIDE_IN))
   && (strpos($args->menu_class, 'menu-drawer') !== false)) {
    $html = get_theme_mod('hvn_mobile_text_setting', __('メニュー', THEME_NAME));
    $nav_menu = '<aside class="widget"><h3 class="widget-title">' . $html . '</h3>' . $nav_menu  . '</aside>';
  }

  return $nav_menu;
}, 10, 2);


//******************************************************************************
//  コメントアイコン追加
//******************************************************************************
// コメントフォーム追加
add_filter('comment_form_field_comment', function($content) {
  $icon = 3;
  $html = null;

  if (get_theme_mod('hvn_comment_setting') && is_user_logged_in()){
    for ($i=1; $i<=$icon; $i++) {
      $checked = ($i == 1) ? 'checked' : null;
      $img = get_theme_mod("hvn_comment_img{$i}_setting");
      if ($img) {
        $url = wp_get_attachment_url($img);
        $html .= <<<EOF
<div class="hvn-comment-icon">
  <figure><img src="{$url}"></figure>
  <input type="radio" name="post-icon" value="{$i}" {$checked}>
</div>

EOF;
      }
    }
    if ($html) {
      $html = "<label>" . __('アイコン', THEME_NAME) . "</label><div class=hvn-comment>{$html}</div>";
    }
  }

  return $html . $content;
});


// カスタムフィールド出力
add_action('comment_post', function($comment_id) {
  if (get_theme_mod('hvn_comment_setting') && is_user_logged_in()) {
    $post_icon = esc_attr($_POST['post-icon']);
    add_comment_meta($comment_id, 'post-icon', $post_icon, true);
  }
});


// コメントカスタムフィールド追加
add_action('add_meta_boxes_comment', function() {
 add_meta_box('hvn-comment-title', __('カスタムフィールド', THEME_NAME), 'hvn_comment_meta_post_icon', 'comment', 'normal', 'high');
});


// コメントを編集カスタムフィールド更新
add_action('edit_comment', function($comment_id) {
  if (isset($_POST['post-icon'])) {
    update_comment_meta($comment_id, 'post-icon', esc_attr($_POST['post-icon']));
  }
});


// コメント一覧にカスタムフィールド追加
add_filter('manage_edit-comments_columns', function($columns) {
  $columns['post-icon'] = __('アイコン番号', THEME_NAME);

  return $columns;
});


// コメント一覧にアイコンカラム追加
add_action('manage_comments_custom_column', function($column_name, $comment_id) {
  if ($column_name == 'post-icon') {
    $post_icon = get_comment_meta($comment_id, 'post-icon', true);
    echo esc_attr($post_icon);
  }
},10, 2);


// アバター変更
add_filter('get_avatar' , function($avatar, $comment) {
  if (get_theme_mod('hvn_comment_setting')) {
    if (!is_admin() && isset($comment->comment_ID)) {
      $no = get_comment_meta($comment->comment_ID, 'post-icon',true);
      if ($no) {
        $img = wp_get_attachment_url(get_theme_mod("hvn_comment_img{$no}_setting"));
        if ($img) {
          $avatar = "<img src={$img} class=avatar>";
        }
      }
    }
  }

  return $avatar;
}, 100001, 2);


//******************************************************************************
//  「タグクラウド」ウィジェットオプション追加
//******************************************************************************
add_action('in_widget_form', function($widget, $return, $instance) {
  if ($widget->id_base == 'tag_cloud') {
    $f_id   = $widget->get_field_id('drop');
    $f_name = $widget->get_field_name('drop');
    echo "<p><input type=checkbox class=widefat name={$f_name}" .  checked(isset($instance['drop']) && $instance['drop'] != '', true, false) . "><label for={$f_id}>ドロップダウンで表示</label></p>";
  }
}, 10, 3);


// 「タグクラウド」ウィジェット設定フォーム更新
add_filter('widget_update_callback', function($instance, $new_instance, $old_instance, $this_widget) {
  if ($this_widget->id_base == 'tag_cloud') {
    $instance['drop'] = ! empty( $new_instance['drop']) ? $new_instance['drop'] : '';
  }

  return $instance;
}, 10, 4);


// 「タグクラウド」ウィジェット設定
add_filter('widget_tag_cloud_args', function($args, $instance) {
  $args['drop'] = isset($instance['drop']) ? $instance['drop'] : '';

  return $args;
}, 2, 10);


// タグクラウド独自表示
add_filter('wp_tag_cloud', function($return, $args) {
  if (isset($args['drop']) && $args['drop'] == 'on'){
    $id = get_query_var('tag_id');
    $tags = get_tags(['orderby'=> 'count', 'order' => 'DESC']);

    ob_start();
    echo '<select aria-label="' . __('選択', THEME_NAME) . '" onchange="document.location.href=this.options[this.selectedIndex].value;"><option value="" selected="selected">タグを選択</option>';

    if ($tags) {
      foreach($tags as $tag) {
        $count = $args["show_count"] ? " &nbsp;({$tag->count})" : '';
?>
<option value="<?php echo get_tag_link($tag->term_id); ?>" <?php selected($tag->term_id, $id); ?>><?php echo $tag->name; ?><?php echo $count; ?></option>
<?php
      }
    }
    echo '</select>';
    $return = ob_get_clean();
  }

  return $return;
}, 2, 10);


//******************************************************************************
//  ブロック変更
//******************************************************************************
add_filter('render_block', function ($block_content, $block) {
  if (!is_singular()) {
    return $block_content;
  }

  // インラインボタンのデザイン変更
  if (strpos($block_content, 'inline-button') !== false) {
    $btn_circle = get_theme_mod('hvn_inline_button_set1_setting') ? 'btn-circle' : '';
    $btn_shine  = get_theme_mod('hvn_inline_button_set2_setting') ? 'btn-shine'  : '';

    if ($btn_circle || $btn_shine) {
      $block_content = preg_replace(
        '/class="([^"]*\binline-button\b[^"]*)"/',
        'class="' . trim("$btn_circle $btn_shine") . ' $1"',
        $block_content
      );
    }
  }

  // カスタム属性を取得
  $h_tag = $block['attrs']['hvnHeadingTag'] ?? '';

  // H2〜H6の範囲内である場合のみ一括置換
  if ($h_tag && preg_match('/^h[2-6]$/', $h_tag)) {
    $pattern = '/<div\s+(class="(?:faq-question-content faq-item-content|timeline-item-title)")>(.*?)<\/div>/s';
    $block_content = preg_replace($pattern, '<' . $h_tag . ' $1>$2</' . $h_tag . '>', $block_content);
  }

  return $block_content;
}, 10, 2);


//******************************************************************************
//  プロフィールリンク変更
//******************************************************************************
add_filter('the_author_box_name', function($name, $id) {
  $url = get_the_author_profile_page_url($id);
  if (!$url) {
    $name = strip_tags($name);
  }

  return $name;
}, 10, 2);


//******************************************************************************
//  リダイレクト・ページ制御
//******************************************************************************
add_action('template_redirect', function() {
  //  著者アーカイブを無効化してトップページへリダイレクト
  if (is_author() || (isset($_GET['author']) && is_numeric($_GET['author']))) {
    wp_safe_redirect(home_url(), 301);
    exit;
  }


  // 分割ページの見出し（H2）カウント処理
  if (!is_singular() || !is_multi_page_toc_visible()) return;
  global $post;

  setup_postdata($post);
  global $page, $pages;

  // 2ページ目以降かつ分割データがある場合
  if (isset($page) && $page > 1 && is_array($pages)) {
    $total_h2 = 0;

    for ($i = 0; $i < $page - 1; $i++) {
      if (isset($pages[$i])) {
        // H2見出しをカウント
        $total_h2 += preg_match_all('/<h2/i', $pages[$i], $matches);
      }
    }

    $GLOBALS['hvn_h2_count'] = $total_h2;
  }
});


//******************************************************************************
//  エディター用CSSファイルキャッシュ保存
//******************************************************************************
add_action('customize_save_after', function() {
  ob_start();
  cocoon_template_part(HVN_SKIN . 'tmp/css-editor');
  $custom_css = ob_get_clean();
  if ($custom_css) {
    wp_filesystem_put_contents(hvn_editor_css_cache_file(), $custom_css);
  }
});


//******************************************************************************
//  フォント削除に連携しオプション削除
//******************************************************************************
add_action('before_delete_post', function($post_id) {
  if (get_post_type($post_id) !== 'wp_font_family') return;

  $font = get_post($post_id);

  if (get_theme_mod('hvn_font_setting') === $font->post_title) {
    set_theme_mod('hvn_font_setting', '');
  }
});


//******************************************************************************
//  本文先頭に目次追加
//******************************************************************************
add_filter('the_content', function($content) {
  global $_THEME_OPTIONS;

  if (get_theme_mod('hvn_toc_top_setting') && is_total_the_page_toc_visible()) {
    $content = do_shortcode('[toc]') . $content;
    $_THEME_OPTIONS['toc_visible'] = 0;
  }
  return $content;
});


//******************************************************************************
//  投稿一覧にPVフィルター追加
//******************************************************************************
add_action('restrict_manage_posts', function() {
  global $typenow;

  // テーマ独自アクセス集計でPV表示
  if ($typenow !== 'post' || !is_access_count_enable() || !is_admin_list_pv_visible()) return;

  $current = $_GET['pv_range'] ?? '';
  ?>
  <select name="pv_range">
    <option value=""    <?php selected($current, ''); ?>><?php echo esc_html(__('人気記事', THEME_NAME)); ?></option>
    <option value="1"   <?php selected($current, '1'); ?>><?php echo esc_html(__('本日', THEME_NAME)); ?></option>
    <option value="7"   <?php selected($current, '7'); ?>><?php echo esc_html(__('今週', THEME_NAME)); ?></option>
    <option value="30"  <?php selected($current, '30'); ?>><?php echo esc_html(__('今月', THEME_NAME)); ?></option>
    <option value="all" <?php selected($current, 'all'); ?>><?php echo esc_html(__('全期間', THEME_NAME)); ?></option>
  </select>
  <?php
});


//******************************************************************************
// 「画像」ブロックに拡大効果リンク設定
//******************************************************************************
add_filter('render_block_core/image', function($block_content, $block) {
  // 既にリンクがある場合
  if (strpos($block_content, '<a ') !== false) {
    return $block_content;
  }

  // 添付ファイルページへのリンクが有効の場合
  if (isset($block['attrs']['linkDestination']) && $block['attrs']['linkDestination'] !== 'none') {
    return $block_content;
  }

  // WordPress標準のライトボックスが有効の場合
  if (isset($block['attrs']['lightbox']['enabled']) && $block['attrs']['lightbox']['enabled'] === true) {
    return $block_content;
  }

  // 拡大効果が有効でない場合
  if (get_image_zoom_effect() === 'none') {
    return $block_content;
  }

  // 画像URLを取得
  $image_url = wp_get_attachment_url($block['attrs']['id']);
  if (!$image_url) {
    return $block_content;
  }

  // リンクを追加
  $pattern = '/(<img[^>]+>)/i';
  $replacement = '<a href="' . esc_url($image_url) . '">$1</a>';
  $block_content = preg_replace($pattern, $replacement, $block_content);

  return $block_content;
}, 10, 2);


//******************************************************************************
//  コメント数で絞り込み
//******************************************************************************
add_filter('posts_where', function($where, $query) {
  if ($query->get('hvn_comments')) {
    global $wpdb;
    $where .= " AND {$wpdb->posts}.comment_count > 0";
  }

  return $where;
}, 10, 2);


//******************************************************************************
//  アーカイブをアコーディオン表示
//******************************************************************************
add_filter('widget_display_callback', function($instance, $widget, $args) {
  if ($widget->id_base !== 'archives' || !get_theme_mod('hvn_accordion_setting')) return $instance;

  // 投稿数を表示
  $is_count_enabled = !empty($instance['count']);

  // ウィジェットの出力をバッファリングして変数に取得
  $instance['echo'] = 0;
  ob_start();
  $widget->widget($args, $instance);
  $html = ob_get_clean();

  // HTMLから各月のリンク（URL、年、月、テキスト）を抽出
  preg_match_all('/<li><a href=\'(.+?\/(\d{4})\/(\d{2})\/)\'.*?>(.*?)<\/a><\/li>/s', $html, $matches, PREG_SET_ORDER);

  if (!empty($matches)) {
    $groups = [];

    foreach ($matches as $match) {
      $url  = $match[1];        // アーカイブURL
      $year = $match[2];        // 年
      $inner_html = $match[4];  // リンクタグ内のHTML

      $count = 0;

      if ($is_count_enabled && preg_match('/class="post-count">(\d+)<\/span>/', $inner_html, $count_match)) {
        $count = (int)$count_match[1];
      }

      // 子リスト（月）に整形
      $month_inner = preg_replace('/\d{4}-/', '', $inner_html);

      // 年をキーとした配列に月のリンクを格納し、合計投稿数を加算
      $groups[$year]['months'][] = "<li><a href='{$url}'>{$month_inner}</a></li>";
      $groups[$year]['total'] = ($groups[$year]['total'] ?? 0) + $count;
    }

    $new_list = '<ul>';
    foreach ($groups as $year => $data) {
      // 合計投稿数のHTML生成
      $count_html = $is_count_enabled ? '<span class="post-count">' . $data['total'] . '</span>' : '';

      // 親（年）のリスト項目を生成
      $new_list .= '<li class="archive-year-parent">';
      $new_list .= '<a><span>' . $year . '</span>' . $count_html . '</a>';

      // 子（月）のリストをを生成
      $new_list .= '<ul class="children" style="display:none;">';
      $new_list .= implode('', $data['months']);
      $new_list .= '</ul>';
      $new_list .= '</li>';
    }
    $new_list .= '</ul>';

    $html = preg_replace('/<ul.*?>.*?<\/ul>/s', $new_list, $html);
  }

  echo $html;
  return false;
}, 10, 3);


//******************************************************************************
//  Gutenbergエディター用JS追加
//******************************************************************************
add_action('enqueue_block_editor_assets', function () {
  $js_url = HVN_SKIN_URL . 'assets/js/block-extensions.js';
  wp_enqueue_script('hvn-block-extensions', $js_url, ['wp-blocks','wp-element','wp-block-editor','wp-components']);
});