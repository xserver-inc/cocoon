<?php
/**
 * キャッシュ関数だけの上書きを画面・CSV・JSONで確認する統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_EXPORT_CACHE_OVERRIDE_WORKER') !== '1') exit(1);
$mode = $argv[1] ?? '';
$format = $argv[2] ?? '';
if (!in_array($mode, array('empty', 'custom', 'full'), true) || !in_array($format, array('screen', 'csv', 'json'), true)) exit(2);
$GLOBALS['pv_cache_override_mode'] = $mode;
$GLOBALS['pv_cache_override_ids'] = array((int) ($argv[3] ?? 0), (int) ($argv[4] ?? 0), (int) ($argv[5] ?? 0));
$GLOBALS['pv_cache_override_calls'] = array();
$GLOBALS['pv_cache_override_producer_rows'] = array();

// 標準ランキング本体を維持した、下位キャッシュ経由の独自除外とPV・順序の補正
function cocoon_analytics_cached($key_parts, callable $producer)
{
    if (!is_array($key_parts) || ($key_parts[0] ?? null) !== 'ranking') return $producer();
    $GLOBALS['pv_cache_override_calls'][] = $key_parts;
    if (count($GLOBALS['pv_cache_override_calls']) > 1) throw new RuntimeException('独自キャッシュへの重複したランキング呼び出し');
    $records = $producer();
    $GLOBALS['pv_cache_override_producer_rows'][] = count($records);
    if ($GLOBALS['pv_cache_override_mode'] === 'empty') return array();
    if ($GLOBALS['pv_cache_override_mode'] === 'custom') {
        $by_id = array_column($records, null, 'post_id');
        $last = $by_id[$GLOBALS['pv_cache_override_ids'][2]];
        $first = $by_id[$GLOBALS['pv_cache_override_ids'][0]];
        $last['post_id'] = (string) $last['post_id'];
        $last['post_type'] = 'cache_custom';
        $last['pv'] = '17';
        $first['pv'] = 41.75;
        return new ArrayIterator(array($last, $first));
    }
    $rows = array();
    for ($i = 0; $i < 500; $i++) $rows[] = array('post_id' => 1700000000 + $i, 'post_type' => 'virtual_cache', 'pv' => $i + 1);
    return $rows;
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('DISABLE_WP_CRON', true);
define('WP_ADMIN', true);
require rtrim(getenv('COCOON_EXPORT_CACHE_OVERRIDE_WP_ROOT'), '/') . '/wp-load.php';
$snapshotQueries = 0;
add_filter('query', static function ($sql) use (&$snapshotQueries) {
    if (strpos($sql, 'CREATE TEMPORARY TABLE `cocoon_pv_export_') === 0) $snapshotQueries++;
    return $sql;
});

// ダウンロード本文へ混入しない、標準エラーへの上書き状態と呼び出し情報の通知
register_shutdown_function(static function () use (&$snapshotQueries) {
    fwrite(STDERR, 'COCOON_EXPORT_CACHE_OVERRIDE ' . json_encode(array(
        'default_ranking' => defined('COCOON_ANALYTICS_DEFAULT_RANKING'),
        'default_cached' => defined('COCOON_ANALYTICS_DEFAULT_CACHED'),
        'calls' => $GLOBALS['pv_cache_override_calls'],
        'producer_rows' => $GLOBALS['pv_cache_override_producer_rows'],
        'snapshot_queries' => $snapshotQueries,
    )) . "\n");
});
if ($format === 'screen') {
    $rows = cocoon_analytics_ranking('2098-10-15', '2098-10-15', null, 500, 0);
    ob_start();
    cocoon_analytics_render_ranking_table($rows);
    $html = ob_get_clean();
    fwrite(STDOUT, json_encode(array('rows' => is_array($rows) ? $rows : iterator_to_array($rows, false), 'html' => $html), JSON_UNESCAPED_UNICODE) . "\n");
    exit;
}

// 永続設定を変更しない、実ハンドラーの管理者権限・有効設定・nonceの準備
global $current_user;
$current_user = new WP_User();
$current_user->ID = 2147483647;
$current_user->allcaps = array('manage_options' => true, 'read' => true);
add_filter('theme_mod_' . OP_ACCESS_ANALYTICS_EXPORT_ENABLE, '__return_true');
$_REQUEST = array('target' => 'ranking', 'format' => $format, 'from' => '2098-10-15', 'to' => '2098-10-15', '_wpnonce' => wp_create_nonce('cocoon_analytics_export'));
while (ob_get_level() > 0) ob_end_clean();
cocoon_analytics_handle_export();
