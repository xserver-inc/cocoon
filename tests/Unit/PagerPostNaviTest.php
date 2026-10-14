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

        Functions\when('is_post_navi_visible')->justReturn(true);
        Functions\when('is_post_navi_same_category_enable')->justReturn(false);
        Functions\when('get_post_navi_exclude_category_ids')->justReturn([]);
        Functions\when('get_post_type')->justReturn('post');
        Functions\when('get_object_taxonomies')->justReturn(['category', 'post_tag']);
        Functions\when('get_post_navi_type')->justReturn('default');
        Functions\when('is_post_navi_border_visible')->justReturn(true);
        Functions\when('is_post_navi_type_spuare')->justReturn(false);
        Functions\when('wp_strip_all_tags')->alias('strip_tags');
        Functions\when('get_post_navi_thumbnail_tag')->alias(static function ($postId, $width, $height) {
            return '<img src="/thumbnail-' . $postId . '.jpg" width="' . $width . '" height="' . $height . '" alt="">';
        });
    }

    public function test_前後のリンクは表示中の記事ではなく各リンク先の全カテゴリーを持つ(): void
    {
        $this->mockAdjacentPosts(101, 303);
        Functions\expect('get_the_category')->once()->with(101)->andReturn([
            (object) ['term_id' => 7],
            (object) ['term_id' => 8],
        ]);
        Functions\expect('get_the_category')->once()->with(303)->andReturn([
            (object) ['term_id' => 9],
        ]);

        $html = $this->renderNavigation();
        $links = $this->navigationLinks($html);

        $this->assertCount(2, $links);
        $this->assertSame('prev-post a-wrap border-element cf post-navi-category-7 post-navi-category-8', $links[0]['class']);
        $this->assertSame('next-post a-wrap cf post-navi-category-9', $links[1]['class']);

        // 識別クラス以外の要素・属性・本文が従来のHTMLと同一であることの確認
        $withoutCategoryClasses = preg_replace('/ post-navi-category-\d+/', '', $html);
        $this->assertSame($this->normalizeHtml($this->originalNavigationHtml()), $this->normalizeHtml($withoutCategoryClasses));
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
            ->andReturn([(object) ['term_id' => 17]]);

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

    public function test_カテゴリーのない記事へのリンクは従来のクラスを維持する(): void
    {
        $this->mockAdjacentPosts(101, 303);
        Functions\expect('get_the_category')->once()->with(101)->andReturn([]);
        Functions\expect('get_the_category')->once()->with(303)->andReturn([]);

        $this->assertSame(
            $this->normalizeHtml($this->originalNavigationHtml()),
            $this->normalizeHtml($this->renderNavigation())
        );
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

    private function normalizeHtml(string $html): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $html));
    }

    private function originalNavigationHtml(): string
    {
        return <<<'HTML'
<div id="pager-post-navi" class="pager-post-navi post-navi-default post-navi-border cf">
<a href="http://example.com/" title="Test Title" class="prev-post a-wrap border-element cf">
<div class="fa fa-chevron-left iconfont" aria-hidden="true"></div>
<figure class="prev-post-thumb card-thumb"><img src="/thumbnail-101.jpg" width="120" height="68" alt=""></figure>
<div class="prev-post-title">Test Title</div></a><a href="http://example.com/" title="Test Title" class="next-post a-wrap cf">
<div class="fa fa-chevron-right iconfont" aria-hidden="true"></div>
<figure class="next-post-thumb card-thumb">
<img src="/thumbnail-303.jpg" width="120" height="68" alt=""></figure>
<div class="next-post-title">Test Title</div></a>
</div><!-- /.pager-post-navi -->
HTML;
    }
}
