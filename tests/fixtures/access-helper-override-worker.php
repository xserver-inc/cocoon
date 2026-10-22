<?php
/**
 * 旧記録ヘルパーの上書きを対象とした統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_PV_HELPER_OVERRIDE_WORKER') !== '1') exit(1);
$mode = isset($argv[2]) ? $argv[2] : '';
if (!in_array($mode, array('both-deny', 'insert-deny', 'update-deny', 'insert-anonymize', 'update-anonymize', 'update-legacy'), true)) exit(2);
$GLOBALS['pv_helper_override_mode'] = $mode;
$GLOBALS['pv_helper_override_calls'] = array('insert' => 0, 'update' => 0);
$GLOBALS['pv_helper_override_arguments'] = array('insert' => array(), 'update' => array());

// 子テーマと同じ読み込み順による、必要な側だけの旧挿入ヘルパーの上書き
if (in_array($mode, array('both-deny', 'insert-deny', 'insert-anonymize'), true)) {
    function insert_accesses_record($posts)
    {
        global $wpdb;
        $GLOBALS['pv_helper_override_calls']['insert']++;
        $GLOBALS['pv_helper_override_arguments']['insert'][] = array_keys($posts);
        if ($GLOBALS['pv_helper_override_mode'] !== 'insert-anonymize') return false;
        $posts['last_ip'] = '192.0.2.0';
        return $wpdb->insert(ACCESSES_TABLE_NAME, $posts);
    }
}

// 配列全体をDBへ渡す既存パターンと、独自のIP変換を持つ旧更新ヘルパーの上書き
if (in_array($mode, array('both-deny', 'update-deny', 'update-anonymize', 'update-legacy'), true)) {
    function update_accesses_record($id, $posts)
    {
        global $wpdb;
        $GLOBALS['pv_helper_override_calls']['update']++;
        $GLOBALS['pv_helper_override_arguments']['update'][] = array_keys($posts);
        if (in_array($GLOBALS['pv_helper_override_mode'], array('both-deny', 'update-deny'), true)) return false;
        if ($GLOBALS['pv_helper_override_mode'] === 'update-anonymize') {
            $posts['last_ip'] = '192.0.2.0';
            $posts['last_accessed_at'] = time();
        }
        return $wpdb->update(ACCESSES_TABLE_NAME, $posts, array('id' => $id));
    }
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '192.0.2.1';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
define('DISABLE_WP_CRON', true);
require rtrim(getenv('COCOON_PV_HELPER_OVERRIDE_WP_ROOT'), '/') . '/wp-load.php';
wp_set_current_user(0);
add_filter('theme_mod_access_count_enable', '__return_true');
global $wpdb;
$postId = (int) $argv[1];
$date = current_time('Y-m-d');
$atomicWrites = 0;
add_filter('query', static function ($sql) use (&$atomicWrites) {
    if (strpos($sql, 'INSERT INTO `' . ACCESSES_TABLE_NAME . '`') !== false && strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false) $atomicWrites++;
    return $sql;
});
$states = array();
$record = static function () use ($postId, $date, &$states) {
    global $wpdb;
    $result = logging_page_access($postId, 'post');
    $row = get_accesse_record_from($postId, $date, 'post');
    $states[] = array(
        'result' => $result,
        'pv' => (int) $wpdb->get_var($wpdb->prepare('SELECT COALESCE(SUM(count), 0) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d', $postId)),
        'last_ip' => $row ? $row->last_ip : null,
        'last_accessed_at' => $row ? (int) $row->last_accessed_at : null,
        'record_key' => $row ? $row->record_key : null,
        'calls' => $GLOBALS['pv_helper_override_calls'],
    );
};
$setTimestamp = static function ($timestamp) use ($postId) {
    global $wpdb;
    if ($wpdb->update(ACCESSES_TABLE_NAME, array('last_accessed_at' => $timestamp), array('post_id' => $postId)) === false) exit(3);
};
$record();
if (in_array($mode, array('both-deny', 'insert-deny'), true)) {
    // ヘルパーの上書きに依存しない、既存行を持つ更新経路の準備
    if ($wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $postId, 'post_type' => 'post', 'date' => $date, 'count' => 7, 'last_ip' => '192.0.2.1')) === false) exit(4);
    $record();
} elseif ($mode === 'update-deny') {
    $record();
    $setTimestamp(time() - 31);
    $record();
    $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
    $record();
} elseif ($mode === 'insert-anonymize') {
    $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
    $record();
    $record();
} elseif ($mode === 'update-anonymize') {
    $setTimestamp(time() - 31);
    $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
    $record();
    $_SERVER['REMOTE_ADDR'] = '192.0.2.0';
    $record();
    $setTimestamp(time() - 30);
    $record();
    $record();
} else {
    // 保存時刻を持たない旧上書きによる、時刻不明行への再記録の確認
    $setTimestamp(0);
    $record();
    $record();
}
fwrite(STDOUT, json_encode(array(
    'states' => $states,
    'calls' => $GLOBALS['pv_helper_override_calls'],
    'arguments' => $GLOBALS['pv_helper_override_arguments'],
    'atomic_writes' => $atomicWrites,
    'default_insert' => defined('COCOON_ANALYTICS_DEFAULT_INSERT_ACCESSES_RECORD'),
    'default_update' => defined('COCOON_ANALYTICS_DEFAULT_UPDATE_ACCESSES_RECORD'),
    'last_error' => $wpdb->last_error,
)) . "\n");
