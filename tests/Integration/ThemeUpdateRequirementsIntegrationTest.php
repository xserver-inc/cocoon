<?php
/**
 * 更新ZIPの展開とWordPress本体の更新経路を通る動作要件の統合テスト
 */

namespace Cocoon\Tests\Integration;

class ThemeUpdateRequirementsIntegrationTest extends IntegrationTestCase
{
    private ?string $fixtureRoot = null;
    private string $directory = 'cocoon-requirements-integration';
    private ?\Cocoon_Theme_Update_Requirements $guard = null;
    private mixed $originalFilesystem = null;
    private bool $hadFilesystem = false;
    private mixed $originalUpdates = false;
    private mixed $originalThemeRoots = false;
    private array $originalThemeDirectories = [];
    private mixed $originalAutoUpdates = false;
    private mixed $originalFailureEmails = false;
    private ?string $originalMaintenanceHash = null;
    private array $filters = [];
    private mixed $lastUpgrader = null;
    private mixed $lastUpgradeReturn = null;
    private string $lastOutput = '';

    protected function setUp(): void
    {
        parent::setUp();
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-automatic-updater.php';
        require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
        require_once dirname(__DIR__, 2) . '/lib/theme-update-requirements.php';

        $this->originalUpdates = get_site_transient('update_themes');
        $this->originalThemeRoots = get_site_transient('theme_roots');
        $this->originalThemeDirectories = $GLOBALS['wp_theme_directories'];
        $this->originalAutoUpdates = get_site_option('auto_update_themes', false);
        $this->originalFailureEmails = get_option('auto_plugin_theme_update_emails', false);
        $this->hadFilesystem = array_key_exists('wp_filesystem', $GLOBALS);
        $this->originalFilesystem = $GLOBALS['wp_filesystem'] ?? null;
        $this->originalMaintenanceHash = is_file(ABSPATH . '.maintenance') ? sha1_file(ABSPATH . '.maintenance') : null;
        $this->fixtureRoot = trailingslashit(WP_CONTENT_DIR) . 'upgrade/cocoon-requirements-integration-' . uniqid('', true);
        $this->assertTrue(wp_mkdir_p($this->fixtureRoot . '/themes/' . $this->directory));
        $this->writeInstalledTheme($this->directory);

        // 実テーマと通常のバックアップ先への書き込みを避ける専用テーマ領域
        register_theme_directory($this->fixtureRoot . '/themes');
        $this->addFilter('theme_root', function () { return $this->fixtureRoot . '/themes'; });
        $this->addFilter('filesystem_method', static function () { return 'direct'; });
        $this->addFilter('pre_http_request', static function () {
            return new \WP_Error('integration-http-disabled', '統合テスト中の外部通信の停止');
        });
        wp_clean_themes_cache(false);
        update_site_option('auto_update_themes', []);
        $this->assertTrue(WP_Filesystem(false, $this->fixtureRoot, true));
        $this->guard = new \Cocoon_Theme_Update_Requirements($this->directory);
    }

