<?php

namespace Cocoon\Tests\Integration;

/**
 * WP External Linksの公開フックを経由したREST応答とリンク加工の回帰テスト
 */
class WpelRestCompatibilityIntegrationTest extends IntegrationTestCase
{
    private static $originalSkipInstall = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // 別プロセスの再起動による共有テストDBの再初期化の防止
        self::$originalSkipInstall = getenv('WP_TESTS_SKIP_INSTALL');
        putenv('WP_TESTS_SKIP_INSTALL=1');
    }

    public static function tearDownAfterClass(): void
    {
        try {
            parent::tearDownAfterClass();
        } finally {
            // 後続テストへ影響を残さない元の環境変数の復元
            putenv(false === self::$originalSkipInstall
                ? 'WP_TESTS_SKIP_INSTALL'
                : 'WP_TESTS_SKIP_INSTALL=' . self::$originalSkipInstall);
        }
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRestFinalOutputIsPreservedAndHtmlLinksAreStillProcessed(): void
    {
        $this->assertSame(PHP_INT_MAX, has_filter('wpel_apply_settings', 'cocoon_wpel_apply_settings'));

        // プラグイン側による通常優先度での適用可否の再判定
        add_filter('wpel_apply_settings', '__return_true', 10);

        $scan_count = 0;
        $scan = static function ($content) use (&$scan_count) {
            if (false === apply_filters('wpel_apply_settings', true)) {
                return $content;
            }
            $scan_count++;
            return str_replace('External link', 'Processed external link', $content);
        };

        // WP External Linksと同じ最終出力・本文・コメント・ウィジェットの入口
        foreach (array('final_output', 'the_content', 'comment_text', 'widget_output') as $hook) {
            add_filter($hook, $scan, 10000000000);
        }

        $html = '<p><a href="https://example.org/">External link</a></p>';
        $this->assertStringContainsString('Processed external link', apply_filters('final_output', $html));
        $this->assertStringContainsString('Processed external link', apply_filters('the_content', $html));
        $this->assertStringContainsString('Processed external link', apply_filters('comment_text', $html));
        $this->assertStringContainsString('Processed external link', apply_filters('widget_output', $html));

        // 既存の適用除外設定の維持
        add_filter('wpel_apply_settings', '__return_false', PHP_INT_MAX - 1);
        $this->assertSame($html, apply_filters('final_output', $html));
        remove_filter('wpel_apply_settings', '__return_false', PHP_INT_MAX - 1);

        define('REST_REQUEST', true);
        $json = wp_json_encode(array('content' => array('rendered' => str_repeat($html, 1600))));
        $before = $scan_count;

        // 空応答・空配列・日本語エラー応答・JSONPの維持
        foreach (array(
            '',
            'null',
            '[]',
            wp_json_encode(array('code' => 'rest_error', 'message' => $html . '日本語🔗', 'data' => array('status' => 500)), JSON_UNESCAPED_UNICODE),
            'callback(' . $json . ');',
        ) as $response) {
            $this->assertSame($response, apply_filters('final_output', $response));
        }

        $other_filter_called = false;

        // 同じ最終出力フックを使う別処理の継続確認
        add_filter('final_output', static function ($content) use (&$other_filter_called) {
            $other_filter_called = true;
            return $content;
        }, PHP_INT_MAX);

        $this->assertSame($json, apply_filters('final_output', $json));
        $this->assertSame($before, $scan_count);
        $this->assertTrue($other_filter_called);

        // 途中フラッシュを含む出力バッファ経由のJSON保護
        $buffer_level = ob_get_level();
        ob_start();
        try {
            ob_start(static function ($content) {
                return apply_filters('final_output', $content);
            });
            $split = intdiv(strlen($json), 2);
            echo substr($json, 0, $split);
            ob_flush();
            echo substr($json, $split);
            ob_end_flush();
            $buffered_json = ob_get_clean();
        } finally {
            // 失敗時にも開始前のバッファ段数へ戻すための後始末
            while (ob_get_level() > $buffer_level) {
                ob_end_clean();
            }
        }
        $this->assertSame($buffer_level, ob_get_level());
        $this->assertSame($json, $buffered_json);
        $this->assertJson($buffered_json);

        // RESTでも完成済み応答の外で行う本文・コメント・ウィジェットの加工の維持
        $this->assertStringContainsString('Processed external link', apply_filters('the_content', $html));
        $this->assertStringContainsString('Processed external link', apply_filters('comment_text', $html));
        $this->assertStringContainsString('Processed external link', apply_filters('widget_output', $html));
    }
}
