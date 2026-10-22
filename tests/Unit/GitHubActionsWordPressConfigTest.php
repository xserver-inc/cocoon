<?php
/**
 * GitHub Actionsで生成するWordPress設定の環境変数・既定値の回帰テスト
 */
namespace Cocoon\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Cocoon\Tests\Support\ProcessEnvironment;

require_once dirname(__DIR__) . '/Support/ProcessEnvironment.php';

class GitHubActionsWordPressConfigTest extends TestCase
{
    private const DEFAULTS = array(
        'WORDPRESS_DB_NAME' => 'wordpress_test',
        'WORDPRESS_DB_USER' => 'root',
        'WORDPRESS_DB_PASSWORD' => 'root',
        'WORDPRESS_DB_HOST' => '127.0.0.1',
        'WORDPRESS_TABLE_PREFIX' => 'wptests_',
    );

    private ?string $temporaryRoot = null;

    protected function setUp(): void
    {
        if (PHP_OS_FAMILY === 'Windows') $this->markTestSkipped('CIのシェルによる設定生成はLinux環境で検証');
        $this->temporaryRoot = sys_get_temp_dir() . '/cocoon-ci-config-' . bin2hex(random_bytes(8));
        mkdir($this->temporaryRoot . '/tests', 0700, true);
        mkdir($this->temporaryRoot . '/wordpress', 0700, true);
        // 実DBへ接続しない、WordPress初期化の呼び出し確認用ファイル
        file_put_contents($this->temporaryRoot . '/wordpress/wp-settings.php', '<?php $GLOBALS["ci_settings_loaded"] = true;');

        $workflow = file_get_contents(dirname(__DIR__, 2) . '/.github/workflows/phpunit.yml');
        $parent = $this->generationBlock($workflow, '$WP_TESTS_DIR/wp-tests-config.php', 'EOF');
        $child = $this->generationBlock($workflow, '$WP_CORE_DIR/wp-config.php', "'EOF'");
        $environment = getenv();
        $environment['WP_TESTS_DIR'] = $this->temporaryRoot . '/tests';
        $environment['WP_CORE_DIR'] = $this->temporaryRoot . '/wordpress';
        $this->runProcess(array('/bin/sh', '-c', "set -eu\n" . $parent . "\n" . $child), $environment);
    }

    protected function tearDown(): void
    {
        if ($this->temporaryRoot === null) return;
        foreach (array('/tests/wp-tests-config.php', '/wordpress/wp-config.php', '/wordpress/wp-settings.php') as $path) {
            if (is_file($this->temporaryRoot . $path)) unlink($this->temporaryRoot . $path);
        }
        foreach (array('/tests', '/wordpress', '') as $path) {
            if (is_dir($this->temporaryRoot . $path)) rmdir($this->temporaryRoot . $path);
        }
    }

    private function generationBlock(string $workflow, string $target, string $delimiter): string
    {
        // Windowsで取得したYAMLにも対応する、シェル入力のLF改行への統一
        $workflow = str_replace("\r\n", "\n", $workflow);
        // YAMLの字下げを除いた、実際のヒアドキュメント生成処理の抽出
        $pattern = '/^([ \t]*)cat > ' . preg_quote($target, '/') . ' << ' . preg_quote($delimiter, '/') . '\R.*?^\1EOF\r?$/ms';
        $this->assertSame(1, preg_match($pattern, $workflow, $matches), $target . ' の設定生成ブロックの欠落');
        return preg_replace('/^' . preg_quote($matches[1], '/') . '/m', '', $matches[0]);
    }

