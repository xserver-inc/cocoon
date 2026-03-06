<?php
/**
 * block-amazon-product-link.php のユニットテスト
 *
 * Amazon商品リンクブロックのヘルパー関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class AmazonBlockTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // WordPress関数のスタブ（読み込み時に必要な最小限）
        if (!function_exists('register_rest_route')) {
            function register_rest_route($ns, $route, $args) {}
        }
        if (!function_exists('current_user_can')) {
            function current_user_can($cap) { return true; }
        }
        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($str) { return trim(strip_tags($str)); }
        }
        if (!function_exists('esc_url_raw')) {
            function esc_url_raw($url) { return filter_var($url, FILTER_SANITIZE_URL); }
        }
        if (!function_exists('esc_html')) {
            function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists('esc_attr')) {
            function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists('esc_url')) {
            function esc_url($url) { return filter_var($url, FILTER_SANITIZE_URL); }
        }
        if (!function_exists('wp_kses_post')) {
            // scriptタグを除去する簡易実装
            function wp_kses_post($str) { return strip_tags($str, '<a><b><strong><em><i><span><div><p><br><img>'); }
        }
        if (!function_exists('rest_ensure_response')) {
            function rest_ensure_response($data) { return $data; }
        }
        if (!function_exists('add_action')) {
            function add_action($hook, $callback, $priority = 10, $args = 1) {}
        }

        // shortcodes-product-func.php の依存関数
        if (!defined('DEBUG_CACHE_ENABLE')) {
            define('DEBUG_CACHE_ENABLE', false);
        }
        if (!defined('AMAZON_DOMAIN')) {
            define('AMAZON_DOMAIN', 'www.amazon.co.jp');
        }
        if (!function_exists('shortcode_atts')) {
            function shortcode_atts($pairs, $atts, $shortcode = '') {
                $out = $pairs;
                if (is_array($atts)) {
                    foreach ($atts as $name => $value) {
                        if (array_key_exists($name, $out)) {
                            $out[$name] = $value;
                        }
                    }
                }
                return $out;
            }
        }
        // キャッシュ定数
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

        // creators-api.php の依存関数
        if (!function_exists(__NAMESPACE__ . '\\get_amazon_creators_api_credential_id')) {
            function get_amazon_creators_api_credential_id() { return ''; }
        }
        if (!function_exists(__NAMESPACE__ . '\\get_amazon_creators_api_secret')) {
            function get_amazon_creators_api_secret() { return ''; }
        }
        if (!function_exists(__NAMESPACE__ . '\\get_amazon_creators_api_version')) {
            function get_amazon_creators_api_version() { return '2.1'; }
        }

        // ブロック関連ファイルの読み込み
        require_once dirname(__DIR__, 2) . '/lib/shortcodes-product-func.php';
        require_once dirname(__DIR__, 2) . '/lib/shortcodes-amazon.php';
        require_once dirname(__DIR__, 2) . '/lib/block-amazon-product-link.php';
    }

    // ========================================================================
    // cocoon_amazon_block_extract_maker() のテスト
    // ========================================================================

    /**
     * Author フィールドがある場合、その値を返す
     */
    public function test_extract_maker_Authorフィールドがある場合その値を返す(): void
    {
        $item = json_decode(json_encode([
            'ItemInfo' => [
                'ByLineInfo' => [
                    'Author' => ['DisplayValue' => '山田太郎'],
                ],
            ],
        ]));
        $this->assertSame('山田太郎', cocoon_amazon_block_extract_maker($item));
    }

    /**
     * Brand フィールドのみある場合、その値を返す
     */
    public function test_extract_maker_Brandフィールドのみある場合その値を返す(): void
    {
        $item = json_decode(json_encode([
            'ItemInfo' => [
                'ByLineInfo' => [
                    'Brand' => ['DisplayValue' => 'Sony'],
                ],
            ],
        ]));
        $this->assertSame('Sony', cocoon_amazon_block_extract_maker($item));
    }

    /**
     * Author と Brand の両方がある場合、Author を優先する
     */
    public function test_extract_maker_AuthorとBrandがある場合Authorを優先する(): void
    {
        $item = json_decode(json_encode([
            'ItemInfo' => [
                'ByLineInfo' => [
                    'Author' => ['DisplayValue' => '著者名'],
                    'Brand'  => ['DisplayValue' => 'ブランド名'],
                ],
            ],
        ]));
        $this->assertSame('著者名', cocoon_amazon_block_extract_maker($item));
    }

    /**
     * ByLineInfo がない場合、空文字を返す
     */
    public function test_extract_maker_ByLineInfoがない場合空文字を返す(): void
    {
        $item = json_decode(json_encode([
            'ItemInfo' => [],
        ]));
        $this->assertSame('', cocoon_amazon_block_extract_maker($item));
    }

    /**
     * ItemInfo がない場合、空文字を返す
     */
    public function test_extract_maker_ItemInfoがない場合空文字を返す(): void
    {
        $item = new \stdClass();
        $this->assertSame('', cocoon_amazon_block_extract_maker($item));
    }

    // ========================================================================
    // cocoon_amazon_block_format_search_item() のテスト
    // ========================================================================

    /**
     * 完全なアイテムデータからフォーマット結果が正しい
     */
    public function test_format_search_item_完全なデータから正しくフォーマットされる(): void
    {
        $item = json_decode(json_encode([
            'ASIN' => 'B09V3KXJPB',
            'ItemInfo' => [
                'Title' => ['DisplayValue' => 'テスト商品'],
                'ByLineInfo' => [
                    'Brand' => ['DisplayValue' => 'テストブランド'],
                ],
            ],
            'Images' => [
                'Primary' => [
                    'Medium' => ['URL' => 'https://example.com/image.jpg'],
                ],
            ],
            'DetailPageURL' => 'https://www.amazon.co.jp/dp/B09V3KXJPB',
        ]));

        $result = cocoon_amazon_block_format_search_item($item);

        $this->assertSame('B09V3KXJPB', $result['asin']);
        $this->assertSame('テスト商品', $result['title']);
        $this->assertSame('テストブランド', $result['maker']);
        $this->assertSame('https://example.com/image.jpg', $result['imageUrl']);
        $this->assertSame('https://www.amazon.co.jp/dp/B09V3KXJPB', $result['detailPageUrl']);
    }

    /**
     * 最小限のデータ（ASINのみ）でもエラーにならない
     */
    public function test_format_search_item_最小限のデータでもエラーにならない(): void
    {
        $item = json_decode(json_encode([
            'ASIN' => 'B001',
        ]));

        $result = cocoon_amazon_block_format_search_item($item);

        $this->assertSame('B001', $result['asin']);
        $this->assertSame('', $result['title']);
        $this->assertSame('', $result['maker']);
        $this->assertSame('', $result['imageUrl']);
    }

    /**
     * 空のオブジェクトでもエラーにならない
     */
    public function test_format_search_item_空オブジェクトでもエラーにならない(): void
    {
        $item = new \stdClass();
        $result = cocoon_amazon_block_format_search_item($item);

        $this->assertSame('', $result['asin']);
        $this->assertSame('', $result['title']);
    }

    // ========================================================================
    // cocoon_amazon_block_extract_item_data() のテスト
    // ========================================================================

    /**
     * 完全なアイテムデータから全フィールドが正しく抽出される
     */
    public function test_extract_item_data_完全なデータから全フィールドが正しく抽出される(): void
    {
        $item = json_decode(json_encode([
            'ASIN' => 'B09V3KXJPB',
            'ItemInfo' => [
                'Title' => ['DisplayValue' => 'テスト商品タイトル'],
                'ByLineInfo' => [
                    'Author' => ['DisplayValue' => '著者名'],
                ],
                'Classifications' => [
                    'ProductGroup' => ['DisplayValue' => '本'],
                ],
                'Features' => [
                    'DisplayValues' => ['特徴1', '特徴2'],
                ],
            ],
            'Images' => [
                'Primary' => [
                    'Small' => ['URL' => 'https://img/s.jpg', 'Width' => 75, 'Height' => 75],
                    'Medium' => ['URL' => 'https://img/m.jpg', 'Width' => 160, 'Height' => 160],
                    'Large' => ['URL' => 'https://img/l.jpg', 'Width' => 500, 'Height' => 500],
                ],
                'Variants' => [
                    ['Small' => ['URL' => 'https://img/v1s.jpg', 'Width' => 30, 'Height' => 30]],
                ],
            ],
            'DetailPageURL' => 'https://www.amazon.co.jp/dp/B09V3KXJPB',
        ]));

        $result = cocoon_amazon_block_extract_item_data($item);

        $this->assertSame('B09V3KXJPB', $result['asin']);
        $this->assertSame('テスト商品タイトル', $result['title']);
        $this->assertSame('著者名', $result['maker']);
        $this->assertSame('本', $result['productGroup']);
        $this->assertSame('特徴1', $result['description']);
        $this->assertSame('https://www.amazon.co.jp/dp/B09V3KXJPB', $result['detailPageUrl']);
        // 画像サイズの確認
        $this->assertSame('https://img/s.jpg', $result['imageSmallUrl']);
        $this->assertSame(75, $result['imageSmallWidth']);
        $this->assertSame('https://img/m.jpg', $result['imageUrl']);
        $this->assertSame(160, $result['imageWidth']);
        $this->assertSame('https://img/l.jpg', $result['imageLargeUrl']);
        $this->assertSame(500, $result['imageLargeWidth']);
        // バリアント画像
        $this->assertCount(1, $result['variantImages']);
        $this->assertSame('https://img/v1s.jpg', $result['variantImages'][0]['smallUrl']);
    }

    /**
     * 画像情報がない場合、デフォルトサイズが使用される
     */
    public function test_extract_item_data_画像なしの場合デフォルト値が使用される(): void
    {
        $item = json_decode(json_encode([
            'ASIN' => 'B001',
            'ItemInfo' => [
                'Title' => ['DisplayValue' => 'タイトル'],
            ],
        ]));

        $result = cocoon_amazon_block_extract_item_data($item);

        $this->assertSame('', $result['imageSmallUrl']);
        $this->assertSame(75, $result['imageSmallWidth']);
        $this->assertSame(75, $result['imageSmallHeight']);
        $this->assertSame('', $result['imageUrl']);
        $this->assertSame(160, $result['imageWidth']);
        $this->assertSame(160, $result['imageHeight']);
        $this->assertSame('', $result['imageLargeUrl']);
        $this->assertSame(500, $result['imageLargeWidth']);
        $this->assertSame(500, $result['imageLargeHeight']);
        $this->assertSame([], $result['variantImages']);
    }

    /**
     * 空オブジェクトでもクラッシュしない
     */
    public function test_extract_item_data_空オブジェクトでもクラッシュしない(): void
    {
        $item = new \stdClass();
        $result = cocoon_amazon_block_extract_item_data($item);

        $this->assertSame('', $result['asin']);
        $this->assertSame('', $result['title']);
        $this->assertSame('', $result['maker']);
        $this->assertSame([], $result['variantImages']);
    }

    // ========================================================================
    // wp_kses_post サニタイズの動作確認（btnTag関連）
    // ========================================================================

    /**
     * wp_kses_post が安全なHTMLタグ（aタグ）を保持することを確認
     * 注: scriptタグの除去はWordPress実環境の wp_kses_post に依存するため
     *     ユニットテストでは安全なタグの保持のみを検証する
     */
    public function test_wp_kses_post_安全なHTMLタグが保持される(): void
    {
        $html = '<a href="https://example.com">リンク</a><strong>太字</strong>';
        $result = wp_kses_post($html);

        $this->assertStringContainsString('<a href="https://example.com">リンク</a>', $result);
        $this->assertStringContainsString('<strong>太字</strong>', $result);
    }
}
