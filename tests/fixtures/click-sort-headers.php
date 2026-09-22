<?php
// ブラウザーからの実行を防ぐ、並べ替え見出しのCLIフィクスチャー
if (PHP_SAPI !== 'cli') exit;
define('ABSPATH', dirname(__DIR__, 2) . '/');
define('THEME_NAME', 'cocoon-master');

// WordPress本体を使わないブラウザーテスト用のURL組み立て
function add_query_arg($args) {
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?: '', $query);
    foreach ($args as $key => $value) {
        if ($value === false) unset($query[$key]);
        else $query[$key] = $value;
    }
    return '/admin.php?' . http_build_query($query);
}
require dirname(__DIR__) . '/wp-mock-functions.php';
require ABSPATH . 'lib/page-access/click-analytics/admin-query-func.php';
require ABSPATH . 'lib/page-access/click-analytics/render-func.php';

function cocoon_test_sort_headers($query_string) {
    $_SERVER['REQUEST_URI'] = '/admin.php?' . $query_string;
    parse_str($query_string, $query);
    $sort = cocoon_click_table_sort_args($query);
    ob_start();
    foreach (array('impressions' => '推定表示', 'clicks' => 'クリック', 'unique' => '日次ユニーク合計', 'ctr' => '推定CTR') as $key => $label) {
        cocoon_click_render_sort_header($key, $label, $sort);
    }
    return ob_get_clean();
}

// 複数の並べ替え条件を1回のPHP起動で処理するCLI専用バッチ
if (($argv[1] ?? '') === '--batch') {
    $headers = array();
    foreach (json_decode($argv[2], true, 512, JSON_THROW_ON_ERROR) as $query_string) {
        $headers[$query_string] = cocoon_test_sort_headers($query_string);
    }
    echo json_encode($headers, JSON_THROW_ON_ERROR);
} else {
    echo cocoon_test_sort_headers($argv[1] ?? '');
}
