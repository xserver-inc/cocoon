<?php
/**
 * 504 Gateway Time-out 防止のユニットテスト
 *
 * blogcard-out.php および open-graph.php における
 * タイムアウト対策（キャッシュバックエンド保護、WP_Errorフォールバック停止、
 * 画像ダウンロードタイムアウト緩和、$ogp初期化）が確実に実装されていることを検証します。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class BlogcardTimeoutTest extends TestCase
{
    // ========================================================================
    // blogcard-out.php のタイムアウト対策検証
    // ========================================================================

    public function test_blogcard_out_キャッシュ更新モードがフロントと非RESTAPIに制限されている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // ブロックエディタの保存中や管理画面で外部への同期通信が走るのを防ぐガード
        $this->assertStringContainsString(
            '!($is_refresh_mode && $is_user_admin && !is_admin() && !$is_rest_req)',
            $file,
            'REST APIや管理画面での強制キャッシュ更新を防止するガードが必要です（エディター保存時の504 Time-out対策）'
        );
    }

    public function test_blogcard_out_画像ダウンロードのタイムアウトが5秒に設定されている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // デフォルトの300秒だと画像サーバーが落ちている場合にハングアップするため
        $this->assertStringContainsString("'timeout' => 5", $file);
        $this->assertStringContainsString("'limit_response_size' => 3 * MB_IN_BYTES + 1", $file);
    }

    public function test_blogcard_out_ogp変数が明示的にnull初期化されている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        $this->assertStringContainsString(
            '$ogp = null;',
            $file,
            'PHP 8.0+ での E_WARNING（Undefined variable）を防止するため、キャッシュ取得分岐前に $ogp 変数を初期化する必要があります'
        );
    }

    // ========================================================================
    // open-graph.php のタイムアウト連鎖対策検証
    // ========================================================================

    /**
     * OGP取得が安全なHTTP関数だけを使う確認
     */
    public function test_open_graph_安全なHTTP関数だけで取得する(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/open-graph.php');

        $this->assertStringContainsString(
            'wp_safe_remote_get( $URI, $args )',
            $file,
            'OGP取得に安全なWordPress HTTP関数が必要です'
        );

        $this->assertStringNotContainsString('get_http_content($URI)', $file);
        $this->assertStringNotContainsString('file_get_contents($URI)', $file);
    }
}
