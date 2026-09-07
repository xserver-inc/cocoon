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

    /** @var array|null Gitで追跡されているパスの一覧 */
    private static ?array $trackedFiles = null;

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
        $this->assertGitTracked($relativePath, $description);

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

    /**
     * 作業ツリーにあるだけの未追跡ファイルを、配布可能と誤判定しないための検証
     */
    private function assertGitTracked(string $relativePath, string $description): void
    {
        $gitMetadataPath = $this->themeRoot . '/.git';
        if (!file_exists($gitMetadataPath)) {
            return;
        }

        if (self::$trackedFiles === null) {
            $output = [];
            $exitCode = 0;
            $command = 'git -C ' . escapeshellarg($this->themeRoot) . ' -c core.quotepath=false ls-files';

            exec($command, $output, $exitCode);
            $this->assertSame(0, $exitCode, 'Git追跡ファイルの一覧を取得できませんでした');
            self::$trackedFiles = array_map(
                static fn (string $path): string => str_replace('\\', '/', trim($path)),
                $output
            );
        }

        $normalizedTarget = str_replace('\\', '/', ltrim($relativePath, '/'));
        $this->assertContains(
            $normalizedTarget,
            self::$trackedFiles,
            "{$description} ({$relativePath}) がGitの追跡対象になっていません"
        );
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
            'cocoon-settings.css' => ['css/cocoon-settings.css', 'Cocoon設定画面CSS'],
            'javascript.js'    => ['javascript.js', 'メインJavaScript'],
        ];
    }

    /**
     * 現在の作業内容から実際の配布tarを作り、追跡漏れとexport-ignoreを同時に検証する。
     */
    #[\PHPUnit\Framework\Attributes\Group('distribution')]
    public function test_Git配布アーカイブに設定画面の実行ファイルだけが含まれる(): void
    {
        if (!file_exists($this->themeRoot . '/.git')) {
            $this->markTestSkipped('Git管理情報がない配布環境ではアーカイブを再生成できません');
        }

        $temporaryIndex = tempnam(sys_get_temp_dir(), 'cocoon-git-index-');
        $temporaryArchive = tempnam(sys_get_temp_dir(), 'cocoon-archive-');
        $temporaryObjects = tempnam(sys_get_temp_dir(), 'cocoon-git-objects-');
        $this->assertIsString($temporaryIndex);
        $this->assertIsString($temporaryArchive);
        $this->assertIsString($temporaryObjects);

        // 空ファイルをGit indexとして誤認させないよう、パスだけ確保してから削除する。
        unlink($temporaryIndex);
        unlink($temporaryArchive);
        unlink($temporaryObjects);
        mkdir($temporaryObjects);

        $previousIndex = getenv('GIT_INDEX_FILE');
        $previousObjectDirectory = getenv('GIT_OBJECT_DIRECTORY');
        $previousAlternates = getenv('GIT_ALTERNATE_OBJECT_DIRECTORIES');
        $objectsOutput = $this->runGitCommand(['rev-parse', '--git-path', 'objects']);
        $objectsPath = trim(implode("\n", $objectsOutput));
        if (!preg_match('~\A(?:[A-Za-z]:[\\\\/]|/)~', $objectsPath)) {
            $objectsPath = $this->themeRoot . '/' . $objectsPath;
        }
        $repositoryObjects = realpath($objectsPath);
        $this->assertIsString($repositoryObjects);
        putenv('GIT_INDEX_FILE=' . $temporaryIndex);
        putenv('GIT_OBJECT_DIRECTORY=' . $temporaryObjects);
        putenv('GIT_ALTERNATE_OBJECT_DIRECTORIES=' . $repositoryObjects);

        try {
            // 実indexを変更せず、HEADと現在の作業ツリー全体を一時indexへ合成する。
            $this->runGitCommand(['read-tree', 'HEAD']);
            $this->runGitCommand(['add', '-A', '--', '.']);
            $treeOutput = $this->runGitCommand(['write-tree']);
            $treeId = trim(implode("\n", $treeOutput));
            $this->assertMatchesRegularExpression('/\A[0-9a-f]{40,64}\z/', $treeId);

            $this->runGitCommand([
                'archive',
                '--format=tar',
                '--output=' . $temporaryArchive,
                $treeId,
            ]);

            $archiveEntries = $this->readTarEntryNames($temporaryArchive);
            $this->assertContains('js/cocoon-settings-navigation.js', $archiveEntries);
            $this->assertContains('css/cocoon-settings.css', $archiveEntries);

            // 管理画面が利用する8言語の全翻訳形式を、実際の配布アーカイブ上で固定する。
            foreach (['de_DE', 'en_US', 'es_ES', 'fr_FR', 'ko_KR', 'pt_PT', 'zh_CN', 'zh_TW'] as $locale) {
                foreach (['po', 'mo', 'l10n.php'] as $extension) {
                    $this->assertContains("languages/{$locale}.{$extension}", $archiveEntries);
                }
            }

            $this->assertNotContains(
                '.github/docs/COCOON-SETTINGS-DESIGN-IMPLEMENTATION-PLAN.md',
                $archiveEntries
            );
            $this->assertNotContains(
                '.github/docs/COCOON-SETTINGS-DATA-COMPATIBILITY-REPORT.md',
                $archiveEntries
            );
            $this->assertNotContains('tests/js/cocoon-settings-navigation.test.js', $archiveEntries);
            $this->assertNotContains('package.json', $archiveEntries);
        } finally {
            // 一時的な環境変数とファイルを、成功・失敗にかかわらず必ず元へ戻す。
            if ($previousIndex === false) {
                putenv('GIT_INDEX_FILE');
            } else {
                putenv('GIT_INDEX_FILE=' . $previousIndex);
            }
            if ($previousObjectDirectory === false) {
                putenv('GIT_OBJECT_DIRECTORY');
            } else {
                putenv('GIT_OBJECT_DIRECTORY=' . $previousObjectDirectory);
            }
            if ($previousAlternates === false) {
                putenv('GIT_ALTERNATE_OBJECT_DIRECTORIES');
            } else {
                putenv('GIT_ALTERNATE_OBJECT_DIRECTORIES=' . $previousAlternates);
            }

            if (file_exists($temporaryIndex)) {
                unlink($temporaryIndex);
            }
            if (file_exists($temporaryArchive)) {
                unlink($temporaryArchive);
            }
            $this->removeTemporaryDirectory($temporaryObjects);
        }
    }

    /**
     * 一時indexを共有したままGitコマンドを実行する。
     *
     * @param array<int, string> $arguments Gitへ渡す引数。
     * @return array<int, string> 標準出力。
     */
    private function runGitCommand(array $arguments): array
    {
        $command = 'git -C ' . escapeshellarg($this->themeRoot)
            . ' -c core.autocrlf=false -c core.safecrlf=false -c core.excludesFile=';
        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);
        $this->assertSame(0, $exitCode, 'Gitコマンドに失敗しました: ' . implode(' ', $arguments));

        return $output;
    }

    /**
     * Gitが生成したustarのヘッダーを読み、配布パスの一覧へ変換する。
     *
     * @return array<int, string> アーカイブ内のパス。
     */
    private function readTarEntryNames(string $archivePath): array
    {
        $handle = fopen($archivePath, 'rb');
        $this->assertIsResource($handle);
        $entries = [];

        try {
            while (!feof($handle)) {
                $header = fread($handle, 512);
                if (!is_string($header) || strlen($header) < 512 || trim($header, "\0") === '') {
                    break;
                }

                // ustarのprefixとnameを結合し、長い配布パスにも対応する。
                $name = rtrim(substr($header, 0, 100), "\0");
                $prefix = rtrim(substr($header, 345, 155), "\0");
                $entryName = $prefix === '' ? $name : $prefix . '/' . $name;
                $entries[] = rtrim($entryName, '/');

                // 次の512バイト境界までデータ本体を読み飛ばす。
                $sizeField = trim(substr($header, 124, 12), " \0");
                $entrySize = $sizeField === '' ? 0 : octdec($sizeField);
                $paddedSize = (int) (ceil($entrySize / 512) * 512);
                if ($paddedSize > 0) {
                    fseek($handle, $paddedSize, SEEK_CUR);
                }
            }
        } finally {
            fclose($handle);
        }

        return $entries;
    }

    /**
     * このテストが作成した一時Git objectディレクトリだけを後始末する。
     */
    private function removeTemporaryDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                chmod($item->getPathname(), 0777);
                rmdir($item->getPathname());
            } else {
                // Git objectはWindowsで読み取り専用になるため、削除前に書込み可能へ戻す。
                chmod($item->getPathname(), 0666);
                unlink($item->getPathname());
            }
        }

        chmod($directory, 0777);
        rmdir($directory);
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
        return [
            '_defins' => ['lib/_defins.php', '_defins.php'],
            '_imports' => ['lib/_imports.php', '_imports.php'],
            'ad' => ['lib/ad.php', 'ad.php'],
            'additional-classes' => ['lib/additional-classes.php', 'additional-classes.php'],
            'admin-forms' => ['lib/admin-forms.php', 'admin-forms.php'],
            'admin-tinymce-qtag' => ['lib/admin-tinymce-qtag.php', 'admin-tinymce-qtag.php'],
            'admin-tools' => ['lib/admin-tools.php', 'admin-tools.php'],
            'admin' => ['lib/admin.php', 'admin.php'],
            'amp' => ['lib/amp.php', 'amp.php'],
            'analytics > access' => ['lib/analytics/access.php', 'access.php'],
            'auto-post-thumbnail' => ['lib/auto-post-thumbnail.php', 'auto-post-thumbnail.php'],
            'block-amazon-product-link-cron' => ['lib/block-amazon-product-link-cron.php', 'block-amazon-product-link-cron.php'],
            'block-amazon-product-link' => ['lib/block-amazon-product-link.php', 'block-amazon-product-link.php'],
            'block-editor-styles-faq' => ['lib/block-editor-styles-faq.php', 'block-editor-styles-faq.php'],
            'block-editor-styles-group' => ['lib/block-editor-styles-group.php', 'block-editor-styles-group.php'],
            'block-editor-styles-image' => ['lib/block-editor-styles-image.php', 'block-editor-styles-image.php'],
            'block-editor-styles-list' => ['lib/block-editor-styles-list.php', 'block-editor-styles-list.php'],
            'block-editor-styles-paragraph' => ['lib/block-editor-styles-paragraph.php', 'block-editor-styles-paragraph.php'],
            'block-rakuten-product-link-cron' => ['lib/block-rakuten-product-link-cron.php', 'block-rakuten-product-link-cron.php'],
            'block-rakuten-product-link' => ['lib/block-rakuten-product-link.php', 'block-rakuten-product-link.php'],
            'blogcard-in' => ['lib/blogcard-in.php', 'blogcard-in.php'],
            'blogcard-out' => ['lib/blogcard-out.php', 'blogcard-out.php'],
            'cache' => ['lib/cache.php', 'cache.php'],
            'comments' => ['lib/comments.php', 'comments.php'],
            'common > color-palette-css' => ['lib/common/color-palette-css.php', 'color-palette-css.php'],
            'common > copy' => ['lib/common/copy.php', 'copy.php'],
            'content-category' => ['lib/content-category.php', 'content-category.php'],
            'content-tag' => ['lib/content-tag.php', 'content-tag.php'],
            'creators-api' => ['lib/creators-api.php', 'creators-api.php'],
            'custom-fields > ad-field' => ['lib/custom-fields/ad-field.php', 'ad-field.php'],
            'custom-fields > amp-field' => ['lib/custom-fields/amp-field.php', 'amp-field.php'],
            'custom-fields > custom-css-field' => ['lib/custom-fields/custom-css-field.php', 'custom-css-field.php'],
            'custom-fields > custom-js-field' => ['lib/custom-fields/custom-js-field.php', 'custom-js-field.php'],
            'custom-fields > memo-field' => ['lib/custom-fields/memo-field.php', 'memo-field.php'],
            'custom-fields > other-field' => ['lib/custom-fields/other-field.php', 'other-field.php'],
            'custom-fields > page-field' => ['lib/custom-fields/page-field.php', 'page-field.php'],
            'custom-fields > redirect-field' => ['lib/custom-fields/redirect-field.php', 'redirect-field.php'],
            'custom-fields > review-field' => ['lib/custom-fields/review-field.php', 'review-field.php'],
            'custom-fields > seo-field' => ['lib/custom-fields/seo-field.php', 'seo-field.php'],
            'custom-fields > sns-image-field' => ['lib/custom-fields/sns-image-field.php', 'sns-image-field.php'],
            'custom-fields > update-field' => ['lib/custom-fields/update-field.php', 'update-field.php'],
            'dashboard-message' => ['lib/dashboard-message.php', 'dashboard-message.php'],
            'db' => ['lib/db.php', 'db.php'],
            'debug' => ['lib/debug.php', 'debug.php'],
            'entry-card' => ['lib/entry-card.php', 'entry-card.php'],
            'eyecatch' => ['lib/eyecatch.php', 'eyecatch.php'],
            'font-awesome' => ['lib/font-awesome.php', 'font-awesome.php'],
            'gutenberg' => ['lib/gutenberg.php', 'gutenberg.php'],
            'html-forms' => ['lib/html-forms.php', 'html-forms.php'],
            'html-tooltips' => ['lib/html-tooltips.php', 'html-tooltips.php'],
            'html5' => ['lib/html5.php', 'html5.php'],
            'image' => ['lib/image.php', 'image.php'],
            'language' => ['lib/language.php', 'language.php'],
            'links' => ['lib/links.php', 'links.php'],
            'medias' => ['lib/medias.php', 'medias.php'],
            'minify > minify-master > src > CSS' => ['lib/minify/minify-master/src/CSS.php', 'CSS.php'],
            'minify > minify-master > src > Exception' => ['lib/minify/minify-master/src/Exception.php', 'Exception.php'],
            'minify > minify-master > src > Exceptions > BasicException' => ['lib/minify/minify-master/src/Exceptions/BasicException.php', 'BasicException.php'],
            'minify > minify-master > src > Exceptions > FileImportException' => ['lib/minify/minify-master/src/Exceptions/FileImportException.php', 'FileImportException.php'],
            'minify > minify-master > src > Exceptions > IOException' => ['lib/minify/minify-master/src/Exceptions/IOException.php', 'IOException.php'],
            'minify > minify-master > src > JS' => ['lib/minify/minify-master/src/JS.php', 'JS.php'],
            'minify > minify-master > src > Minify' => ['lib/minify/minify-master/src/Minify.php', 'Minify.php'],
            'minify > path-converter-master > src > Converter' => ['lib/minify/path-converter-master/src/Converter.php', 'Converter.php'],
            'minify > path-converter-master > src > ConverterInterface' => ['lib/minify/path-converter-master/src/ConverterInterface.php', 'ConverterInterface.php'],
            'minify > path-converter-master > src > NoConverter' => ['lib/minify/path-converter-master/src/NoConverter.php', 'NoConverter.php'],
            'ogp' => ['lib/ogp.php', 'ogp.php'],
            'open-graph' => ['lib/open-graph.php', 'open-graph.php'],
            'original-menu' => ['lib/original-menu.php', 'original-menu.php'],
            'page-access > _top-page' => ['lib/page-access/_top-page.php', '_top-page.php'],
            'page-access > access-forms' => ['lib/page-access/access-forms.php', 'access-forms.php'],
            'page-access > access-func' => ['lib/page-access/access-func.php', 'access-func.php'],
            'page-access > access-posts' => ['lib/page-access/access-posts.php', 'access-posts.php'],
            'page-affiliate-tag > _top-page' => ['lib/page-affiliate-tag/_top-page.php', '_top-page.php'],
            'page-affiliate-tag > affiliate-tag-func' => ['lib/page-affiliate-tag/affiliate-tag-func.php', 'affiliate-tag-func.php'],
            'page-affiliate-tag > form-delete' => ['lib/page-affiliate-tag/form-delete.php', 'form-delete.php'],
            'page-affiliate-tag > form' => ['lib/page-affiliate-tag/form.php', 'form.php'],
            'page-affiliate-tag > list' => ['lib/page-affiliate-tag/list.php', 'list.php'],
            'page-affiliate-tag > posts-delete' => ['lib/page-affiliate-tag/posts-delete.php', 'posts-delete.php'],
            'page-affiliate-tag > posts' => ['lib/page-affiliate-tag/posts.php', 'posts.php'],
            'page-backup > _top-page' => ['lib/page-backup/_top-page.php', '_top-page.php'],
            'page-backup > backup-download' => ['lib/page-backup/backup-download.php', 'backup-download.php'],
            'page-backup > backup-forms' => ['lib/page-backup/backup-forms.php', 'backup-forms.php'],
            'page-backup > backup-func' => ['lib/page-backup/backup-func.php', 'backup-func.php'],
            'page-backup > backup-posts' => ['lib/page-backup/backup-posts.php', 'backup-posts.php'],
            'page-cache > _top-page' => ['lib/page-cache/_top-page.php', '_top-page.php'],
            'page-cache > cache-forms' => ['lib/page-cache/cache-forms.php', 'cache-forms.php'],
            'page-cache > cache-func' => ['lib/page-cache/cache-func.php', 'cache-func.php'],
            'page-cache > cache-posts' => ['lib/page-cache/cache-posts.php', 'cache-posts.php'],
            'page-func-text > _top-page' => ['lib/page-func-text/_top-page.php', '_top-page.php'],
            'page-func-text > form-delete' => ['lib/page-func-text/form-delete.php', 'form-delete.php'],
            'page-func-text > form' => ['lib/page-func-text/form.php', 'form.php'],
            'page-func-text > func-text-func' => ['lib/page-func-text/func-text-func.php', 'func-text-func.php'],
            'page-func-text > list' => ['lib/page-func-text/list.php', 'list.php'],
            'page-func-text > posts-delete' => ['lib/page-func-text/posts-delete.php', 'posts-delete.php'],
            'page-func-text > posts' => ['lib/page-func-text/posts.php', 'posts.php'],
            'page-item-ranking > _top-page' => ['lib/page-item-ranking/_top-page.php', '_top-page.php'],
            'page-item-ranking > form-delete' => ['lib/page-item-ranking/form-delete.php', 'form-delete.php'],
            'page-item-ranking > form' => ['lib/page-item-ranking/form.php', 'form.php'],
            'page-item-ranking > item-ranking-func' => ['lib/page-item-ranking/item-ranking-func.php', 'item-ranking-func.php'],
            'page-item-ranking > list' => ['lib/page-item-ranking/list.php', 'list.php'],
            'page-item-ranking > posts-delete' => ['lib/page-item-ranking/posts-delete.php', 'posts-delete.php'],
            'page-item-ranking > posts' => ['lib/page-item-ranking/posts.php', 'posts.php'],
            'page-settings > _top-page' => ['lib/page-settings/_top-page.php', '_top-page.php'],
            'page-settings > 404-forms' => ['lib/page-settings/404-forms.php', '404-forms.php'],
            'page-settings > 404-funcs' => ['lib/page-settings/404-funcs.php', '404-funcs.php'],
            'page-settings > 404-posts' => ['lib/page-settings/404-posts.php', '404-posts.php'],
            'page-settings > about-forms' => ['lib/page-settings/about-forms.php', 'about-forms.php'],
            'page-settings > about-funcs' => ['lib/page-settings/about-funcs.php', 'about-funcs.php'],
            'page-settings > admin-forms' => ['lib/page-settings/admin-forms.php', 'admin-forms.php'],
            'page-settings > admin-funcs' => ['lib/page-settings/admin-funcs.php', 'admin-funcs.php'],
            'page-settings > admin-posts' => ['lib/page-settings/admin-posts.php', 'admin-posts.php'],
            'page-settings > ads-forms' => ['lib/page-settings/ads-forms.php', 'ads-forms.php'],
            'page-settings > ads-funcs' => ['lib/page-settings/ads-funcs.php', 'ads-funcs.php'],
            'page-settings > ads-posts' => ['lib/page-settings/ads-posts.php', 'ads-posts.php'],
            'page-settings > all-forms' => ['lib/page-settings/all-forms.php', 'all-forms.php'],
            'page-settings > all-funcs' => ['lib/page-settings/all-funcs.php', 'all-funcs.php'],
            'page-settings > all-posts' => ['lib/page-settings/all-posts.php', 'all-posts.php'],
            'page-settings > amp-forms' => ['lib/page-settings/amp-forms.php', 'amp-forms.php'],
            'page-settings > amp-funcs' => ['lib/page-settings/amp-funcs.php', 'amp-funcs.php'],
            'page-settings > amp-posts' => ['lib/page-settings/amp-posts.php', 'amp-posts.php'],
            'page-settings > analytics-forms' => ['lib/page-settings/analytics-forms.php', 'analytics-forms.php'],
            'page-settings > analytics-funcs' => ['lib/page-settings/analytics-funcs.php', 'analytics-funcs.php'],
            'page-settings > analytics-posts' => ['lib/page-settings/analytics-posts.php', 'analytics-posts.php'],
            'page-settings > apis-forms' => ['lib/page-settings/apis-forms.php', 'apis-forms.php'],
            'page-settings > apis-funcs' => ['lib/page-settings/apis-funcs.php', 'apis-funcs.php'],
            'page-settings > apis-posts' => ['lib/page-settings/apis-posts.php', 'apis-posts.php'],
            'page-settings > appeal-forms' => ['lib/page-settings/appeal-forms.php', 'appeal-forms.php'],
            'page-settings > appeal-funcs' => ['lib/page-settings/appeal-funcs.php', 'appeal-funcs.php'],
            'page-settings > appeal-posts' => ['lib/page-settings/appeal-posts.php', 'appeal-posts.php'],
            'page-settings > blogcard-help' => ['lib/page-settings/blogcard-help.php', 'blogcard-help.php'],
            'page-settings > blogcard-in-forms' => ['lib/page-settings/blogcard-in-forms.php', 'blogcard-in-forms.php'],
            'page-settings > blogcard-in-funcs' => ['lib/page-settings/blogcard-in-funcs.php', 'blogcard-in-funcs.php'],
            'page-settings > blogcard-in-posts' => ['lib/page-settings/blogcard-in-posts.php', 'blogcard-in-posts.php'],
            'page-settings > blogcard-out-forms' => ['lib/page-settings/blogcard-out-forms.php', 'blogcard-out-forms.php'],
            'page-settings > blogcard-out-funcs' => ['lib/page-settings/blogcard-out-funcs.php', 'blogcard-out-funcs.php'],
            'page-settings > blogcard-out-posts' => ['lib/page-settings/blogcard-out-posts.php', 'blogcard-out-posts.php'],
            'page-settings > buttons-forms' => ['lib/page-settings/buttons-forms.php', 'buttons-forms.php'],
            'page-settings > buttons-funcs' => ['lib/page-settings/buttons-funcs.php', 'buttons-funcs.php'],
            'page-settings > buttons-posts' => ['lib/page-settings/buttons-posts.php', 'buttons-posts.php'],
            'page-settings > carousel-forms' => ['lib/page-settings/carousel-forms.php', 'carousel-forms.php'],
            'page-settings > carousel-funcs' => ['lib/page-settings/carousel-funcs.php', 'carousel-funcs.php'],
            'page-settings > carousel-posts' => ['lib/page-settings/carousel-posts.php', 'carousel-posts.php'],
            'page-settings > code-forms' => ['lib/page-settings/code-forms.php', 'code-forms.php'],
            'page-settings > code-funcs' => ['lib/page-settings/code-funcs.php', 'code-funcs.php'],
            'page-settings > code-posts' => ['lib/page-settings/code-posts.php', 'code-posts.php'],
            'page-settings > column-forms' => ['lib/page-settings/column-forms.php', 'column-forms.php'],
            'page-settings > column-funcs' => ['lib/page-settings/column-funcs.php', 'column-funcs.php'],
            'page-settings > column-posts' => ['lib/page-settings/column-posts.php', 'column-posts.php'],
            'page-settings > comment-forms' => ['lib/page-settings/comment-forms.php', 'comment-forms.php'],
            'page-settings > comment-funcs' => ['lib/page-settings/comment-funcs.php', 'comment-funcs.php'],
            'page-settings > comment-posts' => ['lib/page-settings/comment-posts.php', 'comment-posts.php'],
            'page-settings > content-forms' => ['lib/page-settings/content-forms.php', 'content-forms.php'],
            'page-settings > content-funcs' => ['lib/page-settings/content-funcs.php', 'content-funcs.php'],
            'page-settings > content-posts' => ['lib/page-settings/content-posts.php', 'content-posts.php'],
            'page-settings > donation-forms' => ['lib/page-settings/donation-forms.php', 'donation-forms.php'],
            'page-settings > donation-funcs' => ['lib/page-settings/donation-funcs.php', 'donation-funcs.php'],
            'page-settings > donation-posts' => ['lib/page-settings/donation-posts.php', 'donation-posts.php'],
            'page-settings > editor-forms' => ['lib/page-settings/editor-forms.php', 'editor-forms.php'],
            'page-settings > editor-funcs' => ['lib/page-settings/editor-funcs.php', 'editor-funcs.php'],
            'page-settings > editor-posts' => ['lib/page-settings/editor-posts.php', 'editor-posts.php'],
            'page-settings > footer-forms' => ['lib/page-settings/footer-forms.php', 'footer-forms.php'],
            'page-settings > footer-funcs' => ['lib/page-settings/footer-funcs.php', 'footer-funcs.php'],
            'page-settings > footer-posts' => ['lib/page-settings/footer-posts.php', 'footer-posts.php'],
            'page-settings > header-forms' => ['lib/page-settings/header-forms.php', 'header-forms.php'],
            'page-settings > header-funcs' => ['lib/page-settings/header-funcs.php', 'header-funcs.php'],
            'page-settings > header-posts' => ['lib/page-settings/header-posts.php', 'header-posts.php'],
            'page-settings > image-forms' => ['lib/page-settings/image-forms.php', 'image-forms.php'],
            'page-settings > image-funcs' => ['lib/page-settings/image-funcs.php', 'image-funcs.php'],
            'page-settings > image-posts' => ['lib/page-settings/image-posts.php', 'image-posts.php'],
            'page-settings > index-forms' => ['lib/page-settings/index-forms.php', 'index-forms.php'],
            'page-settings > index-funcs' => ['lib/page-settings/index-funcs.php', 'index-funcs.php'],
            'page-settings > index-posts' => ['lib/page-settings/index-posts.php', 'index-posts.php'],
            'page-settings > mobile-buttons-forms' => ['lib/page-settings/mobile-buttons-forms.php', 'mobile-buttons-forms.php'],
            'page-settings > mobile-buttons-funcs' => ['lib/page-settings/mobile-buttons-funcs.php', 'mobile-buttons-funcs.php'],
            'page-settings > mobile-buttons-posts' => ['lib/page-settings/mobile-buttons-posts.php', 'mobile-buttons-posts.php'],
            'page-settings > navi-forms' => ['lib/page-settings/navi-forms.php', 'navi-forms.php'],
            'page-settings > navi-funcs' => ['lib/page-settings/navi-funcs.php', 'navi-funcs.php'],
            'page-settings > navi-posts' => ['lib/page-settings/navi-posts.php', 'navi-posts.php'],
            'page-settings > notice-forms' => ['lib/page-settings/notice-forms.php', 'notice-forms.php'],
            'page-settings > notice-funcs' => ['lib/page-settings/notice-funcs.php', 'notice-funcs.php'],
            'page-settings > notice-posts' => ['lib/page-settings/notice-posts.php', 'notice-posts.php'],
            'page-settings > ogp-forms' => ['lib/page-settings/ogp-forms.php', 'ogp-forms.php'],
            'page-settings > ogp-funcs' => ['lib/page-settings/ogp-funcs.php', 'ogp-funcs.php'],
            'page-settings > ogp-posts' => ['lib/page-settings/ogp-posts.php', 'ogp-posts.php'],
            'page-settings > others-forms' => ['lib/page-settings/others-forms.php', 'others-forms.php'],
            'page-settings > others-funcs' => ['lib/page-settings/others-funcs.php', 'others-funcs.php'],
            'page-settings > others-posts' => ['lib/page-settings/others-posts.php', 'others-posts.php'],
            'page-settings > page-forms' => ['lib/page-settings/page-forms.php', 'page-forms.php'],
            'page-settings > page-funcs' => ['lib/page-settings/page-funcs.php', 'page-funcs.php'],
            'page-settings > page-posts' => ['lib/page-settings/page-posts.php', 'page-posts.php'],
            'page-settings > pwa-forms' => ['lib/page-settings/pwa-forms.php', 'pwa-forms.php'],
            'page-settings > pwa-funcs' => ['lib/page-settings/pwa-funcs.php', 'pwa-funcs.php'],
            'page-settings > pwa-posts' => ['lib/page-settings/pwa-posts.php', 'pwa-posts.php'],
            'page-settings > recommended-forms' => ['lib/page-settings/recommended-forms.php', 'recommended-forms.php'],
            'page-settings > recommended-funcs' => ['lib/page-settings/recommended-funcs.php', 'recommended-funcs.php'],
            'page-settings > recommended-posts' => ['lib/page-settings/recommended-posts.php', 'recommended-posts.php'],
            'page-settings > reset-forms' => ['lib/page-settings/reset-forms.php', 'reset-forms.php'],
            'page-settings > reset-funcs' => ['lib/page-settings/reset-funcs.php', 'reset-funcs.php'],
            'page-settings > reset-posts' => ['lib/page-settings/reset-posts.php', 'reset-posts.php'],
            'page-settings > seo-forms' => ['lib/page-settings/seo-forms.php', 'seo-forms.php'],
            'page-settings > seo-funcs' => ['lib/page-settings/seo-funcs.php', 'seo-funcs.php'],
            'page-settings > seo-posts' => ['lib/page-settings/seo-posts.php', 'seo-posts.php'],
            'page-settings > single-forms' => ['lib/page-settings/single-forms.php', 'single-forms.php'],
            'page-settings > single-funcs' => ['lib/page-settings/single-funcs.php', 'single-funcs.php'],
            'page-settings > single-posts' => ['lib/page-settings/single-posts.php', 'single-posts.php'],
            'page-settings > skin-forms' => ['lib/page-settings/skin-forms.php', 'skin-forms.php'],
            'page-settings > skin-funcs' => ['lib/page-settings/skin-funcs.php', 'skin-funcs.php'],
            'page-settings > skin-posts' => ['lib/page-settings/skin-posts.php', 'skin-posts.php'],
            'page-settings > sns-follow-forms' => ['lib/page-settings/sns-follow-forms.php', 'sns-follow-forms.php'],
            'page-settings > sns-follow-funcs' => ['lib/page-settings/sns-follow-funcs.php', 'sns-follow-funcs.php'],
            'page-settings > sns-follow-posts' => ['lib/page-settings/sns-follow-posts.php', 'sns-follow-posts.php'],
            'page-settings > sns-share-forms-bottom' => ['lib/page-settings/sns-share-forms-bottom.php', 'sns-share-forms-bottom.php'],
            'page-settings > sns-share-forms-top' => ['lib/page-settings/sns-share-forms-top.php', 'sns-share-forms-top.php'],
            'page-settings > sns-share-forms' => ['lib/page-settings/sns-share-forms.php', 'sns-share-forms.php'],
            'page-settings > sns-share-funcs-bottom' => ['lib/page-settings/sns-share-funcs-bottom.php', 'sns-share-funcs-bottom.php'],
            'page-settings > sns-share-funcs-top' => ['lib/page-settings/sns-share-funcs-top.php', 'sns-share-funcs-top.php'],
            'page-settings > sns-share-funcs' => ['lib/page-settings/sns-share-funcs.php', 'sns-share-funcs.php'],
            'page-settings > sns-share-posts-bottom' => ['lib/page-settings/sns-share-posts-bottom.php', 'sns-share-posts-bottom.php'],
            'page-settings > sns-share-posts-top' => ['lib/page-settings/sns-share-posts-top.php', 'sns-share-posts-top.php'],
            'page-settings > sns-share-posts' => ['lib/page-settings/sns-share-posts.php', 'sns-share-posts.php'],
            'page-settings > title-forms' => ['lib/page-settings/title-forms.php', 'title-forms.php'],
            'page-settings > title-funcs' => ['lib/page-settings/title-funcs.php', 'title-funcs.php'],
            'page-settings > title-posts' => ['lib/page-settings/title-posts.php', 'title-posts.php'],
            'page-settings > toc-forms' => ['lib/page-settings/toc-forms.php', 'toc-forms.php'],
            'page-settings > toc-funcs' => ['lib/page-settings/toc-funcs.php', 'toc-funcs.php'],
            'page-settings > toc-posts' => ['lib/page-settings/toc-posts.php', 'toc-posts.php'],
            'page-settings > widget-area-forms' => ['lib/page-settings/widget-area-forms.php', 'widget-area-forms.php'],
            'page-settings > widget-area-funcs' => ['lib/page-settings/widget-area-funcs.php', 'widget-area-funcs.php'],
            'page-settings > widget-area-posts' => ['lib/page-settings/widget-area-posts.php', 'widget-area-posts.php'],
            'page-settings > widget-forms' => ['lib/page-settings/widget-forms.php', 'widget-forms.php'],
            'page-settings > widget-funcs' => ['lib/page-settings/widget-funcs.php', 'widget-funcs.php'],
            'page-settings > widget-posts' => ['lib/page-settings/widget-posts.php', 'widget-posts.php'],
            'page-speech-balloon > _top-page' => ['lib/page-speech-balloon/_top-page.php', '_top-page.php'],
            'page-speech-balloon > demo' => ['lib/page-speech-balloon/demo.php', 'demo.php'],
            'page-speech-balloon > form-delete' => ['lib/page-speech-balloon/form-delete.php', 'form-delete.php'],
            'page-speech-balloon > form' => ['lib/page-speech-balloon/form.php', 'form.php'],
            'page-speech-balloon > list' => ['lib/page-speech-balloon/list.php', 'list.php'],
            'page-speech-balloon > posts-delete' => ['lib/page-speech-balloon/posts-delete.php', 'posts-delete.php'],
            'page-speech-balloon > posts' => ['lib/page-speech-balloon/posts.php', 'posts.php'],
            'page-speech-balloon > speech-balloon-func' => ['lib/page-speech-balloon/speech-balloon-func.php', 'speech-balloon-func.php'],
            'page-speed-up > _top-page' => ['lib/page-speed-up/_top-page.php', '_top-page.php'],
            'page-speed-up > minify-css' => ['lib/page-speed-up/minify-css.php', 'minify-css.php'],
            'page-speed-up > minify-html' => ['lib/page-speed-up/minify-html.php', 'minify-html.php'],
            'page-speed-up > minify-js' => ['lib/page-speed-up/minify-js.php', 'minify-js.php'],
            'page-speed-up > speed-up-forms' => ['lib/page-speed-up/speed-up-forms.php', 'speed-up-forms.php'],
            'page-speed-up > speed-up-func' => ['lib/page-speed-up/speed-up-func.php', 'speed-up-func.php'],
            'page-speed-up > speed-up-posts' => ['lib/page-speed-up/speed-up-posts.php', 'speed-up-posts.php'],
            'php-html-css-js-minifier-new' => ['lib/php-html-css-js-minifier-new.php', 'php-html-css-js-minifier-new.php'],
            'php-html-css-js-minifier-odrigo' => ['lib/php-html-css-js-minifier-odrigo.php', 'php-html-css-js-minifier-odrigo.php'],
            'php-html-css-js-minifier' => ['lib/php-html-css-js-minifier.php', 'php-html-css-js-minifier.php'],
            'plugin-update-checker > load-v5p6' => ['lib/plugin-update-checker/load-v5p6.php', 'load-v5p6.php'],
            'plugin-update-checker > plugin-update-checker' => ['lib/plugin-update-checker/plugin-update-checker.php', 'plugin-update-checker.php'],
            'plugin-update-checker > Puc > v5 > PucFactory' => ['lib/plugin-update-checker/Puc/v5/PucFactory.php', 'PucFactory.php'],
            'plugin-update-checker > Puc > v5p6 > Autoloader' => ['lib/plugin-update-checker/Puc/v5p6/Autoloader.php', 'Autoloader.php'],
            'plugin-update-checker > Puc > v5p6 > DebugBar > Extension' => ['lib/plugin-update-checker/Puc/v5p6/DebugBar/Extension.php', 'Extension.php'],
            'plugin-update-checker > Puc > v5p6 > DebugBar > Panel' => ['lib/plugin-update-checker/Puc/v5p6/DebugBar/Panel.php', 'Panel.php'],
            'plugin-update-checker > Puc > v5p6 > DebugBar > PluginExtension' => ['lib/plugin-update-checker/Puc/v5p6/DebugBar/PluginExtension.php', 'PluginExtension.php'],
            'plugin-update-checker > Puc > v5p6 > DebugBar > PluginPanel' => ['lib/plugin-update-checker/Puc/v5p6/DebugBar/PluginPanel.php', 'PluginPanel.php'],
            'plugin-update-checker > Puc > v5p6 > DebugBar > ThemePanel' => ['lib/plugin-update-checker/Puc/v5p6/DebugBar/ThemePanel.php', 'ThemePanel.php'],
            'plugin-update-checker > Puc > v5p6 > InstalledPackage' => ['lib/plugin-update-checker/Puc/v5p6/InstalledPackage.php', 'InstalledPackage.php'],
            'plugin-update-checker > Puc > v5p6 > Metadata' => ['lib/plugin-update-checker/Puc/v5p6/Metadata.php', 'Metadata.php'],
            'plugin-update-checker > Puc > v5p6 > OAuthSignature' => ['lib/plugin-update-checker/Puc/v5p6/OAuthSignature.php', 'OAuthSignature.php'],
            'plugin-update-checker > Puc > v5p6 > Plugin > Package' => ['lib/plugin-update-checker/Puc/v5p6/Plugin/Package.php', 'Package.php'],
            'plugin-update-checker > Puc > v5p6 > Plugin > PluginInfo' => ['lib/plugin-update-checker/Puc/v5p6/Plugin/PluginInfo.php', 'PluginInfo.php'],
            'plugin-update-checker > Puc > v5p6 > Plugin > Ui' => ['lib/plugin-update-checker/Puc/v5p6/Plugin/Ui.php', 'Ui.php'],
            'plugin-update-checker > Puc > v5p6 > Plugin > Update' => ['lib/plugin-update-checker/Puc/v5p6/Plugin/Update.php', 'Update.php'],
            'plugin-update-checker > Puc > v5p6 > Plugin > UpdateChecker' => ['lib/plugin-update-checker/Puc/v5p6/Plugin/UpdateChecker.php', 'UpdateChecker.php'],
            'plugin-update-checker > Puc > v5p6 > PucFactory' => ['lib/plugin-update-checker/Puc/v5p6/PucFactory.php', 'PucFactory.php'],
            'plugin-update-checker > Puc > v5p6 > Scheduler' => ['lib/plugin-update-checker/Puc/v5p6/Scheduler.php', 'Scheduler.php'],
            'plugin-update-checker > Puc > v5p6 > StateStore' => ['lib/plugin-update-checker/Puc/v5p6/StateStore.php', 'StateStore.php'],
            'plugin-update-checker > Puc > v5p6 > Theme > Package' => ['lib/plugin-update-checker/Puc/v5p6/Theme/Package.php', 'Package.php'],
            'plugin-update-checker > Puc > v5p6 > Theme > Update' => ['lib/plugin-update-checker/Puc/v5p6/Theme/Update.php', 'Update.php'],
            'plugin-update-checker > Puc > v5p6 > Theme > UpdateChecker' => ['lib/plugin-update-checker/Puc/v5p6/Theme/UpdateChecker.php', 'UpdateChecker.php'],
            'plugin-update-checker > Puc > v5p6 > Update' => ['lib/plugin-update-checker/Puc/v5p6/Update.php', 'Update.php'],
            'plugin-update-checker > Puc > v5p6 > UpdateChecker' => ['lib/plugin-update-checker/Puc/v5p6/UpdateChecker.php', 'UpdateChecker.php'],
            'plugin-update-checker > Puc > v5p6 > UpgraderStatus' => ['lib/plugin-update-checker/Puc/v5p6/UpgraderStatus.php', 'UpgraderStatus.php'],
            'plugin-update-checker > Puc > v5p6 > Utils' => ['lib/plugin-update-checker/Puc/v5p6/Utils.php', 'Utils.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > Api' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/Api.php', 'Api.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > BaseChecker' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/BaseChecker.php', 'BaseChecker.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > BitBucketApi' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/BitBucketApi.php', 'BitBucketApi.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > GitHubApi' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/GitHubApi.php', 'GitHubApi.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > GitLabApi' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/GitLabApi.php', 'GitLabApi.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > PluginUpdateChecker' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/PluginUpdateChecker.php', 'PluginUpdateChecker.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > Reference' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/Reference.php', 'Reference.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > ReleaseAssetSupport' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/ReleaseAssetSupport.php', 'ReleaseAssetSupport.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > ReleaseFilteringFeature' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/ReleaseFilteringFeature.php', 'ReleaseFilteringFeature.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > ThemeUpdateChecker' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/ThemeUpdateChecker.php', 'ThemeUpdateChecker.php'],
            'plugin-update-checker > Puc > v5p6 > Vcs > VcsCheckerMethods' => ['lib/plugin-update-checker/Puc/v5p6/Vcs/VcsCheckerMethods.php', 'VcsCheckerMethods.php'],
            'plugin-update-checker > Puc > v5p6 > WpCliCheckTrigger' => ['lib/plugin-update-checker/Puc/v5p6/WpCliCheckTrigger.php', 'WpCliCheckTrigger.php'],
            'plugin-update-checker > vendor > Parsedown' => ['lib/plugin-update-checker/vendor/Parsedown.php', 'Parsedown.php'],
            'plugin-update-checker > vendor > ParsedownModern' => ['lib/plugin-update-checker/vendor/ParsedownModern.php', 'ParsedownModern.php'],
            'plugin-update-checker > vendor > PucReadmeParser' => ['lib/plugin-update-checker/vendor/PucReadmeParser.php', 'PucReadmeParser.php'],
            'plugins' => ['lib/plugins.php', 'plugins.php'],
            'products' => ['lib/products.php', 'products.php'],
            'profile' => ['lib/profile.php', 'profile.php'],
            'punycode-obj' => ['lib/punycode-obj.php', 'punycode-obj.php'],
            'punycode' => ['lib/punycode.php', 'punycode.php'],
            'related-entries' => ['lib/related-entries.php', 'related-entries.php'],
            'scripts' => ['lib/scripts.php', 'scripts.php'],
            'seo' => ['lib/seo.php', 'seo.php'],
            'settings' => ['lib/settings.php', 'settings.php'],
            'shortcodes-amazon' => ['lib/shortcodes-amazon.php', 'shortcodes-amazon.php'],
            'shortcodes-product-func' => ['lib/shortcodes-product-func.php', 'shortcodes-product-func.php'],
            'shortcodes-rakuten' => ['lib/shortcodes-rakuten.php', 'shortcodes-rakuten.php'],
            'shortcodes' => ['lib/shortcodes.php', 'shortcodes.php'],
            'sns-follow' => ['lib/sns-follow.php', 'sns-follow.php'],
            'sns-share' => ['lib/sns-share.php', 'sns-share.php'],
            'sns' => ['lib/sns.php', 'sns.php'],
            'ssl' => ['lib/ssl.php', 'ssl.php'],
            'tinymce > affiliate-tags' => ['lib/tinymce/affiliate-tags.php', 'affiliate-tags.php'],
            'tinymce > function-texts' => ['lib/tinymce/function-texts.php', 'function-texts.php'],
            'tinymce > html-tags' => ['lib/tinymce/html-tags.php', 'html-tags.php'],
            'tinymce > insert-html' => ['lib/tinymce/insert-html.php', 'insert-html.php'],
            'tinymce > item-rankings' => ['lib/tinymce/item-rankings.php', 'item-rankings.php'],
            'tinymce > shortcodes' => ['lib/tinymce/shortcodes.php', 'shortcodes.php'],
            'tinymce > speech-balloons' => ['lib/tinymce/speech-balloons.php', 'speech-balloons.php'],
            'toc' => ['lib/toc.php', 'toc.php'],
            'utils' => ['lib/utils.php', 'utils.php'],
            'walkers' => ['lib/walkers.php', 'walkers.php'],
            'widget-areas' => ['lib/widget-areas.php', 'widget-areas.php'],
            'widget' => ['lib/widget.php', 'widget.php'],
            'widgets > ad' => ['lib/widgets/ad.php', 'ad.php'],
            'widgets > author-box' => ['lib/widgets/author-box.php', 'author-box.php'],
            'widgets > box-menus' => ['lib/widgets/box-menus.php', 'box-menus.php'],
            'widgets > classic-text' => ['lib/widgets/classic-text.php', 'classic-text.php'],
            'widgets > cta-box' => ['lib/widgets/cta-box.php', 'cta-box.php'],
            'widgets > display-widgets' => ['lib/widgets/display-widgets.php', 'display-widgets.php'],
            'widgets > fb-like-balloon' => ['lib/widgets/fb-like-balloon.php', 'fb-like-balloon.php'],
            'widgets > fb-like-box' => ['lib/widgets/fb-like-box.php', 'fb-like-box.php'],
            'widgets > info-list' => ['lib/widgets/info-list.php', 'info-list.php'],
            'widgets > item-ranking' => ['lib/widgets/item-ranking.php', 'item-ranking.php'],
            'widgets > mobile-ad' => ['lib/widgets/mobile-ad.php', 'mobile-ad.php'],
            'widgets > mobile-text' => ['lib/widgets/mobile-text.php', 'mobile-text.php'],
            'widgets > navi-entries' => ['lib/widgets/navi-entries.php', 'navi-entries.php'],
            'widgets > new-entries' => ['lib/widgets/new-entries.php', 'new-entries.php'],
            'widgets > pc-ad' => ['lib/widgets/pc-ad.php', 'pc-ad.php'],
            'widgets > pc-double-ads' => ['lib/widgets/pc-double-ads.php', 'pc-double-ads.php'],
            'widgets > pc-text' => ['lib/widgets/pc-text.php', 'pc-text.php'],
            'widgets > popular-entries' => ['lib/widgets/popular-entries.php', 'popular-entries.php'],
            'widgets > recent-comments' => ['lib/widgets/recent-comments.php', 'recent-comments.php'],
            'widgets > recommended-cards' => ['lib/widgets/recommended-cards.php', 'recommended-cards.php'],
            'widgets > related-entries' => ['lib/widgets/related-entries.php', 'related-entries.php'],
            'widgets > sns-follow-buttons' => ['lib/widgets/sns-follow-buttons.php', 'sns-follow-buttons.php'],
            'widgets > toc' => ['lib/widgets/toc.php', 'toc.php'],
            'youtube' => ['lib/youtube.php', 'youtube.php'],
        ];
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
            'admin-javascript.js'            => ['js/admin-javascript.js', '管理画面JS'],
            'cocoon-settings-navigation.js'  => ['js/cocoon-settings-navigation.js', 'Cocoon設定ナビゲーションJS'],
            'gutenberg-editor-classes.js'     => ['js/gutenberg-editor-classes.js', 'Gutenbergエディタクラス付与JS'],
            'gutenberg-toolbar.js'            => ['js/gutenberg-toolbar.js', 'Gutenbergツールバー用JS'],
            'set-event-passive.js'            => ['js/set-event-passive.js', 'パッシブイベントJS'],
            'group-link.js'                   => ['js/group-link.js', 'グループブロックリンク化JS'],
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
            'Cocoon設定実装計画書' => ['/.github/docs/COCOON-SETTINGS-DESIGN-IMPLEMENTATION-PLAN.md', '内部実装計画書'],
            'Cocoon設定互換性報告書' => ['/.github/docs/COCOON-SETTINGS-DATA-COMPATIBILITY-REPORT.md', '内部互換性報告書'],
            'gitattributes'        => ['/.gitattributes', 'gitattributes'],
            'gitignore'            => ['/.gitignore', 'gitignore'],
        ];
    }
}

