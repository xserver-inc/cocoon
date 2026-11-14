<?php
/**
 * WordPress実環境でのランダム投稿転送設定とURLルールの統合テスト
 */

namespace Cocoon\Tests\Integration;

final class RandomContentRedirectIntegrationTest extends IntegrationTestCase
{
    private bool $prepared = false;
    private array|false $savedMods;
    private mixed $savedState;
    private mixed $savedRewriteRules;
    private array $savedSkinOptions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedMods = get_theme_mods();
        $this->savedState = get_option('cocoon_random_content_redirect_disabled', null);
        $this->savedRewriteRules = get_option('rewrite_rules');
        $this->savedSkinOptions = $GLOBALS['_THEME_OPTIONS'];
        $this->prepared = true;
        $GLOBALS['_THEME_OPTIONS'] = [];
        remove_theme_mod(OP_RANDOM_CONTENT_REDIRECT_DISABLE);
        delete_option('cocoon_random_content_redirect_disabled');
    }

    protected function tearDown(): void
    {
        if (!$this->prepared) {
            parent::tearDown();
            return;
        }
        if ($this->savedMods === false) {
            delete_option('theme_mods_' . get_stylesheet());
        } else {
            update_option('theme_mods_' . get_stylesheet(), $this->savedMods);
        }
        if ($this->savedState === null) {
            delete_option('cocoon_random_content_redirect_disabled');
        } else {
            update_option('cocoon_random_content_redirect_disabled', $this->savedState, false);
        }
        $GLOBALS['_THEME_OPTIONS'] = $this->savedSkinOptions;
        parent::tearDown();
        update_option('rewrite_rules', $this->savedRewriteRules);
        $GLOBALS['wp_rewrite']->rules = $this->savedRewriteRules;
    }

    /**
     * 実際のフィルター登録と初期状態でのWordPress標準動作の維持の確認
     */
    public function testRegisteredFilterPreservesDefaultAndPluginSettings(): void
    {
        $this->assertSame(10, has_filter('wp_enable_random_content_redirect', 'cocoon_filter_random_content_redirect_enabled'));
        $this->assertSame(10, has_action('wp_loaded', 'cocoon_sync_random_content_redirect_rewrite_rules'));
        $this->assertSame(10, has_action('cocoon_settings_after_save', 'cocoon_sync_random_content_redirect_rewrite_rules'));
        $this->assertFalse(is_random_content_redirect_disabled());
        $this->assertTrue(apply_filters('wp_enable_random_content_redirect', true));

        add_filter('wp_enable_random_content_redirect', '__return_false', 5);
        try {
            $this->assertFalse(apply_filters('wp_enable_random_content_redirect', true));
        } finally {
            remove_filter('wp_enable_random_content_redirect', '__return_false', 5);
        }
        set_theme_mod(OP_RANDOM_CONTENT_REDIRECT_DISABLE, '1');
        $this->assertFalse(apply_filters('wp_enable_random_content_redirect', true));
    }

    /**
     * 実際のフォーム出力による初期チェック状態とWordPressバージョン対応の確認
     */
    public function testFormMatchesCoreApiAvailability(): void
    {
        ob_start();
        try {
            require get_cocoon_template_directory() . '/lib/page-settings/others-forms.php';
            $html = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $checkboxes = $this->getHtmlElementsByXPath($html, '//input[@name="random_content_redirect_disable"]');
        if (cocoon_supports_random_content_redirect()) {
            $this->assertCount(1, $checkboxes);
            $this->assertFalse($checkboxes->item(0)->hasAttribute('checked'));
            $this->assertStringContainsString('WordPress 7.2', $html);
        } else {
            $this->assertCount(0, $checkboxes);
        }
    }

    /**
     * 実際の保存処理によるチェック状態の保存とチェック解除後の標準動作への復帰の確認
     */
    public function testSavePersistsBothCheckboxStates(): void
    {
        $this->requireRandomContentRedirect();
        $savedPost = $_POST;
        try {
            $_POST = [OP_RANDOM_CONTENT_REDIRECT_DISABLE => '1'];
            require get_cocoon_template_directory() . '/lib/page-settings/others-posts.php';
            $this->assertSame('1', get_theme_mod(OP_RANDOM_CONTENT_REDIRECT_DISABLE));
            $this->assertFalse(wp_is_random_content_redirect_enabled());

            $_POST = [];
            require get_cocoon_template_directory() . '/lib/page-settings/others-posts.php';
            $this->assertFalse(is_random_content_redirect_disabled());
            $this->assertTrue(wp_is_random_content_redirect_enabled());
        } finally {
            $_POST = $savedPost;
        }
    }

    /**
     * 有効・無効の切り替え時だけ実際のランダムURLルールが再生成されることの確認
     */
    public function testSettingChangesRegenerateCoreRewriteRulesOnce(): void
    {
        $this->requireRandomContentRedirect();
        $this->setPermalinkStructure('/%postname%/');
        flush_rewrite_rules(false);
        $rule = $GLOBALS['wp_rewrite']->root . $GLOBALS['wp_rewrite']->get_random_base() . '/?$';
        $this->assertArrayHasKey($rule, get_option('rewrite_rules'));

        $regenerations = 0;
        $countRegenerations = static function ($rules) use (&$regenerations) {
            $regenerations++;
            return $rules;
        };
        add_filter('rewrite_rules_array', $countRegenerations);
        try {
            cocoon_sync_random_content_redirect_rewrite_rules();
            $this->assertSame(0, $regenerations);

            set_theme_mod(OP_RANDOM_CONTENT_REDIRECT_DISABLE, '1');
            do_action('cocoon_settings_after_save');
            $this->assertArrayNotHasKey($rule, get_option('rewrite_rules'));
            cocoon_sync_random_content_redirect_rewrite_rules();
            $this->assertSame(1, $regenerations);

            remove_theme_mod(OP_RANDOM_CONTENT_REDIRECT_DISABLE);
            cocoon_sync_random_content_redirect_rewrite_rules();
            $this->assertArrayHasKey($rule, get_option('rewrite_rules'));
            cocoon_sync_random_content_redirect_rewrite_rules();
            $this->assertSame(2, $regenerations);
        } finally {
            remove_filter('rewrite_rules_array', $countRegenerations);
        }
    }

    /**
     * 設定配列の直接復元と削除による無効化状態の変更検出の確認
     */
    public function testRestoredAndRemovedThemeModsAreDetected(): void
    {
        $this->requireRandomContentRedirect();
        update_option('theme_mods_' . get_stylesheet(), [OP_RANDOM_CONTENT_REDIRECT_DISABLE => '1']);
        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertTrue((bool) get_option('cocoon_random_content_redirect_disabled'));
        $this->assertFalse(wp_is_random_content_redirect_enabled());

        delete_option('theme_mods_' . get_stylesheet());
        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertFalse((bool) get_option('cocoon_random_content_redirect_disabled'));
        $this->assertTrue(wp_is_random_content_redirect_enabled());
    }

    private function requireRandomContentRedirect(): void
    {
        if (!cocoon_supports_random_content_redirect()) {
            $this->markTestSkipped('WordPress 7.2以降のランダム投稿転送APIが必要です。');
        }
    }
}
