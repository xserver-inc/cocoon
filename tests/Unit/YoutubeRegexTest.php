<?php
/**
 * YouTube関連の正規表現テスト
 *
 * YouTube埋め込み処理で使用される正規表現パターン
 * （ビデオID抽出、プレイリストID抽出等）を検証します。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class YoutubeRegexTest extends TestCase
{
    // ========================================================================
    // YouTube ビデオID抽出（embed URLから）
    // ========================================================================

    #[DataProvider('youtubeEmbedUrlProvider')]
    public function test_embedURLからビデオIDを抽出する(string $embedHtml, string $expectedId): void
    {
        preg_match('/(?<=embed\/)(.+?)(?=\?)/', $embedHtml, $match);
        $this->assertNotEmpty($match, 'ビデオIDの抽出に失敗: ' . $embedHtml);
        $this->assertSame($expectedId, $match[1]);
    }

    public static function youtubeEmbedUrlProvider(): array
    {
        return [
            '標準embed' => [
                '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ?feature=oembed">',
                'dQw4w9WgXcQ',
            ],
            'パラメータ複数' => [
                '<iframe src="https://www.youtube.com/embed/abc123DEF_-?rel=0&autoplay=1">',
                'abc123DEF_-',
            ],
        ];
    }

    // ========================================================================
    // YouTube プレイリストID抽出
    // ========================================================================

    #[DataProvider('youtubePlaylistProvider')]
    public function test_プレイリストIDを抽出する(string $html, string $expectedListId): void
    {
        preg_match('/(?<=list=)(.+?)(?=")/', $html, $match);
        $this->assertNotEmpty($match, 'プレイリストIDの抽出に失敗');
        $this->assertSame($expectedListId, $match[1]);
    }

    public static function youtubePlaylistProvider(): array
    {
        return [
            '標準プレイリスト' => [
                '<iframe src="https://www.youtube.com/embed/videoseries?list=PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf"',
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
            ],
            'list=パラメータ' => [
                '<iframe src="https://www.youtube.com/embed/?list=PL1234567890ABCDEF"',
                'PL1234567890ABCDEF',
            ],
        ];
    }

    // ========================================================================
    // videoseries/list= キーワード判定
    // ========================================================================

    public function test_プレイリスト判定_videoseries(): void
    {
        $html = '<iframe src="https://www.youtube.com/embed/videoseries?list=PLtest123">';
        $this->assertMatchesRegularExpression('/videoseries|list=/i', $html);
    }

    public function test_プレイリスト判定_list_パラメータ(): void
    {
        $html = '<iframe src="https://www.youtube.com/embed/?list=PLtest123">';
        $this->assertMatchesRegularExpression('/videoseries|list=/i', $html);
    }

    public function test_通常の動画はプレイリスト判定されない(): void
    {
        $html = '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ?feature=oembed">';
        $result = preg_match_all('/videoseries|list=/i', $html, $m);
        $this->assertSame(0, $result);
    }

    // ========================================================================
    // data-youtube キャッシュ抽出
    // ========================================================================

    public function test_dataYoutubeキャッシュを抽出する(): void
    {
        $cache = base64_encode(json_encode(['title' => 'テスト動画', 'video_id' => 'abc123']));
        $html = '<iframe data-youtube="' . $cache . '" src="https://youtube.com/embed/abc123">';
        preg_match('/(?<=data-youtube=")(.+?)(?=")/', $html, $match);
        $this->assertNotEmpty($match);
        $decoded = json_decode(base64_decode($match[1]), true);
        $this->assertSame('テスト動画', $decoded['title']);
        $this->assertSame('abc123', $decoded['video_id']);
    }

    public function test_dataYoutubeがないHTML(): void
    {
        $html = '<iframe src="https://youtube.com/embed/abc123">';
        $result = preg_match('/(?<=data-youtube=")(.+?)(?=")/', $html, $match);
        $this->assertSame(0, $result);
    }

    // ========================================================================
    // YouTube サムネイルURL生成パターン
    // ========================================================================

    #[DataProvider('youtubeThumbnailProvider')]
    public function test_サムネイルURLからビデオIDを抽出する(string $thumbnailUrl, string $expectedId): void
    {
        preg_match('/(?<=vi\/)(.+?)(?=\/)/', $thumbnailUrl, $match);
        $this->assertNotEmpty($match);
        $this->assertSame($expectedId, $match[1]);
    }

    public static function youtubeThumbnailProvider(): array
    {
        return [
            '標準サムネイル' => [
                'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
                'dQw4w9WgXcQ',
            ],
            'maxresサムネイル' => [
                'https://i.ytimg.com/vi/abc123DEF_-/maxresdefault.jpg',
                'abc123DEF_-',
            ],
        ];
    }

    // ========================================================================
    // Unicode エスケープ修正（YouTube JSON）
    // ========================================================================

    public function test_大文字Uエスケープを小文字に修正する(): void
    {
        $data = '{"title": "\\U0001f534 Red Circle"}';
        $fixed = str_replace("\\U", '\\u', $data);
        $this->assertStringContainsString('\\u0001f534', $fixed);
        $this->assertStringNotContainsString('\\U0001f534', $fixed);
    }
}
