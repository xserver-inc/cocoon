<?php
/**
 * ブロック本体の読込状態に編集ツールバーが追従することを確認する統合テスト
 */

namespace Cocoon\Tests\Integration;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class GutenbergToolbarTest extends TestCase
{
    private const BLOCKS_HANDLE = 'cocoon-blocks-js';

    private array $originalGlobals = [];
    private array $originalScreenRegistry = [];
    private ?\ReflectionProperty $screenRegistryProperty = null;
    private ?\WP_Scripts $scripts = null;
    private ?\Closure $visualEditorStyleFilter = null;
    private bool $visualEditorStyleEnabled = true;

    protected function setUp(): void
    {
        parent::setUp();

        // WordPress本体の存在確認と、モックを使う通常ユニットテストでのスキップ
        if (!defined('WP_TESTS_DOMAIN')) {
            $this->markTestSkipped('WordPress テスト環境が利用できません。WP_TESTS_DIR を設定してください。');
        }

        // 他のテストのスクリプト、画面、スキン設定、フック実行回数の退避
        foreach (['wp_scripts', 'current_screen', '_THEME_OPTIONS', 'wp_actions'] as $name) {
            $this->originalGlobals[$name] = [
                'exists' => array_key_exists($name, $GLOBALS),
                'value' => $GLOBALS[$name] ?? null,
            ];
        }

        // ケースごとに独立した、WordPress本来の登録・依存解決処理の準備
        $this->scripts = new \WP_Scripts();
        $GLOBALS['wp_scripts'] = $this->scripts;
        wp_dequeue_script(self::BLOCKS_HANDLE);
        wp_deregister_script(self::BLOCKS_HANDLE);
        wp_dequeue_script($this->toolbarHandle());
        wp_deregister_script($this->toolbarHandle());

        // フロント側として起動する統合テスト用のWordPress画面クラスの読込
        require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
        require_once ABSPATH . 'wp-admin/includes/screen.php';

        // 既存の画面オブジェクトの再初期化を防ぐための、共有レジストリの一時隔離
        $this->screenRegistryProperty = new \ReflectionProperty(\WP_Screen::class, '_registry');
        $this->originalScreenRegistry = $this->screenRegistryProperty->getValue();
        $this->screenRegistryProperty->setValue(null, []);

        // current_screenフックの副作用を避けるための、is_admin()参照画面オブジェクトだけの切替
        $GLOBALS['current_screen'] = \WP_Screen::get('post');

        // DB変更なしで設定を制御するための、スキン固定値の一時解除と設定フィルターの登録
        unset($GLOBALS['_THEME_OPTIONS'][OP_VISUAL_EDITOR_STYLE_ENABLE]);
        $this->visualEditorStyleFilter = function () {
            return $this->visualEditorStyleEnabled;
        };
        add_filter('theme_mod_' . OP_VISUAL_EDITOR_STYLE_ENABLE, $this->visualEditorStyleFilter);
    }

    protected function tearDown(): void
    {
        // WordPress未読込によるスキップ時の、WordPress APIを使う後処理の回避
        if ($this->scripts !== null) {
            remove_filter('theme_mod_' . OP_VISUAL_EDITOR_STYLE_ENABLE, $this->visualEditorStyleFilter);

            // WP_Scriptsのコンストラクターで追加されたinitコールバックの除去
            remove_action('init', [$this->scripts, 'init'], 0);
        }

        // 共有参照先の画面オブジェクトを変更しない、テスト前のレジストリへの復元
        if ($this->screenRegistryProperty !== null) {
            $this->screenRegistryProperty->setValue(null, $this->originalScreenRegistry);
        }

        // 退避したグローバルの復元と、開始時に存在しなかったグローバルの削除
        foreach ($this->originalGlobals as $name => $original) {
            if ($original['exists']) {
                $GLOBALS[$name] = $original['value'];
            } else {
                unset($GLOBALS[$name]);
            }
        }

        parent::tearDown();
    }

