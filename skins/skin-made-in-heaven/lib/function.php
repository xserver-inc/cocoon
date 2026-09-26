<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザーメニュー設定
//******************************************************************************
if (!function_exists('hvn_menu_setting')):
function hvn_menu_setting($name) {
  $data = [];

  $file = url_to_local(get_theme_file_uri(HVN_SKIN . "assets/css/{$name}/{$name}.csv"));
  if (($fp = fopen($file, 'r')) !== false) {
    while (($line = fgetcsv($fp))) {
      $data["$line[0]"] = __("$line[1]", THEME_NAME);
    }
    fclose($fp);
  }

  return ['choices' => $data];
}
endif;


//******************************************************************************
//  カスタマイザーコントロール
//******************************************************************************
if (!function_exists('hvn_panel_control')):
function hvn_panel_control($wp_customize, $section, $setting, $default, $label, $description, $input_attrs, $type) {
  $wp_customize->add_setting($setting, $default);

  $args = array_merge(
    [
      'label'       => $label,
      'description' => $description,
      'section'     => "hvn_{$section}_section",
      'settings'    => $setting,
      'type'        => $type,
    ],
    $input_attrs,
  );

  $control_classes = [
    'color'   => 'WP_Customize_Color_Control',
    'i_image' => 'WP_Customize_Image_Control',
    'image'   => 'WP_Customize_Media_Control',
    'video'   => 'WP_Customize_Media_Control',
  ];

  // メディア系の設定
  if (in_array($type, ['i_image', 'image', 'video'])) {
    unset($args['type']);
    $args['mime_type'] = ($type === 'i_image') ? 'image' : $type;
  }

  $class_name = isset($control_classes[$type]) ? $control_classes[$type] : 'WP_Customize_Control';
  $wp_customize->add_control(new $class_name($wp_customize, $setting, $args));
}
endif;


//******************************************************************************
//  カスタマイザーラベル出力
//******************************************************************************
if (!function_exists('hvn_panel_label')):
function hvn_panel_label($wp_customize, $section, $label) {
  static $label_count = 1;

  $id = "hvn_label{$label_count}_{$section}_section";

  $wp_customize->add_setting($id);
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      $id,
      [
        'label'    => "■ {$label}",
        'section'  => "hvn_{$section}_section",
        'settings' => $id,
        'type'     => 'hidden',
      ]
    )
  );

  $label_count++;
}
endif;


//******************************************************************************
//  カスタマイザー値チェック
//******************************************************************************
// 数字チェック
if (!function_exists('hvn_sanitize_number_range')):
function hvn_sanitize_number_range($number, $setting) {
  $number = absint($number);
  $atts   = $setting->manager->get_control($setting->id)->input_attrs;

  $min  = (isset($atts['min'])  ? $atts['min'] : $number);
  $max  = (isset($atts['max'])  ? $atts['max'] : $number);
  $step = (isset($atts['step']) ? $atts['step'] : 1);

  if ($number >= $min && $number <= $max && ($number % $step === 0)) {
    return $number;
  }

  return $setting->default;
}
endif;


// テキストチェック
if (!function_exists('hvn_sanitize_text')):
function hvn_sanitize_text($text, $setting) {
  return ($text ? $text : $setting->default);
}
endif;


// 16進チェック
if (!function_exists('hvn_sanitize_color')):
function hvn_sanitize_color($color, $setting) {
  return (sanitize_hex_color($color) ? $color : $setting->default);
}
endif;


//******************************************************************************
//  ダッシュボード投稿・固定ページ一覧カラムにデータ出力
//******************************************************************************
if (!function_exists('hvn_custom_columns_content')):
function hvn_custom_columns_content($column_name, $post_id) {
  switch($column_name) {
    case 'slug':
      $post = get_post($post_id);
      echo esc_attr(urldecode($post->post_name));
      break;

    case 'last_modified':
      $p_date = get_the_date('Y-m-d H:i');
      $u_date = get_the_modified_date('Y-m-d H:i');
      if ($p_date != $u_date) {
        $url = admin_url("admin-post.php?action=delete_date&id={$post_id}");
        echo $u_date . ' <a class="button" href="' . esc_url($url) . '">' . __('クリア', THEME_NAME) . '</a>';
      }
      break;

    case 'the_page_meta_description':
      $post_meta = get_post_meta($post_id, 'the_page_meta_description', true);
      echo $post_meta ? esc_html($post_meta) : '';
      break;
  }
}
endif;