    private function runProcess(array $command, array $environment): string
    {
        // 設定の独立読み込みと、標準出力・標準エラーの分離
        $process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, $this->temporaryRoot, ProcessEnvironment::preserveEmptyValues($environment));
        $this->assertIsResource($process);
        try {
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $this->assertSame(0, proc_close($process), $stderr);
            $this->assertSame('', $stderr, '設定読み込み時の警告やエラー');
            return $stdout;
        } finally {
            foreach ($pipes as $pipe) if (is_resource($pipe)) fclose($pipe);
            if (is_resource($process)) {
                proc_terminate($process, 9);
                proc_close($process);
            }
        }
    }

    #[DataProvider('configurationProvider')]
    public function testGeneratedConfigurationPreservesExplicitEnvironmentValues(string $mode, array $values, array $expected): void
    {
        $environment = getenv();
        foreach (array_keys(self::DEFAULTS) as $name) unset($environment[$name]);
        foreach ($values as $name => $value) $environment[$name] = $value;
        $environment['WP_TESTS_PHPUNIT_POLYFILLS_PATH'] = '/unused-polyfills';
        $configuration = $this->temporaryRoot . ($mode === 'parent' ? '/tests/wp-tests-config.php' : '/wordpress/wp-config.php');
        // 初期化順序と既存定数の保持を確認する、別PHPプロセス用コード
        $source = <<<'PHP'
set_error_handler(static function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
define('MYSQL_CLIENT_FLAGS', 2);
define('WP_MEMORY_LIMIT', '96M');
define('WP_MAX_MEMORY_LIMIT', '96M');
if ($argv[2] === 'child-predefined') define('ABSPATH', dirname($argv[1]) . '/');
require $argv[1];
echo json_encode(array(
    'connection' => array('WORDPRESS_DB_NAME' => DB_NAME, 'WORDPRESS_DB_USER' => DB_USER, 'WORDPRESS_DB_PASSWORD' => DB_PASSWORD, 'WORDPRESS_DB_HOST' => DB_HOST, 'WORDPRESS_TABLE_PREFIX' => $table_prefix),
    'root' => ABSPATH,
    'settings_loaded' => $GLOBALS['ci_settings_loaded'] ?? false,
    'flags' => array(MYSQL_CLIENT_FLAGS, WP_MEMORY_LIMIT, WP_MAX_MEMORY_LIMIT),
), JSON_THROW_ON_ERROR);
PHP;
        $stdout = $this->runProcess(array(PHP_BINARY, '-r', $source, $configuration, $mode), $environment);
        $result = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($expected, $result['connection']);
        $this->assertSame($this->temporaryRoot . '/wordpress/', $result['root']);
        $this->assertSame($mode !== 'parent', $result['settings_loaded']);
        $this->assertSame(array(2, '96M', '96M'), $result['flags']);
    }

    public static function configurationProvider(): iterable
    {
        $custom = array('WORDPRESS_DB_NAME' => 'ci_test', 'WORDPRESS_DB_USER' => 'ci_user', 'WORDPRESS_DB_PASSWORD' => 'ci_password', 'WORDPRESS_DB_HOST' => 'db.invalid:3307', 'WORDPRESS_TABLE_PREFIX' => 'ci_');
        $special = array('WORDPRESS_DB_PASSWORD' => "quote'\" dollar\$ backtick\x60 slash\\\n日本語");
        $empty = array_fill_keys(array_keys(self::DEFAULTS), '');
        $zero = array_fill_keys(array_keys(self::DEFAULTS), '0');
        $mixed = array('WORDPRESS_DB_USER' => '0', 'WORDPRESS_DB_PASSWORD' => '', 'WORDPRESS_TABLE_PREFIX' => '0');
        $cases = array(
            'unset' => array(array(), self::DEFAULTS),
            'custom' => array($custom, $custom),
            'special' => array($special, array_replace(self::DEFAULTS, $special)),
            'empty' => array($empty, $empty),
            'zero' => array($zero, $zero),
            'mixed' => array($mixed, array_replace(self::DEFAULTS, $mixed)),
        );
        foreach (array('parent', 'child', 'child-predefined') as $mode) {
            foreach ($cases as $name => $case) yield $mode . '/' . $name => array($mode, $case[0], $case[1]);
        }
    }
}
