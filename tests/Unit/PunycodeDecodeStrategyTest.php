<?php
/**
 * Punycodeデコード方針のリグレッションテスト
 *
 * 今回の修正で判明した「punycode_decode() はフルURL用ラッパーであり、
 * ホスト名単体に渡すとデコードされない」という仕様を検証します。
 * また、Punycode::decode() がホスト名単体で正しく動作することを検証し、
 * 将来の実装者が誤った方の関数を使わないようにガードします。
 *
 * @see lib/punycode.php   punycode_decode() / punycode_encode() ラッパー
 * @see lib/punycode-obj.php  Punycode クラス本体
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Punycode;

class PunycodeDecodeStrategyTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Punycodeライブラリを読み込む
        require_once dirname(__DIR__, 2) . '/lib/punycode.php';
    }

    // ========================================================================
    // Punycode::decode() — ホスト名単体でのデコード（正しい使い方）
    // ========================================================================

    /**
     * @dataProvider ホスト名デコードプロバイダ
     */
    public function test_Punycodeクラスはホスト名単体を正しくデコードする(string $input, string $expected): void
    {
        $punycode = new Punycode();
        $result = $punycode->decode($input);
        $this->assertSame($expected, $result);
    }

    public static function ホスト名デコードプロバイダ(): array
    {
        return [
            'Punycodeドメイン' => ['xn--eckwd4c7cu47r2wf.jp', 'ドメイン名例.jp'],
            'ASCIIドメイン（変化なし）' => ['example.com', 'example.com'],
            'サブドメイン付きPunycode' => ['sub.xn--eckwd4c7cu47r2wf.jp', 'sub.ドメイン名例.jp'],
            '空文字列' => ['', ''],
            'localhost' => ['localhost', 'localhost'],
            'IPアドレス' => ['192.168.1.1', '192.168.1.1'],
            'www付きPunycode' => ['www.xn--eckwd4c7cu47r2wf.jp', 'www.ドメイン名例.jp'],
        ];
    }

    // ========================================================================
    // punycode_decode() ラッパー — フルURLでの使用（正しい使い方）
    // ========================================================================

    public function test_punycode_decodeラッパーはフルURLのホスト名をデコードする(): void
    {
        $result = punycode_decode('https://xn--eckwd4c7cu47r2wf.jp/path?q=1');
        $this->assertStringContainsString('ドメイン名例.jp', $result);
        // パスとクエリは維持される
        $this->assertStringContainsString('/path', $result);
        $this->assertStringContainsString('q=1', $result);
    }

    public function test_punycode_decodeラッパーはASCIIフルURLをそのまま返す(): void
    {
        $result = punycode_decode('https://example.com/path');
        $this->assertSame('https://example.com/path', $result);
    }

    // ========================================================================
    // punycode_decode() ラッパーにホスト名単体を渡した場合（✘ 間違った使い方）
    // このテストは「なぜホスト名にはPunycode::decode()を使うべきか」を実証する
    // ========================================================================

    public function test_punycode_decodeラッパーにホスト名単体を渡すとデコードされない(): void
    {
        // punycode_decode() は内部で wp_parse_url() を使うため、
        // スキームなしのホスト名は host ではなく path として解釈される。
        // → Punycodeデコードが実行されない（これが今回のバグの原因）
        $input = 'xn--eckwd4c7cu47r2wf.jp';
        $result = punycode_decode($input);

        // ラッパー経由ではデコードされない（設計上の制約）
        $this->assertSame($input, $result,
            'punycode_decode() にホスト名単体を渡してもデコードされません。' .
            'ホスト名には Punycode::decode() を直接使用してください。');
    }

    // ========================================================================
    // エンコード/デコードの往復テスト（roundtrip）
    // ========================================================================

    public function test_日本語ドメインのエンコードとデコードが往復一致する(): void
    {
        // フルURL版: punycode_encode → punycode_decode で元に戻る
        $original = 'https://日本語.jp/path';
        $encoded = punycode_encode($original);
        $decoded = punycode_decode($encoded);
        $this->assertSame($original, $decoded);
    }

    public function test_ホスト名単体のエンコードとデコードが往復一致する(): void
    {
        // Punycodeクラス直接版: encode → decode で元に戻る
        $punycode = new Punycode();
        $original = 'ドメイン名例.jp';
        $encoded = $punycode->encode($original);
        $decoded = $punycode->decode($encoded);
        $this->assertSame($original, $decoded);
    }
}
