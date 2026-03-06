<?php
/**
 * shortcodes.php のユニットテスト
 *
 * ショートコード関連の純粋関数、正規表現ロジック、
 * パラメータ解析のテストを行います。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ShortcodesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // shortcodes.php が依存する定数を定義
        if (!defined('AFFI_SHORTCODE')) {
            define('AFFI_SHORTCODE', 'affi');
        }
        if (!defined('TIME_ERROR_MESSAGE')) {
            define('TIME_ERROR_MESSAGE', '日時入力エラー');
        }
        if (!defined('TOC_SHORTCODE_REG')) {
            define('TOC_SHORTCODE_REG', '/\[toc[^\]]*\]/i');
        }
        if (!defined('TOC_SHORTCODE_ERROR_MESSAGE')) {
            define('TOC_SHORTCODE_ERROR_MESSAGE', '目次ショートコードエラー');
        }

        require_once dirname(__DIR__, 2) . '/lib/shortcodes.php';
    }

    // ========================================================================
    // get_affiliate_tag_shortcode()
    // ========================================================================

    public function test_get_affiliate_tag_shortcode_正しいショートコード文字列を生成する(): void
    {
        $result = get_affiliate_tag_shortcode(123);
        $this->assertSame('[affi id=123]', $result);
    }

    public function test_get_affiliate_tag_shortcode_ID_0でも文字列を生成する(): void
    {
        $result = get_affiliate_tag_shortcode(0);
        $this->assertSame('[affi id=0]', $result);
    }

    // ========================================================================
    // get_function_text_shortcode()
    // ========================================================================

    public function test_get_function_text_shortcode_正しいショートコード文字列を生成する(): void
    {
        $result = get_function_text_shortcode(456);
        $this->assertSame('[temp id=456]', $result);
    }

    // ========================================================================
    // get_item_ranking_shortcode()
    // ========================================================================

    public function test_get_item_ranking_shortcode_正しいショートコード文字列を生成する(): void
    {
        $result = get_item_ranking_shortcode(789);
        $this->assertSame('[rank id=789]', $result);
    }

    // ========================================================================
    // remove_wrap_shortcode_wpautop()
    // ========================================================================

    public function test_remove_wrap_shortcode_wpautop_tiショートコードのみ抽出する(): void
    {
        $content = '<p>余計なテキスト</p>[ti title="手順1"]内容1[/ti]<p>余計なテキスト2</p>[ti title="手順2"]内容2[/ti]';
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertSame('[ti title="手順1"]内容1[/ti][ti title="手順2"]内容2[/ti]', $result);
    }

    public function test_remove_wrap_shortcode_wpautop_マッチしない場合nullを返す(): void
    {
        $content = '<p>テキストのみ</p>';
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertNull($result);
    }

    public function test_remove_wrap_shortcode_wpautop_ネストしたコンテンツを処理する(): void
    {
        $content = '[ti title="テスト"]<p>ネストされた<strong>コンテンツ</strong></p>[/ti]';
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertSame('[ti title="テスト"]<p>ネストされた<strong>コンテンツ</strong></p>[/ti]', $result);
    }

    public function test_remove_wrap_shortcode_wpautop_改行を含むコンテンツを処理する(): void
    {
        $content = "[ti title=\"A\"]\n内容A\n[/ti]\n[ti title=\"B\"]\n内容B\n[/ti]";
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertNotNull($result);
        $this->assertStringContainsString('[ti title="A"]', $result);
        $this->assertStringContainsString('[ti title="B"]', $result);
    }

    // ========================================================================
    // html_shortcode()
    // ========================================================================

    public function test_html_shortcode_HTMLエンティティをデコードする(): void
    {
        $content = '&lt;div&gt;テスト&lt;/div&gt;';
        $result = html_shortcode([], $content);
        $this->assertSame('<div>テスト</div>', $result);
    }

    public function test_html_shortcode_クォートはデコードしない(): void
    {
        // ENT_NOQUOTES ではクォートはデコードされない
        $content = '&quot;テスト&quot;';
        $result = html_shortcode([], $content);
        $this->assertSame('&quot;テスト&quot;', $result);
    }

    public function test_html_shortcode_nullの場合空文字を返す(): void
    {
        $result = html_shortcode([], null);
        $this->assertSame('', $result);
    }

    // ========================================================================
    // shortcodes_to_exempt_from_wptexturize()
    // ========================================================================

    public function test_shortcodes_to_exempt_htmlを配列に追加する(): void
    {
        $shortcodes = ['code', 'pre'];
        $result = shortcodes_to_exempt_from_wptexturize($shortcodes);
        $this->assertContains('html', $result);
        $this->assertContains('code', $result);
        $this->assertCount(3, $result);
    }

    public function test_shortcodes_to_exempt_空配列にhtmlを追加する(): void
    {
        $result = shortcodes_to_exempt_from_wptexturize([]);
        $this->assertSame(['html'], $result);
    }

    // ========================================================================
    // get_countdown_days()
    // ========================================================================

    public function test_get_countdown_days_未来日までの日数を返す(): void
    {
        // 10日後のタイムスタンプ
        $to = time() + (86400 * 10);
        $result = get_countdown_days($to);
        $this->assertSame('10', $result);
    }

    public function test_get_countdown_days_過去日は0を返す(): void
    {
        // 5日前のタイムスタンプ
        $to = time() - (86400 * 5);
        $result = get_countdown_days($to);
        $this->assertSame('0', $result);
    }

    public function test_get_countdown_days_今日は1を返す(): void
    {
        // 12時間後 = ceil(0.5) = 1
        $to = time() + (43200);
        $result = get_countdown_days($to);
        $this->assertSame('1', $result);
    }
}
