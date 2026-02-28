<?php
/**
 * 配布ファイル構成のテスト
 *
 * .gitattributes の export-ignore 設定により、
 * テーマに必要なファイルが配布ZIPから除外されていないことを検証します。
 * また、開発用ファイルが正しく除外されていることも併せて確認します。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class DistributionFilesTest extends TestCase
{
    /** @var string テーマルートディレクトリ */
    private string $themeRoot;

    /** @var array export-ignore されているパスの一覧 */
    private array $exportIgnoredPaths = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->themeRoot = dirname(__DIR__, 2);

        // .gitattributes から export-ignore パターンを抽出
        $gitattributes = file($this->themeRoot . '/.gitattributes', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($gitattributes as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (str_contains($line, 'export-ignore')) {
                $path = trim(preg_replace('/\s+export-ignore.*$/', '', $line));
                $this->exportIgnoredPaths[] = $path;
            }
        }
    }

    /**
     * 対象パスが export-ignore に含まれていないかチェックするヘルパー
     */
    private function assertNotExportIgnored(string $relativePath, string $description): void
    {
        $fullPath = $this->themeRoot . '/' . ltrim($relativePath, '/');
        $this->assertFileExists($fullPath, "{$description} ({$relativePath}) が存在しません");

        foreach ($this->exportIgnoredPaths as $ignoredPath) {
            $normalizedIgnored = '/' . ltrim($ignoredPath, '/');
            $normalizedTarget = '/' . ltrim($relativePath, '/');

            // ディレクトリパターン（末尾/）の場合
            if (str_ends_with($normalizedIgnored, '/')) {
                $this->assertFalse(
                    str_starts_with($normalizedTarget, $normalizedIgnored) ||
                    str_starts_with($normalizedTarget . '/', $normalizedIgnored),
                    "{$description} ({$relativePath}) が '{$ignoredPath}' により配布から除外されています"
                );
            } else {
                $this->assertNotSame(
                    $normalizedIgnored,
                    $normalizedTarget,
                    "{$description} ({$relativePath}) が .gitattributes により配布から除外されています"
                );
            }
        }
    }

    // ========================================================================
    // 必須ファイル: テーマコア
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('coreFilesProvider')]
    public function test_テーマコアファイルが配布に含まれる(string $path, string $desc): void
    {
        $this->assertNotExportIgnored($path, $desc);
    }

    public static function coreFilesProvider(): array
    {
        return [
            'style.css'        => ['style.css', 'テーマメインCSS'],
            'functions.php'    => ['functions.php', 'テーマ関数ファイル'],
            'screenshot.jpg'   => ['screenshot.jpg', 'テーマスクリーンショット'],
            'theme.json'       => ['theme.json', 'テーマ設定JSON'],
            'readme.md'        => ['readme.md', 'テーマREADME'],
            'license'          => ['license', 'ライセンスファイル'],
            'amp.css'          => ['amp.css', 'AMP用CSS'],
            'editor-style.css' => ['editor-style.css', 'エディタ用CSS'],
            'keyframes.css'    => ['keyframes.css', 'アニメーションCSS'],
            'javascript.js'    => ['javascript.js', 'メインJavaScript'],
        ];
    }

    // ========================================================================
    // 必須ファイル: テンプレートファイル
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('templateFilesProvider')]
    public function test_テンプレートファイルが配布に含まれる(string $path, string $desc): void
    {
        $this->assertNotExportIgnored($path, $desc);
    }

    public static function templateFilesProvider(): array
    {
        return [
            'index.php'      => ['index.php', 'メインインデックス'],
            'header.php'     => ['header.php', 'ヘッダー'],
            'footer.php'     => ['footer.php', 'フッター'],
            'sidebar.php'    => ['sidebar.php', 'サイドバー'],
            'single.php'     => ['single.php', '個別投稿'],
            'page.php'       => ['page.php', '固定ページ'],
            'comments.php'   => ['comments.php', 'コメント'],
            '404.php'        => ['404.php', '404ページ'],
            'searchform.php' => ['searchform.php', '検索フォーム'],
        ];
    }

    // ========================================================================
    // 必須ファイル: カスタムCSS・テンプレートパーツ（tmpディレクトリ）
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('tmpFilesProvider')]
    public function test_tmpテンプレートが配布に含まれる(string $path, string $desc): void
    {
        $this->assertNotExportIgnored($path, $desc);
    }

    public static function tmpFilesProvider(): array
    {
        return [
            'css-custom.php'       => ['tmp/css-custom.php', 'カスタムCSS生成'],
            'content.php'          => ['tmp/content.php', 'コンテンツ'],
            'header-container.php' => ['tmp/header-container.php', 'ヘッダーコンテナ'],
            'footer-javascript.php'=> ['tmp/footer-javascript.php', 'フッターJS'],
            'head-analytics.php'   => ['tmp/head-analytics.php', 'アナリティクスヘッダー'],
            'sns-share-buttons.php'=> ['tmp/sns-share-buttons.php', 'SNS共有ボタン'],
            'sns-follow-buttons.php'=> ['tmp/sns-follow-buttons.php', 'SNSフォローボタン'],
            'breadcrumbs.php'      => ['tmp/breadcrumbs.php', 'パンくずリスト'],
            'entry-card.php'       => ['tmp/entry-card.php', 'エントリーカード'],
            'pagination.php'       => ['tmp/pagination.php', 'ページネーション'],
            'navi.php'             => ['tmp/navi.php', 'ナビゲーション'],
            'ad.php'               => ['tmp/ad.php', '広告'],
            'eye-catch.php'        => ['tmp/eye-catch.php', 'アイキャッチ'],
        ];
    }

    // ========================================================================
    // 必須ファイル: ブロック関連
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('blockFilesProvider')]
    public function test_ブロック関連ファイルが配布に含まれる(string $path, string $desc): void
    {
        $this->assertNotExportIgnored($path, $desc);
    }

    public static function blockFilesProvider(): array
    {
        return [
            'blocks.build.js'          => ['blocks/dist/blocks.build.js', 'ブロックビルド済みJS'],
            'init.php'                 => ['blocks/src/init.php', 'ブロック初期化PHP'],
            'block-amazon'             => ['lib/block-amazon-product-link.php', 'Amazon商品リンクブロック'],
            'block-rakuten'            => ['lib/block-rakuten-product-link.php', '楽天商品リンクブロック'],
        ];
    }

    // ========================================================================
    // 必須ファイル: ライブラリ
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('libFilesProvider')]
    public function test_ライブラリファイルが配布に含まれる(string $path, string $desc): void
    {
        $this->assertNotExportIgnored($path, $desc);
    }

    public static function libFilesProvider(): array
    {
        // lib/ 以下の全PHPファイルを動的に検出
        $themeRoot = dirname(__DIR__, 2);
        $libDir = $themeRoot . '/lib';
        $result = [];

        // RecursiveDirectoryIterator で lib/ 以下を再帰的に走査
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($libDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            // テーマルートからの相対パスを取得（スラッシュ区切りに統一）
            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($themeRoot) + 1));
            // ファイル名をデータセットのキーに使用（重複防止のため相対パスから生成）
            $key = str_replace(['/', '.php'], [' > ', ''], $relativePath);
            $result[$key] = [$relativePath, basename($relativePath)];
        }

        // キー名でソートして見やすくする
        ksort($result);
        return $result;
    }

    // ========================================================================
    // 必須ディレクトリ: 主要ディレクトリが存在し除外されていない
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('requiredDirectoriesProvider')]
    public function test_必須ディレクトリが配布に含まれる(string $path, string $desc): void
    {
        $fullPath = $this->themeRoot . '/' . ltrim($path, '/');
        $this->assertDirectoryExists($fullPath, "{$desc} ({$path}) が存在しません");

        // ディレクトリ自体が export-ignore されていないことを確認
        foreach ($this->exportIgnoredPaths as $ignoredPath) {
            $normalizedIgnored = '/' . ltrim($ignoredPath, '/');
            $normalizedTarget = '/' . ltrim($path, '/');

            // 末尾スラッシュを正規化して比較
            $this->assertNotSame(
                rtrim($normalizedIgnored, '/'),
                rtrim($normalizedTarget, '/'),
                "{$desc} ({$path}) が .gitattributes により配布から除外されています"
            );
        }
    }

    public static function requiredDirectoriesProvider(): array
    {
        return [
            'lib'        => ['lib', 'ライブラリ'],
            'tmp'        => ['tmp', 'テンプレートパーツ'],
            'tmp-user'   => ['tmp-user', 'ユーザーテンプレート'],
            'blocks'     => ['blocks', 'ブロック'],
            'blocks/dist'=> ['blocks/dist', 'ブロックビルド済み'],
            'skins'      => ['skins', 'スキン'],
            'languages'  => ['languages', '翻訳ファイル'],
            'webfonts'   => ['webfonts', 'Webフォント'],
            'images'     => ['images', '画像'],
            'js'         => ['js', 'JavaScript'],
            'css'        => ['css', 'CSS'],
            'scss'       => ['scss', 'SCSS'],
            'plugins'    => ['plugins', 'プラグイン'],
            'configs'    => ['configs', '設定'],
        ];
    }

    // ========================================================================
    // 必須ファイル: JavaScript
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('jsFilesProvider')]
    public function test_JSファイルが配布に含まれる(string $path, string $desc): void
    {
        $this->assertNotExportIgnored($path, $desc);
    }

    public static function jsFilesProvider(): array
    {
        return [
            'admin-javascript.js' => ['js/admin-javascript.js', '管理画面JS'],
            'gutenberg.js'        => ['js/gutenberg.js', 'GutenbergエディタJS'],
            'set-event-passive.js'=> ['js/set-event-passive.js', 'パッシブイベントJS'],
        ];
    }

    // ========================================================================
    // 開発用ファイルが除外されていることの確認
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('devOnlyPathsProvider')]
    public function test_開発用ファイルが配布から除外されている(string $path, string $desc): void
    {
        $normalizedTarget = '/' . ltrim($path, '/');

        $isIgnored = false;
        foreach ($this->exportIgnoredPaths as $ignoredPath) {
            $normalizedIgnored = '/' . ltrim($ignoredPath, '/');

            if ($normalizedIgnored === $normalizedTarget) {
                $isIgnored = true;
                break;
            }
            if (str_ends_with($normalizedIgnored, '/') &&
                str_starts_with($normalizedTarget, rtrim($normalizedIgnored, '/'))) {
                $isIgnored = true;
                break;
            }
        }

        $this->assertTrue(
            $isIgnored,
            "{$desc} ({$path}) が .gitattributes で配布から除外されていません"
        );
    }

    public static function devOnlyPathsProvider(): array
    {
        return [
            'テストディレクトリ'    => ['/tests/', 'テストファイル'],
            'PHPUnit設定'          => ['/phpunit.xml', 'PHPUnit設定'],
            'GitHub Actions'       => ['/.github/', 'GitHub Actions'],
            'Composer JSON'        => ['/composer.json', 'Composer設定'],
            'Composer Lock'        => ['/composer.lock', 'Composerロック'],
            '翻訳スクリプト'       => ['/scripts/', '翻訳ワークフロー用スクリプト'],
            'gitattributes'        => ['/.gitattributes', 'gitattributes'],
            'gitignore'            => ['/.gitignore', 'gitignore'],
        ];
    }
}
