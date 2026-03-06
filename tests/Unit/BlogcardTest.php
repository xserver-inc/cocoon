<?php
/**
 * ブログカード関連のユニットテスト
 *
 * URL解析、pタグ判定、ブログカード無効化解除など
 * コンテンツ処理の正規表現ロジックをテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class BlogcardTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // blogcard 関連の定数
        if (!defined('URL_REG_STR')) {
            define('URL_REG_STR', 'https?://[-_.!~*\'()a-zA-Z0-9;/?:\@&=+\$,%#]+');
        }
        if (!defined('TRANSIENT_BLOGCARD_PREFIX')) {
            define('TRANSIENT_BLOGCARD_PREFIX', 'blogcard_');
        }

        require_once dirname(__DIR__, 2) . '/lib/blogcard-in.php';
    }

    // ========================================================================
    // is_p_tag_appropriate() - pタグ判定
    // ========================================================================

    public function test_is_p_tag_appropriate_pタグなしのURLはtrueを返す(): void
    {
        $this->assertTrue(is_p_tag_appropriate('https://example.com'));
    }

    public function test_is_p_tag_appropriate_正しいpタグ囲みはtrueを返す(): void
    {
        $match = '<p>https://example.com</p>';
        $this->assertTrue(is_p_tag_appropriate($match));
    }

    public function test_is_p_tag_appropriate_開始pタグのみはfalseを返す(): void
    {
        $match = '<p>https://example.com';
        $this->assertFalse(is_p_tag_appropriate($match));
    }

    public function test_is_p_tag_appropriate_終了pタグのみはfalseを返す(): void
    {
        $match = 'https://example.com</p>';
        $this->assertFalse(is_p_tag_appropriate($match));
    }

    public function test_is_p_tag_appropriate_クラス付きpタグもtrueを返す(): void
    {
        $match = '<p class="test">https://example.com</p>';
        $this->assertTrue(is_p_tag_appropriate($match));
    }

    // ========================================================================
    // ampersand_urldecode() - URLデコード
    // ========================================================================

    public function test_ampersand_urldecode_ampをアンパサンドに変換する(): void
    {
        $url = 'https://example.com/?a=1&amp;b=2';
        $this->assertSame('https://example.com/?a=1&b=2', ampersand_urldecode($url));
    }

    public function test_ampersand_urldecode_038をアンパサンドに変換する(): void
    {
        $url = 'https://example.com/?a=1#038;b=2';
        $this->assertSame('https://example.com/?a=1&b=2', ampersand_urldecode($url));
    }

    public function test_ampersand_urldecode_連続アンパサンドを一つにする(): void
    {
        $url = 'https://example.com/?a=1&&b=2';
        $this->assertSame('https://example.com/?a=1&b=2', ampersand_urldecode($url));
    }

    public function test_ampersand_urldecode_正常なURLはそのまま返す(): void
    {
        $url = 'https://example.com/?a=1&b=2';
        $this->assertSame($url, ampersand_urldecode($url));
    }

    // ========================================================================
    // get_url_params() - URLパラメータ取得
    // ========================================================================

    public function test_get_url_params_クエリパラメータを配列で返す(): void
    {
        $url = 'https://example.com/?title=テスト&snippet=説明文';
        $result = get_url_params($url);
        $this->assertIsArray($result);
        $this->assertSame('テスト', $result['title']);
        $this->assertSame('説明文', $result['snippet']);
    }

    public function test_get_url_params_クエリがないURLはnullを返す(): void
    {
        $url = 'https://example.com/path/to/page';
        $result = get_url_params($url);
        $this->assertNull($result);
    }

    public function test_get_url_params_ampエンコードされたURLを処理する(): void
    {
        $url = 'https://example.com/?a=1&amp;b=2';
        $result = get_url_params($url);
        $this->assertIsArray($result);
        $this->assertSame('1', $result['a']);
        $this->assertSame('2', $result['b']);
    }

    // ========================================================================
    // calc_publisher_image_sizes() - パブリシャーロゴサイズ
    // ========================================================================

    public function test_calc_publisher_image_sizes_正常なサイズはそのまま返す(): void
    {
        $result = calc_publisher_image_sizes(300, 30);
        $this->assertSame(300, $result['width']);
        $this->assertSame(30, $result['height']);
    }

    public function test_calc_publisher_image_sizes_幅が大きい場合600に縮小する(): void
    {
        $result = calc_publisher_image_sizes(1200, 120);
        $this->assertEquals(600, $result['width']);
        $this->assertEquals(60, $result['height']);
    }

    public function test_calc_publisher_image_sizes_高さが大きい場合60に縮小する(): void
    {
        $result = calc_publisher_image_sizes(300, 120);
        $this->assertEquals(150, $result['width']);
        $this->assertEquals(60, $result['height']);
    }

    public function test_calc_publisher_image_sizes_幅も高さも大きい場合両方縮小する(): void
    {
        $result = calc_publisher_image_sizes(1200, 240);
        // まず幅600に: height = 240 * (600/1200) = 120
        // 次に高さ60に: width = 600 * (60/120) = 300
        $this->assertEquals(300, $result['width']);
        $this->assertEquals(60, $result['height']);
    }

    // ========================================================================
    // sanitize_shortcode_value() - ショートコード値サニタイズ
    // ========================================================================

    public function test_sanitize_shortcode_value_通常のテキストをサニタイズする(): void
    {
        $result = sanitize_shortcode_value('テスト値');
        $this->assertSame('テスト値', $result);
    }

    public function test_sanitize_shortcode_value_HTMLタグを除去する(): void
    {
        $result = sanitize_shortcode_value('<script>alert("xss")</script>テスト');
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('テスト', $result);
    }

    public function test_sanitize_shortcode_value_クォートを除去する(): void
    {
        $result = sanitize_shortcode_value('テスト"値\'');
        $this->assertStringNotContainsString('"', $result);
        $this->assertStringNotContainsString("'", $result);
    }

    public function test_sanitize_shortcode_value_ブラケットを除去する(): void
    {
        $result = sanitize_shortcode_value('テスト[値]');
        $this->assertStringNotContainsString('[', $result);
        $this->assertStringNotContainsString(']', $result);
    }

    public function test_sanitize_shortcode_value_前後空白をトリムする(): void
    {
        $result = sanitize_shortcode_value('  テスト  ');
        $this->assertStringNotContainsString('  ', $result);
    }

    public function test_sanitize_shortcode_value_nullはそのまま返す(): void
    {
        $this->assertNull(sanitize_shortcode_value(null));
    }

    public function test_sanitize_shortcode_value_空文字列はそのまま返す(): void
    {
        $this->assertSame('', sanitize_shortcode_value(''));
    }

    // ========================================================================
    // remove_crlf() - 改行削除
    // ========================================================================

    public function test_remove_crlf_改行を削除する(): void
    {
        $this->assertSame('abc', remove_crlf("a\nb\rc"));
    }

    public function test_remove_crlf_CRLFを削除する(): void
    {
        $this->assertSame('abc', remove_crlf("a\r\nb\r\nc"));
    }

    public function test_remove_crlf_改行なしはそのまま返す(): void
    {
        $this->assertSame('abc', remove_crlf('abc'));
    }

    // ========================================================================
    // wpunautop() - wpautop段落削除
    // ========================================================================

    public function test_wpunautop_pタグを削除する(): void
    {
        $this->assertSame('テスト', wpunautop('<p>テスト</p>'));
    }

    public function test_wpunautop_brタグを削除する(): void
    {
        $this->assertSame('テスト', wpunautop('テスト<br />'));
    }

    public function test_wpunautop_pタグとbrタグの両方を削除する(): void
    {
        $this->assertSame('テスト文章', wpunautop('<p>テスト<br />文章</p>'));
    }

    // ========================================================================
    // str_to_bool() - 文字列をBoolean変換
    // ========================================================================

    public function test_str_to_bool_1はtrueを返す(): void
    {
        $this->assertTrue(str_to_bool('1'));
    }

    public function test_str_to_bool_trueはtrueを返す(): void
    {
        $this->assertTrue(str_to_bool('true'));
    }

    public function test_str_to_bool_0はfalseを返す(): void
    {
        $this->assertFalse(str_to_bool('0'));
    }

    public function test_str_to_bool_falseはfalseを返す(): void
    {
        $this->assertFalse(str_to_bool('false'));
    }

    public function test_str_to_bool_空文字列はfalseを返す(): void
    {
        $this->assertFalse(str_to_bool(''));
    }

    public function test_str_to_bool_nullはfalseを返す(): void
    {
        $this->assertFalse(str_to_bool(null));
    }

    public function test_str_to_bool_任意の文字列はtrueを返す(): void
    {
        $this->assertTrue(str_to_bool('yes'));
    }

    // ========================================================================
    // get_another_scheme_url() - スキーム変換
    // ========================================================================

    public function test_get_another_scheme_url_httpsをhttpに変換する(): void
    {
        $result = get_another_scheme_url('https://example.com/path');
        $this->assertSame('http://example.com/path', $result);
    }

    public function test_get_another_scheme_url_httpをhttpsに変換する(): void
    {
        $result = get_another_scheme_url('http://example.com/path');
        $this->assertSame('https://example.com/path', $result);
    }

    // ========================================================================
    // cancel_blog_card_deactivation() - ブログカード無効化解除
    // ========================================================================

    public function test_cancel_blog_card_deactivation_エクスクラメーション付きURLのpタグ内無効化を解除する(): void
    {
        $content = '<p>!https://example.com/path</p>';
        $result = cancel_blog_card_deactivation($content, true);
        $this->assertSame('<p>https://example.com/path</p>', $result);
    }

    public function test_cancel_blog_card_deactivation_pタグなしのエクスクラメーション付きURLの無効化を解除する(): void
    {
        $content = '!https://example.com/path';
        $result = cancel_blog_card_deactivation($content, false);
        $this->assertSame('https://example.com/path', $result);
    }

    // ========================================================================
    // get_human_years_ago() - 年齢計算
    // ========================================================================

    public function test_get_human_years_ago_正しい年数を返す(): void
    {
        // 2000年1月1日生まれ
        $from = strtotime('2000-01-01');
        $result = get_human_years_ago($from);
        $expected_year = (int)(new \DateTime('today'))->diff(new \DateTime('2000-01-01'))->format('%y');
        $this->assertSame((string)$expected_year, $result);
    }

    public function test_get_human_years_ago_単位付きで返す(): void
    {
        $from = strtotime('2000-01-01');
        $result = get_human_years_ago($from, '歳');
        $this->assertStringEndsWith('歳', $result);
    }

}
