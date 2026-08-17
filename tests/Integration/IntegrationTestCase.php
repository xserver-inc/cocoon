<?php
/**
 * WordPress 統合テスト 基底クラス
 *
 * PHPUnit 11ではWordPress側のWP_UnitTestCaseに互換性がないため、
 * PHPUnit標準のTestCase上で必要なテストデータを明示的に管理します。
 */

namespace Cocoon\Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    /** @var int[] テスト中に作成した投稿ID */
    private array $createdPostIds = [];

    /** @var array<int, array{id: int, taxonomy: string}> テスト中に作成したターム */
    private array $createdTerms = [];

    /** テスト開始時のパーマリンク構造 */
    private ?string $originalPermalinkStructure = null;

    protected function setUp(): void
    {
        parent::setUp();

        //通常のユニットテストから読み込まれた場合はWordPress依存テストを実行しない
        if (!defined('WP_TESTS_DOMAIN')) {
            $this->markTestSkipped('WordPress テスト環境が利用できません。WP_TESTS_DIR を設定してください。');
        }

        //テストで変更したパーマリンク構造を後から元へ戻せるよう退避する
        $this->originalPermalinkStructure = (string) get_option('permalink_structure', '');
    }

    protected function tearDown(): void
    {
        //投稿に紐づくデータも含めてテスト投稿を完全に削除する
        foreach ($this->createdPostIds as $postId) {
            wp_delete_post($postId, true);
        }

        //テストで作成したカテゴリーやタグを完全に削除する
        foreach ($this->createdTerms as $term) {
            wp_delete_term($term['id'], $term['taxonomy']);
        }

        //各テストがパーマリンク設定を持ち越さないよう開始時の値へ戻す
        if ($this->originalPermalinkStructure !== null) {
            $this->setPermalinkStructure($this->originalPermalinkStructure);
        }

        parent::tearDown();
    }

    /**
     * WordPress APIで投稿を作成し、終了時の削除対象として記録する
     */
    protected function createPost(array $postData): int
    {
        $result = wp_insert_post($postData, true);
        if (is_wp_error($result)) {
            $this->fail($result->get_error_message());
        }

        $postId = (int) $result;
        $this->createdPostIds[] = $postId;
        return $postId;
    }

    /**
     * WordPress APIでカテゴリーを作成し、終了時の削除対象として記録する
     */
    protected function createCategory(array $termData): int
    {
        return $this->createTerm('category', $termData);
    }

    /**
     * WordPress APIでタグを作成し、終了時の削除対象として記録する
     */
    protected function createTag(array $termData): int
    {
        return $this->createTerm('post_tag', $termData);
    }

    /**
     * WordPressのリライトルールへテスト用パーマリンク構造を反映する
     */
    protected function setPermalinkStructure(string $structure): void
    {
        global $wp_rewrite;

        $wp_rewrite->set_permalink_structure($structure);
    }

    /**
     * テーマがアクティブであることを確認する
     */
    protected function assertThemeActive(): void
    {
        $this->assertSame('cocoon-master', get_stylesheet());
    }

    /**
     * WordPress APIでタームを作成し、終了時の削除対象として記録する
     */
    private function createTerm(string $taxonomy, array $termData): int
    {
        $name = isset($termData['name']) ? (string) $termData['name'] : '';
        unset($termData['name']);

        $result = wp_insert_term($name, $taxonomy, $termData);
        if (is_wp_error($result)) {
            $this->fail($result->get_error_message());
        }

        $termId = (int) $result['term_id'];
        $this->createdTerms[] = [
            'id' => $termId,
            'taxonomy' => $taxonomy,
        ];
        return $termId;
    }
}
