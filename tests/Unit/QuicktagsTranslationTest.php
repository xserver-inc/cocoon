<?php
/**
 * 独自翻訳の特殊文字を含むQuicktags出力の回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class QuicktagsTranslationTest extends TestCase
{
    private bool $hadTranslations;
    private mixed $savedTranslations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadTranslations = array_key_exists('test_mock_translations', $GLOBALS);
        $this->savedTranslations = $GLOBALS['test_mock_translations'] ?? null;
        $GLOBALS['test_mock_translations'] = [];
        Functions\when('wp_script_is')->justReturn(true);
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
            '通常の翻訳' => ['Info (i)', 'Info (i)'],
            '引用符' => ['Boîte d\'information "test"', 'Boîte d\'information "test"'],
            'バックスラッシュ' => ['C:\\news\\related', 'C:\\news\\related'],
            '改行' => ["First\r\nSecond", "First\r\nSecond"],
            'Unicodeと絵文字' => ["情報\u{2028}関連\u{2029}😀", "情報\u{2028}関連\u{2029}😀"],
            'スクリプト終了タグ' => ['</script><script>alert("test")</script>&', '</script><script>alert("test")</script>&'],
            '空文字' => ['', ''],
            '不正UTF-8' => ["Info\xFFtext", "Info\u{FFFD}text"],
        ];
    }

    /**
     * すべての翻訳対象ボタンで特殊文字が復元され、script要素が分断されないことを検証
     */
    #[DataProvider('translatedLabels')]
    public function test_translations_survive_script_output(string $translated, string $expected): void
    {
        $sources = ['ふりがな', '太字', '赤字', '太い赤字', '赤アンダー', '黄色マーカー', '黄色アンダーマーカー', '打ち消し線', 'バッジ', 'キーボード', '情報(i)', '質問(?)', 'アラート(!)', 'primary', 'success', 'warning', 'danger'];
        $GLOBALS['test_mock_translations'] = array_fill_keys($sources, $translated);
        $output = $this->renderQuicktags();
        $buttons = $this->decodeButtons($output);

        $this->assertCount(19, $buttons);
        $this->assertSame(1, substr_count(strtolower($output), '</script>'));
        foreach ($buttons as $button) {
            if (!in_array($button[0], ['qt-pre', 'qt-sp-info'], true)) {
                $this->assertSame($expected, $button[1], $button[0]);
            }
        }
    }

    /**
     * 従来のボタン順序と挿入タグが保持されることを検証
     */
    public function test_button_order_and_inserted_tags_are_preserved(): void
    {
        $buttons = $this->decodeButtons($this->renderQuicktags());
        $expected = [
            ['qt-pre', '<pre>', '</pre>'],
            ['qt-ruby', '<ruby>', '<rt>ふりがな</rt></ruby>'],
            ['qt-bold', '<span class="bold">', '</span>'],
            ['qt-red', '<span class="red">', '</span>'],
            ['qt-bold-red', '<span class="bold-red">', '</span>'],
            ['qt-red-under', '<span class="red-under">', '</span>'],
            ['qt-marker', '<span class="marker">', '</span>'],
            ['qt-marker-under', '<span class="marker-under">', '</span>'],
            ['qt-strike', '<span class="strike">', '</span>'],
            ['qt-badge', '<span class="badge">', '</span>'],
            ['qt-keyboard-key', '<span class="keyboard-key">', '</span>'],
            ['qt-information', '<div class="information-box">', '</div>'],
            ['qt-question', '<div class="question-box">', '</div>'],
            ['qt-alert', '<div class="alert-box">', '</div>'],
            ['qt-sp-primary', '<div class="primary-box">', '</div>'],
            ['qt-sp-success', '<div class="success-box">', '</div>'],
            ['qt-sp-info', '<div class="info-box">', '</div>'],
            ['qt-sp-warning', '<div class="warning-box">', '</div>'],
            ['qt-sp-danger', '<div class="danger-box">', '</div>'],
        ];
        $this->assertSame($expected, array_map(static fn($button) => [$button[0], $button[2], $button[3]], $buttons));
    }

    /**
     * ルビの翻訳に含まれるHTMLが挿入タグではなく文字として扱われることを検証
     */
    public function test_ruby_translation_is_escaped_inside_html(): void
    {
        $GLOBALS['test_mock_translations']['ふりがな'] = '<b>Ruby</b> & "reading"';
        $buttons = $this->decodeButtons($this->renderQuicktags());
        $this->assertSame('<b>Ruby</b> & "reading"', $buttons[1][1]);
        $this->assertSame('<rt>&lt;b&gt;Ruby&lt;/b&gt; &amp; &quot;reading&quot;</rt></ruby>', $buttons[1][3]);
    }

    /**
     * Quicktagsが読み込まれていない画面ではスクリプトを出力しないことを検証
     */
    public function test_no_script_is_output_without_quicktags(): void
    {
        Functions\when('wp_script_is')->justReturn(false);
        $this->assertSame('', $this->renderQuicktags());
    }

    private function renderQuicktags(): string
    {
        ob_start();
        try {
            add_quicktags_to_text_editor();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private function decodeButtons(string $output): array
    {
        // ブラウザーへ渡すJSON引数の抽出と復元
        $this->assertSame(1, preg_match('/const buttons = (\[.*\]);/', $output, $matches));
        return json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
    }
}
