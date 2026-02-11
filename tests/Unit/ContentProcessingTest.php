<?php
/**
 * コンテンツ処理関連のユニットテスト
 *
 * TOC（目次）生成、コンテンツフィルタリング、
 * 見出し抽出ロジック等をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ContentProcessingTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // toc.php が依存する定数
        if (!defined('BEFORE_1ST_H2_TOC_PRIORITY_HIGH')) {
            define('BEFORE_1ST_H2_TOC_PRIORITY_HIGH', 9);
        }
        if (!defined('BEFORE_1ST_H2_TOC_PRIORITY_STANDARD')) {
            define('BEFORE_1ST_H2_TOC_PRIORITY_STANDARD', 12);
        }

        require_once dirname(__DIR__, 2) . '/lib/toc.php';
        require_once dirname(__DIR__, 2) . '/lib/content-category.php';
    }

    // ========================================================================
    // get_toc_filter_priority()
    // ========================================================================

    public function test_get_toc_filter_priority_デフォルトはスタンダード優先度(): void
    {
        // is_toc_before_ads() = false のとき STANDARD
        $result = get_toc_filter_priority();
        $this->assertSame(BEFORE_1ST_H2_TOC_PRIORITY_STANDARD, $result);
    }

    // ========================================================================
    // get_h_inner_content() - 見出し内容取得
    // ========================================================================

    public function test_get_h_inner_content_プレーンテキストはそのまま返す(): void
    {
        $result = get_h_inner_content('見出しテキスト');
        $this->assertSame('見出しテキスト', $result);
    }

    public function test_get_h_inner_content_HTMLタグを除去する(): void
    {
        // is_toc_heading_inner_html_tag_enable() = false のとき
        $result = get_h_inner_content('<strong>太字</strong>の見出し');
        $this->assertSame('太字の見出し', $result);
    }

    public function test_get_h_inner_content_アコーディオンボタンはそのまま返す(): void
    {
        $content = '<button aria-expanded="true">トグル</button>';
        $result = get_h_inner_content($content);
        $this->assertSame($content, $result);
    }

    public function test_get_h_inner_content_aria_expanded_falseのアコーディオンも保持する(): void
    {
        $content = '<button class="accordion" aria-expanded="false">閉じた状態</button>';
        $result = get_h_inner_content($content);
        $this->assertSame($content, $result);
    }

    // ========================================================================
    // is_toc_display_count_available() - 目次表示条件
    // ========================================================================

    public function test_is_toc_display_count_available_見出し数が閾値以上でtrueを返す(): void
    {
        // get_toc_display_count() = 2
        $this->assertTrue(is_toc_display_count_available(5));
    }

    public function test_is_toc_display_count_available_見出し数が閾値と同じでtrueを返す(): void
    {
        $this->assertTrue(is_toc_display_count_available(2));
    }

    public function test_is_toc_display_count_available_見出し数が閾値未満でfalseを返す(): void
    {
        $this->assertFalse(is_toc_display_count_available(1));
    }

    public function test_is_toc_display_count_available_見出し数0でfalseを返す(): void
    {
        $this->assertFalse(is_toc_display_count_available(0));
    }

    // ========================================================================
    // コンテンツ内の見出し抽出テスト（正規表現検証）
    // ========================================================================

    public function test_h2見出しの正規表現マッチ(): void
    {
        $content = '<h2>見出し1</h2><p>本文</p><h2>見出し2</h2>';
        preg_match_all('/<h([2-6])[^>]*?>(.*?)<\/h\1>/is', $content, $matches);
        $this->assertCount(2, $matches[0]);
        $this->assertSame('見出し1', $matches[2][0]);
        $this->assertSame('見出し2', $matches[2][1]);
    }

    public function test_h2からh6までの見出しの正規表現マッチ(): void
    {
        $content = '<h2>H2</h2><h3>H3</h3><h4>H4</h4><h5>H5</h5><h6>H6</h6>';
        preg_match_all('/<h([2-6])[^>]*?>(.*?)<\/h\1>/is', $content, $matches);
        $this->assertCount(5, $matches[0]);
        $this->assertSame(['2', '3', '4', '5', '6'], $matches[1]);
    }

    public function test_ID属性付き見出しの正規表現マッチ(): void
    {
        $content = '<h2 id="section-1">見出し</h2>';
        preg_match_all('/<h([2-6])[^>]*?>(.*?)<\/h\1>/is', $content, $matches);
        $this->assertCount(1, $matches[0]);
        $this->assertSame('見出し', $matches[2][0]);
    }

    public function test_h1見出しは目次に含まれない(): void
    {
        $content = '<h1>タイトル</h1><h2>見出し</h2>';
        preg_match_all('/<h([2-6])[^>]*?>(.*?)<\/h\1>/is', $content, $matches);
        $this->assertCount(1, $matches[0]);
        $this->assertSame('見出し', $matches[2][0]);
    }

    // ========================================================================
    // ブログカード URL正規表現テスト
    // ========================================================================

    public function test_blogcard_URL正規表現_pタグ内のURLにマッチする(): void
    {
        $content = '<p>https://example.com/article</p>';
        $pattern = '/^(<p[^>]*?>)?(<a[^>]+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(?!.*<br *\/?>).*?(<\/p>)?/im';
        $this->assertMatchesRegularExpression($pattern, $content);
    }

    public function test_blogcard_URL正規表現_裸のURLにマッチする(): void
    {
        $content = 'https://example.com/article';
        $pattern = '/^(<p[^>]*?>)?(<a[^>]+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(?!.*<br *\/?>).*?(<\/p>)?/im';
        $this->assertMatchesRegularExpression($pattern, $content);
    }

    public function test_blogcard_URL正規表現_テキスト付きURLにマッチしない(): void
    {
        $content = 'テキスト https://example.com/article';
        $pattern = '/^(<p[^>]*?>)?(<a[^>]+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(?!.*<br *\/?>).*?(<\/p>)?/im';
        $this->assertDoesNotMatchRegularExpression($pattern, $content);
    }

    // ========================================================================
    // ファイル拡張子除外正規表現テスト（OGPブログカード）
    // ========================================================================

    #[\PHPUnit\Framework\Attributes\DataProvider('mediaFileExtensionProvider')]
    public function test_メディアファイルURLは除外される(string $url): void
    {
        $pattern = '/.+(\.mp3|\.midi|\.mp4|\.mpeg|\.mpg|\.jpg|\.jpeg|\.png|\.gif|\.svg|\.pdf)$/i';
        $this->assertMatchesRegularExpression($pattern, $url);
    }

    public static function mediaFileExtensionProvider(): array
    {
        return [
            'MP3'  => ['https://example.com/audio.mp3'],
            'MP4'  => ['https://example.com/video.mp4'],
            'JPG'  => ['https://example.com/image.jpg'],
            'JPEG' => ['https://example.com/image.jpeg'],
            'PNG'  => ['https://example.com/image.png'],
            'GIF'  => ['https://example.com/image.gif'],
            'SVG'  => ['https://example.com/image.svg'],
            'PDF'  => ['https://example.com/document.pdf'],
        ];
    }

    public function test_HTMLページURLは除外されない(): void
    {
        $url = 'https://example.com/article/page';
        $pattern = '/.+(\.mp3|\.midi|\.mp4|\.mpeg|\.mpg|\.jpg|\.jpeg|\.png|\.gif|\.svg|\.pdf)$/i';
        $this->assertDoesNotMatchRegularExpression($pattern, $url);
    }

    // ========================================================================
    // get_the_category_meta_key() - カテゴリメタキー生成
    // ========================================================================

    public function test_get_the_category_meta_key_正しいキーを生成する(): void
    {
        $this->assertSame('category_meta_5', \get_the_category_meta_key(5));
    }

    public function test_get_the_category_meta_key_ID_0でもキーを生成する(): void
    {
        $this->assertSame('category_meta_0', \get_the_category_meta_key(0));
    }

    public function test_get_the_category_meta_key_大きなIDでもキーを生成する(): void
    {
        $this->assertSame('category_meta_99999', \get_the_category_meta_key(99999));
    }
}
