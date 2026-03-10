<?php
/**
 * 管理画面ビジュアルリフレッシュ (WP 7.0) 互換性のユニットテスト
 *
 * WordPress 7.0 では管理画面に新しいタイポグラフィ、カラースキーム、View Transitions が
 * 導入される。テーマが注入しているカスタム管理 CSS がレイアウトを崩さず、期待どおりの
 * セレクタで出力されることを検証する。
 *
 * 対象:
 * - lib/admin.php (add_head_post_custum)
 * - lib/dashboard-message.php (dashboard_message_css)
 *
 * @see docs/WP70-COMPATIBILITY-TEST.md 手動テスト手順
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class AdminVisualRefreshTest extends TestCase
{
    private static $dashboard_message_loaded = false;
    private static $admin_loaded = false;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ========================================================================
    // lib/dashboard-message.php - dashboard_message_css
    // ========================================================================

    /**
     * dashboard_message_css が #dashboard-message 用のスタイルを出力する
     * WP 7.0 管理画面でもこの ID はテーマが出力する要素に付与されるため、セレクタの衝突はない。
     */
    public function test_dashboard_message_css_出力にdashboard_messageのスタイルが含まれる(): void
    {
        if (!self::$dashboard_message_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/dashboard-message.php';
            self::$dashboard_message_loaded = true;
        }

        ob_start();
        dashboard_message_css();
        $output = ob_get_clean();

        $this->assertStringContainsString('#dashboard-message', $output);
        $this->assertStringContainsString('<style', $output);
        $this->assertStringContainsString('</style>', $output);
    }

    /**
     * ブロックエディタページではダッシュボードメッセージを非表示にするルールが含まれる
     * (.block-editor-page は WP の body クラス。WP 7.0 でも維持される想定)
     */
    public function test_dashboard_message_css_ブロックエディタ用の非表示ルールが含まれる(): void
    {
        if (!self::$dashboard_message_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/dashboard-message.php';
            self::$dashboard_message_loaded = true;
        }

        ob_start();
        dashboard_message_css();
        $output = ob_get_clean();

        $this->assertStringContainsString('.block-editor-page #dashboard-message', $output);
        $this->assertStringContainsString('display: none', $output);
    }

    /**
     * カスタム CSS が body や html を広く上書きしておらず、スコープが限定的であることを確認
     * WP 7.0 の新カラースキーム等と衝突しにくい。
     */
    public function test_dashboard_message_css_広域セレクタでbodyやhtmlを上書きしていない(): void
    {
        if (!self::$dashboard_message_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/dashboard-message.php';
            self::$dashboard_message_loaded = true;
        }

        ob_start();
        dashboard_message_css();
        $output = ob_get_clean();

        $this->assertStringNotContainsString('body {', $output);
        $this->assertStringNotContainsString('html {', $output);
    }

    // ========================================================================
    // lib/admin.php - add_head_post_custum
    // ========================================================================

    /**
     * add_head_post_custum がエディタ用のスタイル・スクリプトを出力する
     * (editor-block-list__block は Gutenberg のクラス。WP 7.0 iframe 化後もクラスは維持される想定)
     * 1回だけ呼び出して全ての主張を行う（関数内で define しているため複数回呼ぶと警告になる）
     */
    public function test_add_head_post_custum_スタイルとスクリプトを出力する(): void
    {
        $this->stubAddHeadPostCustumDependencies();
        if (!self::$admin_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/admin.php';
            self::$admin_loaded = true;
        }

        ob_start();
        add_head_post_custum();
        $output = ob_get_clean();

        $this->assertStringContainsString('<style>', $output);
        $this->assertStringContainsString('editor-block-list__block', $output);
        $this->assertMatchesRegularExpression('/<style>.*?<\/style>/s', $output);
        $this->assertStringContainsString('<script', $output);
        $this->assertStringContainsString('jQuery', $output);
    }

    /**
     * add_head_post_custum 実行時に get_theme_mod で日付を渡すと put_theme_css_cache_file を呼ばないようにする
     * （キャッシュ書き出しはテストでは行わない。スタブで日付を「直近」にしておく）
     */
    private function stubAddHeadPostCustumDependencies(): void
    {
        global $test_theme_mods;
        if (!isset($test_theme_mods) || !is_array($test_theme_mods)) {
            $test_theme_mods = [];
        }
        $test_theme_mods['last_output_editor_css_day'] = date('Y-m-d H:i:s');
    }
}
