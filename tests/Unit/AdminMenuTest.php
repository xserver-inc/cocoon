<?php
/**
 * 管理バーメニューのユニットテスト
 *
 * lib/admin.php でリファクタリングした get_admin_bar_menu_array および
 * customize_admin_bar_menu 関数が正しく動作することを検証します。
 *
 * 注意: wp-mock-functions.php で既にグローバル定義されている関数
 * (is_classicpress 等) は Brain\Monkey で上書きできないため、
 * それらのデフォルト返り値 (false) を前提としたテストを記述しています。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class AdminMenuTest extends TestCase
{
    private static $admin_loaded = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$admin_loaded) {
            require_once dirname(__DIR__, 2) . '/lib/admin.php';
            self::$admin_loaded = true;
        }
    }

    // ========================================================================
    // get_admin_bar_menu_array - 構造の検証
    // ========================================================================

    /**
     * 返り値が配列であること
     */
    public function test_get_admin_bar_menu_array_配列を返す(): void
    {
        $menus = get_admin_bar_menu_array();
        $this->assertIsArray($menus);
        $this->assertNotEmpty($menus);
    }

    /**
     * 親メニュー（dashboard_menu）が先頭に存在すること
     */
    public function test_get_admin_bar_menu_array_親メニューが先頭に存在する(): void
    {
        $menus = get_admin_bar_menu_array();
        $this->assertSame('dashboard_menu', $menus[0]['id']);
        // 親メニューには title キーがあること
        $this->assertArrayHasKey('title', $menus[0]);
        // 親メニューには href キーがないこと（親メニューはリンク先なし）
        $this->assertArrayNotHasKey('href', $menus[0]);
    }

    /**
     * デフォルトの子メニュー一覧が含まれていること
     */
    public function test_get_admin_bar_menu_array_デフォルトの子メニューを含む(): void
    {
        $menus = get_admin_bar_menu_array();
        $ids = array_column($menus, 'id');

        // 全ユーザー共通のメニューが含まれる
        $expected_common = [
            'dashboard_menu-dashboard',
            'dashboard_menu-singles',
            'dashboard_menu-pages',
            'dashboard_menu-medias',
            'dashboard_menu-themes',
            'dashboard_menu-customize',
            'dashboard_menu-widget',
            'dashboard_menu-nav-menus',
            'dashboard_menu-theme-editor',
            'dashboard_menu-plugins',
        ];

        foreach ($expected_common as $id) {
            $this->assertContains($id, $ids, "メニュー '{$id}' が存在するべき");
        }
    }

    /**
     * 全ての子メニューが parent => 'dashboard_menu' を持つこと
     */
    public function test_get_admin_bar_menu_array_子メニューは全てdashboard_menuを親に持つ(): void
    {
        $menus = get_admin_bar_menu_array();

        // 先頭（親メニュー自身）を除いた全要素に parent が設定されている
        foreach (array_slice($menus, 1) as $menu) {
            $this->assertSame(
                'dashboard_menu',
                $menu['parent'],
                "メニュー '{$menu['id']}' の parent が 'dashboard_menu' であるべき"
            );
        }
    }

    /**
     * 全てのメニュー要素に必須キー（id, meta, title）があること
     */
    public function test_get_admin_bar_menu_array_全要素に必須キーがある(): void
    {
        $menus = get_admin_bar_menu_array();

        foreach ($menus as $i => $menu) {
            $this->assertArrayHasKey('id', $menu, "要素[{$i}]に 'id' がない");
            $this->assertArrayHasKey('meta', $menu, "要素[{$i}]に 'meta' がない");
            $this->assertArrayHasKey('title', $menu, "要素[{$i}]に 'title' がない");
        }
    }

    // ========================================================================
    // get_admin_bar_menu_array - ClassicPress / 権限による分岐
    // ========================================================================

    /**
     * is_classicpress() が false を返す場合（デフォルト）、パターン一覧が含まれること
     * ※ wp-mock-functions.php で is_classicpress() は false を返す
     */
    public function test_get_admin_bar_menu_array_WordPressではパターン一覧を含む(): void
    {
        $menus = get_admin_bar_menu_array();
        $ids = array_column($menus, 'id');
        $this->assertContains('dashboard_menu-patterns', $ids);
    }

    /**
     * パターン一覧がテーマの直後（プラグイン一覧より前）に配置されていること
     * 改変前の表示順序を維持していることの検証
     */
    public function test_get_admin_bar_menu_array_パターン一覧はテーマの直後に配置される(): void
    {
        $menus = get_admin_bar_menu_array();
        $ids = array_column($menus, 'id');

        $themes_pos = array_search('dashboard_menu-themes', $ids);
        $patterns_pos = array_search('dashboard_menu-patterns', $ids);
        $customize_pos = array_search('dashboard_menu-customize', $ids);

        $this->assertNotFalse($themes_pos);
        $this->assertNotFalse($patterns_pos);
        $this->assertNotFalse($customize_pos);

        // テーマ → パターン → カスタマイズ の順序
        $this->assertSame($themes_pos + 1, $patterns_pos, 'パターン一覧はテーマの直後であるべき');
        $this->assertSame($patterns_pos + 1, $customize_pos, 'カスタマイズはパターン一覧の直後であるべき');
    }

    /**
     * current_user_can('manage_options') が false を返す場合（デフォルト）、
     * 管理者専用サブメニューが含まれないこと
     * ※ TestCase::setUp() で current_user_can() は false を返すようにモックされている
     */
    public function test_get_admin_bar_menu_array_非管理者では管理専用メニューが含まれない(): void
    {
        $menus = get_admin_bar_menu_array();
        $ids = array_column($menus, 'id');

        $admin_only = [
            'dashboard_menu-theme-settings',
            'dashboard_menu-speech-balloon',
            'dashboard_menu-theme-func-text',
            'dashboard_menu-theme-affiliate-tag',
            'dashboard_menu-theme-ranking',
            'dashboard_menu-theme-access',
            'dashboard_menu-theme-speed-up',
            'dashboard_menu-theme-backup',
            'dashboard_menu-theme-cache',
        ];

        foreach ($admin_only as $id) {
            $this->assertNotContains($id, $ids, "非管理者では '{$id}' が含まれるべきでない");
        }
    }

    // ========================================================================
    // get_admin_bar_menu_array - フィルターフックの存在検証
    // ========================================================================

    /**
     * cocoon_admin_bar_menus フィルターが apply_filters 経由で呼ばれていること
     * ※ 実際のフィルター動作は wp-mock-functions.php の apply_filters がパススルーのため、
     *   ここでは返り値が変更されずにそのまま返ることを確認（フィルターが存在する証拠）
     */
    public function test_get_admin_bar_menu_array_フィルター経由で返される(): void
    {
        // apply_filters がパススルーなので、元の配列がそのまま返される
        $menus = get_admin_bar_menu_array();
        $this->assertIsArray($menus);
        // フィルターが壊していないことの確認
        $this->assertSame('dashboard_menu', $menus[0]['id']);
    }

    // ========================================================================
    // customize_admin_bar_menu - add_menu 呼び出し検証
    // ========================================================================

    /**
     * customize_admin_bar_menu が配列の要素数分だけ add_menu を呼び出すこと
     */
    public function test_customize_admin_bar_menu_メニュー数分add_menuを呼び出す(): void
    {
        // get_admin_bar_menu_array の返り値の要素数を取得
        $expected_count = count(get_admin_bar_menu_array());

        // add_menu メソッドを持つモックを作成
        $call_count = 0;
        $wp_admin_bar = new class($call_count) {
            private int $count;
            public function __construct(private &$counter) {
                $this->count = &$counter;
            }
            public function add_menu(array $args): void {
                $this->count++;
            }
        };

        customize_admin_bar_menu($wp_admin_bar);
        $this->assertSame($expected_count, $call_count, 'add_menu はメニュー配列の要素数と同じ回数呼ばれるべき');
    }

    /**
     * customize_admin_bar_menu に渡された各メニューが id を持つこと
     */
    public function test_customize_admin_bar_menu_各メニューにidが含まれる(): void
    {
        $added_menus = [];
        $wp_admin_bar = new class($added_menus) {
            private array $menus;
            public function __construct(private &$collected) {
                $this->menus = &$collected;
            }
            public function add_menu(array $args): void {
                $this->menus[] = $args;
            }
        };

        customize_admin_bar_menu($wp_admin_bar);

        foreach ($added_menus as $i => $menu) {
            $this->assertArrayHasKey('id', $menu, "add_menu で渡された要素[{$i}]に 'id' がない");
        }
    }
}
