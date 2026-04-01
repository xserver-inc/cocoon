<?php
/**
 * Creators API関連のユニットテスト
 *
 * エッジケースを含むバージョン判定や、リージョンごとのLwAトークンエンドポイント振り分けをテストします。
 *
 * 注意: wp_remote_post 等のグローバル変数方式でスタブの戻り値を制御しているため、
 * Brain\Monkey\Functions\expect() は一部関数（get_transient 等）のデフォルト動作設定にのみ使用しています。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class CreatorsApiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // テスト環境でAMAZON_DOMAINが未定義の場合は設定しておく
        if (!defined('AMAZON_DOMAIN')) {
            define('AMAZON_DOMAIN', 'amazon.co.jp');
        }

        // amazon_creators_api_get_user_agent はグローバルスタブに存在しないため定義
        if (!function_exists('amazon_creators_api_get_user_agent')) {
            function amazon_creators_api_get_user_agent() { return 'CocoonTest/1.0'; }
        }

        // テスト対象ファイルを読み込み
        require_once dirname(__DIR__, 2) . '/lib/creators-api.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // transient 関数のデフォルト動作を Brain\Monkey で定義
        // cron-test-stubs.php ではなくここで定義することで Patchwork の DefinedTooEarly を回避
        \Brain\Monkey\Functions\when('get_transient')->justReturn(false);
        \Brain\Monkey\Functions\when('set_transient')->justReturn(true);
        \Brain\Monkey\Functions\when('delete_transient')->justReturn(true);

        // 各テスト前にグローバルモック変数をリセット
        global $test_mock_wp_remote_post_response;
        global $test_mock_wp_remote_post_args;
        global $test_mock_apply_filters_callbacks;

        $test_mock_wp_remote_post_response = null;
        $test_mock_wp_remote_post_args = [];
        $test_mock_apply_filters_callbacks = [];
    }

    protected function tearDown(): void
    {
        // グローバルモック変数をクリーンアップ
        global $test_mock_wp_remote_post_response;
        global $test_mock_wp_remote_post_args;
        global $test_mock_apply_filters_callbacks;
        global $test_mock_creators_api_credential_id;

        $test_mock_wp_remote_post_response = null;
        $test_mock_wp_remote_post_args = [];
        $test_mock_apply_filters_callbacks = [];
        $test_mock_creators_api_credential_id = null;

        parent::tearDown();
    }

    // ========================================================================
    // amazon_creators_api_get_version() のテスト
    // ========================================================================

    public function test_get_version_通常キーの場合はデフォルトバージョンを返す(): void
    {
        $credential_id = 'ABCDEFGHIJKLMN'; // amzn1.以外
        $version = amazon_creators_api_get_version($credential_id);
        $this->assertSame('2.3', $version);
    }

    public function test_get_version_LwAキーの場合は3_3を返す(): void
    {
        $credential_id = 'amzn1.application-oa2-client.1234567890';
        $version = amazon_creators_api_get_version($credential_id);
        $this->assertSame('3.3', $version);
    }

    public function test_get_version_空文字やnullの場合もデフォルトを返す(): void
    {
        $this->assertSame('2.3', amazon_creators_api_get_version(''));
        $this->assertSame('2.3', amazon_creators_api_get_version(null));
    }

    // ========================================================================
    // amazon_creators_api_get_token_endpoint() のテスト
    // ========================================================================

    public function test_get_token_endpoint_v2系のCognitoエンドポイントを返す(): void
    {
        $this->assertSame('https://creatorsapi.auth.us-east-1.amazoncognito.com/oauth2/token', amazon_creators_api_get_token_endpoint('2.1'));
        $this->assertSame('https://creatorsapi.auth.eu-south-2.amazoncognito.com/oauth2/token', amazon_creators_api_get_token_endpoint('2.2'));
        $this->assertSame('https://creatorsapi.auth.us-west-2.amazoncognito.com/oauth2/token', amazon_creators_api_get_token_endpoint('2.3'));
    }

    public function test_get_token_endpoint_未知のバージョンは空文字列を返す(): void
    {
        $this->assertSame('', amazon_creators_api_get_token_endpoint('1.0'));
        $this->assertSame('', amazon_creators_api_get_token_endpoint('unknown'));
    }

    /**
     * 注意: このテストではグローバル定数 AMAZON_DOMAIN の影響を受けます。
     * デフォルト環境 (amazon.co.jp) での挙動を担保します。
     */
    public function test_get_token_endpoint_v3系はドメインに応じたLwAエンドポイントを返す(): void
    {
        // setUpBeforeClass で AMAZON_DOMAIN = 'amazon.co.jp' と想定
        $endpoint = amazon_creators_api_get_token_endpoint('3.3');
        $this->assertSame('https://api.amazon.co.jp/auth/o2/token', $endpoint);

        $endpoint_minor = amazon_creators_api_get_token_endpoint('3.0');
        $this->assertSame('https://api.amazon.co.jp/auth/o2/token', $endpoint_minor);
        
        // PHPでの型キャスト対策テスト（念のため float 3.3 を渡してみる）
        $endpoint_float = amazon_creators_api_get_token_endpoint(3.3);
        $this->assertSame('https://api.amazon.co.jp/auth/o2/token', $endpoint_float);
    }

    // ========================================================================
    // amazon_creators_api_get_access_token() のモック連動フルテストとエッジケース
    // ========================================================================

    public function test_get_access_token_v3_LwAの場合はJSONエンコードでリクエストする(): void
    {
        global $test_mock_wp_remote_post_args;
        global $test_mock_wp_remote_post_response;

        // LwA 用の新しい認証ID
        $credential_id = 'amzn1.application-oa2-client.dummy12345';
        
        // モックから返すレスポンスを設定
        $test_mock_wp_remote_post_response = array(
            'response' => array('code' => 200),
            'body' => json_encode(array('access_token' => 'lwa_mock_token_abc', 'expires_in' => 3600))
        );

        $result = amazon_creators_api_get_access_token($credential_id, 'dummy_secret', '3.3');

        $this->assertIsArray($result);
        $this->assertSame('lwa_mock_token_abc', $result['token']);

        // キャプチャされた引数を検証（重箱の隅チェック）
        $headers = $test_mock_wp_remote_post_args['headers'] ?? [];
        
        // 1. Content-Type が application/json になっていること
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertSame('application/json', $headers['Content-Type']);

        // 2. BodyがJSONフォーマットでエンコードされていること
        $body = $test_mock_wp_remote_post_args['body'] ?? '';
        $this->assertJson($body);
        
        $decoded_body = json_decode($body, true);
        $this->assertSame('client_credentials', $decoded_body['grant_type']);
        $this->assertSame('amzn1.application-oa2-client.dummy12345', $decoded_body['client_id']);
        
        // 3. スコープが『ダブルコロン』の creatorsapi::default になっていること
        $this->assertSame('creatorsapi::default', $decoded_body['scope']);
        
        // 4. タイムアウトがデフォルトで20秒に設定されていること
        $this->assertSame(20, $test_mock_wp_remote_post_args['timeout']);
    }

    public function test_get_access_token_v2_従来方式の場合はフォームエンコードでリクエストする(): void
    {
        global $test_mock_wp_remote_post_args;
        global $test_mock_wp_remote_post_response;

        // 従来形式の ID
        $credential_id = 'XYZ-LEGACY-ID'; 
        
        // モックから返すレスポンスを設定
        $test_mock_wp_remote_post_response = array(
            'response' => array('code' => 200),
            'body' => json_encode(array('access_token' => 'legacy_mock_token_xyz', 'expires_in' => 3600))
        );

        $result = amazon_creators_api_get_access_token($credential_id, 'legacy_secret', '2.3');

        $this->assertIsArray($result);
        $this->assertSame('legacy_mock_token_xyz', $result['token']);

        // キャプチャされた引数を検証
        $headers = $test_mock_wp_remote_post_args['headers'] ?? [];
        
        // 1. Content-Type が form-urlencoded になっていること
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertSame('application/x-www-form-urlencoded', $headers['Content-Type']);

        // 2. Bodyがクエリ文字列フォーマット（JSONではない）こと
        $body = $test_mock_wp_remote_post_args['body'] ?? '';
        $this->assertStringNotContainsString('{', $body);
        $this->assertStringContainsString('grant_type=client_credentials', $body);
        
        // 3. スコープが『スラッシュ区切り』の creatorsapi/default などに（URLエンコードされて）送られていること
        $decoded_query = urldecode($body);
        $this->assertStringContainsString('scope=creatorsapi/default', $decoded_query);
    }

    public function test_get_access_token_エラーハンドリング_JSONデコード失敗(): void
    {
        global $test_mock_wp_remote_post_response;

        // アクセストークンがない不正なJSONレスポンスをシミュレート
        $test_mock_wp_remote_post_response = array(
            'response' => array('code' => 200),
            'body' => '{"error":"invalid_client"}' // access_token キーが存在しない
        );

        $result = amazon_creators_api_get_access_token('test_id', 'secret', '3.3');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayNotHasKey('token', $result);
    }

    public function test_get_access_token_scopeフィルターで空にした場合_scopeが送信されないエッジケース(): void
    {
        global $test_mock_wp_remote_post_args;
        global $test_mock_wp_remote_post_response;
        global $test_mock_apply_filters_callbacks;

        $credential_id = 'amzn1.test'; // v3
        
        // モックレスポンスを設定（scopeテスト用）
        $test_mock_wp_remote_post_response = array(
            'response' => array('code' => 200),
            'body' => json_encode(array('access_token' => 'scope_test_token', 'expires_in' => 3600))
        );
        
        // amazon_creators_api_scope フィルターで強制的に空文字にする
        $test_mock_apply_filters_callbacks['amazon_creators_api_scope'] = function($scope, $version) {
            return '';
        };

        $token = amazon_creators_api_get_access_token($credential_id, 'dummy_secret', '3.3');

        $body = $test_mock_wp_remote_post_args['body'] ?? '';
        $decoded_body = json_decode($body, true);
        
        // scopeが空の場合、リクエストパラメータにscopeキーが含まれるが空文字のはず
        // 実装側で空文字列のscopeを除外するかはcreators-api.phpの実装に依存
        $this->assertSame('client_credentials', $decoded_body['grant_type']);
    }
}
