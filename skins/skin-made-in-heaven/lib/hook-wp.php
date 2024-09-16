<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザー追加
//******************************************************************************
add_action('customize_register', function($wp_customize) {
  $wp_customize->add_panel(
    'hvn_cocoon',
    array(
      'title'     => 'メイド・イン・ヘブン設定',
      'priority'  => 300,
    )
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
  $wp_admin_bar->remove_menu('my-account');
  $wp_admin_bar->remove_menu('wp-logo');
  $wp_admin_bar->remove_menu('search');

  if (is_admin_tool_menu_visible() && is_user_administrator()) {
    $wp_admin_bar->add_menu(array(
      'parent'  => 'dashboard_menu',
      'id'      => 'dashboard_menu-logout',
      'title'   => __('ログアウト', THEME_NAME),
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
  $columns['slug'] = __('Slug');
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
  $columns['last_modified'] = __('更新日', THEME_NAME);
  $columns['the_page_meta_description'] = __('メタディスクリプション', THEME_NAME);
  $columns['slug'] = __('Slug');

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
        $button = " <a class=\"button\" href=\"{$url}\">". __('クリア', THEME_NAME) . "</a>";
        echo $u_date . $button;
      }
      break;

    case 'the_page_meta_description':
      $post_meta = get_post_meta($post_id, 'the_page_meta_description', true);
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
  switch($column_name) {
    case 'the_page_meta_description':
    ?>
<fieldset class="inline-edit-col-right inline-custom-meta">
  <div class="inline-edit-col column-column-memo">
    <label class="inline-edit-group">
      <span class="title"><?php echo __('メモ', THEME_NAME) ?></span>
      <textarea name="the_page_memo"></textarea>
    </label>
  </div>
  <div class="inline-edit-col column-the_page_meta_description">
    <label class="inline-edit-group">
      <span class="title"><?php echo __('メタディスクリプション', THEME_NAME) ?></span><span class="str-count"><?php echo __('文字数', THEME_NAME) ?>:<span class="meta-description-count">0</span></span>
      <textarea name="the_page_meta_description"></textarea>
    </label>
  </div>
</fieldset>
    <?php
    break;
  }
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

      // メモ
      var $memo = $('.column-memo', $post_row).html();
      $(':input[name="the_page_memo"]', $edit_row).val($memo)

      // ディスクリプション
      $elm = $(':input[name="the_page_meta_description"]', $edit_row);

      var $the_page_meta_description = $('.column-the_page_meta_description', $post_row).html();
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

  if (isset($_REQUEST['the_page_memo'])) {
    update_post_meta( $post_id, 'the_page_memo', $_REQUEST['the_page_memo']);
  }

  if (isset($_REQUEST['the_page_meta_description'])) {
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
//  ウィジェットタイトルHTML入力
//******************************************************************************
add_filter('widget_title', function($title) {
  $title = str_replace('[', '<', $title);
  $title = str_replace(']', '>', $title);
  $title = str_replace('&#8216;', "'", $title);
  $title = str_replace('&#8217;', "'", $title);
  $title = str_replace('&#8221;', '"', $title);
  $title = str_replace('&#8220;', '"', $title);

  return $title;
});


//******************************************************************************
//  Gutenbergエディターメニュー追加
//******************************************************************************
add_action('enqueue_block_editor_assets', function() {
  wp_enqueue_script('hvn-block', HVN_SKIN_URL . 'assets/js/block.js', [], false, true);
});


//******************************************************************************
//  CSS追加
//******************************************************************************
add_action('wp_enqueue_scripts', function() {
  hvn_h2_h4_css();
  hvn_color_css();
  hvn_editor_css();
  hvn_custom_css();
  wp_dequeue_style('scrollhint-style');
  wp_enqueue_script('scrollhint-js', get_template_directory_uri() . '/plugins/scroll-hint-master/js/scroll-hint.min.js', array('jquery'), false, true);
}, 999);


//******************************************************************************
//  管理画面CSS追加
//******************************************************************************
add_action('admin_footer', function() {
  global $pagenow;

  // エディター画面
  if (is_gutenberg_editor_enable() && ($pagenow == 'post.php' || $pagenow == 'post-new.php')) {
    hvn_h2_h4_css();
    hvn_editor_css();
  }
  wp_enqueue_style('hvn-admin', HVN_SKIN_URL . 'assets/css/admin.css');
}, 999);


//******************************************************************************
//  カスタマイザーCSS追加
//******************************************************************************
add_action('customize_controls_enqueue_scripts', function() {
  wp_enqueue_style('hnv-custom', HVN_SKIN_URL . '/assets/css/customize.css' );
});


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

  // サイト開設年
  $yymmdd = get_theme_mod('hvn_site_date_setting');
  if ($yymmdd) {
    list($yy, $mm, $dd) = explode('-', $yymmdd);
    $_THEME_OPTIONS['site_initiation_year'] = $yy;
  }

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
    case 'title_card_2':
    case 'tile_card_3':
      remove_theme_mod('entry_card_type');
      break;

    case 'big_card':
      if (get_theme_mod('hvn_card_expansion_setting')) {
        $_THEME_OPTIONS['entry_card_snippet_visible'] = 1;
      }
      break;

    case 'big_card_first':
    case 'vertical_card_2':
      if (get_theme_mod('hvn_card_expansion_setting')) {
        if (strpos(get_front_page_type(), 'category') !== false) {
          // 新着記事数変更
          $_THEME_OPTIONS['index_new_entry_card_count'] = 5;

          // カテゴリーごと記事数変更
          $_THEME_OPTIONS['index_category_entry_card_count'] = 5;
        }
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
  $days = get_theme_mod('hvn_index_new_setting');
  if ($days == 0) {
    return $classes;
  }

  $now = date_i18n('U');
  $mod_time  = get_update_time('U', $post_id);
  $post_time = get_the_time('U', $post_id);

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


//******************************************************************************
//  インラインボタン変更
//******************************************************************************
add_filter('render_block', function($block_content) {
  $btn_circle = null;
  $btn_shine  = null;

  if (get_theme_mod('hvn_inline_button_set1_setting')) {
    $btn_circle = 'btn-circle';
  }

  if (get_theme_mod('hvn_inline_button_set2_setting')) {
    $btn_shine = 'btn-shine';
  }

  $block_content = preg_replace('/class="(inline-button)/', "class=\"$btn_circle $btn_shine $1", $block_content);
  return $block_content;
});


//******************************************************************************
//  コメントフォーム追加
//******************************************************************************

// コメントフォーム追加
add_action('comment_form_field_comment', function($content) {
  $icon = 3;
  $html = null;

  if (get_theme_mod('hvn_comment_setting') && is_user_logged_in()){
    for ($i=1; $i<=$icon; $i++) {
      $checked = null;
      if ($i == 1) {
        $checked = 'checked';
      }
      $img = get_theme_mod("hvn_comment_img{$i}_setting");
      if ($img) {
        $url = wp_get_attachment_url($img);
        $html  .= <<< EOF
<div class="hvn-comment-icon">
  <figure><img src="{$url}"></figure>
  <input type="radio" name="post-icon" value="{$i}" {$checked}>
</div>
EOF;
      }
    }
    if ($html) {
      $html = "<label>アイコン</label><div class=hvn-comment>{$html}</div>";
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


// コメントメタカスタムフィールド追加
add_action('add_meta_boxes_comment', function() {
 add_meta_box('hvn-comment-title', 'カスタムフィールド' , 'comment_meta_post_icon', 'comment', 'normal', 'high');
});


function comment_meta_post_icon($comment) {
  $post_icon = get_comment_meta($comment->comment_ID, 'post-icon', true);

  $html = <<<EOF
<p>
  <label for="post-icon">アイコン番号:</label>
  <input type="text" name="post-icon" value="{$post_icon}"  class="widefat" />
</p>
EOF;

  echo $html;
}


// コメントを編集カスタムフィールド更新
add_action('edit_comment', function($comment_id) {
  if (isset($_POST['post-icon'])) {
    update_comment_meta($comment_id, 'post-icon', esc_attr($_POST['post-icon']));
  }
});


// コメント一覧にカスタムフィールド追加
add_filter('manage_edit-comments_columns', function($columns) {
  $columns['post-icon'] = "アイコン番号";

  return $columns;
});


add_action('manage_comments_custom_column', function($column_name, $comment_id) {
  if ($column_name == 'post-icon') {
    $post_icon = get_comment_meta($comment_id, 'post-icon', true);
    echo esc_attr($post_icon);
  }
},10, 2);


// アバター変更
add_filter('get_avatar' , function($avatar, $comment) {
  if (defined('HVN_OPTION') && HVN_OPTION) {
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
  }

  return $avatar;
}, 100001, 2);


//******************************************************************************
//  独自パターン追加
//******************************************************************************
add_action('init',function() {
  $path = url_to_local(HVN_SKIN_URL) . "assets/pattern/*.json";
  $files = glob($path);

  foreach ($files as $i => $file) {
    $json =  json_decode(file_get_contents($file), true);
    register_block_pattern(
      "heaven/pattern{$i}",
      $json,
    );
  }
  register_block_pattern_category('heaven', ['label' => 'メイド・イン・ヘブン']);
});


//******************************************************************************
//  タグクラウドにパラメータ追加
//******************************************************************************
add_filter('in_widget_form', function($widget, $return, $instance) {
  if ($widget->id_base == 'tag_cloud') {
    $f_id   = $widget->get_field_id('drop');
    $f_name = $widget->get_field_name('drop');
    echo "<p><input type=checkbox class=widefat name={$f_name}" .  checked(isset($instance['drop']), true, false) . "><label for={$f_id}>ドロップダウンで表示</label></p>";
  }
}, 10, 3);


//******************************************************************************
//  設定フォーム更新
//******************************************************************************
add_filter('widget_update_callback', function($instance, $new_instance, $old_instance, $this_widget) {
  $instance['drop'] = $new_instance['drop'];

  return $instance;
}, 10, 4);


//******************************************************************************
//  設定値を追加
//******************************************************************************
add_filter('widget_tag_cloud_args', function($args, $instance) {
  $args['drop'] = isset($instance['drop']) ? $instance['drop'] : '';

  return $args;
},2,10);


//******************************************************************************
//  タグクラウド独自表示
//******************************************************************************
add_filter('wp_tag_cloud', function($return, $args) {
  if (isset($args['drop']) && $args['drop'] == 'on'){
    $id = get_query_var('tag_id');
    $tags = get_tags(array('orderby'=> 'count', 'order' => 'DESC'));

    ob_start();
    echo '<select aria-label="選択" onchange="document.location.href=this.options[this.selectedIndex].value;"><option value="" selected="selected">タグを選択</option>';

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
},2,10);


//******************************************************************************
//  タイムラインのタイトルHTMLタグ変更
//******************************************************************************
add_filter('render_block_cocoon-blocks/timeline', function($content, $block) {
  if (preg_match('/hvn-h[2-6]/', $content, $matches)) {
    $h = str_replace('hvn-', '', $matches[0]);
    $before = '/<div class="timeline-item-title">(.*)<\/div><div class="timeline-item-snippet/';
    $after = '<' . $h . ' class="timeline-item-title">$1</' . $h . '><div class="timeline-item-snippet';

    $content = preg_replace($before, $after, $content);
  }

  return $content;
}, 10, 2);


//******************************************************************************
//  FAQの質問HTMLタグ変更
//******************************************************************************
add_filter('render_block_cocoon-blocks/faq', function($content, $block) {
  if (preg_match('/hvn-h[2-6]/', $content, $matches)) {
    $h = str_replace('hvn-', '', $matches[0]);
    $before = ['/<div class="faq-question-content faq-item-content">(.*)<\/div><\/dt>/s', '/<dl(.*?)\/dl>/s', '/<dt(.*?)\/dt>/s', '/<dd(.*?)\/dd>/s'];
    $after  = ['<' . $h . ' class="faq-question-content faq-item-content">$1</' . $h . '></dt>', '<div$1/div>', '<div$1/div>', '<div$1/div>'];

    $content = preg_replace($before, $after, $content);
}

  return $content;
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
