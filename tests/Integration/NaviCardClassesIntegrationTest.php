<?php
/**
 * メニューのCSSクラスとナビカード出力のWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class NaviCardClassesIntegrationTest extends IntegrationTestCase
{
    private array $createdMenuIds = [];
    private array $originalScreen = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalScreen = [
            'exists' => array_key_exists('current_screen', $GLOBALS),
            'value' => $GLOBALS['current_screen'] ?? null,
        ];
        unset($GLOBALS['current_screen']);
    }

    protected function tearDown(): void
    {
        try {
            // テスト用メニューと所属項目の削除
            foreach ($this->createdMenuIds as $menuId) {
                wp_delete_nav_menu($menuId);
            }
            if ($this->originalScreen) {
                if ($this->originalScreen['exists']) {
                    $GLOBALS['current_screen'] = $this->originalScreen['value'];
                } else {
                    unset($GLOBALS['current_screen']);
                }
            }
        } finally {
            parent::tearDown();
        }
    }

    /**
     * 前後と途中に空白を含むCSSクラスで両ショートコードのリボン1〜5を表示することをテスト
     */
    public function test_空白を含むCSSクラスでも両ショートコードで全リボンを表示する(): void
    {
        foreach (range(1, 5) as $ribbonNo) {
            $menuId = $this->createMenu(' ' . $ribbonNo . '  extra second ');
            $menuItems = wp_get_nav_menu_items($menuId);
            $this->assertSame(['', (string) $ribbonNo, '', 'extra', 'second', ''], $menuItems[0]->classes);

            foreach (['navi', 'navi_list'] as $shortcode) {
                $html = do_shortcode('[' . $shortcode . ' name="' . $menuId . '"]');
                $link = $this->getCardLink($html);
                $this->assertSame($this->baseLinkClass() . ' ' . $ribbonNo . ' extra second', $link->get_attribute('class'));
                $this->assertSame('_blank', $link->get_attribute('target'));
                $this->assertStringContainsString('ribbon-color-' . $ribbonNo, $html);
            }
        }
    }

    /**
     * 空白のない既存指定でもリボンと追加クラスの出力を維持することをテスト
     */
    public function test_空白のない既存指定の出力を維持する(): void
    {
        $menuId = $this->createMenu('3 extra');
        $html = do_shortcode('[navi name="' . $menuId . '"]');

        $this->assertSame($this->baseLinkClass() . ' 3 extra', $this->getCardLink($html)->get_attribute('class'));
        $this->assertStringContainsString('ribbon-color-3', $html);
    }

    /**
     * 空文字または空白だけのCSSクラスでもカード本体を出力することをテスト
     */
    public function test_クラスが空でも余分な空白やリボンを出力しない(): void
    {
        foreach (['', '   '] as $classes) {
            $menuId = $this->createMenu($classes);
            $html = do_shortcode('[navi_list name="' . $menuId . '"]');

            $this->assertSame($this->baseLinkClass(), $this->getCardLink($html)->get_attribute('class'));
            $this->assertStringNotContainsString('ribbon-color-', $html);
        }
    }

    /**
     * 最初の有効クラスが対象番号以外なら後続の数字をリボン番号に採用しないことをテスト
     */
    public function test_先頭のクラスが番号以外なら後続の数字でリボンを付けない(): void
    {
        foreach (['extra 3', '0 3'] as $classes) {
            $menuId = $this->createMenu(' ' . $classes . ' ');
            $html = do_shortcode('[navi name="' . $menuId . '"]');

            $this->assertSame($this->baseLinkClass() . ' ' . $classes, $this->getCardLink($html)->get_attribute('class'));
            $this->assertStringNotContainsString('ribbon-color-', $html);
        }
    }

    /**
     * ショートコードを通さない共通カード出力でも空要素の後にあるCSSクラスを保持することをテスト
     */
    public function test_共通カード出力でも空要素だけを除去する(): void
    {
        $atts = $this->cardAttributes();
        $atts['classes'] = ['', 'extra', '', '0', 'second', ''];
        $html = get_widget_entry_card_link_tag($atts);

        $this->assertSame($this->baseLinkClass() . ' extra 0 second', $this->getCardLink($html)->get_attribute('class'));
    }

    /**
     * 共通カードのCSSクラス省略時とnull指定時の出力を維持することをテスト
     */
    public function test_共通カードのクラス省略時とnull指定時も出力できる(): void
    {
        $atts = $this->cardAttributes();
        $html = get_widget_entry_card_link_tag($atts);
        $this->assertSame($this->baseLinkClass(), $this->getCardLink($html)->get_attribute('class'));

        $atts['classes'] = null;
        $this->assertSame($html, get_widget_entry_card_link_tag($atts));
    }

    /**
     * フィルターでメニュー項目のCSSクラスがnullまたは未設定になっても両ショートコードを出力することをテスト
     */
    public function test_フィルターでクラスがnullまたは未設定でもカードを表示する(): void
    {
        $menuId = $this->createMenu('3 extra');

        foreach ([false, true] as $unsetClasses) {
            $filter = static function ($items, $menu) use ($menuId, $unsetClasses) {
                if ((int) $menu->term_id !== $menuId) {
                    return $items;
                }

                // キャッシュ内の元オブジェクトを変更しないメニュー項目の複製
                return array_map(static function ($item) use ($unsetClasses) {
                    $item = clone $item;
                    if ($unsetClasses) {
                        unset($item->classes);
                    } else {
                        $item->classes = null;
                    }
                    return $item;
                }, $items);
            };
            add_filter('wp_get_nav_menu_items', $filter, 10, 2);

            try {
                foreach (['navi', 'navi_list'] as $shortcode) {
                    $html = do_shortcode('[' . $shortcode . ' name="' . $menuId . '"]');
                    $link = $this->getCardLink($html);
                    $this->assertSame($this->baseLinkClass(), $link->get_attribute('class'));
                    $this->assertSame('_blank', $link->get_attribute('target'));
                    $this->assertStringNotContainsString('ribbon-color-', $html);
                }
            } finally {
                remove_filter('wp_get_nav_menu_items', $filter, 10);
            }
        }
    }

    private function createMenu(string $classes): int
    {
        $menuId = wp_create_nav_menu('ナビカードクラス確認-' . wp_generate_uuid4());
        $this->assertNotInstanceOf(\WP_Error::class, $menuId);
        $this->createdMenuIds[] = $menuId;
        $postId = $this->createPost([
            'post_title' => 'ナビカードのリンク先',
            'post_type' => 'page',
            'post_status' => 'publish',
        ]);

        // 管理画面と同じWordPress APIによるクラス文字列の保存
        $itemId = wp_update_nav_menu_item($menuId, 0, [
            'menu-item-title' => 'ナビカードのテスト',
            'menu-item-type' => 'post_type',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $postId,
            'menu-item-status' => 'publish',
            'menu-item-classes' => $classes,
            'menu-item-target' => '_blank',
        ]);
        $this->assertNotInstanceOf(\WP_Error::class, $itemId);
        $this->assertGreaterThan(0, $itemId);
        return $menuId;
    }

    private function cardAttributes(): array
    {
        return [
            'prefix' => WIDGET_NAVI_ENTRY_CARD_PREFIX,
            'url' => 'https://example.org/navi-card-test/',
            'title' => '共通カードのテスト',
            'object' => 'custom',
            'image_attributes' => ['https://example.org/navi-card-test.png', 120, 68],
        ];
    }

    private function getCardLink(string $html): \WP_HTML_Tag_Processor
    {
        // HTML属性の文字列比較に必要なカードリンク要素の抽出
        $processor = new \WP_HTML_Tag_Processor($html);
        $this->assertTrue($processor->next_tag([
            'tag_name' => 'A',
            'class_name' => WIDGET_NAVI_ENTRY_CARD_PREFIX . '-entry-card-link',
        ]));
        return $processor;
    }

    private function baseLinkClass(): string
    {
        return WIDGET_NAVI_ENTRY_CARD_PREFIX . '-entry-card-link widget-entry-card-link a-wrap';
    }
}
