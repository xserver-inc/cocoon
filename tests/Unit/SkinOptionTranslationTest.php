<?php
/**
 * スキン設定の翻訳タイミングと対象値の回帰テスト
 */
namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class SkinOptionTranslationTest extends TestCase
{
    private array $savedGlobals = [];
    private int $setupCount = 0;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['_THEME_OPTIONS', 'test_mock_translations'] as $name) {
            $this->savedGlobals[$name] = [array_key_exists($name, $GLOBALS), $GLOBALS[$name] ?? null];
        }
        $GLOBALS['test_mock_translations'] = [
            '<div class="blank-box bb-red">誹謗中傷は予告なく削除します</div>' => 'Comment notice',
            'スキンから入力したタイトル' => 'Skin title',
            'スキンから入力したアピールエリアメッセージです。' => 'Skin message',
            'スキンボタンキャプション' => 'Skin button',
        ];
        Functions\when('did_action')->alias(function ($hook) {
            $this->assertSame('after_setup_theme', $hook);
            return $this->setupCount;
        });
    }

    protected function tearDown(): void
    {
        foreach ($this->savedGlobals as $name => [$existed, $value]) {
            if ($existed) {
                $GLOBALS[$name] = $value;
            } else {
                unset($GLOBALS[$name]);
            }
        }
        parent::tearDown();
    }

    /**
     * テーマ初期化前の原文維持と初期化後の全サンプル文言の翻訳確認
     */
    public function testTranslationWaitsForThemeSetup(): void
    {
        $names = ['comment_information_message', 'appeal_area_title', 'appeal_area_message', 'appeal_area_button_message'];
        foreach (array_combine($names, array_keys($GLOBALS['test_mock_translations'])) as $name => $value) {
            $this->assertSame($value, translate_skin_option_value($name, $value));
            $this->setupCount = 1;
            $this->assertSame($GLOBALS['test_mock_translations'][$value], translate_skin_option_value($name, $value));
            $this->setupCount = 0;
        }
    }

    /**
     * 対象外の設定名・独自文言・文字列以外の値の維持確認
     */
    public function testUnrelatedValuesRemainUnchanged(): void
    {
        foreach ([0, 1] as $count) {
            $this->setupCount = $count;
            $this->assertSame('スキンから入力したタイトル', translate_skin_option_value('site_key_color', 'スキンから入力したタイトル'));
            foreach (['独自のタイトル', '', null, false, 12, ['スキンから入力したタイトル']] as $value) {
                $this->assertSame($value, translate_skin_option_value('appeal_area_title', $value));
            }
        }
    }

    /**
     * 先行読込済みの設定の再翻訳と再実行時の値の維持確認
     */
    public function testLoadedOptionsAreTranslatedAfterSetup(): void
    {
        $GLOBALS['_THEME_OPTIONS'] = ['appeal_area_title' => 'スキンから入力したタイトル', 'site_key_color' => '#f09199'];
        translate_loaded_skin_options();
        $this->assertSame('スキンから入力したタイトル', $GLOBALS['_THEME_OPTIONS']['appeal_area_title']);
        $this->setupCount = 1;
        translate_loaded_skin_options();
        $expected = ['appeal_area_title' => 'Skin title', 'site_key_color' => '#f09199'];
        $this->assertSame($expected, $GLOBALS['_THEME_OPTIONS']);
        translate_loaded_skin_options();
        $this->assertSame($expected, $GLOBALS['_THEME_OPTIONS']);
    }

    /**
     * スキン設定の未初期化時や空配列での安全な終了確認
     */
    public function testEmptyOptionsRemainUnchanged(): void
    {
        foreach ([null, false, []] as $value) {
            $GLOBALS['_THEME_OPTIONS'] = $value;
            translate_loaded_skin_options();
            $this->assertSame($value, $GLOBALS['_THEME_OPTIONS']);
        }
    }
}
