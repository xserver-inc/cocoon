<?php
/**
 * 前後記事のカテゴリー識別クラスのWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class PostNaviCategoryIntegrationTest extends IntegrationTestCase
{
    private array $createdCustomTerms = [];
    private array $registeredPostTypes = [];
    private array $registeredTaxonomies = [];
    private bool $postCaptured = false;
    private bool $hadPost = false;
    private mixed $originalPost = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadPost = array_key_exists('post', $GLOBALS);
        $this->originalPost = $GLOBALS['post'] ?? null;
        $this->postCaptured = true;
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            // 投稿削除後の独自タームと一時登録の後始末
            foreach ($this->createdCustomTerms as [$termId, $taxonomy]) {
                wp_delete_term($termId, $taxonomy);
            }
            foreach ($this->registeredTaxonomies as $taxonomy) {
                unregister_taxonomy($taxonomy);
            }
            foreach ($this->registeredPostTypes as $postType) {
                unregister_post_type($postType);
            }
            if ($this->postCaptured) {
                if ($this->hadPost) {
                    $GLOBALS['post'] = $this->originalPost;
                } else {
                    unset($GLOBALS['post']);
                }
            }
        }
    }

    public function test_表示中の記事と異なるリンク先のメインカテゴリーだけを返す(): void
    {
        [$postId, $categoryIds] = $this->createCategorizedPost();
        [$currentPostId, $currentCategoryIds] = $this->createCategorizedPost();
        update_post_meta($postId, 'the_page_main_category', $categoryIds[1]);
        update_post_meta($currentPostId, 'the_page_main_category', $currentCategoryIds[0]);
        $GLOBALS['post'] = get_post($currentPostId);

        $this->assertSelectedCategory($postId, $categoryIds[1]);
    }

    public function test_通常投稿のオブジェクト指定でもID指定と同じメインカテゴリーを返す(): void
    {
        [$postId, $categoryIds] = $this->createCategorizedPost();
        update_post_meta($postId, 'the_page_main_category', $categoryIds[1]);
        $post = get_post($postId);

        $this->assertSelectedCategory($postId, $categoryIds[1]);
        $this->assertSame(get_the_nolink_category($postId), get_the_nolink_category($post));
        $this->assertSame(get_post_navi_category_classes($postId), get_post_navi_category_classes($post));
    }

    public function test_メインカテゴリー未指定時はカードと同じ先頭カテゴリーを返す(): void
    {
        [$postId] = $this->createCategorizedPost();
        $categories = get_the_category($postId);

        $this->assertSelectedCategory($postId, (int) $categories[0]->cat_ID);
    }

    public function test_所属外のメインカテゴリーはカードと同じ先頭カテゴリーへ戻る(): void
    {
        [$postId] = $this->createCategorizedPost();
        $otherCategoryId = $this->createCategory(['name' => '所属外-' . wp_generate_uuid4()]);
        update_post_meta($postId, 'the_page_main_category', $otherCategoryId);
        $categories = get_the_category($postId);

        $this->assertSelectedCategory($postId, (int) $categories[0]->cat_ID);
    }

    public function test_削除済みメインカテゴリーはカードと同じ先頭カテゴリーへ戻る(): void
    {
        [$postId, $categoryIds] = $this->createCategorizedPost();
        update_post_meta($postId, 'the_page_main_category', $categoryIds[1]);
        wp_delete_term($categoryIds[1], 'category');

        $this->assertSelectedCategory($postId, $categoryIds[0]);
    }

    public function test_既存の二引数フィルターをカードとナビで共有する(): void
    {
        [$postId, $categoryIds] = $this->createCategorizedPost();
        $filteredCategory = get_category($categoryIds[1]);
        $receivedCategories = [];
        $filter = static function ($category, $categories) use ($filteredCategory, &$receivedCategories) {
            $receivedCategories = $categories;
            return $filteredCategory;
        };

        add_filter('get_the_nolink_category', $filter, 20, 2);
        try {
            $this->assertSelectedCategory($postId, $categoryIds[1]);
            $this->assertEqualsCanonicalizing($categoryIds, array_map(
                static fn ($category) => (int) $category->cat_ID,
                $receivedCategories
            ));
        } finally {
            remove_filter('get_the_nolink_category', $filter, 20);
        }
    }

    public function test_新しい第三引数フィルターに指定記事と省略時の記事IDを渡す(): void
    {
        [$postId] = $this->createCategorizedPost();
        [$currentPostId] = $this->createCategorizedPost();
        $GLOBALS['post'] = get_post($currentPostId);
        $receivedPostIds = [];
        $filter = static function ($category, $categories, $requestedPostId) use (&$receivedPostIds) {
            $receivedPostIds[] = $requestedPostId;
            return $category;
        };

        add_filter('get_the_nolink_category', $filter, 20, 3);
        try {
            get_category_for_label($postId);
            get_the_nolink_category($postId);
            get_post_navi_category_classes($postId);
            get_category_for_label();
            get_the_nolink_category();
            $this->assertSame([$postId, $postId, $postId, $currentPostId, $currentPostId], $receivedPostIds);
        } finally {
            remove_filter('get_the_nolink_category', $filter, 20);
        }
    }

    public function test_カテゴリーラベル非表示でもナビにメインカテゴリーを一つだけ付ける(): void
    {
        [$postId, $categoryIds] = $this->createCategorizedPost();
        update_post_meta($postId, 'the_page_main_category', $categoryIds[1]);

        $this->assertNull(get_the_nolink_category($postId, false));
        $this->assertSame(' post-navi-category-' . $categoryIds[1], get_post_navi_category_classes($postId));
    }

    public function test_標準カテゴリーのない投稿タイプには識別クラスを追加しない(): void
    {
        $pageId = $this->createPost([
            'post_title' => '標準カテゴリーのないページ',
            'post_type' => 'page',
            'post_status' => 'publish',
        ]);

        $this->assertNull(get_the_nolink_category($pageId));
        $this->assertSame('', get_post_navi_category_classes($pageId));
    }

    public function test_カテゴリー取得失敗時はWordPressの標準処理で空文字列になる(): void
    {
        $postId = $this->createPost([
            'post_title' => 'カテゴリー取得失敗の確認',
            'post_status' => 'publish',
        ]);
        $filter = static function ($terms, $requestedPostId, $taxonomy) use ($postId) {
            if ($requestedPostId === $postId && $taxonomy === 'category') {
                return new \WP_Error('post_navi_category_test', 'カテゴリー取得失敗のテスト');
            }
            return $terms;
        };

        // get_the_terms()の失敗をget_the_category()が空配列へ変換する経路の確認
        add_filter('get_the_terms', $filter, 10, 3);
        try {
            $this->assertNull(get_the_nolink_category($postId));
            $this->assertSame('', get_post_navi_category_classes($postId));
        } finally {
            remove_filter('get_the_terms', $filter, 10);
        }
    }

    public function test_カスタム投稿は表示中の通常記事ではなくリンク先のメインタームを返す(): void
    {
        [$postType, $taxonomy] = $this->registerCategorizedPostType();
        [$postId, $termIds] = $this->createCustomPost($postType, $taxonomy);
        [$currentPostId] = $this->createCategorizedPost();
        update_post_meta($postId, 'the_page_main_category', $termIds[0]);
        $GLOBALS['post'] = get_post($currentPostId);

        $this->assertSelectedCategory($postId, $termIds[0], $taxonomy);
    }

    public function test_カスタム投稿のオブジェクト指定でもID指定と同じメインタームを返す(): void
    {
        [$postType, $taxonomy] = $this->registerCategorizedPostType();
        [$postId, $termIds] = $this->createCustomPost($postType, $taxonomy);
        update_post_meta($postId, 'the_page_main_category', $termIds[0]);
        $post = get_post($postId);
        $GLOBALS['post'] = $post;

        $this->assertSelectedCategory($postId, $termIds[0], $taxonomy);
        $this->assertSame(get_the_nolink_category($postId), get_the_nolink_category($post));
        $this->assertSame(get_post_navi_category_classes($postId), get_post_navi_category_classes($post));
    }

    public function test_通常記事は表示中のカスタム投稿のメインタームを参照しない(): void
    {
        [$postType, $taxonomy] = $this->registerCategorizedPostType();
        [$currentPostId, $termIds] = $this->createCustomPost($postType, $taxonomy);
        [$postId, $categoryIds] = $this->createCategorizedPost();
        update_post_meta($currentPostId, 'the_page_main_category', $termIds[0]);
        update_post_meta($postId, 'the_page_main_category', $categoryIds[1]);
        $GLOBALS['post'] = get_post($currentPostId);

        $this->assertSelectedCategory($postId, $categoryIds[1]);
    }

    public function test_カスタム投稿でメイン未指定なら最初の階層タクソノミーの名前順先頭を返す(): void
    {
        [$postType, $taxonomy] = $this->registerCategorizedPostType();
        [$postId, $termIds] = $this->createCustomPost($postType, $taxonomy);
        $secondTaxonomy = $this->registerTestTaxonomy($postType);
        $otherTermId = $this->createCustomTerm($secondTaxonomy, '0-別タクソノミー');
        wp_set_object_terms($postId, [$otherTermId], $secondTaxonomy);

        $this->assertSelectedCategory($postId, $termIds[1], $taxonomy);
    }

    public function test_カスタム投稿でメインターム削除後は名前順先頭へ戻る(): void
    {
        [$postType, $taxonomy] = $this->registerCategorizedPostType();
        [$postId, $termIds] = $this->createCustomPost($postType, $taxonomy);
        update_post_meta($postId, 'the_page_main_category', $termIds[1]);
        wp_delete_term($termIds[1], $taxonomy);

        $this->assertSelectedCategory($postId, $termIds[0], $taxonomy);
    }

    public function test_カスタム投稿にタームがなければカテゴリークラスを付けない(): void
    {
        [$postType] = $this->registerCategorizedPostType();
        $postId = $this->createPost([
            'post_title' => 'タームなしカスタム投稿',
            'post_type' => $postType,
            'post_status' => 'publish',
        ]);

        $this->assertNull(get_the_nolink_category($postId));
        $this->assertSame('', get_post_navi_category_classes($postId));
    }

    public function test_カスタム投稿のターム取得失敗時はカテゴリークラスを付けない(): void
    {
        [$postType, $taxonomy] = $this->registerCategorizedPostType();
        [$postId] = $this->createCustomPost($postType, $taxonomy);
        $filter = static function ($terms, array $objectIds, array $taxonomies) use ($postId, $taxonomy) {
            if (in_array($postId, $objectIds, true) && in_array($taxonomy, $taxonomies, true)) {
                return new \WP_Error('post_navi_custom_term_test', '独自ターム取得失敗のテスト');
            }
            return $terms;
        };

        // 投稿IDと分類名を配列で受け取るフックによる取得失敗の再現
        add_filter('get_object_terms', $filter, 10, 3);
        try {
            $this->assertNull(get_the_nolink_category($postId));
            $this->assertSame('', get_post_navi_category_classes($postId));
        } finally {
            remove_filter('get_object_terms', $filter, 10);
        }
    }

    private function createCategorizedPost(): array
    {
        $categoryIds = [
            $this->createCategory(['name' => 'A-前後記事-' . wp_generate_uuid4()]),
            $this->createCategory(['name' => 'Z-前後記事-' . wp_generate_uuid4()]),
        ];
        $postId = $this->createPost([
            'post_title' => '複数カテゴリーの前後記事',
            'post_status' => 'publish',
            'post_category' => $categoryIds,
        ]);
        return [$postId, $categoryIds];
    }

    private function registerCategorizedPostType(): array
    {
        $postType = 'cocoon_nav_' . substr(str_replace('-', '', wp_generate_uuid4()), 0, 8);
        $result = register_post_type($postType, ['public' => true, 'rewrite' => false]);
        $this->assertNotInstanceOf(\WP_Error::class, $result);
        $this->registeredPostTypes[] = $postType;
        return [$postType, $this->registerTestTaxonomy($postType)];
    }

    private function registerTestTaxonomy(string $postType): string
    {
        $taxonomy = 'cocoon_nav_tax_' . substr(str_replace('-', '', wp_generate_uuid4()), 0, 8);
        $result = register_taxonomy($taxonomy, $postType, ['hierarchical' => true, 'rewrite' => false]);
        $this->assertNotInstanceOf(\WP_Error::class, $result);
        $this->registeredTaxonomies[] = $taxonomy;
        return $taxonomy;
    }

    private function createCustomPost(string $postType, string $taxonomy): array
    {
        $termIds = [
            $this->createCustomTerm($taxonomy, 'Z-先に作成したターム'),
            $this->createCustomTerm($taxonomy, 'A-名前順の先頭ターム'),
        ];
        $postId = $this->createPost([
            'post_title' => '複数タームのカスタム投稿',
            'post_type' => $postType,
            'post_status' => 'publish',
        ]);
        $result = wp_set_object_terms($postId, $termIds, $taxonomy);
        $this->assertNotInstanceOf(\WP_Error::class, $result);
        return [$postId, $termIds];
    }

    private function createCustomTerm(string $taxonomy, string $name): int
    {
        $result = wp_insert_term($name, $taxonomy);
        $this->assertNotInstanceOf(\WP_Error::class, $result);
        $termId = (int) $result['term_id'];
        $this->createdCustomTerms[] = [$termId, $taxonomy];
        return $termId;
    }

    private function assertSelectedCategory(int $postId, int $categoryId, string $taxonomy = 'category'): void
    {
        $term = get_term($categoryId, $taxonomy);
        $selectedCategory = get_category_for_label($postId);

        // HTMLの完全一致と単一クラスによる既存表示と選択結果の一致確認
        $this->assertSame($categoryId, (int) $selectedCategory->cat_ID);
        $this->assertSame(
            '<span class="cat-label cat-label-' . $categoryId . '">' . $term->name . '</span>',
            get_the_nolink_category($postId)
        );
        $this->assertSame(' post-navi-category-' . $categoryId, get_post_navi_category_classes($postId));
    }
}
