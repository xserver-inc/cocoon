<?php
// simple-darkmodeの動的スタイルシートを参照する
include_once get_template_directory() . '/skins/simple-darkmode/inc/func-style.php';

add_action( 'wp_enqueue_scripts', function() {
  // スタイルシート読み込み
  wp_enqueue_style(
    'cocoon-skins-sda',
    esc_url(get_template_directory_uri() . '/skins/simple-darkmode-always/css/style.css'),
    [THEME_NAME.'-style', THEME_NAME.'-keyframes']
  );
});
