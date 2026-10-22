<?php
/**
 * ランキング上書きと画面・実書き出しハンドラーの互換性を確認する専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_RANKING_OVERRIDE_WORKER') !== '1') exit(1);
$mode = $argv[1] ?? '';
$format = $argv[2] ?? '';
if (!in_array($mode, array('empty', 'custom', 'full'), true) || !in_array($format, array('screen', 'csv', 'json'), true)) exit(2);
$GLOBALS['pv_ranking_override_mode'] = $mode;
$GLOBALS['pv_ranking_override_ids'] = array((int) ($argv[3] ?? 0), (int) ($argv[4] ?? 0));
$GLOBALS['pv_ranking_override_calls'] = array();

// offsetを宣言しない旧4引数の上書きと、繰り返し呼び出しによる重複出力の検出
function cocoon_analytics_ranking($from, $to, $post_types = null, $limit = 10)
{
    $GLOBALS['pv_ranking_override_calls'][] = func_get_args();
    if (count($GLOBALS['pv_ranking_override_calls']) > 1) throw new RuntimeException('旧ランキング上書きへの重複呼び出し');
    if ($GLOBALS['pv_ranking_override_mode'] === 'empty') return array();
    if ($GLOBALS['pv_ranking_override_mode'] === 'custom') {
        return new ArrayIterator(array(
            array('post_id' => (string) $GLOBALS['pv_ranking_override_ids'][1], 'post_type' => 'book_custom', 'pv' => '17'),
            array('post_id' => 1999999000, 'post_type' => 'virtual_custom', 'pv' => 900),
            array('post_id' => $GLOBALS['pv_ranking_override_ids'][0], 'post_type' => 'page_override', 'pv' => 41.75),
        ));
    }
    $rows = array();
    for ($i = 0; $i < 500; $i++) $rows[] = array('post_id' => 1600000000 + $i, 'post_type' => 'virtual_custom', 'pv' => $i + 1);
    return $rows;
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('DISABLE_WP_CRON', true);
define('WP_ADMIN', true);
require rtrim(getenv('COCOON_RANKING_OVERRIDE_WP_ROOT'), '/') . '/wp-load.php';
$snapshotQueries = 0;
add_filter('query', static function ($sql) use (&$snapshotQueries) {
    if (strpos($sql, 'CREATE TEMPORARY TABLE `cocoon_pv_export_') === 0) $snapshotQueries++;
    return $sql;
});

// 本文と診断を混ぜない、標準エラーへの1回だけの呼び出し情報の通知
register_shutdown_function(static function () use (&$snapshotQueries) {
    fwrite(STDERR, 'COCOON_RANKING_OVERRIDE ' . json_encode(array(
        'calls' => $GLOBALS['pv_ranking_override_calls'],
        'snapshot_queries' => $snapshotQueries,
    )) . "\n");
});
if ($format === 'screen') {
    $rows = cocoon_analytics_ranking('2098-10-08', '2098-10-08', null, 500, 0);
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
$_REQUEST = array('target' => 'ranking', 'format' => $format, 'from' => '2098-10-08', 'to' => '2098-10-08', '_wpnonce' => wp_create_nonce('cocoon_analytics_export'));
while (ob_get_level() > 0) ob_end_clean();
cocoon_analytics_handle_export();
