<?php
/**
 * 広告関連関数のユニットテスト
 *
 * AdSenseコードからのID抽出（data-ad-client, data-ad-slot）と
 * H2見出し検出の正規表現ロジックをテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AdTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // ad.php 依存定数
        if (!defined('DATA_AD_CLIENT')) {
            define('DATA_AD_CLIENT', 'data-ad-client');
        }
        if (!defined('DATA_AD_SLOT')) {
            define('DATA_AD_SLOT', 'data-ad-slot');
        }
        if (!defined('DATA_AD_FORMAT_NONE')) {
            define('DATA_AD_FORMAT_NONE', 'none');
        }
        if (!defined('DATA_AD_FORMAT_AUTO')) {
            define('DATA_AD_FORMAT_AUTO', 'auto');
        }
        if (!defined('DATA_AD_FORMAT_FLUID')) {
            define('DATA_AD_FORMAT_FLUID', 'fluid');
        }
        if (!defined('BEFORE_1ST_H2_AD_PRIORITY_STANDARD')) {
            define('BEFORE_1ST_H2_AD_PRIORITY_STANDARD', 12);
        }
        if (!defined('H2_REG')) {
            // アコーディオンブロックをスキップし、H2を検出する正規表現
            define('H2_REG', '/<div[^>]*class="[^"]*\\bwp-block-accordion\\b[^"]*"[\s\S]*?<\\/div>(*SKIP)(*F)|<h2\\b/i');
        }

        require_once dirname(__DIR__, 2) . '/lib/ad.php';
    }

    // ========================================================================
    // get_adsense_ids() - AdSenseコードからID抽出
    // ========================================================================

    public function test_get_adsense_ids_標準的なAdSenseコードからIDを抽出する(): void
    {
        $code = '<ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-1234567890123456"
            data-ad-slot="9876543210"
            data-ad-format="auto"></ins>';

        $result = get_adsense_ids($code);
        $this->assertIsArray($result);
        $this->assertSame('ca-pub-1234567890123456', $result[DATA_AD_CLIENT]);
        $this->assertSame('9876543210', $result[DATA_AD_SLOT]);
    }

    public function test_get_adsense_ids_改行を含むAdSenseコードからもIDを抽出する(): void
    {
        $code = "<ins class=\"adsbygoogle\"\n" .
                "  data-ad-client=\"ca-pub-9999999999999999\"\n" .
                "  data-ad-slot=\"1111111111\"\n" .
                "  data-ad-format=\"auto\"></ins>";

        $result = get_adsense_ids($code);
        $this->assertIsArray($result);
        $this->assertSame('ca-pub-9999999999999999', $result[DATA_AD_CLIENT]);
        $this->assertSame('1111111111', $result[DATA_AD_SLOT]);
    }

    public function test_get_adsense_ids_AdSenseコードでないものはnullを返す(): void
    {
        $code = '<div class="ad">広告テキスト</div>';
        $result = get_adsense_ids($code);
        $this->assertNull($result);
    }

    public function test_get_adsense_ids_空文字列はnullを返す(): void
    {
        $result = get_adsense_ids('');
        $this->assertNull($result);
    }

    // ========================================================================
    // get_adsense_data_ad_client() - data-ad-client抽出
    // ========================================================================

    public function test_get_adsense_data_ad_client_クライアントIDを返す(): void
    {
        $code = '<ins data-ad-client="ca-pub-1234567890123456" data-ad-slot="9876543210"></ins>';
        $result = get_adsense_data_ad_client($code);
        $this->assertSame('ca-pub-1234567890123456', $result);
    }

    public function test_get_adsense_data_ad_client_不正なコードはnullを返す(): void
    {
        $result = get_adsense_data_ad_client('invalid code');
        $this->assertNull($result);
    }

    // ========================================================================
    // get_adsense_data_ad_slot() - data-ad-slot抽出
    // ========================================================================

    public function test_get_adsense_data_ad_slot_スロットIDを返す(): void
    {
        $code = '<ins data-ad-client="ca-pub-1234567890123456" data-ad-slot="9876543210"></ins>';
        $result = get_adsense_data_ad_slot($code);
        $this->assertSame('9876543210', $result);
    }

    // ========================================================================
    // get_h2_included_in_body() - H2見出し検出
    // ========================================================================

    public function test_get_h2_included_in_body_H2タグを検出する(): void
    {
        $content = '<p>テスト</p><h2>見出し</h2><p>テスト</p>';
        $result = get_h2_included_in_body($content);
        $this->assertSame('<h2', $result);
    }

    public function test_get_h2_included_in_body_H2がない場合はnullを返す(): void
    {
        $content = '<p>テスト</p><h3>H3見出し</h3><p>テスト</p>';
        $result = get_h2_included_in_body($content);
        $this->assertNull($result);
    }

    public function test_get_h2_included_in_body_クラス付きH2も検出する(): void
    {
        $content = '<h2 class="wp-heading">見出し</h2>';
        $result = get_h2_included_in_body($content);
        $this->assertSame('<h2', $result);
    }

    public function test_get_h2_included_in_body_大文字のH2も検出する(): void
    {
        $content = '<p>テスト</p><H2>見出し</H2>';
        $result = get_h2_included_in_body($content);
        $this->assertSame('<H2', $result);
    }

    public function test_get_h2_included_in_body_空コンテンツはnullを返す(): void
    {
        $this->assertNull(get_h2_included_in_body(''));
    }

    #[DataProvider('h2ContentProvider')]
    public function test_get_h2_included_in_body_各種パターン(string $content, bool $expectFound): void
    {
        $result = get_h2_included_in_body($content);
        if ($expectFound) {
            $this->assertNotNull($result);
        } else {
            $this->assertNull($result);
        }
    }

    public static function h2ContentProvider(): array
    {
        return [
            'H2のみ'       => ['<h2>テスト</h2>', true],
            'H2+属性'      => ['<h2 id="test" class="heading">テスト</h2>', true],
            'H3のみ'       => ['<h3>テスト</h3>', false],
            'テキストのみ' => ['<p>H2という文字列</p>', false],
            'h2で始まらないタグ' => ['<h2x>テスト</h2x>', false], // \b ワード境界でh2xにはマッチしない
        ];
    }
}
