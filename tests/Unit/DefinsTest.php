<?php
/**
 * テーマ定数の検証テスト
 *
 * キャッシュプレフィックス定数が正しいフォーマットで定義されているかを検証します。
 * AmazonApiTestとProductFuncTest間のスタブ競合再発を防止する
 * リグレッションテストとしても機能します。
 *
 * 注: _defins.php はWordPress依存が強いため直接ロードせず、
 * bootstrap.phpとテストクラス内で定数の期待値を検証します。
 * テスト実行順に依存しないよう、定数の「値の一致」ではなく
 * 「定義状態の正しさ」を検証する設計です。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class DefinsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // キャッシュプレフィックス定数を定義（_defins.phpと同じ形式）
        // 注: 他のテストが先に異なる値で定義する場合があるためガード付き
        if (!defined('TRANSIENT_SHARE_PREFIX')) {
            define('TRANSIENT_SHARE_PREFIX', THEME_NAME . '_share_count_');
        }
        if (!defined('TRANSIENT_FOLLOW_PREFIX')) {
            define('TRANSIENT_FOLLOW_PREFIX', THEME_NAME . '_follow_count_');
        }
        if (!defined('TRANSIENT_BLOGCARD_PREFIX')) {
            define('TRANSIENT_BLOGCARD_PREFIX', THEME_NAME . '_bcc_');
        }
        if (!defined('TRANSIENT_AMAZON_API_PREFIX')) {
            define('TRANSIENT_AMAZON_API_PREFIX', THEME_NAME . '_amazon_paapi_v5_asin_');
        }
        if (!defined('TRANSIENT_BACKUP_AMAZON_API_PREFIX')) {
            define('TRANSIENT_BACKUP_AMAZON_API_PREFIX', THEME_NAME . '_backup_amazon_paapi_v5_asin_');
        }
        if (!defined('TRANSIENT_RAKUTEN_API_PREFIX')) {
            define('TRANSIENT_RAKUTEN_API_PREFIX', THEME_NAME . '_rakuten_api_id_');
        }
        if (!defined('TRANSIENT_BACKUP_RAKUTEN_API_PREFIX')) {
            define('TRANSIENT_BACKUP_RAKUTEN_API_PREFIX', THEME_NAME . '_backup_rakuten_api_id_');
        }
        if (!defined('IMAGE_RECOGNITION_EXTENSIONS_REG')) {
            define('IMAGE_RECOGNITION_EXTENSIONS_REG', '\.jpe?g|\.png|\.gif|\.webp|\.avif');
        }
    }

    // ========================================================================
    // THEME_NAME 関連定数
    // ========================================================================

    public function test_THEME_NAME_が定義されている(): void
    {
        $this->assertTrue(defined('THEME_NAME'));
    }

    public function test_THEME_NAME_は文字列である(): void
    {
        $this->assertIsString(THEME_NAME);
        $this->assertNotEmpty(THEME_NAME);
    }

    // ========================================================================
    // キャッシュプレフィックス定数 — AmazonAPI・楽天API関連
    // これらは他テストと競合しない（DefinsTestまたはProductFuncTestで定義）
    // ========================================================================

    public function test_TRANSIENT_AMAZON_API_PREFIX_はTHEME_NAMEで始まる(): void
    {
        // リグレッション: AmazonApiTestのスタブと競合して
        // 'transient_' になっていた問題を検出するテスト
        $this->assertStringStartsWith(
            THEME_NAME . '_',
            TRANSIENT_AMAZON_API_PREFIX,
            'TRANSIENT_AMAZON_API_PREFIX は THEME_NAME_ で始まるべきです'
        );
    }

    public function test_TRANSIENT_AMAZON_API_PREFIX_のフォーマットが正しい(): void
    {
        $expected = THEME_NAME . '_amazon_paapi_v5_asin_';
        $this->assertSame($expected, TRANSIENT_AMAZON_API_PREFIX);
    }

    public function test_TRANSIENT_BACKUP_AMAZON_API_PREFIX_のフォーマットが正しい(): void
    {
        $expected = THEME_NAME . '_backup_amazon_paapi_v5_asin_';
        $this->assertSame($expected, TRANSIENT_BACKUP_AMAZON_API_PREFIX);
    }

    public function test_TRANSIENT_RAKUTEN_API_PREFIX_のフォーマットが正しい(): void
    {
        $expected = THEME_NAME . '_rakuten_api_id_';
        $this->assertSame($expected, TRANSIENT_RAKUTEN_API_PREFIX);
    }

    public function test_TRANSIENT_BACKUP_RAKUTEN_API_PREFIX_のフォーマットが正しい(): void
    {
        $expected = THEME_NAME . '_backup_rakuten_api_id_';
        $this->assertSame($expected, TRANSIENT_BACKUP_RAKUTEN_API_PREFIX);
    }

    // ========================================================================
    // キャッシュプレフィックス定数 — 定義されていることの検証
    // これらは他テスト（BlogcardTest等）で先に定義される場合があるため、
    // 値の完全一致ではなく定義状態と型を検証する
    // ========================================================================

    public function test_TRANSIENT_SHARE_PREFIX_が定義されている(): void
    {
        $this->assertTrue(defined('TRANSIENT_SHARE_PREFIX'));
        $this->assertIsString(TRANSIENT_SHARE_PREFIX);
        $this->assertNotEmpty(TRANSIENT_SHARE_PREFIX);
    }

    public function test_TRANSIENT_FOLLOW_PREFIX_が定義されている(): void
    {
        $this->assertTrue(defined('TRANSIENT_FOLLOW_PREFIX'));
        $this->assertIsString(TRANSIENT_FOLLOW_PREFIX);
        $this->assertNotEmpty(TRANSIENT_FOLLOW_PREFIX);
    }

    public function test_TRANSIENT_BLOGCARD_PREFIX_が定義されている(): void
    {
        $this->assertTrue(defined('TRANSIENT_BLOGCARD_PREFIX'));
        $this->assertIsString(TRANSIENT_BLOGCARD_PREFIX);
        $this->assertNotEmpty(TRANSIENT_BLOGCARD_PREFIX);
    }

    // ========================================================================
    // 全プレフィックス定数がユニークであることの検証
    // ========================================================================

    public function test_キャッシュプレフィックス定数がすべてユニーク(): void
    {
        $prefixes = [
            TRANSIENT_AMAZON_API_PREFIX,
            TRANSIENT_BACKUP_AMAZON_API_PREFIX,
            TRANSIENT_RAKUTEN_API_PREFIX,
            TRANSIENT_BACKUP_RAKUTEN_API_PREFIX,
            TRANSIENT_SHARE_PREFIX,
            TRANSIENT_FOLLOW_PREFIX,
            TRANSIENT_BLOGCARD_PREFIX,
        ];

        // 重複がないことを確認
        $unique = array_unique($prefixes);
        $this->assertCount(
            count($prefixes),
            $unique,
            'キャッシュプレフィックス定数に重複があります'
        );
    }

    // ========================================================================
    // 画像拡張子正規表現
    // ========================================================================

    /**
     * @dataProvider 画像拡張子パターンプロバイダ
     */
    public function test_IMAGE_RECOGNITION_EXTENSIONS_REG_が正しい拡張子にマッチ(string $extension, bool $shouldMatch): void
    {
        // 正規表現パターンとしてマッチテスト
        $pattern = '/' . IMAGE_RECOGNITION_EXTENSIONS_REG . '/i';
        if ($shouldMatch) {
            $this->assertMatchesRegularExpression($pattern, $extension);
        } else {
            $this->assertDoesNotMatchRegularExpression($pattern, $extension);
        }
    }

    public static function 画像拡張子パターンプロバイダ(): array
    {
        return [
            'jpg' => ['.jpg', true],
            'jpeg' => ['.jpeg', true],
            'png' => ['.png', true],
            'gif' => ['.gif', true],
            'webp' => ['.webp', true],
            'avif' => ['.avif', true],
            'txt' => ['.txt', false],
            'php' => ['.php', false],
        ];
    }
}
