<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー追加
//******************************************************************************
add_action('customize_register', function($wp_customize) {
  $wp_customize->add_panel(
    'hvn_cocoon',
    array(
      'title'     => 'Cocoon拡張設定',
      'priority'  => 300,
    )
  );
  wp_enqueue_style('hvn-admin', HVN_SKIN_URL . 'assets/css/admin.css');

  hvn_color($wp_customize);
  hvn_main($wp_customize);
  hvn_header($wp_customize);
});


//******************************************************************************
//  adminバー変更
//******************************************************************************
add_action('admin_bar_menu', function($wp_admin_bar) {
  $wp_admin_bar->remove_menu('my-account');
  $wp_admin_bar->remove_menu('wp-logo');
  $wp_admin_bar->remove_menu('search');

  if (is_admin_tool_menu_visible() && is_user_administrator()) {
    $wp_admin_bar->add_menu(array(
      'parent'  => 'dashboard_menu',
      'id'      => 'dashboard_menu-logout',
      'title'   => __('ログアウト'),
      'href'    => wp_logout_url()
    ));
  }
}, 10000);


//******************************************************************************
//　ダッシュボード投稿、固定ページ一覧を24時間表示
//******************************************************************************
add_filter('post_date_column_time', function($h_time, $post) {
  return get_the_date('Y年n月j日 H:i');
}, 10, 2);


add_filter('manage_pages_columns', function($columns) {
  $columns['slug'] = 'スラッグ';
  return $columns;
});


add_action('manage_pages_custom_column', function($column_name, $post_id) {
  switch($column_name) {
    case 'slug':
      $post = get_post($post_id);
      $slug = $post->post_name;
      echo esc_attr(urldecode($slug));
      break;
  }
}, 10, 2);


//******************************************************************************
//　ダッシュボード投稿一覧に追加
//******************************************************************************
add_filter('manage_post_posts_columns', function($columns) {
  $columns['last_modified'] = '更新日';
  $columns['the_page_meta_description'] = 'メタディスクリプション';
  $columns['slug'] = 'スラッグ';

  return $columns;
});


add_filter('manage_edit-post_sortable_columns', function($columns) {
  $columns['last_modified'] = 'modified';
  return $columns;
});


add_action('manage_posts_custom_column', function($column_name, $post_id) {
  switch($column_name) {
    case 'last_modified':
      $p_date = get_the_date('Y年n月j日 H:i');
      $u_date = get_the_modified_date('Y年n月j日 H:i');
      if ($p_date != $u_date) {
        $url = admin_url() . "admin-post.php?action=delete_date&id={$post_id}";
        $button = " <a class=\"button\" href=\"{$url}\">クリア</a>";
        echo $u_date . $button;
      }
      break;

    case 'the_page_meta_description':
      $post_meta = get_post_meta( $post_id, 'the_page_meta_description', true );
      if ($post_meta) {
        echo $post_meta;
      } else {
        echo '';
      }
      break;

    case 'slug':
      $post = get_post($post_id);
      $slug = $post->post_name;
      echo esc_attr(urldecode($slug));
      break;
  }
}, 10, 2);


//******************************************************************************
//　更新日クリア
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
//　クイック編集に入力フォーム追加
//******************************************************************************
add_action('quick_edit_custom_box', function($column_name, $post_type) {
  static $print_nonce = TRUE;
  if ($print_nonce) {
    $print_nonce = FALSE;
    wp_nonce_field('quick_edit_action', $post_type . '_edit_nonce'); //CSRF対策
  }
?>
<fieldset class="inline-edit-col-right inline-custom-meta">
  <div class="inline-edit-col column-<?php echo $column_name ?>">
    <label class="inline-edit-group">
    <?php
      switch($column_name) {
        case 'the_page_meta_description':
    ?>
    <span class="title">メタディスクリプション</span><span class="str-count">文字数:<span class="meta-description-count">0</span></span><textarea name="the_page_meta_description"></textarea>
    <?php
          break;
      }
    ?>
    </label>
  </div>
</fieldset>
<?php
}, 10, 2);


