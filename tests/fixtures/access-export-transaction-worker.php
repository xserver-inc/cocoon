<?php
/**
 * GTIDで一時表を作成できない外側トランザクションを再現する、書き出し専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_EXPORT_TRANSACTION_WORKER') !== '1') exit(1);
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('DISABLE_WP_CRON', true);
define('WP_MEMORY_LIMIT', '128M');
define('WP_MAX_MEMORY_LIMIT', '128M');
require rtrim(getenv('COCOON_EXPORT_TRANSACTION_WP_ROOT'), '/') . '/wp-load.php';
while (ob_get_level() > 0) ob_end_clean();

global $wpdb;
$database = $wpdb;
$wpdb->save_queries = false;
$wpdb->queries = array();
$template = json_decode((string) fgets(STDIN), true);
if (!is_array($template) || wp_convert_hr_to_bytes(ini_get('memory_limit')) !== 134217728) exit(2);
$originalPosts = $wpdb->posts;
$originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit');
$posts = 'cocoon_pv_transaction_posts';
$marker = 'cocoon_pv_transaction_marker';
$date = '2098-10-09';
$created = array();
$started = false;
$rolledBack = false;
$temporaryQueries = 0;
$transactionQueries = array();
$result = null;
$failure = null;
$readIsolation = static function () use ($wpdb) {
    $rows = $wpdb->get_results("SHOW SESSION VARIABLES WHERE Variable_name IN ('transaction_isolation', 'tx_isolation')", ARRAY_A);
    if (!$rows) throw new RuntimeException('分離レベルの取得失敗');
    return $rows[0]['Value'];
};
$originalIsolation = $readIsolation();
$streamIds = static function () {
    return array_keys(get_resources('stream'));
};
$hashRow = static function ($digest, $id, $type, $pv) {
    hash_update($digest, $id . '|' . $type . '|' . (int) $pv . "\n");
};
$denyTemporary = static function ($sql) use (&$temporaryQueries, &$transactionQueries) {
    if (preg_match('/^\s*(?:CREATE|DROP) TEMPORARY TABLE (?:IF EXISTS )?`cocoon_pv_export_/', $sql)) {
        $temporaryQueries++;
        return "SIGNAL SQLSTATE 'HY000' SET MYSQL_ERRNO=3748, MESSAGE_TEXT='Statement violates GTID consistency: CREATE TEMPORARY TABLE inside transaction'";
    }
    if (preg_match('/^\s*(?:START\s+TRANSACTION|BEGIN|COMMIT|ROLLBACK|SET\b.*(?:AUTOCOMMIT|ISOLATION|BINLOG|GTID))/i', $sql)) $transactionQueries[] = $sql;
    return $sql;
};

try {
    // 本文を含む1500記事を永続表へ残さない、接続単位の合成データの準備
    $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
    if (!$schema || $wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)) === false) throw new RuntimeException('一時アクセス表の作成失敗');
    $created[] = ACCESSES_TABLE_NAME;
    if ($wpdb->query('CREATE TEMPORARY TABLE `' . $posts . '` LIKE `' . $originalPosts . '`') === false) throw new RuntimeException('一時記事表の作成失敗');
    $created[] = $posts;
    if ($wpdb->query('CREATE TEMPORARY TABLE `' . $marker . '` (id INT PRIMARY KEY) ENGINE=InnoDB') === false) throw new RuntimeException('トランザクション検証表の作成失敗');
    $created[] = $marker;
    $wpdb->posts = $posts;
    $body = str_repeat('x', 65536);
    for ($i = 0; $i < 1500; $i++) {
        $id = 1480000000 + $i;
        $post = array_merge($template, array('ID' => $id, 'post_title' => '外側TX &amp; 引用 &quot;試験&quot;', 'post_content' => $body, 'post_date' => '2098-10-0' . (1 + $i % 5) . ' 00:00:00', 'post_name' => 'pv-transaction-' . $i));
        if ($wpdb->insert($posts, $post) === false) throw new RuntimeException('合成記事の作成失敗');
        $pv = $i === 0 ? null : ($i === 1 ? -3 : ($i === 2 ? 0 : 1 + $i % 17));
        if (insert_accesses_record(array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => $pv, 'last_ip' => '192.0.2.1')) === false) throw new RuntimeException('合成PVの作成失敗');
    }
    unset($post, $body);
    $expected = $wpdb->get_results(cocoon_analytics_ranking_sql($date, $date) . ' LIMIT 2000', ARRAY_A);
    if (count($expected) !== 1500) throw new RuntimeException('比較用ランキングの取得件数の不一致');
    $expectedDigest = hash_init('sha256');
    $expectedPv = 0;
    foreach ($expected as $row) {
        $hashRow($expectedDigest, $row['post_id'], $row['post_type'], $row['pv']);
        $expectedPv += (int) $row['pv'];
    }
    $expectedHash = hash_final($expectedDigest);
    unset($expected, $row);

    // 取得上限と外側トランザクションを変更せず書き出すための、未確定データの準備
    if ($wpdb->query('SET SESSION sql_select_limit=10') === false || $wpdb->query('START TRANSACTION') === false) throw new RuntimeException('外側トランザクションの開始失敗');
    $started = true;
    if ($wpdb->insert($marker, array('id' => 1)) === false) throw new RuntimeException('未確定データの作成失敗');
    add_filter('query', $denyTemporary);
    $originalStreams = $streamIds();
    $originalCache = wp_suspend_cache_addition();

    // 反復開始前に破棄した場合の、全ソート用ストリームの解放確認
    $iterator = cocoon_analytics_export_ranking_rows($date, $date);
    if (is_wp_error($iterator)) throw new RuntimeException($iterator->get_error_message());
    unset($iterator);
    $unstartedReleased = !array_diff($streamIds(), $originalStreams);

    // 行の作成で例外が発生した場合の、ファイルとキャッシュ設定の復元確認
    $titleFailure = static function () { throw new RuntimeException('外側TXのタイトル処理の検証例外'); };
    add_filter('the_title', $titleFailure);
    $caught = false;
    try {
        $iterator = cocoon_analytics_export_ranking_rows($date, $date);
        if (is_wp_error($iterator)) throw new RuntimeException($iterator->get_error_message());
        $iterator->current();
    } catch (RuntimeException $error) {
        if ($error->getMessage() !== '外側TXのタイトル処理の検証例外') throw $error;
        $caught = true;
    } finally {
        remove_filter('the_title', $titleFailure);
        unset($iterator);
    }
    if (!$caught) throw new RuntimeException('タイトル処理の例外経路の未実行');
    $exceptionReleased = !array_diff($streamIds(), $originalStreams);

    // 全件完成後のPV変更を反復へ反映しない、ファイル内の順位と値の固定確認
    $iterator = cocoon_analytics_export_ranking_rows($date, $date);
    if (is_wp_error($iterator)) throw new RuntimeException($iterator->get_error_message());
    if ($wpdb->query('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=99999 WHERE post_id=1480000003') === false) throw new RuntimeException('順位固定後のPV更新失敗');
    $actualDigest = hash_init('sha256');
    $actualPv = 0;
    $rowCount = 0;
    $ids = array();
    $titleAndUrl = true;
    foreach ($iterator as $row) {
        $hashRow($actualDigest, $row[0], $row[1], $row[3]);
        $actualPv += (int) $row[3];
        $ids[$row[0]] = true;
        $rowCount++;
        if ($row[2] !== '外側TX & 引用 "試験"' || !is_string($row[4]) || $row[4] === '') $titleAndUrl = false;
    }
    unset($iterator, $row);
    $completedReleased = !array_diff($streamIds(), $originalStreams);
    $cacheRestored = wp_suspend_cache_addition() === $originalCache;
    $databasePreserved = $wpdb === $database;
    $isolationPreserved = $readIsolation() === $originalIsolation;
    $limitDuring = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit');
    $markerPreserved = (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . $marker . '` WHERE id=1') === 1;
    remove_filter('query', $denyTemporary);
    if ($wpdb->query('ROLLBACK') === false) throw new RuntimeException('外側トランザクションのロールバック失敗');
    $rolledBack = true;
    $markerRolledBack = (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . $marker . '` WHERE id=1') === 0;
    $result = array('rows' => $rowCount, 'unique_ids' => count($ids), 'expected_hash' => $expectedHash, 'actual_hash' => hash_final($actualDigest), 'expected_pv' => $expectedPv, 'actual_pv' => $actualPv, 'title_and_url_preserved' => $titleAndUrl, 'temporary_queries' => $temporaryQueries, 'transaction_queries' => $transactionQueries, 'database_preserved' => $databasePreserved, 'isolation_preserved' => $isolationPreserved, 'marker_preserved' => $markerPreserved, 'marker_rolled_back' => $markerRolledBack, 'select_limit_during_export' => $limitDuring, 'unstarted_streams_released' => $unstartedReleased, 'exception_streams_released' => $exceptionReleased, 'completed_streams_released' => $completedReleased, 'cache_settings_restored' => $cacheRestored, 'memory_limit' => wp_convert_hr_to_bytes(ini_get('memory_limit')), 'memory_peak' => memory_get_peak_usage(true), 'last_error' => $wpdb->last_error);
} catch (Throwable $error) {
    $failure = $error->getMessage();
} finally {
    remove_filter('query', $denyTemporary);
    if (isset($iterator)) unset($iterator);
    if ($started && !$rolledBack) $wpdb->query('ROLLBACK');
    $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
    $wpdb->posts = $originalPosts;
    foreach (array_reverse($created) as $table) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $table . '`');
    if ($result !== null) $result['select_limit_restored'] = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit') === $originalLimit;
}
if ($failure !== null) {
    fwrite(STDERR, (DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $failure) : $failure) . "\n");
    exit(3);
}
fwrite(STDOUT, json_encode($result) . "\n");
