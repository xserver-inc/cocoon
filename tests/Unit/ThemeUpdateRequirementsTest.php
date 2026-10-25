<?php
/**
 * 更新ZIP内のテーマヘッダーと動作要件の単体テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class ThemeUpdateRequirementsTest extends TestCase
{
    private $savedGlobals = array();
    private $hadGlobals = array();
    private $filesystem;
    private $source = '/tmp/cocoon-update/package/cocoon-master';
    private $remoteSource = '/tmp/cocoon-update/package/';

    protected function setUp(): void
    {
        parent::setUp();
        foreach (array('wp_version', 'wp_filesystem') as $name) {
            $this->hadGlobals[$name] = array_key_exists($name, $GLOBALS);
            $this->savedGlobals[$name] = $GLOBALS[$name] ?? null;
        }
        $GLOBALS['wp_version'] = '6.7';
        Functions\when('get_stylesheet')->justReturn('cocoon-child');

        // 実ファイルや通信に依存しない展開済みZIPの読み取り環境
        $this->filesystem = new class {
            public $contents;
            public $reads = array();

            public function get_contents($path)
            {
                $this->reads[] = $path;
                return $this->contents;
            }
        };
        $this->filesystem->contents = $this->headers();
        $GLOBALS['wp_filesystem'] = $this->filesystem;
        require_once dirname(__DIR__, 2) . '/lib/theme-update-requirements.php';
    }

    protected function tearDown(): void
    {
        foreach (array('wp_version', 'wp_filesystem') as $name) {
            if ($this->hadGlobals[$name]) {
                $GLOBALS[$name] = $this->savedGlobals[$name];
            } else {
                unset($GLOBALS[$name]);
            }
        }
        parent::tearDown();
    }

    private function headers($wordpress = '6.1.0', $php = '7.4.0'): string
    {
        return "/*\nTheme Name: Cocoon\nVersion: 3.0.0\nRequires at least: {$wordpress}\nRequires PHP: {$php}\n*/\nbody { color: #333; }";
    }

    private function target(array $overrides = array()): array
    {
        return array_merge(array('type' => 'theme', 'action' => 'update', 'theme' => 'cocoon-master'), $overrides);
    }

    private function filterSource($extra = null)
    {
        $guard = new \Cocoon_Theme_Update_Requirements('cocoon-master');
        return $guard->filter_source($this->source, $this->remoteSource, new \stdClass(), $extra ?? $this->target());
    }

    /**
     * 更新ZIPから最低WordPress版と最低PHP版を取得することを検証
     */
    public function test_parser_reads_both_requirements_from_theme_header(): void
    {
        $this->assertSame(array('requires' => '6.1.0', 'requires_php' => '7.4.0'), \Cocoon_Theme_Update_Requirements::parse_requirements($this->headers()));
    }

    /**
     * LFとCRLFとCRの各改行形式による最低要件の取得を検証
     */
    public function test_parser_supports_wordpress_header_line_endings(): void
    {
        foreach (array("\n", "\r\n", "\r") as $lineEnding) {
            $this->assertSame(array('requires' => '6.1.0', 'requires_php' => '7.4.0'), \Cocoon_Theme_Update_Requirements::parse_requirements(str_replace("\n", $lineEnding, $this->headers())));
        }
    }

    /**
     * コメント接頭辞と大小文字と空白によるWordPress形式のヘッダー取得を検証
     */
    public function test_parser_supports_comment_prefixes_and_case_insensitive_headers(): void
    {
        $contents = "/**\n * reQUIres at least:\t6.1\t\n * REQUIRES PHP: 7.4 \n */";
        $this->assertSame(array('requires' => '6.1', 'requires_php' => '7.4'), \Cocoon_Theme_Update_Requirements::parse_requirements($contents));
    }

    /**
     * 1行コメントの閉じ記号を最低要件へ混入させないことを検証
     */
    public function test_parser_removes_trailing_comment_closers(): void
    {
        $this->assertSame(array('requires' => '6.1', 'requires_php' => '7.4'), \Cocoon_Theme_Update_Requirements::parse_requirements("/* Requires at least: 6.1 */\n/* Requires PHP: 7.4 */"));
    }

    /**
     * 片方または両方の必須ヘッダーの欠落を不明として扱うことを検証
     */
    public function test_parser_rejects_missing_or_empty_requirement_headers(): void
    {
        foreach (array('', '/* Theme Name: Cocoon */', 'Requires at least: 6.1', 'Requires PHP: 7.4', "Requires at least:\nRequires PHP: 7.4", "Requires at least: 6.1\nRequires PHP:") as $contents) {
            $this->assertNull(\Cocoon_Theme_Update_Requirements::parse_requirements($contents));
        }
    }

    /**
     * 読取失敗や文字列以外の内容を不明として扱うことを検証
     */
    public function test_parser_rejects_non_string_contents(): void
    {
        foreach (array(null, false, true, 0, array(), new \stdClass()) as $contents) {
            $this->assertNull(\Cocoon_Theme_Update_Requirements::parse_requirements($contents));
        }
    }

    /**
     * 説明文や開発版や全ゼロなどの不正な最低要件を拒否することを検証
     */
    public function test_parser_rejects_malformed_requirements_for_either_runtime(): void
    {
        foreach (array('latest', '>=7.4', 'PHP 7.4', '7.4beta1', '7.4.0.0.1', '0', '0.0.0', str_repeat('1', 33), '7.4<script>') as $invalid) {
            $this->assertNull(\Cocoon_Theme_Update_Requirements::parse_requirements($this->headers($invalid, '7.4')));
            $this->assertNull(\Cocoon_Theme_Update_Requirements::parse_requirements($this->headers('6.1', $invalid)));
        }
    }

    /**
     * ヘッダーの最初8KiBを超える本文内の要件を採用しないことを検証
     */
    public function test_parser_does_not_use_requirements_after_first_eight_kibibytes(): void
    {
        $this->assertNull(\Cocoon_Theme_Update_Requirements::parse_requirements(str_repeat(' ', 8192) . "\n" . $this->headers()));
    }

    /**
     * 先頭8KiBの境界で切れた最低要件行を低い版として採用しないことを検証
     */
    public function test_parser_rejects_requirement_lines_truncated_at_header_boundary(): void
    {
        foreach (array(
            array("Requires at least: 1.0\n", 'Requires PHP: 8.3'),
            array("Requires PHP: 1.0\n", 'Requires at least: 6.1'),
        ) as $case) {
            $contents = $case[0] . str_repeat(' ', 8192 - strlen($case[0]) - strlen($case[1])) . $case[1] . ".99\n";
            $this->assertNull(\Cocoon_Theme_Update_Requirements::parse_requirements($contents));
        }
    }

    /**
     * 手動と一括と自動の更新経路で適合するZIPを許可することを検証
     */
    public function test_zip_validation_allows_compatible_manual_bulk_and_automatic_contexts(): void
    {
        foreach (array($this->target(), array('theme' => 'cocoon-master'), $this->target(array('autoupdate' => true))) as $extra) {
            $this->assertSame($this->source, $this->filterSource($extra));
        }
        $this->assertSame(array_fill(0, 3, $this->source . '/style.css'), $this->filesystem->reads);
    }

    /**
     * 更新JSONやインストール済み要件の保存値を参照しないことを検証
     */
    public function test_zip_validation_does_not_read_metadata_or_saved_requirements(): void
    {
        Functions\expect('get_site_transient')->never();
        Functions\expect('get_site_option')->never();
        $this->assertSame($this->source, $this->filterSource());
        $this->assertSame(array($this->source . '/style.css'), $this->filesystem->reads);
    }

    /**
     * style.cssの読取失敗や必須ヘッダー欠落による更新中止を検証
     */
    public function test_zip_validation_rejects_unreadable_or_invalid_style_contents(): void
    {
        foreach (array(false, null, '', '/* Theme Name: Cocoon */', $this->headers('6.1', 'latest')) as $contents) {
            $this->filesystem->contents = $contents;
            $error = $this->filterSource();
            $this->assertInstanceOf('\WP_Error', $error);
            $this->assertStringContainsString('Cocoon', $error->get_error_message());
        }
    }

    /**
     * ファイルシステムの未初期化による更新中止を検証
     */
    public function test_zip_validation_rejects_missing_filesystem(): void
    {
        foreach (array(null, false, new \stdClass()) as $filesystem) {
            $GLOBALS['wp_filesystem'] = $filesystem;
            $this->assertInstanceOf('\WP_Error', $this->filterSource());
        }
    }

    /**
     * 展開元パスが文字列として利用できない場合の更新中止を検証
     */
    public function test_zip_validation_rejects_invalid_source_paths(): void
    {
        $guard = new \Cocoon_Theme_Update_Requirements('cocoon-master');
        foreach (array(null, false, '', array(), new \stdClass()) as $source) {
            $this->assertInstanceOf('\WP_Error', $guard->filter_source($source, $this->remoteSource, new \stdClass(), $this->target()));
        }
        $this->assertSame(array(), $this->filesystem->reads);
    }

    /**
     * WordPressとPHPそれぞれの最低要件不足によるZIP拒否を検証
     */
    public function test_zip_validation_rejects_insufficient_runtime_versions(): void
    {
        foreach (array(array('999.0', '1.0'), array('1.0', '999.0')) as $versions) {
            $this->filesystem->contents = $this->headers($versions[0], $versions[1]);
            $error = $this->filterSource();
            $this->assertInstanceOf('\WP_Error', $error);
            $this->assertStringContainsString($versions[0], $error->get_error_message());
            $this->assertStringContainsString($versions[1], $error->get_error_message());
        }
    }

    /**
     * 先行フィルターのエラーをZIP検査で上書きしないことを検証
     */
    public function test_zip_validation_preserves_previous_errors_without_reading_files(): void
    {
        $guard = new \Cocoon_Theme_Update_Requirements('cocoon-master');
        $error = new \WP_Error('existing_error', '先行する更新処理のエラー');
        $this->assertSame($error, $guard->filter_source($error, $this->remoteSource, new \stdClass(), $this->target()));
        $this->assertSame(array(), $this->filesystem->reads);
    }

    /**
     * 他テーマとプラグインと新規インストールを対象から除外することを検証
     */
    public function test_zip_validation_preserves_unrelated_update_contexts(): void
    {
        $guard = new \Cocoon_Theme_Update_Requirements('cocoon-master');
        foreach (array(null, false, array(), array('theme' => 'other'), $this->target(array('type' => 'plugin')), $this->target(array('action' => 'install'))) as $extra) {
            $this->assertSame($this->source, $guard->filter_source($this->source, $this->remoteSource, new \stdClass(), $extra));
        }
        $this->assertSame(array(), $this->filesystem->reads);
    }

    /**
     * cocoon-master以外の実ディレクトリ名でも検査が働くことを検証
     */
    public function test_zip_validation_uses_actual_parent_theme_directory(): void
    {
        $guard = new \Cocoon_Theme_Update_Requirements('cocoon');
        $this->filesystem->contents = $this->headers('1.0', '999.0');
        $this->assertInstanceOf('\WP_Error', $guard->filter_source($this->source, $this->remoteSource, new \stdClass(), $this->target(array('theme' => 'cocoon'))));
    }

    /**
     * 同じ最低版または新しい版で更新を許可することを検証
     */
    public function test_version_check_allows_equal_or_newer_versions(): void
    {
        $requirements = array('requires' => '6.1', 'requires_php' => '7.4');
        $this->assertNull(\Cocoon_Theme_Update_Requirements::check_versions($requirements, '6.1', '7.4'));
        $this->assertNull(\Cocoon_Theme_Update_Requirements::check_versions($requirements, '7.1.3', '8.3.36'));
    }

    /**
     * 安定版の末尾ゼロの省略による同じ最低版の許可を検証
     */
    public function test_version_check_treats_omitted_trailing_zero_components_as_equal(): void
    {
        foreach (array('6.1', '6.1.0', '6.1.0.0') as $requiredWp) {
            foreach (array('7.4', '7.4.0', '7.4.0.0') as $requiredPhp) {
                $requirements = array('requires' => $requiredWp, 'requires_php' => $requiredPhp);
                $this->assertNull(\Cocoon_Theme_Update_Requirements::check_versions($requirements, '6.1', '7.4'));
                $this->assertNull(\Cocoon_Theme_Update_Requirements::check_versions($requirements, '6.1.0', '7.4.0'));
            }
        }
    }

    /**
     * 最低安定版と同じ数値のWordPress開発版の拒否を検証
     */
    public function test_version_check_rejects_wordpress_prereleases_of_required_stable_version(): void
    {
        $requirements = array('requires' => '6.1', 'requires_php' => '7.4');
        foreach (array('6.1-beta1', '6.1.0-beta1', '6.1-RC1', '6.1.0-RC1', '6.1-dev', '6.1.0-dev') as $wpVersion) {
            $error = \Cocoon_Theme_Update_Requirements::check_versions($requirements, $wpVersion, '8.3.36');
            $this->assertInstanceOf('\WP_Error', $error);
            $this->assertStringContainsString($wpVersion, $error->get_error_message());
        }
    }

    /**
     * 最低安定版より高い修正版の開発版の許可を検証
     */
    public function test_version_check_allows_prereleases_of_higher_patch_versions(): void
    {
        $requirements = array('requires' => '6.1', 'requires_php' => '7.4');
        foreach (array(array('6.1.1-beta1', '7.4.1beta1'), array('6.1.1-RC1', '7.4.1RC1'), array('6.1.1-dev', '7.4.1-dev')) as $versions) {
            $this->assertNull(\Cocoon_Theme_Update_Requirements::check_versions($requirements, $versions[0], $versions[1]));
        }
    }

    /**
     * 最低安定版と同じ数値のPHP開発版の拒否を検証
     */
    public function test_version_check_rejects_php_prereleases_of_required_stable_version(): void
    {
        foreach (array('8.4', '8.4.0') as $requiredPhp) {
            $requirements = array('requires' => '6.1', 'requires_php' => $requiredPhp);
            foreach (array('8.4.0RC1', '8.4.0beta1', '8.4.0-dev') as $phpVersion) {
                $error = \Cocoon_Theme_Update_Requirements::check_versions($requirements, '7.1.3', $phpVersion);
                $this->assertInstanceOf('\WP_Error', $error);
                $this->assertStringContainsString($phpVersion, $error->get_error_message());
            }
        }
    }

    /**
     * WordPressとPHPそれぞれの最低版不足による更新拒否を検証
     */
    public function test_version_check_rejects_either_unsupported_runtime(): void
    {
        $requirements = array('requires' => '6.1', 'requires_php' => '7.4');
        foreach (array(array('5.4.21', '8.3.36'), array('7.1.3', '5.6.40'), array('6.0.9', '7.3.99')) as $versions) {
            $error = \Cocoon_Theme_Update_Requirements::check_versions($requirements, $versions[0], $versions[1]);
            $this->assertInstanceOf('\WP_Error', $error);
            $this->assertStringContainsString($versions[0], $error->get_error_message());
            $this->assertStringContainsString($versions[1], $error->get_error_message());
            $this->assertStringContainsString('6.1', $error->get_error_message());
            $this->assertStringContainsString('7.4', $error->get_error_message());
        }
    }

    /**
     * 欠落または不正な最低要件による更新中止を検証
     */
    public function test_version_check_fails_closed_for_unknown_requirements(): void
    {
        foreach (array(null, false, array(), array('requires' => '6.1'), array('requires' => '6.1', 'requires_php' => ''), array('requires' => 'latest', 'requires_php' => '7.4')) as $requirements) {
            $this->assertInstanceOf('\WP_Error', \Cocoon_Theme_Update_Requirements::check_versions($requirements, '7.1.3', '8.3.36'));
        }
    }

    /**
     * 数値または文字列による全ゼロの最低要件を受け入れないことを検証
     */
    public function test_version_check_rejects_zero_requirements_for_both_runtimes(): void
    {
        foreach (array('requires', 'requires_php') as $field) {
            foreach (array(0, 0.0, '0', '0.0', '0.0.0') as $zero) {
                $requirements = array('requires' => '6.1', 'requires_php' => '7.4');
                $requirements[$field] = $zero;
                $this->assertInstanceOf('\WP_Error', \Cocoon_Theme_Update_Requirements::check_versions($requirements, '7.1.3', '8.3.36'));
            }
        }
    }
}
