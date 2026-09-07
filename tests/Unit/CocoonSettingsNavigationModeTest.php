<?php
/**
 * Cocoon設定ナビゲーションの表示モード保存を検証する。
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CocoonSettingsNavigationModeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Brain Monkeyの有効化後に一度だけ管理画面関数を読み込む。
        if (!function_exists('cocoon_normalize_settings_navigation_mode')) {
            require_once dirname(__DIR__, 2) . '/lib/admin.php';
        }

        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    #[DataProvider('navigationModeProvider')]
    public function test_表示モードは許可した2値以外を従来表示へ戻す(mixed $mode, string $expected): void
    {
        $this->assertSame($expected, cocoon_normalize_settings_navigation_mode($mode));
    }

    public static function navigationModeProvider(): array
    {
        return [
            'レスポンシブ表示' => ['responsive', 'responsive'],
            '従来タブ表示' => ['tabs', 'tabs'],
            '未保存' => ['', 'tabs'],
            'null' => [null, 'tabs'],
            '配列' => [['responsive'], 'tabs'],
            '未知の値' => ['modern', 'tabs'],
            '大文字' => ['RESPONSIVE', 'tabs'],
        ];
    }

    public function test_表示モードは現在サイトのユーザー設定から読み込む(): void
    {
        Functions\expect('get_current_user_id')->once()->andReturn(42);
        Functions\expect('get_user_option')
            ->once()
            ->with(COCOON_SETTINGS_NAVIGATION_MODE_OPTION, 42)
            ->andReturn('responsive');

        $this->assertSame('responsive', cocoon_get_settings_navigation_mode());
    }

    public function test_未保存ユーザーはDBへ書き込まず従来表示になる(): void
    {
        Functions\expect('get_current_user_id')->once()->andReturn(42);
        Functions\expect('get_user_option')
            ->once()
            ->with(COCOON_SETTINGS_NAVIGATION_MODE_OPTION, 42)
            ->andReturn(false);
        Functions\expect('update_user_option')->never();

        $this->assertSame('tabs', cocoon_get_settings_navigation_mode());
    }

    public function test_権限がない場合は表示モードを保存しない(): void
    {
        $_POST = ['mode' => 'responsive', 'nonce' => 'nonce'];

        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'forbidden'], 403);
        Functions\expect('check_ajax_referer')->never();
        Functions\expect('update_user_option')->never();

        cocoon_ajax_save_settings_navigation_mode();
        $this->addToAssertionCount(1);
    }

    public function test_不正な表示モードは保存しない(): void
    {
        $_POST = ['mode' => 'invalid-mode', 'nonce' => 'nonce'];

        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cocoon_settings_navigation_mode', 'nonce');
        Functions\expect('wp_unslash')->once()->with('invalid-mode')->andReturn('invalid-mode');
        Functions\expect('sanitize_key')->once()->with('invalid-mode')->andReturn('invalid-mode');
        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'invalid_mode'], 400);
        Functions\expect('get_current_user_id')->never();
        Functions\expect('update_user_option')->never();

        cocoon_ajax_save_settings_navigation_mode();
        $this->addToAssertionCount(1);
    }

    public function test_配列の表示モードはサニタイズ前に拒否する(): void
    {
        $_POST = ['mode' => ['responsive'], 'nonce' => 'nonce'];

        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cocoon_settings_navigation_mode', 'nonce');
        Functions\expect('wp_unslash')->never();
        Functions\expect('sanitize_key')->never();
        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'invalid_mode'], 400);
        Functions\expect('update_user_option')->never();

        cocoon_ajax_save_settings_navigation_mode();
        $this->addToAssertionCount(1);
    }

    #[DataProvider('savableNavigationModeProvider')]
    public function test_許可した表示モードだけを現在ユーザーへ保存する(string $mode): void
    {
        $_POST = ['mode' => $mode, 'nonce' => 'nonce'];

        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cocoon_settings_navigation_mode', 'nonce');
        Functions\expect('wp_unslash')->once()->with($mode)->andReturn($mode);
        Functions\expect('sanitize_key')->once()->with($mode)->andReturn($mode);
        Functions\expect('get_current_user_id')->once()->andReturn(42);
        Functions\expect('update_user_option')
            ->once()
            ->with(42, COCOON_SETTINGS_NAVIGATION_MODE_OPTION, $mode, false)
            ->andReturn(true);
        Functions\expect('get_user_option')
            ->once()
            ->with(COCOON_SETTINGS_NAVIGATION_MODE_OPTION, 42)
            ->andReturn($mode);
        Functions\expect('wp_send_json_success')->once()->with(['mode' => $mode]);

        cocoon_ajax_save_settings_navigation_mode();
        $this->addToAssertionCount(1);
    }

    public static function savableNavigationModeProvider(): array
    {
        return [
            'レスポンシブ表示' => ['responsive'],
            '従来タブ表示' => ['tabs'],
        ];
    }

    public function test_再読込した値が異なる場合は保存失敗を返す(): void
    {
        $_POST = ['mode' => 'responsive', 'nonce' => 'nonce'];

        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('check_ajax_referer')
            ->once()
            ->with('cocoon_settings_navigation_mode', 'nonce');
        Functions\expect('wp_unslash')->once()->with('responsive')->andReturn('responsive');
        Functions\expect('sanitize_key')->once()->with('responsive')->andReturn('responsive');
        Functions\expect('get_current_user_id')->once()->andReturn(42);
        Functions\expect('update_user_option')
            ->once()
            ->with(42, COCOON_SETTINGS_NAVIGATION_MODE_OPTION, 'responsive', false)
            ->andReturn(false);
        Functions\expect('get_user_option')
            ->once()
            ->with(COCOON_SETTINGS_NAVIGATION_MODE_OPTION, 42)
            ->andReturn('tabs');
        Functions\expect('wp_send_json_error')
            ->once()
            ->with(['message' => 'save_failed'], 500);
        Functions\expect('wp_send_json_success')->never();

        cocoon_ajax_save_settings_navigation_mode();
        $this->addToAssertionCount(1);
    }
}
