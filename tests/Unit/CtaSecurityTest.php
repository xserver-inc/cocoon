<?php
/**
 * CTAの出力エスケープに関する回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class CtaSecurityTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/shortcodes.php';
        require_once dirname(__DIR__, 2) . '/blocks/src/block/cta/index.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // WordPressのテンプレート引数と実際のCTAテンプレート読み込みの再現
        Functions\when('get_template_part')
            ->alias(function (string $slug, ?string $name = null, array $args = []): void {
                extract($args, EXTR_SKIP);
                require dirname(__DIR__, 2) . '/' . $slug . '.php';
            });
        Functions\when('wp_kses_post')
            ->alias(static function ($html): string {
                return self::sanitize_post_html_for_test($html);
            });
    }

    /**
     * ショートコードの各出力文脈で危険な入力が無害化されることを検証
     */
    public function test_ショートコードの各出力文脈をエスケープする(): void
    {
        $output = get_cta_tag(
            [
                'heading' => '見出し" onmouseover="alert(1)',
                'image_url' => 'https://example.com/image.png',
                'layout' => 'cta-left-and-right',
                'filter' => 0,
                'button_text' => '<img src=x onerror=alert(1)>詳しく見る',
                'button_url' => 'https://example.com/detail',
                'button_color' => 'btn-blue',
            ],
            '<strong>許可HTML</strong><a href="javascript:alert(1)">危険なリンク</a><img src="x" onerror="alert(1)"><script>alert(1)</script>'
        );

        $this->assertStringContainsString('class="cta-box cta-left-and-right"', $output);
        $this->assertStringContainsString('見出し&quot; onmouseover=&quot;alert(1)', $output);
        $this->assertStringContainsString('<strong>許可HTML</strong>', $output);
        $this->assertStringContainsString('class="btn btn-blue btn-l"', $output);
        $this->assertStringContainsString('>詳しく見る</a>', $output);
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertStringNotContainsString('onerror=', $output);
        $this->assertDoesNotMatchRegularExpression('/<[^>]+\sonmouseover\s*=/i', $output);
    }

    /**
     * javascript URLと不正なCSSクラスが出力されないことを検証
     */
    public function test_javascript_URLと不正なCSSクラスを拒否する(): void
    {
        $output = get_cta_tag(
            [
                'image_url' => 'javascript:alert(1)',
                'layout' => 'cta-left-and-right" onmouseover="alert(1)',
                'button_url' => 'javascript:alert(1)',
                'button_color' => 'btn-red" onclick="alert(1)',
            ],
            '安全な本文'
        );

        $this->assertStringContainsString('class="cta-box cta-top-and-bottom"', $output);
        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertStringNotContainsString('onmouseover=', $output);
        $this->assertStringNotContainsString('onclick=', $output);
        $this->assertStringNotContainsString('<img', $output);
        $this->assertStringNotContainsString('<a ', $output);
    }

    /**
     * 通常入力と空文字、0、falseで既存のHTML構造が維持されることを検証
     */
    public function test_通常入力と空入力と0とfalseの互換性を維持する(): void
    {
        $normal = get_cta_tag(
            [
                'heading' => '日本語＆記号',
                'layout' => ' cta-right-and-left ',
                'filter' => false,
                'button_text' => '0',
                'button_url' => './',
                'button_color' => ' btn-red ',
            ],
            '<em>本文</em>'
        );
        $empty = get_cta_tag(
            [
                'heading' => '',
                'image_url' => '',
                'layout' => '',
                'filter' => 0,
                'button_text' => false,
                'button_url' => '',
                'button_color' => '',
            ],
            false
        );

        $this->assertStringContainsString('class="cta-box cta-right-and-left"', $normal);
        $this->assertStringContainsString('<em>本文</em>', $normal);
        $this->assertStringContainsString('href="./"', $normal);
        $this->assertStringContainsString('>0</a>', $normal);
        $this->assertStringContainsString('class="cta-box "', $empty);
        $this->assertStringNotContainsString('cta-heading', $empty);
        $this->assertStringNotContainsString('cta-button', $empty);
    }

    /**
     * 未定義のブロック属性でもWarningなしで既定値が使われることを検証
     */
    public function test_未定義のブロック属性でもWarningなしで既定値を使う(): void
    {
        $output = render_cta([], null);

        $this->assertStringContainsString('class="cta-box cta-left-and-right"', $output);
        $this->assertStringNotContainsString('cta-heading', $output);
        $this->assertStringNotContainsString('cta-button', $output);
    }

    /**
     * 同じ入力を渡したショートコードとブロックのフロントHTMLが一致することを検証
     */
    public function test_ショートコードとブロックのフロント出力が一致する(): void
    {
        $shortcode = get_cta_tag(
            [
                'heading' => '共通見出し',
                'image_url' => 'https://example.com/image.png',
                'layout' => ' cta-left-and-right ',
                'filter' => 0,
                'button_text' => '詳しく見る',
                'button_url' => 'https://example.com/detail',
                'button_color' => 'btn-green',
            ],
            '<strong>共通本文</strong>'
        );
        $block = render_cta(
            [
                'header' => '共通見出し',
                'image' => 'https://example.com/image.png',
                'layout' => 'cta-left-and-right',
                'message' => '<strong>共通本文</strong>',
                'autoParagraph' => false,
                'buttonText' => '詳しく見る',
                'buttonURL' => 'https://example.com/detail',
                'buttonColor' => ' btn-green ',
            ],
            ''
        );

        $this->assertSame($shortcode, $block);
    }

    /**
     * ブロックのフロント表示とRESTプレビューで同じ安全性が保たれることを検証
     */
    public function test_ブロックのフロント表示とRESTプレビューで同じ安全性を保つ(): void
    {
        $attributes = [
            'header' => '<script>alert(1)</script>見出し',
            'image' => 'javascript:alert(1)',
            'message' => '<strong>本文</strong><img src="x" onerror="alert(1)">',
            'autoParagraph' => false,
            'buttonText' => '読む" onclick="alert(1)',
            'buttonURL' => 'javascript:alert(1)',
            'layout' => '不正なクラス名',
            'buttonColor' => 'btn-blue onclick=alert(1)',
        ];

        $front = render_cta($attributes, '');
        define('REST_REQUEST', true);
        $rest = render_cta($attributes, '');

        foreach ([$front, $rest] as $output) {
            $this->assertStringNotContainsString('<script', $output);
            $this->assertStringNotContainsString('javascript:', $output);
            $this->assertStringNotContainsString('onerror=', $output);
            $this->assertDoesNotMatchRegularExpression('/<[^>]+\sonclick\s*=/i', $output);
            $this->assertStringContainsString('<strong>本文</strong>', $output);
            $this->assertStringContainsString('cta-left-and-right', $output);
        }

        $this->assertStringNotContainsString('cocoon-editor-no-link-click', $front);
        $this->assertStringContainsString('cocoon-editor-no-link-click', $rest);
    }

    /**
     * WordPress本体のwp_kses_post相当として、テスト対象の危険なタグと属性を除去する
     */
    private static function sanitize_post_html_for_test($html): string
    {
        $html = is_scalar($html) ? (string) $html : '';
        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = strip_tags($html, '<p><br><strong><em><b><i><a><img><ul><ol><li>');
        $html = preg_replace('/\s+on[a-z0-9_-]+\s*=\s*"[^"]*"/i', '', $html) ?? '';
        $html = preg_replace("/\\s+on[a-z0-9_-]+\\s*=\\s*'[^']*'/i", '', $html) ?? '';
        $html = preg_replace('/\s+on[a-z0-9_-]+\s*=\s*[^\s>]+/i', '', $html) ?? '';
        $html = preg_replace('/\s+(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '', $html) ?? '';

        return $html;
    }
}
