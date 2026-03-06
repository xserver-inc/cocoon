<?php
/**
 * 追加CSSクラス生成関数のユニットテスト
 *
 * replace_value_to_class() の文字列変換（アンダースコア→ハイフン、小文字化）と
 * フォント系クラス名生成関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AdditionalClassesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/additional-classes.php';
    }

    // ========================================================================
    // replace_value_to_class() - 文字列→CSSクラス変換
    // ========================================================================

    #[DataProvider('replaceValueToClassProvider')]
    public function test_replace_value_to_class_アンダースコアをハイフンに変換し小文字化する(
        string $input, string $expected
    ): void {
        $this->assertSame($expected, replace_value_to_class($input));
    }

    public static function replaceValueToClassProvider(): array
    {
        return [
            'アンダースコアをハイフンに' => ['Noto_Sans_JP', 'noto-sans-jp'],
            '大文字を小文字に'           => ['MEIRYO', 'meiryo'],
            'ハイフンはそのまま'         => ['noto-sans', 'noto-sans'],
            '数字を含む'                 => ['font_123', 'font-123'],
            '空文字列'                   => ['', ''],
            '全て小文字'                 => ['arial', 'arial'],
            '複数アンダースコア'         => ['a_b_c_d', 'a-b-c-d'],
        ];
    }

    // ========================================================================
    // get_site_font_family_class() - フォントファミリークラス
    // ========================================================================

    public function test_get_site_font_family_class_プレフィックス付きクラスを返す(): void
    {
        $result = get_site_font_family_class();
        $this->assertStringStartsWith('ff-', $result);
        // get_site_font_family() → 'Noto_Sans_JP' → 'noto-sans-jp'
        $this->assertSame('ff-noto-sans-jp', $result);
    }

    // ========================================================================
    // get_site_font_size_class() - フォントサイズクラス
    // ========================================================================

    public function test_get_site_font_size_class_プレフィックス付きクラスを返す(): void
    {
        $result = get_site_font_size_class();
        $this->assertStringStartsWith('fz-', $result);
        // get_site_font_size() → '16px'
        $this->assertSame('fz-16px', $result);
    }

    // ========================================================================
    // get_site_font_weight_class() - フォントウエイトクラス
    // ========================================================================

    public function test_get_site_font_weight_class_プレフィックス付きクラスを返す(): void
    {
        $result = get_site_font_weight_class();
        $this->assertStringStartsWith('fw-', $result);
        // get_site_font_weight() → '400'
        $this->assertSame('fw-400', $result);
    }
}
