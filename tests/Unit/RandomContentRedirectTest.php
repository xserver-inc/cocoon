<?php
/**
 * ランダム投稿転送の標準動作・設定保存・URLルール更新の回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

// WordPress 7.2のAPIの有無をテストごとに分離するための別プロセス実行
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class RandomContentRedirectTest extends TestCase
{
    private array $savedGlobals = [];
    private array $savedPost = [];
    private array $rewriteFlushes = [];
    private array $optionWrites = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedPost = $_POST;
        $_POST = [];
        foreach (['_THEME_OPTIONS', 'test_theme_mods', 'test_mock_options', 'test_mock_apply_filters_callbacks', 'test_mock_translations'] as $name) {
            $this->savedGlobals[$name] = [array_key_exists($name, $GLOBALS), $GLOBALS[$name] ?? null];
            $GLOBALS[$name] = [];
        }
        Functions\when('set_theme_mod')->alias(static function ($name, $value) {
            $GLOBALS['test_theme_mods'][$name] = $value;
        });
        Functions\when('update_option')->alias(function ($name, $value, $autoload = null) {
            $this->optionWrites[] = [$name, $value, $autoload];
            $GLOBALS['test_mock_options'][$name] = $value;
            return true;
        });
        Functions\when('flush_rewrite_rules')->alias(function ($hard = true) {
            $this->rewriteFlushes[] = $hard;
        });
        require_once dirname(__DIR__, 2) . '/lib/page-settings/others-funcs.php';
        require_once dirname(__DIR__, 2) . '/lib/html-forms.php';
    }

    protected function tearDown(): void
    {
        $_POST = $this->savedPost;
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
     * 設定の未保存時に無効化せずWordPressと他プラグインの状態に従う確認
     */
    public function testDefaultPreservesWordPressBehavior(): void
    {
        $this->assertFalse(is_random_content_redirect_disabled());
        $this->assertTrue(cocoon_filter_random_content_redirect_enabled(true));
        $this->assertFalse(cocoon_filter_random_content_redirect_enabled(false));
    }

    /**
     * 無効化のチェック状態と既存の有効状態を組み合わせた転送可否の確認
     */
    #[DataProvider('redirectStateProvider')]
    public function testFilterRespectsDisabledSetting(string $saved, bool $enabled, bool $expected): void
    {
        $GLOBALS['test_theme_mods'][OP_RANDOM_CONTENT_REDIRECT_DISABLE] = $saved;
        $this->assertSame($expected, cocoon_filter_random_content_redirect_enabled($enabled));
    }

    public static function redirectStateProvider(): array
    {
        return [
            '未チェックと標準有効' => ['', true, true],
            '未チェックと他プラグインでの無効化' => ['', false, false],
            'チェック済みと標準有効' => ['1', true, false],
            'チェック済みと他プラグインでの無効化' => ['1', false, false],
        ];
    }

    /**
     * チェックとチェック解除の両方が保存処理から反映されることの確認
     */
    public function testSavePersistsCheckedAndUncheckedValues(): void
    {
        $this->supportRandomContentRedirect();
        $_POST[OP_RANDOM_CONTENT_REDIRECT_DISABLE] = '1';
        require dirname(__DIR__, 2) . '/lib/page-settings/others-posts.php';
        $this->assertTrue(is_random_content_redirect_disabled());

        $_POST = [];
        require dirname(__DIR__, 2) . '/lib/page-settings/others-posts.php';
        $this->assertFalse(is_random_content_redirect_disabled());
    }

    /**
     * 旧WordPressでの設定非表示・保存済み値の維持・URLルール更新の回避の確認
     */
    public function testUnsupportedWordPressPreservesStoredSetting(): void
    {
        $GLOBALS['test_theme_mods'][OP_RANDOM_CONTENT_REDIRECT_DISABLE] = '1';
        $this->assertFalse(cocoon_supports_random_content_redirect());
        $this->assertStringNotContainsString('name="random_content_redirect_disable"', $this->renderForm());
        require dirname(__DIR__, 2) . '/lib/page-settings/others-posts.php';
        $this->assertTrue(is_random_content_redirect_disabled());

        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertSame([], $this->rewriteFlushes);
        $this->assertSame([], $this->optionWrites);
    }

    /**
     * 初期状態のチェックなし表示とWordPress 7.2以降の注意書きの確認
     */
    public function testSupportedFormIsInitiallyUnchecked(): void
    {
        $this->supportRandomContentRedirect();
        $html = $this->renderForm();
        $checkbox = $this->getCheckbox($html);
        $this->assertFalse($checkbox->hasAttribute('checked'));
        $this->assertStringContainsString('WordPress 7.2以降', $html);
        $this->assertStringContainsString('未チェックの場合はWordPressの標準動作と他のプラグインの設定に従います。', $html);

        $GLOBALS['test_theme_mods'][OP_RANDOM_CONTENT_REDIRECT_DISABLE] = '1';
        $this->assertTrue($this->getCheckbox($this->renderForm())->hasAttribute('checked'));
    }

    /**
     * 初期状態で余分なURLルール更新や状態保存が発生しないことの確認
     */
    public function testDefaultDoesNotRegenerateRewriteRules(): void
    {
        $this->supportRandomContentRedirect();
        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertSame([], $this->rewriteFlushes);
        $this->assertSame([], $this->optionWrites);
    }

    /**
     * 無効化と標準状態への復帰の各変更時だけURLルールが更新されることの確認
     */
    public function testRewriteRulesAreUpdatedOncePerSettingChange(): void
    {
        $this->supportRandomContentRedirect();
        $GLOBALS['test_theme_mods'][OP_RANDOM_CONTENT_REDIRECT_DISABLE] = '1';
        cocoon_sync_random_content_redirect_rewrite_rules();
        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertSame([false], $this->rewriteFlushes);
        $this->assertSame([['cocoon_random_content_redirect_disabled', true, false]], $this->optionWrites);

        unset($GLOBALS['test_theme_mods'][OP_RANDOM_CONTENT_REDIRECT_DISABLE]);
        cocoon_sync_random_content_redirect_rewrite_rules();
        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertSame([false, false], $this->rewriteFlushes);
        $this->assertSame(['cocoon_random_content_redirect_disabled', false, false], $this->optionWrites[1]);
    }

    /**
     * データベースから文字列として取得した状態との比較時に再更新しないことの確認
     */
    public function testStoredStringStateDoesNotRepeatRewriteUpdates(): void
    {
        $this->supportRandomContentRedirect();
        $GLOBALS['test_theme_mods'][OP_RANDOM_CONTENT_REDIRECT_DISABLE] = '1';
        $GLOBALS['test_mock_options']['cocoon_random_content_redirect_disabled'] = '1';
        cocoon_sync_random_content_redirect_rewrite_rules();
        $this->assertSame([], $this->rewriteFlushes);
        $this->assertSame([], $this->optionWrites);
    }

    private function supportRandomContentRedirect(): void
    {
        Functions\when('wp_is_random_content_redirect_enabled')->justReturn(true);
        $this->assertTrue(cocoon_supports_random_content_redirect());
    }

    private function renderForm(): string
    {
        ob_start();
        try {
            require dirname(__DIR__, 2) . '/lib/page-settings/others-forms.php';
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private function getCheckbox(string $html): \DOMElement
    {
        $document = new \DOMDocument();
        $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET);
        $elements = (new \DOMXPath($document))->query('//input[@name="random_content_redirect_disable"]');
        $this->assertCount(1, $elements);
        return $elements->item(0);
    }
}
