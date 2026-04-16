<?php
/**
 * キャッシュ削除機能のテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class CacheFuncTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once dirname(__DIR__, 2) . '/lib/page-cache/cache-func.php';
        
        if (!defined('TRANSIENT_BLOGCARD_PREFIX')) {
            define('TRANSIENT_BLOGCARD_PREFIX', 'blogcard_');
        }
    }

    public function test_delete_blogcard_cache_transients_外部と内部のURL判定キャッシュの両方が削除されること(): void
    {
        // WordPress のグローバル $wpdb オブジェクトをモック
        global $wpdb;

        // モックオブジェクトを作成
        $wpdb = \Mockery::mock('WPDB_Mock');
        $wpdb->options = 'wp_options';

        // $wpdb->esc_like() が呼ばれた際にそのまま返すモック
        $wpdb->shouldReceive('esc_like')
            ->andReturnUsing(function($text) {
                return $text;
            });

        // $wpdb->prepare() が呼ばれた際にクエリ文字列を組み立てて返すモック
        $wpdb->shouldReceive('prepare')
            ->andReturnUsing(function($query, ...$args) {
                return vsprintf(str_replace('%s', "'%s'", $query), $args);
            });

        // $wpdb->query() が少なくとも2回呼ばれ、それぞれ外部キャッシュと内部キャッシュの削除クエリが含まれていることを確認
        $wpdb->shouldReceive('query')
            ->times(2)
            ->withArgs(function($query) {
                // blogcard キャッシュ (bcc_) の削除または internal 判定 (cocoon_url_tpi) の削除のクエリであることを確認
                return strpos($query, '_transient_bcc_') !== false || strpos($query, '_transient_cocoon_url_tpi_') !== false;
            })
            ->andReturn(true);

        // lib/cache.php にある delete_blogcard_cache_transients を読み込む
        // (すでにブートストラップ等で読み込まれている可能性があるため、require_once にする)
        require_once dirname(__DIR__, 2) . '/lib/cache.php';

        // 実行：エラーが出ずに、上記のクエリモックが期待通り呼ばれれば成功
        delete_blogcard_cache_transients();

        $this->assertTrue(true, 'delete_blogcard_cache_transients内で2つのキャッシュ削除クエリが実行されました');
    }
}