//******************************************************************************
//  フォントライブラリ取得
//******************************************************************************
if (!function_exists('hvn_font_setting')):
function hvn_font_setting() {
  //  フォントライブラリからフォントを取得
  $installed_fonts = get_posts([
    'post_type'      => 'wp_font_family',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
  ]);

  $font_choices = [
    '' => 'なし',
  ];

  if (!empty($installed_fonts)) {
    foreach ($installed_fonts as $font) {
      $font_choices[$font->post_title] = $font->post_title;
    }
  }

  return ['choices' => $font_choices];
}
endif;


//******************************************************************************
//  カラーコードを%薄いRGB、HEXコード変換
//******************************************************************************
if (!function_exists('hvn_color_mix_rgb')):
function hvn_color_mix_rgb($color, $per) {
  $color = ltrim($color, '#');
  if (strlen($color) == 3) {
    $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
  }

  $r = hexdec(substr($color, 0, 2));
  $g = hexdec(substr($color, 2, 2));
  $b = hexdec(substr($color, 4, 2));

  $res['red']   = (int)min(255, round($r * $per + 255 * (1 - $per)));
  $res['green'] = (int)min(255, round($g * $per + 255 * (1 - $per)));
  $res['blue']  = (int)min(255, round($b * $per + 255 * (1 - $per)));

  $res['hex'] = sprintf('#%02x%02x%02x', $res['red'], $res['green'], $res['blue']);

  return $res;
}
endif;


//******************************************************************************
//  スライド画像数取得
//******************************************************************************
if (!function_exists('hvn_image_count')):
function hvn_image_count() {
  static $cnt = null;

  if ($cnt !== null) return $cnt;

  $cnt = 0;
  for ($i=1; $i<=3; $i++) {
    if (get_theme_mod("hvn_header_img{$i}_setting")) {
      $cnt++;
    }
  }

  return $cnt;
}
endif;


//******************************************************************************
//  エディター用CSSキャッシュファイル名取得
//******************************************************************************
if (!function_exists('hvn_editor_css_cache_file')):
function hvn_editor_css_cache_file(){
  $file = get_theme_css_cache_path() . 'hvn-editor.css';
  return $file;
}
endif;


//******************************************************************************
//  インラインCSS出力
//******************************************************************************
if (!function_exists('hvn_add_inline_css')):
function hvn_add_inline_css($handle, $css) {
  wp_register_style($handle, false);
  wp_enqueue_style($handle);
  wp_add_inline_style($handle, $css);
}
endif;


//******************************************************************************
//  基本カラーCSS追加
//******************************************************************************
if (!function_exists('hvn_color_css')):
function hvn_color_css() {
  $GLOBALS['hvn_eyecatch'] = false;
  $vars = [];

  $vars[] = '--gap30:' . HVN_GAP . 'px';

  // サイトカラー
  $main_color = get_theme_mod('hvn_main_color_setting', HVN_MAIN_COLOR);
  $main_rgb   = colorcode_to_rgb($main_color);
  $hover_rgb  = hvn_color_mix_rgb($main_color, 0.15);

  $vars[] = "--main-color: {$main_color}";
  $vars[] = "--main-rgb-color: {$main_rgb['red']} {$main_rgb['green']} {$main_rgb['blue']}";
  $vars[] = "--hover-color: {$hover_rgb['hex']}";

  // テキスト・背景カラー
  $text_color = get_theme_mod('hvn_text_color_setting', HVN_TEXT_COLOR);
  $body_color = get_theme_mod('hvn_body_color_setting', HVN_BODY_COLOR);
  $body_rgb   = colorcode_to_rgb($body_color);
  $title_color = is_dark_hexcolor($body_color) ? '#fff' : '#333';

  $vars[] = "--text-color: {$text_color}";
  $vars[] = "--title-color: {$title_color}";
  $vars[] = "--body-color: {$body_color}";
  $vars[] = "--body-rgb-color: {$body_rgb['red']} {$body_rgb['green']} {$body_rgb['blue']}";

  // Scrollボタンカラー
  $scroll_color  = get_theme_mod('hvn_scroll_color_setting', HVN_SCROLL_COLOR);
  $vars[] = "--scroll-color: {$scroll_color}";

  // カード四角
  if (get_theme_mod('hvn_border_radius_setting')) {
    $vars[] = "--border-radius10: 0";
    $vars[] = "--border-radius100: 0";
  }

  // 縦アイキャッチ背景ぼかし
  if (get_theme_mod('hvn_eyecatch_setting') && has_post_thumbnail()) {
    $eye_img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
    if ($eye_img && $eye_img[2] > $eye_img[1]) {
      $GLOBALS['hvn_eyecatch'] = true;
      $vars[] = "--eyecatch: url({$eye_img[0]})";
    }
  }

  // 全ての目次表示
  if (is_multi_page_toc_visible()) {
    $count = isset($GLOBALS['hvn_h2_count']) ? $GLOBALS['hvn_h2_count'] : 0;
    $vars[] = "--h2-start-count: {$count}";
  }

  $css = ':root {' . implode(';', $vars) . ';}';
  hvn_add_inline_css('hvn-color', $css);
}
endif;


