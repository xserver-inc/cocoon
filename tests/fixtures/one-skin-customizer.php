<?php
// ブラウザーからの実行を防ぐ、ONEのプレビュー検証用CLIフィクスチャー
if (PHP_SAPI !== 'cli') {
    exit;
}
define('ABSPATH', dirname(__DIR__, 2) . '/');
$input = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
$GLOBALS['test_theme_mods'] = $input['initial'];
$hooks = [];

function add_filter($tag, $callback, $priority = 10, $accepted_args = 1) {
    $GLOBALS['hooks'][$tag][] = $callback;
    return true;
}
function add_action($tag, $callback, $priority = 10, $accepted_args = 1) {
    return add_filter($tag, $callback, $priority, $accepted_args);
}
function add_theme_support($feature, ...$args) {
    $GLOBALS['one_theme_supports'][$feature] = $args;
}
function get_site_background_color() { return '#123456'; }
function get_site_key_color() { return '#abcdef'; }
function get_site_key_text_color() { return '#ffffff'; }
function get_site_text_color() { return '#333333'; }

require dirname(__DIR__) . '/wp-mock-functions.php';
require ABSPATH . 'lib/utils.php';
ob_start();
require ABSPATH . 'skins/one/functions.php';
ob_end_clean();

$results = [];
// スキン読み込み後に有効となる未保存値の再現
foreach ($input['previews'] as $mods) {
    $GLOBALS['test_theme_mods'] = $mods;
    $classes = ['existing-class'];
    foreach ($hooks['body_class'] ?? [] as $callback) {
        $classes = $callback($classes);
    }
    $results[] = $classes;
}
$_THEME_OPTIONS = [
    'site_background_color' => '#123456', 'site_key_color' => '#abcdef',
    'site_key_text_color' => '#ffffff', 'site_text_color' => '#333333',
    'unrelated_option' => 'preserved',
];
ob_start();
foreach ($hooks['get_template_part_tmp/css-custom'] as $callback) {
    $callback();
}
$css = ob_get_clean();
echo json_encode([
    'classes' => $results, 'css' => $css, 'options' => $_THEME_OPTIONS,
    'supports' => array_keys($GLOBALS['one_theme_supports'] ?? []),
], JSON_THROW_ON_ERROR);
