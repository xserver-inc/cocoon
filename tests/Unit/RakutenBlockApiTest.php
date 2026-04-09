<?php
/**
 * 楽天API（新旧・エッジケース対応）の通信ロジック用ユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey;

class RakutenBlockApiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!function_exists(__NAMESPACE__ . '\\register_rest_route')) {
            function register_rest_route($ns, $route, $args) {}
        }
        if (!function_exists(__NAMESPACE__ . '\\current_user_can')) {
            function current_user_can($cap) { return true; }
        }
        if (!function_exists(__NAMESPACE__ . '\\sanitize_text_field')) {
            function sanitize_text_field($str) { return trim(strip_tags($str)); }
        }
        if (!function_exists(__NAMESPACE__ . '\\is_wp_error')) {
            function is_wp_error($obj) { return ($obj instanceof \WP_Error); }
        }
        if (!function_exists(__NAMESPACE__ . '\\rest_ensure_response')) {
            function rest_ensure_response($data) { return $data; }
        }
        if (!function_exists(__NAMESPACE__ . '\\apply_filters')) {
            function apply_filters($tag, $value) { return $value; }
        }

        // 定数定義
        if (!defined('COCOON_RAKUTEN_API_VERSION')) {
            define('COCOON_RAKUTEN_API_VERSION', '20260401');
        }
        if (!defined('COCOON_RAKUTEN_API_LEGACY_VERSION')) {
            define('COCOON_RAKUTEN_API_LEGACY_VERSION', '20220601');
        }
        if (!defined('THEME_NAME')) {
            define('THEME_NAME', 'cocoon');
        }

        // ファイル読み込み（ヘルパーを先に読み込む）
        require_once dirname(__DIR__, 2) . '/lib/rakuten-api-helpers.php';
        require_once dirname(__DIR__, 2) . '/lib/block-rakuten-product-link.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 共通モック
        Monkey\Functions\when('get_rakuten_application_id')->justReturn('test_app_id');
        Monkey\Functions\when('get_rakuten_affiliate_id')->justReturn('test_aff_id');
        Monkey\Functions\when('get_rakuten_api_sort')->justReturn('standard');
    }

    protected function tearDown(): void
    {
        global $test_mock_wp_remote_get_response;
        global $test_mock_wp_remote_get_args;
        global $test_mock_wp_remote_get_url;
        $test_mock_wp_remote_get_response = null;
        $test_mock_wp_remote_get_args = null;
        $test_mock_wp_remote_get_url = null;

        Monkey\tearDown();
        parent::tearDown();
    }

    // ========================================================================
    // 旧APIへのフォールバック通信テスト（正常系）
    // ========================================================================
    
    public function test_通信エンドポイントのフォールバック_アクセスキーなし(): void
    {
        // アクセスキーが未設定の場合
        Monkey\Functions\when('get_rakuten_access_key')->justReturn('');

        // HTTPリクエスト関数をモックし、送信されるURLを補足する
        global $test_mock_wp_remote_get_response;
        global $test_mock_wp_remote_get_url;
        
        $test_mock_wp_remote_get_response = function($url, $args) {
            return [
                'response' => ['code' => 200],
                'body' => json_encode(['Items' => []])
            ];
        };

        // 検索リクエストの実行
        $request = \Mockery::mock('\WP_REST_Request');
        $request->shouldReceive('get_param')->with('keyword')->andReturn('テスト');
        $request->shouldReceive('get_param')->with('page')->andReturn(1);
        $request->shouldReceive('get_param')->with('sort')->andReturn('standard');
        $request->shouldReceive('get_param')->with('purchaseType')->andReturn(0);

        $result = cocoon_rakuten_block_search($request);
        
        // 旧API形式であることを確認
        $requestedUrl = $test_mock_wp_remote_get_url;
        $this->assertStringContainsString('https://app.rakuten.co.jp/services/api/IchibaItem/Search/'.COCOON_RAKUTEN_API_LEGACY_VERSION, $requestedUrl);
        // application_id はあるが accessKey はないこと
        $this->assertStringContainsString('applicationId=test_app_id', $requestedUrl);
        $this->assertStringNotContainsString('accessKey=', $requestedUrl);
        
        $this->assertTrue($result['success']);
    }

    // ========================================================================
    // 新API（OpenAPI）通信テスト（正常系）
    // ========================================================================
    
    public function test_新API通信_アクセスキーあり_かつヘッダーが付与されている(): void
    {
        Monkey\Functions\when('get_rakuten_access_key')->justReturn('test_access_key');

        global $test_mock_wp_remote_get_response;
        global $test_mock_wp_remote_get_url;
        global $test_mock_wp_remote_get_args;

        $test_mock_wp_remote_get_response = function($url, $args) {
            return [
                'response' => ['code' => 200],
                'body' => json_encode([
                    'count' => 1,
                    'Items' => [
                        [
                            'Item' => [
                                'itemName' => 'ダミー商品',
                                'itemPrice' => 100
                            ]
                        ]
                    ]
                ])
            ];
        };

        $request = \Mockery::mock('\WP_REST_Request');
        $request->shouldReceive('get_param')->with('keyword')->andReturn('テスト');
        $request->shouldReceive('get_param')->with('page')->andReturn(1);
        $request->shouldReceive('get_param')->with('sort')->andReturn('');
        $request->shouldReceive('get_param')->with('purchaseType')->andReturn(0);

        $result = cocoon_rakuten_block_search($request);
        
        // 新APIエンドポイントであることを確認
        $requestedUrl = $test_mock_wp_remote_get_url;
        $requestedArgs = $test_mock_wp_remote_get_args;
        $this->assertStringContainsString('https://openapi.rakuten.co.jp/ichibams/api/IchibaItem/Search/20260401', $requestedUrl);
        // accessKeyがクエリに含まれていること
        $this->assertStringContainsString('accessKey=test_access_key', $requestedUrl);
        
        // ヘッダーが正しく設定されていること（Referer と Origin）
        $headers = $requestedArgs['headers'];
        $this->assertSame('http://example.com/', $headers['Referer']);
        $this->assertSame('http://example.com', $headers['Origin']);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['items']);
    }

    // ========================================================================
    // JSONレスポンスの構造差異テスト（エッジケース・formatVersion2）
    // ========================================================================
    
    public function test_JSONフォーマット差異のフラット構造を正常にパースできる(): void
    {
        Monkey\Functions\when('get_rakuten_access_key')->justReturn('test_access_key');

        global $test_mock_wp_remote_get_response;
        $test_mock_wp_remote_get_response = [
            'response' => ['code' => 200],
            'body' => json_encode([
                'count' => 2,
                'Items' => [
                    [
                        'itemName' => '新仕様商品A',
                        'itemPrice' => 150
                    ],
                    [
                        'itemName' => '新仕様商品B',
                        'itemPrice' => 200
                    ]
                ]
            ])
        ];

        $request = \Mockery::mock('\WP_REST_Request');
        $request->shouldReceive('get_param')->with('keyword')->andReturn('テスト');
        $request->shouldReceive('get_param')->with('page')->andReturn(1);
        $request->shouldReceive('get_param')->with('sort')->andReturn('');
        $request->shouldReceive('get_param')->with('purchaseType')->andReturn(0);

        $result = cocoon_rakuten_block_search($request);
        
        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['items']);
        $this->assertSame('新仕様商品A', $result['items'][0]['title']);
        $this->assertSame('新仕様商品B', $result['items'][1]['title']);
    }

    // ========================================================================
    // HTTPステータスエラー エラーメッセージ構造の抽出テスト（エッジケース）
    // ========================================================================
    
    public function test_HTTPエラー_新APIのエラーメッセージ構造を正しく抽出する(): void
    {
        Monkey\Functions\when('get_rakuten_access_key')->justReturn('test_access_key');

        global $test_mock_wp_remote_get_response;
        $test_mock_wp_remote_get_response = [
            'response' => ['code' => 403],
            'body' => json_encode([
                'errors' => [
                    'errorCode' => 403,
                    'errorMessage' => 'Invalid Access Key'
                ]
            ])
        ];

        $request = \Mockery::mock('\WP_REST_Request');
        $request->shouldReceive('get_param')->with('keyword')->andReturn('テスト');
        $request->shouldReceive('get_param')->with('page')->andReturn(1);
        $request->shouldReceive('get_param')->with('sort')->andReturn('');
        $request->shouldReceive('get_param')->with('purchaseType')->andReturn(0);

        $result = cocoon_rakuten_block_search($request);
        
        $this->assertInstanceOf('\WP_Error', $result);
        // エラー詳細メッセージが "Invalid Access Key" として正しく抽出できているか
        $this->assertSame('Invalid Access Key', $result->get_error_message());
        $this->assertSame(403, $result->get_error_data()['status']);
    }

    public function test_HTTPエラー_旧APIのエラーメッセージ構造を正しく抽出する(): void
    {
        Monkey\Functions\when('get_rakuten_access_key')->justReturn('');

        global $test_mock_wp_remote_get_response;
        $test_mock_wp_remote_get_response = [
            'response' => ['code' => 400],
            'body' => json_encode([
                'error' => 'wrong_parameter',
                'error_description' => 'specify valid applicationId'
            ])
        ];

        $request = \Mockery::mock('\WP_REST_Request');
        $request->shouldReceive('get_param')->with('keyword')->andReturn('テスト');
        $request->shouldReceive('get_param')->with('page')->andReturn(1);
        $request->shouldReceive('get_param')->with('sort')->andReturn('');
        $request->shouldReceive('get_param')->with('purchaseType')->andReturn(0);

        $result = cocoon_rakuten_block_search($request);
        
        $this->assertInstanceOf('\WP_Error', $result);
        $this->assertSame('specify valid applicationId', $result->get_error_message());
    }


}
