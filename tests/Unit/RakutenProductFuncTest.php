<?php
/**
 * 楽天API関連関数のユニットテスト
 *
 * shortcodes-product-func.php 内の楽天API関連関数と、
 * 既存ProductFuncTestではカバーされていないAmazonバックアップ関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class RakutenProductFuncTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // 定数定義
        if (!defined('AMAZON_DOMAIN')) {
            define('AMAZON_DOMAIN', 'www.amazon.co.jp');
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

        // shortcodes-product-func.php を読み込み
        require_once dirname(__DIR__, 2) . '/lib/shortcodes-product-func.php';
    }

    // ========================================================================
    // get_rakuten_api_transient_id() — 楽天キャッシュID生成
    // ========================================================================

    public function test_get_rakuten_api_transient_id_プレフィックス付きIDを返す(): void
    {
        $result = get_rakuten_api_transient_id('12345');
        $this->assertStringStartsWith(TRANSIENT_RAKUTEN_API_PREFIX, $result);
        $this->assertStringContainsString('12345', $result);
    }

    public function test_get_rakuten_api_transient_id_異なるIDで異なる結果(): void
    {
        $result1 = get_rakuten_api_transient_id('item_001');
        $result2 = get_rakuten_api_transient_id('item_002');
        $this->assertNotSame($result1, $result2);
    }

    public function test_get_rakuten_api_transient_id_長いIDはMD5ハッシュ化される(): void
    {
        // 50文字を超える長いIDの場合、MD5でハッシュ化される
        $longId = str_repeat('a', 60);
        $result = get_rakuten_api_transient_id($longId);
        $this->assertStringStartsWith(TRANSIENT_RAKUTEN_API_PREFIX, $result);
        // 元の長いIDは含まれない（MD5化されている）
        $this->assertStringNotContainsString($longId, $result);
    }

    // ========================================================================
    // get_rakuten_api_transient_bk_id() — 楽天バックアップキャッシュID
    // ========================================================================

    public function test_get_rakuten_api_transient_bk_id_バックアッププレフィックス付き(): void
    {
        $result = get_rakuten_api_transient_bk_id('12345');
        $this->assertStringStartsWith(TRANSIENT_BACKUP_RAKUTEN_API_PREFIX, $result);
        $this->assertStringContainsString('12345', $result);
    }

    public function test_get_rakuten_api_transient_bk_id_通常IDと異なるプレフィックス(): void
    {
        $normalId = get_rakuten_api_transient_id('test_item');
        $backupId = get_rakuten_api_transient_bk_id('test_item');
        // 同じキーでもプレフィックスが異なるため、結果も異なる
        $this->assertNotSame($normalId, $backupId);
    }

    // ========================================================================
    // get_amazon_api_transient_bk_id() — Amazonバックアップキャッシュ
    // ========================================================================

    public function test_get_amazon_api_transient_bk_id_プレフィックス付きIDを返す(): void
    {
        $result = get_amazon_api_transient_bk_id('B08N5WRWNW');
        $this->assertStringStartsWith(TRANSIENT_BACKUP_AMAZON_API_PREFIX, $result);
        $this->assertStringContainsString('B08N5WRWNW', $result);
    }

    public function test_get_amazon_api_transient_bk_id_通常IDと異なる(): void
    {
        $normalId = get_amazon_api_transient_id('B08N5WRWNW');
        $backupId = get_amazon_api_transient_bk_id('B08N5WRWNW');
        $this->assertNotSame($normalId, $backupId);
    }

    // ========================================================================
    // get_long_str_to_md5_hash() — 長い文字列のMD5ハッシュ化
    // ========================================================================

    public function test_get_long_str_to_md5_hash_短い文字列はそのまま(): void
    {
        $this->assertSame('short', get_long_str_to_md5_hash('short'));
    }

    public function test_get_long_str_to_md5_hash_50文字ちょうどはそのまま(): void
    {
        $str50 = str_repeat('a', 50);
        $this->assertSame($str50, get_long_str_to_md5_hash($str50));
    }

    public function test_get_long_str_to_md5_hash_51文字以上はMD5化(): void
    {
        $str51 = str_repeat('a', 51);
        $result = get_long_str_to_md5_hash($str51);
        // MD5ハッシュは32文字の16進数
        $this->assertSame(32, strlen($result));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $result);
    }

    public function test_get_long_str_to_md5_hash_カスタム長さ指定(): void
    {
        $str = 'abcdefghij'; // 10文字
        // length=5 なら、10文字は長いのでMD5化される
        $result = get_long_str_to_md5_hash($str, 5);
        $this->assertSame(32, strlen($result));
    }

    public function test_get_long_str_to_md5_hash_空文字列はそのまま(): void
    {
        $this->assertSame('', get_long_str_to_md5_hash(''));
    }

    // ========================================================================
    // URL生成関数
    // ========================================================================

    public function test_get_amazon_search_url_キーワードで検索URLを生成(): void
    {
        $result = get_amazon_search_url('テスト商品');
        $this->assertStringContainsString(AMAZON_DOMAIN, $result);
        $this->assertStringContainsString('keywords=', $result);
    }

    public function test_get_amazon_search_url_トラッキングID付き(): void
    {
        $result = get_amazon_search_url('テスト商品', 'mytrackingid');
        $this->assertStringContainsString('tag=mytrackingid', $result);
    }

    public function test_get_amazon_review_url_レビューURLを生成(): void
    {
        $result = get_amazon_review_url('B08N5WRWNW');
        $this->assertStringContainsString('product-reviews/B08N5WRWNW', $result);
    }

    public function test_get_rakuten_search_url_楽天検索URLを生成(): void
    {
        $result = get_rakuten_search_url('テスト', null);
        $this->assertStringContainsString('search.rakuten.co.jp', $result);
    }

    public function test_get_yahoo_search_url_Yahoo検索URLを生成(): void
    {
        $result = get_yahoo_search_url('テスト');
        $this->assertStringContainsString('search.shopping.yahoo.co.jp', $result);
    }

    public function test_get_mercari_search_url_メルカリ検索URLを生成(): void
    {
        $result = get_mercari_search_url('iPhone', 'test_afid');
        $this->assertStringContainsString('jp.mercari.com', $result);
        $this->assertStringContainsString('afid=test_afid', $result);
    }
}
