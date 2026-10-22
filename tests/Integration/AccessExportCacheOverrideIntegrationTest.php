<?php
/**
 * キャッシュ関数の上書きによる除外・補正を維持する実DB・書き出しハンドラー検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportCacheOverrideIntegrationTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertTrue(create_accesses_table());
        // 管理画面の起動時に参照する、テスト専用DB内のテーマ専用テーブルの準備
        create_speech_balloons_table();
        create_function_texts_table();
        create_affiliate_tags_table();
        create_item_rankings_table();
    }

    /**
     * キャッシュ関数による全件除外と画面・CSV・JSONの空結果の一致確認
     */
    public function testCacheOverrideCanExcludeAllRankingRowsFromTheScreenAndBothExports(): void
    {
        $this->assertOverrideMatchesExports('empty');
    }

    /**
     * キャッシュ関数による一部除外・PVの型と値・独自順序の出力互換性確認
     */
    public function testCacheOverridePreservesExclusionPvTypesAndOrderInBothExports(): void
    {
        $this->assertOverrideMatchesExports('custom');
    }

    /**
     * キャッシュ関数の独自500件と従来の引数・1回呼び出し契約の維持確認
     */
    public function testCacheOverrideReturningFiveHundredRowsIsCalledOnlyOnce(): void
    {
        $this->assertOverrideMatchesExports('full');
    }

    private function assertOverrideMatchesExports(string $mode): void
    {
        global $wpdb;
        $ids = array();
        try {
            // ランキング本体を置換しない、公開記事3件の実DB集計と下位キャッシュだけの上書き
            foreach (array(9, 5, 2) as $offset => $pv) {
                $id = $this->createPost(array('post_status' => 'publish', 'post_title' => 'キャッシュ上書き &amp; 引用 &quot;' . $offset . '&quot;'));
                $ids[] = $id;
                $this->assertNotFalse(insert_accesses_record(array('post_id' => $id, 'date' => '2098-10-15', 'post_type' => 'post', 'count' => $pv, 'last_ip' => '192.0.2.1')));
            }
            $expected = array();
            if ($mode === 'custom') {
                $expected = array(
                    array('post_id' => (string) $ids[2], 'post_type' => 'cache_custom', 'pv' => '17'),
                    array('post_id' => $ids[0], 'post_type' => 'post', 'pv' => 41.75),
                );
            } elseif ($mode === 'full') {
                for ($i = 0; $i < 500; $i++) $expected[] = array('post_id' => 1700000000 + $i, 'post_type' => 'virtual_cache', 'pv' => $i + 1);
            }
            $screen = json_decode($this->runWorker($mode, 'screen', $ids), true);
            $this->assertIsArray($screen);
            $this->assertSame($expected, $screen['rows']);
            preg_match_all('/class="cocoon-analytics-ranking-pv-num">([^<]*)<\//', $screen['html'], $pvMatches);
            $screenPv = array_map(static function ($value) { return (int) str_replace(',', '', $value); }, $pvMatches[1]);
            $this->assertSame(array_map('intval', array_column($expected, 'pv')), $screenPv);
            preg_match_all('/class="cocoon-analytics-badge">([^<]*)<\//', $screen['html'], $typeMatches);
            $this->assertSame(array_column($expected, 'post_type'), $typeMatches[1]);

            $headers = array('post_id', 'post_type', 'title', 'pv', 'url');
            $expectedRows = array();
            foreach ($expected as $row) {
                $post = get_post($row['post_id']);
                $expectedRows[] = array($row['post_id'], $row['post_type'], $post ? cocoon_analytics_plain_title($post) : '', $row['pv'], $post ? get_permalink($post) : false);
            }
            $csv = $this->runWorker($mode, 'csv', $ids);
            $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
            $stream = fopen('php://temp', 'w+');
            try {
                fwrite($stream, substr($csv, 3));
                rewind($stream);
                $this->assertSame($headers, fgetcsv($stream, 0, ',', '"', '\\'));
                $csvRows = array();
                while (($row = fgetcsv($stream, 0, ',', '"', '\\')) !== false) $csvRows[] = $row;
                $expectedCsv = array_map(static function ($row) { return array_map('strval', $row); }, $expectedRows);
                $this->assertSame($expectedCsv, $csvRows);
            } finally {
                fclose($stream);
            }
            $json = json_decode($this->runWorker($mode, 'json', $ids), true);
            $this->assertIsArray($json);
            $this->assertSame($headers, $json['columns']);
            $this->assertSame('ranking', $json['meta']['target']);
            $this->assertSame('2098-10-15', $json['meta']['from']);
            $this->assertSame('2098-10-15', $json['meta']['to']);
            $expectedJson = array_map(static function ($row) use ($headers) { return array_combine($headers, $row); }, $expectedRows);
            $this->assertSame($expectedJson, $json['data']);
        } finally {
            foreach ($ids as $id) $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id), array('%d'));
        }
    }

    private function runWorker(string $mode, string $format, array $ids): string
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_EXPORT_CACHE_OVERRIDE_WORKER'] = '1';
        $environment['COCOON_EXPORT_CACHE_OVERRIDE_WP_ROOT'] = ABSPATH;
        // コマンド引数や診断への露出を避けた、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $command = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-export-cache-override-worker.php', $mode, $format);
        foreach ($ids as $id) $command[] = (string) $id;
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, $this->workerEnvironment($environment));
        $this->assertIsResource($process);
        $stdout = '';
        $stderr = '';
        $exit = null;
        $deadline = hrtime(true) / 1e9 + 120;
        try {
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            do {
                $output = stream_get_contents($pipes[1]);
                $errors = stream_get_contents($pipes[2]);
                if ($output === false || $errors === false) throw new \RuntimeException('キャッシュ上書きワーカーの出力読み取り失敗');
                $stdout .= $output;
                $stderr .= $errors;
                $this->assertLessThanOrEqual(4194304, strlen($stdout) + strlen($stderr), 'キャッシュ上書きワーカーの出力上限の超過');
                $status = proc_get_status($process);
                if (!$status['running'] && $exit === null) $exit = $status['exitcode'];
                if ($exit !== null && feof($pipes[1]) && feof($pipes[2])) break;
                $remaining = $deadline - hrtime(true) / 1e9;
                $this->assertGreaterThan(0, $remaining, 'キャッシュ上書きワーカーの実行時間の超過');
                $read = array();
                foreach (array(1, 2) as $index) if (!feof($pipes[$index])) $read[] = $pipes[$index];
                $wait = max(1, (int) min(100000, $remaining * 1000000));
                if ($read) {
                    $write = null;
                    $except = null;
                    $this->assertNotFalse(stream_select($read, $write, $except, 0, $wait));
                } else {
                    usleep($wait);
                }
            } while (true);
            $diagnostic = $this->redact(substr($stderr, -2000));
            $this->assertSame(0, $exit, $diagnostic);
            $this->assertMatchesRegularExpression('/\ACOCOON_EXPORT_CACHE_OVERRIDE ([^\r\n]+)\r?\n\z/', $this->redact($stderr), $diagnostic);
            $metadata = json_decode(substr(trim($stderr), strlen('COCOON_EXPORT_CACHE_OVERRIDE ')), true);
            $this->assertIsArray($metadata, $diagnostic);
            $this->assertTrue($metadata['default_ranking']);
            $this->assertFalse($metadata['default_cached']);
            $this->assertSame(array(array('ranking', '2098-10-15', '2098-10-15', null, 500, 0)), $metadata['calls']);
            $this->assertSame(array(3), $metadata['producer_rows']);
            $this->assertSame(0, $metadata['snapshot_queries']);
            return $stdout;
        } finally {
            foreach ($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);
            $status = proc_get_status($process);
            if ($status['running']) proc_terminate($process, 9);
            proc_close($process);
        }
    }

    private function redact(string $value): string
    {
        return DB_PASSWORD !== '' ? str_replace(DB_PASSWORD, '[伏せ字]', $value) : $value;
    }
}
