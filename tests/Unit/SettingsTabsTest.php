<?php
/**
 * 設定タブの互換性と拡張用フックの回帰テスト。
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

// 他のテストが定義するテーマ関数やrequire_onceの状態を引き継がない。
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SettingsTabsTest extends TestCase
{
    private array $original_post = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->original_post = $_POST;
        $_POST = [];
        $GLOBALS['test_mock_apply_filters_callbacks'] = [];
        Functions\when('is_amp_enable')->justReturn(false);
        Functions\when('is_pwa_enable')->justReturn(false);
        Functions\when('wp_unslash')->alias('stripslashes');
        if (!defined('HIDDEN_FIELD_NAME')) {
            define('HIDDEN_FIELD_NAME', 'cocoon_settings_nonce');
        }
        require_once dirname(__DIR__, 2) . '/lib/page-settings/settings-tabs.php';
    }

    protected function tearDown(): void
    {
        $_POST = $this->original_post;
        unset($GLOBALS['test_mock_apply_filters_callbacks'], $GLOBALS['test_mock_translations']);
        parent::tearDown();
    }

    public function test_標準タブの順序と既存フォームを維持する(): void
    {
        $expected = [
            'skin' => ['skin-forms.php'],
            'all' => ['all-forms.php'],
            'theme-header' => ['header-forms.php'],
            'ads' => ['ads-forms.php'],
            'title' => ['title-forms.php'],
            'seo' => ['seo-forms.php'],
            'ogp' => ['ogp-forms.php'],
            'analytics' => ['analytics-forms.php'],
            'column' => ['column-forms.php'],
            'index-page' => ['index-forms.php'],
            'single-page' => ['single-forms.php'],
            'page-page' => ['page-forms.php'],
            'content-page' => ['content-forms.php'],
            'toc-page' => ['toc-forms.php'],
            'sns-share' => ['sns-share-forms.php'],
            'sns-follow' => ['sns-follow-forms.php'],
            'image' => ['image-forms.php'],
            'blog-card' => ['blogcard-in-forms.php', 'blogcard-out-forms.php'],
            'code-highlight' => ['code-forms.php'],
            'comment' => ['comment-forms.php'],
            'notice-area' => ['notice-forms.php'],
            'appeal-area' => ['appeal-forms.php'],
            'recommended' => ['recommended-forms.php'],
            'carousel' => ['carousel-forms.php'],
            'footer' => ['footer-forms.php'],
            'buttons' => ['buttons-forms.php'],
            'mobile-buttons' => ['mobile-buttons-forms.php'],
            'page-404' => ['404-forms.php'],
            'admin' => ['admin-forms.php'],
            'widget' => ['widget-forms.php'],
            'widget-area' => ['widget-area-forms.php'],
            'editor' => ['editor-forms.php'],
            'apis' => ['apis-forms.php'],
            'others' => ['others-forms.php'],
            'reset' => ['reset-forms.php'],
            'about' => ['about-forms.php'],
        ];
        $tabs = cocoon_get_default_settings_tabs();
        $this->assertSame(array_keys($expected), array_keys($tabs));
        foreach ($expected as $tab_id => $forms) {
            $this->assertSame($forms, $tabs[$tab_id]['forms'], $tab_id);
            foreach ($forms as $form) {
                $this->assertFileExists(dirname(__DIR__, 2) . '/lib/page-settings/' . $form);
            }
        }

        // ナビの入力欄は独立タブではなく、ヘッダーフォームの中で読み込まれる。
        $header_form = file_get_contents(dirname(__DIR__, 2) . '/lib/page-settings/header-forms.php');
        $this->assertStringContainsString("require_once abspath(__FILE__).'navi-forms.php'", $header_form);
    }

    public function test_AMPとPWAは有効な場合だけ元の位置に表示する(): void
    {
        foreach ([[false, false], [true, false], [false, true], [true, true]] as [$amp, $pwa]) {
            Functions\when('is_amp_enable')->justReturn($amp);
            Functions\when('is_pwa_enable')->justReturn($pwa);
            $tabs = cocoon_get_default_settings_tabs();
            $this->assertSame($amp, isset($tabs['amp']));
            $this->assertSame($pwa, isset($tabs['pwa']));
            $ids = array_keys($tabs);
            $expected = ['page-404'];
            if ($amp) $expected[] = 'amp';
            if ($pwa) $expected[] = 'pwa';
            $expected[] = 'admin';
            $this->assertSame($expected, array_slice($ids, array_search('page-404', $ids, true), count($expected)));
        }
    }

    public function test_フィルターには翻訳済みラベルだけを公開する(): void
    {
        $GLOBALS['test_mock_translations']['スキン'] = 'Translated skin';
        $received = null;
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static function ($definitions) use (&$received) {
            $received = $definitions;
            return $definitions;
        };
        $tabs = cocoon_get_settings_tabs();
        $this->assertSame('Translated skin', $tabs['skin']['label']);
        foreach ($received as $definition) {
            $this->assertSame(['label'], array_keys($definition));
        }
    }

    public function test_タブ構成はスキンの上書きによらず保存済み設定で評価する(): void
    {
        $form_skin_options = ['site_key_color' => '#123456'];
        $GLOBALS['_FORM_SKIN_OPTIONS'] = $form_skin_options;
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static function ($tabs) {
            if (get_theme_option('appeal_area_display_type', 'none') !== 'none') {
                $tabs['child-appeal'] = ['label' => 'アピールエリアの拡張設定'];
            }
            return $tabs;
        };

        // GETではスキンが読み込み済み、POSTでは保存後まで未読み込みという違いを再現する。
        foreach ([
            ['GET', 'none', ['appeal_area_display_type' => 'front_page_only'], false],
            ['POST', 'none', [], false],
            ['GET', 'front_page_only', ['appeal_area_display_type' => 'none'], true],
            ['POST', 'front_page_only', [], true],
        ] as [$method, $saved_value, $skin_options, $expected]) {
            $_POST = $method === 'POST' ? [HIDDEN_FIELD_NAME => 'valid'] : [];
            $GLOBALS['test_theme_mods'] = ['appeal_area_display_type' => $saved_value];
            $GLOBALS['_THEME_OPTIONS'] = $skin_options;

            $tabs = cocoon_get_settings_tabs();

            $this->assertSame($expected, isset($tabs['child-appeal']), $method . ': ' . $saved_value);
            $this->assertSame($skin_options, $GLOBALS['_THEME_OPTIONS']);
            $this->assertSame($form_skin_options, $GLOBALS['_FORM_SKIN_OPTIONS']);
        }
    }

    public function test_タブ定義の評価中に例外が起きてもスキン状態を復元する(): void
    {
        $skin_options = ['appeal_area_display_type' => 'front_page_only'];
        $form_skin_options = ['site_key_color' => '#123456'];
        $GLOBALS['_THEME_OPTIONS'] = $skin_options;
        $GLOBALS['_FORM_SKIN_OPTIONS'] = $form_skin_options;
        $failure = new \RuntimeException('タブ定義の評価に失敗');
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static function () use ($failure) {
            throw $failure;
        };
        $this->expectExceptionObject($failure);

        // 例外を呼び出し元へ伝えた場合も、後続のフォーム描画に使う状態を保つ。
        try {
            cocoon_get_settings_tabs();
        } finally {
            $this->assertSame($skin_options, $GLOBALS['_THEME_OPTIONS']);
            $this->assertSame($form_skin_options, $GLOBALS['_FORM_SKIN_OPTIONS']);
        }
    }

    public function test_並べ替えと独自タブ追加を許可し省略された標準タブを補完する(): void
    {
        $defaults = cocoon_get_default_settings_tabs();
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static function () {
            return [
                'my-settings' => ['label' => '独自設定'],
                'seo' => ['label' => '検索エンジン設定'],
                'skin' => ['label' => []],
            ];
        };
        $tabs = cocoon_get_settings_tabs();
        $expected_ids = array_merge(['my-settings', 'seo'], array_values(array_diff(array_keys($defaults), ['seo'])));
        $this->assertSame($expected_ids, array_keys($tabs));
        $this->assertSame('検索エンジン設定', $tabs['seo']['label']);
        $this->assertSame($defaults['skin'], $tabs['skin']);
        $this->assertSame([], $tabs['my-settings']['forms']);
    }

    public function test_配列ではないフィルター結果から標準タブに復帰する(): void
    {
        $defaults = cocoon_get_default_settings_tabs();
        foreach ([null, false, 42, 'broken', (object) ['label' => '設定']] as $result) {
            $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static fn () => $result;
            $this->assertSame($defaults, cocoon_get_settings_tabs());
        }
    }

    public function test_不正なIDとラベルや無効な標準機能の再追加を拒否する(): void
    {
        $invalid = [
            'Bad-case' => ['label' => '大文字'],
            'bad id' => ['label' => '空白'],
            'bad<id' => ['label' => 'HTML'],
            "bad\n" => ['label' => '改行'],
            '../file' => ['label' => 'パス'],
            123 => ['label' => '数値ID'],
            'empty-label' => ['label' => ''],
            'space-label' => ['label' => " \t\n"],
            'array-label' => ['label' => ['文字列以外']],
            'number-label' => ['label' => 123],
            'null-label' => ['label' => null],
            'no-label' => [],
            'string-definition' => '設定',
            'amp' => ['label' => '無効AMP'],
            'pwa' => ['label' => '無効PWA'],
        ];
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static fn () => $invalid + [
            'valid_tab-2' => ['label' => '独自設定'],
        ];
        $tabs = cocoon_get_settings_tabs();
        foreach (array_keys($invalid) as $tab_id) {
            $this->assertArrayNotHasKey($tab_id, $tabs);
        }
        $this->assertArrayHasKey('valid_tab-2', $tabs);
        $this->assertCount(count(cocoon_get_default_settings_tabs()) + 1, $tabs);
    }

    public function test_フィルターからフォームや保存ファイルを指定できない(): void
    {
        $GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static function () {
            $definition = [
                'label' => '表示名',
                'forms' => ['../../functions.php'],
                'file' => '/outside.php',
                'posts' => ['reset-posts.php'],
            ];
            return ['theme-header' => $definition, 'custom' => $definition];
        };
        $tabs = cocoon_get_settings_tabs();
        $this->assertSame(['label' => '表示名', 'forms' => ['header-forms.php']], $tabs['theme-header']);
        $this->assertSame(['label' => '表示名', 'forms' => []], $tabs['custom']);
    }

    public function test_独自タブにも切り替えCSSを生成し不正IDを除外する(): void
    {
        $css = cocoon_get_settings_tabs_css([
            'skin' => ['label' => 'スキン'],
            'custom_tab-2' => ['label' => '独自設定'],
            'bad}body{' => ['label' => '不正'],
        ]);
        $this->assertStringContainsString('#tabs > #tab-skin-input:checked ~ #tab-skin-content', $css);
        $this->assertStringContainsString('#tabs > #tab-custom_tab-2-input:checked ~ #tab-custom_tab-2-content', $css);
        $this->assertStringContainsString('display: block;', $css);
        $this->assertStringNotContainsString('bad}body{', $css);
        $this->assertSame('', cocoon_get_settings_tabs_css([]));
    }

    public function test_選択値は存在するタブに限って復元する(): void
    {
        $tabs = ['custom' => ['label' => '独自設定'], 'skin' => ['label' => 'スキン']];
        $_POST['tab-input'] = 'tab-skin-input';
        $this->assertSame('skin', cocoon_get_selected_settings_tab($tabs));
        $_POST['tab-input'] = 'tab-custom-input';
        $this->assertSame('custom', cocoon_get_selected_settings_tab($tabs));
        foreach ([null, [], ['tab-skin-input'], 1, 'skin', 'tab-removed-input', 'tab-skin-input" onclick="bad'] as $invalid) {
            $_POST['tab-input'] = $invalid;
            $this->assertSame('custom', cocoon_get_selected_settings_tab($tabs));
        }
        unset($_POST['tab-input']);
        $this->assertSame('custom', cocoon_get_selected_settings_tab($tabs));
        $this->assertNull(cocoon_get_selected_settings_tab([]));
    }

    public function test_管理者権限と正しい設定nonceを確認する(): void
    {
        // 基底クラスで作成済みの権限スタブを置き換えて、確認する権限も検証する。
        Functions\when('current_user_can')->alias(function ($capability): bool {
            $this->assertSame('manage_options', $capability);
            return true;
        });
        $_POST[HIDDEN_FIELD_NAME] = "valid\\'nonce";
        Functions\expect('wp_verify_nonce')->once()->with("valid'nonce", 'settings')->andReturn(1);
        $this->assertTrue(cocoon_is_settings_post_valid());
    }

    public function test_権限不足または不正なnonce値では保存を許可しない(): void
    {
        $_POST[HIDDEN_FIELD_NAME] = 'valid';
        Functions\when('current_user_can')->justReturn(false);
        Functions\expect('wp_verify_nonce')->never();
        $this->assertFalse(cocoon_is_settings_post_valid());
        Functions\when('current_user_can')->justReturn(true);
        foreach ([null, [], ['valid'], 123] as $invalid) {
            $_POST[HIDDEN_FIELD_NAME] = $invalid;
            $this->assertFalse(cocoon_is_settings_post_valid());
        }
        unset($_POST[HIDDEN_FIELD_NAME]);
        $this->assertFalse(cocoon_is_settings_post_valid());
    }

    public function test_保存フックは有効な送信時だけ標準タブと独自タブに実行する(): void
    {
        $result = $this->runWithActionRecorder(<<<'PHP'
Functions\when('current_user_can')->alias(static fn () => $GLOBALS['can_manage']);
Functions\when('wp_unslash')->alias('stripslashes');
Functions\when('wp_verify_nonce')->alias(static fn ($nonce, $action) => $nonce === 'valid' && $action === 'settings');
define('HIDDEN_FIELD_NAME', 'cocoon_settings_nonce');
$tabs = ['skin' => ['label' => 'スキン', 'forms' => []], 'custom' => ['label' => '独自設定', 'forms' => []]];
$cases = [];
foreach ([[false, 'valid'], [true, 'invalid'], [true, ['valid']], [true, null], [true, 'valid']] as [$can_manage, $nonce]) {
    $GLOBALS['can_manage'] = $can_manage;
    $GLOBALS['recorded_actions'] = [];
    $_POST = [HIDDEN_FIELD_NAME => $nonce];
    cocoon_save_settings_tab_extensions($tabs);
    $cases[] = $GLOBALS['recorded_actions'];
}
echo json_encode($cases);
PHP);
        $this->assertSame([[], [], [], [], ['cocoon_settings_save_skin', 'cocoon_settings_save_custom']], $result);
    }

    public function test_実際のウィジェットフォームの変数がフック名を書き換えない(): void
    {
        $result = $this->runWithActionRecorder(<<<'PHP'
Functions\when('is_amp_enable')->justReturn(false);
Functions\when('is_pwa_enable')->justReturn(false);
Functions\when('get_help_page_tag')->justReturn('');
Functions\when('generate_label_tag')->justReturn(null);
Functions\when('get_exclude_widget_area_ids')->justReturn([]);
Functions\when('generate_checkbox_tag')->alias(static function ($name, $value, $label, $id): void {
    echo '<input value="' . $id . '">';
});
define('OP_EXCLUDE_WIDGET_AREA_IDS', 'exclude_widget_area_ids');
$GLOBALS['wp_registered_sidebars'] = ['sidebar-1' => ['name' => 'サイドバー', 'description' => '説明']];
$tabs = cocoon_get_default_settings_tabs();
ob_start();
cocoon_render_settings_tab_content('widget-area', $tabs['widget-area']);
$html = ob_get_clean();
cocoon_render_settings_tab_content('custom', ['label' => '独自設定', 'forms' => []]);
echo json_encode(['html' => $html, 'actions' => $GLOBALS['recorded_actions']]);
PHP);
        $this->assertStringContainsString('id="widget-area-page"', $result['html']);
        $this->assertStringContainsString('value="sidebar-1"', $result['html']);
        $this->assertSame(['cocoon_settings_tab_content_widget-area', 'cocoon_settings_tab_content_custom'], $result['actions']);
    }

    public function test_設定ページはラベルをエスケープし選択値を安全に復元する(): void
    {
        $pages = $this->runWithActionRecorder(<<<'PHP'
// 通常の関数宣言はスクリプトの実行前に登録され、標準フォームの読み込みだけを置き換える。
function cocoon_render_settings_tab_content($tab_id, $tab) {}
Functions\when('is_amp_enable')->justReturn(false);
Functions\when('is_pwa_enable')->justReturn(false);
Functions\when('current_user_can')->justReturn(false);
Functions\when('submit_button')->justReturn(null);
Functions\when('wp_create_nonce')->justReturn('test-nonce');
Functions\when('get_template_part')->justReturn(null);
define('HIDDEN_FIELD_NAME', 'cocoon_settings_nonce');
define('SELECT_INDEX_NAME', 'select_index');
$_GET = [];
$GLOBALS['_THEME_OPTIONS'] = [];
$GLOBALS['test_mock_apply_filters_callbacks']['cocoon_settings_tabs'] = static function ($tabs) {
    return ['custom' => ['label' => '<img src=x onerror=alert(1)> & "独自"']] + $tabs;
};
$pages = [];
foreach ([
    [],
    ['tab-input' => 'tab-custom-input', 'select_index' => '3'],
    ['tab-input' => 'tab-seo-input', 'select_index' => '" onfocus="alert(1)'],
    ['tab-input' => ['tab-seo-input'], 'select_index' => []],
    ['tab-input' => 'tab-removed-input', 'select_index' => '-2'],
] as $post) {
    $_POST = $post;
    ob_start();
    require 'lib/page-settings/_top-page.php';
    $pages[] = ob_get_clean();
}
echo json_encode($pages);
PHP);
        $expected = [['custom', '0'], ['custom', '3'], ['seo', '0'], ['custom', '0'], ['custom', '0']];
        foreach ($pages as $index => $html) {
            $document = new \DOMDocument('1.0', 'UTF-8');
            $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new \DOMXPath($document);
            $label = $xpath->query('//label[@id="tab-custom-label"]')->item(0);
            $this->assertSame('<img src=x onerror=alert(1)> & "独自"', $label->textContent);
            $this->assertSame(0, $label->getElementsByTagName('*')->length);
            $checked = $xpath->query('//input[@name="tab-input"][@checked]');
            $this->assertSame(1, $checked->length);
            $this->assertSame('tab-' . $expected[$index][0] . '-input', $checked->item(0)->getAttribute('id'));
            $legacy_index = $xpath->query('//input[@id="select_index"]')->item(0);
            $this->assertSame($expected[$index][1], $legacy_index->getAttribute('value'));
            $this->assertSame(4, $legacy_index->attributes->length);
        }
    }

    // 通常のbootstrapの固定do_actionスタブより先に、呼び出し記録用スタブを定義する。
    private function runWithActionRecorder(string $body): array
    {
        $root = dirname(__DIR__, 2);
        $program = <<<'PHP'
<?php
use Brain\Monkey\Functions;
$GLOBALS['recorded_actions'] = [];
function do_action($hook, ...$args) {
    $GLOBALS['recorded_actions'][] = $hook;
}
require 'tests/bootstrap.php';
Brain\Monkey\setUp();
require 'lib/page-settings/settings-tabs.php';
PHP;
        $program .= "\n" . $body . "\nBrain\\Monkey\\tearDown();\n";
        // コマンド文字列にコードを埋め込まず、標準入力で渡して引用符の解釈を避ける。
        $process = proc_open([PHP_BINARY], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root);
        $this->assertIsResource($process);
        fwrite($pipes[0], $program);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);
        $this->assertSame(0, $status, $errors . $output);
        $this->assertSame('', $errors);
        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }
}
