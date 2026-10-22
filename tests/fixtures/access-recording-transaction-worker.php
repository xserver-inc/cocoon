<?php
/**
 * RRトランザクション内の実PV記録を、INSERT直前で同期する統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_PV_TRANSACTION_WORKER') !== '1') exit(1);
$table = $argv[1] ?? '';
$ip = $argv[2] ?? '';
if (!preg_match('/^cocoon_pv_tx_[a-f0-9]{16}$/', $table) || !in_array($ip, array('192.0.2.1', '192.0.2.2'), true)) exit(2);
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('DISABLE_WP_CRON', true);
require rtrim(getenv('COCOON_PV_TRANSACTION_WP_ROOT'), '/') . '/wp-load.php';

function cocoon_pv_transaction_await_command(string $expected, float $timeout = 65): void
{
    $pending = '';
    $deadline = hrtime(true) / 1e9 + $timeout;
    stream_set_blocking(STDIN, false);
    do {
        $chunk = stream_get_contents(STDIN);
        if ($chunk === false) throw new RuntimeException('親プロセスの開始合図の読み取り失敗');
        $pending .= $chunk;
        if (strpos($pending, "\n") !== false) {
            if ($pending !== $expected . "\n") throw new RuntimeException('親プロセスからの予期しない開始合図');
            return;
        }
        if (feof(STDIN)) throw new RuntimeException('親プロセスの開始合図前の切断');
        if (strlen($pending) > 64) throw new RuntimeException('親プロセスの開始合図の上限超過');
        $remaining = $deadline - hrtime(true) / 1e9;
        if ($remaining <= 0) throw new RuntimeException('親プロセスの開始合図の待ち時間の超過');
        $read = array(STDIN);
        $write = null;
        $except = null;
        if (stream_select($read, $write, $except, 0, max(1, (int) min(100000, $remaining * 1000000))) === false) throw new RuntimeException('親プロセスの開始合図の監視失敗');
    } while (true);
}

class CocoonPvTransactionDatabase extends wpdb
{
    public string $destinationTable = '';
    public bool $insertBarrier = false;

    public function query($sql)
    {
        if ($this->destinationTable !== '' && strpos($sql, ACCESSES_TABLE_NAME) !== false) {
            // 実writerを変更せず、今回のテストだけの専用テーブルへ限定する書き込み先
            $sql = str_replace(ACCESSES_TABLE_NAME, $this->destinationTable, $sql);
            if (!$this->insertBarrier && preg_match('/^\s*INSERT\s+INTO\b/i', $sql) && strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false) {
                $this->insertBarrier = true;
                fwrite(STDOUT, "INSERT_READY\n");
                fflush(STDOUT);
                cocoon_pv_transaction_await_command('INSERT');
            }
        }
        return parent::query($sql);
    }
}

global $wpdb;
$original = $wpdb;
$database = null;
$started = false;
$committed = false;
$failure = null;
$result = null;
try {
    $database = new CocoonPvTransactionDatabase(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
    $database->suppress_errors(true);
    $database->destinationTable = $table;
    $wpdb = $database;
    if ($database->query('SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ') === false) throw new RuntimeException('RR分離レベルの設定失敗');
    if ($database->query('SET SESSION innodb_lock_wait_timeout = 3') === false) throw new RuntimeException('ロック待ち時間の設定失敗');
    $isolation = $database->get_results("SHOW SESSION VARIABLES WHERE Variable_name IN ('transaction_isolation', 'tx_isolation')", ARRAY_A);
    if (!$isolation) throw new RuntimeException('RR分離レベルの取得失敗');
    if ($database->query('START TRANSACTION') === false) throw new RuntimeException('外側トランザクションの開始失敗');
    $started = true;
    fwrite(STDOUT, "READY\n");
    fflush(STDOUT);
    cocoon_pv_transaction_await_command('START', 125);

    // 初回採番によるgap lockも含めた、実関数のINSERT直前での両接続の待ち合わせ
    $accepted = cocoon_analytics_record_page_access(1530000002, '2098-10-23', 'post', $ip);
    $writeError = $database->last_error;
    if ($accepted !== 1) throw new RuntimeException('並列初回記録の失敗: ' . $writeError);
    if ($database->query('COMMIT') === false) throw new RuntimeException('並列記録の確定失敗');
    $committed = true;
    $result = array('accepted' => $accepted, 'write_error' => $writeError, 'isolation' => strtoupper(str_replace('-', ' ', $isolation[0]['Value'])), 'insert_barrier' => $database->insertBarrier);
} catch (Throwable $error) {
    $failure = $error->getMessage();
} finally {
    if ($database !== null) {
        if ($started && !$committed) $database->query('ROLLBACK');
        $database->close();
    }
    $wpdb = $original;
}
if ($failure !== null) {
    fwrite(STDERR, (DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $failure) : $failure) . "\n");
    exit(3);
}
fwrite(STDOUT, 'RESULT ' . json_encode($result) . "\n");
fflush(STDOUT);
