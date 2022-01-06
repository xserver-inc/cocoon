<?php
// simple-darkmodeのfunctionsを参照する
include_once get_template_directory() . '/skins/simple-darkmode/functions.php';

/**
 * エディタ側エンキュー
 */
add_action( 'enqueue_block_editor_assets', function() {
  wp_enqueue_style( 'simple-darkmode-always-editor', get_template_directory_uri().'/skins/simple-darkmode-always/css/editor.css' );
});
