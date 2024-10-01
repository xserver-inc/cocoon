<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  カスタマイザーメニュー設定
//******************************************************************************
if (!function_exists('hvn_menu_setting')):
function hvn_menu_setting($name) {
  $data = array();

  $file = url_to_local(get_theme_file_uri(HVN_SKIN . "assets/css/{$name}/{$name}.csv"));
  if (($fp = fopen($file, 'r')) !== false) {
    while (($line = fgetcsv($fp))) {
      $data["$line[0]"] = "$line[1]";
    }
    fclose($fp);
  }

  return $data;
}
endif;


//******************************************************************************
//  カスタマイザー値チェック
//******************************************************************************
// 数字
if (!function_exists('hvn_sanitize_number_range')):
function hvn_sanitize_number_range($number, $setting) {
  $number = absint($number);
  $atts   = $setting->manager->get_control($setting->id)->input_attrs;
  $min  = (isset($atts['min'])  ? $atts['min'] : $number);
  $max  = (isset($atts['max'])  ? $atts['max'] : $number);
  $step = (isset($atts['step']) ? $atts['step'] : 1);

  return ($min <= $number && $number <= $max && is_int($number / $step) ? $number : $setting->default);
}
endif;


// テキスト
if (!function_exists('hvn_sanitize_text')):
function hvn_sanitize_text($text, $setting) {
  return ($text ? $text : $setting->default);
}
endif;


// パレット
if (!function_exists('hvn_sanitize_color')):
function hvn_sanitize_color($color, $setting) {
  return (sanitize_hex_color($color) ? $color : $setting->default);
}
endif;


//******************************************************************************
//  カラーコードを%薄いRGB、HEXコード変換
//******************************************************************************
if (!function_exists('hvn_color_mix_rgb')):
function hvn_color_mix_rgb($color, $per) {
  $color  = str_replace('#', '', $color);

  $a['red']   = round(hexdec(substr($color, 0, 2)) * $per + 255 * (1 - $per));
  $a['green'] = round(hexdec(substr($color, 2, 2)) * $per + 255 * (1 - $per));
  $a['blue']  = round(hexdec(substr($color, 4, 2)) * $per + 255 * (1 - $per));

  $a['red']   = $a['red']   > 255 ? 255 : $a['red'];
  $a['green'] = $a['green'] > 255 ? 255 : $a['green'];
  $a['blue']  = $a['blue']  > 255 ? 255 : $a['blue'];

  $a['hex']   = '#'. substr('0' . dechex($a['red']), -2) . substr('0' . dechex($a['green']), -2) . substr('0' . dechex($a['blue']), -2);

  return $a;
}
endif;


//******************************************************************************
//  見出しデザインCSS追加
//******************************************************************************
if (!function_exists('hvn_h2_h4_css')):
function hvn_h2_h4_css() {
  for ($i=2; $i<=4; $i++) {
    $no = get_theme_mod("hvn_h{$i}_css_setting", '0');
    if ($no) {
      $h_url = get_theme_file_uri(HVN_SKIN . "assets/css/h{$i}/h{$i}-{$no}.css");
      wp_enqueue_style("hvn-h{$i}-style", $h_url);
    }
  }

  $widget = get_theme_mod('hvn_widget_css_setting', '0');
  if ($widget) {
    $widget_url = get_theme_file_uri(HVN_SKIN . "assets/css/w/w-{$widget}.css");
    wp_enqueue_style('hvn-widget-style', $widget_url);
  }

  wp_enqueue_style('hvn-original-style', HVN_SKIN_URL . 'assets/css/original.css');
}
endif;


