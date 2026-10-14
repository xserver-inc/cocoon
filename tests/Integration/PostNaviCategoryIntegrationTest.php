<?php
/**
 * 前後記事のカテゴリー識別クラスのWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class PostNaviCategoryIntegrationTest extends IntegrationTestCase
{
    public function test_表示中の投稿と異なるカテゴリーでも指定投稿の全カテゴリーを返す(): void
    {
        $categoryId = $this->createCategory(['name' => '前後記事カテゴリー-' . wp_generate_uuid4()]);
        $childCategoryId = $this->createCategory([
            'name' => '前後記事子カテゴリー-' . wp_generate_uuid4(),
            'parent' => $categoryId,
        ]);
        $postId = $this->createPost([
            'post_title' => '複数カテゴリーの前後記事',
            'post_status' => 'publish',
            'post_category' => [$categoryId, $childCategoryId],
        ]);
        $currentPostId = $this->createPost([
            'post_title' => '表示中の記事',
            'post_status' => 'publish',
        ]);

        // 表示中の投稿に依存しないカテゴリー取得の確認
        $hadPost = array_key_exists('post', $GLOBALS);
        $originalPost = $GLOBALS['post'] ?? null;
        $GLOBALS['post'] = get_post($currentPostId);
        try {
            $classes = preg_split('/\s+/', trim(get_post_navi_category_classes($postId)));
            $this->assertEqualsCanonicalizing([
                'post-navi-category-' . $categoryId,
                'post-navi-category-' . $childCategoryId,
            ], $classes);
        } finally {
            if ($hadPost) {
                $GLOBALS['post'] = $originalPost;
            } else {
                unset($GLOBALS['post']);
            }
        }
    }

    public function test_標準カテゴリーのない投稿タイプには識別クラスを追加しない(): void
    {
        $pageId = $this->createPost([
            'post_title' => '標準カテゴリーのないページ',
            'post_type' => 'page',
            'post_status' => 'publish',
        ]);

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
            $this->assertSame('', get_post_navi_category_classes($postId));
        } finally {
            remove_filter('get_the_terms', $filter, 10);
        }
    }
}
