<?php
/**
 * 設定ページのバリデーション関連テスト
 *
 * テーマオプションの取得・保存、各種設定値のバリデーションをテストします。
 * WordPress 関数は Brain\Monkey でモック化しています。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class SettingsValidationTest extends TestCase
{
    // utils.php はブートストラップで読み込み済み

    // ========================================================================
    // カラーコードのバリデーション（is_dark_hexcolor を活用）
    // ========================================================================

    /**
     * @dataProvider colorCodeProvider
     */
    public function test_colorcode_to_rgb_各種カラーコード(string $hex, int $r, int $g, int $b): void
    {
        $result = colorcode_to_rgb($hex);
        $this->assertSame($r, $result['red']);
        $this->assertSame($g, $result['green']);
        $this->assertSame($b, $result['blue']);
    }

    public static function colorCodeProvider(): array
    {
        return [
            '黒'     => ['#000000', 0, 0, 0],
            '白'     => ['#FFFFFF', 255, 255, 255],
            '赤'     => ['#FF0000', 255, 0, 0],
            '緑'     => ['#00FF00', 0, 255, 0],
            '青'     => ['#0000FF', 0, 0, 255],
            'Cocoonブルー' => ['#1e73be', 30, 115, 190],
            'グレー'  => ['#808080', 128, 128, 128],
        ];
    }

    // ========================================================================
    // URL バリデーション
    // ========================================================================

    /**
     * @dataProvider urlDelimiterProvider
     */
    public function test_add_delimiter_各種URLパターン(string $input, string $expected): void
    {
        $this->assertSame($expected, add_delimiter_to_url_if_last_nothing($input));
    }

    public static function urlDelimiterProvider(): array
    {
        return [
            'スラッシュなし'     => ['https://example.com', 'https://example.com/'],
            'スラッシュあり'     => ['https://example.com/', 'https://example.com/'],
            'パス付き'          => ['https://example.com/path', 'https://example.com/path/'],
            'パス付きスラッシュ' => ['https://example.com/path/', 'https://example.com/path/'],
            'ファイル名付き'     => ['https://example.com/file.html', 'https://example.com/file.html/'],
        ];
    }

    // ========================================================================
    // チェックボックス値のバリデーション
    // ========================================================================

    /**
     * @dataProvider checkboxValueProvider
     */
    public function test_is_field_checkbox_value_default_各種値(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, is_field_checkbox_value_default($value));
    }

    public static function checkboxValueProvider(): array
    {
        return [
            '空文字列はデフォルト'   => ['', true],
            'nullはデフォルト'       => [null, true],
            '1はデフォルトでない'    => ['1', false],
            '0はデフォルトでない'    => ['0', false],
            'trueはデフォルトでない' => [true, false],
            'falseはデフォルトでない' => [false, false],
            'テキストはデフォルトでない' => ['text', false],
        ];
    }

    // ========================================================================
    // サニタイズ関数の包括テスト
    // ========================================================================

    /**
     * @dataProvider commaTextProvider
     */
    public function test_sanitize_comma_text_各種パターン(string $input, string $expected): void
    {
        $this->assertSame($expected, sanitize_comma_text($input));
    }

    public static function commaTextProvider(): array
    {
        return [
            '正常なカンマ区切り'     => ['1,2,3', '1,2,3'],
            'スペース付き'           => ['1, 2, 3', '1,2,3'],
            '全角カンマ'             => ['1、2、3', '1,2,3'],
            '混在'                   => ['aaa、 bbb、 ccc', 'aaa,bbb,ccc'],
            '前後空白'               => ['  1,2  ', '1,2'],
            '空文字列'               => ['', ''],
            'カテゴリID風'           => ['10, 20, 30, 40', '10,20,30,40'],
        ];
    }
}
