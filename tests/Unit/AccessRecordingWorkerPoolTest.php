<?php
/**
 * PV並列ワーカーの待機期限・部分出力・異常終了の回帰テスト
 */
namespace Cocoon\Tests\Unit;

use Cocoon\Tests\Support\AccessRecordingWorkerPool;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/Support/AccessRecordingWorkerPool.php';

class AccessRecordingWorkerPoolTest extends TestCase
{
    protected function setUp(): void
    {
        // Windowsのパイプ監視の非対応によるDockerテスト環境への限定
        if (PHP_OS_FAMILY === 'Windows') $this->markTestSkipped('プロセス間パイプの監視はDockerのLinux環境で検証');
    }

    private function runChild(string $source, float $startupTimeout = 3, float $recordingTimeout = 2, float $totalTimeout = 5, array $extraEnvironment = array()): int
    {
        $command = array(PHP_BINARY, '-r', $source);
        return (new AccessRecordingWorkerPool())->run(array($command, $command), array_merge(getenv(), $extraEnvironment), $startupTimeout, $recordingTimeout, $totalTimeout);
    }

    private function readySource(): string
    {
        return 'fwrite(STDOUT, "COCOON_PV_READY\\n"); fflush(STDOUT); fgets(STDIN);';
    }

    private function resultSource(): string
    {
        return 'fwrite(STDOUT, "COCOON_PV_RESULT {\\"accepted\\":1}\\n"); fflush(STDOUT);';
    }

    public function testPartialMessagesAreCollectedWithoutBlockingOnNewlines(): void
    {
        $source = 'fwrite(STDOUT, "COCOON_PV_RE"); fflush(STDOUT); usleep(20000); fwrite(STDOUT, "ADY\\n"); fflush(STDOUT); fgets(STDIN);'
            . 'fwrite(STDOUT, "COCOON_PV_RESULT {\\"accepted\\":"); fflush(STDOUT); usleep(20000); fwrite(STDOUT, "1}\\n");';
        $this->assertSame(2, $this->runChild($source));
    }

    public function testWordPressStartupHasItsOwnLongerDeadline(): void
    {
        $this->assertSame(2, $this->runChild('usleep(800000);' . $this->readySource() . $this->resultSource(), 3, 0.5, 4));
    }

    public function testPartialLineCannotBypassTheStartupDeadline(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('起動待ち時間の超過');
        $this->runChild('fwrite(STDOUT, "COCOON_PV_RE"); fflush(STDOUT); sleep(10);', 0.2, 2, 3);
    }

    public function testRecordingHasAFiniteDeadlineAfterBothWorkersAreReady(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('記録待ち時間の超過');
        $this->runChild($this->readySource() . 'sleep(10);', 3, 0.2, 4);
    }

    public function testTotalDeadlineAlsoLimitsTheLongerPhaseDeadlines(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('待ち時間の超過');
        $this->runChild('sleep(10);', 3, 3, 0.2);
    }

    public function testAbnormalExitIsReportedWithoutWaitingForTheDeadline(): void
    {
        try {
            $this->runChild('exit(7);');
            $this->fail('異常終了の見落とし');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('異常終了', $error->getMessage());
            $this->assertStringContainsString('終了状態=7', $error->getMessage());
        }
    }

    public function testStderrIsDiagnosedSeparatelyWithTheDatabasePasswordRedacted(): void
    {
        try {
            $this->runChild('fwrite(STDERR, "simulated warning secret-pv-test"); sleep(10);', 3, 2, 5, array('WORDPRESS_DB_PASSWORD' => 'secret-pv-test'));
            $this->fail('標準エラーの見落とし');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('標準エラー', $error->getMessage());
            $this->assertStringContainsString('simulated warning [伏せ字]', $error->getMessage());
            $this->assertStringNotContainsString('secret-pv-test', (string) $error);
        }
    }

    public function testUnexpectedStdoutIsReportedAsAProtocolFailure(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('予期しない標準出力');
        $this->runChild('fwrite(STDOUT, "unexpected output\\n"); sleep(10);');
    }

