<?php
/**
 * PV並列記録テストのプロセス起動・通信・終了の管理
 */
namespace Cocoon\Tests\Support;

require_once __DIR__ . '/ProcessEnvironment.php';

class AccessRecordingWorkerPool
{
    public const READY = 'COCOON_PV_READY';
    public const RESULT = 'COCOON_PV_RESULT ';

    public function run(array $commands, array $environment, float $startupTimeout = 120, float $recordingTimeout = 60, float $totalTimeout = 180): int
    {
        if (count($commands) !== 2) throw new \InvalidArgumentException('PV記録ワーカーは2プロセスでの実行が必要');
        $workers = array();
        $deadline = $this->now() + $totalTimeout;
        $startupDeadline = min($deadline, $this->now() + $startupTimeout);
        try {
            foreach ($commands as $command) {
                // PHPエラーと開始・結果メッセージの混同を防ぐ独立パイプ
                $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, ProcessEnvironment::preserveEmptyValues($environment));
                if (!is_resource($process)) throw new \RuntimeException('PV記録ワーカーの起動失敗');
                stream_set_blocking($pipes[1], false);
                stream_set_blocking($pipes[2], false);
                $workers[] = array('process' => $process, 'pipes' => $pipes, 'pending' => '', 'stdout' => '', 'stderr' => '', 'ready' => false, 'result' => null, 'exit' => null);
            }
            // WordPressの初回読み込みと、競合を検証する記録処理との待ち時間の分離
            $this->await($workers, '起動', $startupDeadline);
            $recordingDeadline = min($deadline, $this->now() + $recordingTimeout);
            // 例外時にも後始末対象を上書きしない、参照を伴わない開始合図の送信
            foreach ($workers as $startingWorker) {
                if (@fwrite($startingWorker['pipes'][0], "START\n") !== 6) throw new \RuntimeException('PV記録ワーカーへの開始合図の送信失敗');
                fclose($startingWorker['pipes'][0]);
            }
            $this->await($workers, '記録', $recordingDeadline);
            $this->await($workers, '終了', $recordingDeadline);
            return array_sum(array_column($workers, 'result'));
        } catch (\Throwable $error) {
            $message = $error->getMessage() . $this->diagnostics($workers);
            // 接続障害の診断に含まれる可能性があるDBパスワードの伏せ字
            if (isset($environment['WORDPRESS_DB_PASSWORD']) && $environment['WORDPRESS_DB_PASSWORD'] !== '') $message = str_replace($environment['WORDPRESS_DB_PASSWORD'], '[伏せ字]', $message);
            throw new \RuntimeException($message);
        } finally {
            foreach ($workers as $worker) {
                foreach ($worker['pipes'] as $pipe) if (is_resource($pipe)) fclose($pipe);
                $status = proc_get_status($worker['process']);
                if ($status['running']) proc_terminate($worker['process'], 9);
                proc_close($worker['process']);
            }
        }
    }

    private function now(): float
    {
        // システム時刻の補正に影響されない待ち時間の測定
        return hrtime(true) / 1000000000;
    }

    private function await(array &$workers, string $phase, float $deadline): void
    {
        do {
            $complete = true;
            foreach ($workers as &$worker) {
                $this->read($worker);
                $status = proc_get_status($worker['process']);
                if (!$status['running']) {
                    // プロセス終了直前にパイプへ書き込まれた残りの応答の回収
                    $this->read($worker);
                    if ($worker['exit'] === null) $worker['exit'] = $status['exitcode'];
                    // 正常終了と結果通知だけでは検出できない、改行のない末尾出力の拒否
                    if ($worker['pending'] !== '') throw new \RuntimeException('PV記録ワーカーの予期しない標準出力（改行のない末尾）');
                }
                if ($worker['exit'] !== null && $worker['exit'] !== 0) throw new \RuntimeException('PV記録ワーカーの異常終了');
                $done = $phase === '起動' ? $worker['ready'] : ($phase === '記録' ? $worker['result'] !== null : $worker['exit'] === 0);
                if (!$done && $worker['exit'] !== null) throw new \RuntimeException('PV記録ワーカーの' . $phase . '応答前の終了');
                $complete = $complete && $done;
            }
            unset($worker);
            if ($complete) return;
            $remaining = $deadline - $this->now();
            if ($remaining <= 0) throw new \RuntimeException('PV記録ワーカーの' . $phase . '待ち時間の超過');
            $read = array();
            foreach ($workers as $worker) {
                foreach (array(1, 2) as $index) if (!feof($worker['pipes'][$index])) $read[] = $worker['pipes'][$index];
            }
            $microseconds = max(1, (int) min(100000, $remaining * 1000000));
            if (!$read) {
                usleep($microseconds);
                continue;
            }
            $write = null;
            $except = null;
            // 両ワーカーの標準出力・標準エラーを同時監視する100ミリ秒以内の待機
            if (stream_select($read, $write, $except, 0, $microseconds) === false) throw new \RuntimeException('PV記録ワーカーのパイプ監視失敗');
        } while (true);
    }

    private function read(array &$worker): void
    {
        foreach (array(1 => 'stdout', 2 => 'stderr') as $index => $name) {
            $chunk = stream_get_contents($worker['pipes'][$index]);
            if ($chunk === false) throw new \RuntimeException('PV記録ワーカーの出力読み取り失敗');
            // 大量のエラー出力による診断用メモリの増加防止
            $worker[$name] = substr($worker[$name] . $chunk, -8192);
            if ($index === 1) $worker['pending'] .= $chunk;
        }
        if ($worker['stderr'] !== '') throw new \RuntimeException('PV記録ワーカーの標準エラーへの出力');
        // 改行前の部分出力でも停止しない、非ブロッキング読み取り後の行分割
        while (($newline = strpos($worker['pending'], "\n")) !== false) {
            $line = rtrim(substr($worker['pending'], 0, $newline), "\r");
            $worker['pending'] = substr($worker['pending'], $newline + 1);
            if ($line === self::READY && !$worker['ready']) {
                $worker['ready'] = true;
            } elseif (strpos($line, self::RESULT) === 0 && $worker['ready'] && $worker['result'] === null) {
                $data = json_decode(substr($line, strlen(self::RESULT)), true);
                if (!is_array($data) || !isset($data['accepted']) || !is_int($data['accepted']) || $data['accepted'] < 0) throw new \RuntimeException('PV記録ワーカーの結果形式の不正');
                $worker['result'] = $data['accepted'];
            } elseif ($line !== '') {
                throw new \RuntimeException('PV記録ワーカーの予期しない標準出力');
            }
        }
        if (strlen($worker['pending']) > 65536) throw new \RuntimeException('PV記録ワーカーの改行のない出力の超過');
    }

    private function diagnostics(array $workers): string
    {
        $message = '';
        foreach ($workers as $index => $worker) {
            $status = proc_get_status($worker['process']);
            $exit = $worker['exit'] ?? ($status['running'] ? '実行中' : $status['exitcode']);
            $message .= "\nワーカー" . ($index + 1) . ': 終了状態=' . $exit . ', READY=' . ($worker['ready'] ? 'あり' : 'なし') . ", stdout=" . $worker['stdout'] . ', stderr=' . $worker['stderr'];
        }
        return $message;
    }
}
