<?php
/**
 * 接続フラグによるPV記録結果を、一時テーブルだけで比較する統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_PV_FOUND_ROWS_WORKER') !== '1') exit(1);
$mode = $argv[2] ?? '';
if (!in_array($mode, array('found', 'default'), true)) exit(2);
// WordPressの最初のDB接続へ適用する、比較対象のクライアントフラグ
define('MYSQL_CLIENT_FLAGS', $mode === 'found' ? MYSQLI_CLIENT_FOUND_ROWS : 0);
define('DISABLE_WP_CRON', true);
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '192.0.2.1';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
require rtrim(getenv('COCOON_PV_FOUND_ROWS_WP_ROOT'), '/') . '/wp-load.php';
wp_set_current_user(0);
add_filter('theme_mod_access_count_enable', '__return_true');

global $wpdb;
$database = $wpdb;
$id = (int) ($argv[1] ?? 0);
if ($id < 1) exit(3);
$date = current_time('Y-m-d');
$schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
// 接続単位の一時アクセス表による、実サイトや親プロセスへの書き込みの回避
if (!$schema || $wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)) === false) exit(4);
$record = static function () use ($id, $date) {
    global $wpdb;
    $result = logging_page_access($id, 'post');
    // 後続SELECTで書き換わる前の、同じ書き込みSQLの応答情報の取得
    $affected = $wpdb->rows_affected;
    $insertId = (string) $wpdb->insert_id;
    $writeError = $wpdb->last_error;
    $row = get_accesse_record_from($id, $date, 'post');
    return array(
        'result' => $result,
        'affected_rows' => $affected,
        'insert_id' => $insertId,
        'pv' => (int) $wpdb->get_var($wpdb->prepare('SELECT COALESCE(SUM(count), 0) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d', $id)),
        'id' => $row ? (string) $row->id : null,
        'raw_count' => $row && $row->count !== null ? (string) $row->count : null,
        'last_ip' => $row ? $row->last_ip : null,
        'last_accessed_at' => $row ? (int) $row->last_accessed_at : null,
        'write_error' => $writeError,
    );
};
$result = null;
try {
    $states = array($record(), $record());
    $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
    $states[] = $record();
    $states[] = $record();
    if ($wpdb->update(ACCESSES_TABLE_NAME, array('last_accessed_at' => time() - 30), array('post_id' => $id)) === false) exit(5);
    $states[] = $record();
    $states[] = $record();

    // 別処理の生成IDを持つ接続での、除外時のセッション状態の保持確認
    if ($wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $id + 1000000000, 'date' => $date, 'post_type' => 'post', 'count' => 42, 'last_ip' => '192.0.2.1')) === false) exit(6);
    $sessionId = $wpdb->get_var('SELECT LAST_INSERT_ID()');
    $sessionNoop = $record();
    $sessionPreserved = $wpdb->get_var('SELECT LAST_INSERT_ID()') === $sessionId;

    // 書き込み失敗時の、以前の成功IDによる誤判定と連打判定の時刻消費の検証
    $failWrite = static function ($sql) {
        return strpos($sql, 'INSERT INTO `' . ACCESSES_TABLE_NAME . '`') !== false && strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false ? '' : $sql;
    };
    $_SERVER['REMOTE_ADDR'] = '192.0.2.3';
    add_filter('query', $failWrite);
    try {
        $failure = $record();
    } finally {
        remove_filter('query', $failWrite);
    }
    $retry = $record();

    // 旧行の採番と生成IDのないUPDATE応答の混同を防ぐ、既存行からの加算の確認
    if ($wpdb->query('DELETE FROM `' . ACCESSES_TABLE_NAME . '`') === false) exit(7);
    if ($wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => 7, 'last_ip' => '192.0.2.3', 'last_accessed_at' => 0)) === false) exit(8);
    $legacyId = (string) $wpdb->insert_id;
    $legacy = array($record(), $record());
    $rows = (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '`');

    // 大小文字の比較方法だけを変える、接続専用一時テーブルでのIPv6表記の比較
    $ipv6 = array();
    foreach (array('ci' => 'utf8mb4_general_ci', 'binary' => 'utf8mb4_bin') as $comparison => $collation) {
        if ($wpdb->query('DELETE FROM `' . ACCESSES_TABLE_NAME . '`') === false) exit(9);
        if ($wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` MODIFY last_ip VARCHAR(40) CHARACTER SET utf8mb4 COLLATE ' . $collation) === false) exit(10);
        $_SERVER['REMOTE_ADDR'] = '2001:db8::a';
        $caseStates = array($record());
        $_SERVER['REMOTE_ADDR'] = '2001:DB8::A';
        $caseStates[] = $record();
        $_SERVER['REMOTE_ADDR'] = '2001:db8::a';
        $caseStates[] = $record();
        $expiredAt = time() - 30;
        if ($wpdb->update(ACCESSES_TABLE_NAME, array('last_accessed_at' => $expiredAt), array('post_id' => $id)) === false) exit(11);
        $_SERVER['REMOTE_ADDR'] = '2001:DB8::A';
        $caseStates[] = $record();
        $_SERVER['REMOTE_ADDR'] = '2001:db8::a';
        $caseStates[] = $record();
        $ipv6[$comparison] = array('states' => $caseStates, 'expired_at' => $expiredAt, 'rows' => (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '`'));
    }

    // NULLだけの書き換えによる成功誤判定を防ぐ、時刻付きの旧許容行の検証
    if ($wpdb->query('DELETE FROM `' . ACCESSES_TABLE_NAME . '`') === false) exit(12);
    $_SERVER['REMOTE_ADDR'] = '192.0.2.1';
    $nullableAt = time();
    if ($wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => null, 'last_ip' => '192.0.2.1', 'last_accessed_at' => $nullableAt)) === false) exit(13);
    $nullableId = (string) $wpdb->insert_id;
    $nullableStates = array($record());
    $nullableExpiredAt = time() - 30;
    if ($wpdb->update(ACCESSES_TABLE_NAME, array('last_accessed_at' => $nullableExpiredAt), array('post_id' => $id)) === false) exit(14);
    $nullableStates[] = $record();
    $nullableStates[] = $record();
    $nullableCount = array('states' => $nullableStates, 'initial_at' => $nullableAt, 'expired_at' => $nullableExpiredAt, 'id' => $nullableId, 'rows' => (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '`'));
    $result = array('debug_mode' => (bool) DEBUG_MODE, 'database_preserved' => $database === $wpdb, 'states' => $states, 'session_noop' => $sessionNoop, 'session_insert_id_preserved' => $sessionPreserved, 'failure' => $failure, 'retry' => $retry, 'legacy_id' => $legacyId, 'legacy' => $legacy, 'rows' => $rows, 'ipv6' => $ipv6, 'nullable_count' => $nullableCount, 'last_error' => $wpdb->last_error);
} finally {
    $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . ACCESSES_TABLE_NAME . '`');
}
fwrite(STDOUT, json_encode($result) . "\n");
