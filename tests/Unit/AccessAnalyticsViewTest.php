<?php
/**
 * アクセス解析ダッシュボードのビュー選択とタブ表示のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class AccessAnalyticsViewTest extends TestCase
{
    private static bool $analytics_functions_loaded = false;

    private bool $had_theme_options;
    private $previous_theme_options;
    private bool $had_theme_mods;
    private $previous_theme_mods;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$analytics_functions_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/query-func.php';
            require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/render-func.php';
            self::$analytics_functions_loaded = true;
        }

        // 他のテストが設定したグローバル値を終了時に復元できるよう保存する
        $this->had_theme_options = array_key_exists('_THEME_OPTIONS', $GLOBALS);
        $this->previous_theme_options = $GLOBALS['_THEME_OPTIONS'] ?? null;
        $this->had_theme_mods = array_key_exists('test_theme_mods', $GLOBALS);
        $this->previous_theme_mods = $GLOBALS['test_theme_mods'] ?? null;

        $GLOBALS['_THEME_OPTIONS'] = array();
        $GLOBALS['test_theme_mods'] = array();

        // WordPress本体と同様にビュー名を小文字の安全なキーへ変換する
        Functions\when('sanitize_key')->alias(static function ($value): string {
            return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) $value));
        });
    }

    protected function tearDown(): void
    {
        // テスト開始前に存在したグローバル状態を漏れなく復元する
        if ($this->had_theme_options) {
            $GLOBALS['_THEME_OPTIONS'] = $this->previous_theme_options;
        } else {
            unset($GLOBALS['_THEME_OPTIONS']);
        }

        if ($this->had_theme_mods) {
            $GLOBALS['test_theme_mods'] = $this->previous_theme_mods;
        } else {
            unset($GLOBALS['test_theme_mods']);
        }

        parent::tearDown();
    }

    /**
     * ダッシュボード機能が有効でビュー指定がない場合はダッシュボードを返すことをテスト
     */
    public function test_resolve_view_有効時の既定ビューはダッシュボード(): void
    {
        $this->setAnalyticsEnabled(true);

        $this->assertSame('dashboard', cocoon_analytics_resolve_view(null));
    }

    /**
     * ダッシュボード機能が有効な場合は許可された要求ビューを維持することをテスト
     */
    public function test_resolve_view_有効時は許可されたビューを維持する(): void
    {
        $this->setAnalyticsEnabled(true);

        $this->assertSame('ranking', cocoon_analytics_resolve_view('RANKING'));
    }

    /**
     * ダッシュボード機能が有効で不正なビューの場合はダッシュボードへ戻すことをテスト
     */
    public function test_resolve_view_有効時の不正ビューはダッシュボードへ戻す(): void
    {
        $this->setAnalyticsEnabled(true);

        $this->assertSame('dashboard', cocoon_analytics_resolve_view('invalid'));
    }

    /**
     * ダッシュボード機能が無効でビュー指定がない場合は設定画面を返すことをテスト
     */
    public function test_resolve_view_無効時の既定ビューは設定画面(): void
    {
        $this->setAnalyticsEnabled(false);

        $this->assertSame('settings', cocoon_analytics_resolve_view(null));
    }

    /**
     * ダッシュボード機能が無効な場合は要求ビューにかかわらず設定画面を返すことをテスト
     */
    public function test_resolve_view_無効時は集計系ビューを設定画面へ正規化する(): void
    {
        $this->setAnalyticsEnabled(false);

        $this->assertSame('settings', cocoon_analytics_resolve_view('dashboard'));
        $this->assertSame('settings', cocoon_analytics_resolve_view('ranking'));
    }

    /**
     * ダッシュボード機能が無効な場合はタブHTMLを出力しないことをテスト
     */
    public function test_render_tabs_無効時はタブを出力しない(): void
    {
        $this->setAnalyticsEnabled(false);

        ob_start();
        cocoon_analytics_render_tabs('settings');
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    /**
     * ダッシュボード機能が有効な場合は従来の全タブを出力することをテスト
     */
    public function test_render_tabs_有効時は全タブを出力する(): void
    {
        $this->setAnalyticsEnabled(true);

        ob_start();
        cocoon_analytics_render_tabs('settings');
        $output = ob_get_clean();

        $this->assertSame(8, substr_count($output, '<a '));
        $this->assertStringContainsString('view=settings', $output);
        $this->assertStringContainsString('nav-tab-active', $output);
    }

    /**
     * テスト対象のダッシュボード機能設定を切り替える
     */
    private function setAnalyticsEnabled(bool $enabled): void
    {
        $GLOBALS['test_theme_mods'][OP_ACCESS_ANALYTICS_ENABLE] = $enabled ? 1 : 0;
    }
}