    protected function tearDown(): void
    {
        if ($this->guard !== null) {
            remove_filter('upgrader_source_selection', [$this->guard, 'filter_source'], PHP_INT_MAX);
        }
        foreach (array_reverse($this->filters) as [$name, $callback, $priority]) {
            remove_filter($name, $callback, $priority);
        }
        if ($this->fixtureRoot !== null) {
            // テスト専用の生成先に限定した一時ファイルの再帰削除
            $parent = wp_normalize_path(dirname($this->fixtureRoot));
            $expectedParent = rtrim(wp_normalize_path(WP_CONTENT_DIR), '/') . '/upgrade';
            if ($parent !== $expectedParent || strpos(basename($this->fixtureRoot), 'cocoon-requirements-integration-') !== 0) {
                throw new \RuntimeException('テスト用ファイル削除先の不一致');
            }
            $this->assertTrue($GLOBALS['wp_filesystem']->delete($this->fixtureRoot, true));
            $GLOBALS['wp_theme_directories'] = $this->originalThemeDirectories;
            wp_clean_themes_cache(false);
            $this->restoreTransient('update_themes', $this->originalUpdates);
            $this->restoreTransient('theme_roots', $this->originalThemeRoots);
            if ($this->originalAutoUpdates === false) {
                delete_site_option('auto_update_themes');
            } else {
                update_site_option('auto_update_themes', $this->originalAutoUpdates);
            }
            if ($this->originalFailureEmails === false) {
                delete_option('auto_plugin_theme_update_emails');
            } else {
                update_option('auto_plugin_theme_update_emails', $this->originalFailureEmails);
            }
            $this->assertSame($this->originalMaintenanceHash, is_file(ABSPATH . '.maintenance') ? sha1_file(ABSPATH . '.maintenance') : null);
        }
        if ($this->hadFilesystem) {
            $GLOBALS['wp_filesystem'] = $this->originalFilesystem;
        } elseif ($this->fixtureRoot !== null) {
            unset($GLOBALS['wp_filesystem']);
        }
        parent::tearDown();
    }

    /**
     * 手動更新でJSONに要件がなくてもZIP内の適合要件により置換できることを検証
     */
    public function test_manual_update_allows_compatible_zip_without_json_requirements(): void
    {
        $this->assertSuccessfulReplacement($this->performUpdate('manual', '1.0', '1.0'));
    }

    /**
     * 手動更新でJSONの低い要件に依存せずZIP内の必須版不足により停止することを検証
     */
    public function test_manual_update_blocks_incompatible_zip_despite_compatible_json(): void
    {
        $result = $this->performUpdate('manual', '999.0', '1.0', ['requires' => '1.0', 'requires_php' => '1.0']);
        $this->assertBlockedWithoutReplacement($result, ['cocoon_update_requirements_incompatible']);
    }

    /**
     * 手動更新でZIP内の要件が欠落する場合に既存ファイルを維持することを検証
     */
    public function test_manual_update_blocks_zip_with_missing_requirements(): void
    {
        $this->assertBlockedWithoutReplacement($this->performUpdate('manual', null, null), ['cocoon_update_requirements_missing']);
    }

    /**
     * 一括更新でJSONに要件がなくてもZIP内の適合要件により置換できることを検証
     */
    public function test_bulk_update_allows_compatible_zip_without_json_requirements(): void
    {
        $this->assertSuccessfulReplacement($this->performUpdate('bulk', '1.0', '1.0'));
        $this->assertArrayNotHasKey('type', $this->lastUpgrader->runOptions['hook_extra']);
        $this->assertArrayNotHasKey('action', $this->lastUpgrader->runOptions['hook_extra']);
    }

    /**
     * 一括更新でZIP内のPHP必須版不足により既存ファイルを維持することを検証
     */
    public function test_bulk_update_blocks_incompatible_zip_despite_compatible_json(): void
    {
        $result = $this->performUpdate('bulk', '1.0', '999.0', ['requires' => '1.0', 'requires_php' => '1.0']);
        $this->assertBlockedWithoutReplacement($result, ['cocoon_update_requirements_incompatible', 'incompatible_php_required_version']);
    }

    /**
     * 一括更新で標準検査が許可する要件欠落をCocoonの検査で停止することを検証
     */
    public function test_bulk_update_blocks_zip_with_missing_requirements(): void
    {
        $this->assertBlockedWithoutReplacement($this->performUpdate('bulk', null, null), ['cocoon_update_requirements_missing']);
    }

    /**
     * 自動更新の判定と専用スキンを通り要件なしJSONから適合ZIPを更新できることを検証
     */
    public function test_automatic_update_allows_compatible_zip_without_json_requirements(): void
    {
        $this->assertSuccessfulReplacement($this->performUpdate('automatic', '1.0', '1.0'));
        $this->assertInstanceOf(\Automatic_Upgrader_Skin::class, $this->lastUpgrader->skin);
    }

