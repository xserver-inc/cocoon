<?php
/**
 * 固定表示投稿と除外カテゴリーの優先順位テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

final class StickyPostMainQueryStub
{
    public function __construct(
        private readonly bool $home,
        private readonly bool $paged,
        private readonly bool $ignoreStickyPosts
    ) {
    }

    public function is_home(): bool
    {
        return $this->home;
    }

    public function is_paged(): bool
    {
        return $this->paged;
    }

    public function get(string $key): bool
    {
        return $key === 'ignore_sticky_posts' && $this->ignoreStickyPosts;
    }
}

final class StickyPostExclusionTest extends TestCase
{
    public function test_除外カテゴリーか固定表示投稿が空なら検索しない(): void
    {
        Functions\expect('get_posts')->never();

        $this->assertSame([], get_sticky_post_ids_in_categories([], [101, 202]));
        $this->assertSame([], get_sticky_post_ids_in_categories([9], []));
    }

    public function test_除外カテゴリーに属する固定表示投稿だけを取得する(): void
    {
        Functions\expect('get_posts')
            ->once()
            ->with([
                'post__in' => [101, 202],
                'category__in' => [4, 9],
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'ignore_sticky_posts' => true,
                'fields' => 'ids',
            ])
            ->andReturn(['202', 101, 202, 0]);

        $category_ids = [9, '4', 9, 0];
        $sticky_post_ids = [101, '202', 0, 202];

        $this->assertSame(
            [202, 101],
            get_sticky_post_ids_in_categories(array_reverse($category_ids), array_reverse($sticky_post_ids))
        );
        $this->assertSame(
            [202, 101],
            get_sticky_post_ids_in_categories($category_ids, $sticky_post_ids)
        );
    }

    public function test_明示的な除外投稿とカテゴリー内の固定表示投稿を統合する(): void
    {
        Functions\expect('get_posts')->once()->andReturn([30, '40', 30]);

        $this->assertSame(
            [10, 20, 30, 40],
            merge_category_excluded_sticky_post_ids(['10', 20, 10, 0], [5], [30, 40])
        );
    }

    public function test_ホーム1ページ目だけカテゴリー内の固定表示投稿を統合する(): void
    {
        Functions\expect('get_posts')->once()->andReturn([303]);

        $home_query = new StickyPostMainQueryStub(true, false, false);
        $paged_query = new StickyPostMainQueryStub(true, true, false);
        $archive_query = new StickyPostMainQueryStub(false, false, false);
        $ignore_sticky_query = new StickyPostMainQueryStub(true, false, true);

        $this->assertSame(
            [7, 303],
            merge_home_category_excluded_sticky_post_ids($home_query, [7], [12], [303])
        );
        $this->assertSame(
            [7],
            merge_home_category_excluded_sticky_post_ids($paged_query, [7], [12], [303])
        );
        $this->assertSame(
            [7],
            merge_home_category_excluded_sticky_post_ids($archive_query, [7], [12], [303])
        );
        $this->assertSame(
            [7],
            merge_home_category_excluded_sticky_post_ids($ignore_sticky_query, [7], [12], [303])
        );
    }
}
