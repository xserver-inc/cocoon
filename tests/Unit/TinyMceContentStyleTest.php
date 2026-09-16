<?php
/**
 * 翻訳済みブログカードCSSを含むTinyMCE初期設定の回帰テスト
 *
 * @see https://wp-cocoon.com/community/postid/89458/
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TinyMceContentStyleTest extends TestCase
{
    private bool $hadTranslations;
    private mixed $savedTranslations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hadTranslations = array_key_exists('test_mock_translations', $GLOBALS);
        $this->savedTranslations = $GLOBALS['test_mock_translations'] ?? null;
        $GLOBALS['test_mock_translations'] = [];

        Functions\when('is_visual_editor_style_enable')->justReturn(false);
        Functions\when('get_main_column_contents_width')->justReturn(0);
        Functions\when('get_main_column_padding')->justReturn(0);

        require_once dirname(__DIR__, 2) . '/lib/admin-tinymce-qtag.php';
    }

    protected function tearDown(): void
    {
        if ($this->hadTranslations) {
            $GLOBALS['test_mock_translations'] = $this->savedTranslations;
        } else {
            unset($GLOBALS['test_mock_translations']);
        }

        parent::tearDown();
    }

    public static function translatedLabels(): array
    {
        return [
            '英語' => ['No label'],
            '引用符とアンパサンド' => ['Reader\'s "recommended" & related'],
            'バックスラッシュ' => ['C:\\news\\related'],
            '改行' => ["Related\r\nposts"],
            'Unicode区切り文字' => ["関連記事\u{2028}参考\u{2029}情報"],
        ];
    }

    /**
     * 翻訳済みCSSがJavaScript文字列として読み取れ、元のCSSへ復元できることを検証
     */
    #[DataProvider('translatedLabels')]
    public function test_translated_css_survives_javascript_string_output(string $label): void
    {
        $GLOBALS['test_mock_translations']['ラベルなし'] = $label;
        $expectedCss = get_blogcard_label_css_variables();

        $settings = initialize_tinymce_styles([]);

        $this->assertNotSame('', $expectedCss);
        $this->assertSame($expectedCss, $this->decodeContentStyle($settings['content_style']));
    }

    /**
     * 他の処理が用意したエスケープ済みCSSを二重変換せず保持することを検証
     */
    public function test_existing_escaped_css_is_preserved(): void
    {
        $GLOBALS['test_mock_translations']['ラベルなし'] = 'No label';
        $existingCss = 'body::before{content:"Existing\\\\label";}';
        $existingValue = substr(json_encode($existingCss), 1, -1);

        $settings = initialize_tinymce_styles(['content_style' => $existingValue]);

        $this->assertStringStartsWith($existingValue . ' ', $settings['content_style']);
        $this->assertSame(
            $existingCss . ' ' . get_blogcard_label_css_variables(),
            $this->decodeContentStyle($settings['content_style'])
        );
    }

    /**
     * 翻訳ラベルがない場合に不要なCSS設定を追加しないことを検証
     */
    public function test_untranslated_labels_do_not_add_content_style(): void
    {
        $settings = initialize_tinymce_styles([]);

        $this->assertArrayNotHasKey('content_style', $settings);
    }

    /**
     * 日本語環境で既存のCSS設定を変更しないことを検証
     */
    public function test_untranslated_labels_preserve_existing_content_style(): void
    {
        $existingValue = 'body{color:red;}';

        $settings = initialize_tinymce_styles(['content_style' => $existingValue]);

        $this->assertSame($existingValue, $settings['content_style']);
    }

    /**
     * エディターの幅と余白を指定した場合も翻訳CSSと共存することを検証
     */
    public function test_editor_dimensions_are_preserved_with_translated_labels(): void
    {
        $GLOBALS['test_mock_translations']['ラベルなし'] = 'No label';
        Functions\when('is_visual_editor_style_enable')->justReturn(true);
        Functions\when('get_main_column_contents_width')->justReturn(640);
        Functions\when('get_main_column_padding')->justReturn(24);

        $settings = initialize_tinymce_styles([]);

        $this->assertSame(
            get_blogcard_label_css_variables() . ' #tinymce.mce-content-body{max-width:100%;width:688px;padding:1em 24px !important;}',
            $this->decodeContentStyle($settings['content_style'])
        );
    }

    private function decodeContentStyle(string $value): string
    {
        // WordPressの設定出力と同じダブルクォート囲みでの構文と復元内容の確認
        return json_decode('"' . $value . '"', true, 512, JSON_THROW_ON_ERROR);
    }
}
