<?php
/**
 * CSV・JSONサンプルスキンの実ファイル読込と翻訳タイミングの検証
 */
namespace Cocoon\Tests\Integration;

class SkinOptionTranslationIntegrationTest extends IntegrationTestCase
{
    /**
     * CSVスキンの設定を即時適用し翻訳のみテーマ初期化後に実行することの確認
     */
    public function testCsvSkinDefersTranslation(): void
    {
        $this->assertSkinTranslationTiming('skin-demo-csv', '#f09199');
    }

    /**
     * JSONスキンの設定を即時適用し翻訳のみテーマ初期化後に実行することの確認
     */
    public function testJsonSkinDefersTranslation(): void
    {
        $this->assertSkinTranslationTiming('skin-demo-json', '#19448e');
    }

    private function assertSkinTranslationTiming(string $skin, string $color): void
    {
        global $_THEME_OPTIONS, $wp_actions;
        $options = $_THEME_OPTIONS;
        $actions = $wp_actions;
        $translationCalls = [];
        $skinFilter = static function () use ($skin) {
            return get_cocoon_template_directory_uri() . '/skins/' . $skin . '/style.css';
        };
        $translationFilter = static function ($translation, $text) use (&$translationCalls) {
            $translationCalls[] = $text;
            return 'translated: ' . $text;
        };
        add_filter('get_skin_url', $skinFilter);
        add_filter('gettext_' . THEME_NAME, $translationFilter, 10, 2);

        try {
            // テーマ読込時点の再現と実際のCSV・JSON設定読込
            unset($wp_actions['after_setup_theme'], $wp_actions['init']);
            $_THEME_OPTIONS = [];
            cocoon_skin_settings();
            $this->assertSame([], $translationCalls, 'テーマ初期化前に翻訳関数を呼び出さないこと');
            $this->assertSame($color, $_THEME_OPTIONS['site_key_color']);
            $this->assertSame('vertical_card_2', $_THEME_OPTIONS['entry_card_type']);
            $this->assertSame('スキンから入力したタイトル', $_THEME_OPTIONS['appeal_area_title']);
            $this->assertSame(20, has_action('after_setup_theme', 'translate_loaded_skin_options'));

            // 翻訳カタログ登録後のコールバックと遅延読込経路の検証
            $wp_actions['after_setup_theme'] = 1;
            translate_loaded_skin_options();
            $this->assertSame('translated: スキンから入力したタイトル', $_THEME_OPTIONS['appeal_area_title']);
            $this->assertSame('translated: スキンから入力したアピールエリアメッセージです。', $_THEME_OPTIONS['appeal_area_message']);
            $this->assertSame('translated: スキンボタンキャプション', $_THEME_OPTIONS['appeal_area_button_message']);
            if ($skin === 'skin-demo-csv') {
                $this->assertSame('translated: <div class="blank-box bb-red">誹謗中傷は予告なく削除します</div>', $_THEME_OPTIONS['comment_information_message']);
            }
            $translatedOptions = $_THEME_OPTIONS;
            translate_loaded_skin_options();
            $this->assertSame($translatedOptions, $_THEME_OPTIONS);
            $_THEME_OPTIONS = [];
            cocoon_skin_settings();
            $this->assertSame($translatedOptions, $_THEME_OPTIONS);
        } finally {
            $_THEME_OPTIONS = $options;
            $wp_actions = $actions;
            remove_filter('get_skin_url', $skinFilter);
            remove_filter('gettext_' . THEME_NAME, $translationFilter, 10);
        }
    }
}
