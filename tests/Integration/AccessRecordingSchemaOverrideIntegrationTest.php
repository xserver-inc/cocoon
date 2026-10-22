<?php
/**
 * スキーマ更新関数の従来の上書きと、PV記録の実DB互換性検証
 */
namespace Cocoon\Tests\Integration;

class AccessRecordingSchemaOverrideIntegrationTest extends IntegrationTestCase
{
    private array $accessPostIds = array();

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertTrue(create_accesses_table());
    }

    protected function tearDown(): void
    {
        global $wpdb;
        foreach ($this->accessPostIds as $id) $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id), array('%d'));
        parent::tearDown();
    }

    public function testVoidSchemaOverrideStillRecordsPvWithAValidSchema(): void
    {
        $id = $this->fixture();
        $result = $this->runOverrideWorker($id, 'void');
        $this->assertSame(array(1, false), $result['results']);
        $this->assertSame(2, $result['calls']);
        $this->assertSame(1, $this->countPv($id));
    }

    public function testExplicitSchemaFailureStillRejectsRecording(): void
    {
        $id = $this->fixture();
        $result = $this->runOverrideWorker($id, 'false');
        $this->assertSame(array(false, false), $result['results']);
        $this->assertSame(2, $result['calls']);
        $this->assertSame(0, $this->countPv($id));
    }

    private function fixture(): int
    {
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'スキーマ更新関数の上書き互換性'));
        $this->accessPostIds[] = $id;
        return $id;
    }

    private function countPv(int $id): int
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare('SELECT COALESCE(SUM(count),0) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id));
    }

    private function runOverrideWorker(int $id, string $mode): array
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_PV_SCHEMA_OVERRIDE_WORKER'] = '1';
        $environment['COCOON_PV_SCHEMA_OVERRIDE_WP_ROOT'] = ABSPATH;
        // コマンド引数への露出を避けた、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-schema-override-worker.php', (string) $id, $mode);
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('redirect', 1)), $pipes, null, $this->workerEnvironment($environment));
        $this->assertIsResource($process);
        try {
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            $output = '';
            $deadline = hrtime(true) / 1e9 + 120;
            while (!feof($pipes[1])) {
                $this->assertLessThan($deadline, hrtime(true) / 1e9, '上書き互換性ワーカーの実行時間の超過');
                $read = array($pipes[1]);
                $write = null;
                $except = null;
                $ready = stream_select($read, $write, $except, 1);
                $this->assertNotFalse($ready);
                if ($ready > 0) $output .= stream_get_contents($pipes[1]);
                $this->assertLessThanOrEqual(65536, strlen($output), '上書き互換性ワーカーの出力上限の超過');
            }
            fclose($pipes[1]);
            // ワーカーの異常終了時にも認証情報を含まない診断文への変換
            $diagnostic = str_replace(DB_PASSWORD, '[REDACTED]', substr($output, -1000));
            $this->assertSame(0, proc_close($process), $diagnostic);
            $result = json_decode(trim($output), true);
            $this->assertIsArray($result, $diagnostic);
            return $result;
        } finally {
            foreach ($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);
            if (is_resource($process)) {
                $status = proc_get_status($process);
                if ($status['running']) proc_terminate($process, 9);
                proc_close($process);
            }
        }
    }
}
