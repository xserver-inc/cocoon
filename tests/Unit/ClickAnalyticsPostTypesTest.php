<?php
/**
 * クリック解析の対象投稿タイプと公開状態の回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/settings-func.php';

class ClickAnalyticsPostTypesTest extends TestCase
{
    private bool $hadMockPost = false;
    private mixed $previousMockPost = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadMockPost = array_key_exists('test_mock_get_post', $GLOBALS);
        $this->previousMockPost = $GLOBALS['test_mock_get_post'] ?? null;
        Functions\when('get_post_types')->justReturn(array('click_public', 'click_queryable', 'click_hidden'));
        Functions\when('is_post_type_viewable')->alias(static function ($type) {
            return in_array($type, array('post', 'page', 'click_public', 'click_queryable'), true);
        });
    }

    protected function tearDown(): void
    {
        if ($this->hadMockPost) {
            $GLOBALS['test_mock_get_post'] = $this->previousMockPost;
        } else {
            unset($GLOBALS['test_mock_get_post']);
        }
        parent::tearDown();
    }

    /**
     * 標準投稿と閲覧可能なカスタム投稿だけを対象とする判定の確認
     */
    public function testTrackableTypesIncludeViewableCustomPostsWithoutInternalTypes(): void
    {
        $this->assertSame(array('post', 'page', 'click_public', 'click_queryable'), cocoon_click_trackable_post_types());
    }

    /**
     * 公開済み投稿の種類と未公開状態の組み合わせによる対象判定の確認
     */
    public function testOnlyPublishedTrackablePostsAreAccepted(): void
    {
        $cases = array(
            array('post', 'publish', true),
            array('page', 'publish', true),
            array('click_public', 'publish', true),
            array('click_queryable', 'publish', true),
            array('click_hidden', 'publish', false),
            array('attachment', 'publish', false),
            array('wp_block', 'publish', false),
            array('revision', 'publish', false),
            array('click_public', 'draft', false),
            array('click_public', 'private', false),
            array('click_public', 'future', false),
        );
        foreach ($cases as [$type, $status, $expected]) {
            $GLOBALS['test_mock_get_post'] = (object) array('ID' => 42, 'post_type' => $type, 'post_status' => $status);
            $this->assertSame($expected, cocoon_click_is_trackable_post(42), $type . ':' . $status);
            $this->assertSame($expected, cocoon_click_is_trackable_post($GLOBALS['test_mock_get_post']), $type . ':' . $status);
        }
    }

    /**
     * 未指定IDから現在の投稿への意図しないフォールバックの防止確認
     */
    public function testEmptyPostIdentifiersDoNotFallBackToTheCurrentPost(): void
    {
        $GLOBALS['test_mock_get_post'] = (object) array('ID' => 42, 'post_type' => 'click_public', 'post_status' => 'publish');
        foreach (array(null, false, 0, '0', '') as $empty) {
            $this->assertFalse(cocoon_click_is_trackable_post($empty));
        }
        unset($GLOBALS['test_mock_get_post']);
        $this->assertFalse(cocoon_click_is_trackable_post(999));
    }
}
