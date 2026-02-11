<?php
/**
 * テーマセットアップの統合テスト
 *
 * WordPress 環境でテーマが正しく読み込まれ、
 * 基本機能が動作することを確認します。
 *
 * このテストは WP_TESTS_DIR が設定されている環境でのみ実行されます。
 */

namespace Cocoon\Tests\Integration;

class ThemeSetupTest extends IntegrationTestCase
{
    /**
     * テーマがアクティブであること
     */
    public function test_テーマがアクティブである(): void
    {
        $this->assertThemeActive();
    }

    /**
     * テーマのスタイルシートが存在すること
     */
    public function test_スタイルシートが存在する(): void
    {
        $stylesheet_path = get_stylesheet_directory() . '/style.css';
        $this->assertFileExists($stylesheet_path);
    }

    /**
     * テーマのfunctions.phpが正しく読み込まれていること
     */
    public function test_テーマ関数が定義されている(): void
    {
        // テーマの基本関数が定義されていることを確認
        $this->assertTrue(function_exists('get_theme_option'));
    }

    /**
     * テーマオプションの取得・設定が動作すること
     */
    public function test_テーマオプションの取得と設定(): void
    {
        // デフォルト値の取得
        $default = get_theme_mod('test_option_nonexistent', 'default_value');
        $this->assertSame('default_value', $default);

        // 値の設定と取得
        set_theme_mod('test_option_unit_test', 'test_value');
        $value = get_theme_mod('test_option_unit_test');
        $this->assertSame('test_value', $value);

        // クリーンアップ
        remove_theme_mod('test_option_unit_test');
    }

    /**
     * ウィジェットエリアが登録されていること
     */
    public function test_ウィジェットエリアが登録されている(): void
    {
        global $wp_registered_sidebars;
        $this->assertNotEmpty($wp_registered_sidebars);
    }

    /**
     * 投稿を作成してカテゴリを設定できること
     */
    public function test_投稿の作成とカテゴリ設定(): void
    {
        $post_id = $this->factory->post->create([
            'post_title'   => 'テスト投稿',
            'post_content' => '<h2>見出し1</h2><p>テスト本文</p><h2>見出し2</h2><p>本文2</p>',
            'post_status'  => 'publish',
        ]);

        $this->assertIsInt($post_id);
        $this->assertGreaterThan(0, $post_id);

        $post = get_post($post_id);
        $this->assertSame('テスト投稿', $post->post_title);

        // カテゴリ設定
        $cat_id = $this->factory->category->create(['name' => 'テストカテゴリ']);
        wp_set_post_categories($post_id, [$cat_id]);

        $categories = get_the_category($post_id);
        $this->assertNotEmpty($categories);
    }

    /**
     * ショートコードが正しく登録されていること
     */
    public function test_テーマショートコードが登録されている(): void
    {
        // テーマで定義される主要なショートコードの存在確認
        $expected_shortcodes = [
            'new_list',
            'popular_list',
            'star',
            'timeline',
            'blogcard',
        ];

        foreach ($expected_shortcodes as $shortcode) {
            $this->assertTrue(
                shortcode_exists($shortcode),
                "ショートコード '{$shortcode}' が登録されていません"
            );
        }
    }
}
