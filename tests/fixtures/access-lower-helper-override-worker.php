<?php
/**
 * 汎用DBヘルパーの上書きと、上位ヘルパーとの組み合わせを対象とした統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_PV_LOWER_HELPER_OVERRIDE_WORKER') !== '1') exit(1);
$mode = isset($argv[2]) ? $argv[2] : '';
$modes = array('both-deny', 'insert-deny', 'update-deny', 'insert-anonymize', 'update-anonymize', 'both-anonymize', 'update-legacy', 'lower-insert-upper-update', 'upper-insert-lower-update', 'race-insert-update-deny', 'race-delete-insert-deny');
if (!in_array($mode, $modes, true)) exit(2);
$GLOBALS['pv_lower_helper_mode'] = $mode;
$GLOBALS['pv_lower_helper_calls'] = array('insert' => 0, 'update' => 0, 'upper_insert' => 0, 'upper_update' => 0);
$GLOBALS['pv_lower_helper_arguments'] = array('insert' => array(), 'update' => array(), 'upper_insert' => array(), 'upper_update' => array());

// 子テーマと同じ読み込み順による、挿入側だけの汎用DBヘルパーの上書き
if (in_array($mode, array('both-deny', 'insert-deny', 'insert-anonymize', 'both-anonymize', 'lower-insert-upper-update', 'race-delete-insert-deny'), true)) {
    function insert_db_table_record($table, $data, $format)
    {
        global $wpdb;
        if (!defined('ACCESSES_TABLE_NAME') || $table !== ACCESSES_TABLE_NAME) return $wpdb->insert($table, $data, $format);
        $GLOBALS['pv_lower_helper_calls']['insert']++;
        $GLOBALS['pv_lower_helper_arguments']['insert'][] = array('keys' => array_keys($data), 'format' => $format);
        if (in_array($GLOBALS['pv_lower_helper_mode'], array('both-deny', 'insert-deny', 'race-delete-insert-deny'), true)) return false;
        $data['last_ip'] = '192.0.2.0';
        return $wpdb->insert($table, $data, $format);
    }
}

// 更新側だけの上書きと、上書き側で完結するIP変換および時刻管理
if (in_array($mode, array('both-deny', 'update-deny', 'update-anonymize', 'both-anonymize', 'update-legacy', 'upper-insert-lower-update', 'race-insert-update-deny'), true)) {
    function update_db_table_record($table, $data, $where, $format, $where_format)
    {
        global $wpdb;
        if (!defined('ACCESSES_TABLE_NAME') || $table !== ACCESSES_TABLE_NAME) return $wpdb->update($table, $data, $where, $format, $where_format);
        $GLOBALS['pv_lower_helper_calls']['update']++;
        $GLOBALS['pv_lower_helper_arguments']['update'][] = array('keys' => array_keys($data), 'format' => $format, 'where' => array_keys($where), 'where_format' => $where_format);
        if (in_array($GLOBALS['pv_lower_helper_mode'], array('both-deny', 'update-deny', 'race-insert-update-deny'), true)) return false;
        if ($GLOBALS['pv_lower_helper_mode'] !== 'update-legacy') {
            $data['last_ip'] = '192.0.2.0';
            $data['last_accessed_at'] = time();
            $format[] = '%d';
        }
        return $wpdb->update($table, $data, $where, $format, $where_format);
    }
}

// 挿入側が下位の上書きでも更新側が上位の上書きとなる、交差した拡張構成
if ($mode === 'lower-insert-upper-update') {
    function update_accesses_record($id, $posts)
    {
        global $wpdb;
        $GLOBALS['pv_lower_helper_calls']['upper_update']++;
        $GLOBALS['pv_lower_helper_arguments']['upper_update'][] = array_keys($posts);
        $posts['last_ip'] = '192.0.2.0';
        $posts['last_accessed_at'] = time();
        return $wpdb->update(ACCESSES_TABLE_NAME, $posts, array('id' => $id));
    }
}

// 挿入側が上位の上書きでも更新側が下位の上書きとなる、逆方向の拡張構成
if ($mode === 'upper-insert-lower-update') {
    function insert_accesses_record($posts)
    {
        global $wpdb;
        $GLOBALS['pv_lower_helper_calls']['upper_insert']++;
        $GLOBALS['pv_lower_helper_arguments']['upper_insert'][] = array_keys($posts);
        $posts['last_ip'] = '192.0.2.0';
        return $wpdb->insert(ACCESSES_TABLE_NAME, $posts);
    }
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '192.0.2.1';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
define('DISABLE_WP_CRON', true);
require rtrim(getenv('COCOON_PV_LOWER_HELPER_OVERRIDE_WP_ROOT'), '/') . '/wp-load.php';
wp_set_current_user(0);
add_filter('theme_mod_access_count_enable', '__return_true');
global $wpdb;
$postId = (int) $argv[1];
$date = current_time('Y-m-d');
$atomicWrites = 0;
$raceTriggered = false;
$raceConnection = null;
if (in_array($mode, array('race-insert-update-deny', 'race-delete-insert-deny'), true)) {
    $raceConnection = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
    if ($mode === 'race-delete-insert-deny' && $wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $postId, 'post_type' => 'post', 'date' => $date, 'count' => 7, 'last_ip' => '192.0.2.1', 'last_accessed_at' => time() - 31, 'record_key' => cocoon_analytics_record_key($postId, $date, 'post'))) === false) exit(5);
    // 元の検索結果を維持した、検索と保存の間の別接続による追加・削除の再現
    add_filter('query', static function ($sql) use ($mode, $postId, $date, $raceConnection, &$raceTriggered) {
        if ($raceTriggered || strpos($sql, 'SELECT * FROM `' . ACCESSES_TABLE_NAME . '` USE INDEX') !== 0) return $sql;
        $raceTriggered = true;
        if ($mode === 'race-insert-update-deny') {
            if ($raceConnection->insert(ACCESSES_TABLE_NAME, array('post_id' => $postId, 'post_type' => 'post', 'date' => $date, 'count' => 7, 'last_ip' => '192.0.2.9', 'record_key' => cocoon_analytics_record_key($postId, $date, 'post'))) === false) exit(6);
            return 'SELECT * FROM `' . ACCESSES_TABLE_NAME . '` WHERE 1 = 0 LIMIT 1';
        }
        $before = $raceConnection->get_row($sql);
        if (!$before || $raceConnection->delete(ACCESSES_TABLE_NAME, array('post_id' => $postId), array('%d')) !== 1) exit(7);
        return $raceConnection->prepare('SELECT %d AS `id`, %d AS `post_id`, %s AS `date`, %s AS `post_type`, %d AS `count`, %s AS `last_ip`, %d AS `last_accessed_at`, %s AS `record_key` LIMIT 1', $before->id, $before->post_id, $before->date, $before->post_type, $before->count, $before->last_ip, $before->last_accessed_at, $before->record_key);
    });
}
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
        'calls' => $GLOBALS['pv_lower_helper_calls'],
    );
};
$setTimestamp = static function ($timestamp) use ($postId) {
    global $wpdb;
    if ($wpdb->update(ACCESSES_TABLE_NAME, array('last_accessed_at' => $timestamp), array('post_id' => $postId)) === false) exit(3);
};
$record();
if (in_array($mode, array('race-insert-update-deny', 'race-delete-insert-deny'), true)) {
    // 保存操作の反転を避けた、競合再現後1回だけの記録結果
} elseif (in_array($mode, array('both-deny', 'insert-deny'), true)) {
    // 下位ヘルパーを経由しない、更新経路のための既存行の準備
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
} elseif ($mode === 'update-legacy') {
    // 最終時刻の保存を行わない旧実装に対する、永久除外の防止の確認
    $setTimestamp(0);
    $record();
    $record();
} else {
    $setTimestamp(time() - 31);
    $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
    $record();
    $_SERVER['REMOTE_ADDR'] = '192.0.2.0';
    $record();
    $setTimestamp(time() - 30);
    $record();
    $record();
}
fwrite(STDOUT, json_encode(array(
    'states' => $states,
    'calls' => $GLOBALS['pv_lower_helper_calls'],
    'arguments' => $GLOBALS['pv_lower_helper_arguments'],
    'atomic_writes' => $atomicWrites,
    'default_insert' => defined('COCOON_DEFAULT_INSERT_DB_TABLE_RECORD'),
    'default_update' => defined('COCOON_DEFAULT_UPDATE_DB_TABLE_RECORD'),
    'race_triggered' => $raceTriggered,
    'row_counts' => array_map('intval', $wpdb->get_col($wpdb->prepare('SELECT count FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d ORDER BY id ASC', $postId))),
    'last_error' => $wpdb->last_error,
)) . "\n");
if ($raceConnection) $raceConnection->close();