    /**
     * 自動更新の判定後にもZIP内の必須版不足により既存ファイルを維持することを検証
     */
    public function test_automatic_update_blocks_incompatible_zip_despite_compatible_json(): void
    {
        $result = $this->performUpdate('automatic', '999.0', '1.0', ['requires' => '1.0', 'requires_php' => '1.0']);
        $this->assertBlockedWithoutReplacement($result, ['cocoon_update_requirements_incompatible']);
    }

    /**
     * 自動更新の判定後にもZIP内の要件欠落により既存ファイルを維持することを検証
     */
    public function test_automatic_update_blocks_zip_with_missing_requirements(): void
    {
        $this->assertBlockedWithoutReplacement($this->performUpdate('automatic', null, null), ['cocoon_update_requirements_missing']);
    }

    /**
     * 利用者の自動更新停止設定を保ちZIPの展開へ進まないことを検証
     */
    public function test_automatic_update_preserves_disabled_decision(): void
    {
        $this->assertFalse($this->performUpdate('automatic', '1.0', '1.0', ['autoupdate' => false]));
        $this->assertNull($this->lastUpgrader);
        $this->assertSame('old-theme-files', file_get_contents($this->installedPath() . '/marker.txt'));
        $this->assertDirectoryDoesNotExist($this->fixtureRoot . '/unpacked');
    }

    /**
     * 親テーマが直接有効な単独更新の停止時にもメンテナンス状態が終了することを検証
     */
    public function test_active_parent_failure_ends_started_maintenance_mode(): void
    {
        $this->addFilter('pre_option_stylesheet', function () { return $this->directory; });
        $this->addFilter('pre_option_template', function () { return $this->directory; });
        $result = $this->performUpdate('manual', '999.0', '1.0');
        $this->assertBlockedWithoutReplacement($result, ['cocoon_update_requirements_incompatible']);
        $this->assertSame([true, false], $this->lastUpgrader->maintenanceTransitions);
        $this->assertFalse($this->lastUpgrader->maintenanceEnabled);
    }

    /**
     * 他テーマの単独更新はCocoon向け要件確認の影響を受けないことを検証
     */
    public function test_other_theme_update_is_not_blocked(): void
    {
        $this->writeInstalledTheme('another-theme');
        $this->assertSuccessfulReplacement($this->performUpdate('manual', null, null, [], 'another-theme'), 'another-theme');
    }

    /**
     * 先行する展開元選択エラーを保持して既存ファイルの置換を始めないことを検証
     */
    public function test_existing_source_error_is_preserved(): void
    {
        $previousError = new \WP_Error('existing-source-error', '先行処理での更新停止');
        $this->addFilter('upgrader_source_selection', static function () use ($previousError) { return $previousError; }, 1);
        $result = $this->performUpdate('manual', '1.0', '1.0');
        $this->assertSame($previousError, $result);
        $this->assertBlockedWithoutReplacement($result, ['existing-source-error']);
    }