//******************************************************************************
//  見出しデザインCSS追加
//******************************************************************************
if (!function_exists('hvn_h2_h4_css')):
function hvn_h2_h4_css($deps=[]) {
  foreach (['h2','h3','h4'] as $tag) {
    $no = get_theme_mod("hvn_{$tag}_css_setting", '0');
    if ($no) {
      $h_url = get_theme_file_uri(HVN_SKIN . "assets/css/{$tag}/{$tag}-{$no}.css");
      wp_enqueue_style("hvn-{$tag}-style", $h_url, $deps);
    }
  }

  $widget = get_theme_mod('hvn_widget_css_setting', '0');
  if ($widget) {
    $widget_url = get_theme_file_uri(HVN_SKIN . "assets/css/w/w-{$widget}.css");
    wp_enqueue_style('hvn-widget-style', $widget_url);
  }

  $scroll = get_theme_mod('hvn_header_scroll_setting', '0');
  if ($scroll) {
    $scroll_url = get_theme_file_uri(HVN_SKIN . "assets/css/s/s-{$scroll}.css");
    wp_enqueue_style('hvn-scroll-style', $scroll_url);
  }

  wp_enqueue_style('hvn-original-style', HVN_SKIN_URL . 'assets/css/original.css');

  hvn_color_css();
  hvn_editor_css();
  hvn_custom_css();
}
endif;


//******************************************************************************
//  カスタムCSS追加
//******************************************************************************
if (!function_exists('hvn_custom_css')):
function hvn_custom_css() {
  $load = get_theme_mod('hvn_front_loading_setting', 'none');

  if (is_front_top_page() && $load != 'none') {
    $load_url = get_theme_file_uri(HVN_SKIN . "assets/css/l/{$load}.css");
    wp_enqueue_style('hvn-load-style', $load_url);
  }

  ob_start();
  cocoon_template_part(HVN_SKIN . 'tmp/css-custom');

  $css = ob_get_clean();
  if ($css) {
    hvn_add_inline_css('hvn-custom', $css);
  }
}
endif;


//******************************************************************************
//  エディターCSS追加
//******************************************************************************
if (!function_exists('hvn_editor_css')):
function hvn_editor_css() {
  ob_start();
  cocoon_template_part(HVN_SKIN . 'tmp/css-editor');

  $css = ob_get_clean();
  if ($css) {
    hvn_add_inline_css('hvn-editor', $css);
  }
}
endif;


//******************************************************************************
//  フロントページタイトル設定
//******************************************************************************
if (!function_exists('hvn_get_title_settings')):
function hvn_get_title_settings() {
  return [
    'new' => [
      'title' => ['key' => 'hvn_title_new_option_setting'         , 'default' => 'New Post', 'label' => __('新着記事', THEME_NAME)],
      'sub'   => ['key' => 'hvn_title_new_sub_option_setting'     , 'default' => __('新着・更新された記事です', THEME_NAME), 'label' => ''],
    ],
    'popular' => [
      'title' => ['key' => 'hvn_title_popular_option_setting'     , 'default' => 'Popular', 'label' => __('人気記事', THEME_NAME)],
      'sub'   => ['key' => 'hvn_title_popular_sub_option_setting' , 'default' => __('本日読まれている記事です', THEME_NAME), 'label' => ''],
    ],
    'category' => [
      'title' => ['key' => 'hvn_title_category_option_setting'    , 'default' => 'Category', 'label' => __('カテゴリーごと', THEME_NAME)],
      'sub'   => ['key' => 'hvn_title_category_sub_option_setting', 'default' => __('カテゴリーから記事を探す', THEME_NAME), 'label' => ''],
    ],
  ];
}
endif;


