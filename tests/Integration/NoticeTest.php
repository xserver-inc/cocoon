<?php
/**
 * 通知エリア（notice-area-wrap）の出力検証テスト
 */

namespace Cocoon\Tests\Integration;

class NoticeTest extends IntegrationTestCase
{
    /**
     * テーマセットアップを実行
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // オプションを初期化
        remove_theme_mod('notice_area_visible');
        remove_theme_mod('notice_area_message');
        remove_theme_mod('notice_area_url');
        remove_theme_mod('notice_type');
    }

    /**
     * tmp/notice.php の出力を取得するヘルパー関数
     */
    private function render_notice()
    {
        ob_start();
        require get_template_directory() . '/tmp/notice.php';
        return ob_get_clean();
    }

    /**
     * 通知URLが設定されている場合、notice-area-wrapが出力され、URLリンクが含まれること
     */
    public function test_notice_area_wrap_exists_when_url_is_set(): void
    {
        // オプションの設定
        set_theme_mod('notice_area_visible', 1); // 有効
        set_theme_mod('notice_area_message', 'テストメッセージ');
        set_theme_mod('notice_area_url', 'https://example.com/notice');

        // notice_area_visible フィルターにより常に有効にする (is_amp()対策等)
        add_filter('notice_area_visible', '__return_true');

        $output = $this->render_notice();

        $this->assertStringContainsString('<div id="notice-area-wrap" class="notice-area-wrap">', $output);
        $this->assertStringContainsString('<a href="https://example.com/notice"', $output);
        $this->assertStringContainsString('テストメッセージ', $output);
    }

    /**
     * 通知URLが設定されていない場合でも、notice-area-wrapが出力されること
     */
    public function test_notice_area_wrap_exists_when_url_is_not_set(): void
    {
        // オプションの設定
        set_theme_mod('notice_area_visible', 1); // 有効
        set_theme_mod('notice_area_message', 'テストメッセージ2');
        set_theme_mod('notice_area_url', ''); // 空文字にする

        // notice_area_visible フィルターにより常に有効にする
        add_filter('notice_area_visible', '__return_true');

        $output = $this->render_notice();

        // urlが無くても wrap は出力される
        $this->assertStringContainsString('<div id="notice-area-wrap" class="notice-area-wrap">', $output);
        // aタグは出力されない
        $this->assertStringNotContainsString('<a href=', $output);
        $this->assertStringContainsString('テストメッセージ2', $output);
    }
}
