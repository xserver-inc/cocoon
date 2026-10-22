<?php
/**
 * 旧記録ヘルパーの上書きと、標準側の原子的記録の実DB互換性検証
 */
namespace Cocoon\Tests\Integration;

class AccessRecordingHelperOverrideIntegrationTest extends IntegrationTestCase
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

    public function testBothHelperOverridesCanRejectWritesWithoutExtraArgumentKeys(): void
    {
        $result = $this->runWorker('both-deny');
        $this->assertSame(array(false, false), array_column($result['states'], 'result'));
        $this->assertSame(array(0, 7), array_column($result['states'], 'pv'));
        $this->assertSame(array('insert' => 1, 'update' => 1), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertFalse($result['default_insert']);
        $this->assertFalse($result['default_update']);
        $this->assertLegacyArguments($result);
    }

    public function testInsertOnlyOverridePreservesTheStandardUpdateAndItsTimestamp(): void
    {
        $result = $this->runWorker('insert-deny');
        $this->assertSame(array(false, 1), array_column($result['states'], 'result'));
        $this->assertSame(array(0, 8), array_column($result['states'], 'pv'));
        $this->assertSame(array('insert' => 1, 'update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertGreaterThan(0, $result['states'][1]['last_accessed_at']);
        $this->assertFalse($result['default_insert']);
        $this->assertTrue($result['default_update']);
        $this->assertLegacyArguments($result);
    }

    public function testUpdateOnlyOverrideCanRejectWritesWithoutConsumingTheInterval(): void
    {
        $result = $this->runWorker('update-deny');
        $this->assertSame(array(1, false, false, false), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 1, 1, 1), array_column($result['states'], 'pv'));
        $this->assertSame(0, $result['states'][1]['calls']['update']);
        $this->assertSame(1, $result['states'][2]['calls']['update']);
        $this->assertSame(2, $result['states'][3]['calls']['update']);
        $this->assertSame($result['states'][2]['last_accessed_at'], $result['states'][3]['last_accessed_at']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertTrue($result['default_insert']);
        $this->assertFalse($result['default_update']);
        $this->assertLegacyArguments($result);
    }

    public function testInsertOverrideIpTransformationAndStandardRepeatExclusionArePreserved(): void
    {
        $result = $this->runWorker('insert-anonymize');
        $this->assertSame(array(1, 1, false), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 2, 2), array_column($result['states'], 'pv'));
        $this->assertSame('192.0.2.0', $result['states'][0]['last_ip']);
        $this->assertSame('192.0.2.2', $result['states'][1]['last_ip']);
        $this->assertSame(array('insert' => 1, 'update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    public function testUpdateOverrideIpTransformationAndKnownThirtySecondIntervalArePreserved(): void
    {
        $result = $this->runWorker('update-anonymize');
        $this->assertSame(array(1, 1, false, 1, false), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 2, 2, 3, 3), array_column($result['states'], 'pv'));
        $this->assertSame('192.0.2.0', $result['states'][1]['last_ip']);
        $this->assertSame('192.0.2.0', $result['states'][3]['last_ip']);
        $this->assertSame(array('insert' => 0, 'update' => 2), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    public function testLegacyOverrideWithoutATimestampIsNotPermanentlyExcluded(): void
    {
        $result = $this->runWorker('update-legacy');
        $this->assertSame(array(1, 1, 1), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 2, 3), array_column($result['states'], 'pv'));
        $this->assertSame(0, $result['states'][1]['last_accessed_at']);
        $this->assertSame(0, $result['states'][2]['last_accessed_at']);
        $this->assertSame(array('insert' => 0, 'update' => 2), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    private function assertLegacyArguments(array $result): void
    {
        foreach ($result['arguments']['insert'] as $keys) $this->assertEqualsCanonicalizing(array('post_id', 'date', 'post_type', 'last_ip', 'count'), $keys);
        foreach ($result['arguments']['update'] as $keys) $this->assertEqualsCanonicalizing(array('last_ip', 'count'), $keys);
        $this->assertSame('', $result['last_error']);
    }

    private function runWorker(string $mode): array
    {
        global $wpdb;
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '記録ヘルパーの上書き互換性'));
        $this->accessPostIds[] = $id;
        $environment = getenv();
        $environment['COCOON_PV_HELPER_OVERRIDE_WORKER'] = '1';
        $environment['COCOON_PV_HELPER_OVERRIDE_WP_ROOT'] = ABSPATH;
        // コマンド引数への露出を避けた、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-helper-override-worker.php', (string) $id, $mode);
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('redirect', 1)), $pipes, null, $this->workerEnvironment($environment));
        $this->assertIsResource($process);
        try {
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            $output = '';
            $deadline = hrtime(true) / 1e9 + 120;
            while (!feof($pipes[1])) {
                $this->assertLessThan($deadline, hrtime(true) / 1e9, '記録ヘルパー上書きワーカーの実行時間の超過');
                $read = array($pipes[1]);
                $write = null;
                $except = null;
                $ready = stream_select($read, $write, $except, 1);
                $this->assertNotFalse($ready);
                if ($ready > 0) $output .= stream_get_contents($pipes[1]);
                $this->assertLessThanOrEqual(65536, strlen($output), '記録ヘルパー上書きワーカーの出力上限の超過');
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
