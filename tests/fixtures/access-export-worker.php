<?php
/**
 * 実際の書き出しハンドラーを低メモリで実行する統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_EXPORT_TEST_WORKER') !== '1') exit(1);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('DISABLE_WP_CRON', true);
define('WP_ADMIN', true);
define('WP_MEMORY_LIMIT', '128M');
define('WP_MAX_MEMORY_LIMIT', '128M');
require rtrim(getenv('COCOON_EXPORT_TEST_WP_ROOT'), '/') . '/wp-load.php';
if (wp_convert_hr_to_bytes(ini_get('memory_limit')) !== 134217728) exit(8);

// 合成記事を通常テーブルへ残さない、接続単位の一時テーブルへの切り替え
global $wpdb, $current_user;
$wpdb->save_queries = false;
$wpdb->queries = array();
$template = json_decode((string) fgets(STDIN), true);
if (!is_array($template)) exit(2);
$original_posts = $wpdb->posts;
$temporary_posts = $original_posts . '_pv_stream_export';
$schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
if (!$schema || $wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)) === false) exit(3);
if ($wpdb->query("CREATE TEMPORARY TABLE `{$temporary_posts}` LIKE `{$original_posts}`") === false) exit(4);
$wpdb->posts = $temporary_posts;
$body = str_repeat('x', 65536);
for ($i = 0; $i < 1500; $i++) {
    $id = 1200000000 + $i;
    $post = array_merge($template, array('ID' => $id, 'post_title' => '=ランキング &amp; 引用 &quot;試験&quot;', 'post_content' => $body, 'post_name' => 'pv-stream-' . $i));
    if ($wpdb->insert($temporary_posts, $post) === false) exit(5);
    if (insert_accesses_record(array('post_id' => $id, 'date' => '2098-10-04', 'post_type' => 'post', 'count' => 1, 'last_ip' => '192.0.2.1')) === false) exit(6);
}
unset($post, $body);

// 権限不足の数値コードによる作成拒否と、取得上限0件での全件出力の検証
$mode = $argv[1];
$denied = 0;
if (substr($mode, -7) === '-denied') {
    if ($wpdb->query('SET SESSION sql_select_limit=0') === false) exit(10);
    add_filter('query', static function ($sql) use (&$denied) {
        if (strpos($sql, 'CREATE TEMPORARY TABLE `cocoon_pv_export_') === 0) {
            $denied++;
            return "SIGNAL SQLSTATE '42000' SET MYSQL_ERRNO=1142, MESSAGE_TEXT='CREATE command denied to user'";
        }
        return $sql;
    });
}

// 通常のソース読み取り失敗を成功扱いにしない、出力開始前のエラー応答の捕捉
if ($mode === 'source-error') {
    add_filter('query', static function ($sql) {
        return strpos($sql, ACCESSES_TABLE_NAME) !== false && preg_match('/SUM\(\s*(?:[a-zA-Z_]\w*\.)?`?count`?\s*\)/i', $sql)
            ? 'SELECT * FROM `cocoon_export_source_missing_table`' : $sql;
    });
    add_filter('wp_die_handler', static function () {
        return static function ($message, $title, $args) {
            echo json_encode(array('error' => $message, 'response' => $args['response']));
            exit;
        };
    });
}

// ユーザーや設定の永続変更を伴わない、実ハンドラーの認証・有効設定の準備
$current_user = new WP_User();
$current_user->ID = 2147483647;
$current_user->allcaps = array('manage_options' => true, 'read' => true);
add_filter('theme_mod_' . OP_ACCESS_ANALYTICS_EXPORT_ENABLE, '__return_true');
add_filter('pre_option_permalink_structure', static function () { return '/%category%/%postname%/'; });
$_REQUEST = array('target' => 'ranking', 'format' => strpos($mode, 'json') === 0 ? 'json' : 'csv', 'from' => '2098-10-04', 'to' => '2098-10-04', '_wpnonce' => wp_create_nonce('cocoon_analytics_export'));

// 起動時やテーマによる出力バッファーを除いた、ダウンロード本文だけの取得
while (ob_get_level() > 0) ob_end_clean();
$term_query_count = isset($GLOBALS['wp_object_cache']->cache['term-queries']) ? count($GLOBALS['wp_object_cache']->cache['term-queries']) : 0;
register_shutdown_function(static function () use ($term_query_count, $mode, &$denied, $wpdb) {
    if (substr($mode, -7) === '-denied' && ($denied !== 1 || (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1') !== '0')) {
        fwrite(STDERR, "作成拒否による代替出力または取得上限の保持の失敗\n");
        exit(11);
    }
    if (wp_suspend_cache_addition()) {
        fwrite(STDERR, "キャッシュ追加停止設定の残存\n");
        exit(7);
    }
    $current_count = isset($GLOBALS['wp_object_cache']->cache['term-queries']) ? count($GLOBALS['wp_object_cache']->cache['term-queries']) : 0;
    if ($current_count !== $term_query_count) {
        fwrite(STDERR, "ターム検索キャッシュの増加\n");
        exit(9);
    }
});
cocoon_analytics_handle_export();
