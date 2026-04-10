<?php
/**
 * キャンペーンブロックのユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class CampaignBlockTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        if (!defined('DAY_IN_SECONDS')) {
            define('DAY_IN_SECONDS', 86400);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 2) . '/blocks/src/block/campaign/index.php';
    }

    /**
     * 期間内の場合にコンテンツが表示されることをテスト
     */
    public function test_render_block_cocoon_campaign_期間内の場合コンテンツを表示する()
    {
        $tz = new \DateTimeZone('Asia/Tokyo');
        $now = new \DateTimeImmutable('2026-04-10 15:00:00', $tz);

        Functions\when('wp_timezone')->justReturn($tz);
        Functions\when('current_datetime')->justReturn($now);

        $attributes = [
            'from' => '2026/04/01 00:00:00',
            'to'   => '2026/04/30 23:59:59',
        ];
        $content = 'キャンペーン中';

        $result = render_block_cocoon_campaign($attributes, $content);
        $this->assertSame('キャンペーン中', $result);
    }

    /**
     * 開始時刻と現在時刻が全く同じ場合（境界値）に表示されることをテスト
     */
    public function test_render_block_cocoon_campaign_開始時刻ちょうどの場合は表示する()
    {
        $tz = new \DateTimeZone('Asia/Tokyo');
        $now = new \DateTimeImmutable('2026-04-10 15:00:00', $tz);

        Functions\when('wp_timezone')->justReturn($tz);
        Functions\when('current_datetime')->justReturn($now);

        $attributes = [
            'from' => '2026/04/10 15:00:00', // 同じ時刻
            'to'   => '2026/04/30 23:59:59',
        ];
        $content = '開始直後';

        $result = render_block_cocoon_campaign($attributes, $content);
        $this->assertSame('開始直後', $result);
    }

    /**
     * 期間外（開始前）の場合に空文字が返されることをテスト
     */
    public function test_render_block_cocoon_campaign_期間外の場合は空文字を返す()
    {
        $tz = new \DateTimeZone('Asia/Tokyo');
        $now = new \DateTimeImmutable('2026-03-31 23:59:59', $tz);

        Functions\when('wp_timezone')->justReturn($tz);
        Functions\when('current_datetime')->justReturn($now);

        $attributes = [
            'from' => '2026/04/01 00:00:00',
            'to'   => '2026/04/30 23:59:59',
        ];
        $content = 'キャンペーン中';

        $result = render_block_cocoon_campaign($attributes, $content);
        $this->assertSame('', $result);
    }
}
