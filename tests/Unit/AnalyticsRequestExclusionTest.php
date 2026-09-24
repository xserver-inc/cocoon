<?php
/**
 * アクセス解析のボット・自動操作・先読み除外の回帰テスト
 */
namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-access/request-exclusion.php';

class AnalyticsRequestExclusionTest extends TestCase
{
    private array $server;
    private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = $_SERVER;
        $_SERVER = array('HTTP_USER_AGENT' => self::BROWSER, 'REMOTE_ADDR' => '192.0.2.1');
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        parent::tearDown();
    }

    /**
     * 既知のボット名と自動操作ツールの表記揺れに対する除外の確認
     */
    public function testKnownBotsAndAutomationAreExcluded(): void
    {
        foreach (array(
            'Googlebot/2.1', 'GoogleOther', 'Mediapartners-Google', 'bingbot/2.0',
            'GPTBot/1.0', 'ChatGPT-User/1.0', 'ClaudeBot/1.0', 'AhrefsBot/7.0',
            'SemrushBot/7~bl', 'facebookexternalhit/1.1', 'Twitterbot/1.0',
            'YandexBot/3.0', 'Applebot/0.1', 'HeadlessChrome/154.0.0.0',
            'PhantomJS/2.1.1', 'Puppeteer', 'Playwright', 'Selenium',
        ) as $agent) {
            foreach (array($agent, self::BROWSER . ' ' . $agent, strtolower($agent)) as $value) {
                $this->assertTrue(cocoon_analytics_is_automated_user_agent($value), $value);
                $_SERVER['HTTP_USER_AGENT'] = $value;
                $this->assertTrue(cocoon_analytics_request_is_excluded(), $value);
            }
        }
    }

    /**
     * 通常ブラウザー・アプリ内ブラウザー・端末名の誤除外の防止
     */
    public function testOrdinaryAndInAppBrowsersAreNotExcluded(): void
    {
        foreach (array(
            self::BROWSER,
            self::BROWSER . ' Edg/154.0.0.0',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/19.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 19_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/19.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 16; Pixel 10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 15; CUBOT X90) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36',
            self::BROWSER . ' Line/16.0.0',
            self::BROWSER . ' [FBAN/FB4A;FBAV/500.0.0.0;]',
            self::BROWSER . ' Instagram 400.0.0.0',
            'FutureBrowser/1.0',
        ) as $agent) {
            $this->assertFalse(cocoon_analytics_is_automated_user_agent($agent), $agent);
            $_SERVER['HTTP_USER_AGENT'] = $agent;
            $this->assertFalse(cocoon_analytics_request_is_excluded(), $agent);
        }
    }

    /**
     * 明示的な先読みヘッダーと通常のナビゲーションの識別
     */
    public function testPrefetchHeadersAreExcludedButNormalNavigationIsCountable(): void
    {
        foreach (array('HTTP_PURPOSE', 'HTTP_SEC_PURPOSE', 'HTTP_X_MOZ') as $header) {
            foreach (array('prefetch', 'PREFETCH', ' prefetch ', 'prefetch;prerender', 'prerender', 'navigate, prefetch') as $value) {
                $_SERVER[$header] = $value;
                $this->assertTrue(cocoon_analytics_request_is_excluded(), $header . ': ' . $value);
            }
            foreach (array('', 'navigate', 'preview', 'not-prefetch', array('prefetch')) as $value) {
                $_SERVER[$header] = $value;
                $this->assertFalse(cocoon_analytics_request_is_excluded());
            }
            unset($_SERVER[$header]);
        }
        $this->assertFalse(cocoon_analytics_request_is_excluded());
    }

    /**
     * 欠損・不正な型・長すぎるUser-Agentの安全な除外
     */
    public function testMissingAndMalformedUserAgentsAreExcludedWithoutWarnings(): void
    {
        foreach (array(null, '', '   ', false, 42, array('Chrome'), new \stdClass(), str_repeat('a', 4097)) as $value) {
            $_SERVER['HTTP_USER_AGENT'] = $value;
            $this->assertTrue(cocoon_analytics_request_is_excluded());
        }
        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertTrue(cocoon_analytics_request_is_excluded());
    }

    /**
     * 転送ヘッダーに依存しない接続元の確認とIPv6への対応
     */
    public function testRemoteAddressIsRequiredWithoutTrustingForwardedHeaders(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '203.0.113.1';
        unset($_SERVER['REMOTE_ADDR']);
        $this->assertTrue(cocoon_analytics_request_is_excluded());
        $_SERVER['REMOTE_ADDR'] = array('192.0.2.1');
        $this->assertTrue(cocoon_analytics_request_is_excluded());
        $_SERVER['REMOTE_ADDR'] = '2001:db8::1';
        $this->assertFalse(cocoon_analytics_request_is_excluded());
    }

    /**
     * 従来の除外対象と共有ボット判定の挙動の維持
     */
    public function testExistingExclusionsAndSharedFunctionBehaviorArePreserved(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'adsence-Google';
        $this->assertTrue(cocoon_analytics_request_is_excluded());
        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertFalse(is_useragent_robot());
    }

    /**
     * 同梱定義の構文・出典・ライセンスと結合可能性の検証
     */
    public function testBundledPatternsAreValidAndHavePinnedAttribution(): void
    {
        $path = dirname(__DIR__, 2) . '/lib/page-access/';
        $data = json_decode(file_get_contents($path . 'bot-patterns.json'), true, 512, JSON_THROW_ON_ERROR);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{40}$/', $data['revision']);
        $this->assertSame('LGPL-3.0-or-later', $data['license']);
        $this->assertGreaterThan(800, count($data['patterns']));
        $this->assertFileExists($path . 'bot-patterns.LICENSE');
        $this->assertFileExists($path . 'bot-patterns.COPYING');
        $compiled = cocoon_analytics_compile_bot_patterns(file_get_contents($path . 'bot-patterns.json'));
        $this->assertCount((int) ceil(count($data['patterns']) / 40), $compiled);
        foreach ($compiled as $regex) {
            $this->assertSame(0, preg_match($regex, self::BROWSER), $regex);
        }
        foreach ($data['patterns'] as $pattern) {
            // 定義更新時の、結合で意味が変わる後方参照や条件分岐の検出
            $this->assertDoesNotMatchRegularExpression('/\\\\[1-9gk]|\\(\\?\\(/', $pattern);
            $regex = '/(?:^|[^A-Z0-9_-]|[^A-Z0-9-]_|sprd-|MZ-)(?:' . str_replace('/', '\\/', $pattern) . ')/i';
            $this->assertNotFalse(preg_match($regex, self::BROWSER), $pattern);
        }
    }

    /**
     * 読み込み失敗・不正JSON・不正な定義構造でも処理が継続可能なことの確認
     */
    public function testMissingOrMalformedDefinitionsFallBackWithoutWarnings(): void
    {
        foreach (array(false, null, '', '{', 'null', '42', '"string"', '{}',
            '{"patterns":null}', '{"patterns":"bot"}', '{"patterns":{}}',
            '{"patterns":[null]}', '{"patterns":[42]}', '{"patterns":[[]]}',
            '{"patterns":[" "]}') as $json) {
            $this->assertSame(array(), cocoon_analytics_compile_bot_patterns($json));
        }
    }

    /**
     * 壊れた定義や全件一致の定義を除外しつつ正常な定義を維持することの確認
     */
    public function testInvalidRegexChunksDoNotDisableHealthyChunks(): void
    {
        foreach (array('[', '(?:)') as $invalid) {
            $data = array('patterns' => array_merge(array('ReviewBot'), array_fill(0, 40, $invalid)));
            $compiled = cocoon_analytics_compile_bot_patterns(json_encode($data));
            $this->assertCount(1, $compiled);
            $this->assertSame(1, preg_match($compiled[0], 'ReviewBot/1.0'));
            $this->assertSame(0, preg_match($compiled[0], self::BROWSER));
        }
    }
}
