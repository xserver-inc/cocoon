<?php
if (!function_exists('skin_get_site_font_family')) :
  function skin_get_site_font_family()
  {
    $skin_font_set = get_theme_option(OP_SITE_FONT_FAMILY, 'hiragino');
    $font_families = [
      'hiragino' => 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"',
      'meiryo' => 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"',
      'yu_gothic' => '"Yu Gothic", Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"',
      'ms_pgothic' => '"MS PGothic", "Hiragino Kaku Gothic ProN", "Hiragino Sans"',
      'noto_sans_jp' => '"Noto Sans JP"',
      'noto_serif_jp' => '"Noto Serif JP"',
      'mplus_1p' => '"M PLUS 1p"',
      'rounded_mplus_1c' => '"M PLUS Rounded 1c"',
      'kosugi' => '"Kosugi"',
      'kosugi_maru' => '"Kosugi Maru"',
      'sawarabi_gothic' => '"Sawarabi Gothic"',
      'sawarabi_mincho' => '"Sawarabi Mincho"',
    ];
    return $font_families[$skin_font_set] ?? 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans"';
  }
endif;

if (!function_exists('skin_get_nagi_radius')) :
  function skin_get_nagi_radius()
  {
    $radius_options = [
      'radius1' => '3px',
      'radius2' => '5px',
      'radius0' => '0px'
    ];
    return $radius_options[get_theme_mod('radius_radio')] ?? '3px';
  }
endif;

if (!function_exists('skin_get_nagi_big_radius')) :
  function skin_get_nagi_big_radius()
  {
    $big_radius_options = [
      'radius1' => '8px',
      'radius2' => '30px',
      'radius0' => '0px'
    ];
    return $big_radius_options[get_theme_mod('radius_radio')] ?? '8px';
  }
endif;

if (!function_exists('skin_get_nagi_shadow')) :
  function skin_get_nagi_shadow()
  {
    $shadow_options = [
      'shadow1' => '0 3px 12px -4px rgb(0 0 0 / 25%)',
      'shadow2' => '0 3px 10px -6px rgb(0 0 0 / 85%)',
      'shadow0' => 'none'
    ];
    return $shadow_options[get_theme_mod('shadow_radio')] ?? '0 3px 12px -4px rgb(0 0 0 / 25%)';
  }
endif;

