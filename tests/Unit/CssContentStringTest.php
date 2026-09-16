<?php
/**
 * CSS文字列と翻訳ラベルの文字コード・フォールバックを検証する回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CssContentStringTest extends TestCase
{
    private bool $hadTranslations;
    private mixed $savedTranslations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hadTranslations = array_key_exists('test_mock_translations', $GLOBALS);
        $this->savedTranslations = $GLOBALS['test_mock_translations'] ?? null;
        $GLOBALS['test_mock_translations'] = [];
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

    public static function validStrings(): array
    {
        return [
            '英語' => ['Related posts', 'Related posts'],
            '日本語' => ['関連記事', '関連記事'],
            '空文字' => ['', ''],
            'ゼロ' => ['0', '0'],
            '引用符' => ['Reader\'s "recommended" & related', 'Reader\'s \\"recommended\\" & related'],
            'バックスラッシュ' => ['C:\\news\\related', 'C:\\\\news\\\\related'],
            '改行' => ["Related\r\nposts", 'Related posts'],
            'タグ区切り文字' => ['2 < 3 > 1', '2  3  1'],
            'Unicode区切り文字' => ["関連記事\u{2028}参考\u{2029}情報", "関連記事\u{2028}参考\u{2029}情報"],
        ];
    }

    public static function invalidUtf8Strings(): array
    {
        return [
            '不正な開始バイト' => ["Related\xFFposts"],
            '途中で切れた文字' => ["Related\xC3"],
            'サロゲート' => ["Related\xED\xA0\x80posts"],
            '過長形式' => ["Related\xC0\xB1posts"],
        ];
    }

    /**
     * 正常な文字列と意図的な空文字について従来のCSSエスケープ結果を維持することを検証
     */
    #[DataProvider('validStrings')]
    public function test_valid_strings_preserve_css_escaping(string $input, string $expected): void
    {
        $this->assertSame($expected, escape_css_content_string($input, '既定値'));
    }

    /**
     * 不正UTF-8を警告なしでフォールバックへ置換することを検証
     */
    #[DataProvider('invalidUtf8Strings')]
    public function test_invalid_utf8_uses_fallback_without_php_warnings(string $input): void
    {
        $actual = $this->withoutPhpWarnings(static function () use ($input) {
            return escape_css_content_string($input, '既定値');
        });

        $this->assertSame('既定値', $actual);
    }

    /**
     * 不正UTF-8のブログカード翻訳が日本語ラベルへ戻ることを検証
     */
    #[DataProvider('invalidUtf8Strings')]
    public function test_blogcard_labels_fall_back_to_japanese(string $input): void
    {
        $GLOBALS['test_mock_translations']['関連記事'] = $input;

        $actual = $this->withoutPhpWarnings(static function () {
            return get_blogcard_label_css_variables();
        });

        $this->assertSame(':root{--cocoon-bct-related-text:"関連記事";}', $actual);
    }

    /**
     * 不正UTF-8のスキン翻訳が日本語ラベルへ戻ることを検証
     */
    #[DataProvider('invalidUtf8Strings')]
    public function test_skin_labels_fall_back_to_japanese(string $input): void
    {
        $GLOBALS['test_mock_translations']['ランキング'] = $input;

        $actual = $this->withoutPhpWarnings(static function () {
            return get_skin_text_css_variables();
        });

        $this->assertSame(':root{--cocoon-skin-ranking-text:"ランキング";}', $actual);
    }

    /**
     * フォールバック省略時とフォールバックも不正な場合に安全な空文字へ戻ることを検証
     */
    public function test_invalid_or_missing_fallback_returns_safe_empty_string(): void
    {
        $this->assertSame('', $this->withoutPhpWarnings(static function () {
            return escape_css_content_string("\xFF");
        }));
        $this->assertSame('', $this->withoutPhpWarnings(static function () {
            return escape_css_content_string("\xFF", "\xC3");
        }));
    }

    /**
     * フォールバックのタグ・改行・引用符・バックスラッシュもCSS用に処理することを検証
     */
    public function test_fallback_is_escaped_as_css_content(): void
    {
        $fallback = "<既定>\r\n\"ラベル\"\\name";
        $actual = $this->withoutPhpWarnings(static function () use ($fallback) {
            return escape_css_content_string("\xFF", $fallback);
        });

        $this->assertSame('既定 \\"ラベル\\"\\\\name', $actual);
    }

    /**
     * 正常な翻訳・意図的な空ラベル・未翻訳の省略を両CSS生成関数で維持することを検証
     */
    public function test_label_generators_preserve_normal_empty_and_untranslated_labels(): void
    {
        $this->assertSame('', get_blogcard_label_css_variables());
        $this->assertSame('', get_skin_text_css_variables());

        $GLOBALS['test_mock_translations']['関連記事'] = 'Related posts';
        $GLOBALS['test_mock_translations']['ランキング'] = 'Ranking';
        $this->assertSame(':root{--cocoon-bct-related-text:"Related posts";}', get_blogcard_label_css_variables());
        $this->assertSame(':root{--cocoon-skin-ranking-text:"Ranking";}', get_skin_text_css_variables());

        $GLOBALS['test_mock_translations']['関連記事'] = '';
        $GLOBALS['test_mock_translations']['ランキング'] = '';
        $this->assertSame(':root{--cocoon-bct-related-text:"";}', get_blogcard_label_css_variables());
        $this->assertSame(':root{--cocoon-skin-ranking-text:"";}', get_skin_text_css_variables());
    }

    private function withoutPhpWarnings(callable $callback): string
    {
        $warnings = [];
        // 検証中のPHP警告・非推奨通知の収集と、既存エラーハンドラーの復元
        set_error_handler(static function ($severity, $message) use (&$warnings) {
            $warnings[] = $message;
            return true;
        });
        try {
            $result = $callback();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);
        return $result;
    }
}
