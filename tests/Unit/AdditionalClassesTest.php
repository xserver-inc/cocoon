<?php
/**
 * 追加CSSクラス生成関数のユニットテスト
 *
 * replace_value_to_class() の文字列変換（アンダースコア→ハイフン、小文字化）と
 * フォント系クラス名生成関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AdditionalClassesTest extends TestCase
{
    private bool $hadInCategoryCallback = false;
    private mixed $originalInCategoryCallback = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/additional-classes.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 所属判定コールバックの退避とテストごとの初期化
        $this->hadInCategoryCallback = array_key_exists('test_mock_in_category_callback', $GLOBALS);
        $this->originalInCategoryCallback = $GLOBALS['test_mock_in_category_callback'] ?? null;
        unset($GLOBALS['test_mock_in_category_callback']);
        Functions\when('get_the_page_main_category')->justReturn(null);
    }

    protected function tearDown(): void
    {
        // テスト前の所属判定コールバックの復元
        if ($this->hadInCategoryCallback) {
            $GLOBALS['test_mock_in_category_callback'] = $this->originalInCategoryCallback;
        } else {
            unset($GLOBALS['test_mock_in_category_callback']);
        }

        parent::tearDown();
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

    public function test_複数カテゴリーの前後記事はラベルと同じ先頭カテゴリーだけを含む(): void
    {
        Functions\expect('get_the_category')
            ->once()
            ->with(456)
            ->andReturn([
                (object) ['cat_ID' => 9, 'cat_name' => 'カテゴリー9'],
                (object) ['cat_ID' => 14, 'cat_name' => 'カテゴリー14'],
            ]);

        $this->assertSame(
            ' post-navi-category-9',
            get_post_navi_category_classes(456)
        );
    }

    public function test_前後記事のカテゴリークラスはラベルと同じメインカテゴリーを含む(): void
    {
        Functions\expect('get_the_category')->once()->with(456)->andReturn([
            (object) ['cat_ID' => 9, 'cat_name' => 'カテゴリー9'],
            (object) ['cat_ID' => 14, 'cat_name' => 'カテゴリー14'],
        ]);
        Functions\when('get_the_page_main_category')->justReturn(14);
        $GLOBALS['test_mock_in_category_callback'] = function ($categoryId, $postId) {
            $this->assertSame([14, 456], [$categoryId, $postId]);
            return true;
        };
        Functions\expect('get_category')->once()->with(14)
            ->andReturn((object) ['cat_ID' => '14', 'cat_name' => 'カテゴリー14']);

        $this->assertSame(' post-navi-category-14', get_post_navi_category_classes(456));
    }

    #[DataProvider('invalidPostNaviCategoryProvider')]
    public function test_カテゴリーIDが無効なら識別クラスを追加しない(object $category): void
    {
        Functions\expect('get_the_category')->once()->with(789)->andReturn([$category]);

        $this->assertSame('', get_post_navi_category_classes(789));
    }

    public static function invalidPostNaviCategoryProvider(): array
    {
        return [
            'カテゴリーIDなし' => [(object) ['cat_name' => 'IDなし']],
            'カテゴリーIDが0' => [(object) ['cat_ID' => 0]],
            'カテゴリーIDが負数' => [(object) ['cat_ID' => -9]],
            'カテゴリーIDが数値以外' => [(object) ['cat_ID' => 'invalid']],
        ];
    }

    public function test_カテゴリーのない前後記事はクラスも余分な空白も追加しない(): void
    {
        Functions\expect('get_the_category')->once()->with(789)->andReturn([]);

        $this->assertSame('', get_post_navi_category_classes(789));
    }
}