//******************************************************************************
//　クイック編集の入力フォームに値表示
//******************************************************************************
add_action('admin_footer-edit.php', function() {
  global $post_type;
  $slug = 'post';
  if ($post_type == $slug) {
?>
<script>
(function($) {
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

      $elm = $(':input[name="the_page_meta_description"]',$edit_row);

      var $the_page_meta_description = $('.column-the_page_meta_description', $post_row).html();
      $elm.val($the_page_meta_description);

      // 文字数表示
      $count = $elm.val().length;
      $('.meta-description-count', $edit_row).text($count);
      $elm.bind("keydown keyup keypress change",function(){
        var $count = $(this).val().length;
        $('.meta-description-count', $edit_row).text($count);
      });
    }
  };
  
})(jQuery);
</script>
<?php
  }
});


//******************************************************************************
//　カスタムフィールド更新
//******************************************************************************
add_action('save_post', function($post_id) {
  $slug = 'post';

  if ($slug !== get_post_type($post_id)) {
    return;
  }
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  $_POST += array("{$slug}_edit_nonce" => '');
  if (!wp_verify_nonce($_POST["{$slug}_edit_nonce"], 'quick_edit_action')) {
    return;
  }

  if ( isset( $_REQUEST['the_page_meta_description'] ) ) {
    update_post_meta( $post_id, 'the_page_meta_description', $_REQUEST['the_page_meta_description']);
  }
});


//******************************************************************************
//  リンクマネージャ非表示
//******************************************************************************
add_action('after_setup_theme', function (){
  remove_filter('pre_option_link_manager_enabled', '__return_true');
});


//******************************************************************************
//  ページ表示件数変更
//******************************************************************************
add_action('pre_get_posts', function($query) {
  if (is_admin() || !$query->is_main_query()) {
    return;
  }
  $query->set('posts_per_page', 12);
});


//******************************************************************************
//  検索結果を投稿日順
//******************************************************************************
add_filter('posts_search_orderby', function($search_orderby, $wp_query) {
  return 'post_date desc';
}, 10, 2);


//******************************************************************************
//  ウィジェットでショートコード実行許可
//******************************************************************************
add_filter('widget_text', 'do_shortcode');


//******************************************************************************
//  ウィジェットタイトルHTML入力
//******************************************************************************
add_filter('widget_title', function($title) {
  $title = str_replace('[', '<', $title);
  $title = str_replace(']', '>', $title);
  $title = str_replace('&#039;', "'", $title);
  $title = str_replace('&quot;', '"', $title);

  return $title;
});


//******************************************************************************
//  Gutenbergエディターメニュー追加
//******************************************************************************
add_action('enqueue_block_editor_assets', function() {
  wp_enqueue_script('hvn-block', HVN_SKIN_URL . 'assets/js/block.js', [], false, true);
});


