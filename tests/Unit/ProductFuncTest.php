<?php
/**
 * 商品リンク関数のユニットテスト
 *
 * Amazon/楽天/Yahoo/メルカリ/DMM等のアフィリエイトURL生成、
 * キャッシュID生成、画像サイズ抽出、ボタンHTML生成をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ProductFuncTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // 定数
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
        if (!defined('HIDDEN_DELETE_FIELD_NAME')) {
            define('HIDDEN_DELETE_FIELD_NAME', '_cocoon_delete');
        }
        if (!defined('ONCLICK_DELETE_CONFIRM')) {
            define('ONCLICK_DELETE_CONFIRM', '');
        }

        // 依存関数スタブ
        if (!function_exists('htmlspecialchars_decode')) {
            // PHP 組み込みなので通常不要
        }
        if (!function_exists('is_user_administrator')) {
            function is_user_administrator() { return false; }
        }
        if (!function_exists('add_query_arg')) {
            function add_query_arg($args, $url = '') { return $url . '?' . http_build_query($args); }
        }
        if (!function_exists('admin_url')) {
            function admin_url($path = '') { return 'http://example.com/wp-admin/' . $path; }
        }
        if (!function_exists('wp_create_nonce')) {
            function wp_create_nonce($action = -1) { return 'test_nonce'; }
        }
        if (!function_exists('get_message_box_tag')) {
            function get_message_box_tag($message, $class = '') { return '<div class="' . $class . '">' . $message . '</div>'; }
        }
        if (!function_exists('get_content_excerpt')) {
            function get_content_excerpt($content, $length = 120) { return mb_substr(strip_tags($content), 0, $length); }
        }

        require_once dirname(__DIR__, 2) . '/lib/shortcodes-product-func.php';
    }

    // ========================================================================
    // get_amazon_associate_url() - AmazonアソシエイトURL
    // ========================================================================

    public function test_get_amazon_associate_url_ASINのみでURLを生成する(): void
    {
        $result = get_amazon_associate_url('B08N5WRWNW');
        $this->assertStringContainsString('www.amazon.co.jp', $result);
        $this->assertStringContainsString('B08N5WRWNW', $result);
    }

    public function test_get_amazon_associate_url_トラッキングID付きでURLを生成する(): void
    {
        $result = get_amazon_associate_url('B08N5WRWNW', 'mytag-22');
        $this->assertStringContainsString('B08N5WRWNW', $result);
        $this->assertStringContainsString('mytag-22', $result);
    }

    // ========================================================================
    // get_amazon_search_url() - Amazon検索URL
    // ========================================================================

    public function test_get_amazon_search_url_キーワードでURLを生成する(): void
    {
        $result = get_amazon_search_url('PHP入門');
        $this->assertStringStartsWith('https://www.amazon.co.jp/gp/search?keywords=', $result);
        $this->assertStringContainsString(urlencode('PHP入門'), $result);
    }

    public function test_get_amazon_search_url_トラッキングID付き(): void
    {
        $result = get_amazon_search_url('テスト', 'tag-22');
        $this->assertStringContainsString('&tag=tag-22', $result);
    }

    public function test_get_amazon_search_url_トラッキングIDなし(): void
    {
        $result = get_amazon_search_url('テスト');
        $this->assertStringNotContainsString('&tag=', $result);
    }

    // ========================================================================
    // get_amazon_review_url() - AmazonレビューURL
    // ========================================================================

    public function test_get_amazon_review_url_ASINでレビューURLを生成する(): void
    {
        $result = get_amazon_review_url('B08N5WRWNW');
        $this->assertSame('https://www.amazon.co.jp/product-reviews/B08N5WRWNW/', $result);
    }

    public function test_get_amazon_review_url_トラッキングID付き(): void
    {
        $result = get_amazon_review_url('B08N5WRWNW', 'tag-22');
        $this->assertStringContainsString('?tag=tag-22', $result);
    }

    public function test_get_amazon_review_url_前後空白をトリムする(): void
    {
        $result = get_amazon_review_url(' B08N5WRWNW ');
        $this->assertStringContainsString('/B08N5WRWNW/', $result);
    }

    // ========================================================================
    // get_rakuten_search_url() - 楽天検索URL
    // ========================================================================

    public function test_get_rakuten_search_url_キーワードでURLを生成する(): void
    {
        $result = get_rakuten_search_url('PHP入門', null);
        $this->assertStringStartsWith('https://search.rakuten.co.jp/search/mall/', $result);
        $this->assertStringContainsString(urlencode('PHP入門'), $result);
    }

    public function test_get_rakuten_search_url_除外キーワード付き(): void
    {
        $result = get_rakuten_search_url('ノートPC', ['中古', '訳あり']);
        $this->assertStringContainsString('?nitem=', $result);
        $this->assertStringContainsString('中古', $result);
        $this->assertStringContainsString('訳あり', $result);
    }

    public function test_get_rakuten_search_url_除外キーワードなし(): void
    {
        $result = get_rakuten_search_url('テスト', null);
        $this->assertStringNotContainsString('?nitem=', $result);
    }

    // ========================================================================
    // get_rakuten_affiliate_search_url() - 楽天アフィリエイト検索URL
    // ========================================================================

    public function test_get_rakuten_affiliate_search_url_基本URL生成(): void
    {
        $result = get_rakuten_affiliate_search_url('PHP入門', 'affiliate123');
        $this->assertStringStartsWith('https://hb.afl.rakuten.co.jp/hgc/affiliate123/', $result);
        $this->assertStringContainsString('pc=', $result);
        $this->assertStringContainsString('&m=', $result);
    }

    public function test_get_rakuten_affiliate_search_url_前後空白をトリムする(): void
    {
        $result = get_rakuten_affiliate_search_url('テスト', ' affiliate123 ');
        $this->assertStringContainsString('/affiliate123/', $result);
    }

    // ========================================================================
    // get_yahoo_search_url() - Yahoo!検索URL
    // ========================================================================

    public function test_get_yahoo_search_url_キーワードでURLを生成する(): void
    {
        $result = get_yahoo_search_url('PHP入門');
        $this->assertSame('https://search.shopping.yahoo.co.jp/search?p=' . urlencode('PHP入門'), $result);
    }

    // ========================================================================
    // get_valucomace_yahoo_search_url() - バリューコマースYahoo!URL
    // ========================================================================

    public function test_get_valucomace_yahoo_search_url_パラメータ付きURLを生成する(): void
    {
        $result = get_valucomace_yahoo_search_url('テスト', 'sid123', 'pid456');
        $this->assertStringContainsString('sid=sid123', $result);
        $this->assertStringContainsString('pid=pid456', $result);
        $this->assertStringContainsString(urlencode('テスト'), $result);
    }

    // ========================================================================
    // get_mercari_search_url() - メルカリ検索URL
    // ========================================================================

    public function test_get_mercari_search_url_アフィリエイトID付きURLを生成する(): void
    {
        $result = get_mercari_search_url('iPhone', 'aff123');
        $this->assertSame('https://jp.mercari.com/search?afid=aff123&keyword=' . urlencode('iPhone'), $result);
    }

    public function test_get_mercari_search_url_日本語キーワード(): void
    {
        $result = get_mercari_search_url('中古パソコン', 'aff456');
        $this->assertStringContainsString(urlencode('中古パソコン'), $result);
    }

    // ========================================================================
    // get_dmm_search_url() - DMM検索URL
    // ========================================================================

    public function test_get_dmm_search_url_アフィリエイトID付きURLを生成する(): void
    {
        $result = get_dmm_search_url('テスト', 'dmm123');
        $this->assertStringStartsWith('https://al.dmm.com/', $result);
        $this->assertStringContainsString('af_id=dmm123', $result);
        $this->assertStringContainsString(urlencode('テスト'), $result);
    }

    // ========================================================================
    // get_moshimo_amazon_search_url() - もしもAmazon URL
    // ========================================================================

    public function test_get_moshimo_amazon_search_url_もしもURL生成(): void
    {
        $result = get_moshimo_amazon_search_url('テスト', 'moshi123');
        $this->assertStringStartsWith('https://af.moshimo.com/af/c/click?a_id=moshi123', $result);
        $this->assertStringContainsString('p_id=170', $result);
    }

    // ========================================================================
    // get_moshimo_rakuten_search_url() - もしも楽天URL
    // ========================================================================

    public function test_get_moshimo_rakuten_search_url_もしもURL生成(): void
    {
        $result = get_moshimo_rakuten_search_url('テスト', 'moshi456', null);
        $this->assertStringStartsWith('https://af.moshimo.com/af/c/click?a_id=moshi456', $result);
        $this->assertStringContainsString('p_id=54', $result);
    }

    // ========================================================================
    // get_moshimo_yahoo_search_url() - もしもYahoo!URL
    // ========================================================================

    public function test_get_moshimo_yahoo_search_url_もしもURL生成(): void
    {
        $result = get_moshimo_yahoo_search_url('テスト', 'moshi789');
        $this->assertStringStartsWith('https://af.moshimo.com/af/c/click?a_id=moshi789', $result);
        $this->assertStringContainsString('p_id=1225', $result);
    }

    // ========================================================================
    // get_long_str_to_md5_hash() - 長い文字列のMD5変換
    // ========================================================================

    public function test_get_long_str_to_md5_hash_短い文字列はそのまま返す(): void
    {
        $this->assertSame('short', get_long_str_to_md5_hash('short'));
    }

    public function test_get_long_str_to_md5_hash_長い文字列はMD5ハッシュになる(): void
    {
        $long = str_repeat('a', 51);
        $result = get_long_str_to_md5_hash($long);
        $this->assertSame(32, strlen($result)); // MD5は32文字
        $this->assertSame(md5($long), $result);
    }

    public function test_get_long_str_to_md5_hash_ちょうど50文字はそのまま返す(): void
    {
        $str = str_repeat('a', 50);
        $this->assertSame($str, get_long_str_to_md5_hash($str));
    }

    public function test_get_long_str_to_md5_hash_カスタム長さ(): void
    {
        $str = 'abcdefghijk'; // 11文字
        $result = get_long_str_to_md5_hash($str, 10);
        $this->assertSame(md5($str), $result);
    }

    // ========================================================================
    // get_amazon_api_transient_id() - AmazonキャッシュID
    // ========================================================================

    public function test_get_amazon_api_transient_id_プレフィックス付きIDを返す(): void
    {
        $result = get_amazon_api_transient_id('B08N5WRWNW');
        $this->assertStringStartsWith(TRANSIENT_AMAZON_API_PREFIX, $result);
        $this->assertStringContainsString('B08N5WRWNW', $result);
    }

    // ========================================================================
    // get_rakuten_api_transient_id() - 楽天キャッシュID
    // ========================================================================

    public function test_get_rakuten_api_transient_id_プレフィックス付きIDを返す(): void
    {
        $result = get_rakuten_api_transient_id('item123');
        $this->assertStringStartsWith(TRANSIENT_RAKUTEN_API_PREFIX, $result);
    }

    // ========================================================================
    // get_rakuten_image_size() - 楽天画像サイズ取得
    // ========================================================================

    public function test_get_rakuten_image_size_URLからサイズを抽出する(): void
    {
        $url = 'https://thumbnail.image.rakuten.co.jp/ran/img/1001/0004/548/592/973/240/10010004548592973240_1.jpg?_ex=128x128';
        $result = get_rakuten_image_size($url);
        $this->assertSame(128, $result['width']);
        $this->assertSame(128, $result['height']);
    }

    public function test_get_rakuten_image_size_異なるサイズを抽出する(): void
    {
        $url = 'https://example.com/image.jpg?_ex=200x300';
        $result = get_rakuten_image_size($url);
        $this->assertSame(200, $result['width']);
        $this->assertSame(300, $result['height']);
    }

    public function test_get_rakuten_image_size_サイズ情報がないURLはnullを返す(): void
    {
        $url = 'https://example.com/image.jpg';
        // 元コードが $m[1] に直接アクセスするため、マッチしない場合は警告が出る
        $result = @get_rakuten_image_size($url);
        $this->assertNull($result);
    }

    // ========================================================================
    // get_additional_button_tag() - ボタンHTML生成
    // ========================================================================

    public function test_get_additional_button_tag_URLとテキストからボタンを生成する(): void
    {
        $result = get_additional_button_tag('https://example.com', 'ボタン', null);
        $this->assertStringContainsString('https://example.com', $result);
        $this->assertStringContainsString('ボタン', $result);
        $this->assertStringContainsString('shoplinkbtn', $result);
    }

    public function test_get_additional_button_tag_カスタムタグが優先される(): void
    {
        $custom = '<a href="https://custom.com">カスタム</a>';
        $result = get_additional_button_tag('https://example.com', 'テスト', $custom);
        $this->assertStringContainsString('カスタム', $result);
        $this->assertStringNotContainsString('example.com', $result);
    }

    public function test_get_additional_button_tag_URLもテキストもないときnullを返す(): void
    {
        $result = get_additional_button_tag(null, null, null);
        $this->assertNull($result);
    }

    public function test_get_additional_button_tag_カスタム名前でクラスが生成される(): void
    {
        $result = get_additional_button_tag('https://example.com', 'テスト', null, 'btn1');
        $this->assertStringContainsString('shoplinkbtn1', $result);
    }

    // ========================================================================
    // get_item_price_tag() - 商品価格タグ
    // ========================================================================

    public function test_get_item_price_tag_価格タグを生成する(): void
    {
        $result = get_item_price_tag('¥1,980');
        $this->assertStringContainsString('¥1,980', $result);
        $this->assertStringContainsString('product-item-price', $result);
    }

    public function test_get_item_price_tag_日付付き価格タグ(): void
    {
        $result = get_item_price_tag('¥1,980', '2025/01/01');
        $this->assertStringContainsString('¥1,980', $result);
        $this->assertStringContainsString('2025/01/01', $result);
        $this->assertStringContainsString('acquired-date', $result);
    }

    public function test_get_item_price_tag_価格がないときnullを返す(): void
    {
        $this->assertNull(get_item_price_tag(null));
        $this->assertNull(get_item_price_tag(''));
    }

    // ========================================================================
    // get_item_description_tag() - 商品説明タグ
    // ========================================================================

    public function test_get_item_description_tag_説明タグを生成する(): void
    {
        $result = get_item_description_tag('テスト商品の説明');
        $this->assertStringContainsString('テスト商品の説明', $result);
        $this->assertStringContainsString('product-item-description', $result);
    }

    public function test_get_item_description_tag_空のときnullを返す(): void
    {
        $this->assertNull(get_item_description_tag(null));
        $this->assertNull(get_item_description_tag(''));
    }

    // ========================================================================
    // wrap_product_item_box() - 商品ボックスラッパー
    // ========================================================================

    public function test_wrap_product_item_box_メッセージをボックスで囲む(): void
    {
        $result = wrap_product_item_box('エラーメッセージ', 'amazon');
        $this->assertStringContainsString('エラーメッセージ', $result);
        $this->assertStringContainsString('amazon-item-box', $result);
        $this->assertStringContainsString('product-item-error', $result);
    }

    public function test_wrap_product_item_box_楽天タイプ(): void
    {
        $result = wrap_product_item_box('テスト', 'rakuten');
        $this->assertStringContainsString('rakuten-item-box', $result);
    }
}
