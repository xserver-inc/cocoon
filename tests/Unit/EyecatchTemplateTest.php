<?php
/**
 * アイキャッチの表示条件とキャプション取得の回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;

class EyecatchTemplateTest extends TestCase
{
    private const PROCESS_OUTPUT_LIMIT = 2097152;

    /**
     * 投稿の表示条件に応じたラベル・キャプションの出力と子設定の維持を確認するテスト
     */
    #[DataProvider('singularProvider')]
    public function test_投稿の表示条件と保存済み子設定の維持(array $condition): void
    {
        $result = $this->renderTemplate('eye-catch.php', $condition);
        $visible = $condition['parent'] && $condition['image'];
        $captionVisible = $visible && $condition['captionEnabled'];
        $labelVisible = $visible && $condition['labelEnabled'] && $condition['single'];

        $this->assertSame($captionVisible ? 1 : 0, $result['captionReads']);
        $this->assertSame($labelVisible ? 1 : 0, $result['labelReads']);
        $this->assertSame($captionVisible && $condition['caption'] !== '', str_contains($result['html'], '<figcaption'));
        $this->assertSame($labelVisible, str_contains($result['html'], 'カテゴリーラベル'));
        $this->assertSame(!$visible, str_contains($result['html'], 'eye-catch-wrap display-none'));
        $this->assertStringNotContainsString('bbpress-forums', $result['html']);
        $this->assertStringContainsString('<meta itemprop="url"', $result['html']);
        $this->assertSame($condition['labelEnabled'], $result['savedLabel']);
        $this->assertSame($condition['captionEnabled'], $result['savedCaption']);
        $this->assertSame($result['beforeOptions'], $result['afterOptions']);

        if ($captionVisible && $condition['caption'] !== '') {
            $this->assertStringContainsString($condition['caption'], $result['html']);
        }
    }

    public static function singularProvider(): array
    {
        $base = ['parent' => true, 'image' => true, 'labelEnabled' => true, 'captionEnabled' => true, 'single' => true, 'caption' => '画像の説明'];

        return [
            '親OFF・画像あり・子ON' => [array_replace($base, ['parent' => false])],
            '親OFF・画像なし・子ON' => [array_replace($base, ['parent' => false, 'image' => false])],
            '親ON・画像なし・子ON' => [array_replace($base, ['image' => false])],
            '親ON・画像あり・子ON' => [$base],
            'キャプションのみOFF' => [array_replace($base, ['captionEnabled' => false])],
            'ラベルのみOFF' => [array_replace($base, ['labelEnabled' => false])],
            '子設定が両方OFF' => [array_replace($base, ['labelEnabled' => false, 'captionEnabled' => false])],
            '空キャプション' => [array_replace($base, ['caption' => ''])],
            '固定ページのカテゴリーラベル除外' => [array_replace($base, ['single' => false])],
        ];
    }

    /**
     * カテゴリーとタグの非表示時の取得停止および表示時のキャプション維持を確認するテスト
     */
    #[DataProvider('taxonomyProvider')]
    public function test_カテゴリーとタグの表示条件と保存済み子設定の維持(string $template, array $condition): void
    {
        $result = $this->renderTemplate($template, $condition);
        $captionVisible = $condition['parent'] && $condition['image'] && $condition['captionEnabled'];
        $labelVisible = $template === 'category-content.php' && $condition['parent'] && $condition['image'] && $condition['labelEnabled'];

        $this->assertSame($captionVisible ? 1 : 0, $result['captionReads']);
        $this->assertSame($captionVisible && $condition['caption'] !== '', str_contains($result['html'], '<figcaption'));
        $this->assertSame($labelVisible, str_contains($result['html'], 'カテゴリーラベル'));
        $this->assertSame($condition['labelEnabled'], $result['savedLabel']);
        $this->assertSame($condition['captionEnabled'], $result['savedCaption']);
        $this->assertSame($result['beforeOptions'], $result['afterOptions']);

        if ($captionVisible && $condition['caption'] !== '') {
            $this->assertStringContainsString(htmlspecialchars($condition['caption'], ENT_QUOTES, 'UTF-8'), $result['html']);
        }
    }

    public static function taxonomyProvider(): array
    {
        $base = ['parent' => true, 'image' => true, 'labelEnabled' => true, 'captionEnabled' => true, 'single' => false, 'caption' => '画像の説明 & <リンク>'];
        $cases = [];

        foreach (['category-content.php', 'tag-content.php'] as $template) {
            $cases[$template . ' 親OFF・子ON'] = [$template, array_replace($base, ['parent' => false])];
            $cases[$template . ' 親ON・子ON'] = [$template, $base];
            $cases[$template . ' 子設定OFF'] = [$template, array_replace($base, ['labelEnabled' => false, 'captionEnabled' => false])];
            $cases[$template . ' 画像なし'] = [$template, array_replace($base, ['image' => false])];
            $cases[$template . ' 空キャプション'] = [$template, array_replace($base, ['caption' => ''])];
        }

        return $cases;
    }

    /**
     * パイプ容量を超える両出力を待ち合わせなしで回収することの確認
     */
    public function test_大量の標準出力と標準エラーの回収(): void
    {
        $result = $this->runPhp('fwrite(STDERR, str_repeat("e", 1048576)); fwrite(STDOUT, str_repeat("o", 1048576));');

        $this->assertSame(0, $result['exitcode']);
        $this->assertSame(1048576, strlen($result['stdout']));
        $this->assertSame(1048576, strlen($result['stderr']));
        $this->assertSame('o', substr($result['stdout'], -1));
        $this->assertSame('e', substr($result['stderr'], -1));
    }

    /**
     * 終了状態の監視後も子PHPの異常終了コードを保持することの確認
     */
    public function test_子プロセスの異常終了コードの保持(): void
    {
        $result = $this->runPhp('fwrite(STDERR, "意図的な異常終了"); exit(23);');

        $this->assertSame(23, $result['exitcode']);
        $this->assertSame('', $result['stdout']);
        $this->assertSame('意図的な異常終了', $result['stderr']);
    }

    /**
     * 実行期限を超える子PHPを終了して検証失敗へ変換することの確認
     */
    public function test_子プロセスの実行期限超過の検出(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('子PHPの待ち時間の超過');

        $started = hrtime(true);
        try {
            $this->runPhp('usleep(10000000);', [], 0.3);
        } finally {
            $this->assertLessThan(5.0, (hrtime(true) - $started) / 1000000000, '子PHP終了処理の待ち時間');
        }
    }

    /**
     * 過剰な子PHPの出力を無制限に読み込まないことの確認
     */
    public function test_子プロセスの出力上限超過の検出(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('子PHPの出力上限の超過');

        $this->runPhp('fwrite(STDERR, str_repeat("x", 2097153));');
    }

    /**
     * 正常終了した子PHPの警告も描画結果の検証失敗とすることの確認
     */
    public function test_子PHPの警告を正常終了時にも検出(): void
    {
        $result = $this->runPhp('trigger_error("検証用の警告", E_USER_WARNING); echo "{}";');
        $this->assertSame(0, $result['exitcode']);
        $this->assertSame('{}', $result['stdout']);
        $this->assertStringContainsString('検証用の警告', $result['stderr']);
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('子PHPの標準エラーへの出力');

        $this->assertSuccessfulProcess($result);
    }

    private function renderTemplate(string $template, array $condition): array
    {
        // 既存bootstrapの固定スタブと干渉しない独立PHPプロセスでの実テンプレート検証
        $script = <<<'PHP'
$root = $argv[1];
$case = json_decode(base64_decode($argv[2]), true, 512, JSON_THROW_ON_ERROR);
require $root . '/vendor/autoload.php';
\Brain\Monkey\setUp();
define('ABSPATH', $root . '/');

try {
    $options = [
        'eyecatch_visible' => $case['parent'],
        'eyecatch_label_visible' => $case['labelEnabled'],
        'eyecatch_caption_visible' => $case['captionEnabled'],
    ];
    $beforeOptions = $options;
    $captionReads = 0;
    $labelReads = 0;

    // 実際の設定取得関数を通す保存済みオプションの代替
    \Brain\Monkey\Functions\when('get_theme_option')->alias(static function ($name, $default = null) use (&$options) {
        return $options[$name] ?? $default;
    });
    \Brain\Monkey\Functions\when('update_theme_option')->alias(static function () {
        throw new \RuntimeException('表示処理による設定値の書き換え');
    });
    require $root . '/lib/page-settings/image-funcs.php';

    \Brain\Monkey\Functions\when('has_post_thumbnail')->justReturn($case['image']);
    \Brain\Monkey\Functions\when('get_post_thumbnail_id')->justReturn($case['image'] ? 77 : 0);
    \Brain\Monkey\Functions\when('wp_get_attachment_image_src')->justReturn(['https://example.com/eyecatch.png', 800, 600]);
    \Brain\Monkey\Functions\when('the_post_thumbnail')->alias(static function () {
        echo '<img src="https://example.com/eyecatch.png" width="800" height="600">';
    });
    \Brain\Monkey\Functions\when('get_singular_eyecatch_image_url')->justReturn('https://example.com/no-image.png');
    \Brain\Monkey\Functions\when('get_image_width_and_height')->justReturn(['width' => 800, 'height' => 600]);
    \Brain\Monkey\Functions\when('is_single')->justReturn($case['single']);
    \Brain\Monkey\Functions\when('the_nolink_category')->alias(static function () use (&$labelReads) {
        $labelReads++;
        echo '<span class="cat-label">カテゴリーラベル</span>';
    });
    \Brain\Monkey\Functions\when('get_the_post_thumbnail_caption')->alias(static function () use (&$captionReads, $case) {
        $captionReads++;
        return $case['caption'];
    });

    // 画像未設定時の誤参照を検出するbbPress互換投稿の抜粋
    \Brain\Monkey\Functions\when('get_post')->justReturn((object) ['post_excerpt' => '<div id="bbpress-forums">フォーラム本文</div>']);
    \Brain\Monkey\Functions\when('get_query_var')->justReturn(13);
    \Brain\Monkey\Functions\when('get_queried_object_id')->justReturn(13);
    \Brain\Monkey\Functions\when('get_the_category_eye_catch_url')->justReturn($case['image'] ? 'https://example.com/category.png' : '');
    \Brain\Monkey\Functions\when('get_the_tag_eye_catch_url')->justReturn($case['image'] ? 'https://example.com/tag.png' : '');
    \Brain\Monkey\Functions\when('get_the_category_content')->justReturn('');
    \Brain\Monkey\Functions\when('get_the_tag_content')->justReturn('');
    \Brain\Monkey\Functions\when('get_additional_entry_content_classes')->justReturn('');
    \Brain\Monkey\Functions\when('cocoon_template_part')->justReturn(null);
    \Brain\Monkey\Functions\when('single_cat_title')->justReturn('カテゴリーラベル');
    \Brain\Monkey\Functions\when('get_caption_from_image_url')->alias(static function () use (&$captionReads, $case) {
        $captionReads++;
        return $case['caption'];
    });
    \Brain\Monkey\Functions\when('is_large_pr_labels_visible')->justReturn(false);
    \Brain\Monkey\Functions\when('esc_url')->returnArg();
    \Brain\Monkey\Functions\when('esc_html')->alias(static fn ($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));

    // 例外発生時も出力バッファーを残さない本番テンプレートの読み込み
    ob_start();
    try {
        include $root . '/tmp/' . $case['template'];
        $html = ob_get_contents();
    } finally {
        ob_end_clean();
    }

    echo json_encode([
        'html' => $html,
        'captionReads' => $captionReads,
        'labelReads' => $labelReads,
        'savedLabel' => is_eyecatch_label_visible(),
        'savedCaption' => is_eyecatch_caption_visible(),
        'beforeOptions' => $beforeOptions,
        'afterOptions' => $options,
    ], JSON_THROW_ON_ERROR);
} finally {
    \Brain\Monkey\tearDown();
}
PHP;

        $root = dirname(__DIR__, 2);
        $condition['template'] = $template;
        $result = $this->runPhp($script, [$root, base64_encode(json_encode($condition, JSON_THROW_ON_ERROR))]);
        $output = $this->assertSuccessfulProcess($result);

        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }

    private function assertSuccessfulProcess(array $result): string
    {
        // エラー診断の肥大化を防ぐ先頭8KiBへの制限
        $this->assertSame(0, $result['exitcode'], substr($result['stderr'] . $result['stdout'], 0, 8192));
        $this->assertSame('', substr($result['stderr'], 0, 8192), '子PHPの標準エラーへの出力');

        return $result['stdout'];
    }

    private function runPhp(string $script, array $arguments = [], float $timeout = 30.0): array
    {
        $root = dirname(__DIR__, 2);
        $scratchRoot = $root . '/scratch';
        $scratchCreated = false;
        $processDirectory = null;
        $createdPaths = [];
        $outputHandles = [];
        $process = null;
        $pipes = [];

        try {
            if (!is_dir($scratchRoot)) {
                $scratchCreated = @mkdir($scratchRoot, 0777, true);
                if (!$scratchCreated && !is_dir($scratchRoot)) {
                    throw new \RuntimeException('子PHP用一時ディレクトリの作成失敗');
                }
            }

            // シンボリックリンクによるプロジェクト外への一時出力の防止
            $resolvedRoot = realpath($root);
            $resolvedScratch = realpath($scratchRoot);
            if ($resolvedRoot === false || $resolvedScratch === false) {
                throw new \RuntimeException('子PHP用一時出力先の解決失敗');
            }
            $rootPrefix = rtrim(str_replace('\\', '/', $resolvedRoot), '/') . '/';
            $resolvedScratch = str_replace('\\', '/', $resolvedScratch);
            $insideProject = PHP_OS_FAMILY === 'Windows'
                ? strncasecmp($resolvedScratch, $rootPrefix, strlen($rootPrefix)) === 0
                : str_starts_with($resolvedScratch, $rootPrefix);
            if (!$insideProject) {
                throw new \RuntimeException('子PHP用一時出力先がプロジェクト外です。');
            }

            // 並行テストと衝突しない今回専用の一時出力先
            $processDirectory = $scratchRoot . '/cocoon-eyecatch-process-' . bin2hex(random_bytes(8));
            if (!mkdir($processDirectory, 0700)) {
                $processDirectory = null;
                throw new \RuntimeException('子PHP専用一時ディレクトリの作成失敗');
            }
            foreach (['stdout', 'stderr'] as $name) {
                $path = $processDirectory . '/' . $name . '.log';
                $handle = fopen($path, 'x+b');
                if ($handle === false) {
                    throw new \RuntimeException('子PHPの一時出力ファイルの作成失敗');
                }
                $createdPaths[] = $path;
                $outputHandles[$name] = $handle;
            }

            // Windowsのパイプ監視制約と大量出力時の待ち合わせを避ける実ファイル接続
            $command = array_merge([
                PHP_BINARY, '-d', 'display_errors=stderr', '-d', 'log_errors=0',
                '-d', 'error_reporting=-1', '-r', $script,
            ], $arguments);
            $process = proc_open($command, [
                0 => ['pipe', 'r'],
                1 => $outputHandles['stdout'],
                2 => $outputHandles['stderr'],
            ], $pipes, $root);
            if (!is_resource($process)) {
                throw new \RuntimeException('子PHPの起動失敗');
            }
            fclose($pipes[0]);

            // 時計の補正に影響されない実行期限と各出力2MiBまでの制限
            $deadline = hrtime(true) / 1000000000 + $timeout;
            do {
                $status = proc_get_status($process);
                foreach ($outputHandles as $handle) {
                    $stat = fstat($handle);
                    if ($stat === false) {
                        throw new \RuntimeException('子PHPの出力サイズの確認失敗');
                    }
                    if ($stat['size'] > self::PROCESS_OUTPUT_LIMIT) {
                        throw new \RuntimeException('子PHPの出力上限の超過');
                    }
                }
                if (!$status['running']) {
                    $exitCode = $status['exitcode'];
                    break;
                }
                if (hrtime(true) / 1000000000 >= $deadline) {
                    throw new \RuntimeException('子PHPの待ち時間の超過');
                }
                usleep(10000);
            } while (true);

            // PHP 8.2での終了コード消失を避ける最初の終了状態の保持
            $closedExitCode = proc_close($process);
            $process = null;
            $result = ['exitcode' => $exitCode >= 0 ? $exitCode : $closedExitCode];
            foreach ($outputHandles as $name => $handle) {
                if (!rewind($handle)) {
                    throw new \RuntimeException('子PHPの出力ファイルの読み戻し失敗');
                }
                $output = stream_get_contents($handle, self::PROCESS_OUTPUT_LIMIT + 1);
                if ($output === false) {
                    throw new \RuntimeException('子PHPの出力ファイルの読み取り失敗');
                }
                if (strlen($output) > self::PROCESS_OUTPUT_LIMIT) {
                    throw new \RuntimeException('子PHPの出力上限の超過');
                }
                $result[$name] = $output;
            }

            return $result;
        } finally {
            // 成否によらない子プロセス・ハンドル・今回作成した一時出力の解放
            foreach ($pipes as $pipe) {
                if (is_resource($pipe)) fclose($pipe);
            }
            if (is_resource($process)) {
                $status = proc_get_status($process);
                if ($status['running']) proc_terminate($process, 9);
                proc_close($process);
            }
            foreach ($outputHandles as $handle) {
                if (is_resource($handle)) fclose($handle);
            }
            foreach ($createdPaths as $path) {
                if (is_file($path)) unlink($path);
            }
            if ($processDirectory !== null && is_dir($processDirectory)) rmdir($processDirectory);
            if ($scratchCreated && is_dir($scratchRoot)) {
                $scratchEntries = scandir($scratchRoot);
                if (is_array($scratchEntries) && count($scratchEntries) === 2) @rmdir($scratchRoot);
            }
        }
    }
}
