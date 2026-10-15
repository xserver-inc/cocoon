<?php
/**
 * カテゴリーラベルと前後記事ナビで共有するカテゴリー選択のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class CategoryForLabelTest extends TestCase
{
    private bool $hadFilterCallbacks = false;
    private mixed $originalFilterCallbacks = null;
    private bool $hadCurrentPostId = false;
    private mixed $originalCurrentPostId = null;
    private bool $hadInCategoryCallback = false;
    private mixed $originalInCategoryCallback = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/additional-classes.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用フィルターと表示中の投稿IDの退避
        $this->hadFilterCallbacks = array_key_exists('test_mock_apply_filters_callbacks', $GLOBALS);
        $this->originalFilterCallbacks = $GLOBALS['test_mock_apply_filters_callbacks'] ?? null;
        $this->hadCurrentPostId = array_key_exists('test_mock_get_the_id', $GLOBALS);
        $this->originalCurrentPostId = $GLOBALS['test_mock_get_the_id'] ?? null;
        $this->hadInCategoryCallback = array_key_exists('test_mock_in_category_callback', $GLOBALS);
        $this->originalInCategoryCallback = $GLOBALS['test_mock_in_category_callback'] ?? null;
        $GLOBALS['test_mock_apply_filters_callbacks'] = [];
        $GLOBALS['test_mock_get_the_id'] = 999;
        unset($GLOBALS['test_mock_in_category_callback']);

        Functions\when('get_the_page_main_category')->justReturn(null);
    }

    protected function tearDown(): void
    {
        // 各テストで差し替えたグローバル値の復元
        foreach ([
            'test_mock_apply_filters_callbacks' => [$this->hadFilterCallbacks, $this->originalFilterCallbacks],
            'test_mock_get_the_id' => [$this->hadCurrentPostId, $this->originalCurrentPostId],
            'test_mock_in_category_callback' => [$this->hadInCategoryCallback, $this->originalInCategoryCallback],
        ] as $name => [$hadValue, $originalValue]) {
            if ($hadValue) {
                $GLOBALS[$name] = $originalValue;
            } else {
                unset($GLOBALS[$name]);
            }
        }

        parent::tearDown();
    }

    public function test_メインカテゴリー未指定なら取得順の先頭カテゴリーを選ぶ(): void
    {
        $first = (object) ['cat_ID' => 7, 'cat_name' => '先頭カテゴリー'];
        $second = (object) ['cat_ID' => 8, 'cat_name' => '2番目カテゴリー'];
        Functions\expect('get_the_category')->once()->with(101)->andReturn([$first, $second]);
        Functions\expect('get_category')->never();

        $this->assertSame($first, get_category_for_label(101));
    }

    public function test_所属するメインカテゴリー指定があれば先頭より優先する(): void
    {
        $first = (object) ['cat_ID' => 7, 'cat_name' => '先頭カテゴリー'];
        $main = (object) ['cat_ID' => 8, 'cat_name' => 'メインカテゴリー'];
        Functions\expect('get_the_category')->once()->with(101)->andReturn([$first, $main]);
        Functions\when('get_the_page_main_category')->alias(function ($postId) {
            $this->assertSame(101, $postId);
            return 8;
        });
        $GLOBALS['test_mock_in_category_callback'] = function ($categoryId, $postId) {
            $this->assertSame([8, 101], [$categoryId, $postId]);
            return true;
        };
        Functions\expect('get_category')->once()->with(8)->andReturn($main);

        $this->assertSame($main, get_category_for_label(101));
    }

    public function test_所属外のメインカテゴリー指定は採用しない(): void
    {
        $first = (object) ['cat_ID' => 7, 'cat_name' => '先頭カテゴリー'];
        Functions\expect('get_the_category')->once()->with(101)->andReturn([$first]);
        Functions\when('get_the_page_main_category')->alias(function ($postId) {
            $this->assertSame(101, $postId);
            return 88;
        });
        $inCategoryCalls = [];
        $GLOBALS['test_mock_in_category_callback'] = static function ($categoryId, $postId) use (&$inCategoryCalls) {
            $inCategoryCalls[] = [$categoryId, $postId];
            return false;
        };
        Functions\expect('get_category')->never();

        $this->assertSame($first, get_category_for_label(101));
        $this->assertSame([[88, 101]], $inCategoryCalls);
    }

    public function test_フィルターはメインカテゴリー選択後に対象投稿IDと全カテゴリーを受け取る(): void
    {
        $first = (object) ['cat_ID' => 7, 'cat_name' => '先頭カテゴリー'];
        $main = (object) ['cat_ID' => 8, 'cat_name' => 'メインカテゴリー'];
        $filtered = (object) ['cat_ID' => 55, 'cat_name' => 'フィルター指定'];
        $categories = [$first, $main];
        Functions\expect('get_the_category')->once()->with(101)->andReturn($categories);
        Functions\when('get_the_page_main_category')->alias(function ($postId) {
            $this->assertSame(101, $postId);
            return 8;
        });
        $GLOBALS['test_mock_in_category_callback'] = function ($categoryId, $postId) {
            $this->assertSame([8, 101], [$categoryId, $postId]);
            return true;
        };
        Functions\expect('get_category')->once()->with(8)->andReturn($main);
        $received = null;
        $GLOBALS['test_mock_apply_filters_callbacks']['get_the_nolink_category'] = static function ($category, $allCategories, $postId) use ($filtered, &$received) {
            $received = [$category, $allCategories, $postId];
            return $filtered;
        };

        $this->assertSame($filtered, get_category_for_label(101));
        $this->assertSame([$main, $categories, 101], $received);
    }

    public function test_投稿ID省略時は従来どおりカテゴリーを取得しフィルターへ現在投稿IDを渡す(): void
    {
        $category = (object) ['cat_ID' => 7, 'cat_name' => '現在投稿のカテゴリー'];
        Functions\expect('get_the_category')->once()->withNoArgs()->andReturn([$category]);
        $receivedPostId = null;
        $GLOBALS['test_mock_apply_filters_callbacks']['get_the_nolink_category'] = static function ($selected, $categories, $postId) use (&$receivedPostId) {
            $receivedPostId = $postId;
            return $selected;
        };

        $this->assertSame($category, get_category_for_label());
        $this->assertSame(999, $receivedPostId);
    }

    public function test_カテゴリーがなくてもフィルターの代替カテゴリーを採用する(): void
    {
        $category = (object) ['cat_ID' => 55, 'cat_name' => '代替カテゴリー'];
        Functions\expect('get_the_category')->once()->with(101)->andReturn([]);
        $GLOBALS['test_mock_apply_filters_callbacks']['get_the_nolink_category'] = static function ($selected, $categories) use ($category) {
            return $category;
        };

        $this->assertSame($category, get_category_for_label(101));
    }

    public function test_カテゴリーもフィルター指定もなければ未取得結果を返す(): void
    {
        Functions\expect('get_the_category')->once()->with(101)->andReturn([]);

        $this->assertNull(get_category_for_label(101));
    }

    public function test_ラベルHTMLと前後記事クラスは同じフィルター結果のカテゴリーを使う(): void
    {
        Functions\expect('get_the_category')->twice()->with(101)->andReturn([
            (object) ['cat_ID' => 7, 'cat_name' => '先頭カテゴリー'],
        ]);
        $GLOBALS['test_mock_apply_filters_callbacks']['get_the_nolink_category'] = static function ($selected, $categories, $postId) {
            return (object) ['cat_ID' => 55, 'cat_name' => 'フィルター指定'];
        };

        $this->assertSame(
            '<span class="cat-label cat-label-55">フィルター指定</span>',
            get_the_nolink_category(101)
        );
        $this->assertSame(' post-navi-category-55', get_post_navi_category_classes(101));
    }

    public function test_非表示指定のカテゴリーラベルはHTMLを返さない(): void
    {
        Functions\when('get_the_category')->justReturn([
            (object) ['cat_ID' => 7, 'cat_name' => '非表示カテゴリー'],
        ]);

        $this->assertNull(get_the_nolink_category(101, false));
    }
}
