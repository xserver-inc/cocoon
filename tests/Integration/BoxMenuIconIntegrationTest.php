<?php
/**
 * ボックスメニューのFont Awesomeアイコンに関するWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class BoxMenuIconIntegrationTest extends IntegrationTestCase
{
    private array $createdMenuIds = [];
    private array $originalScreen = [];
    private array $originalMenuLocations = [];

    protected function setUp(): void
    {
        parent::setUp();

        // 公開画面と同じ出力条件にそろえるための管理画面状態の退避
        $this->originalScreen = [
            'exists' => array_key_exists('current_screen', $GLOBALS),
            'value' => $GLOBALS['current_screen'] ?? null,
        ];
        unset($GLOBALS['current_screen']);

        // メニュー削除で更新される配置設定の元の状態の退避
        $themeMods = (array) get_theme_mods();
        $this->originalMenuLocations = [
            'exists' => array_key_exists('nav_menu_locations', $themeMods),
            'value' => $themeMods['nav_menu_locations'] ?? null,
        ];
    }

    protected function tearDown(): void
    {
        try {
            // テスト専用DBに作成したメニューと所属項目の削除
            foreach ($this->createdMenuIds as $menuId) {
                wp_delete_nav_menu($menuId);
            }
        } finally {
            // メニュー配置設定の値と登録状態の復元
            if ($this->originalMenuLocations) {
                if ($this->originalMenuLocations['exists']) {
                    set_theme_mod('nav_menu_locations', $this->originalMenuLocations['value']);
                } else {
                    remove_theme_mod('nav_menu_locations');
                }
            }
            // テスト開始前の管理画面状態の復元
            if ($this->originalScreen) {
                if ($this->originalScreen['exists']) {
                    $GLOBALS['current_screen'] = $this->originalScreen['value'];
                } else {
                    unset($GLOBALS['current_screen']);
                }
            }
            parent::tearDown();
        }
    }

    /**
     * 数字を含むアイコンと既存のFont Awesome指定を両方の呼び出し経路で維持することをテスト
     *
     * @dataProvider supportedIconClassesProvider
     */
    public function test_対応するアイコンクラスを星に置き換えず出力する(string $classes): void
    {
        $menuId = $this->createMenu($classes);

        foreach ($this->renderMenu($menuId) as $html) {
            $icon = $this->getIconElement($html);
            $this->assertSame('DIV', $icon->get_tag());
            $this->assertSame($classes, $icon->get_attribute('class'));
            $this->assertSame('true', $icon->get_attribute('aria-hidden'));
            $this->assertStringNotContainsString('class="fa fa-star"', $html);
        }
    }

    public static function supportedIconClassesProvider(): array
    {
        return [
            '数字で始まるFA4アイコン' => ['fa fa-500px'],
            '数字で始まるブランドアイコン' => ['fab fa-500px'],
            'FA4の通常アイコン' => ['fa fa-home'],
            'Solidの通常アイコン' => ['fas fa-home'],
            'Regularの通常アイコン' => ['far fa-circle'],
            'Brandsの通常アイコン' => ['fab fa-wordpress'],
            'Lightの既存指定' => ['fal fa-home'],
            'Duotoneの既存指定' => ['fad fa-home'],
            'Kitカスタムアイコンの既存指定' => ['fak fa-basketball-hoop-broken'],
            'Thinの既存指定' => ['fat fa-house'],
            '末尾が数字のアイコン' => ['fab fa-html5'],
            '途中に数字を含むアイコン' => ['fas fa-dice-d20'],
            '前後の通常クラス付き' => ['custom-before fab fa-500px custom-after'],
            '前後と途中の連続空白付き' => [' custom-before  fab  fa-500px  custom-after '],
        ];
    }

    /**
     * 無効な接頭辞やアイコン名の部分一致をアイコン指定として誤認しないことをテスト
     *
     * @dataProvider invalidIconClassesProvider
     */
    public function test_無効なクラス指定なら既定の星を表示する(string $classes): void
    {
        $menuId = $this->createMenu($classes);

        foreach ($this->renderMenu($menuId) as $html) {
            $icon = $this->getIconElement($html);
            $this->assertSame('fa fa-star', $icon->get_attribute('class'));
            $this->assertSame('true', $icon->get_attribute('aria-hidden'));
        }
    }

    public static function invalidIconClassesProvider(): array
    {
        return [
            '別クラス内のfab' => ['myfab fa-home'],
            '未対応のfax' => ['fax fa-home'],
            '無効なアイコン名の末尾' => ['fab fa-home_bad'],
        ];
    }

    /**
     * アイコンクラスを指定していないメニューでは既定の星を表示することをテスト
     */
    public function test_アイコン未指定なら既定の星を表示する(): void
    {
        $menuId = $this->createMenu('');

        foreach ($this->renderMenu($menuId) as $html) {
            $icon = $this->getIconElement($html);
            $this->assertSame('fa fa-star', $icon->get_attribute('class'));
            $this->assertSame('true', $icon->get_attribute('aria-hidden'));
        }
    }

    /**
     * アイコンと画像URLの両方が指定された場合に画像を優先することをテスト
     */
    public function test_画像URLの指定をアイコンフォントより優先する(): void
    {
        $imageUrl = 'https://example.org/box-menu-icon.png';
        $menuId = $this->createMenu('fab fa-500px', $imageUrl);

        foreach ($this->renderMenu($menuId) as $html) {
            $icon = $this->getIconElement($html);
            $this->assertSame('IMG', $icon->get_tag());
            $this->assertSame($imageUrl, $icon->get_attribute('src'));
            $this->assertSame('', $icon->get_attribute('alt'));
            $this->assertStringNotContainsString('class="fa fa-star"', $html);
            $this->assertStringNotContainsString('class="fab fa-500px"', $html);
        }
    }

    private function createMenu(string $classes, string $imageUrl = ''): int
    {
        $menuId = wp_create_nav_menu('ボックスメニューアイコン確認-' . wp_generate_uuid4());
        $this->assertNotInstanceOf(\WP_Error::class, $menuId);
        $this->createdMenuIds[] = $menuId;

        // 管理画面と同じWordPress APIによるクラス文字列と画像URLの保存
        $itemId = wp_update_nav_menu_item($menuId, 0, [
            'menu-item-title' => 'アイコンのテスト',
            'menu-item-type' => 'custom',
            'menu-item-url' => 'https://example.org/box-menu-test/',
            'menu-item-status' => 'publish',
            'menu-item-classes' => $classes,
            'menu-item-attr-title' => $imageUrl,
        ]);
        $this->assertNotInstanceOf(\WP_Error::class, $itemId);
        $this->assertGreaterThan(0, $itemId);

        return $menuId;
    }

    private function renderMenu(int $menuId): array
    {
        return [
            '共通関数' => get_box_menu_tag(['name' => $menuId]),
            'ショートコード' => do_shortcode('[box_menu name="' . $menuId . '"]'),
        ];
    }

    private function getIconElement(string $html): \WP_HTML_Tag_Processor
    {
        // アイコン専用のラッパーから実際の画像またはアイコン要素への移動
        $processor = new \WP_HTML_Tag_Processor($html);
        $this->assertTrue($processor->next_tag([
            'tag_name' => 'DIV',
            'class_name' => 'box-menu-icon',
        ]));
        $this->assertTrue($processor->next_tag());

        return $processor;
    }
}
