<?php
/**
 * 内部ブログカードがリンク先の代替画像を選ぶ回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class InternalBlogcardThumbnailTest extends TestCase
{
    private array $savedGlobals = [];
    private bool $sourceHasImage = true;
    private bool $targetHasImage = true;
    private string $thumbnail = '';

    protected function setUp(): void
    {
        parent::setUp();

        // 実関数が参照する投稿・設定・フィルターの退避と検証用初期化
        foreach (['post', '_THEME_OPTIONS', 'test_theme_mods', 'test_mock_get_the_id', 'test_mock_get_post', 'test_mock_is_internal_blogcard_url', 'test_mock_is_internal_blogcard_enable', 'test_mock_apply_filters_callbacks'] as $name) {
            $this->savedGlobals[$name] = [array_key_exists($name, $GLOBALS), $GLOBALS[$name] ?? null];
        }
        $GLOBALS['post'] = (object) ['ID' => 101];
        $GLOBALS['_THEME_OPTIONS'] = [];
        $GLOBALS['test_theme_mods'] = ['no_image_url' => $this->imageUrl('no-image.png')];
        $GLOBALS['test_mock_get_the_id'] = 101;
        $GLOBALS['test_mock_get_post'] = (object) [
            'ID' => 202,
            'post_title' => 'リンク先',
            'post_date' => '2026-10-19 00:00:00',
        ];
        $GLOBALS['test_mock_is_internal_blogcard_url'] = true;
        $GLOBALS['test_mock_is_internal_blogcard_enable'] = false;
        // 画像補完フィルターへの本番関数の接続
        $GLOBALS['test_mock_apply_filters_callbacks'] = [
            'get_sized_no_image_url' => static fn ($url, $width, $height, $id, $singular) => get_categorized_no_image_url($url, $width, $height, $id, $singular),
        ];

        // 他テストで定義済みの場合は既存値を維持する画像定数の補完
        foreach (['THUMB160' => 'thumb160', 'THUMB160WIDTH' => 160, 'THUMB160WIDTH_DEF' => 160, 'THUMB160HEIGHT_DEF' => 90] as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        Functions\when('get_transient')->justReturn(202);
        Functions\when('get_taxonomies')->justReturn([]);
        Functions\when('wp_get_post_tags')->justReturn([]);
        Functions\when('get_the_category')->alias(function ($id) {
            $hasImage = $id === 202 ? $this->targetHasImage : $this->sourceHasImage;
            return $hasImage ? [(object) ['cat_ID' => $id]] : [];
        });
        Functions\when('get_the_category_eye_catch_url')->alias(fn ($id) => $this->imageUrl($id === 202 ? 'target.png' : 'source.png'));
        Functions\when('get_the_page_main_category')->justReturn(null);
        Functions\when('setup_postdata')->justReturn(true);
        Functions\when('wp_reset_query')->justReturn(null);
        Functions\when('get_the_page_meta_description')->justReturn('リンク先の説明');
        Functions\when('mysql2date')->justReturn('2026-10-19');
        Functions\when('get_site_date_format')->justReturn('Y-m-d');
        Functions\when('get_internal_blogcard_date_type')->justReturn('post_date');
        Functions\when('is_internal_blogcard_date_visible')->justReturn(false);
        Functions\when('get_the_post_thumbnail')->alias(fn () => $this->thumbnail);
        Functions\when('get_additional_internal_blogcard_classes')->justReturn('');
        Functions\when('is_internal_blogcard_target_blank')->justReturn(false);
        Functions\when('get_internal_blogcard_domain_style')->justReturn('domain');
        Functions\when('get_home_url')->justReturn('http://example.com');
        Functions\when('convert_all_lazy_load_tag')->returnArg();
        Functions\when('wp_strip_all_tags')->alias('strip_tags');

        // スタブで置き換えない本番のカード生成関数と画像補完関数の読み込み
        require_once dirname(__DIR__, 2) . '/lib/page-settings/image-funcs.php';
        require_once dirname(__DIR__, 2) . '/lib/blogcard-in.php';
    }

    protected function tearDown(): void
    {
        foreach ($this->savedGlobals as $name => [$exists, $value]) {
            if ($exists) {
                $GLOBALS[$name] = $value;
            } else {
                unset($GLOBALS[$name]);
            }
        }
        parent::tearDown();
    }

    /**
     * リンク元とリンク先の画像有無を変えて最終的なカード画像を確認するテスト
     */
    #[DataProvider('thumbnailProvider')]
    public function test_リンク先の情報だけでカード画像を選択(bool $sourceHasImage, bool $targetHasImage, bool $hasThumbnail, string $expected): void
    {
        $this->sourceHasImage = $sourceHasImage;
        $this->targetHasImage = $targetHasImage;
        $this->thumbnail = $hasThumbnail ? '<img src="' . $this->imageUrl('featured.png') . '">' : '';

        $html = url_to_internal_blogcard_tag('http://example.com/target/');
        $this->assertIsString($html);
        // ファビコンを除外したカード本体の画像URLの抽出
        preg_match('/<figure class="blogcard-thumbnail internal-blogcard-thumbnail"><img src="([^"]+)"/', $html, $matches);
        $this->assertSame($this->imageUrl($expected), $matches[1] ?? '');
    }

    /**
     * サムネイルがあるカードでは分類画像やNO IMAGEの取得を行わない確認
     */
    public function test_サムネイルがある場合は代替画像を取得しない(): void
    {
        $this->thumbnail = '<img src="' . $this->imageUrl('featured.png') . '">';
        $fallbackCalls = 0;
        $fallback = $GLOBALS['test_mock_apply_filters_callbacks']['get_sized_no_image_url'];

        // 本番の分類画像補完を維持した代替画像取得回数の監視
        $GLOBALS['test_mock_apply_filters_callbacks']['get_sized_no_image_url'] = static function (...$args) use (&$fallbackCalls, $fallback) {
            $fallbackCalls++;
            return $fallback(...$args);
        };

        $html = url_to_internal_blogcard_tag('http://example.com/target/');

        $this->assertIsString($html);
        $this->assertStringContainsString($this->thumbnail, $html);
        $this->assertSame(0, $fallbackCalls, 'サムネイルがある場合は代替画像の取得が不要');
    }

    /**
     * 代替画像も空の場合にサイトのスクリーンショットへ補完する既存動作の確認
     */
    public function test_代替画像が空の場合はスクリーンショットを取得(): void
    {
        $GLOBALS['test_mock_apply_filters_callbacks']['get_sized_no_image_url'] = static fn () => '';
        Functions\expect('get_site_screenshot_url')
            ->once()
            ->with('http://example.com/target/')
            ->andReturn($this->imageUrl('screenshot.png'));

        $html = url_to_internal_blogcard_tag('http://example.com/target/');

        $this->assertIsString($html);
        $this->assertStringContainsString($this->imageUrl('screenshot.png'), $html);
    }

    public static function thumbnailProvider(): array
    {
        return [
            '異なるカテゴリーのリンク元画像を除外' => [true, true, false, 'target.png'],
            'リンク元にカテゴリーがなくてもリンク先を補完' => [false, true, false, 'target.png'],
            'リンク先に画像がなければNO IMAGE' => [true, false, false, 'no-image.png'],
            '投稿アイキャッチの優先を維持' => [true, true, true, 'featured.png'],
        ];
    }

    private function imageUrl(string $filename): string
    {
        return 'http://example.com/' . $filename;
    }
}
