<?php
/**
 * クリックマップ記事候補APIの入力・権限テスト
 */
namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-access/analytics/map-post-picker.php';

class MapPostPickerTest extends TestCase
{
    public function testCandidateApiRejectsUsersWithoutPermission(): void
    {
        Functions\expect('wp_send_json_error')->once()->with(array('message' => 'forbidden'), 403)->andThrow(new \RuntimeException('forbidden'));
        Functions\expect('check_ajax_referer')->never();
        $this->expectExceptionMessage('forbidden');
        cocoon_analytics_ajax_map_post_candidates();
    }

    public function testCandidateApiChecksNonceBeforeReadingCandidates(): void
    {
        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('check_ajax_referer')->once()->with('cocoon_analytics_post_picker', 'nonce')->andThrow(new \RuntimeException('invalid nonce'));
        $this->expectExceptionMessage('invalid nonce');
        cocoon_analytics_ajax_map_post_candidates();
    }

    public function testCandidateApiRejectsMissingOrArrayDates(): void
    {
        Functions\when('current_user_can')->justReturn(true);
        Functions\when('check_ajax_referer')->justReturn(1);
        Functions\when('wp_unslash')->returnArg();
        Functions\expect('wp_send_json_error')->once()->with(array('message' => 'invalid_period'), 400)->andThrow(new \RuntimeException('invalid period'));
        $previous = $_GET;
        $_GET = array('period' => 'custom', 'from' => array('2026-09-01'), 'to' => '2026-09-22');
        $this->expectExceptionMessage('invalid period');
        try {
            cocoon_analytics_ajax_map_post_candidates();
        } finally {
            $_GET = $previous;
        }
    }
}
