<?php
/**
 * Punycode クラスのユニットテスト
 *
 * RFC 3492 に基づく Punycode エンコード/デコードをテストします。
 * 純粋な PHP クラスのため、WordPress 依存なしでテスト可能です。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Punycode;

class PunycodeTest extends TestCase
{
    private Punycode $punycode;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once dirname(__DIR__, 2) . '/lib/punycode-obj.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->punycode = new Punycode();
    }

    // ========================================================================
    // エンコード テスト
    // ========================================================================

    public function test_encode_ASCII文字列はそのまま返す(): void
    {
        $this->assertSame('example.com', $this->punycode->encode('example.com'));
    }

    public function test_encode_日本語ドメインをエンコードする(): void
    {
        $encoded = $this->punycode->encode('日本語.jp');
        $this->assertStringStartsWith('xn--', $encoded);
        $this->assertStringEndsWith('.jp', $encoded);
    }

    public function test_encode_中国語ドメインをエンコードする(): void
    {
        $encoded = $this->punycode->encode('中文.com');
        $this->assertStringStartsWith('xn--', $encoded);
    }

    public function test_encode_ドイツ語ウムラウトをエンコードする(): void
    {
        // münchen.de → xn--mnchen-3ya.de
        $encoded = $this->punycode->encode('münchen.de');
        $this->assertSame('xn--mnchen-3ya.de', $encoded);
    }

    // ========================================================================
    // デコード テスト
    // ========================================================================

    public function test_decode_ASCII文字列はそのまま返す(): void
    {
        $this->assertSame('example.com', $this->punycode->decode('example.com'));
    }

    public function test_decode_Punycodeドメインをデコードする(): void
    {
        // xn--mnchen-3ya.de → münchen.de
        $decoded = $this->punycode->decode('xn--mnchen-3ya.de');
        $this->assertSame('münchen.de', $decoded);
    }

    // ========================================================================
    // エンコード ↔ デコード 往復テスト
    // ========================================================================

    public function test_roundtrip_日本語ドメインをエンコードしてデコードすると元に戻る(): void
    {
        $original = '日本語.jp';
        $encoded = $this->punycode->encode($original);
        $decoded = $this->punycode->decode($encoded);
        $this->assertSame($original, $decoded);
    }

    public function test_roundtrip_ドイツ語ドメイン(): void
    {
        $original = 'münchen.de';
        $encoded = $this->punycode->encode($original);
        $decoded = $this->punycode->decode($encoded);
        $this->assertSame($original, $decoded);
    }

    public function test_roundtrip_複数のUnicodeラベル(): void
    {
        $original = 'テスト.例え.jp';
        $encoded = $this->punycode->encode($original);
        $decoded = $this->punycode->decode($encoded);
        $this->assertSame($original, $decoded);
    }
}
