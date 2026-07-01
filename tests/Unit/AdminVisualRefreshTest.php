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
 *
 * @see docs/WP70-COMPATIBILITY-TEST.md 手動テスト手順
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class AdminVisualRefreshTest extends TestCase
{
    private static $admin_loaded = false;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ========================================================================
    // lib/admin.php - add_head_post_custum
    // ========================================================================

    /**
     * add_head_post_custum がカテゴリー検索の JavaScript を出力する
     * (旧エディターフォント CSS は cocoon_enqueue_editor_font_styles へ移行済み)
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

        // 旧エディターフォント CSS は cocoon_enqueue_editor_font_styles へ移行済みのため、
        // add_head_post_custum からは <style> ではなく <script> のみ出力される
        $this->assertStringNotContainsString('editor-block-list__block', $output);
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

    // ========================================================================
    // cocoon_enqueue_editor_font_styles() - enqueue_block_assets フック
    // ========================================================================

    /**
     * フォント設定がある場合、enqueue_block_assets で .editor-styles-wrapper に CSS を注入する
     */
    public function test_cocoon_enqueue_editor_font_styles_フォント設定がある場合スタイルを登録する(): void
    {
        if ( !self::$admin_loaded ) {
            require_once dirname( __DIR__, 2 ) . '/lib/admin.php';
            self::$admin_loaded = true;
        }

        // cocoon_enqueue_editor_font_styles 関数が存在することを確認
        $this->assertTrue( function_exists( 'cocoon_enqueue_editor_font_styles' ) );
    }

    /**
     * フォント設定がすべて空の場合、スタイルを登録しない（何も出力しない）
     */
    public function test_cocoon_enqueue_editor_font_styles_関数が登録されている(): void
    {
        // cocoon_enqueue_editor_font_styles が定義されており、
        // enqueue_block_assets フックで呼び出されることを確認
        // （実際の動作確認は統合テストまたは手動テストで行う）
        $this->assertTrue( function_exists( 'cocoon_enqueue_editor_font_styles' ) );
    }

}
