<?php
/**
 * 呼び出し元のトランザクション保護と、初回PV記録の実行段階を同期する実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessRecordingTransactionIntegrationTest extends IntegrationTestCase
{
    public function testOldSchemaMigrationIsDeferredInsideAnExplicitTransaction(): void
    {
        $this->assertMigrationPreservesTheCallerTransaction(false);
    }

    public function testOldSchemaMigrationIsDeferredInsideAnImplicitTransaction(): void
    {
        $this->assertMigrationPreservesTheCallerTransaction(true);
    }

    private function assertMigrationPreservesTheCallerTransaction(bool $implicit): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $id = $this->createPost(array('post_status' => 'publish', 'post_title' => 'トランザクション内のPV移行'));
        $server = $_SERVER;
        $mods = get_theme_mods();
        $userId = get_current_user_id();
        $autocommit = (int) $wpdb->get_var('SELECT @@SESSION.autocommit');
        $this->assertSame(1, $autocommit, '検証開始時の自動コミット設定');
        $marker = 'cocoon_pv_tx_marker_' . bin2hex(random_bytes(8));
        $accessesCreated = false;
        $markerCreated = false;
        $transactionStarted = false;
        $ddl = array();
        $observe = static function ($sql) use (&$ddl) {
            if (preg_match('/^\s*(?:CREATE|ALTER|DROP|TRUNCATE|RENAME|COMMIT)\b/i', $sql)) $ddl[] = $sql;
            return $sql;
        };
        $enabled = static function () { return true; };
        try {
            // 永続アクセス表の変更を避ける、接続専用の旧0.0.4スキーマ
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertIsArray($schema);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $accessesCreated = true;
            $this->assertNotFalse($wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` DROP INDEX `' . INDEX_ACCESSES_RECORD_KEY . '`, DROP COLUMN record_key, DROP COLUMN last_accessed_at'));
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE `' . $marker . '` (id INT PRIMARY KEY) ENGINE=InnoDB'));
            $markerCreated = true;
            $date = current_time('Y-m-d');
            $this->assertSame(1, $wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => $id, 'post_type' => 'post', 'date' => $date, 'count' => 7, 'last_ip' => '192.0.2.1')));
            set_theme_mod(OP_ACCESSES_TABLE_VERSION, '0.0.4');
            add_filter('theme_mod_' . OP_ACCESS_COUNT_ENABLE, $enabled);
            wp_set_current_user(0);
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REMOTE_ADDR'] = '192.0.2.1';
            $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
            unset($_SERVER['HTTP_PURPOSE'], $_SERVER['HTTP_SEC_PURPOSE'], $_SERVER['HTTP_X_MOZ']);

            // 明示開始と自動コミット停止後の書き込みによる、2種類の外側トランザクション
            $this->assertNotFalse($wpdb->query($implicit ? 'SET SESSION autocommit = 0' : 'START TRANSACTION'));
            $transactionStarted = true;
            $this->assertSame(1, $wpdb->insert($marker, array('id' => 1)));
            add_filter('query', $observe);
            try {
                $this->assertFalse(logging_page_access($id, 'post'));
            } finally {
                remove_filter('query', $observe);
            }
            $this->assertSame(array(), $ddl, '外側トランザクション中のDDLまたはCOMMIT');
            $this->assertSame('0.0.4', get_accesses_table_version());
            $this->assertSame(array(), $wpdb->get_col('SHOW COLUMNS FROM `' . ACCESSES_TABLE_NAME . "` WHERE Field IN ('last_accessed_at', 'record_key')"));
            $this->assertSame('7', $wpdb->get_var('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '`'));
            $this->assertNotFalse($wpdb->query('ROLLBACK'));
            $transactionStarted = false;
            $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . $marker . '`'), '移行処理による呼び出し元データの意図しない確定');
            $this->assertSame('7', $wpdb->get_var('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '`'));

            // 外側トランザクション終了後の移行再試行と、旧PVを維持した最初の加算
            $this->assertNotFalse($wpdb->query('SET SESSION autocommit = 1'));
            $this->assertSame(1, logging_page_access($id, 'post'));
            $this->assertSame(ACCESSES_TABLE_VERSION, get_accesses_table_version());
            $this->assertCount(2, $wpdb->get_col('SHOW COLUMNS FROM `' . ACCESSES_TABLE_NAME . "` WHERE Field IN ('last_accessed_at', 'record_key')"));
            $this->assertSame('8', $wpdb->get_var('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '`'));
            $this->assertSame('1', $wpdb->get_var('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '`'));
            $this->assertSame(cocoon_analytics_record_key($id, $date, 'post'), $wpdb->get_var('SELECT record_key FROM `' . ACCESSES_TABLE_NAME . '`'));
        } finally {
            remove_filter('query', $observe);
            remove_filter('theme_mod_' . OP_ACCESS_COUNT_ENABLE, $enabled);
            if ($transactionStarted || $implicit) $wpdb->query('ROLLBACK');
            $wpdb->query('SET SESSION autocommit = ' . $autocommit);
            if ($markerCreated) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . $marker . '`');
            if ($accessesCreated) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . ACCESSES_TABLE_NAME . '`');
            update_option('theme_mods_' . get_stylesheet(), $mods);
            $_SERVER = $server;
            wp_set_current_user($userId);
        }
    }

    public function testCurrentSchemaRecordingRemainsInsideTheCallerTransaction(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $created = false;
        $started = false;
        try {
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertIsArray($schema);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $created = true;
            $this->assertNotFalse($wpdb->query('START TRANSACTION'));
            $started = true;
            $this->assertSame(1, cocoon_analytics_record_page_access(1530000001, '2098-10-23', 'post', '192.0.2.1'));
            $this->assertSame('1', $wpdb->get_var('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '`'));
            $this->assertNotFalse($wpdb->query('ROLLBACK'));
            $started = false;
            $this->assertSame('0', $wpdb->get_var('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '`'));
        } finally {
            if ($started) $wpdb->query('ROLLBACK');
            if ($created) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . ACCESSES_TABLE_NAME . '`');
        }
    }

    public function testParallelFirstViewsInRepeatableReadTransactionsDoNotKeepAdoptionGapLocks(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        // 別接続から参照でき、開発用アクセス表へ混ざらないテスト専用テーブル
        $table = 'cocoon_pv_tx_' . bin2hex(random_bytes(8));
        $created = false;
        try {
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertIsArray($schema);
            $this->assertNotFalse($wpdb->query(str_replace('`' . ACCESSES_TABLE_NAME . '`', '`' . $table . '`', $schema[1])));
            $created = true;
            $this->assertNotFalse($wpdb->query('ALTER TABLE `' . $table . '` ENGINE=InnoDB'));
            $results = $this->runTransactionWorkers($table);
            $this->assertCount(2, $results);
            foreach ($results as $result) {
                $this->assertSame(1, $result['accepted']);
                $this->assertSame('', $result['write_error']);
                $this->assertSame('REPEATABLE READ', $result['isolation']);
                $this->assertTrue($result['insert_barrier']);
            }
            $row = $wpdb->get_row('SELECT COUNT(*) AS records, SUM(count) AS pv FROM `' . $table . '`', ARRAY_A);
            $this->assertSame('1', $row['records']);
            $this->assertSame('2', $row['pv']);
        } finally {
            if ($created) $wpdb->query('DROP TABLE IF EXISTS `' . $table . '`');
        }
    }

    private function runTransactionWorkers(string $table): array
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_PV_TRANSACTION_WORKER'] = '1';
        $environment['COCOON_PV_TRANSACTION_WP_ROOT'] = ABSPATH;
        // 引数や診断へ出さない、テスト専用DB接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $workers = array();
        $totalDeadline = $this->now() + 180;
        try {
            // 同時2プロセスに限定した、異なる接続元IPの初回記録
            foreach (array('192.0.2.1', '192.0.2.2') as $ip) {
                $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-recording-transaction-worker.php', $table, $ip);
                $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, $this->workerEnvironment($environment));
                if (!is_resource($process)) throw new \RuntimeException('トランザクション検証ワーカーの起動失敗');
                stream_set_blocking($pipes[1], false);
                stream_set_blocking($pipes[2], false);
                $workers[] = array('process' => $process, 'pipes' => $pipes, 'pending' => '', 'stdout' => '', 'stderr' => '', 'ready' => false, 'barrier' => false, 'result' => null, 'exit' => null);
            }
            $this->awaitTransactionWorkers($workers, 'ready', min($totalDeadline, $this->now() + 120));
            $recordingDeadline = min($totalDeadline, $this->now() + 60);
            foreach ($workers as $worker) {
                if (@fwrite($worker['pipes'][0], "START\n") !== 6) throw new \RuntimeException('トランザクション検証の開始合図の送信失敗');
            }
            // 両接続が採番処理を終えてINSERT直前へ到達した後だけの、同時書き込みの解放
            $this->awaitTransactionWorkers($workers, 'barrier', $recordingDeadline);
            foreach ($workers as $worker) {
                if (@fwrite($worker['pipes'][0], "INSERT\n") !== 7) throw new \RuntimeException('INSERT障壁の解放失敗');
                fclose($worker['pipes'][0]);
            }
            $this->awaitTransactionWorkers($workers, 'exit', $recordingDeadline);
            return array_column($workers, 'result');
        } catch (\Throwable $error) {
            $message = $error->getMessage();
            foreach ($workers as $index => $worker) $message .= "\nワーカー" . ($index + 1) . ': stdout=' . $worker['stdout'] . ', stderr=' . $worker['stderr'] . ', 終了状態=' . ($worker['exit'] ?? '実行中');
            throw new \RuntimeException(DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $message) : $message);
        } finally {
            foreach ($workers as $worker) {
                foreach ($worker['pipes'] as $pipe) if (is_resource($pipe)) fclose($pipe);
                $status = proc_get_status($worker['process']);
                if ($status['running']) proc_terminate($worker['process'], 9);
                proc_close($worker['process']);
            }
        }
    }

    private function awaitTransactionWorkers(array &$workers, string $phase, float $deadline): void
    {
        do {
            $complete = true;
            foreach ($workers as &$worker) {
                $this->readTransactionWorker($worker);
                $status = proc_get_status($worker['process']);
                if (!$status['running']) {
                    $this->readTransactionWorker($worker);
                    if ($worker['exit'] === null) $worker['exit'] = $status['exitcode'];
                    if ($worker['pending'] !== '') throw new \RuntimeException('トランザクション検証ワーカーの未改行出力');
                }
                if ($worker['exit'] !== null && $worker['exit'] !== 0) throw new \RuntimeException('トランザクション検証ワーカーの異常終了');
                $done = $phase === 'exit' ? $worker['exit'] === 0 && $worker['result'] !== null : $worker[$phase];
                if (!$done && $worker['exit'] !== null) throw new \RuntimeException('トランザクション検証ワーカーの応答前の終了');
                $complete = $complete && $done;
            }
            unset($worker);
            if ($complete) return;
            $remaining = $deadline - $this->now();
            if ($remaining <= 0) throw new \RuntimeException('トランザクション検証ワーカーの' . $phase . '待ち時間の超過');
            $read = array();
            foreach ($workers as $worker) foreach (array(1, 2) as $index) if (!feof($worker['pipes'][$index])) $read[] = $worker['pipes'][$index];
            $wait = max(1, (int) min(100000, $remaining * 1000000));
            if ($read) {
                $write = null;
                $except = null;
                if (stream_select($read, $write, $except, 0, $wait) === false) throw new \RuntimeException('トランザクション検証ワーカーのパイプ監視失敗');
            } else {
                usleep($wait);
            }
        } while (true);
    }

    private function readTransactionWorker(array &$worker): void
    {
        foreach (array(1 => 'stdout', 2 => 'stderr') as $index => $name) {
            $chunk = stream_get_contents($worker['pipes'][$index]);
            if ($chunk === false) throw new \RuntimeException('トランザクション検証ワーカーの読み取り失敗');
            $worker[$name] = substr($worker[$name] . $chunk, -8192);
            if ($index === 1) $worker['pending'] .= $chunk;
        }
        if ($worker['stderr'] !== '') throw new \RuntimeException('トランザクション検証ワーカーの標準エラーへの出力');
        while (($newline = strpos($worker['pending'], "\n")) !== false) {
            $line = rtrim(substr($worker['pending'], 0, $newline), "\r");
            $worker['pending'] = substr($worker['pending'], $newline + 1);
            if ($line === 'READY' && !$worker['ready']) {
                $worker['ready'] = true;
            } elseif ($line === 'INSERT_READY' && $worker['ready'] && !$worker['barrier']) {
                $worker['barrier'] = true;
            } elseif (strpos($line, 'RESULT ') === 0 && $worker['barrier'] && $worker['result'] === null) {
                $result = json_decode(substr($line, 7), true);
                if (!is_array($result) || !isset($result['accepted'], $result['write_error'], $result['isolation'], $result['insert_barrier'])) throw new \RuntimeException('トランザクション検証ワーカーの結果形式の不正');
                $worker['result'] = $result;
            } elseif ($line !== '') {
                throw new \RuntimeException('トランザクション検証ワーカーの予期しない標準出力');
            }
        }
        if (strlen($worker['pending']) > 65536) throw new \RuntimeException('トランザクション検証ワーカーの未改行出力の超過');
    }

    private function now(): float
    {
        // システム時刻の変更に左右されない、全フェーズの待機期限
        return hrtime(true) / 1e9;
    }
}
