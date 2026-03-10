<?php
/**
 * 管理画面カスタム列のユニットテスト
 *
 * lib/admin.php で登録している manage_*_columns 系のコールバックが
 * 期待どおりの列を返すことを検証します。
 * WordPress 7.0 の DataViews 移行後も、従来のフィルター経由で
 * これらのコールバックが呼ばれる限り同じ挙動になることを確認する目的です。
 *
 * @see docs/wp70-dataviews-admin-columns-test.md 手動テスト手順
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class AdminColumnsTest extends TestCase
{
    private static $admin_loaded = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stubAdminColumnDependencies();
        if (!self::$admin_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/admin.php';
            self::$admin_loaded = true;
        }
    }

    /**
     * 列コールバックが依存する関数をスタブ（admin.php 読み込み時に必要なもの含む）
     */
    private function stubAdminColumnDependencies(): void
    {
        $screen = (object) ['id' => 'edit-post'];
        Functions\when('get_current_screen')->justReturn($screen);
        Functions\when('is_admin_list_author_visible')->justReturn(true);
        Functions\when('is_admin_list_categories_visible')->justReturn(true);
        Functions\when('is_admin_list_tags_visible')->justReturn(true);
        Functions\when('is_admin_list_comments_visible')->justReturn(true);
        Functions\when('is_admin_list_date_visible')->justReturn(true);
        Functions\when('is_admin_list_post_id_visible')->justReturn(true);
        Functions\when('is_admin_list_word_count_visible')->justReturn(true);
        Functions\when('is_admin_list_pv_visible')->justReturn(true);
        Functions\when('is_admin_list_eyecatch_visible')->justReturn(true);
        Functions\when('is_admin_list_memo_visible')->justReturn(true);
        // is_admin_tool_menu_visible / is_user_administrator は wp-mock-functions / utils で定義済みのためスタブ不可
    }

    // ========================================================================
    // manage_posts_columns / manage_pages_columns
    // ========================================================================

    public function test_customize_admin_manage_posts_columns_カスタム列IDを追加する(): void
    {
        $columns = [
            'cb'     => '<input type="checkbox" />',
            'title'  => 'タイトル',
            'author' => '作成者',
            'date'   => '日付',
        ];
        $result = customize_admin_manage_posts_columns($columns);

        $this->assertArrayHasKey('post-id', $result);
        $this->assertArrayHasKey('word-count', $result);
        $this->assertArrayHasKey('pv', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('memo', $result);
    }

    public function test_customize_admin_manage_posts_columns_visibilityがfalseの列は追加しない(): void
    {
        Functions\when('is_admin_list_post_id_visible')->justReturn(false);
        Functions\when('is_admin_list_word_count_visible')->justReturn(false);
        Functions\when('is_admin_list_pv_visible')->justReturn(false);
        Functions\when('is_admin_list_eyecatch_visible')->justReturn(false);
        Functions\when('is_admin_list_memo_visible')->justReturn(false);

        $columns = ['title' => 'タイトル', 'date' => '日付'];
        $result = customize_admin_manage_posts_columns($columns);

        $this->assertArrayNotHasKey('post-id', $result);
        $this->assertArrayNotHasKey('word-count', $result);
        $this->assertArrayNotHasKey('pv', $result);
        $this->assertArrayNotHasKey('thumbnail', $result);
        $this->assertArrayNotHasKey('memo', $result);
    }

    public function test_customize_admin_manage_posts_columns_wp_block画面ではPVとアイキャッチとメモを出さない(): void
    {
        Functions\when('get_current_screen')->justReturn((object) ['id' => 'edit-wp_block']);
        Functions\when('is_admin_list_post_id_visible')->justReturn(true);
        Functions\when('is_admin_list_pv_visible')->justReturn(true);
        Functions\when('is_admin_list_eyecatch_visible')->justReturn(true);
        Functions\when('is_admin_list_memo_visible')->justReturn(true);

        $columns = ['title' => 'タイトル'];
        $result = customize_admin_manage_posts_columns($columns);

        $this->assertArrayNotHasKey('pv', $result);
        $this->assertArrayNotHasKey('thumbnail', $result);
        $this->assertArrayNotHasKey('memo', $result);
    }

    // ========================================================================
    // manage_edit-post_sortable_columns / manage_edit-page_sortable_columns
    // ========================================================================

    public function test_sort_post_columns_IDをソート可能にしている(): void
    {
        $columns = ['title' => 'タイトル', 'date' => '日付'];
        $result = sort_post_columns($columns);

        $this->assertArrayHasKey('post-id', $result);
        $this->assertSame('ID', $result['post-id']);
    }

    // ========================================================================
    // manage_edit-category_columns / manage_edit-post_tag_columns
    // ========================================================================

    public function test_add_taxonomy_columns_ID列を追加する(): void
    {
        $columns = ['name' => '名前', 'slug' => 'スラッグ', 'posts' => '投稿数'];
        $result = add_taxonomy_columns($columns);

        $this->assertArrayHasKey('id', $result);
        $this->assertSame('ID', $result['id']);
    }

    public function test_add_taxonomy_custom_fields_id列でterm_idを返す(): void
    {
        $content = add_taxonomy_custom_fields('', 'id', 42);
        $this->assertSame(42, $content);
    }

    public function test_add_taxonomy_custom_fields_他の列はそのまま(): void
    {
        $content = add_taxonomy_custom_fields('もともとの内容', 'name', 1);
        $this->assertSame('もともとの内容', $content);
    }

    public function test_sort_term_columns_IDをソート可能にしている(): void
    {
        $columns = ['name' => '名前', 'slug' => 'スラッグ'];
        $result = sort_term_columns($columns);

        $this->assertArrayHasKey('id', $result);
        $this->assertSame('ID', $result['id']);
    }

    // ========================================================================
    // manage_edit-wp_block_columns / manage_wp_block_posts_custom_column
    // ========================================================================

    public function test_add_wp_block_columns_ショートコード列を追加する(): void
    {
        $columns = ['title' => 'タイトル', 'author' => '作成者', 'date' => '日付'];
        $result = add_wp_block_columns($columns);

        $this->assertArrayHasKey('shortcode', $result);
    }

    public function test_add_wp_block_custom_fields_shortcode列でショートコードを出力する(): void
    {
        $this->expectOutputRegex('/\[pattern id=.*123.*\]/');
        add_wp_block_custom_fields('shortcode', 123);
    }

    public function test_add_wp_block_custom_fields_shortcode以外の列は出力しない(): void
    {
        ob_start();
        add_wp_block_custom_fields('title', 123);
        $out = ob_get_clean();
        $this->assertSame('', $out);
    }
}
