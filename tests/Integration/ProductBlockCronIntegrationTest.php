<?php
/**
 * 商品リンクブロックの自動更新による投稿本文の上書き防止テスト
 */

namespace Cocoon\Tests\Integration;

class ProductBlockCronIntegrationTest extends IntegrationTestCase
{
    /**
     * 読み込み時と同じ本文の書き戻しと、投稿キャッシュ・更新日時・リビジョンの確認
     */
    public function test_updates_matching_content_without_creating_a_revision(): void
    {
        $originalContent = '<p>商品 Café</p>';
        $updatedContent = "<p>商品 Café の価格は '1,000円'</p>";
        $postId = $this->createPost([
            'post_status' => 'publish',
            'post_content' => $originalContent,
            'post_date' => '2024-01-02 03:04:05',
        ]);
        $before = get_post($postId);
        $revisionIds = array_keys(wp_get_post_revisions($postId));

        $this->assertSame(1, cocoon_product_block_update_post_content($postId, $before->post_content, $updatedContent));

        $after = get_post($postId);
        $this->assertSame($updatedContent, $after->post_content);
        $this->assertSame($before->post_modified, $after->post_modified);
        $this->assertSame($before->post_modified_gmt, $after->post_modified_gmt);
        $this->assertSame($revisionIds, array_keys(wp_get_post_revisions($postId)));
    }

    /**
     * 大文字・小文字や末尾の空白だけが異なる編集内容の保持
     */
    public function test_skips_when_the_saved_content_differs(): void
    {
        $originalContent = '<p>商品 Café</p>';
        foreach (['<p>編集済みの商品</p>', '<p>商品 café</p>', $originalContent . ' '] as $editedContent) {
            $postId = $this->createPost([
                'post_status' => 'publish',
                'post_content' => $originalContent,
            ]);
            $snapshot = get_post($postId);

            $this->assertSame($postId, wp_update_post(['ID' => $postId, 'post_content' => $editedContent], true));
            $this->assertSame($editedContent, get_post($postId)->post_content);
            $this->assertSame(0, cocoon_product_block_update_post_content($postId, $snapshot->post_content, '<p>自動更新後の本文</p>'));

            // キャッシュを経由しない、データベースに保存された本文の確認
            clean_post_cache($postId);
            $this->assertSame($editedContent, get_post($postId)->post_content);
        }
    }

    /**
     * 公開状態や投稿タイプが変わり、自動更新の対象外となった本文の保持
     */
    public function test_skips_when_a_post_is_no_longer_a_cron_target(): void
    {
        $originalContent = '<p>公開済みの本文</p>';
        foreach (['post_status' => 'draft', 'post_type' => 'wp_block'] as $field => $value) {
            $postId = $this->createPost([
                'post_status' => 'publish',
                'post_content' => $originalContent,
            ]);
            $snapshot = get_post($postId);

            $this->assertSame($postId, wp_update_post(['ID' => $postId, $field => $value], true));
            $this->assertSame($value, get_post($postId)->$field);
            $this->assertSame(0, cocoon_product_block_update_post_content($postId, $snapshot->post_content, '<p>自動更新後の本文</p>'));
            clean_post_cache($postId);
            $this->assertSame($originalContent, get_post($postId)->post_content);
        }
    }

    /**
     * 本文列に保存できない文字が含まれる場合の書き戻し拒否
     */
    public function test_rejects_invalid_content_without_writing_it(): void
    {
        $originalContent = '<p>保存済みの本文</p>';
        $postId = $this->createPost([
            'post_status' => 'publish',
            'post_content' => $originalContent,
        ]);

        $this->assertFalse(cocoon_product_block_update_post_content($postId, $originalContent, "<p>Invalid byte \xFF</p>"));
        clean_post_cache($postId);
        $this->assertSame($originalContent, get_post($postId)->post_content);
    }

    /**
     * 長い本文を更新するときのSQLへの元本文の重複防止
     */
    public function test_does_not_duplicate_the_content_in_the_update_query(): void
    {
        global $wpdb;

        $originalContent = '<p>' . str_repeat('長い記事本文', 2048) . '</p>';
        $updatedContent = $originalContent . '<p>更新後の商品情報</p>';
        $postId = $this->createPost([
            'post_status' => 'publish',
            'post_content' => $originalContent,
        ]);
        $snapshot = get_post($postId);

        // 更新SQLに長い本文が重複して含まれるかの確認
        $updateLength = 0;
        $captureUpdate = static function ($sql) use ($wpdb, &$updateLength) {
            if (strpos($sql, "UPDATE `{$wpdb->posts}`") === 0) {
                $updateLength = strlen($sql);
            }
            return $sql;
        };
        add_filter('query', $captureUpdate);
        try {
            $this->assertSame(1, cocoon_product_block_update_post_content($postId, $snapshot->post_content, $updatedContent));
        } finally {
            remove_filter('query', $captureUpdate);
        }

        $this->assertGreaterThan(strlen($updatedContent), $updateLength);
        $this->assertLessThan(strlen($updatedContent) + 1024, $updateLength);
        $this->assertSame($updatedContent, get_post($postId)->post_content);
    }
}