    /**
     * 同じ手動更新オブジェクトの再利用でも要件不足と破損ZIPと成功後の失敗を安全に表示することを検証
     */
    public function test_reused_manual_upgrader_completes_standard_skin_after_repeated_failures(): void
    {
        $result = $this->performUpdate('manual', '999.0', '1.0');
        $this->assertBlockedWithoutReplacement($result, ['cocoon_update_requirements_incompatible']);
        $upgrader = $this->lastUpgrader;
        $this->assertSame(1, $upgrader->skin->completedAfterCalls);

        $this->performUpdate('manual', '1.0', '1.0', [], null, $upgrader, true);
        $this->assertNull($this->lastUpgradeReturn);
        $this->assertNull($upgrader->result);
        $this->assertNull($upgrader->skin->resultDuringAfter);
        $this->assertSame(2, $upgrader->skin->completedAfterCalls);
        $this->assertInstanceOf(\WP_Error::class, $upgrader->skin->lastDisplayedError);
        $this->assertNotSame($result, $upgrader->skin->lastDisplayedError);
        $this->assertStringContainsString($upgrader->skin->lastDisplayedError->get_error_message(), $this->lastOutput);
        $this->assertSame('old-theme-files', file_get_contents($this->installedPath() . '/marker.txt'));

        $this->assertSuccessfulReplacement($this->performUpdate('manual', '1.0', '1.0', [], null, $upgrader));
        $this->assertSame($upgrader, $this->lastUpgrader);
        $this->assertSame(3, $upgrader->skin->completedAfterCalls);

        $installedStyleHash = sha1_file($this->installedPath() . '/style.css');
        $this->assertUpdateError($this->performUpdate('manual', '999.0', '1.0', [], null, $upgrader), ['cocoon_update_requirements_incompatible']);
        $this->assertSame(4, $upgrader->skin->completedAfterCalls);
        $this->assertSame($installedStyleHash, sha1_file($this->installedPath() . '/style.css'));
        $this->assertSame('new-theme-files', file_get_contents($this->installedPath() . '/marker.txt'));
        $this->assertFileDoesNotExist($this->installedPath() . '/legacy.txt');
    }

    private function performUpdate(string $mode, ?string $requiresWp, ?string $requiresPhp, array $metadata = [], ?string $directory = null, ?\Theme_Upgrader $upgrader = null, bool $corruptPackage = false)
    {
        $directory = $directory ?? $this->directory;
        $package = $this->createZip($directory, $requiresWp, $requiresPhp);
        if ($corruptPackage) {
            file_put_contents($package, 'broken-zip-archive');
        }
        $offer = array_merge([
            'theme' => $directory,
            'new_version' => '3.0.0',
            'url' => 'https://example.com/cocoon-integration-test',
            'package' => $package,
            'autoupdate' => true,
        ], $metadata);
        set_site_transient('update_themes', (object) [
            'last_checked' => time(),
            'checked' => [$directory => '1.0.0'],
            'response' => [$directory => $offer],
            'translations' => [],
        ]);
        if ($mode === 'automatic') {
            // 環境固有の停止条件だけを除いたWordPress本体の自動更新判定
            $automatic = new class extends \WP_Automatic_Updater {
                public function is_disabled() { return false; }
                public function is_vcs_checkout($context) { return false; }
            };
            $decision = $automatic->should_update('theme', (object) $offer, $this->fixtureRoot . '/themes');
            if (!$decision) {
                return false;
            }
            $this->assertTrue($decision);
        }
        $this->lastUpgrader = $upgrader ?? $this->makeUpgrader($mode, $directory);
        $bufferLevel = ob_get_level();
        ob_start();
        try {
            if ($mode === 'bulk') {
                $results = $this->lastUpgrader->bulk_upgrade([$directory], ['clear_update_cache' => false]);
                $this->assertIsArray($results);
                $this->lastUpgradeReturn = $results[$directory];
                return $this->lastUpgradeReturn;
            }
            $result = $this->lastUpgrader->upgrade($directory, ['clear_update_cache' => false]);
            $this->lastUpgradeReturn = $result;
            // 先行フィルター等の標準エラーを含む単独更新の失敗結果の取得
            return is_wp_error($this->lastUpgrader->skin->result) ? $this->lastUpgrader->skin->result : $result;
        } finally {
            $this->lastOutput = '';
            // PHPUnitの出力バッファへ影響しない更新表示の回収
            while (ob_get_level() > $bufferLevel) {
                $this->lastOutput = ob_get_clean() . $this->lastOutput;
            }
            $this->assertSame($bufferLevel, ob_get_level());
        }
    }

