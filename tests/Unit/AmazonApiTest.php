<?php
/**
 * shortcodes-amazon.php のユニットテスト
 *
 * CocoonAwsV4 クラスと Amazon API 関連の関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class AmazonApiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Amazon API ファイルが依存する定数・関数を定義
        if (!defined('DEBUG_CACHE_ENABLE')) {
            define('DEBUG_CACHE_ENABLE', false);
        }

        // creators-api.php の依存関数
        if (!function_exists('get_amazon_creators_api_credential_id')) {
            function get_amazon_creators_api_credential_id() { return ''; }
        }
        if (!function_exists('get_amazon_creators_api_secret')) {
            function get_amazon_creators_api_secret() { return ''; }
        }
        if (!function_exists('get_amazon_creators_api_version')) {
            function get_amazon_creators_api_version() { return '2.1'; }
        }
        if (!function_exists('get_amazon_api_transient_id')) {
            function get_amazon_api_transient_id($key) { return 'transient_' . $key; }
        }
        if (!function_exists('get_amazon_api_transient_bk_id')) {
            function get_amazon_api_transient_bk_id($key) { return 'transient_bk_' . $key; }
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

        require_once dirname(__DIR__, 2) . '/lib/shortcodes-amazon.php';
    }

    // ========================================================================
    // CocoonAwsV4 クラス
    // ========================================================================

    public function test_CocoonAwsV4_インスタンスが正しく作成される(): void
    {
        $aws = new \CocoonAwsV4('AKIAIOSFODNN7EXAMPLE', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
        $this->assertInstanceOf(\CocoonAwsV4::class, $aws);
    }

    public function test_CocoonAwsV4_ヘッダーが生成される(): void
    {
        $aws = new \CocoonAwsV4('AKIAIOSFODNN7EXAMPLE', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
        $aws->setPath('/paapi5/searchitems');
        $aws->setServiceName('ProductAdvertisingAPI');
        $aws->setRegionName('us-east-1');
        $aws->setPayload('{}');
        $aws->setRequestMethod('POST');
        $aws->addHeader('host', 'webservices.amazon.com');
        $aws->addHeader('content-type', 'application/json; charset=UTF-8');
        $aws->addHeader('content-encoding', 'amz-1.0');

        $headers = $aws->getHeaders();

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertArrayHasKey('x-amz-date', $headers);
    }

    public function test_CocoonAwsV4_Authorization_ヘッダーの形式が正しい(): void
    {
        $aws = new \CocoonAwsV4('AKIAIOSFODNN7EXAMPLE', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
        $aws->setPath('/paapi5/searchitems');
        $aws->setServiceName('ProductAdvertisingAPI');
        $aws->setRegionName('us-east-1');
        $aws->setPayload('{}');
        $aws->setRequestMethod('POST');
        $aws->addHeader('host', 'webservices.amazon.com');
        $aws->addHeader('content-type', 'application/json; charset=UTF-8');

        $headers = $aws->getHeaders();
        $auth = $headers['Authorization'];

        // AWS4-HMAC-SHA256 Credential=.../.../.../aws4_request,SignedHeaders=...,Signature=...
        $this->assertStringStartsWith('AWS4-HMAC-SHA256', $auth);
        $this->assertStringContainsString('Credential=AKIAIOSFODNN7EXAMPLE', $auth);
        $this->assertStringContainsString('SignedHeaders=', $auth);
        $this->assertStringContainsString('Signature=', $auth);
    }

    public function test_CocoonAwsV4_x_amz_date_ヘッダーの形式が正しい(): void
    {
        $aws = new \CocoonAwsV4('AKIAIOSFODNN7EXAMPLE', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
        $aws->setPath('/paapi5/searchitems');
        $aws->setServiceName('ProductAdvertisingAPI');
        $aws->setRegionName('us-east-1');
        $aws->setPayload('{}');
        $aws->setRequestMethod('POST');
        $aws->addHeader('host', 'webservices.amazon.com');

        $headers = $aws->getHeaders();

        // x-amz-date format: YYYYMMDDTHHmmssZ
        $this->assertMatchesRegularExpression('/^\d{8}T\d{6}Z$/', $headers['x-amz-date']);
    }

    public function test_CocoonAwsV4_同じ入力で同じ署名が生成される(): void
    {
        $createInstance = function() {
            $aws = new \CocoonAwsV4('AKIAIOSFODNN7EXAMPLE', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
            $aws->setPath('/paapi5/searchitems');
            $aws->setServiceName('ProductAdvertisingAPI');
            $aws->setRegionName('us-east-1');
            $aws->setPayload('{"test": "data"}');
            $aws->setRequestMethod('POST');
            $aws->addHeader('host', 'webservices.amazon.com');
            return $aws;
        };

        $headers1 = $createInstance()->getHeaders();
        $headers2 = $createInstance()->getHeaders();

        $this->assertSame($headers1['Authorization'], $headers2['Authorization']);
    }

    // ========================================================================
    // is_paapi_json_error()
    // ========================================================================

    public function test_is_paapi_json_error_エラーがある場合trueを返す(): void
    {
        $json = json_decode('{"Errors": [{"Code": "TooManyRequests"}]}');
        $this->assertTrue(is_paapi_json_error($json));
    }

    public function test_is_paapi_json_error_エラーがない場合falseを返す(): void
    {
        $json = json_decode('{"ItemsResult": {"Items": []}}');
        $this->assertFalse(is_paapi_json_error($json));
    }

    public function test_is_paapi_json_error_nullの場合falseを返す(): void
    {
        $this->assertFalse(is_paapi_json_error(null));
    }

    public function test_is_paapi_json_error_オブジェクト以外の場合falseを返す(): void
    {
        $this->assertFalse(is_paapi_json_error('string'));
    }

    public function test_is_paapi_json_error_配列の場合falseを返す(): void
    {
        $this->assertFalse(is_paapi_json_error(['Errors' => []]));
    }

    // ========================================================================
    // is_paapi_json_item_exist()
    // ========================================================================

    public function test_is_paapi_json_item_exist_アイテムが存在する場合trueを返す(): void
    {
        $json = json_decode('{"ItemsResult": {"Items": [{"ASIN": "B001"}]}}');
        $this->assertTrue(is_paapi_json_item_exist($json));
    }

    public function test_is_paapi_json_item_exist_ItemsResultがない場合falseを返す(): void
    {
        $json = json_decode('{"Errors": []}');
        $this->assertFalse(is_paapi_json_item_exist($json));
    }

    public function test_is_paapi_json_item_exist_Itemsがない場合falseを返す(): void
    {
        $json = json_decode('{"ItemsResult": {"SearchURL": "..."}}');
        $this->assertFalse(is_paapi_json_item_exist($json));
    }

    public function test_is_paapi_json_item_exist_nullの場合falseを返す(): void
    {
        $this->assertFalse(is_paapi_json_item_exist(null));
    }

    public function test_is_paapi_json_item_exist_オブジェクト以外の場合falseを返す(): void
    {
        $this->assertFalse(is_paapi_json_item_exist('not an object'));
    }
}