    public function test_本体が未登録ならツールバーを追加しない(): void
    {
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'registered'));

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
    }

    public function test_本体が登録だけならツールバー経由で読み込まない(): void
    {
        $this->registerBlocks();
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'registered'));
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));
    }

    public function test_本体が登録されenqueue済みならツールバーを追加する(): void
    {
        $this->enqueueBlocks();

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarReady();
    }

    public function test_本体が登録されdone済みならqueueになくてもツールバーを追加する(): void
    {
        $this->printBlocksAndDequeue();
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'registered'));
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'done'));
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));
        $this->assertNotContains(self::BLOCKS_HANDLE, $this->scripts->queue);

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarReady();
    }

    public function test_登録済みの本体が間接依存ならツールバーを追加する(): void
    {
        $this->registerBlocks();
        $this->enqueueDependentScript();
        $this->assertNotContains(self::BLOCKS_HANDLE, $this->scripts->queue);
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarReady();
    }

    public function test_未登録の本体が間接依存でenqueued扱いでもツールバーを追加しない(): void
    {
        $this->enqueueDependentScript();

        // 未登録のハンドルにもenqueuedを返すWordPress本来の挙動の確認
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'registered'));
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
    }

    public function test_本体をenqueue後にdequeueしたらツールバー経由で再読込しない(): void
    {
        $this->enqueueBlocks();
        wp_dequeue_script(self::BLOCKS_HANDLE);
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'registered'));
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));
    }

    public function test_本体が未登録でdoneだけ残っていてもツールバーを追加しない(): void
    {
        $this->printBlocksAndDequeue();
        wp_deregister_script(self::BLOCKS_HANDLE);
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'registered'));
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'done'));

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
    }

    public function test_管理画面以外では本体がenqueue済みでもツールバーを追加しない(): void
    {
        $this->enqueueBlocks();
        $GLOBALS['current_screen'] = \WP_Screen::get('front');
        $this->assertFalse(is_admin());

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
    }

    public function test_ビジュアルエディタースタイルが無効ならツールバーを追加しない(): void
    {
        $this->enqueueBlocks();
        $this->visualEditorStyleEnabled = false;
        $this->assertFalse((bool) is_visual_editor_style_enable());

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarAbsent();
    }

    public function test_同じフックを複数回実行してもツールバーを二重追加しない(): void
    {
        $this->enqueueBlocks();

        $this->runToolbarHookTwice();

        $this->assertToolbarReady();
        $this->assertCount(1, array_keys($this->scripts->queue, $this->toolbarHandle(), true));
    }

    public function test_同じフックを複数回実行しても未登録の間接依存にツールバーを追加しない(): void
    {
        $this->enqueueDependentScript();
        $this->assertTrue(wp_script_is(self::BLOCKS_HANDLE, 'enqueued'));

        $this->runToolbarHookTwice();

        $this->assertToolbarAbsent();
        $this->assertFalse(wp_script_is(self::BLOCKS_HANDLE, 'registered'));
    }

    public function test_ツールバーの依存関係とURLと更新日時とフッターと翻訳設定を維持する(): void
    {
        $this->enqueueBlocks();

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarReady();
        $toolbar = $this->scripts->registered[$this->toolbarHandle()];
        $this->assertSame(['wp-i18n', self::BLOCKS_HANDLE], $toolbar->deps);
        $this->assertSame(get_cocoon_template_directory_uri() . '/js/gutenberg-toolbar.js', $toolbar->src);
        $this->assertSame(filemtime(get_cocoon_template_directory() . '/js/gutenberg-toolbar.js'), $toolbar->ver);
        $this->assertSame(1, $this->scripts->get_data($this->toolbarHandle(), 'group'));
        $this->assertSame(THEME_NAME, $toolbar->textdomain);
        $this->assertSame(get_cocoon_template_directory() . '/languages', $toolbar->translations_path);
    }

    public function test_ファイルが存在しない場合は既存のバージョンフォールバックを維持する(): void
    {
        $this->enqueueBlocks();

        // 実在ファイルをディレクトリとして扱う、ファイル変更なしでの不存在状態の再現
        $directoryFilter = static function () {
            return __FILE__;
        };
        add_filter('get_cocoon_template_directory', $directoryFilter);

        try {
            $this->assertFileDoesNotExist(get_cocoon_template_directory() . '/js/gutenberg-toolbar.js');
            cocoon_enqueue_toolbar_script();
        } finally {
            remove_filter('get_cocoon_template_directory', $directoryFilter);
        }

        $this->assertToolbarReady();
        $this->assertFalse($this->scripts->registered[$this->toolbarHandle()]->ver);
    }

    public function test_ウィジェットでも本体がenqueue済みならツールバーを追加する(): void
    {
        $this->enqueueBlocks();
        $GLOBALS['current_screen'] = \WP_Screen::get('widgets');

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarReady();
    }

    public function test_カスタマイザーでも本体がenqueue済みならツールバーを追加する(): void
    {
        $this->enqueueBlocks();
        $GLOBALS['current_screen'] = \WP_Screen::get('customize');

        cocoon_enqueue_toolbar_script();

        $this->assertToolbarReady();
    }

    public function test_依存が欠けて出力できないツールバーを正常扱いしない(): void
    {
        $this->enqueueBlocks();
        cocoon_enqueue_toolbar_script();
        wp_deregister_script(self::BLOCKS_HANDLE);

        $toolbar = $this->toolbarHandle();
        $dependencyWarnings = [];
        $phpNotices = [];
        $failure = null;

        // 意図的に欠損させた本体とツールバーに限定した、WordPress通知の記録
        $captureWarning = static function ($function, $message) use (&$dependencyWarnings, $toolbar) {
            if ($function === 'WP_Scripts::add'
                && strpos($message, $toolbar) !== false
                && strpos($message, self::BLOCKS_HANDLE) !== false) {
                $dependencyWarnings[] = $message;
            }
        };
        add_action('doing_it_wrong_run', $captureWarning, 10, 2);

        // 想定したPHP通知だけの記録と、その他のエラーの既存ハンドラーへの委譲
        $previousHandler = null;
        $previousHandler = set_error_handler(
            static function ($severity, $message, $file, $line) use (&$previousHandler, &$phpNotices, $toolbar) {
                if ($severity === E_USER_NOTICE
                    && strpos($message, 'WP_Scripts::add') !== false
                    && strpos($message, $toolbar) !== false
                    && strpos($message, self::BLOCKS_HANDLE) !== false) {
                    $phpNotices[] = $message;
                    return true;
                }

                return $previousHandler ? $previousHandler($severity, $message, $file, $line) : false;
            }
        );

        // ヘルパー自身の失敗だけを捕捉し、後続の検証失敗との取り違えを防ぐための分離
        try {
            $this->assertToolbarReady();
        } catch (AssertionFailedError $error) {
            $failure = $error;
        } finally {
            restore_error_handler();
            remove_action('doing_it_wrong_run', $captureWarning, 10);
        }

        $this->assertInstanceOf(AssertionFailedError::class, $failure);
        $this->assertStringContainsString('ツールバーが依存解決後の処理予定・出力済み一覧にありません。', $failure->getMessage());
        $this->assertFalse(wp_script_is($toolbar, 'to_do'));
        $this->assertFalse(wp_script_is($toolbar, 'done'));

        // 通知機能の導入前後とWP_DEBUGの設定に応じた、想定通知件数の検証
        $expectedWarnings = method_exists(\WP_Dependencies::class, 'get_dependency_warning_message') ? 1 : 0;
        $this->assertCount($expectedWarnings, $dependencyWarnings);
        $this->assertCount(WP_DEBUG ? $expectedWarnings : 0, $phpNotices);
    }

    public function test_出力済みのツールバーは処理予定になくても正常扱いする(): void
    {
        $this->enqueueBlocks();
        cocoon_enqueue_toolbar_script();

        // WordPress本来の出力処理による、ツールバーのdone状態の準備
        ob_start();
        try {
            $this->scripts->do_items([$this->toolbarHandle()]);
        } finally {
            ob_end_clean();
        }
        $this->assertTrue(wp_script_is($this->toolbarHandle(), 'done'));
        $this->assertFalse(wp_script_is($this->toolbarHandle(), 'to_do'));

        $this->assertToolbarReady();
    }

    private function toolbarHandle(): string
    {
        return THEME_NAME . '-gutenberg-toolbar';
    }

    private function registerBlocks(): void
    {
        // 本体の実ファイルによる登録状態の準備と、個別ブロックの依存関係からの試験の分離
        wp_register_script(self::BLOCKS_HANDLE, get_cocoon_template_directory_uri() . '/blocks/dist/blocks.build.js');
    }

    private function enqueueBlocks(): void
    {
        $this->registerBlocks();
        wp_enqueue_script(self::BLOCKS_HANDLE);
    }

    private function enqueueDependentScript(): void
    {
        // 本体をqueueへ直接追加しない、二段階の依存グラフによる判定状態の準備
        wp_register_script('cocoon-toolbar-test-bridge', false, [self::BLOCKS_HANDLE]);
        wp_register_script('cocoon-toolbar-test-consumer', false, ['cocoon-toolbar-test-bridge']);
        wp_enqueue_script('cocoon-toolbar-test-consumer');
    }

    private function printBlocksAndDequeue(): void
    {
        $this->enqueueBlocks();

        // doneの直接変更を避ける、WordPress本来の出力処理による出力済み状態の準備
        ob_start();
        try {
            $this->scripts->do_items([self::BLOCKS_HANDLE]);
        } finally {
            ob_end_clean();
        }
        wp_dequeue_script(self::BLOCKS_HANDLE);
    }

    private function runToolbarHookTwice(): void
    {
        $hook = 'enqueue_block_editor_assets';
        $priority = has_action($hook, 'cocoon_enqueue_toolbar_script');
        $this->assertSame(10, $priority);
        $originalHook = $GLOBALS['wp_filter'][$hook];

        // 他の画面用処理の副作用を避ける、実際のツールバー関数だけを登録したフックの準備
        $GLOBALS['wp_filter'][$hook] = new \WP_Hook();
        add_action($hook, 'cocoon_enqueue_toolbar_script', $priority);
        try {
            do_action($hook);
            do_action($hook);
        } finally {
            $GLOBALS['wp_filter'][$hook] = $originalHook;
        }
    }

    private function assertToolbarAbsent(): void
    {
        $this->assertFalse(wp_script_is($this->toolbarHandle(), 'registered'));
        $this->assertFalse(wp_script_is($this->toolbarHandle(), 'enqueued'));
    }

    private function assertToolbarReady(): void
    {
        $this->assertTrue(wp_script_is($this->toolbarHandle(), 'registered'));
        $this->assertTrue(wp_script_is($this->toolbarHandle(), 'enqueued'));

        // 依存欠落時にもtrueを返す戻り値ではなく、解決後の処理予定・出力済み状態の確認
        $this->scripts->all_deps([$this->toolbarHandle()]);
        $this->assertTrue(
            wp_script_is($this->toolbarHandle(), 'to_do') || wp_script_is($this->toolbarHandle(), 'done'),
            'ツールバーが依存解決後の処理予定・出力済み一覧にありません。'
        );
    }
}
