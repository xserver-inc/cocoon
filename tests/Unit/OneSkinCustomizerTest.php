<?php
/** ONEのカスタマイザープレビューと配色の回帰テスト */
namespace Cocoon\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class OneSkinCustomizerTest extends TestCase
{
    private function runFixture(array $initial, array $previews): array
    {
        // 共通WordPressスタブから分離したスキンのフック検証
        $process = proc_open(
            [PHP_BINARY, dirname(__DIR__) . '/fixtures/one-skin-customizer.php'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        $this->assertIsResource($process);
        fwrite($pipes[0], json_encode(['initial' => $initial, 'previews' => $previews], JSON_THROW_ON_ERROR));
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->assertSame(0, proc_close($process), $error);
        $this->assertSame('', $error);
        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }

    public static function settings(): iterable
    {
        yield '見出しカウント' => ['checkbox_one', 'is-count-on', true];
        yield 'PC固定メニュー' => ['checkbox_one2', 'is-pcmenu-on', true];
        yield '影' => ['checkbox_one3', 'is-shadow-on', true];
        yield 'ダーク' => ['checkbox_one4', 'is-dark-on', false];
        yield '角丸なし' => ['checkbox_one5', 'is-border-0', true];
        yield '本文明朝体' => ['checkbox_one6', 'is-main-serif', true];
        yield 'サイドバー明朝体' => ['checkbox_one7', 'is-sidebar-serif', true];
    }

    #[DataProvider('settings')]
    public function testPreviewChangesAfterSkinLoading(string $setting, string $class, bool $default): void
    {
        $values = [$default, !$default, $default, !$default];
        $result = $this->runFixture([], array_map(fn ($value) => [$setting => $value], $values));
        foreach ($values as $index => $value) {
            $this->assertContains('existing-class', $result['classes'][$index]);
            $this->assertSame($value, in_array($class, $result['classes'][$index], true));
        }
    }

    public function testAllDisabledAndAllEnabled(): void
    {
        $settings = iterator_to_array(self::settings());
        $mods = array_fill_keys(array_column($settings, 0), false);
        $result = $this->runFixture($mods, [$mods, array_fill_keys(array_keys($mods), true)]);
        $this->assertSame(['existing-class'], $result['classes'][0]);
        $this->assertSame(array_merge(['existing-class'], array_column($settings, 1)), $result['classes'][1]);
    }

    public function testUnregisteredSettingsKeepOriginalDefaults(): void
    {
        // 初期値を明示したケースとは独立した、未保存設定の検証
        $result = $this->runFixture([], [[]]);
        $this->assertSame([
            'existing-class', 'is-count-on', 'is-pcmenu-on', 'is-shadow-on',
            'is-border-0', 'is-main-serif', 'is-sidebar-serif',
        ], $result['classes'][0]);
    }

    public function testSavedCheckboxValuesKeepOriginalMeaning(): void
    {
        $keys = array_column(iterator_to_array(self::settings()), 0);
        // データベースや外部コードから取得した数値・文字列の互換性検証
        foreach ([false, 0, '0', '', true, 1, '1'] as $value) {
            $mods = array_fill_keys($keys, $value);
            $result = $this->runFixture($mods, [$mods]);
            $expected = (bool) $value
                ? array_merge(['existing-class'], array_column(iterator_to_array(self::settings()), 1))
                : ['existing-class'];
            $this->assertSame($expected, $result['classes'][0]);
        }
    }

    public function testEditorStylesSupportRemainsForAdditionalStylesheets(): void
    {
        $result = $this->runFixture([], []);
        $this->assertContains('editor-styles', $result['supports']);
    }

    public function testSkinColorOutputSuppressesOnlyParentColorOverrides(): void
    {
        $result = $this->runFixture([], []);
        $this->assertStringContainsString('background-color: #123456;', $result['css']);
        foreach (['site_background_color', 'site_key_color', 'site_key_text_color', 'site_text_color'] as $key) {
            $this->assertSame('', $result['options'][$key]);
        }
        $this->assertSame('preserved', $result['options']['unrelated_option']);
        $this->assertArrayNotHasKey('site_bakground_color', $result['options']);
    }
}
