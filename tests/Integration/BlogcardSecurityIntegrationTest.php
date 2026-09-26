<?php
/**
 * 外部ブログカードの表示と取得経路に関する統合テスト
 */

namespace Cocoon\Tests\Integration;

class BlogcardSecurityIntegrationTest extends IntegrationTestCase
{
    /**
     * URL指定のタイトルと説明文がHTMLとして実行されない確認
     */
    public function test_ブログカードの指定文言をHTMLとして解釈しない(): void
    {
        $title = '<svg onload=alert(1)>';
        $snippet = '<img src=x onerror=alert(1)>';
        $url = 'https://example.com/?title=' . rawurlencode($title) . '&snippet=' . rawurlencode($snippet);
        $cacheKey = TRANSIENT_BLOGCARD_PREFIX . md5($url);
        set_transient($cacheKey, 'error', HOUR_IN_SECONDS);

        try {
            $html = url_to_external_ogp_blogcard_tag($url);
            $this->assertStringContainsString('&lt;svg onload=alert(1)&gt;', $html);
            $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
            $this->assertStringNotContainsString('<svg', $html);
            $this->assertStringNotContainsString('<img src=x', $html);
        } finally {
            delete_transient($cacheKey);
        }
    }

    /**
     * OGP取得がループバックアドレスを拒否する確認
     */
    public function test_OGP取得が内部アドレスを拒否する(): void
    {
        $this->assertFalse(\OpenGraphGetter::fetch('http://127.0.0.1/private'));
    }

    /**
     * PHP拡張子のURLにある画像も画像専用の拡張子で保存される確認
     */
    public function test_PHP拡張子の画像URLを安全な拡張子で保存する(): void
    {
        $url = 'https://example.com/security-fixture.php?case=' . uniqid('', true);
        $fixture = tempnam(sys_get_temp_dir(), 'cocoon-image-');
        $target = trailingslashit(get_theme_blog_card_cache_path()) . md5($url) . '.png';
        file_put_contents($fixture, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL/nwAAAABJRU5ErkJggg=='));

        // HTTP通信の代わりに画像を一時ファイルへ書き込むテスト用応答
        $mockResponse = static function ($preempt, $args, $requestUrl) use ($fixture, $url) {
            if ($requestUrl !== $url) {
                return $preempt;
            }
            copy($fixture, $args['filename']);
            return ['response' => ['code' => 200, 'message' => 'OK'], 'headers' => [], 'body' => '', 'cookies' => []];
        };
        add_filter('pre_http_request', $mockResponse, 10, 3);

        try {
            $savedUrl = fetch_card_image($url);
            $this->assertIsString($savedUrl);
            $this->assertStringEndsWith('.png', $savedUrl);
            $this->assertFileExists($target);
        } finally {
            remove_filter('pre_http_request', $mockResponse, 10);
            if (is_file($target)) {
                unlink($target);
            }
            unlink($fixture);
        }
    }
}
