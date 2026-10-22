<?php
/**
 * ランキング書き出しと通常のPV加算を別プロセスで重ねる実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportConcurrencyIntegrationTest extends IntegrationTestCase
{
    /**
     * REPEATABLE READの書き出し中もPV加算が待たされず、集計値が維持されることの検証
     */
    public function testRepeatableReadExportDoesNotBlockRecording(): void
    {
        $this->assertConcurrentExport('REPEATABLE READ', false);
    }

    /**
     * REPEATABLE READの外側トランザクションと未確定の変更が維持されることの検証
     */
    public function testRepeatableReadExportPreservesAnExistingTransaction(): void
    {
        $this->assertConcurrentExport('REPEATABLE READ', true);
    }

    /**
     * READ COMMITTEDの書き出し中もPV加算が待たされず、集計値が維持されることの検証
     */
    public function testReadCommittedExportDoesNotBlockRecording(): void
    {
        $this->assertConcurrentExport('READ COMMITTED', false);
    }

    /**
     * READ COMMITTEDの外側トランザクションと未確定の変更が維持されることの検証
     */
    public function testReadCommittedExportPreservesAnExistingTransaction(): void
    {
        $this->assertConcurrentExport('READ COMMITTED', true);
    }

    /**
     * 101記事の途中で確定した一括更新を混在させない、両分離レベルと3経路の検証
     */
    public function testConcurrentAtomicUpdateKeepsOneSnapshotAcrossMoreThanOneHundredPosts(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $firstId = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '複数バッチの並列更新'));
        $template = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID=%d", $firstId), ARRAY_A);
        $this->assertIsArray($template);
        unset($template['ID']);
        $ids = array($firstId);
        $date = '2098-10-15';
        $lockPrefix = 'cocoon_pv_export_' . md5(uniqid('', true));
        $gate = $lockPrefix . '_gate';
        $signal = $lockPrefix . '_signal';
        $timeout = (int) $wpdb->get_var('SELECT @@SESSION.innodb_lock_wait_timeout');
        $suppressed = $wpdb->suppress_errors(true);
        try {
            // 投稿フックを100回実行しない、公開情報だけを使った回帰テスト用記事の準備
            for ($i = 1; $i <= 100; $i++) {
                $post = array_merge($template, array('post_name' => 'pv-snapshot-' . $lockPrefix . '-' . $i));
                $this->assertNotFalse($wpdb->insert($wpdb->posts, $post));
                $ids[] = (int) $wpdb->insert_id;
            }
            $lastId = end($ids);
            $values = array();
            $arguments = array();
            foreach ($ids as $id) {
                $values[] = '(%d,%s,%s,%d,%s)';
                $arguments = array_merge($arguments, array($id, $date, 'post', $id === $firstId ? 100 : ($id === $lastId ? 1 : 0), '192.0.2.1'));
            }
            $this->assertSame(101, $wpdb->query($wpdb->prepare('INSERT INTO `' . ACCESSES_TABLE_NAME . '` (post_id,date,post_type,count,last_ip) VALUES ' . implode(',', $values), $arguments)));
            $this->assertNotFalse($wpdb->query('SET SESSION innodb_lock_wait_timeout = 1'));
            $this->assertSame(1, (int) $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0)', $gate)));
            // WordPressの起動を繰り返さない、同じ子プロセス内での6条件の順次検証
            $result = $this->runExportWorker($firstId, $date, 'REPEATABLE READ', false, $gate, $signal, $ids);
            $this->assertCount(6, $result['worker'], $result['diagnostics']);
            $expectedCases = array('REPEATABLE READ/temporary', 'REPEATABLE READ/file', 'REPEATABLE READ/transaction', 'READ COMMITTED/temporary', 'READ COMMITTED/file', 'READ COMMITTED/transaction');
            foreach ($result['worker'] as $index => $case) {
                $label = $expectedCases[$index] . ': ' . $result['diagnostics'];
                $worker = $case['worker'];
                $this->assertSame($expectedCases[$index], $worker['case'], $label);
                $this->assertSame(2, $case['accepted'], $label);
                $this->assertSame('', $case['write_error'], $label);
                $this->assertSame(array(1100, 101), $case['committed_pv'], $label);
                $this->assertSame(101, $worker['rows'], $label);
                $this->assertSame(101, $worker['unique_ids'], $label);
                $exportedIds = $worker['post_ids'];
                sort($exportedIds);
                $this->assertSame($ids, $exportedIds, $label);
                // 順位だけでは見逃す、更新前のPV組と全件合計の完全照合
                $this->assertSame(array(100, 1), $worker['pair_pv'], $label);
                $this->assertSame(array(0, 1), $worker['pair_positions'], $label);
                $this->assertSame(101, $worker['total_pv'], $label);
                $this->assertSame($index % 3 === 1 ? 1 : 0, $worker['temporary_denials'], $label);
                $this->assertSame($index % 3 === 2 ? 0 : ($index % 3 === 1 ? 1 : 2), $worker['temporary_queries'], $label);
                foreach (array('isolation_preserved', 'marker_preserved', 'marker_rolled_back', 'isolation_restored', 'database_preserved', 'select_limit_preserved', 'select_limit_restored', 'cache_settings_preserved', 'errors_preserved', 'streams_released', 'temporary_tables_released', 'locks_released') as $flag) {
                    $this->assertTrue($worker[$flag], $flag . ': ' . $label);
                }
            }
        } finally {
            $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $gate));
            $wpdb->query('SET SESSION innodb_lock_wait_timeout = ' . $timeout);
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            $wpdb->query($wpdb->prepare('DELETE FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id IN (' . $placeholders . ')', $ids));
            // 基底クラス管理の先頭記事を除いた、SQLで作成した記事とキャッシュの片付け
            $rawIds = array_slice($ids, 1);
            if ($rawIds) {
                $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE ID IN (" . implode(',', array_fill(0, count($rawIds), '%d')) . ')', $rawIds));
                foreach ($rawIds as $id) wp_cache_delete($id, 'posts');
            }
            $wpdb->suppress_errors($suppressed);
        }
    }

    private function assertConcurrentExport(string $isolation, bool $transaction): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '書き出し中のPV記録'));
        $date = '2098-10-09';
        $lockPrefix = 'cocoon_pv_export_' . md5(uniqid('', true));
        $gate = $lockPrefix . '_gate';
        $signal = $lockPrefix . '_signal';
        $timeout = (int) $wpdb->get_var('SELECT @@SESSION.innodb_lock_wait_timeout');
        $suppressed = $wpdb->suppress_errors(true);
        try {
            $this->assertSame(1, cocoon_analytics_record_page_access($id, $date, 'post', '192.0.2.1'));
            // 書き出し側の共有ロックを検出するための、通常より短い書き込み待ち時間
            $this->assertNotFalse($wpdb->query('SET SESSION innodb_lock_wait_timeout = 1'));
            // 集計後の待機位置を固定するための、親接続による通過用ロックの事前取得
            $this->assertSame(1, (int) $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0)', $gate)));
            $result = $this->runExportWorker($id, $date, $isolation, $transaction, $gate, $signal);
            $this->assertSame(1, $result['accepted'], $result['diagnostics']);
            $this->assertSame('', $result['write_error'], $result['diagnostics']);
            $this->assertSame(1, $result['worker']['matching_rows']);
            $this->assertSame(1, $result['worker']['pv']);
            $this->assertTrue($result['worker']['isolation_preserved']);
            $this->assertTrue($result['worker']['marker_preserved']);
            $this->assertTrue($result['worker']['marker_rolled_back']);
            $this->assertTrue($result['worker']['isolation_restored']);
            $this->assertSame(2, (int) $wpdb->get_var($wpdb->prepare('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d AND date = %s', $id, $date)));
        } finally {
            $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $gate));
            $wpdb->query('SET SESSION innodb_lock_wait_timeout = ' . $timeout);
            $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id), array('%d'));
            $wpdb->suppress_errors($suppressed);
        }
    }

    private function runExportWorker(int $id, string $date, string $isolation, bool $transaction, string $gate, string $signal, array $snapshotIds = array()): array
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_EXPORT_CONCURRENCY_WORKER'] = '1';
        $environment['COCOON_EXPORT_CONCURRENCY_WP_ROOT'] = ABSPATH;
        // 引数や診断ログへ出さない、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-export-concurrency-worker.php', (string) $id, $date, $isolation, $transaction ? 'transaction' : 'autocommit', $gate, $signal);
        if ($snapshotIds) $command = array_merge($command, array('snapshot', (string) end($snapshotIds)));
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, $environment);
        if (!is_resource($process)) throw new \RuntimeException('書き出し競合ワーカーの起動失敗');
        $stdout = '';
        $stderr = '';
        $pending = '';
        $ready = false;
        $reading = false;
        $result = null;
        $accepted = null;
        $writeError = '';
        $exit = null;
        $cases = array();
        $totalDeadline = $this->monotonicTime() + ($snapshotIds ? 300 : 180);
        $phaseDeadline = min($totalDeadline, $this->monotonicTime() + 120);
        try {
            if (!$snapshotIds) fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            do {
                foreach (array(1, 2) as $index) {
                    $chunk = stream_get_contents($pipes[$index]);
                    if ($chunk === false) throw new \RuntimeException('書き出し競合ワーカーの出力読み取り失敗');
                    if ($index === 1) {
                        $stdout = substr($stdout . $chunk, -8192);
                        $pending .= $chunk;
                    } else {
                        $stderr = substr($stderr . $chunk, -8192);
                    }
                }
                if ($stderr !== '') throw new \RuntimeException('書き出し競合ワーカーの標準エラーへの出力');
                // 部分出力に停止しない、非ブロッキング読み取り後のメッセージ解析
                while (($newline = strpos($pending, "\n")) !== false) {
                    $line = rtrim(substr($pending, 0, $newline), "\r");
                    $pending = substr($pending, $newline + 1);
                    if ($line === 'READY' && !$ready) {
                        $ready = true;
                        $phaseDeadline = min($totalDeadline, $this->monotonicTime() + 60);
                    } elseif ($line === 'READING' && $ready && !$reading) {
                        $reading = true;
                        try {
                            // 通知直後の実行順序に依存しない、集計後の名前付きロック取得までの待機
                            $this->waitForAggregationSignal($signal, min($phaseDeadline, $totalDeadline));
                            if ($snapshotIds) {
                                // 途中状態が存在しない、別接続からの2記事の原子的なPV更新
                                $accepted = $wpdb->query($wpdb->prepare('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=CASE WHEN post_id=%d THEN 1100 ELSE 101 END WHERE date=%s AND post_id IN (%d,%d)', $id, $date, $id, end($snapshotIds)));
                            } else {
                                $accepted = cocoon_analytics_record_page_access($id, $date, 'post', '192.0.2.2');
                            }
                            $writeError = $wpdb->last_error;
                        } finally {
                            $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $gate));
                        }
                    } elseif (strpos($line, 'RESULT ') === 0 && $reading && $result === null) {
                        $result = json_decode(substr($line, 7), true);
                        if (!is_array($result)) throw new \RuntimeException('書き出し競合ワーカーの結果形式の不正');
                        if ($snapshotIds) {
                            $committed = $wpdb->get_col($wpdb->prepare('SELECT count FROM `' . ACCESSES_TABLE_NAME . '` WHERE date=%s AND post_id IN (%d,%d) ORDER BY post_id ASC LIMIT 2', $date, $id, end($snapshotIds)));
                            $cases[] = array('accepted' => $accepted, 'write_error' => $this->redact($writeError), 'committed_pv' => array_map('intval', $committed), 'worker' => $result);
                            if (count($cases) === 6) {
                                $result = $cases;
                                fclose($pipes[0]);
                            } else {
                                // 次の条件の開始前に完了する、初期PVと親側の通過用ロックの再準備
                                if ($wpdb->query($wpdb->prepare('UPDATE `' . ACCESSES_TABLE_NAME . '` SET count=CASE WHEN post_id=%d THEN 100 ELSE 1 END WHERE date=%s AND post_id IN (%d,%d)', $id, $date, $id, end($snapshotIds))) === false
                                    || (int) $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0)', $gate)) !== 1) throw new \RuntimeException('次の書き出し条件の準備失敗');
                                $ready = false;
                                $reading = false;
                                $result = null;
                                $accepted = null;
                                $writeError = '';
                                $phaseDeadline = min($totalDeadline, $this->monotonicTime() + 60);
                                if (fwrite($pipes[0], "NEXT\n") !== 5) throw new \RuntimeException('次の書き出し条件への開始通知失敗');
                                fflush($pipes[0]);
                            }
                        }
                    } elseif ($line !== '') {
                        throw new \RuntimeException('書き出し競合ワーカーの予期しない標準出力');
                    }
                }
                if (strlen($pending) > 65536) throw new \RuntimeException('書き出し競合ワーカーの未改行出力の超過');
                $status = proc_get_status($process);
                if (!$status['running'] && $exit === null) $exit = $status['exitcode'];
                if ($exit !== null && $exit !== 0) throw new \RuntimeException('書き出し競合ワーカーの異常終了');
                if ($exit === 0 && feof($pipes[1]) && feof($pipes[2])) {
                    if ($result === null || !$reading || $pending !== '') throw new \RuntimeException('書き出し競合ワーカーの結果通知前の終了');
                    break;
                }
                $remaining = min($phaseDeadline, $totalDeadline) - $this->monotonicTime();
                if ($remaining <= 0) throw new \RuntimeException('書き出し競合ワーカーの' . ($ready ? '実行' : '起動') . '待ち時間の超過');
                $read = array();
                foreach (array(1, 2) as $index) if (!feof($pipes[$index])) $read[] = $pipes[$index];
                $wait = max(1, (int) min(100000, $remaining * 1000000));
                if ($read) {
                    $write = null;
                    $except = null;
                    if (stream_select($read, $write, $except, 0, $wait) === false) throw new \RuntimeException('書き出し競合ワーカーのパイプ監視失敗');
                } else {
                    usleep($wait);
                }
            } while (true);
            $diagnostics = $this->redact('stdout=' . $stdout . ', stderr=' . $stderr . ', 終了状態=' . $exit);
            return array('accepted' => $accepted, 'write_error' => $this->redact($writeError), 'worker' => $result, 'diagnostics' => $diagnostics);
        } catch (\Throwable $error) {
            throw new \RuntimeException($this->redact($error->getMessage() . ', stdout=' . $stdout . ', stderr=' . $stderr . ', 終了状態=' . ($exit ?? '実行中')));
        } finally {
            foreach ($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);
            $status = proc_get_status($process);
            if ($status['running']) proc_terminate($process, 9);
            proc_close($process);
        }
    }

    private function monotonicTime(): float
    {
        return hrtime(true) / 1000000000;
    }

    private function waitForAggregationSignal(string $signal, float $deadline): void
    {
        global $wpdb;
        do {
            $owner = $wpdb->get_var($wpdb->prepare('SELECT IS_USED_LOCK(%s)', $signal));
            if ($wpdb->last_error !== '') throw new \RuntimeException('書き出し競合ワーカーの集計通知の取得失敗');
            if ($owner !== null) return;
            if ($this->monotonicTime() >= $deadline) throw new \RuntimeException('書き出し競合ワーカーの集計通知待ち時間の超過');
            usleep(10000);
        } while (true);
    }

    private function redact(string $message): string
    {
        return DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $message) : $message;
    }
}