//******************************************************************************
//  CSS、ライブラリ追加
//******************************************************************************
add_action('wp_enqueue_scripts', function() {
  if (get_theme_mod('hvn_like_setting')) {
    wp_enqueue_script('cookie', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js', ['jquery-migrate'], false, true);
  }

  hvn_h2_h4_css();
  hvn_color_css();
  hvn_custom_css();
  wp_dequeue_style('scrollhint-style');
}, 999);


//******************************************************************************
//  管理画面CSS追加
//******************************************************************************
add_action('admin_footer', function() {
  global $pagenow;

  // エディター画面
  if (is_gutenberg_editor_enable() && ($pagenow == 'post.php' || $pagenow == 'post-new.php')) {
    hvn_h2_h4_css();
  }
  wp_enqueue_style('hvn-admin', HVN_SKIN_URL . 'assets/css/admin.css');
}, 999);


//******************************************************************************
//  クラシックエディターCSS追加
//******************************************************************************
add_filter('tiny_mce_before_init',function($settings) {
  $settings['content_style'] = hvn_color_css();

  return $settings;
});


add_filter('editor_stylesheets', function($stylesheets) {
  if (!is_gutenberg_editor_enable()) {
    for ($i=2; $i<=4; $i++) {
      $no = get_theme_mod("hvn_h{$i}_css_setting", '1');
      $h_url = get_theme_file_uri(HVN_SKIN . "assets/css/h{$i}/h{$i}-{$no}.css");
      array_push($stylesheets , $h_url);
    }
  }

  return $stylesheets;
}, 999); 


//******************************************************************************
//  ローディング画面追加
//******************************************************************************
add_action('wp_body_open', function() {
  $load = get_theme_mod('hvn_front_loading_setting', 'none');
  if (is_front_top_page() && $load != 'none') {
    cocoon_template_part(HVN_SKIN . 'tmp/load/' . $load);
  }
});


//******************************************************************************
//  オプション更新
//******************************************************************************
add_action('wp_head', function() {
  global $_THEME_OPTIONS;

  $yymmdd = get_theme_mod('hvn_site_date_setting');
  if ($yymmdd) {
    list($yy, $mm, $dd) = explode('-', $yymmdd);
    $_THEME_OPTIONS['site_initiation_year'] = $yy;
  }
  $_THEME_OPTIONS['front_page_type'] = get_theme_mod('front_page_type', 'index');
  $_THEME_OPTIONS['entry_card_type'] = get_theme_mod('entry_card_type', 'entry_card');

  // タイルカード除外
  if ((get_theme_mod('entry_card_type') == 'tile_card_2')
   || (get_theme_mod('entry_card_type') == 'tile_card_3')) {
    remove_theme_mod('entry_card_type');
  }

  // サイドバー変更
  if ((get_theme_mod('entry_card_type') == 'vertical_card_3')
   || (get_theme_mod('front_page_type') == 'category_3_columns')) {
      $_THEME_OPTIONS['sidebar_display_type'] = 'no_display_index_pages';
  } else {
    // 3列解除
    if (get_theme_mod('entry_card_type') == 'vertical_card_3') {
      remove_theme_mod('entry_card_type');
    }
    if (get_theme_mod('front_page_type') == 'category_3_columns') {
      remove_theme_mod('front_page_type');
    }
  }
}, 999);


//******************************************************************************
//  エントリーカードいいねボタン
//******************************************************************************
add_action('wp_ajax_hvn_like_action', 'hvn_like_ajax');
add_action('wp_ajax_nopriv_hvn_like_action', 'hvn_like_ajax');


//******************************************************************************
//  アーカイブ日付をY-m形式変更
//******************************************************************************
add_filter('get_archives_link', function($html) {
  $html = preg_replace('/([0-9]*)年([0-9]*)月/', '$1-0$2', $html);
  $html = preg_replace('/([0-9]*)-0*([0-9]{2,})/','$1-$2', $html);

  return $html;
});


//******************************************************************************
//  カレンダー日付をY-m形式変更
//******************************************************************************
add_filter('get_calendar', function($html) {
  $html = preg_replace('/\s?([0-9]{1,})月\s?/', '0$1月', $html);
  $html = preg_replace('/0*([0-9]{2,})月/','$1', $html);
  $html = preg_replace('/([0-9]*)年/', '$1-', $html);

  /* ボタン */
  $html = str_replace('&laquo;', '<i class="fas fa-angle-left"></i>', $html);
  $html = str_replace('&raquo;', '<i class="fas fa-angle-right"></i>', $html);

  return $html;
});


//******************************************************************************
//  RSS日付Y-m-d形式変更
//******************************************************************************
add_filter('option_date_format', function($option){
  if (!is_admin()) {
    $option = get_site_date_format();
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
//  NEWマーク追加
//******************************************************************************
add_filter('post_class', function($classes, $class, $post_id) {
  // 表示期間3日
  $days = get_theme_mod('hvn_index_new_setting');
  if ($days == 0) {
    return $classes;
  }

  // 現在の時刻取得
  $now = date_i18n('U');

  // 最終更新日取得
  $mod_time  = get_update_time('U', $post_id);

  // 投稿日取得
  $post_time = get_the_time('U', $post_id);

  // 表示期間
  $last = $now - ($days * 24 * 60 * 60);
  if ($post_time > $last) {
    $classes[] = 'new-post';
  } else if ($mod_time > $last) {
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
    $html = get_theme_mod('hvn_mobile_text_setting', 'メニュー');
    $nav_menu = '<aside class="widget"><h3 class="widget-title">' . $html . '</h3>' . $nav_menu  . '</aside>';
  }

  return $nav_menu;
}, 10,2);