//******************************************************************************
//  メインビジュアル出力
//******************************************************************************
if (!function_exists('hvn_add_header')):
function hvn_add_header() {
  $output = [];
  $select = get_theme_mod('hvn_header_setting', 'none');

  // メインビジュアル
  $visual_html = '';
  switch ($select) {
    case 'video':
      $video_url = wp_get_attachment_url(get_theme_mod('hvn_header_video_setting'));
      if ($video_url) {
        $visual_html = sprintf('<div class="video_wrapper"><video autoplay loop muted playsinline><source src="%s"></video></div>', esc_url($video_url));
      }
      break;

    case 'image':
      $fade = get_theme_mod('hvn_header_fade_setting');
      $is_split = in_array($fade, ['h-split', 'v-split']) && hvn_image_count() > 1;
      $img_count = $is_split ? 2 : 1;
      $slides = '';

      for ($i=1; $i<=3; $i++) {
        $img_url = wp_get_attachment_url(get_theme_mod("hvn_header_img{$i}_setting"));
        if (!$img_url) continue;

        $inner_imgs = '';
        for ($j=1; $j<=$img_count; $j++) {
          $inner_imgs .= sprintf('<div class="img%d"><img src="%s" alt=""></div>', $j, esc_url($img_url));
        }
        $slides .= '<div class="swiper-slide">' . $inner_imgs . '</div>';
      }

      if ($slides) {
        $visual_html = '<div class="hvn-swiper"><div class="swiper-wrapper">' . $slides . '</div></div>';
      }
      break;
  }

  if ($visual_html) {
    $output[] = $visual_html;
    $output[] = '<div class="hvn-mask"></div>';
  }

  // ヘッダーロゴ
  $message_html = '';
  if (get_theme_mod('hvn_header_logo_setting')) {
    $logo_url = get_the_site_logo_url();
    $content = $logo_url
      ? sprintf('<img src="%s" alt="">', esc_url($logo_url))
      : apply_filters('site_logo_text', get_bloginfo('name'));
    $message_html = '<div class="message">' . $content . '</div>';

  } else {
    // タイトルテキスト
    $raw_msg = get_theme_mod('hvn_header_message_setting');
    if ($raw_msg) {
      $message_html = '<div class="message"><div>' . do_shortcode($raw_msg) . '</div></div>';
    }
  }

  if ($message_html) {
    $output[] = $message_html;
  }

  // 波線
  if (get_theme_mod('hvn_header_wave_setting')) {
    $output[] = hvn_wave('hvn-wave-main');
  }

  // Scrollボタン
  $scroll_type = get_theme_mod('hvn_header_scroll_setting', '0');
  if ($scroll_type && !empty($output)) {
    ob_start();
    cocoon_template_part(HVN_SKIN . 'tmp/scroll/s-' . $scroll_type);
    $output[] = ob_get_clean();
  }

  return implode("\n", $output);
}
endif;


//******************************************************************************
//  波線出力
//******************************************************************************
if (!function_exists('hvn_wave')):
function hvn_wave($class = '') {
  return sprintf(
    '<div class="%s">
      <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
          <path id="wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
        </defs>
        <g class="parallax">
          <use data-wave-index="1" xlink:href="#wave" x="48" y="0" style="fill:rgb(var(--body-rgb-color) / 70%%)"></use>
          <use data-wave-index="2" xlink:href="#wave" x="48" y="3" style="fill:rgb(var(--body-rgb-color) / 50%%)"></use>
          <use data-wave-index="3" xlink:href="#wave" x="48" y="5" style="fill:rgb(var(--body-rgb-color) / 30%%)"></use>
          <use data-wave-index="4" xlink:href="#wave" x="48" y="7" style="fill:rgb(var(--body-rgb-color) / 100%%)"></use>
        </g>
      </svg>
    </div>',
    esc_attr($class)
  );
}
endif;


//******************************************************************************
//  エントリーカードいいねボタン
//******************************************************************************
if (!function_exists('hvn_like_ajax')):
function hvn_like_ajax() {
  check_ajax_referer('hvn_like_nonce');

  $id   = $_POST['id'];
  $mode = $_POST['mode'];
  $key_like = 'post_like';

  $count = get_post_meta($id, $key_like, true);
  if ($count == null) {
    // 新規
    $count = 1;
    add_post_meta($id, $key_like, $count ,true);

  } else {
    // 更新
    if ($mode == 'add') {
      $count ++;
    }else{
      $count --;
    }
    update_post_meta($id, $key_like, $count);
  }
  // カウント値返却
  echo json_encode($count);
  wp_die();
}
endif;


