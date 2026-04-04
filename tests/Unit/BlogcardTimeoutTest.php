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
            '!(is_external_blogcard_refresh_mode() && is_user_administrator() && !is_admin() && !is_rest())',
            $file,
            'REST APIや管理画面での強制キャッシュ更新を防止するガードが必要です（エディター保存時の504 Time-out対策）'
        );
    }

    public function test_blogcard_out_画像ダウンロードのタイムアウトが5秒に設定されている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // デフォルトの300秒だと画像サーバーが落ちている場合にハングアップするため
        $this->assertStringContainsString(
            'download_url($image, 5)',
            $file,
            '画像の単一処理でハングアップすることを防ぐため、download_urlのタイムアウトを5秒に設定する必要があります'
        );
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

    public function test_open_graph_HTTPリクエストエラー時にフォールバックがブロックされる(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/open-graph.php');

        // フォールバック前に WP_Error 全体を弾いているか（http_request_failed だけでなく全件）
        $this->assertStringContainsString(
            'if ( !is_wp_error( $res ) ) {',
            $file,
            'WP_Errorが発生した場合（接続タイムアウトやDNS拒否など）に、さらにHTTPリクエストを行うフォールバックを停止するガードが必要です（504 Time-outの多重加算回避）'
        );

        // wp_filesystem_get_contents がそのガード内にあることの確認
        $this->assertMatchesRegularExpression(
            '/if \(\s*!is_wp_error\(\s*\$res\s*\)\s*\)\s*\{[\s\S]*?wp_filesystem_get_contents/',
            $file,
            'wp_filesystem_get_contents の前に !is_wp_error($res) ガードが正しく囲むように配置されている必要があります'
        );
    }
}
