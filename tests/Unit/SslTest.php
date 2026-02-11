<?php
/**
 * SSL変換関数のユニットテスト
 *
 * chagne_site_url_html_to_https() による各種アフィリエイトサービスの
 * HTTP→HTTPS URL変換ロジックをテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class SslTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/ssl.php';
    }

    // ========================================================================
    // 内部リンクのHTTP→HTTPS変換
    // ========================================================================

    public function test_内部リンクのHTTPをHTTPSに変換する(): void
    {
        $content = '<a href="http://example.com/page">リンク</a>';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://example.com/page', $result);
        $this->assertStringNotContainsString('http://example.com', $result);
    }

    public function test_既にHTTPSの内部リンクは変更しない(): void
    {
        $content = '<a href="https://example.com/page">リンク</a>';
        $result = chagne_site_url_html_to_https($content);
        $this->assertSame($content, $result);
    }

    // ========================================================================
    // AmazonアソシエイトのSSL対応
    // ========================================================================

    public function test_Amazon画像URLをHTTPSに変換する(): void
    {
        $content = '<img src="http://ecx.images-amazon.com/images/test.jpg">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://images-fe.ssl-images-amazon.com', $result);
    }

    public function test_Amazon広告システムURLをHTTPSに変換する(): void
    {
        $content = '<img src="http://ir-jp.amazon-adsystem.com/e/ir?t=tag">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://ir-jp.amazon-adsystem.com', $result);
    }

    // ========================================================================
    // バリューコマースのSSL対応
    // ========================================================================

    public function test_バリューコマースCKをHTTPSに変換する(): void
    {
        $content = '<a href="http://ck.jp.ap.valuecommerce.com/servlet/referral">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://ck.jp.ap.valuecommerce.com', $result);
    }

    public function test_バリューコマースADをHTTPSに変換する(): void
    {
        $content = '<img src="http://ad.jp.ap.valuecommerce.com/servlet/gifbanner">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://ad.jp.ap.valuecommerce.com', $result);
    }

    // ========================================================================
    // もしもアフィリエイトのSSL対応
    // ========================================================================

    public function test_もしもアフィリエイトCをHTTPSに変換する(): void
    {
        $content = '<a href="http://c.af.moshimo.com/af/c/click">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://af.moshimo.com', $result);
    }

    public function test_もしもアフィリエイトIをHTTPSに変換する(): void
    {
        $content = '<img src="http://i.af.moshimo.com/af/i/impression">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://i.moshimo.com', $result);
    }

    public function test_もしもアフィリエイト画像をHTTPSに変換する(): void
    {
        $content = '<img src="http://image.moshimo.com/af/image">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://image.moshimo.com', $result);
    }

    // ========================================================================
    // A8.netのSSL対応
    // ========================================================================

    public function test_A8netをHTTPSに変換する(): void
    {
        $content = '<a href="http://px.a8.net/svt/ejp">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://px.a8.net', $result);
    }

    public function test_A8netのGIF画像URLを正規表現でHTTPSに変換する(): void
    {
        $content = '<img src="http://www14.a8.net/0.gif">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://www14.a8.net/0.gif', $result);
    }

    public function test_A8netの異なるサーバー番号も変換する(): void
    {
        $content = '<img src="http://www16.a8.net/0.gif">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://www16.a8.net/0.gif', $result);
    }

    // ========================================================================
    // アクセストレードのSSL対応
    // ========================================================================

    public function test_アクセストレードをHTTPSに変換する(): void
    {
        $content = '<a href="http://h.accesstrade.net/sp/cc">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://h.accesstrade.net', $result);
    }

    // ========================================================================
    // はてなのSSL対応
    // ========================================================================

    public function test_はてなブログカードをHTTPSに変換する(): void
    {
        $content = '<iframe src="http://hatenablog.com/embed?url=test">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://hatenablog-parts.com/embed?url=', $result);
    }

    public function test_はてブ数画像をHTTPSに変換する(): void
    {
        $content = '<img src="http://b.hatena.ne.jp/entry/image/test">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://b.hatena.ne.jp/entry/image/', $result);
    }

    // ========================================================================
    // 楽天のSSL対応
    // ========================================================================

    public function test_楽天商品画像をHTTPSに変換する(): void
    {
        $content = '<img src="http://hbb.afl.rakuten.co.jp/hgb/test.gif">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://hbb.afl.rakuten.co.jp', $result);
    }

    // ========================================================================
    // その他のSSL対応
    // ========================================================================

    public function test_リンクシェアをHTTPSに変換する(): void
    {
        $content = '<a href="http://ad.linksynergy.com/fs-bin/show">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://ad.linksynergy.com', $result);
    }

    public function test_Google検索ボックスをHTTPSに変換する(): void
    {
        $content = '<script src="http://www.google.co.jp/cse/cse.js"></script>';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringContainsString('https://www.google.co.jp/cse', $result);
    }

    // ========================================================================
    // 複合テスト
    // ========================================================================

    public function test_複数のHTTPリンクを同時にHTTPSに変換する(): void
    {
        $content = '<a href="http://example.com/page">テスト</a>' .
                   '<img src="http://ecx.images-amazon.com/test.jpg">' .
                   '<a href="http://px.a8.net/svt/ejp">';
        $result = chagne_site_url_html_to_https($content);
        $this->assertStringNotContainsString('http://example.com', $result);
        $this->assertStringNotContainsString('http://ecx.images-amazon.com', $result);
        $this->assertStringNotContainsString('http://px.a8.net', $result);
    }

    public function test_変換不要のコンテンツはそのまま返す(): void
    {
        $content = '<p>HTTPSとは関係ないテキスト</p>';
        $result = chagne_site_url_html_to_https($content);
        $this->assertSame($content, $result);
    }
}
