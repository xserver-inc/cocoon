<?php
/**
 * 固定表示投稿と除外カテゴリーのWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

use PHPUnit\Framework\TestCase;

class StickyPostExclusionIntegrationTest extends TestCase
{
    private array $originalStickyPostIds = [];
    private bool $hadExcludeCategoryThemeMod = false;
    private mixed $originalExcludeCategoryThemeMod = null;
    private array $createdPostIds = [];
    private array $createdCategoryIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        //通常のユニットテストから読み込まれた場合はWordPress依存テストを実行しない
        if (!defined('WP_TESTS_DOMAIN')) {
            $this->markTestSkipped('WordPress テスト環境が利用できません。WP_TESTS_DIR を設定してください。');
        }

        //テスト後にWordPressの固定表示設定を元へ戻せるよう退避する
        $this->originalStickyPostIds = (array) get_option('sticky_posts', []);

        //テスト後にCocoonの除外カテゴリー設定を元へ戻せるよう退避する
        $themeMods = get_theme_mods();
        $this->hadExcludeCategoryThemeMod = is_array($themeMods)
            && array_key_exists(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS, $themeMods);
        $this->originalExcludeCategoryThemeMod = $this->hadExcludeCategoryThemeMod
            ? $themeMods[OP_ARCHIVE_EXCLUDE_CATEGORY_IDS]
            : null;
    }

    protected function tearDown(): void
    {
        //WordPress未読み込みでスキップした場合はWordPress APIによる後始末を行わない
        if (!defined('WP_TESTS_DOMAIN')) {
            parent::tearDown();
            return;
        }

        //固定表示設定をテスト前の状態へ戻す
        update_option('sticky_posts', $this->originalStickyPostIds);

        //Cocoonの除外カテゴリー設定をテスト前の状態へ戻す
        if ($this->hadExcludeCategoryThemeMod) {
            set_theme_mod(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS, $this->originalExcludeCategoryThemeMod);
        } else {
            remove_theme_mod(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS);
        }

        //テストで作成した投稿を完全に削除する
        foreach ($this->createdPostIds as $postId) {
            wp_delete_post($postId, true);
        }

        //テストで作成したカテゴリーを完全に削除する
        foreach ($this->createdCategoryIds as $categoryId) {
            wp_delete_term($categoryId, 'category');
        }

        parent::tearDown();
    }

    public function test_ホームのメインクエリは除外カテゴリーの固定表示投稿を再挿入しない(): void
    {
        [$excludedPostId, $visiblePostId] = $this->createExcludedStickyScenario();

        //is_main_query()を満たすクエリとしてWordPressの取得処理を最後まで実行する
        $query = new \WP_Query();
        $hadWpQuery = array_key_exists('wp_query', $GLOBALS);
        $hadWpTheQuery = array_key_exists('wp_the_query', $GLOBALS);
        $originalWpQuery = $hadWpQuery ? $GLOBALS['wp_query'] : null;
        $originalWpTheQuery = $hadWpTheQuery ? $GLOBALS['wp_the_query'] : null;
        $GLOBALS['wp_query'] = $query;
        $GLOBALS['wp_the_query'] = $query;

        try {
            $posts = $query->query([
                'posts_per_page' => 10,
                'post_status' => 'publish',
            ]);
        } finally {
            //WordPressのメインクエリー用グローバルを必ず元へ戻す
            $this->restoreGlobal('wp_query', $hadWpQuery, $originalWpQuery);
            $this->restoreGlobal('wp_the_query', $hadWpTheQuery, $originalWpTheQuery);
        }

        //固定表示投稿の再挿入が発生するクエリー条件を実際に通ったことを保証する
        $this->assertTrue($query->is_home());
        $this->assertFalse((bool) $query->get('ignore_sticky_posts'));

        $postIds = wp_list_pluck($posts, 'ID');
        $this->assertContains($visiblePostId, $postIds);
        $this->assertNotContains($excludedPostId, $postIds);
    }

    public function test_新着記事ウィジェットは除外カテゴリーの固定表示投稿を表示しない(): void
    {
        [$excludedPostId, $visiblePostId] = $this->createExcludedStickyScenario();

        //実際のWP_Queryとカード生成処理を通してウィジェットHTMLを取得する
        ob_start();
        try {
            generate_widget_entries_tag([
                'entry_count' => 10,
                'sticky' => 1,
            ]);
            $html = (string) ob_get_contents();
        } finally {
            //例外時にも出力バッファーを残さない
            ob_end_clean();
        }

        $this->assertStringContainsString(get_permalink($visiblePostId), $html);
        $this->assertStringNotContainsString(get_permalink($excludedPostId), $html);
    }

    private function createExcludedStickyScenario(): array
    {
        //除外対象と表示対象のカテゴリーをそれぞれ作成する
        $suffix = wp_generate_uuid4();
        $excludedCategoryId = $this->createCategory('固定表示除外カテゴリー-' . $suffix);
        $visibleCategoryId = $this->createCategory('固定表示表示カテゴリー-' . $suffix);

        //除外カテゴリーに属する固定表示投稿を作成する
        $excludedPostId = $this->createPost([
            'post_title' => '除外される固定表示投稿',
            'post_status' => 'publish',
            'post_category' => [$excludedCategoryId],
        ]);

        //一覧に残る通常投稿を作成する
        $visiblePostId = $this->createPost([
            'post_title' => '表示される通常投稿',
            'post_status' => 'publish',
            'post_category' => [$visibleCategoryId],
        ]);

        //Cocoonの除外カテゴリーとWordPressの固定表示を実際のAPIで設定する
        set_theme_mod(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS, [$excludedCategoryId]);
        stick_post($excludedPostId);

        return [$excludedPostId, $visiblePostId];
    }

    private function createCategory(string $name): int
    {
        //WordPress APIでカテゴリーを作成し、失敗時は原因を表示する
        $result = wp_insert_term($name, 'category');
        if (is_wp_error($result)) {
            $this->fail($result->get_error_message());
        }

        $categoryId = (int) $result['term_id'];
        $this->createdCategoryIds[] = $categoryId;
        return $categoryId;
    }

    private function createPost(array $postData): int
    {
        //WordPress APIで投稿を作成し、失敗時は原因を表示する
        $result = wp_insert_post($postData, true);
        if (is_wp_error($result)) {
            $this->fail($result->get_error_message());
        }

        $postId = (int) $result;
        $this->createdPostIds[] = $postId;
        return $postId;
    }

    private function restoreGlobal(string $name, bool $hadValue, mixed $originalValue): void
    {
        //テスト前に存在しなかったグローバルは削除する
        if (!$hadValue) {
            unset($GLOBALS[$name]);
            return;
        }

        $GLOBALS[$name] = $originalValue;
    }
}
