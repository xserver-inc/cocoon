<?php
/**
 * ユーティリティ関数の統合テスト
 *
 * WordPress 環境でのユーティリティ関数の動作を検証します。
 * URL変換、パス変換などWordPress APIに依存する関数をテストします。
 */

namespace Cocoon\Tests\Integration;

class UtilsFunctionsTest extends IntegrationTestCase
{
    /**
     * url_to_local() が内部URLをローカルパスに変換すること
     */
    public function test_url_to_local_内部URLをローカルパスに変換する(): void
    {
        $url = site_url('/wp-content/themes/cocoon-master/style.css');
        $result = url_to_local($url);
        $this->assertIsString($result);
        $this->assertStringContainsString('cocoon-master', $result);
        $this->assertStringContainsString('style.css', $result);
    }

    /**
     * url_to_local() が外部URLに対してfalseを返すこと
     */
    public function test_url_to_local_外部URLはfalseを返す(): void
    {
        $url = 'https://external-site.com/path/to/file.css';
        $result = url_to_local($url);
        $this->assertFalse($result);
    }

    /**
     * local_to_url() がローカルパスを内部URLに変換すること
     */
    public function test_local_to_url_ローカルパスを内部URLに変換する(): void
    {
        $local = ABSPATH . 'wp-content/themes/cocoon-master/style.css';
        $result = local_to_url($local);
        $this->assertIsString($result);
        $this->assertStringContainsString('http', $result);
        $this->assertStringContainsString('style.css', $result);
    }

    /**
     * includes_site_url() が内部URLを正しく判定すること
     */
    public function test_includes_site_url_内部URLを正しく判定する(): void
    {
        $url = site_url('/wp-content/themes/cocoon-master/');
        $this->assertTrue(includes_site_url($url));
    }

    /**
     * includes_site_url() が外部URLを正しく判定すること
     */
    public function test_includes_site_url_外部URLを正しく判定する(): void
    {
        $url = 'https://external-site.com/';
        $this->assertFalse(includes_site_url($url));
    }

    /**
     * includes_home_url() が内部URLを正しく判定すること
     */
    public function test_includes_home_url_ホームURLを正しく判定する(): void
    {
        $url = home_url('/path/to/page');
        $this->assertTrue(includes_home_url($url));
    }

    /**
     * get_domain_name() がURLからドメイン名を取得すること
     */
    public function test_get_domain_name_URLからドメイン名を取得する(): void
    {
        $this->assertSame('example.com', get_domain_name('https://example.com/path'));
        $this->assertSame('sub.example.com', get_domain_name('https://sub.example.com/'));
    }

    /**
     * get_domain_name() が空のURLに対してnullを返すこと
     */
    public function test_get_domain_name_空のURLはnullを返す(): void
    {
        $this->assertNull(get_domain_name(''));
    }

    /**
     * カテゴリメタキーの生成が正しいこと
     */
    public function test_カテゴリメタキー生成_統合テスト(): void
    {
        $cat_id = $this->factory->category->create(['name' => 'テストカテゴリ']);
        $key = get_the_category_meta_key($cat_id);
    }

    // ========================================================================
    // url_to_category_object(), url_to_tag_object()
    // ========================================================================

    public function test_url_to_category_object_URLからカテゴリオブジェクトを取得できる(): void
    {
        // 事前準備: カテゴリ作成
        $cat_id = $this->factory->category->create(['name' => 'テストカテゴリ', 'slug' => 'test-cat']);
        
        // パーマリンク環境の設定 (パスベースで動かすため)
        $this->set_permalink_structure('/%postname%/');

        // URLの組み立て
        $cat_url = get_category_link($cat_id);

        $result = url_to_category_object($cat_url);
        $this->assertNotNull($result);
        $this->assertEquals($cat_id, $result->term_id);

        // クエリ形式
        $result_query = url_to_category_object(home_url('/?cat=' . $cat_id));
        $this->assertNotNull($result_query);
        $this->assertEquals($cat_id, $result_query->term_id);
    }

    public function test_url_to_tag_object_URLからタグオブジェクトを取得できる(): void
    {
        // 事前準備: タグ作成
        $tag_id = $this->factory->tag->create(['name' => 'テストタグ', 'slug' => 'test-tag']);
        
        // パーマリンク環境の設定
        $this->set_permalink_structure('/%postname%/');

        // URLの組み立て
        $tag_url = get_tag_link($tag_id);

        $result = url_to_tag_object($tag_url);
        $this->assertNotNull($result);
        $this->assertEquals($tag_id, $result->term_id);

        // クエリ形式
        $result_query = url_to_tag_object(home_url('/?tag=test-tag'));
        $this->assertNotNull($result_query);
        $this->assertEquals($tag_id, $result_query->term_id);
    }
}
