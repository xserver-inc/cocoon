<?php
/**
 * Creators API関連のユニットテスト
 *
 * エッジケースを含むバージョン判定や、リージョンごとのLwAトークンエンドポイント振り分けをテストします。
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

        // 依存関数スタブ
        if (!function_exists('home_url')) {
            function home_url() { return 'https://example.com'; }
        }
        if (!function_exists('get_amazon_creators_api_credential_id')) {
            function get_amazon_creators_api_credential_id() { return 'test_id'; }
        }
        if (!function_exists('get_amazon_creators_api_secret')) {
            function get_amazon_creators_api_secret() { return 'test_secret'; }
        }

        // テスト対象ファイルを読み込み
        require_once dirname(__DIR__, 2) . '/lib/creators-api.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
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
}
