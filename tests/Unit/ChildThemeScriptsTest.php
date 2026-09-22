<?php
// 子テーマのJavaScript読み込み条件の回帰テスト

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class ChildThemeScriptsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // WordPress起動前のテストに必要なスクリプト定数の定義
        if (!defined('THEME_JS')) {
            define('THEME_JS', THEME_NAME . '-js');
        }
        if (!defined('THEME_CHILD_JS')) {
            define('THEME_CHILD_JS', THEME_NAME . '-child-js');
        }
        if (!defined('THEME_CHILD_JS_URL')) {
            define('THEME_CHILD_JS_URL', 'https://example.com/wp-content/themes/cocoon-child/javascript.js');
        }
    }

    public function test_子テーマにJavaScriptがなければ読み込まない(): void
    {
        $childDirectory = dirname(__DIR__) . '/fixtures';
        $this->assertFileDoesNotExist($childDirectory . '/javascript.js');
        Functions\when('is_child_theme')->justReturn(true);
        Functions\when('get_cocoon_stylesheet_directory')->justReturn($childDirectory);

        $calls = array();
        Functions\when('wp_enqueue_script')->alias(function (...$args) use (&$calls) {
            $calls[] = $args;
        });

        wp_enqueue_script_theme_child_js();

        $this->assertSame(array(), $calls);
    }

    public function test_子テーマにJavaScriptがあれば従来の依存関係とフッター指定で読み込む(): void
    {
        // 既存の実ファイルを使った、JavaScriptを持つ子テーマの再現
        $childDirectory = dirname(__DIR__, 2);
        $this->assertFileExists($childDirectory . '/javascript.js');
        Functions\when('is_child_theme')->justReturn(true);
        Functions\when('get_cocoon_stylesheet_directory')->justReturn($childDirectory);

        $calls = array();
        Functions\when('wp_enqueue_script')->alias(function (...$args) use (&$calls) {
            $calls[] = $args;
        });

        wp_enqueue_script_theme_child_js();

        $this->assertSame(array(
            array(THEME_CHILD_JS, THEME_CHILD_JS_URL, array('jquery', THEME_JS), false, true),
        ), $calls);
    }

    public function test_親テーマ単独の場合は子テーマのJavaScriptを読み込まない(): void
    {
        Functions\when('is_child_theme')->justReturn(false);
        Functions\expect('get_cocoon_stylesheet_directory')->never();

        $calls = array();
        Functions\when('wp_enqueue_script')->alias(function (...$args) use (&$calls) {
            $calls[] = $args;
        });

        wp_enqueue_script_theme_child_js();

        $this->assertSame(array(), $calls);
    }
}
