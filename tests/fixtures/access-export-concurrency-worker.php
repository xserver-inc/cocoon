<?php
/**
 * 集計SQLの実行中に別プロセスからPVを加算する統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_EXPORT_CONCURRENCY_WORKER') !== '1') exit(1);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('DISABLE_WP_CRON', true);
require rtrim(getenv('COCOON_EXPORT_CONCURRENCY_WP_ROOT'), '/') . '/wp-load.php';

global $wpdb;
$wpdb->save_queries = false;
$wpdb->queries = array();
$id = (int) ($argv[1] ?? 0);
$date = $argv[2] ?? '';
$isolation = $argv[3] ?? '';
$transaction = ($argv[4] ?? '') === 'transaction';
$gate = $argv[5] ?? '';
$signal = $argv[6] ?? '';
$snapshotMode = ($argv[7] ?? '') === 'snapshot';
$lastId = (int) ($argv[8] ?? 0);
if ($id < 1 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !in_array($isolation, array('REPEATABLE READ', 'READ COMMITTED'), true)
    || !preg_match('/^cocoon_pv_export_[a-f0-9]{32}_gate$/D', $gate)
    || !preg_match('/^cocoon_pv_export_[a-f0-9]{32}_signal$/D', $signal) || ($snapshotMode && $lastId <= $id)) exit(2);

// MySQLとMariaDBで名前が異なる、接続単位の分離レベルの取得
$readIsolation = static function () use ($wpdb) {
    $rows = $wpdb->get_results("SHOW SESSION VARIABLES WHERE Variable_name IN ('transaction_isolation', 'tx_isolation')", ARRAY_A);
    if (!$rows) throw new RuntimeException('分離レベルの取得失敗');
    return strtoupper(str_replace('-', ' ', $rows[0]['Value']));
};
$originalIsolation = $readIsolation();
$originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit');
$database = $wpdb;
$cases = array(array('isolation' => $isolation, 'route' => $transaction ? 'transaction' : 'temporary'));
if ($snapshotMode) {
    $cases = array();
    foreach (array('REPEATABLE READ', 'READ COMMITTED') as $level) {
        foreach (array('temporary', 'file', 'transaction') as $route) $cases[] = array('isolation' => $level, 'route' => $route);
    }
}
$failure = null;
foreach ($cases as $caseIndex => $case) {
    $isolation = $case['isolation'];
    $transaction = $case['route'] === 'transaction';
    $marker = 'cocoon_pv_export_tx_' . md5(uniqid('', true));
    $started = false;
    $rolledBack = false;
    $reading = false;
    $result = null;
    $active = array();
    $temporaryQueries = 0;
    $temporaryDenials = 0;
    $filter = static function ($sql) use (&$reading, $wpdb, $id, $gate, $signal, $case, &$active, &$temporaryQueries, &$temporaryDenials) {
        if (preg_match('/^CREATE TEMPORARY TABLE `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) {
            $temporaryQueries++;
            if ($case['route'] === 'file') {
                $temporaryDenials++;
                return "SIGNAL SQLSTATE 'HY000' SET MYSQL_ERRNO=1142, MESSAGE_TEXT='Temporary table creation denied'";
            }
            $active[$match[1]] = true;
        }
        if (preg_match('/^DROP TEMPORARY TABLE IF EXISTS `(cocoon_pv_export_[a-f0-9]+(?:_source)?)`/', $sql, $match)) unset($active[$match[1]]);
        // 実行計画で開始通知を消費しない、集計SQL本体だけへの遅延の限定
        if (!$reading && !preg_match('/^\s*EXPLAIN\b/i', $sql) && strpos($sql, ACCESSES_TABLE_NAME) !== false && preg_match('/SUM\(\s*(?:[a-zA-Z_]\w*\.)?`?count`?\s*\)/i', $sql)) {
            // 集計後の通知と親の加算完了までの待機による、固定秒数に依存しない実行順序の固定
            // グループごとの同じロックの多重取得を避けた、先頭記事だけの同期式
            $barrier = $wpdb->prepare('IF(MIN(post_id) = %d, IF(GET_LOCK(%s, 0) = 1, GET_LOCK(%s, 120) - 1, NULL), 0)', $id, $signal, $gate);
            $sql = preg_replace('/SUM\(\s*(?:[a-zA-Z_]\w*\.)?`?count`?\s*\)/i', '$0 + ' . $barrier, $sql, 1);
            $reading = true;
            fwrite(STDOUT, "READING\n");
            fflush(STDOUT);
        }
        return $sql;
    };

    try {
        if ($snapshotMode && $wpdb->query('SET SESSION sql_select_limit=10') === false) throw new RuntimeException('取得上限の準備失敗');
        if ($wpdb->query('SET SESSION TRANSACTION ISOLATION LEVEL ' . $isolation) === false) throw new RuntimeException('分離レベルの準備失敗');
        if ($transaction) {
            if ($wpdb->query('START TRANSACTION') === false) throw new RuntimeException('外側トランザクションの開始失敗');
            $started = true;
            if ($wpdb->insert($wpdb->options, array('option_name' => $marker, 'option_value' => '未確定の変更', 'autoload' => 'off')) === false) throw new RuntimeException('ロールバック検証用データの作成失敗');
        }
        $beforeStreams = array_keys(get_resources('stream'));
        $beforeCache = wp_suspend_cache_addition();
        $beforeErrors = $wpdb->suppress_errors;
        add_filter('query', $filter);
        fwrite(STDOUT, "READY\n");
        fflush(STDOUT);
        $iterator = cocoon_analytics_export_ranking_rows($date, $date);
        if (is_wp_error($iterator)) throw new RuntimeException($iterator->get_error_message());
        $matches = array();
        $ids = array();
        $pair = array(null, null);
        $positions = array(null, null);
        $totalPv = 0;
        foreach ($iterator as $row) {
            $rowId = (int) $row[0];
            $position = count($ids);
            $ids[] = $rowId;
            $totalPv += (int) $row[3];
            if ($rowId === $id) {
                $matches[] = $row;
                $pair[0] = (int) $row[3];
                $positions[0] = $position;
            } elseif ($rowId === $lastId) {
                $pair[1] = (int) $row[3];
                $positions[1] = $position;
            }
        }
        unset($iterator, $row);
        remove_filter('query', $filter);
        $preserved = $readIsolation() === $isolation;
        $markerPreserved = !$transaction || (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s", $marker)) === 1;
        if ($transaction) {
            if ($wpdb->query('ROLLBACK') === false) throw new RuntimeException('外側トランザクションのロールバック失敗');
            $rolledBack = true;
        }
        $markerCount = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s", $marker));
        $result = array('matching_rows' => count($matches), 'pv' => isset($matches[0]) ? (int) $matches[0][3] : null, 'isolation_preserved' => $preserved, 'marker_preserved' => $markerPreserved, 'marker_rolled_back' => $markerCount === 0);
        if ($snapshotMode) {
            $result += array('case' => $isolation . '/' . $case['route'], 'rows' => count($ids), 'unique_ids' => count(array_unique($ids)), 'post_ids' => $ids, 'pair_pv' => $pair, 'pair_positions' => $positions, 'total_pv' => $totalPv, 'temporary_queries' => $temporaryQueries, 'temporary_denials' => $temporaryDenials, 'database_preserved' => $wpdb === $database, 'select_limit_preserved' => (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit') === '10', 'cache_settings_preserved' => wp_suspend_cache_addition() === $beforeCache, 'errors_preserved' => $wpdb->suppress_errors === $beforeErrors, 'streams_released' => !array_diff(array_keys(get_resources('stream')), $beforeStreams), 'temporary_tables_released' => !$active);
        }
    } catch (Throwable $error) {
        $failure = $error->getMessage();
    } finally {
        if (isset($iterator)) unset($iterator);
        remove_filter('query', $filter);
        // 成功・失敗のどちらでも接続へ残さない、同期用の名前付きロックの解放
        $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $signal));
        $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $gate));
        if ($started && !$rolledBack) $wpdb->query('ROLLBACK');
        // 不意のcommitが発生した場合にもテスト用データを残さない、検証後の片付け
        $wpdb->delete($wpdb->options, array('option_name' => $marker), array('%s'));
        foreach (array_keys($active) as $table) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $table . '`');
        $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
        $wpdb->query('SET SESSION TRANSACTION ISOLATION LEVEL ' . $originalIsolation);
        if ($result !== null) {
            $result['isolation_restored'] = $readIsolation() === $originalIsolation;
            if ($snapshotMode) {
                $result['select_limit_restored'] = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit') === $originalLimit;
                $result['locks_released'] = $wpdb->get_var($wpdb->prepare('SELECT IS_USED_LOCK(%s)', $signal)) === null
                    && $wpdb->get_var($wpdb->prepare('SELECT IS_USED_LOCK(%s)', $gate)) === null;
            }
        }
    }
    if ($failure !== null) break;
    fwrite(STDOUT, 'RESULT ' . json_encode($result) . "\n");
    fflush(STDOUT);
    if ($snapshotMode && $caseIndex + 1 < count($cases)) {
        // 親の初期値復元が終わるまで次の条件を開始しない、明示的な継続合図の待機
        stream_set_timeout(STDIN, 60);
        if (fgets(STDIN) !== "NEXT\n") {
            $failure = '次の書き出し条件への開始通知待ちの失敗';
            break;
        }
    }
}
if ($failure !== null) {
    fwrite(STDERR, (DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $failure) : $failure) . "\n");
    exit(3);
}
