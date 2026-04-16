<?php
/**
 * 管理画面のフィルター（絞り込み）系のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class AdminFiltersTest extends TestCase
{
    private static $admin_loaded = false;

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::$admin_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/admin.php';
            self::$admin_loaded = true;
        }
    }

    public function test_customize_restrict_manage_posts_ユーザー絞り込みにwho_authorsを指定する(): void
    {
        global $post_type, $tag;
        $post_type = 'post';
        $tag = '';

        // タグの絞り込み機能のモック
        Functions\expect('is_object_in_taxonomy')
            ->once()
            ->with('post', 'post_tag')
            ->andReturn(false); // タグ側のドロップダウンのテストは省略

        // wp_dropdown_users関数が呼ばれる際、'who' => 'authors'が含まれているかを検証
        Functions\expect('wp_dropdown_users')
            ->once()
            ->with(\Mockery::on(function ($args) {
                return is_array($args) &&
                       isset($args['name']) && $args['name'] === 'author' &&
                       isset($args['who']) && $args['who'] === 'authors';
            }));

        custmuize_restrict_manage_posts();
        
        $this->assertTrue(true); // Mockeryのアサーションを実行するためのおまじない
    }
}