// いいね数出力
if (!function_exists('hvn_like_tag')):
function hvn_like_tag($post_ID) {
  if (!get_theme_mod('hvn_like_setting') || !$post_ID) return '';

  $count = intval(get_post_meta($post_ID, 'post_like', true));

  return sprintf(
    '<div class="like" title="いいね"><span class="button" data-id="%d"></span><span class="count">%d</span></div>',
    esc_attr($post_ID),
    esc_html($count)
  );
}
endif;


//******************************************************************************
//  コメントアイコン追加
//******************************************************************************
if (!function_exists('hvn_comment_meta_post_icon')):
function hvn_comment_meta_post_icon($comment) {
  $post_icon = get_comment_meta($comment->comment_ID, 'post-icon', true);
  $label = __('アイコン番号', THEME_NAME);

  echo <<<EOF
<p>
  <label for="post-icon">{$label}:</label>
  <input type="text" name="post-icon" value="{$post_icon}"  class="widefat" />
</p>
EOF;
}
endif;


//******************************************************************************
//  パンくず「ホーム」変更
//******************************************************************************
if (!function_exists('hvn_breadcrumbs_root_text_custom')):
function hvn_breadcrumbs_root_text_custom(){
  return get_theme_mod('hvn_breadcrumbs_setting', __('ホーム', THEME_NAME));
}
endif;


//******************************************************************************
//  SNSシェア、フォローボタン変更
//******************************************************************************
if (!function_exists('hvn_sns_brand_color_replace')):
function hvn_sns_brand_color_replace($classes) {
  return str_replace('bc-brand-color ', 'bc-brand-color-white ', $classes);
}
endif;


//******************************************************************************
//  投稿・固定ページタイトルをカテゴリー・タグページに反映
//******************************************************************************
if (!function_exists('hvn_category_title_format_mapping')):
function hvn_category_title_format_mapping() {
  $page_format = get_theme_option('singular_page_title_format', 'pagetitle_sitename');

  $mapping = [
    'pagetitle_only'     => 'category_only',
    'pagetitle_sitename' => 'category_sitename',
    'sitename_pagetitle' => 'sitename_category',
  ];

  return isset($mapping[$page_format]) ? $mapping[$page_format] : 'category_only';
}
endif;


//******************************************************************************
//  「回転テキスト」ブロック出力
//******************************************************************************
if (!function_exists('hvn_render_circular_text_block')):
function hvn_render_circular_text_block($attributes, $content, $block) {
  $logo_size = 240;

  $bg_image = !empty($attributes['image_url']) ? 'url(' . esc_url($attributes['image_url']) . ')' : 'none';
  $weight_value = !empty($attributes['font_weight']) ? 'bold' : 'normal';

  $styles = [
    '--hvn-block-logo-size: ' . $logo_size . 'px',
    '--hvn-block-logo-bg-image: ' . $bg_image,
    '--hvn-block-logo-color: ' . esc_attr($attributes['text_color']),
    '--hvn-block-logo-font-size: ' . (int)$attributes['font_size'] . 'px',
    '--hvn-block-logo-font-weight: ' . $weight_value,
  ];

  $text_length = (int)($logo_size * M_PI);
  $unique_id = 'hvn-circular-text-' . wp_unique_id();

  $wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'hvn-block-logo-wrap',
    'style' => implode('; ', $styles)
  ]);

  ob_start();
  ?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="hvn-block-logo">
    <svg viewBox="0 0 <?php echo $logo_size; ?> <?php echo $logo_size; ?>">
      <path d="M 0,<?php echo $logo_size / 2; ?> a <?php echo $logo_size / 2; ?>,<?php echo $logo_size / 2; ?> 0 1,1 0,1 z" id="<?php echo esc_attr($unique_id); ?>" />
      <text>
        <textPath xlink:href="#<?php echo esc_attr($unique_id); ?>" textLength="<?php echo $text_length; ?>" lengthAdjust="spacing"><?php echo esc_html($attributes['main_text']). '　'; ?></textPath>
      </text>
    </svg>
  </div>
</div>
  <?php
  return ob_get_clean();
}
endif;