//******************************************************************************
//  基本カラーCSS追加
//******************************************************************************
if (!function_exists('hvn_color_css')):
function hvn_color_css() {
  global $_HVN_EYECATCH;

  $_HVN_EYECATCH = false;

  $css = '--wrap:' . get_site_wrap_width() . 'px;';
  $css .= '--gap30:' . HVN_GAP . 'px;';

  // サイトカラー
  $main_color = get_theme_mod('hvn_main_color_setting', HVN_MAIN_COLOR);
  if ($main_color) {
    $css .= "--main-color: {$main_color};";

    $rgb = colorcode_to_rgb($main_color);
    $css .= "--main-rgb-color: {$rgb['red']} {$rgb['green']} {$rgb['blue']};";

    $rgb = hvn_color_mix_rgb($main_color, 0.15);
    $css .= "--hover-color: {$rgb['hex']};";
  }

  // テキストカラー
  $text_color = get_theme_mod('hvn_text_color_setting', HVN_TEXT_COLOR);
  if ($text_color) {
    $css .= "--text-color: {$text_color};";
  }

  // 背景カラー
  $body_color = get_theme_mod('hvn_body_color_setting', HVN_BODY_COLOR);
  if ($body_color) {
    $title_color = is_dark_hexcolor($body_color) ? '#fff' : '#333';

    $rgb = colorcode_to_rgb($body_color);
    $css .= "--title-color: {$title_color};";
    $css .= "--body-color: {$body_color};";
    $css .= "--body-rgb-color: {$rgb['red']} {$rgb['green']} {$rgb['blue']};";
  }

  // プロフィール背景画像
  $img = wp_get_attachment_url(get_theme_mod('hvn_prof_setting'));
  $css .= $img ? "--prof-image: url({$img});" : "--prof-image: none;";

  // カード四角
  if (get_theme_mod('hvn_border_radius_setting')) {
    $css .= "--border-radius10: 0;--border-radius100: 0;";
  }

  // 縦アイキャッチ背景ぼかし
  if (get_theme_mod('hvn_eyecatch_setting')) {
    if (has_post_thumbnail()) {
      $thumbnail_id = get_post_thumbnail_id();
      $eye_img = wp_get_attachment_image_src($thumbnail_id , 'full');
      $url    = $eye_img[0];
      $width  = $eye_img[1];
      $height = $eye_img[2];

      if ($height > $width) {
        $_HVN_EYECATCH = true;
        $css .= "--eyecatch:url({$url});";
      }
    }
  }

  $css = ':root{' . $css . '}';

  $handle = 'hvn-color';
  wp_register_style($handle, false, array());
  wp_enqueue_style($handle);
  wp_add_inline_style($handle, $css);

  return $css;
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
    $handle = 'hvn-custom';
    wp_register_style($handle, false, array());
    wp_enqueue_style($handle);
    wp_add_inline_style($handle, $css);
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
    $handle = 'hvn-editor';
    wp_register_style($handle, false, array());
    wp_enqueue_style($handle);
    wp_add_inline_style($handle, $css);
  }
}
endif;


//******************************************************************************
//  メインビジュアル追加
//******************************************************************************
if (!function_exists('hvn_add_header')):
function hvn_add_header() {
  $html  = null;

  // メインビジュアル
  $select = get_theme_mod('hvn_header_setting');
  switch($select) {
    case 'video':
      $video  = wp_get_attachment_url(get_theme_mod('hvn_header_video_setting'));
      if ($video) {
        $html .= "<div class=video_wrapper><video autoplay loop muted playsinline><source src={$video}></video></div>";
      }
      break;

    case 'image':
      $fade = get_theme_mod('hvn_header_fade_setting');
      $img_html = null;
      $cnt = 1;
      if ((($fade == 'h-split') || ($fade == 'v-split'))  && hvn_image_count() > 1) {
        $cnt = 2;
      }
      for ($i=1; $i<=3; $i++) {
        $img = wp_get_attachment_url(get_theme_mod("hvn_header_img{$i}_setting"));

        if ($img) {
          $img_html .= '<div class="swiper-slide">';
          for ($j=1; $j<=$cnt; $j++) {
            $img_html .= "<div class=img{$j}><img src={$img}></div>" ;
          }
          $img_html .= '</div>';
        }
      }

      if ($img_html) {
        $html .= "<div class=hvn-swiper><div class=swiper-wrapper>{$img_html}</div></div>";
      }
      break;
  }
  $html .= '<div class="hvn-mask"></div>';

  // タイトルテキスト
  $msg = get_theme_mod('hvn_header_message_setting');
  if ($msg) {
    $msg = do_shortcode($msg);
    $msg = "<div class=message><div>{$msg}</div></div>";
  }

  // ヘッダーロゴ
  if (get_theme_mod('hvn_header_logo_setting')) {
    $url = get_the_site_logo_url();
    if ($url) {
      $msg = "<div class=message><img src={$url}></div>";
    } else {
      $site_logo_text = apply_filters('site_logo_text', get_bloginfo('name'));
      $msg = "<div class=message>{$site_logo_text}</div>";
    }
  }
  $html .= $msg;

  // 波線
  if (get_theme_mod('hvn_header_wave_setting')) {
    $html .= hvn_wave('hvn-wave-main');
  }

  // Scrollボタン
  $scroll = get_theme_mod('hvn_header_scroll_setting');
  if ($scroll && $html) {
    $html .= '<div class="scrolldown scrolldown' . $scroll .'"><span>Scroll</span></div>';
  }

  return $html;
}
endif;


