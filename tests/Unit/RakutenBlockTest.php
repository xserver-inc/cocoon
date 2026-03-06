<?php
/**
 * block-rakuten-product-link.php のユニットテスト
 *
 * 楽天商品リンクブロックのヘルパー関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class RakutenBlockTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // WordPress関数のスタブ（読み込み時に必要な最小限）
        // PHPの名前空間内ではfunction_existsはグローバル、スタブはNS内で定義されるため
        // __NAMESPACE__付きでチェックして他テストクラスとの再宣言競合を防ぐ
        if (!function_exists(__NAMESPACE__ . '\\register_rest_route')) {
            function register_rest_route($ns, $route, $args) {}
        }
        if (!function_exists(__NAMESPACE__ . '\\current_user_can')) {
            function current_user_can($cap) { return true; }
        }
        if (!function_exists(__NAMESPACE__ . '\\sanitize_text_field')) {
            function sanitize_text_field($str) { return trim(strip_tags($str)); }
        }
        if (!function_exists(__NAMESPACE__ . '\\sanitize_textarea_field')) {
            function sanitize_textarea_field($str) { return trim(strip_tags($str)); }
        }
        if (!function_exists(__NAMESPACE__ . '\\esc_url_raw')) {
            function esc_url_raw($url) { return filter_var($url, FILTER_SANITIZE_URL); }
        }
        if (!function_exists(__NAMESPACE__ . '\\esc_html')) {
            function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists(__NAMESPACE__ . '\\esc_attr')) {
            function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists(__NAMESPACE__ . '\\esc_url')) {
            function esc_url($url) { return filter_var($url, FILTER_SANITIZE_URL); }
        }
        if (!function_exists(__NAMESPACE__ . '\\wp_kses_post')) {
            function wp_kses_post($str) { return strip_tags($str, '<a><b><strong><em><i><span><div><p><br><img>'); }
        }
        if (!function_exists(__NAMESPACE__ . '\\rest_ensure_response')) {
            function rest_ensure_response($data) { return $data; }
        }
        if (!function_exists(__NAMESPACE__ . '\\add_action')) {
            function add_action($hook, $callback, $priority = 10, $args = 1) {}
        }
        if (!function_exists(__NAMESPACE__ . '\\apply_filters')) {
            function apply_filters($tag, $value) { return $value; }
        }
        if (!function_exists(__NAMESPACE__ . '\\is_wp_error')) {
            function is_wp_error($obj) { return ($obj instanceof \WP_Error); }
        }
        if (!function_exists(__NAMESPACE__ . '\\date_i18n')) {
            // テスト環境では単純にPHPのdate()を使用
            function date_i18n($format, $timestamp = null) {
                if ($timestamp !== null) {
                    return date($format, $timestamp);
                }
                return date($format);
            }
        }
        if (!function_exists(__NAMESPACE__ . '\\wp_date')) {
            // テスト環境では単純にdate()で代用
            function wp_date($format, $timestamp = null) {
                if ($timestamp !== null) {
                    return date($format, $timestamp);
                }
                return date($format);
            }
        }

        // 定数定義
        if (!defined('DEBUG_CACHE_ENABLE')) {
            define('DEBUG_CACHE_ENABLE', false);
        }
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

        // shortcodes-product-func.phpで定義される関数のスタブ
        if (!function_exists(__NAMESPACE__ . '\\get_rakuten_image_size')) {
            // 楽天画像URLからサイズを取得するスタブ（テストでは固定値を返す）
            function get_rakuten_image_size($url) { return null; }
        }

        // Amazonのショートコード依存関数（他のテストクラスで未定義の場合のみ定義）
        if (!function_exists(__NAMESPACE__ . '\\get_amazon_creators_api_credential_id')) {
            function get_amazon_creators_api_credential_id() { return ''; }
        }
        if (!function_exists(__NAMESPACE__ . '\\get_amazon_creators_api_secret')) {
            function get_amazon_creators_api_secret() { return ''; }
        }
        if (!function_exists(__NAMESPACE__ . '\\get_amazon_creators_api_version')) {
            function get_amazon_creators_api_version() { return '2.1'; }
        }

        // ファイルの読み込み
        require_once dirname(__DIR__, 2) . '/lib/shortcodes-product-func.php';
        require_once dirname(__DIR__, 2) . '/lib/block-rakuten-product-link.php';
    }

    // ========================================================================
    // cocoon_rakuten_block_format_search_item() のテスト
    // ========================================================================

    /**
     * 完全なアイテムデータからフォーマット結果が正しい
     */
    public function test_format_search_item_完全なデータから正しくフォーマットされる(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'       => 'shop123:item456',
            'itemName'       => 'テスト商品名',
            'shopName'       => 'テストショップ',
            'itemPrice'      => 3980,
            'mediumImageUrls' => [
                ['imageUrl' => 'https://example.com/img/medium.jpg'],
            ],
            'affiliateUrl'   => 'https://hb.afl.rakuten.co.jp/example',
        ]));

        $result = cocoon_rakuten_block_format_search_item($Item);

        $this->assertSame('shop123:item456', $result['itemCode']);
        $this->assertSame('テスト商品名', $result['title']);
        $this->assertSame('テストショップ', $result['shopName']);
        $this->assertSame('￥ 3,980', $result['price']);
        $this->assertSame('https://example.com/img/medium.jpg', $result['imageUrl']);
        $this->assertSame('https://hb.afl.rakuten.co.jp/example', $result['affiliateUrl']);
    }

    /**
     * 価格が0の場合、priceは空文字になる
     */
    public function test_format_search_item_価格が0の場合空文字になる(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'  => 'shop:item',
            'itemName'  => '商品名',
            'itemPrice' => 0,
        ]));

        $result = cocoon_rakuten_block_format_search_item($Item);

        $this->assertSame('', $result['price']);
    }

    /**
     * 画像URLが複数ある場合、最初の1件のみ使用する
     */
    public function test_format_search_item_複数画像の場合最初の1件を使用(): void
    {
        $Item = json_decode(json_encode([
            'itemCode' => 'shop:item',
            'mediumImageUrls' => [
                ['imageUrl' => 'https://example.com/first.jpg'],
                ['imageUrl' => 'https://example.com/second.jpg'],
            ],
        ]));

        $result = cocoon_rakuten_block_format_search_item($Item);

        $this->assertSame('https://example.com/first.jpg', $result['imageUrl']);
    }

    /**
     * 最小限のデータ（itemCodeのみ）でもエラーにならない
     */
    public function test_format_search_item_最小限のデータでもエラーにならない(): void
    {
        $Item = json_decode(json_encode(['itemCode' => 'shop:item']));

        $result = cocoon_rakuten_block_format_search_item($Item);

        $this->assertSame('shop:item', $result['itemCode']);
        $this->assertSame('', $result['title']);
        $this->assertSame('', $result['shopName']);
        $this->assertSame('', $result['price']);
        $this->assertSame('', $result['imageUrl']);
        $this->assertSame('', $result['affiliateUrl']);
    }

    /**
     * 空のオブジェクトでもエラーにならない
     */
    public function test_format_search_item_空オブジェクトでもエラーにならない(): void
    {
        $Item = new \stdClass();
        $result = cocoon_rakuten_block_format_search_item($Item);

        $this->assertSame('', $result['itemCode']);
        $this->assertSame('', $result['title']);
        $this->assertSame('', $result['imageUrl']);
    }

    /**
     * 価格が正しく3桁区切りでフォーマットされる
     */
    public function test_format_search_item_価格が3桁区切りでフォーマットされる(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'  => 'shop:item',
            'itemPrice' => 1234567,
        ]));

        $result = cocoon_rakuten_block_format_search_item($Item);

        $this->assertSame('￥ 1,234,567', $result['price']);
    }

    // ========================================================================
    // cocoon_rakuten_block_extract_item_data() のテスト
    // ========================================================================

    /**
     * 完全なアイテムデータから全フィールドが正しく抽出される
     */
    public function test_extract_item_data_完全なデータから全フィールドが正しく抽出される(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'     => 'shop123:item456',
            'itemName'     => 'テスト商品タイトル',
            'shopName'     => 'テストショップ',
            'shopCode'     => 'shop123',
            'itemPrice'    => 5800,
            'itemCaption'  => 'これは商品の説明文です。',
            'affiliateUrl' => 'https://hb.afl.rakuten.co.jp/example',
            'affiliateRate' => 1.0,
            'smallImageUrls' => [
                ['imageUrl' => 'https://example.com/img/small.jpg'],
            ],
            'mediumImageUrls' => [
                ['imageUrl' => 'https://example.com/img/medium.jpg'],
            ],
        ]));

        $result = cocoon_rakuten_block_extract_item_data($Item);

        $this->assertSame('shop123:item456', $result['itemCode']);
        $this->assertSame('テスト商品タイトル', $result['title']);
        $this->assertSame('テストショップ', $result['shopName']);
        $this->assertSame('shop123', $result['shopCode']);
        $this->assertSame(5800, $result['itemPrice']);
        $this->assertSame('これは商品の説明文です。', $result['itemCaption']);
        $this->assertSame('https://hb.afl.rakuten.co.jp/example', $result['affiliateUrl']);
        $this->assertSame(1.0, $result['affiliateRate']);
        $this->assertSame('https://example.com/img/small.jpg', $result['imageSmallUrl']);
        $this->assertSame('https://example.com/img/medium.jpg', $result['imageUrl']);
    }

    /**
     * 画像がない場合、デフォルトサイズが使用される
     */
    public function test_extract_item_data_画像なしの場合デフォルト値が使用される(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'  => 'shop:item',
            'itemName'  => 'タイトル',
            'itemPrice' => 1000,
        ]));

        $result = cocoon_rakuten_block_extract_item_data($Item);

        $this->assertSame('', $result['imageSmallUrl']);
        $this->assertSame(64, $result['imageSmallWidth']);
        $this->assertSame(64, $result['imageSmallHeight']);
        $this->assertSame('', $result['imageUrl']);
        $this->assertSame(128, $result['imageWidth']);
        $this->assertSame(128, $result['imageHeight']);
    }

    /**
     * 空オブジェクトでもクラッシュしない
     */
    public function test_extract_item_data_空オブジェクトでもクラッシュしない(): void
    {
        $Item = new \stdClass();
        $result = cocoon_rakuten_block_extract_item_data($Item);

        $this->assertSame('', $result['itemCode']);
        $this->assertSame('', $result['title']);
        $this->assertSame('', $result['shopName']);
        $this->assertSame('', $result['shopCode']);
        $this->assertSame(0, $result['itemPrice']);
        $this->assertSame('', $result['itemCaption']);
        $this->assertSame('', $result['affiliateUrl']);
        $this->assertSame(0.0, (float)$result['affiliateRate']);
    }

    /**
     * itemPriceが文字列で渡された場合、intにキャストされる
     */
    public function test_extract_item_data_価格が文字列でもintにキャストされる(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'  => 'shop:item',
            'itemPrice' => '2500',
        ]));

        $result = cocoon_rakuten_block_extract_item_data($Item);

        $this->assertSame(2500, $result['itemPrice']);
        $this->assertIsInt($result['itemPrice']);
    }

    /**
     * affiliateRateが文字列で渡された場合、floatにキャストされる
     */
    public function test_extract_item_data_アフィリエイトレートがfloatにキャストされる(): void
    {
        $Item = json_decode(json_encode([
            'itemCode'     => 'shop:item',
            'affiliateRate' => '1.5',
        ]));

        $result = cocoon_rakuten_block_extract_item_data($Item);

        $this->assertSame(1.5, $result['affiliateRate']);
        $this->assertIsFloat($result['affiliateRate']);
    }
}
