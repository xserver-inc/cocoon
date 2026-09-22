<?php
// ブラウザーからの実行を防ぐ、ONEの動的CSS検証用CLIフィクスチャー
if (PHP_SAPI !== 'cli') {
  exit;
}

define('ABSPATH', dirname(__DIR__, 2) . '/');

// スキンのCSS出力だけを実行するための、対象フックの捕捉
function add_action($tag, $callback, $priority = 10, $accepted_args = 1) {
  if ($tag === 'get_template_part_tmp/css-custom') {
    $GLOBALS['one_css_callback'] = $callback;
  }
  return true;
}

function get_site_background_color() { return ''; }
function get_site_key_color() { return ''; }
function get_site_key_text_color() { return ''; }
function get_site_text_color() { return $GLOBALS['one_test_color']; }

require dirname(__DIR__) . '/wp-mock-functions.php';
require ABSPATH . 'lib/utils.php';
// PHPファイル末尾の空白をJSONに混入させないための出力捕捉
ob_start();
require ABSPATH . 'skins/one/functions.php';
ob_end_clean();

$styles = [];
foreach (['default' => '', 'custom' => '#123456', 'light-text' => '#f4eedd'] as $name => $color) {
  $GLOBALS['one_test_color'] = $color;
  ob_start();
  $GLOBALS['one_css_callback']();
  $styles[$name] = ob_get_clean();
}
echo json_encode($styles, JSON_THROW_ON_ERROR);