if (!function_exists('nagi_css_custom')) {
  function nagi_css_custom() {
    $site_key_color = get_site_key_color() ?: '#000000';
    $site_key_color_light = colorcode_to_rgb_css_code($site_key_color, 0.6);
    $site_text_color = get_site_text_color() ?: '#333333';
    $site_text_color_light = colorcode_to_rgb_css_code($site_text_color, 0.4);
    $site_bg_color = get_site_background_color() ?: '#fff';
    $top_tab_bg = get_theme_mod('top_tab_bg', '#474a56');
    $top_tab_color = get_theme_mod('top_tab_color', '#ffffff');
    $gray_zone = colorcode_to_rgb_css_code(get_theme_mod('gray_zone', '#efefef'), 0.35);
    $nagi_g_font = '"' . get_theme_mod('font_radio', 'Quicksand') . '"';
    $nagi_c_font = skin_get_site_font_family();
    $nagi_radius = skin_get_nagi_radius();
    $nagi_big_radius = skin_get_nagi_big_radius();
    $nagi_shadow = skin_get_nagi_shadow();
    $cta_red = get_theme_mod('cta_red', '#ff3131');
    $cta_blue = get_theme_mod('cta_blue', '#0076ff');
    $cta_green = get_theme_mod('cta_green', '#42d800');
    $cta_bg0 = get_theme_mod('cta_bg', '#000000');
    $cta_color = get_theme_mod('cta_color', '#ffffff');
    $cta_bg_opa = get_theme_mod('cta_bg_opa', '0.8');
    $cta_bg = colorcode_to_rgb_css_code($cta_bg0, $cta_bg_opa);
    $header_bg_color = get_header_container_background_color();
    $header_text_color = get_header_container_text_color();
    $color_code = '';
    if ($header_bg_color && $header_text_color) {
      if ($header_bg_color) {
        $color_code .= 'background-color: '.$header_bg_color.';';
      }
      if ($header_text_color) {
        $color_code .= 'color: '.$header_text_color.';';
      }
      $color_code .= '.mobile-header-menu-buttons{'.$color_code.'}';
    }

    $icon_tabs = [
      1 => get_theme_mod('tab1_icon', 'f15c'),
      2 => get_theme_mod('tab2_icon', 'f164'),
      3 => get_theme_mod('tab3_icon', 'f164'),
      4 => get_theme_mod('tab4_icon', 'f164')
    ];

    $tab_bg1 = get_theme_mod('tab_bg_1', '#00d4ff');
    $tab_bg2 = get_theme_mod('tab_bg_2', '#e9ff00');

    $css = <<< EOM
:root{
  --nagi_key_color: $site_key_color;
  --nagi_key_color_light: $site_key_color_light;
  --nagi_site_text_color: $site_text_color;
  --nagi_site_text_color_light: $site_text_color_light;
  --nagi_bg_color: $site_bg_color;
  --nagi_g_font_fa: $nagi_g_font;
  --nagi_gray_zone: $gray_zone;
  --white_on_text: #626262;
  --nagi_r_radius: $nagi_radius;
  --nagi_r_big_radius: $nagi_big_radius;
  --nagi_r_shadow: $nagi_shadow;
  --nagi_cta_red: $cta_red;
  --nagi_cta_blue: $cta_blue;
  --nagi_cta_green: $cta_green;
  --nagi_cta_color: $cta_color;
  --nagi_cta_bg: $cta_bg;
  --nagi_c_font_fa: $nagi_c_font;
}
$color_code
EOM;

    foreach ($icon_tabs as $key => $icon) {
      $css .= <<< EOM
#index-tab-$key:checked ~ .index-tab-buttons .index-tab-button[for=index-tab-$key]::after {
  background-image: linear-gradient(90deg, $tab_bg1 65%, $tab_bg2);
}
#index-tab-$key:checked ~ .index-tab-buttons .index-tab-button[for=index-tab-$key] {
  background-image: linear-gradient(90deg, $tab_bg1, $tab_bg2);
  color: $top_tab_color;
  border: none;
}
.index-tab-buttons .index-tab-button {
  background-color: $top_tab_bg;
  color: $top_tab_color;
  border: none;
}
.index-tab-buttons .index-tab-button[for=index-tab-$key]::before {
  content: '\\$icon';
}
EOM;
    }

    if (get_theme_mod('sns_fix', true)) {
      $css .= <<< EOM
@media screen and (min-width: 1401px) {
  .sns-share.ss-col-6.ss-top {
    position: fixed;
    left: 2em;
    flex-direction: column;
    top: 50%;
    transform: translateY(-50%);
  }
  .sns-share.ss-col-6.ss-top .sns-share-buttons.sns-buttons {
    flex-direction: column;
  }
  .ss-top.sns-share.ss-high-and-low-lc a {
    width: 40px;
    height: 40px;
  }
  .ss-top.sns-share.ss-high-and-low-lc a .social-icon {
    font-size: 14px;
  }
  .ss-top.sns-share.ss-high-and-low-lc a .share-count {
    font-size: 9px;
  }
}
EOM;
    }

    if (get_theme_mod('sidebar_use_gray', true)) {
      $css .= <<< EOM
@media screen and (min-width: 1024px) {
  .widget-sidebar {
    padding: 10px;
    background-color: $gray_zone;
  }
  .widget_author_box, .nwa .toc {
    background-color: transparent;
  }
}
EOM;
    }

    if (get_theme_mod('tab_icon_ori', true)) {
      $css .= <<< EOM
.index-tab-buttons .index-tab-button {
  background-color: var(--nagi_bg_color) !important;
  color: var(--cocoon-text-color) !important;
  border: none !important;
  font-weight: normal !important;
  font-size: 11px;
  width: 100px;
  padding: 0;
  max-width: 19%;
  position: relative;
}
.index-tab-buttons .index-tab-button::before {
  display: block;
  font-size: 20px;
  margin: 0 auto 5px;
  width: 50px;
  height: 50px;
  background: $top_tab_bg;
  border-radius: 50px;
  line-height: 50px;
  position: relative;
  z-index: 1;
  color: $top_tab_color;
}
#index-tab-1:checked~.index-tab-buttons .index-tab-button[for=index-tab-1]::after,
#index-tab-2:checked~.index-tab-buttons .index-tab-button[for=index-tab-2]::after,
#index-tab-3:checked~.index-tab-buttons .index-tab-button[for=index-tab-3]::after,
#index-tab-4:checked~.index-tab-buttons .index-tab-button[for=index-tab-4]::after {
  width: 57px;
  height: 57px;
  content: '';
  position: absolute;
  display: block;
  top: -3px;
  left: 50%;
  transform: translate(-50%, 0px);
  border-radius: 50%;
  animation: 2s linear infinite guruguru;
}
label.index-tab-button {
  background-image: none !important;
}
EOM;
    }

    echo '<style>' . $css . '</style>';
  }
}
add_action('wp_head', 'nagi_css_custom', 101);

function nagi_custom_editor_styles() {
  wp_enqueue_style('my-editor-style', get_template_directory_uri() . '/skins/nagi/editor-style.css');

  $site_key_color = get_site_key_color() ?: '#000000';
  $site_key_color_light = colorcode_to_rgb_css_code($site_key_color, 0.6);
  $site_text_color = get_site_text_color() ?: '#333';
  $site_text_color_light = colorcode_to_rgb_css_code($site_text_color, 0.4);
  $site_bg_color = get_site_background_color() ?: '#fff';
  $gray_zone = colorcode_to_rgb_css_code(get_theme_mod('gray_zone', '#efefef'), 0.35);
  $nagi_g_font = '"' . get_theme_mod('font_radio', 'Quicksand') . '"';
  $nagi_c_font = skin_get_site_font_family();
  $nagi_radius = skin_get_nagi_radius();
  $nagi_big_radius = skin_get_nagi_big_radius();

  $custom_css = <<< EOM
:root{
  --nagi_key_color: $site_key_color;
  --nagi_key_color_light: $site_key_color_light;
  --nagi_site_text_color: $site_text_color;
  --nagi_site_text_color_light: $site_text_color_light;
  --nagi_bg_color: $site_bg_color;
  --nagi_g_font_fa: $nagi_g_font;
  --nagi_gray_zone: $gray_zone;
  --white_on_text: #626262;
  --nagi_r_radius: $nagi_radius;
  --nagi_r_big_radius: $nagi_big_radius;
  --nagi_c_font_fa: $nagi_c_font;
}
EOM;

  wp_add_inline_style('my-editor-style', $custom_css);
}
add_action('enqueue_block_editor_assets', 'nagi_custom_editor_styles');