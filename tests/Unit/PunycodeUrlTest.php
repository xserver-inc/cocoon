<?php
/**
 * Punycode URL処理のユニットテスト
 *
 * puny_http_build_url() によるURL結合・フラグ処理、
 * convert_punycode() によるPunycode変換をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class PunycodeUrlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/punycode.php';
    }

    // ========================================================================
    // puny_http_build_url() - URL結合
    // ========================================================================

    public function test_puny_http_build_url_基本的なURLをパースして再構築する(): void
    {
        $url = 'https://example.com/path?query=1#fragment';
        $result = puny_http_build_url($url);
        $this->assertSame($url, $result);
    }

    public function test_puny_http_build_url_スキームとホストのみ(): void
    {
        $result = puny_http_build_url('https://example.com');
        $this->assertSame('https://example.com', $result);
    }

    public function test_puny_http_build_url_パーツでホストを置換する(): void
    {
        $result = puny_http_build_url('https://old.example.com/path', ['host' => 'new.example.com']);
        $this->assertStringContainsString('new.example.com', $result);
        $this->assertStringContainsString('/path', $result);
    }

    public function test_puny_http_build_url_パーツでスキームを置換する(): void
    {
        $result = puny_http_build_url('http://example.com/path', ['scheme' => 'https']);
        $this->assertStringStartsWith('https://', $result);
    }

    public function test_puny_http_build_url_REPLACEフラグでパスを置換する(): void
    {
        $result = puny_http_build_url(
            'https://example.com/old-path',
            ['path' => '/new-path'],
            COCOON_HTTP_URL_REPLACE
        );
        $this->assertStringContainsString('/new-path', $result);
        $this->assertStringNotContainsString('/old-path', $result);
    }

    public function test_puny_http_build_url_REPLACEフラグでクエリを置換する(): void
    {
        $result = puny_http_build_url(
            'https://example.com/path?old=1',
            ['query' => 'new=2'],
            COCOON_HTTP_URL_REPLACE
        );
        $this->assertStringContainsString('?new=2', $result);
        $this->assertStringNotContainsString('old=1', $result);
    }

    public function test_puny_http_build_url_JOIN_QUERYフラグでクエリを結合する(): void
    {
        $result = puny_http_build_url(
            'https://example.com/path?a=1',
            ['query' => 'b=2'],
            COCOON_HTTP_URL_JOIN_QUERY
        );
        $this->assertStringContainsString('a=1', $result);
        $this->assertStringContainsString('b=2', $result);
    }

    public function test_puny_http_build_url_STRIP_QUERYフラグでクエリを削除する(): void
    {
        $result = puny_http_build_url(
            'https://example.com/path?query=1',
            [],
            COCOON_HTTP_URL_STRIP_QUERY
        );
        $this->assertStringNotContainsString('?query=1', $result);
        $this->assertStringContainsString('/path', $result);
    }

    public function test_puny_http_build_url_STRIP_FRAGMENTフラグでフラグメントを削除する(): void
    {
        $result = puny_http_build_url(
            'https://example.com/path#section',
            [],
            COCOON_HTTP_URL_STRIP_FRAGMENT
        );
        $this->assertStringNotContainsString('#section', $result);
    }

    public function test_puny_http_build_url_STRIP_PORTフラグでポートを削除する(): void
    {
        $result = puny_http_build_url(
            'https://example.com:8080/path',
            [],
            COCOON_HTTP_URL_STRIP_PORT
        );
        $this->assertStringNotContainsString(':8080', $result);
    }

    public function test_puny_http_build_url_空URLはnullを返す(): void
    {
        $this->assertNull(puny_http_build_url(''));
        $this->assertNull(puny_http_build_url(null));
    }

    public function test_puny_http_build_url_ユーザー情報付きURL(): void
    {
        $result = puny_http_build_url('https://user:pass@example.com/path');
        $this->assertStringContainsString('user:pass@', $result);
    }

    public function test_puny_http_build_url_STRIP_AUTHフラグでユーザー情報を削除する(): void
    {
        $result = puny_http_build_url(
            'https://user:pass@example.com/path',
            [],
            COCOON_HTTP_URL_STRIP_AUTH
        );
        $this->assertStringNotContainsString('user', $result);
        $this->assertStringNotContainsString('pass', $result);
    }

    // ========================================================================
    // convert_punycode() - Punycode変換
    // ========================================================================

    public function test_convert_punycode_ASCIIドメインはそのまま(): void
    {
        $result = convert_punycode('https://example.com/path');
        $this->assertStringContainsString('example.com', $result);
        $this->assertStringContainsString('/path', $result);
    }

    public function test_convert_punycode_日本語ドメインをエンコードする(): void
    {
        $result = convert_punycode('https://日本語.jp/path', true);
        $this->assertStringContainsString('xn--', $result);
        $this->assertStringContainsString('/path', $result);
    }

    public function test_convert_punycode_空URLはnullを返す(): void
    {
        $this->assertNull(convert_punycode(''));
        $this->assertNull(convert_punycode(null));
    }

    // ========================================================================
    // punycode_encode() / punycode_decode()
    // ========================================================================

    public function test_punycode_encode_URLのホスト部分をエンコードする(): void
    {
        $result = punycode_encode('https://example.com/path');
        $this->assertStringContainsString('example.com', $result);
    }

    public function test_punycode_decode_URLのホスト部分をデコードする(): void
    {
        $result = punycode_decode('https://example.com/path');
        $this->assertStringContainsString('example.com', $result);
    }
}
