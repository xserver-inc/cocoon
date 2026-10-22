<?php
/**
 * 汎用DB保存ヘルパーの上書きと、上位ヘルパーとの交差構成の実DB互換性検証
 */
namespace Cocoon\Tests\Integration;

class AccessRecordingLowerHelperOverrideIntegrationTest extends IntegrationTestCase
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

    public function testBothGenericHelperOverridesCanRejectWrites(): void
    {
        $result = $this->runWorker('both-deny');
        $this->assertSame(array(false, false), array_column($result['states'], 'result'));
        $this->assertSame(array(0, 7), array_column($result['states'], 'pv'));
        $this->assertSame(array('insert' => 1, 'update' => 1, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertFalse($result['default_insert']);
        $this->assertFalse($result['default_update']);
        $this->assertLegacyArguments($result);
    }

    public function testInsertOnlyGenericOverridePreservesTheStandardUpdateAndItsTimestamp(): void
    {
        $result = $this->runWorker('insert-deny');
        $this->assertSame(array(false, 1), array_column($result['states'], 'result'));
        $this->assertSame(array(0, 8), array_column($result['states'], 'pv'));
        $this->assertSame(array('insert' => 1, 'update' => 0, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertGreaterThan(0, $result['states'][1]['last_accessed_at']);
        $this->assertFalse($result['default_insert']);
        $this->assertTrue($result['default_update']);
        $this->assertLegacyArguments($result);
    }

    public function testUpdateOnlyGenericOverrideCanRejectWithoutConsumingTheInterval(): void
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

    public function testGenericInsertIpTransformationAndStandardRepeatExclusionArePreserved(): void
    {
        $result = $this->runWorker('insert-anonymize');
        $this->assertSame(array(1, 1, false), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 2, 2), array_column($result['states'], 'pv'));
        $this->assertSame('192.0.2.0', $result['states'][0]['last_ip']);
        $this->assertSame(0, $result['states'][0]['last_accessed_at']);
        $this->assertSame('192.0.2.2', $result['states'][1]['last_ip']);
        $this->assertSame(array('insert' => 1, 'update' => 0, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    public function testGenericUpdateIpTransformationAndKnownThirtySecondIntervalArePreserved(): void
    {
        $result = $this->runWorker('update-anonymize');
        $this->assertAnonymizedUpdate($result);
        $this->assertSame(array('insert' => 0, 'update' => 2, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
    }

    public function testBothGenericOverridesPreserveTheUpdateTransformation(): void
    {
        $result = $this->runWorker('both-anonymize');
        $this->assertAnonymizedUpdate($result);
        $this->assertSame(array('insert' => 1, 'update' => 2, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
    }

    public function testGenericOverrideWithoutATimestampIsNotPermanentlyExcluded(): void
    {
        $result = $this->runWorker('update-legacy');
        $this->assertSame(array(1, 1, 1), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 2, 3), array_column($result['states'], 'pv'));
        $this->assertSame(0, $result['states'][1]['last_accessed_at']);
        $this->assertSame(0, $result['states'][2]['last_accessed_at']);
        $this->assertSame(array('insert' => 0, 'update' => 2, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    public function testGenericInsertAndUpperUpdateOverridesAreBothRespected(): void
    {
        $result = $this->runWorker('lower-insert-upper-update');
        $this->assertAnonymizedUpdate($result);
        $this->assertSame(array('insert' => 1, 'update' => 0, 'upper_insert' => 0, 'upper_update' => 2), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
    }

    public function testUpperInsertAndGenericUpdateOverridesAreBothRespected(): void
    {
        $result = $this->runWorker('upper-insert-lower-update');
        $this->assertAnonymizedUpdate($result);
        $this->assertSame(array('insert' => 0, 'update' => 2, 'upper_insert' => 1, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
    }

    public function testConcurrentInsertCannotTurnTheStandardInsertIntoADeniedUpdate(): void
    {
        $result = $this->runWorker('race-insert-update-deny');
        $this->assertTrue($result['race_triggered']);
        $this->assertSame(array(1), array_column($result['states'], 'result'));
        // 追加済みの相手側の7PVを保持した、選択済みINSERTだけの実行結果
        $this->assertSame(array(7, 1), $result['row_counts']);
        $this->assertSame(array('insert' => 0, 'update' => 0, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    public function testConcurrentDeleteCannotTurnTheStandardUpdateIntoADeniedInsert(): void
    {
        $result = $this->runWorker('race-delete-insert-deny');
        $this->assertTrue($result['race_triggered']);
        $this->assertSame(array(0), array_column($result['states'], 'result'));
        $this->assertSame(array(), $result['row_counts']);
        $this->assertSame(array('insert' => 0, 'update' => 0, 'upper_insert' => 0, 'upper_update' => 0), $result['calls']);
        $this->assertSame(0, $result['atomic_writes']);
        $this->assertLegacyArguments($result);
    }

    private function assertAnonymizedUpdate(array $result): void
    {
        $this->assertSame(array(1, 1, false, 1, false), array_column($result['states'], 'result'));
        $this->assertSame(array(1, 2, 2, 3, 3), array_column($result['states'], 'pv'));
        $this->assertSame('192.0.2.0', $result['states'][1]['last_ip']);
        $this->assertSame('192.0.2.0', $result['states'][3]['last_ip']);
        $this->assertLegacyArguments($result);
    }

    private function assertLegacyArguments(array $result): void
    {
        // データの項目と書式の位置関係まで対象とした、従来の下位ヘルパー契約の確認
        foreach ($result['arguments']['insert'] as $arguments) {
            $this->assertSame(array('post_id', 'date', 'post_type', 'count', 'last_ip'), $arguments['keys']);
            $this->assertSame(array('%d', '%s', '%s', '%d', '%s'), $arguments['format']);
        }
        foreach ($result['arguments']['update'] as $arguments) {
            $this->assertSame(array('count', 'last_ip'), $arguments['keys']);
            $this->assertSame(array('%d', '%s'), $arguments['format']);
            $this->assertSame(array('id'), $arguments['where']);
            $this->assertSame(array('%d'), $arguments['where_format']);
        }
        foreach ($result['arguments']['upper_insert'] as $keys) $this->assertEqualsCanonicalizing(array('post_id', 'date', 'post_type', 'last_ip', 'count'), $keys);
        foreach ($result['arguments']['upper_update'] as $keys) $this->assertEqualsCanonicalizing(array('last_ip', 'count'), $keys);
        $this->assertSame('', $result['last_error']);
    }

    private function runWorker(string $mode): array
    {
        global $wpdb;
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => '汎用DB保存ヘルパーの上書き互換性'));
        $this->accessPostIds[] = $id;
        $environment = getenv();
        $environment['COCOON_PV_LOWER_HELPER_OVERRIDE_WORKER'] = '1';
        $environment['COCOON_PV_LOWER_HELPER_OVERRIDE_WP_ROOT'] = ABSPATH;
        // コマンド引数への露出を避けた、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-lower-helper-override-worker.php', (string) $id, $mode);
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('redirect', 1)), $pipes, null, $environment);
        $this->assertIsResource($process);
        try {
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            $output = '';
            $deadline = hrtime(true) / 1e9 + 120;
            while (!feof($pipes[1])) {
                $this->assertLessThan($deadline, hrtime(true) / 1e9, '汎用DBヘルパー上書きワーカーの実行時間の超過');
                $read = array($pipes[1]);
                $write = null;
                $except = null;
                $ready = stream_select($read, $write, $except, 1);
                $this->assertNotFalse($ready);
                if ($ready > 0) $output .= stream_get_contents($pipes[1]);
                $this->assertLessThanOrEqual(65536, strlen($output), '汎用DBヘルパー上書きワーカーの出力上限の超過');
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