    public function testUnterminatedStdoutAfterResultIsRejectedAtProcessExit(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('予期しない標準出力（改行のない末尾）');
        $this->runChild($this->readySource() . $this->resultSource() . 'fwrite(STDOUT, "unexpected output");');
    }

    public function testDelayedUnterminatedStdoutAfterResultIsRejectedAtProcessExit(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('予期しない標準出力（改行のない末尾）');
        // 結果取得後の待機と複数回の部分出力にまたがる、未改行出力の検証
        $source = $this->readySource() . $this->resultSource()
            . 'usleep(50000); fwrite(STDOUT, "unexpected "); fflush(STDOUT);'
            . 'usleep(50000); fwrite(STDOUT, "output"); fflush(STDOUT); usleep(50000);';
        $this->runChild($source);
    }

    public function testUnterminatedReadyMessageIsRejectedAtProcessExit(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('予期しない標準出力（改行のない末尾）');
        $this->runChild('fwrite(STDOUT, "COCOON_PV_READY");');
    }

    public function testResultIsNotAcceptedWhenTheProcessFailsAfterWritingIt(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('異常終了');
        $this->runChild($this->readySource() . $this->resultSource() . 'exit(9);');
    }

    public function testSecondStartFailurePreservesTheErrorAndStopsBothProcesses(): void
    {
        if (!function_exists('posix_kill')) $this->markTestSkipped('子プロセスの終了確認にPOSIX拡張が必要');
        // 通常の応答形式を変えない、ローカル接続経由での子プロセスIDの取得
        $server = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);
        $this->assertIsResource($server, $errorMessage);
        $address = 'tcp://' . stream_socket_get_name($server, false);
        $announcePid = '$socket = stream_socket_client(' . var_export($address, true) . ', $code, $message, 1);'
            . 'if (!$socket) exit(3); fwrite($socket, getmypid() . "\\n"); fclose($socket);';
        // 第1ワーカーの標準入力の維持と、第2ワーカーだけの開始送信の拒否
        $ready = 'fwrite(STDOUT, "COCOON_PV_READY\\n"); fflush(STDOUT); fclose(STDOUT); fclose(STDERR);';
        $commands = array(
            array(PHP_BINARY, '-r', $announcePid . $ready . 'sleep(3);'),
            array(PHP_BINARY, '-r', 'fclose(STDIN);' . $announcePid . $ready . 'sleep(3);'),
        );
        $pids = array();
        $failure = null;
        try {
            try {
                (new AccessRecordingWorkerPool())->run($commands, getenv(), 3, 2, 5);
            } catch (\Throwable $error) {
                $failure = $error;
            }
            foreach (array(1, 2) as $index) {
                $connection = stream_socket_accept($server, 1);
                $this->assertIsResource($connection, 'ワーカー' . $index . 'のプロセスIDの取得失敗');
                try {
                    stream_set_timeout($connection, 1);
                    $pid = trim((string) fgets($connection));
                    $this->assertMatchesRegularExpression('/^[1-9][0-9]*$/', $pid);
                    $pids[] = (int) $pid;
                } finally {
                    fclose($connection);
                }
            }
            $this->assertInstanceOf(\RuntimeException::class, $failure);
            $this->assertStringContainsString('開始合図の送信失敗', $failure->getMessage());
            $this->assertStringContainsString('ワーカー1: 終了状態=実行中', $failure->getMessage());
            $this->assertStringContainsString('ワーカー2: 終了状態=実行中', $failure->getMessage());
            $this->assertCount(2, array_unique($pids));
            foreach ($pids as $pid) $this->assertFalse(@posix_kill($pid, 0), '子プロセスの終了処理の不足: ' . $pid);
        } finally {
            // 旧実装でテストが失敗した場合にも残さない、今回起動した子プロセスだけの停止
            foreach ($pids as $pid) if (@posix_kill($pid, 0)) @posix_kill($pid, 9);
            fclose($server);
        }
    }
}