    private function makeUpgrader(string $mode, string $directory)
    {
        if ($mode === 'manual') {
            // 実際の単独更新画面の後処理を通る標準テーマ更新スキン
            $skin = new class(['theme' => $directory]) extends \Theme_Upgrader_Skin {
                public int $completedAfterCalls = 0;
                public mixed $resultDuringAfter = null;
                public mixed $lastDisplayedError = null;
                public function header() {}
                public function footer() {}
                public function error($errors)
                {
                    $this->lastDisplayedError = $errors;
                    parent::error($errors);
                }
                public function after()
                {
                    $this->resultDuringAfter = $this->upgrader->result;
                    parent::after();
                    ++$this->completedAfterCalls;
                }
                public function feedback($feedback, ...$args)
                {
                    // 全バッファの強制終了だけを省いた標準通知の整形と出力
                    if (isset($this->upgrader->strings[$feedback])) {
                        $feedback = $this->upgrader->strings[$feedback];
                    }
                    if (strpos($feedback, '%') !== false && $args) {
                        $feedback = vsprintf($feedback, array_map('esc_html', array_map('strip_tags', $args)));
                    }
                    if ($feedback !== '') {
                        echo '<p>' . $feedback . "</p>\n";
                    }
                }
            };
        } else {
            // 管理画面を出力せず一括更新にも対応するWordPress標準スキン
            $skin = new class extends \Automatic_Upgrader_Skin {
                public $theme_info;
                // PHPUnitの出力バッファと干渉しない表示処理
                public function header() {}
                public function footer() {}
                public function bulk_header() {}
                public function bulk_footer() {}
            };
        }
        return new class($skin, $this->fixtureRoot) extends \Theme_Upgrader {
            public array $maintenanceTransitions = [];
            public bool $maintenanceEnabled = false;
            public array $runOptions = [];
            private string $fixtureRoot;
            public function __construct($skin, string $fixtureRoot)
            {
                parent::__construct($skin);
                $this->fixtureRoot = $fixtureRoot;
            }
            public function run($options)
            {
                // 実テーマと共有バックアップ先への書き込みを防ぐ対象範囲の照合
                if ($options['destination'] !== $this->fixtureRoot . '/themes') {
                    throw new \RuntimeException('テスト用更新先の不一致');
                }
                unset($options['hook_extra']['temp_backup']);
                $this->runOptions = $options;
                return parent::run($options);
            }
            public function unpack_package($package, $delete_package = true)
            {
                // WordPress標準のZIP展開とテスト専用作業領域への限定
                $workingDirectory = $this->fixtureRoot . '/unpacked';
                if (!wp_mkdir_p($workingDirectory)) {
                    return new \WP_Error('fixture-unpack-directory', 'テスト用ZIP展開先の作成失敗');
                }
                $result = unzip_file($package, $workingDirectory);
                return is_wp_error($result) ? $result : $workingDirectory;
            }
            public function maintenance_mode($enable = false)
            {
                // 実サイトのメンテナンスファイルに触れない状態遷移の記録
                $this->maintenanceTransitions[] = (bool) $enable;
                $this->maintenanceEnabled = (bool) $enable;
            }
        };
    }

    private function createZip(string $directory, ?string $requiresWp, ?string $requiresPhp): string
    {
        $source = $this->fixtureRoot . '/package/' . $directory;
        $this->assertTrue(wp_mkdir_p($source));
        $header = "/*\nTheme Name: Test Update\nVersion: 3.0.0\n";
        if ($requiresWp !== null) {
            $header .= 'Requires at least: ' . $requiresWp . "\n";
        }
        if ($requiresPhp !== null) {
            $header .= 'Requires PHP: ' . $requiresPhp . "\n";
        }
        file_put_contents($source . '/style.css', $header . "*/\n");
        file_put_contents($source . '/index.php', '<?php throw new RuntimeException("更新ZIP内のPHP実行は禁止");');
        file_put_contents($source . '/marker.txt', 'new-theme-files');
        $package = $this->fixtureRoot . '/update.zip';
        // 追加のPHP拡張なしで作成する実際のテーマZIP
        $archive = new \PclZip($package);
        $this->assertIsArray($archive->create($source, PCLZIP_OPT_REMOVE_PATH, dirname($source)), $archive->errorInfo());
        return $package;
    }

