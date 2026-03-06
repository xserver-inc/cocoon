<?php
/**
 * apis-funcs.php のユニットテスト
 *
 * API設定関数（Amazon・楽天の表示設定）をテストします。
 * wp-mock-functions.php の get_theme_mod スタブのグローバル変数
 * $test_theme_mods を使って戻り値を制御します。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ApisFuncsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // apis-funcs.php の読み込み（get_theme_option は utils.php で定義済み）
        require_once dirname(__DIR__, 2) . '/lib/page-settings/apis-funcs.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        // 各テスト開始時にモック値をリセット
        global $test_theme_mods;
        $test_theme_mods = [];
    }

    protected function tearDown(): void
    {
        // テスト終了時にグローバル変数をクリア
        global $test_theme_mods;
        $test_theme_mods = [];
        parent::tearDown();
    }

    // ========================================================================
    // 楽天説明文表示設定のテスト
    // ========================================================================

    /**
     * 楽天説明文表示がONの場合、1を返す
     */
    public function test_is_rakuten_item_description_visible_ONの場合1を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_RAKUTEN_ITEM_DESCRIPTION_VISIBLE] = 1;

        $this->assertSame(1, is_rakuten_item_description_visible());
    }

    /**
     * 楽天説明文表示がOFFの場合、0を返す
     */
    public function test_is_rakuten_item_description_visible_OFFの場合0を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_RAKUTEN_ITEM_DESCRIPTION_VISIBLE] = 0;

        $this->assertSame(0, is_rakuten_item_description_visible());
    }

    /**
     * 楽天説明文表示が未設定の場合、null（デフォルト）を返す
     */
    public function test_is_rakuten_item_description_visible_未設定の場合nullを返す(): void
    {
        // $test_theme_mods にキーが存在しない → get_theme_option の default(null) が返る
        $this->assertNull(is_rakuten_item_description_visible());
    }

    // ========================================================================
    // 楽天価格表示設定のテスト
    // ========================================================================

    /**
     * 楽天価格表示がONの場合、1を返す
     */
    public function test_is_rakuten_item_price_visible_ONの場合1を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_RAKUTEN_ITEM_PRICE_VISIBLE] = 1;

        $this->assertSame(1, is_rakuten_item_price_visible());
    }

    /**
     * 楽天価格表示がOFFの場合、0を返す
     */
    public function test_is_rakuten_item_price_visible_OFFの場合0を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_RAKUTEN_ITEM_PRICE_VISIBLE] = 0;

        $this->assertSame(0, is_rakuten_item_price_visible());
    }

    // ========================================================================
    // Amazon価格表示設定のテスト
    // ========================================================================

    /**
     * Amazon価格表示がONの場合、1を返す
     */
    public function test_is_amazon_item_price_visible_ONの場合1を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_AMAZON_ITEM_PRICE_VISIBLE] = 1;

        $this->assertSame(1, is_amazon_item_price_visible());
    }

    /**
     * Amazon価格表示がOFFの場合、0を返す
     */
    public function test_is_amazon_item_price_visible_OFFの場合0を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_AMAZON_ITEM_PRICE_VISIBLE] = 0;

        $this->assertSame(0, is_amazon_item_price_visible());
    }

    // ========================================================================
    // Amazon説明文表示設定のテスト
    // ========================================================================

    /**
     * Amazon説明文表示がONの場合、1を返す
     */
    public function test_is_amazon_item_description_visible_ONの場合1を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_AMAZON_ITEM_DESCRIPTION_VISIBLE] = 1;

        $this->assertSame(1, is_amazon_item_description_visible());
    }

    /**
     * Amazon説明文表示がOFFの場合、0を返す
     */
    public function test_is_amazon_item_description_visible_OFFの場合0を返す(): void
    {
        global $test_theme_mods;
        $test_theme_mods[OP_AMAZON_ITEM_DESCRIPTION_VISIBLE] = 0;

        $this->assertSame(0, is_amazon_item_description_visible());
    }

    // ========================================================================
    // 定数定義の確認テスト
    // ========================================================================

    /**
     * 楽天説明文表示設定の定数が正しく定義されている
     */
    public function test_OP_RAKUTEN_ITEM_DESCRIPTION_VISIBLE_定数が定義されている(): void
    {
        $this->assertTrue(defined('OP_RAKUTEN_ITEM_DESCRIPTION_VISIBLE'));
        $this->assertSame('rakuten_item_description_visible', OP_RAKUTEN_ITEM_DESCRIPTION_VISIBLE);
    }

    /**
     * 楽天価格表示設定の定数が正しく定義されている
     */
    public function test_OP_RAKUTEN_ITEM_PRICE_VISIBLE_定数が定義されている(): void
    {
        $this->assertTrue(defined('OP_RAKUTEN_ITEM_PRICE_VISIBLE'));
        $this->assertSame('rakuten_item_price_visible', OP_RAKUTEN_ITEM_PRICE_VISIBLE);
    }

    /**
     * Amazon価格表示設定の定数が正しく定義されている
     */
    public function test_OP_AMAZON_ITEM_PRICE_VISIBLE_定数が定義されている(): void
    {
        $this->assertTrue(defined('OP_AMAZON_ITEM_PRICE_VISIBLE'));
        $this->assertSame('amazon_item_price_visible', OP_AMAZON_ITEM_PRICE_VISIBLE);
    }

    /**
     * Amazon説明文表示設定の定数が正しく定義されている
     */
    public function test_OP_AMAZON_ITEM_DESCRIPTION_VISIBLE_定数が定義されている(): void
    {
        $this->assertTrue(defined('OP_AMAZON_ITEM_DESCRIPTION_VISIBLE'));
        $this->assertSame('amazon_item_description_visible', OP_AMAZON_ITEM_DESCRIPTION_VISIBLE);
    }
}