//******************************************************************************
//  波線取得
//******************************************************************************
if (!function_exists('hvn_wave')):
function hvn_wave($class = null) {
  $html = <<< EOF
<div class="{$class}">
  <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
    <defs>
      <path id="wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
    </defs>
    <g class="parallax">
      <use xlink:href="#wave" x="48" y="0" style="fill:rgb(var(--body-rgb-color) / 70%)"></use>
      <use xlink:href="#wave" x="48" y="3" style="fill:rgb(var(--body-rgb-color) / 50%)"></use>
      <use xlink:href="#wave" x="48" y="5" style="fill:rgb(var(--body-rgb-color) / 30%)"></use>
      <use xlink:href="#wave" x="48" y="7" style="fill:rgb(var(--body-rgb-color) / 100%)"></use>
    </g>
  </svg>
</div>
EOF;

  return $html;
}
endif;


//******************************************************************************
//  スライド画像数取得
//******************************************************************************
if (!function_exists('hvn_image_count')):
function hvn_image_count() {
  $cnt = 0;
  for ($i=1; $i<=3; $i++) {
    if (get_theme_mod("hvn_header_img{$i}_setting")) {
      $cnt ++;
    }
  }

  return $cnt;
}
endif;


//******************************************************************************
//  エントリーカードいいねボタン
//******************************************************************************
if (!function_exists('hvn_like_ajax')):
function hvn_like_ajax() {
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


if (!function_exists('hvn_like_tag')):
function hvn_like_tag($post_ID) {
  $count = intval(get_post_meta($post_ID, 'post_like', true));
  $html =<<< EOF
<div class=like>
  <span class="button" data-id="{$post_ID}"></span>
  <span class="count">{$count}</span>
</div>
EOF;

  return $html;
}
endif;


//******************************************************************************
//  カスタマイザーラベル出力
//******************************************************************************
if (!function_exists('hvn_panel_label')):
function hvn_panel_label($wp_customize, $section, $label, $no) {
  $wp_customize->add_setting("hvn_label{$no}_{$section}_section");
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      "hvn_label{$no}_{$section}_section",
      array(
        'label'       => "■ {$label}",
        'section'     => "hvn_{$section}_section",
        'settings'    => "hvn_label{$no}_{$section}_section",
        'type'        => 'hidden',
      )
    )
  );
}
endif;


//******************************************************************************
//  サイト幅
//******************************************************************************
if (!function_exists('get_site_wrap_width')):
function get_site_wrap_width() {
  return HVN_MAIN_WIDTH + HVN_SIDE_WIDTH + HVN_GAP + 20;
}
endif;


if (!function_exists('get_main_column_width')):
function get_main_column_width() {
  return HVN_MAIN_WIDTH;
}
endif;


if (!function_exists('get_sidebar_width')):
function get_sidebar_width() {
  return HVN_SIDE_WIDTH;
}
endif;