    private function writeInstalledTheme(string $directory): void
    {
        $installed = $this->installedPath($directory);
        $this->assertTrue(wp_mkdir_p($installed));
        file_put_contents($installed . '/style.css', "/*\nTheme Name: Installed Test Theme\nVersion: 1.0.0\nRequires at least: 1.0\nRequires PHP: 1.0\n*/");
        file_put_contents($installed . '/index.php', '<?php');
        file_put_contents($installed . '/marker.txt', 'old-theme-files');
        file_put_contents($installed . '/legacy.txt', 'old-theme-only');
    }

    private function installedPath(?string $directory = null): string
    {
        return $this->fixtureRoot . '/themes/' . ($directory ?? $this->directory);
    }

    private function assertSuccessfulReplacement($result, ?string $directory = null): void
    {
        $this->assertTrue($result === true || is_array($result));
        if ($this->lastUpgrader->skin instanceof \Theme_Upgrader_Skin) {
            $this->assertGreaterThan(0, $this->lastUpgrader->skin->completedAfterCalls);
            $this->assertIsArray($this->lastUpgrader->skin->resultDuringAfter);
            $this->assertStringContainsString($this->lastUpgrader->strings['process_success'], $this->lastOutput);
        }
        $this->assertSame('new-theme-files', file_get_contents($this->installedPath($directory) . '/marker.txt'));
        $this->assertFileDoesNotExist($this->installedPath($directory) . '/legacy.txt');
        $this->assertSame('3.0.0', get_file_data($this->installedPath($directory) . '/style.css', ['Version' => 'Version'])['Version']);
    }

    private function assertBlockedWithoutReplacement($result, array $errorCodes): void
    {
        $this->assertUpdateError($result, $errorCodes);
        $this->assertSame('old-theme-files', file_get_contents($this->installedPath() . '/marker.txt'));
        $this->assertFileExists($this->installedPath() . '/legacy.txt');
        $this->assertSame('1.0.0', get_file_data($this->installedPath() . '/style.css', ['Version' => 'Version'])['Version']);
        $this->assertFileExists($this->fixtureRoot . '/update.zip');
    }

    private function assertUpdateError($result, array $errorCodes): void
    {
        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertContains($result->get_error_code(), $errorCodes);
        if (strpos($result->get_error_code(), 'cocoon_update_requirements_') === 0) {
            if (!($this->lastUpgrader->skin instanceof \Theme_Upgrader_Skin)) {
                // 一括更新の実行結果と自動更新の呼び出し元へ残る同一エラーの確認
                $this->assertSame($result, $this->lastUpgradeReturn);
                if (!$this->lastUpgrader->bulk) {
                    $this->assertSame($result, $this->lastUpgrader->result);
                }
            }
            $this->assertSame($result, $this->lastUpgrader->skin->result);
        }
        if ($this->lastUpgrader->skin instanceof \Theme_Upgrader_Skin) {
            $this->assertNull($this->lastUpgradeReturn);
            $this->assertNull($this->lastUpgrader->result);
            $this->assertGreaterThan(0, $this->lastUpgrader->skin->completedAfterCalls);
            $this->assertNull($this->lastUpgrader->skin->resultDuringAfter);
            $this->assertStringContainsString($result->get_error_message(), $this->lastOutput);
        }
    }

    private function addFilter(string $name, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_filter($name, $callback, $priority, $acceptedArgs);
        $this->filters[] = [$name, $callback, $priority];
    }

    private function restoreTransient(string $name, $value): void
    {
        if ($value === false) {
            delete_site_transient($name);
        } else {
            set_site_transient($name, $value);
        }
    }
}
