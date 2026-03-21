<?php
/**
 * 画像関連関数のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey;

class ImageFuncsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once dirname(__DIR__, 2) . '/lib/page-settings/image-funcs.php';
    }

    // ========================================================================
    // get_categorized_no_image_url()
    // ========================================================================

    public function test_get_categorized_no_image_url_指定された投稿IDのタグ画像を取得できる(): void
    {
        $post_id = 999;
        $url = 'default.jpg';
        $width = 120;
        $height = 68;

        // タクソノミ除外のため get_taxonomies は空を返す
        Monkey\Functions\expect('get_taxonomies')
            ->andReturn([]);

        // wp_get_post_tags をモックする
        $mock_tag = new \stdClass();
        $mock_tag->term_id = 10;
        Monkey\Functions\expect('wp_get_post_tags')
            ->once()
            ->with($post_id)
            ->andReturn([$mock_tag]);

        // get_the_tag_eye_catch_url
        Monkey\Functions\expect('get_the_tag_eye_catch_url')
            ->once()
            ->with(10)
            ->andReturn('tag-eyecatch.jpg');

        // モックせず関数をそのまま使用するため get_extention モックのみ必要かもしれません。
        // が、まずはそのまま実行させます。

        // get_the_category (以降は呼ばれても上書きされるため空を返す)
        Monkey\Functions\expect('get_the_category')
            ->andReturn([]);
        Monkey\Functions\expect('get_the_page_main_category')
            ->andReturn(null);

        $result = get_categorized_no_image_url($url, $width, $height, $post_id, true);
        
        // ファイルが存在しない場合はそのままのURLが返される仕様
        $this->assertStringContainsString('tag-eyecatch', $result);
    }
}
