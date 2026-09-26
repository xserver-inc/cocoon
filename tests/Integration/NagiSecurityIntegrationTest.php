<?php
/**
 * Nagi スキンの保存済み CTA 値に関する統合テスト
 */

namespace Cocoon\Tests\Integration;

class NagiSecurityIntegrationTest extends IntegrationTestCase
{
    /**
     * 不正な保存済みクラス値が安全な既定値に置き換わる確認
     */
    public function test_不正なCTAクラス値を表示しない(): void
    {
        $postId = $this->createPost(['post_title' => 'Nagi CTA テスト', 'post_status' => 'publish']);
        update_post_meta($postId, 'fix_link', 'CTA');
        update_post_meta($postId, 'cta_color', '" onmouseover="alert(1)');
        update_post_meta($postId, 'cta_layout', ['cta_s']);

        $originalPost = $GLOBALS['post'] ?? null;
        $GLOBALS['post'] = get_post($postId);
        $post = $GLOBALS['post'];
        $originalBufferLevel = ob_get_level();
        ob_start();

        try {
            include dirname(__DIR__, 2) . '/skins/nagi/fix-cta.php';
            $html = ob_get_clean();
        } finally {
            // 途中で例外が起きた場合の出力バッファと投稿情報の復元
            while (ob_get_level() > $originalBufferLevel) {
                ob_end_clean();
            }
            $GLOBALS['post'] = $originalPost;
        }

        $this->assertStringContainsString('class="fixed_contents cta_red cta_v"', $html);
        $this->assertStringNotContainsString('onmouseover=', $html);
    }
}
