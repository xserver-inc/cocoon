<?php
/**
 * 外側トランザクションを保った、低メモリのランキング書き出し検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportTransactionIntegrationTest extends IntegrationTestCase
{
    public function testOuterTransactionExportsAllRowsWithoutTemporaryTablesOrUnboundedMemory(): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '外側トランザクションの書き出し'));
        $template = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID=%d", $id), ARRAY_A);
        $result = $this->runWorker($template);
        $this->assertSame(1500, $result['rows']);
        $this->assertSame(1500, $result['unique_ids']);
        $this->assertSame($result['expected_hash'], $result['actual_hash']);
        $this->assertSame($result['expected_pv'], $result['actual_pv']);
        $this->assertTrue($result['title_and_url_preserved']);
        $this->assertSame(0, $result['temporary_queries']);
        $this->assertSame(array(), $result['transaction_queries']);
        $this->assertTrue($result['database_preserved']);
        $this->assertTrue($result['isolation_preserved']);
        $this->assertTrue($result['marker_preserved']);
        $this->assertTrue($result['marker_rolled_back']);
        $this->assertSame('10', $result['select_limit_during_export']);
        $this->assertTrue($result['select_limit_restored']);
        $this->assertTrue($result['unstarted_streams_released']);
        $this->assertTrue($result['exception_streams_released']);
        $this->assertTrue($result['completed_streams_released']);
        $this->assertTrue($result['cache_settings_restored']);
        $this->assertSame(134217728, $result['memory_limit']);
        $this->assertLessThanOrEqual(134217728, $result['memory_peak']);
        $this->assertSame('', $result['last_error']);
    }

    private function runWorker(array $template): array
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_EXPORT_TRANSACTION_WORKER'] = '1';
        $environment['COCOON_EXPORT_TRANSACTION_WP_ROOT'] = ABSPATH;
        // コマンド引数へ露出しない、テスト用DB接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, '-d', 'memory_limit=128M', dirname(__DIR__) . '/fixtures/access-export-transaction-worker.php');
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, $environment);
        if (!is_resource($process)) throw new \RuntimeException('外側トランザクション書き出しワーカーの起動失敗');
        $stdout = '';
        $stderr = '';
        $exit = null;
        $deadline = hrtime(true) / 1e9 + 240;
        try {
            $input = json_encode($template) . "\n";
            if (fwrite($pipes[0], $input) !== strlen($input)) throw new \RuntimeException('書き出しワーカーへの雛形の送信失敗');
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            do {
                $output = stream_get_contents($pipes[1]);
                $errors = stream_get_contents($pipes[2]);
                if ($output === false || $errors === false) throw new \RuntimeException('書き出しワーカーの出力読み取り失敗');
                $stdout .= $output;
                $stderr .= $errors;
                if (strlen($stdout) > 65536 || strlen($stderr) > 8192) throw new \RuntimeException('書き出しワーカーの出力上限の超過');
                $status = proc_get_status($process);
                if (!$status['running'] && $exit === null) $exit = $status['exitcode'];
                if ($exit !== null && feof($pipes[1]) && feof($pipes[2])) break;
                $remaining = $deadline - hrtime(true) / 1e9;
                if ($remaining <= 0) throw new \RuntimeException('書き出しワーカーの待ち時間の超過');
                $read = array();
                foreach (array(1, 2) as $index) if (!feof($pipes[$index])) $read[] = $pipes[$index];
                $wait = max(1, (int) min(100000, $remaining * 1000000));
                if ($read) {
                    $write = null;
                    $except = null;
                    if (stream_select($read, $write, $except, 0, $wait) === false) throw new \RuntimeException('書き出しワーカーのパイプ監視失敗');
                } else {
                    usleep($wait);
                }
            } while (true);
            if ($exit !== 0 || $stderr !== '') throw new \RuntimeException('書き出しワーカーの異常終了');
            $result = json_decode(trim($stdout), true);
            if (!is_array($result)) throw new \RuntimeException('書き出しワーカーの結果形式の不正');
            return $result;
        } catch (\Throwable $error) {
            $message = $error->getMessage() . ', 終了状態=' . ($exit ?? '実行中') . ', stdout=' . $stdout . ', stderr=' . $stderr;
            throw new \RuntimeException(DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $message) : $message);
        } finally {
            foreach ($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);
            $status = proc_get_status($process);
            if ($status['running']) proc_terminate($process, 9);
            proc_close($process);
        }
    }
}
