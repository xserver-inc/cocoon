<?php
/**
 * 固定フロントページのSNS画像選択とOGPメタタグの回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class SnsShareImageTest extends TestCase
{
    private const HOME_IMAGE = 'https://example.com/home.png';
    private const NO_IMAGE = 'https://example.com/no-image.png';
    private const SNS_IMAGE = 'https://example.com/sns.png';
    private const BODY_IMAGE = 'https://example.com/body.png';

    private array $savedGlobals = [];
    private string $snsImage = '';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // 単独実行と他テストの実行順序に依存しない既定画像定数の補完
        if (!defined('OGP_HOME_IMAGE_URL_DEFAULT')) {
            define('OGP_HOME_IMAGE_URL_DEFAULT', 'https://example.com/wp-content/themes/cocoon/screenshot.jpg');
        }
        if (!defined('NO_IMAGE_LARGE')) {
            define('NO_IMAGE_LARGE', 'https://example.com/wp-content/themes/cocoon/images/no-image-large.png');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 既存bootstrapが参照するグローバル変数の保存と検証用初期化
        foreach (['post', '_THEME_OPTIONS', 'test_theme_mods', 'test_mock_is_front_page', 'test_mock_is_singular', 'test_mock_apply_filters_callbacks'] as $name) {
            $this->savedGlobals[$name] = [array_key_exists($name, $GLOBALS), $GLOBALS[$name] ?? null];
        }
        $GLOBALS['post'] = (object) ['ID' => 89646, 'post_type' => 'page', 'post_content' => ''];
        $GLOBALS['_THEME_OPTIONS'] = [];
        $GLOBALS['test_theme_mods'] = [
            'ogp_home_image_url' => self::HOME_IMAGE,
            'no_image_url' => self::NO_IMAGE,
            'auto_post_thumbnail_enable' => 0,
        ];
        $GLOBALS['test_mock_is_front_page'] = true;
        $GLOBALS['test_mock_is_singular'] = true;
        $GLOBALS['test_mock_apply_filters_callbacks'] = [];

        // 実際の設定取得関数とSNS画像カスタムフィールド関数の読み込み
        require_once dirname(__DIR__, 2) . '/lib/page-settings/image-funcs.php';
        require_once dirname(__DIR__, 2) . '/lib/page-settings/ogp-funcs.php';
        require_once dirname(__DIR__, 2) . '/lib/custom-fields/sns-image-field.php';

        Functions\when('get_post_meta')->alias(function ($postId, $key, $single = false) {
            return $key === 'sns_image_url' ? $this->snsImage : '';
        });

        // 画像選択以外のメタタグ出力に必要なWordPress依存処理の代替
        Functions\when('get_ogp_description_text')->justReturn('SNS画像の検証');
        Functions\when('get_the_permalink')->justReturn('https://example.com/');
        Functions\when('get_seo_post_time')->justReturn('2026-10-14T00:00:00+09:00');
        Functions\when('get_seo_update_time')->justReturn('');
        Functions\when('get_the_category')->justReturn([]);
        Functions\when('get_the_author_twitter_id')->justReturn('');
    }

    protected function tearDown(): void
    {
        // 次のテストへ検証条件を持ち越さないグローバル変数の復元
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
     * フロントページの補完と個別画像の優先順位を両メタタグまで確認するテスト
     */
    #[DataProvider('imageSelectionProvider')]
    public function test_SNS画像の選択結果がOGPとTwitterで一致(array $condition, string $expectedImage): void
    {
        $GLOBALS['test_mock_is_front_page'] = $condition['front'] ?? true;
        $GLOBALS['post']->post_type = $condition['type'] ?? 'page';
        $GLOBALS['post']->post_content = !empty($condition['body']) ? '<img src="' . self::BODY_IMAGE . '">' : '';
        $GLOBALS['test_theme_mods']['auto_post_thumbnail_enable'] = $condition['auto'] ?? 0;
        $GLOBALS['test_theme_mods']['ogp_home_image_url'] = $condition['home'] ?? self::HOME_IMAGE;
        $GLOBALS['test_theme_mods']['no_image_url'] = $condition['no'] ?? self::NO_IMAGE;
        $this->snsImage = !empty($condition['sns']) ? self::SNS_IMAGE : '';

        $expected = $expectedImage === 'default' ? OGP_HOME_IMAGE_URL_DEFAULT : $expectedImage;
        $this->assertSame($expected, get_singular_sns_share_image_url());
        $this->assertTemplateImages($expected);
    }

    public static function imageSelectionProvider(): array
    {
        return [
            '固定フロントでホーム画像をNO IMAGEより優先' => [[], self::HOME_IMAGE],
            '固定フロントでNO IMAGE未設定' => [['no' => ''], self::HOME_IMAGE],
            '固定フロントでホーム画像未設定' => [['home' => ''], 'default'],
            '固定フロントで両画像未設定' => [['home' => '', 'no' => ''], 'default'],
            '固定フロントでSNS画像を本文とホーム画像より優先' => [['sns' => true, 'body' => true, 'auto' => 1], self::SNS_IMAGE],
            '固定フロントで自動設定ONの本文画像を優先' => [['body' => true, 'auto' => 1], self::BODY_IMAGE],
            '固定フロントで自動設定OFFの本文画像を除外' => [['body' => true, 'auto' => 0], self::HOME_IMAGE],
            '通常投稿のNO IMAGEを維持' => [['front' => false, 'type' => 'post'], self::NO_IMAGE],
            '通常固定ページのNO IMAGEを維持' => [['front' => false], self::NO_IMAGE],
            '通常投稿で自動設定ONの本文画像を優先' => [['front' => false, 'type' => 'post', 'body' => true, 'auto' => 1], self::BODY_IMAGE],
            '通常投稿で自動設定OFFの本文画像を除外' => [['front' => false, 'type' => 'post', 'body' => true], self::NO_IMAGE],
            '通常投稿でNO IMAGE未設定時のホーム画像を維持' => [['front' => false, 'type' => 'post', 'no' => ''], self::HOME_IMAGE],
            '通常固定ページで両画像未設定時の既定画像を維持' => [['front' => false, 'home' => '', 'no' => ''], 'default'],
        ];
    }

    /**
     * ホーム画像取得フィルターによる無効化時のNO IMAGE補完を確認するテスト
     */
    public function test_ホーム画像取得フィルターが空値ならNO_IMAGEへ補完(): void
    {
        $GLOBALS['test_mock_apply_filters_callbacks']['get_ogp_home_image_url'] = static fn ($image) => '';

        $this->assertSame(self::NO_IMAGE, get_singular_sns_share_image_url());
        $this->assertTemplateImages(self::NO_IMAGE);
    }

    /**
     * 個別画像取得フィルターによる上書きを両メタタグへ反映するテスト
     */
    public function test_SNS画像取得フィルターによる上書きの維持(): void
    {
        $filtered = 'https://example.com/filtered-sns.png';
        $values = [];
        $GLOBALS['test_mock_apply_filters_callbacks']['get_singular_sns_share_image_url'] = static function ($image) use ($filtered, &$values) {
            $values[] = $image;
            return $filtered;
        };

        $this->assertSame($filtered, get_singular_sns_share_image_url());
        $this->assertTemplateImages($filtered);
        $this->assertSame([self::HOME_IMAGE, self::HOME_IMAGE, self::HOME_IMAGE], $values);
    }

    /**
     * 共通のメタタグ画像フィルターによるOGPとTwitterの上書きを確認するテスト
     */
    public function test_メタタグ画像フィルターによる両テンプレートの上書き維持(): void
    {
        $filtered = 'https://example.com/filtered-card.png';
        $values = [];
        $GLOBALS['test_mock_apply_filters_callbacks']['ogp_card_ogp_image'] = static function ($image) use ($filtered, &$values) {
            $values[] = $image;
            return $filtered;
        };

        $this->assertSame(self::HOME_IMAGE, get_singular_sns_share_image_url());
        $this->assertTemplateImages($filtered);
        $this->assertSame([self::HOME_IMAGE, self::HOME_IMAGE], $values);
    }

    private function assertTemplateImages(string $expected): void
    {
        $this->assertStringContainsString(
            '<meta property="og:image" content="' . $expected . '">',
            $this->renderTemplate('header-ogp.php')
        );
        $this->assertStringContainsString(
            '<meta name="twitter:image" content="' . $expected . '">',
            $this->renderTemplate('header-twitter-card.php')
        );
    }

    private function renderTemplate(string $template): string
    {
        // 本番テンプレートの出力取得と例外時も残さない出力バッファーの後始末
        ob_start();
        try {
            include dirname(__DIR__, 2) . '/tmp/' . $template;
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
