<?php
// 本番PHPによる絞り込みフォームのCLI専用フィクスチャー
if (PHP_SAPI !== 'cli') exit;
define('ABSPATH', dirname(__DIR__, 2) . '/');
define('THEME_NAME', 'cocoon-master');
function admin_url($path = '') {return 'https://filters.test/' . $path;}
require dirname(__DIR__) . '/wp-mock-functions.php';
function esc_html__($text, $domain = '') {return esc_html(__($text, $domain));}
function esc_html_e($text, $domain = '') {echo esc_html__($text, $domain);}
function esc_attr__($text, $domain = '') {return esc_attr(__($text, $domain));}
function esc_attr_e($text, $domain = '') {echo esc_attr__($text, $domain);}
function selected($value, $key) {if ((string) $value === (string) $key) echo 'selected="selected"';}
function wp_parse_args($args, $defaults) {return array_merge($defaults, $args);}
function number_format_i18n($value) {return number_format($value);}
require ABSPATH . 'lib/page-access/analytics/render-func.php';
require ABSPATH . 'lib/page-access/analytics/map-post-picker.php';
require ABSPATH . 'lib/page-access/click-analytics/render-func.php';
require ABSPATH . 'lib/page-access/click-analytics/admin-query-func.php';
require ABSPATH . 'lib/page-access/click-analytics/filter-func.php';

parse_str($argv[1] ?? '', $query);
$view = $query['click_view'] ?? 'internal';
$args = cocoon_click_table_sort_args($query);
if (in_array($view, array('internal', 'external'), true)) $args['group'] = $query['group'] ?? 'occurrence';
cocoon_click_render_filters($view, $query['period'] ?? 'all', $query['from'] ?? '2026-09-01', $query['to'] ?? '2026-09-23', cocoon_click_filter_values($view, $query), $args);
if (isset($args['group'])) cocoon_click_render_group_switcher($view, $args['group']);
