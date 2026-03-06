<?php
/**
 * OpenGraph解析のユニットテスト
 *
 * OpenGraphGetter::_parse() のHTMLメタタグ解析ロジック、
 * OGPデータの抽出・フォールバック処理をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class OpenGraphTest extends TestCase
{
    private static \ReflectionMethod $parseMethod;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // open-graph.php 依存関数
        if (!function_exists('wp_remote_retrieve_response_code')) {
            function wp_remote_retrieve_response_code($response) { return 200; }
        }
        if (!function_exists('wp_filesystem_get_contents')) {
            function wp_filesystem_get_contents($url, $use_include_path = false) { return false; }
        }
        if (!function_exists('get_http_content')) {
            function get_http_content($url) { return false; }
        }
        if (!function_exists('is_amazon_site_page')) {
            function is_amazon_site_page($url) { return false; }
        }
        if (!function_exists('is_rakuten_site_page')) {
            function is_rakuten_site_page($url) { return false; }
        }

        require_once dirname(__DIR__, 2) . '/lib/open-graph.php';

        // _parse はプライベートメソッドなのでリフレクションでアクセスする
        self::$parseMethod = new \ReflectionMethod(\OpenGraphGetter::class, '_parse');
    }

    /**
     * _parse メソッドを呼び出すヘルパー
     */
    private function callParse(string $html, ?string $uri = null)
    {
        return self::$parseMethod->invoke(null, $html, $uri);
    }

    /**
     * テスト用HTMLを生成するヘルパー
     */
    private function makeHtml(string $head = '', string $body = ''): string
    {
        return '<!DOCTYPE html><html><head><meta charset="UTF-8">' . $head . '</head><body>' . $body . '</body></html>';
    }

    // ========================================================================
    // OGPタグの抽出
    // ========================================================================

    public function test_OGPタイトルを抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" content="テストタイトル">' .
            '<title>フォールバックタイトル</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('テストタイトル', $og->title);
    }

    public function test_OGP説明文を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:description" content="テスト説明文">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('テスト説明文', $og->description);
    }

    public function test_OGP画像を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://example.com/image.jpg">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('https://example.com/image.jpg', $og->image);
    }

    public function test_OGPサイト名を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:site_name" content="テストサイト">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('テストサイト', $og->site_name);
    }

    public function test_OGPタイプを抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:type" content="article">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('article', $og->type);
    }

    // ========================================================================
    // フォールバック動作
    // ========================================================================

    public function test_OGPタイトルがない場合titleタグからフォールバック(): void
    {
        $html = $this->makeHtml('<title>Test Title Value</title>');
        $og = $this->callParse($html);
        $this->assertSame('Test Title Value', $og->title);
    }

    public function test_OGP説明文がない場合metaDescriptionからフォールバック(): void
    {
        $html = $this->makeHtml(
            '<meta name="description" content="メタディスクリプション">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('メタディスクリプション', $og->description);
    }

    public function test_OGPタイトルが空文字の場合titleタグからフォールバック(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" content="">' .
            '<title>Fallback Title</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('Fallback Title', $og->title);
    }

    // ========================================================================
    // エッジケース
    // ========================================================================

    public function test_空HTMLはfalseを返す(): void
    {
        $result = $this->callParse('');
        $this->assertFalse($result);
    }

    public function test_メタタグもタイトルもないHTMLはfalseを返す(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body>コンテンツのみ</body></html>';
        $result = $this->callParse($html);
        $this->assertFalse($result);
    }

    public function test_複数のOGPプロパティを同時に抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" content="タイトル">' .
            '<meta property="og:description" content="説明">' .
            '<meta property="og:image" content="https://example.com/img.jpg">' .
            '<meta property="og:url" content="https://example.com/">' .
            '<title>タイトル</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('タイトル', $og->title);
        $this->assertSame('説明', $og->description);
        $this->assertSame('https://example.com/img.jpg', $og->image);
        $this->assertSame('https://example.com/', $og->url);
    }

    public function test_シングルクォートのmetaDescriptionも取得できる(): void
    {
        $html = $this->makeHtml(
            "<meta name='description' content='シングルクォート説明'>" .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('シングルクォート説明', $og->description);
    }

    // ========================================================================
    // value属性パターン（NYTなど malformed OGP）
    // ========================================================================

    public function test_value属性のOGPも抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" value="value属性タイトル">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('value属性タイトル', $og->title);
    }

    // ========================================================================
    // $TYPES 定数
    // ========================================================================

    public function test_OGPタイプ定義が正しい構造である(): void
    {
        $types = \OpenGraphGetter::$TYPES;
        $this->assertIsArray($types);
        $this->assertArrayHasKey('website', $types);
        $this->assertArrayHasKey('product', $types);
        $this->assertContains('blog', $types['website']);
        $this->assertContains('book', $types['product']);
    }
}
