<?php
/**
 * 旧WordPress向けJSXランタイムの条件付き登録テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class BlockJsxRuntimeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/blocks/src/legacy-jsx-runtime.php';
    }

    /**
     * JSXランタイムが不要な場合の登録処理の除外
     */
    public function test_ランタイムが不要な場合は登録状態を調べない(): void
    {
        Functions\expect('wp_script_is')->never();
        Functions\expect('wp_register_script')->never();
        $this->assertNull(cocoon_register_legacy_jsx_runtime(['wp-element']));
    }

    /**
     * WordPress本体やGutenbergが登録済みの標準ランタイムの優先
     */
    public function test_登録済みのランタイムを上書きしない(): void
    {
        Functions\expect('wp_script_is')->once()->with('react-jsx-runtime', 'registered')->andReturn(true);
        Functions\expect('wp_register_script')->never();
        $this->assertNull(cocoon_register_legacy_jsx_runtime(['react-jsx-runtime']));
    }

    /**
     * 未登録の旧WordPressに対する互換スクリプトの登録
     */
    public function test_未登録の場合だけ互換ランタイムを登録する(): void
    {
        $theme_root = dirname(__DIR__, 2);
        $asset_file = require $theme_root . '/blocks/dist/legacy-jsx-runtime.asset.php';
        Functions\expect('wp_script_is')->once()->with('react-jsx-runtime', 'registered')->andReturn(false);
        Functions\when('get_cocoon_template_directory')->justReturn($theme_root);
        Functions\when('get_cocoon_template_directory_uri')->justReturn('https://example.com/cocoon');
        Functions\expect('wp_register_script')->once()->with(
            'react-jsx-runtime',
            'https://example.com/cocoon/blocks/dist/legacy-jsx-runtime.js',
            ['wp-element'],
            $asset_file['version']
        )->andReturn(true);
        cocoon_register_legacy_jsx_runtime(['react-jsx-runtime']);
        $this->assertSame(['wp-element'], $asset_file['dependencies']);
    }
}
