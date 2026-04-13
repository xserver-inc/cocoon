<?php
/**
 * sns-follow.php のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class SnsFollowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // sns-follow.php の読み込み
        $lib_dir = dirname(__DIR__, 2) . '/lib';
        if (file_exists($lib_dir . '/sns-follow.php')) {
            require_once $lib_dir . '/sns-follow.php';
        }
    }

    public function test_get_the_author_threads_url_が_esc_url_を経由してURLを返すか(): void
    {
        Functions\expect('get_the_posts_author_id')
            ->once()
            ->andReturn(10);

        Functions\expect('get_the_author_meta')
            ->once()
            ->with('threads_url', 10)
            ->andReturn('https://www.threads.net/@test_user');

        $url = get_the_author_threads_url();
        $this->assertSame('https://www.threads.net/@test_user', $url);
    }

    public function test_その他のURL取得関数も_esc_url_を通過しているか(): void
    {
        Functions\when('get_the_posts_author_id')->justReturn(5);

        // キーに応じた戻り値を返すようにモック
        Functions\expect('get_the_author_meta')
            ->withAnyArgs()
            ->andReturnUsing(function($key, $id) {
                if ($key === 'bluesky_url') return 'https://bsky.app/profile/test';
                if ($key === 'misskey_url') return 'https://misskey.io/@test';
                if ($key === 'facebook_url') return 'https://facebook.com/test';
                return '';
            });

        $this->assertSame('https://bsky.app/profile/test', get_the_author_bluesky_url());
        $this->assertSame('https://misskey.io/@test', get_the_author_misskey_url());
        $this->assertSame('https://facebook.com/test', get_the_author_facebook_url());
    }
}
