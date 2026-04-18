<?php
/**
 * sns-share.php のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

class SnsShareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // sns-share.php の読み込み
        $lib_dir = dirname(__DIR__, 2) . '/lib';
        if (file_exists($lib_dir . '/sns-share.php')) {
            require_once $lib_dir . '/sns-share.php';
        }
        
        if (!defined('SS_MOBILE')) define('SS_MOBILE', 'mobile');
        if (!defined('SS_BOTTOM')) define('SS_BOTTOM', 'bottom');
        if (!defined('SS_TOP'))    define('SS_TOP', 'top');
    }

    public static function provideShareUrls(): array
    {
        return [
            ['get_twitter_share_url', 'https://x.com/intent/tweet?text=Test+Title&amp;url=https%3A%2F%2Fexample.com%2Ftest'],
            ['get_mastodon_share_url', '//www.addtoany.com/add_to/mastodon?linkurl=https%3A%2F%2Fexample.com%2Ftest&linkname=Test+Title'],
            ['get_bluesky_share_url', '//bsky.app/intent/compose?text=Test+Title https%3A%2F%2Fexample.com%2Ftest'],
            ['get_misskey_share_url', '//misskey-hub.net/share/?text=Test+Title&url=https%3A%2F%2Fexample.com%2Ftest&visibility=public&localOnly=0'],
            ['get_facebook_share_url', '//www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fexample.com%2Ftest&amp;t=Test+Title'],
            ['get_threads_share_url', 'https://www.threads.net/intent/post?text=Test+Title+https%3A%2F%2Fexample.com%2Ftest'],
            ['get_hatebu_share_url', '//b.hatena.ne.jp/entry/s/example.com/test'],
            ['get_google_plus_share_url', '//plus.google.com/share?url=https%3A%2F%2Fexample.com%2Ftest'],
            ['get_pocket_share_url', '//getpocket.com/edit?url=https://example.com/test'],
            ['get_line_share_url', '//timeline.line.me/social-plugin/share?url=https%3A%2F%2Fexample.com%2Ftest'],
            ['get_pinterest_share_url', '//www.pinterest.com/pin/create/button/?url=https%3A%2F%2Fexample.com%2Ftest'],
            ['get_linkedin_share_url', '//www.linkedin.com/shareArticle?mini=true&url=https%3A%2F%2Fexample.com%2Ftest'],
            ['get_copy_share_url', 'javascript:void(0)'], // デフォルト非AMP用
        ];
    }

    #[DataProvider('provideShareUrls')]
    public function test_各種SNSのシェアURLが正しく生成されるか(string $func, string $expected_url): void
    {
        // 共通のモック対象関数
        Functions\when('get_share_page_title')->justReturn('Test Title');
        Functions\when('get_share_page_url')->justReturn('https://example.com/test');
        
        // 分岐や条件により呼ばれる可能性のある関数を安全のためにモック
        Functions\when('get_the_author_meta')->justReturn(''); 
        Functions\when('get_twitter_hash_tag')->justReturn('');
        Functions\when('get_twitter_via_param')->justReturn('');
        Functions\when('get_twitter_related_param')->justReturn('');

        Functions\when('get_the_posts_author_id')->justReturn(1);

        $this->assertTrue(function_exists($func), "関数 {$func} が存在しません");
        $this->assertSame($expected_url, $func());
    }

    public function test_get_threads_count_が空文字を返すか(): void
    {
        $this->assertSame('', get_threads_count());
        $this->assertSame('', get_threads_count('https://example.com'));
    }

    public function test_is_threads_share_button_visible_の条件分岐が正しいか(): void
    {
        // apply_filters はテストブートストラップ側で定義済みなのでモック不要

        // モバイル要求の時は is_xxx_share_button_visible の戻り値に関係なく true
        Functions\when('is_bottom_threads_share_button_visible')->justReturn(false);
        Functions\when('is_top_threads_share_button_visible')->justReturn(false);
        $this->assertTrue(is_threads_share_button_visible(SS_MOBILE));

        // SS_BOTTOMが有効な場合
        Functions\when('is_bottom_threads_share_button_visible')->justReturn(true);
        $this->assertTrue(is_threads_share_button_visible(SS_BOTTOM));

        // 両方マッチしない場合は false
        Functions\when('is_bottom_threads_share_button_visible')->justReturn(false);
        $this->assertFalse(is_threads_share_button_visible(SS_BOTTOM));
    }
}
