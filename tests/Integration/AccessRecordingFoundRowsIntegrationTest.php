<?php
/**
 * FOUND_ROWS接続と標準接続での、実PVと記録成功の戻り値の実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessRecordingFoundRowsIntegrationTest extends IntegrationTestCase
{
    public function testFoundRowsConnectionStillReportsOnlyAcceptedViews(): void
    {
        $this->assertRecordingResults('found', 1);
    }

    public function testDefaultConnectionKeepsItsRecordingResults(): void
    {
        $this->assertRecordingResults('default', 0);
    }

    private function assertRecordingResults(string $mode, int $noopAffectedRows): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '記録結果と接続フラグ'));
        $data = $this->runWorker($id, $mode);
        $this->assertFalse($data['debug_mode']);
        $this->assertTrue($data['database_preserved']);
        $this->assertSame(array(1, false, 1, false, 1, false), array_column($data['states'], 'result'));
        $this->assertSame(array(1, 1, 2, 2, 3, 3), array_column($data['states'], 'pv'));
        $this->assertSame(array(1, $noopAffectedRows, 2, $noopAffectedRows, 2, $noopAffectedRows), array_column($data['states'], 'affected_rows'));
        $this->assertGreaterThan(0, (int) $data['states'][0]['insert_id']);
        foreach (array(1, 3, 5) as $position) $this->assertSame('0', $data['states'][$position]['insert_id']);
        $this->assertSame($data['states'][0]['last_accessed_at'], $data['states'][1]['last_accessed_at']);
        $this->assertFalse($data['session_noop']['result']);
        $this->assertSame($noopAffectedRows, $data['session_noop']['affected_rows']);
        $this->assertTrue($data['session_insert_id_preserved']);
        $this->assertFalse($data['failure']['result']);
        $this->assertSame(3, $data['failure']['pv']);
        $this->assertSame($data['session_noop']['last_accessed_at'], $data['failure']['last_accessed_at']);
        $this->assertSame(1, $data['retry']['result']);
        $this->assertSame(4, $data['retry']['pv']);
        $this->assertSame(array(1, false), array_column($data['legacy'], 'result'));
        $this->assertSame(array(8, 8), array_column($data['legacy'], 'pv'));
        $this->assertSame($data['legacy_id'], $data['legacy'][0]['id']);
        $this->assertSame(1, $data['rows']);
        $ci = $data['ipv6']['ci']['states'];
        $this->assertSame(array(1, false, false, 1, false), array_column($ci, 'result'));
        $this->assertSame(array(1, 1, 1, 2, 2), array_column($ci, 'pv'));
        $this->assertSame(array(1, $noopAffectedRows, $noopAffectedRows, 2, $noopAffectedRows), array_column($ci, 'affected_rows'));
        $this->assertSame(array_fill(0, 5, '2001:db8::a'), array_column($ci, 'last_ip'));
        $this->assertSame($ci[0]['last_accessed_at'], $ci[1]['last_accessed_at']);
        $this->assertSame($ci[0]['last_accessed_at'], $ci[2]['last_accessed_at']);
        $this->assertGreaterThan($data['ipv6']['ci']['expired_at'], $ci[3]['last_accessed_at']);
        $this->assertSame($ci[3]['last_accessed_at'], $ci[4]['last_accessed_at']);
        foreach (array(1, 2, 4) as $position) $this->assertSame('0', $ci[$position]['insert_id']);
        $binary = $data['ipv6']['binary']['states'];
        $this->assertSame(array(1, 1, 1, 1, 1), array_column($binary, 'result'));
        $this->assertSame(array(1, 2, 3, 4, 5), array_column($binary, 'pv'));
        $this->assertSame(array(1, 2, 2, 2, 2), array_column($binary, 'affected_rows'));
        $this->assertSame(array('2001:db8::a', '2001:DB8::A', '2001:db8::a', '2001:DB8::A', '2001:db8::a'), array_column($binary, 'last_ip'));
        $this->assertGreaterThan($data['ipv6']['binary']['expired_at'], $binary[3]['last_accessed_at']);
        foreach ($data['ipv6'] as $case) {
            $this->assertSame(1, $case['rows']);
            $this->assertSame(array_fill(0, 5, ''), array_column($case['states'], 'write_error'));
        }
        $nullable = $data['nullable_count']['states'];
        $this->assertSame(array(false, 1, false), array_column($nullable, 'result'));
        $this->assertSame(array(null, '1', '1'), array_column($nullable, 'raw_count'));
        $this->assertSame(array(0, 1, 1), array_column($nullable, 'pv'));
        $this->assertSame(array($noopAffectedRows, 2, $noopAffectedRows), array_column($nullable, 'affected_rows'));
        $this->assertSame(array_fill(0, 3, '192.0.2.1'), array_column($nullable, 'last_ip'));
        $this->assertSame(array_fill(0, 3, $data['nullable_count']['id']), array_column($nullable, 'id'));
        $this->assertSame($data['nullable_count']['initial_at'], $nullable[0]['last_accessed_at']);
        $this->assertGreaterThan($data['nullable_count']['expired_at'], $nullable[1]['last_accessed_at']);
        $this->assertSame($nullable[1]['last_accessed_at'], $nullable[2]['last_accessed_at']);
        foreach (array(0, 2) as $position) $this->assertSame('0', $nullable[$position]['insert_id']);
        $this->assertSame(array_fill(0, 3, ''), array_column($nullable, 'write_error'));
        $this->assertSame(1, $data['nullable_count']['rows']);
        $this->assertSame('', $data['last_error']);
        // 子プロセスの合成アクセス記録が永続テーブルに残らないことの確認
        $this->assertSame(0, (int) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d', $id)));
    }

    private function runWorker(int $id, string $mode): array
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_PV_FOUND_ROWS_WORKER'] = '1';
        $environment['COCOON_PV_FOUND_ROWS_WP_ROOT'] = ABSPATH;
        // コマンド引数へ出さない、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-found-rows-worker.php', (string) $id, $mode);
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, $this->workerEnvironment($environment));
        if (!is_resource($process)) throw new \RuntimeException('接続フラグ検証ワーカーの起動失敗');
        $stdout = '';
        $stderr = '';
        $exit = null;
        $deadline = hrtime(true) / 1e9 + 120;
        try {
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            do {
                $stdout .= stream_get_contents($pipes[1]);
                $stderr .= stream_get_contents($pipes[2]);
                if (strlen($stdout) > 65536 || strlen($stderr) > 8192) throw new \RuntimeException('接続フラグ検証ワーカーの出力上限の超過');
                $status = proc_get_status($process);
                if (!$status['running'] && $exit === null) $exit = $status['exitcode'];
                if ($exit !== null && feof($pipes[1]) && feof($pipes[2])) break;
                $remaining = $deadline - hrtime(true) / 1e9;
                if ($remaining <= 0) throw new \RuntimeException('接続フラグ検証ワーカーの待ち時間の超過');
                $read = array();
                foreach (array(1, 2) as $index) if (!feof($pipes[$index])) $read[] = $pipes[$index];
                $wait = max(1, (int) min(100000, $remaining * 1000000));
                if ($read) {
                    $write = null;
                    $except = null;
                    if (stream_select($read, $write, $except, 0, $wait) === false) throw new \RuntimeException('接続フラグ検証ワーカーのパイプ監視失敗');
                } else {
                    usleep($wait);
                }
            } while (true);
            if ($exit !== 0 || $stderr !== '') throw new \RuntimeException('接続フラグ検証ワーカーの異常終了');
            $result = json_decode(trim($stdout), true);
            if (!is_array($result)) throw new \RuntimeException('接続フラグ検証ワーカーの結果形式の不正');
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
