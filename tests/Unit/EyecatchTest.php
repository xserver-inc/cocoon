<?php
/**
 * アイキャッチ関連関数のユニットテスト
 *
 * sanitize_post_title() のタイトル整形（絵文字除去・空白正規化・HTMLエンティティ変換）と
 * get_dynamic_featured_image_size() のサイズ計算をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EyecatchTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/eyecatch.php';
    }

    // ========================================================================
    // sanitize_post_title() - タイトル整形
    // ========================================================================

    public function test_sanitize_post_title_通常テキストはそのまま返す(): void
    {
        $this->assertSame('テストタイトル', sanitize_post_title('テストタイトル'));
    }

    public function test_sanitize_post_title_連続空白を1つに置換する(): void
    {
        $this->assertSame('Hello World', sanitize_post_title('Hello  World'));
        $this->assertSame('A B C', sanitize_post_title('A   B   C'));
    }

    public function test_sanitize_post_title_HTMLエンティティをデコードする(): void
    {
        $this->assertSame('A & B', sanitize_post_title('A &amp; B'));
        $this->assertSame('"引用"', sanitize_post_title('&quot;引用&quot;'));
        $this->assertSame("It's", sanitize_post_title('It&#039;s'));
    }

    public function test_sanitize_post_title_顔文字を除去する(): void
    {
        $result = sanitize_post_title('テスト😀タイトル');
        $this->assertSame('テストタイトル', $result);
    }

    public function test_sanitize_post_title_その他のシンボル絵文字を除去する(): void
    {
        // 🌟 (U+1F31F) はシンボル範囲
        $result = sanitize_post_title('🌟重要なお知らせ🌟');
        $this->assertSame('重要なお知らせ', $result);
    }

    public function test_sanitize_post_title_交通記号絵文字を除去する(): void
    {
        // 🚀 (U+1F680) は交通記号範囲
        $result = sanitize_post_title('🚀ロケット発射');
        $this->assertSame('ロケット発射', $result);
    }

    public function test_sanitize_post_title_天気記号を除去する(): void
    {
        // ☀ (U+2600) はその他のシンボル範囲
        $result = sanitize_post_title('☀晴れの日');
        $this->assertSame('晴れの日', $result);
    }

    public function test_sanitize_post_title_複合処理_絵文字と連続空白の同時処理(): void
    {
        // '😀  テスト  🌟  タイトル  😀'
        // → 空白正規化: '😀 テスト 🌟 タイトル 😀'
        // → 絵文字除去: ' テスト  タイトル '（🌟除去で空白が2つ連続に）
        // 注意: 空白正規化→絵文字除去の順なので、絵文字除去後の連続空白は残る
        $result = sanitize_post_title('😀  テスト  🌟  タイトル  😀');
        $this->assertStringContainsString('テスト', $result);
        $this->assertStringContainsString('タイトル', $result);
        // 絵文字が除去されていることを確認
        $this->assertStringNotContainsString('😀', $result);
        $this->assertStringNotContainsString('🌟', $result);
    }

    public function test_sanitize_post_title_空文字列(): void
    {
        $this->assertSame('', sanitize_post_title(''));
    }

    public function test_sanitize_post_title_ASCII文字のみ(): void
    {
        $this->assertSame('Hello World', sanitize_post_title('Hello World'));
    }

    // ========================================================================
    // get_dynamic_featured_image_size() - 動的サイズ計算
    // ========================================================================

    public function test_get_dynamic_featured_image_size_基準1280で比例計算する(): void
    {
        // 1280のキャンバスに30pxの要素 → 30px
        $result = get_dynamic_featured_image_size(1280, 30);
        $this->assertEquals(30, $result);
    }

    public function test_get_dynamic_featured_image_size_小さいキャンバス(): void
    {
        // 640のキャンバスに30pxの要素 → 15px (30/1280*640)
        $result = get_dynamic_featured_image_size(640, 30);
        $this->assertEquals(15, $result);
    }

    public function test_get_dynamic_featured_image_size_大きいキャンバス(): void
    {
        // 2560のキャンバスに30pxの要素 → 60px (30/1280*2560)
        $result = get_dynamic_featured_image_size(2560, 30);
        $this->assertEquals(60, $result);
    }

    public function test_get_dynamic_featured_image_size_端数は四捨五入される(): void
    {
        // 100/1280 * 1000 = 78.125 → 78
        $result = get_dynamic_featured_image_size(1000, 100);
        $this->assertEquals(78, $result);
    }

    public function test_get_dynamic_featured_image_size_ゼロキャンバス(): void
    {
        $result = get_dynamic_featured_image_size(0, 30);
        $this->assertEquals(0, $result);
    }

    public function test_get_dynamic_featured_image_size_ゼロパーツ(): void
    {
        $result = get_dynamic_featured_image_size(1280, 0);
        $this->assertEquals(0, $result);
    }

    #[DataProvider('dynamicSizeProvider')]
    public function test_get_dynamic_featured_image_size_各種サイズの比例計算(
        int $canvas, int $parts, float $expected
    ): void {
        $result = get_dynamic_featured_image_size($canvas, $parts);
        $this->assertEquals($expected, $result);
    }

    public static function dynamicSizeProvider(): array
    {
        return [
            'OGP幅1200でボーダー30' => [1200, 30, round((30 / 1280) * 1200)],
            'OGP幅1200でフォント48' => [1200, 48, round((48 / 1280) * 1200)],
            'OGP幅800でマージン60'  => [800, 60, round((60 / 1280) * 800)],
            'ハーフサイズ'           => [640, 640, round((640 / 1280) * 640)],
        ];
    }
}
