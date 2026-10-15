<?php
/**
 * 前後記事ナビゲーションのカテゴリー識別クラスの回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class PagerPostNaviTest extends TestCase
{
    private bool $hadInCategoryCallback = false;
    private mixed $originalInCategoryCallback = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/additional-classes.php';

        foreach (['THUMB120WIDTH' => 120, 'THUMB120HEIGHT' => 68, 'THUMB150WIDTH' => 150, 'THUMB150HEIGHT' => 150] as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 所属判定コールバックの退避とテストごとの初期化
        $this->hadInCategoryCallback = array_key_exists('test_mock_in_category_callback', $GLOBALS);
        $this->originalInCategoryCallback = $GLOBALS['test_mock_in_category_callback'] ?? null;
        unset($GLOBALS['test_mock_in_category_callback']);
        Functions\when('is_post_navi_visible')->justReturn(true);
        Functions\when('is_post_navi_same_category_enable')->justReturn(false);
        Functions\when('get_post_navi_exclude_category_ids')->justReturn([]);
        Functions\when('get_post_type')->justReturn('post');
        Functions\when('get_object_taxonomies')->justReturn(['category', 'post_tag']);
        Functions\when('get_post_navi_type')->justReturn('default');
        Functions\when('is_post_navi_border_visible')->justReturn(true);
        Functions\when('is_post_navi_type_spuare')->justReturn(false);
        Functions\when('get_the_page_main_category')->justReturn(null);
        Functions\when('wp_strip_all_tags')->alias('strip_tags');
        Functions\when('get_post_navi_thumbnail_tag')->alias(static function ($postId, $width, $height) {
            return '<img src="/thumbnail-' . $postId . '.jpg" width="' . $width . '" height="' . $height . '" alt="">';
        });
    }

    protected function tearDown(): void
    {
        // テスト前の所属判定コールバックの復元
        if ($this->hadInCategoryCallback) {
            $GLOBALS['test_mock_in_category_callback'] = $this->originalInCategoryCallback;
        } else {
            unset($GLOBALS['test_mock_in_category_callback']);
        }

        parent::tearDown();
    }

    public function test_前後のリンクは各リンク先のラベル用カテゴリーを1つだけ持つ(): void
    {
        $this->mockAdjacentPosts(101, 303);
        Functions\expect('get_the_category')->once()->with(101)->andReturn([
            (object) ['cat_ID' => 7, 'cat_name' => 'カテゴリー7'],
            (object) ['cat_ID' => 8, 'cat_name' => 'カテゴリー8'],
        ]);
        Functions\expect('get_the_category')->once()->with(303)->andReturn([
            (object) ['cat_ID' => 9, 'cat_name' => 'カテゴリー9'],
        ]);

        $html = $this->renderNavigation();
        $links = $this->navigationLinks($html);

        $this->assertCount(2, $links);
        $this->assertSame('prev-post a-wrap border-element cf post-navi-category-7', $links[0]['class']);
        $this->assertSame('next-post a-wrap cf post-navi-category-9', $links[1]['class']);
        $this->assertStringNotContainsString('cat-label', $html);
    }

    public function test_前後のリンクは各投稿で指定された異なるメインカテゴリーを持つ(): void
    {
        $this->mockAdjacentPosts(101, 303);
        $previousMain = (object) ['cat_ID' => 8, 'cat_name' => '前の記事のメインカテゴリー'];
        $nextMain = (object) ['cat_ID' => 10, 'cat_name' => '次の記事のメインカテゴリー'];
        Functions\expect('get_the_category')->once()->with(101)->andReturn([
            (object) ['cat_ID' => 7, 'cat_name' => '前の記事の先頭カテゴリー'],
            $previousMain,
        ]);
        Functions\expect('get_the_category')->once()->with(303)->andReturn([
            (object) ['cat_ID' => 9, 'cat_name' => '次の記事の先頭カテゴリー'],
            $nextMain,
        ]);
        $mainCategoryCalls = [];
        Functions\when('get_the_page_main_category')->alias(static function ($postId) use (&$mainCategoryCalls) {
            $mainCategoryCalls[] = $postId;
            return [101 => 8, 303 => 10][$postId] ?? null;
        });
        $inCategoryCalls = [];
        $GLOBALS['test_mock_in_category_callback'] = static function ($categoryId, $postId) use (&$inCategoryCalls) {
            $inCategoryCalls[] = [$categoryId, $postId];
            return true;
        };
        Functions\expect('get_category')->once()->with(8)->andReturn($previousMain);
        Functions\expect('get_category')->once()->with(10)->andReturn($nextMain);

        $links = $this->navigationLinks($this->renderNavigation());

        $this->assertCount(2, $links);
        $this->assertSame([101, 303], $mainCategoryCalls);
        $this->assertSame([[8, 101], [10, 303]], $inCategoryCalls);
        $this->assertSame('prev-post a-wrap border-element cf post-navi-category-8', $links[0]['class']);
        $this->assertSame('next-post a-wrap cf post-navi-category-10', $links[1]['class']);
    }

    #[DataProvider('oneSidedNavigationProvider')]
    public function test_片側だけの記事ナビは実在するリンクだけにカテゴリークラスを付ける(
        ?int $previousId,
        ?int $nextId,
        string $direction
    ): void {
        Functions\when('get_post_navi_type')->justReturn('square');
        Functions\when('is_post_navi_type_spuare')->justReturn(true);
        $this->mockAdjacentPosts($previousId, $nextId);
        Functions\expect('get_the_category')
            ->once()
            ->with($previousId ?? $nextId)
            ->andReturn([(object) ['cat_ID' => 17, 'cat_name' => 'カテゴリー17']]);

        $links = $this->navigationLinks($this->renderNavigation());

        $this->assertCount(2, $links);
        $postLinks = array_values(array_filter($links, static fn($link) => str_contains($link['class'], $direction . '-post ')));
        $homeLinks = array_values(array_filter($links, static fn($link) => $link['class'] === 'prev-next-home a-wrap'));
        $this->assertCount(1, $postLinks);
        $this->assertStringContainsString(' post-navi-category-17', $postLinks[0]['class']);
        $this->assertCount(1, $homeLinks);
    }

    public static function oneSidedNavigationProvider(): array
    {
        return [
            '前の記事のみ' => [101, null, 'prev'],
            '次の記事のみ' => [null, 303, 'next'],
        ];
    }

    public function test_カテゴリーのない記事へのリンクにはカテゴリークラスを付けない(): void
    {
        $this->mockAdjacentPosts(101, 303);
        Functions\expect('get_the_category')->once()->with(101)->andReturn([]);
        Functions\expect('get_the_category')->once()->with(303)->andReturn([]);

        $links = $this->navigationLinks($this->renderNavigation());

        $this->assertCount(2, $links);
        $this->assertSame('prev-post a-wrap border-element cf', $links[0]['class']);
        $this->assertSame('next-post a-wrap cf', $links[1]['class']);
    }

    public function test_隣接する記事がなければカテゴリーの取得もナビの表示も行わない(): void
    {
        $this->mockAdjacentPosts(null, null);
        Functions\expect('get_the_category')->never();

        $this->assertSame('', trim($this->renderNavigation()));
    }

    public function test_ナビ非表示設定では隣接記事とカテゴリーを取得しない(): void
    {
        Functions\when('is_post_navi_visible')->justReturn(false);
        Functions\expect('get_adjacent_post')->never();
        Functions\expect('get_the_category')->never();

        $this->assertSame('', trim($this->renderNavigation()));
    }

    private function mockAdjacentPosts(?int $previousId, ?int $nextId): void
    {
        Functions\expect('get_adjacent_post')->once()->with(false, [], true, 'category')
            ->andReturn($previousId === null ? null : (object) ['ID' => $previousId]);
        Functions\expect('get_adjacent_post')->once()->with(false, [], false, 'category')
            ->andReturn($nextId === null ? null : (object) ['ID' => $nextId]);
    }

    private function renderNavigation(): string
    {
        // 実テンプレートの出力取得と例外時を含む出力バッファーの解放
        ob_start();
        try {
            include dirname(__DIR__, 2) . '/tmp/pager-post-navi.php';
            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private function navigationLinks(string $html): array
    {
        $processor = new \WP_HTML_Tag_Processor($html);
        $links = [];
        while ($processor->next_tag('A')) {
            $links[] = ['class' => $processor->get_attribute('class')];
        }
        return $links;
    }

}
