<?php
/**
 * OpenGraph解析のユニットテスト
 *
 * OpenGraphGetter::_parse() のHTMLメタタグ解析ロジック、
 * OGPデータの抽出・フォールバック処理をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class OpenGraphTest extends TestCase
{
    private static \ReflectionMethod $parseMethod;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // open-graph.php 依存関数
        if (!function_exists('wp_remote_retrieve_response_code')) {
            function wp_remote_retrieve_response_code($response) { return 200; }
        }
        if (!function_exists('wp_filesystem_get_contents')) {
            function wp_filesystem_get_contents($url, $use_include_path = false) { return false; }
        }
        if (!function_exists('get_http_content')) {
            function get_http_content($url) { return false; }
        }
        if (!function_exists('is_amazon_site_page')) {
            function is_amazon_site_page($url) { return strpos((string)$url, 'amazon') !== false || strpos((string)$url, 'amzn.to') !== false; }
        }
        if (!function_exists('is_rakuten_site_page')) {
            function is_rakuten_site_page($url) {
                return (strpos((string)$url, 'rakuten.co.jp') !== false) || (strpos((string)$url, 'r10s.jp') !== false);
            }
        }

        require_once dirname(__DIR__, 2) . '/lib/open-graph.php';

        // _parse はプライベートメソッドなのでリフレクションでアクセスする
        self::$parseMethod = new \ReflectionMethod(\OpenGraphGetter::class, '_parse');
    }

    /**
     * _parse メソッドを呼び出すヘルパー
     */
    private function callParse(string $html, ?string $uri = null)
    {
        return self::$parseMethod->invoke(null, $html, $uri);
    }

    /**
     * テスト用HTMLを生成するヘルパー
     */
    private function makeHtml(string $head = '', string $body = ''): string
    {
        return '<!DOCTYPE html><html><head><meta charset="UTF-8">' . $head . '</head><body>' . $body . '</body></html>';
    }

    // ========================================================================
    // OGPタグの抽出
    // ========================================================================

    public function test_OGPタイトルを抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" content="テストタイトル">' .
            '<title>フォールバックタイトル</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('テストタイトル', $og->title);
    }

    public function test_OGP説明文を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:description" content="テスト説明文">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('テスト説明文', $og->description);
    }

    public function test_OGP画像を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://example.com/image.jpg">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('https://example.com/image.jpg', $og->image);
    }

    public function test_OGPサイト名を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:site_name" content="テストサイト">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('テストサイト', $og->site_name);
    }

    public function test_OGPタイプを抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:type" content="article">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('article', $og->type);
    }

    // ========================================================================
    // フォールバック動作
    // ========================================================================

    public function test_OGPタイトルがない場合titleタグからフォールバック(): void
    {
        $html = $this->makeHtml('<title>Test Title Value</title>');
        $og = $this->callParse($html);
        $this->assertSame('Test Title Value', $og->title);
    }

    public function test_OGP説明文がない場合metaDescriptionからフォールバック(): void
    {
        $html = $this->makeHtml(
            '<meta name="description" content="メタディスクリプション">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('メタディスクリプション', $og->description);
    }

    public function test_OGPタイトルが空文字の場合titleタグからフォールバック(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" content="">' .
            '<title>Fallback Title</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('Fallback Title', $og->title);
    }

    // ========================================================================
    // エッジケース
    // ========================================================================

    public function test_空HTMLはfalseを返す(): void
    {
        $result = $this->callParse('');
        $this->assertFalse($result);
    }

    public function test_メタタグもタイトルもないHTMLはfalseを返す(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body>コンテンツのみ</body></html>';
        $result = $this->callParse($html);
        $this->assertFalse($result);
    }

    public function test_複数のOGPプロパティを同時に抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" content="タイトル">' .
            '<meta property="og:description" content="説明">' .
            '<meta property="og:image" content="https://example.com/img.jpg">' .
            '<meta property="og:url" content="https://example.com/">' .
            '<title>タイトル</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('タイトル', $og->title);
        $this->assertSame('説明', $og->description);
        $this->assertSame('https://example.com/img.jpg', $og->image);
        $this->assertSame('https://example.com/', $og->url);
    }

    public function test_シングルクォートのmetaDescriptionも取得できる(): void
    {
        $html = $this->makeHtml(
            "<meta name='description' content='シングルクォート説明'>" .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('シングルクォート説明', $og->description);
    }

    // ========================================================================
    // value属性パターン（NYTなど malformed OGP）
    // ========================================================================

    public function test_value属性のOGPも抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:title" value="value属性タイトル">' .
            '<title>テスト</title>'
        );
        $og = $this->callParse($html);
        $this->assertSame('value属性タイトル', $og->title);
    }

    // ========================================================================
    // $TYPES 定数
    // ========================================================================

    public function test_OGPタイプ定義が正しい構造である(): void
    {
        $types = \OpenGraphGetter::$TYPES;
        $this->assertIsArray($types);
        $this->assertArrayHasKey('website', $types);
        $this->assertArrayHasKey('product', $types);
        $this->assertContains('blog', $types['website']);
        $this->assertContains('book', $types['product']);
    }

    // ========================================================================
    // 楽天固有のエッジケース (Rakuten)
    // ========================================================================

    public function test_Rakuten_汎用ロゴを回避してHTMLから商品画像を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://r.r10s.jp/com/img/home/2000/logo_r_fb.jpg">' .
            '<img itemprop="image" src="https://image.rakuten.co.jp/shop/cabinet/item.jpg?_ex=400x400">' .
            '<title>楽天テスト</title>'
        );
        
        $og = $this->callParse($html, 'https://item.rakuten.co.jp/shop/item/');
        // 汎用ロゴが除外され、itemprop="image"から抽出＋パラメータ除去が行われる想定
        $this->assertSame('https://image.rakuten.co.jp/shop/cabinet/item.jpg', $og->image);
    }

    public function test_Rakuten_画像URLのサイズ制限パラメータを除去して高画質URLにする(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://thumbnail.image.rakuten.co.jp/@0_mall/shop/cabinet/item.jpg?_ex=300x300&fitin=300:300">' .
            '<title>楽天テスト</title>'
        );
        
        $og = $this->callParse($html, 'https://item.rakuten.co.jp/shop/item/');
        $this->assertSame('https://thumbnail.image.rakuten.co.jp/@0_mall/shop/cabinet/item.jpg', $og->image);
    }

    public function test_Rakuten_og_imageがない場合にitemprop等から画像を抽出する(): void
    {
        $html = $this->makeHtml(
            '<meta itemprop="image" content="https://tshop.r10s.jp/shop/cabinet/item2.jpg?fitin=128:128">' .
            '<title>楽天テスト</title>'
        );
        
        $og = $this->callParse($html, 'https://item.rakuten.co.jp/shop/item/');
        $this->assertSame('https://tshop.r10s.jp/shop/cabinet/item2.jpg', $og->image);
    }

    public function test_Rakuten_ショップ画像パスにlogoディレクトリを含むogImageは除外しない(): void
    {
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://thumbnail.image.rakuten.co.jp/@0_mall/tonya/logo/logo1n.jpg?_ex=200x200">' .
            '<title>楽天ショップテスト</title>'
        );

        $og = $this->callParse($html, 'https://www.rakuten.co.jp/tonya/');
        // ショップ独自画像は除外されず、パラメータ除去のみ行われるべき
        $this->assertSame('https://thumbnail.image.rakuten.co.jp/@0_mall/tonya/logo/logo1n.jpg', $og->image);
    }

    // ========================================================================
    // Amazon特有のエッジケース・画像抽出ロジックテスト
    // ========================================================================

    public function test_Amazonの汎用ロゴは無視して別属性から商品画像を取得する(): void
    {
        $uri = 'https://www.amazon.co.jp/dp/B000000000';
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://m.media-amazon.com/images/G/09/social_share/amazon_logo.png">' .
            '<title>Amazonテスト</title>',
            '<img id="landingImage" data-old-hires="https://m.media-amazon.com/images/I/91XXXXXXX._AC_SY400_.jpg" src="https://m.media-amazon.com/images/I/91XXXXXXX._AC_SY200_.jpg">'
        );
        $og = $this->callParse($html, $uri);
        // パラメータが除去されたクリーンなオリジナル解像度画像であることを確認
        $this->assertSame('https://m.media-amazon.com/images/I/91XXXXXXX.jpg', $og->image);
    }

    public function test_Amazon_imgBlkFrontから書籍の画像を取得する(): void
    {
        $uri = 'https://www.amazon.co.jp/dp/B000000000';
        $html = $this->makeHtml(
            '<title>Amazon書籍</title>',
            '<img id="imgBlkFront" src="https://m.media-amazon.com/images/I/51abcde._SX331_BO1,204,203,200_.jpg">'
        );
        $og = $this->callParse($html, $uri);
        // パラメータが除去されることを確認
        $this->assertSame('https://m.media-amazon.com/images/I/51abcde.jpg', $og->image);
    }

    public function test_Amazon_動的画像データ_data_a_dynamic_image_から取得する(): void
    {
        $uri = 'https://www.amazon.co.jp/dp/B000000000';
        // JSON形式のキーとして画像URLが記述されているパターン
        $dynamic_img = htmlspecialchars('{"https://m.media-amazon.com/images/I/81XYZ._AC_UL320_.jpg":[320,320],"https://m.media-amazon.com/images/I/81XYZ._AC_UL640_.jpg":[640,640]}', ENT_QUOTES);
        $html = $this->makeHtml(
            '<title>Amazon商品</title>',
            '<img id="landingImage" data-a-dynamic-image="' . $dynamic_img . '">'
        );
        $og = $this->callParse($html, $uri);
        // 最初の画像のパラメータが除去されたバージョンになることを確認
        $this->assertSame('https://m.media-amazon.com/images/I/81XYZ.jpg', $og->image);
    }

    public function test_Amazon_正規のOGPが設定されていればそれを採用する(): void
    {
        $uri = 'https://www.amazon.co.jp/dp/B000000000';
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://m.media-amazon.com/images/I/1234567.jpg">' .
            '<title>Amazon商品</title>',
            '<img id="landingImage" data-old-hires="https://m.media-amazon.com/images/I/91XXXXXXX.jpg">'
        );
        $og = $this->callParse($html, $uri);
        // og:image が汎用ロゴでない場合は、HTML内探索の前にog:imageが優先される
        $this->assertSame('https://m.media-amazon.com/images/I/1234567.jpg', $og->image);
    }

    public function test_非AmazonのURLではAmazon用処理が実行されない(): void
    {
        $uri = 'https://example.com/page'; // is_amazon_site_page が false のはず
        $html = $this->makeHtml(
            '<meta property="og:image" content="https://example.com/amazon_logo.png">' . // 汎用ロゴと同じ文字列
            '<title>非Amazonサイト</title>',
            '<img id="landingImage" data-old-hires="https://example.com/item.jpg">'
        );
        $og = $this->callParse($html, $uri);
        // 非Amazonの場合は、amazon_logo文字列であっても無視されず、そのまま採用される
        $this->assertSame('https://example.com/amazon_logo.png', $og->image);
    }
}
