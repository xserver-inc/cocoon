<?php
/**
 * グローバルナビの現在ページ属性に関するWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class MenuDescriptionWalkerIntegrationTest extends IntegrationTestCase
{
    /**
     * 親ページ・子ページ・メニューにないページの検証データ
     */
    public static function currentPages(): array
    {
        return [
            '親ページ' => ['parent'],
            '子ページ' => ['child'],
            'メニューにないページ' => ['unlisted'],
        ];
    }

    /**
     * WordPress本体と一致する現在ページ属性と、既存の説明・階層の維持
     *
     * @dataProvider currentPages
     */
    public function testCurrentPageMatchesWordPressWithoutMarkingAncestors(string $currentPage): void
    {
        $pages = [];
        foreach (['parent', 'child', 'sibling', 'unlisted'] as $key) {
            $pages[$key] = $this->createPost([
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_title' => 'ナビ検証 ' . $key,
                'post_parent' => $key === 'child' ? $pages['parent'] : 0,
            ]);
        }

        $menu = wp_create_nav_menu('現在ページ属性の検証 ' . wp_generate_uuid4());
        $this->assertFalse(is_wp_error($menu));
        $originalQuery = $GLOBALS['wp_query'];
        $originalMainQuery = $GLOBALS['wp_the_query'];

        try {
            $items = [];
            $description = '説明 "引用" & <em>補足</em>';
            foreach (['parent', 'child', 'sibling'] as $key) {
                $items[$key] = wp_update_nav_menu_item($menu, 0, [
                    'menu-item-title' => 'ナビ検証 ' . $key,
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $pages[$key],
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                    'menu-item-parent-id' => $key === 'child' ? $items['parent'] : 0,
                    'menu-item-description' => $key === 'child' ? $description : '',
                ]);
                $this->assertFalse(is_wp_error($items[$key]));
            }

            // WordPressによる保存・整形とHTML文字参照の復号後の説明文との照合
            $description = html_entity_decode(wp_setup_nav_menu_item(get_post($items['child']))->description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // 実際の固定ページクエリによる、WordPressの現在ページ判定の準備
            $GLOBALS['wp_query'] = new \WP_Query(['page_id' => $pages[$currentPage]]);
            $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
            $this->assertSame($pages[$currentPage], get_queried_object_id());

            $arguments = ['menu' => $menu, 'echo' => false, 'container' => false, 'fallback_cb' => false];
            $core = $this->menuDocument(wp_nav_menu($arguments));
            $cocoon = $this->menuDocument(wp_nav_menu($arguments + ['walker' => new \menu_description_walker()]));
            $expectedCount = $currentPage === 'unlisted' ? 0 : 1;
            $this->assertCount($expectedCount, $core->query('//a[@aria-current="page"]'));
            $this->assertCount($expectedCount, $cocoon->query('//a[@aria-current]'));

            foreach ($items as $key => $itemId) {
                $selector = '//li[@id="menu-item-' . $itemId . '"]/a';
                $coreLink = $core->query($selector)->item(0);
                $cocoonLink = $cocoon->query($selector)->item(0);
                $this->assertNotNull($coreLink);
                $this->assertNotNull($cocoonLink);
                $this->assertSame($key === $currentPage, $cocoonLink->hasAttribute('aria-current'));
                $this->assertSame($coreLink->getAttribute('aria-current'), $cocoonLink->getAttribute('aria-current'));
                $this->assertSame(get_permalink($pages[$key]), $cocoonLink->getAttribute('href'));
                $this->assertCount(1, $cocoon->query('./div[@class="caption-wrap"]/div[@class="item-label"]', $cocoonLink));
            }

            // 説明文のエスケープと、子メニュー・開閉アイコンの構造の維持
            $descriptionNode = $cocoon->query('//div[contains(@class,"item-description")]')->item(0);
            $this->assertNotNull($descriptionNode);
            $this->assertSame($description, $descriptionNode->textContent);
            $this->assertCount(0, $cocoon->query('.//em', $descriptionNode));
            $this->assertCount(1, $cocoon->query('//li[@id="menu-item-' . $items['parent'] . '"]/ul/li[@id="menu-item-' . $items['child'] . '"]'));
            $this->assertCount(1, $cocoon->query('//div[contains(@class,"top-has-sub")]'));
        } finally {
            $GLOBALS['wp_query'] = $originalQuery;
            $GLOBALS['wp_the_query'] = $originalMainQuery;
            wp_delete_nav_menu($menu);
        }
    }

    /**
     * 実際のメニューHTMLの解析と、属性比較用XPathの取得
     */
    private function menuDocument(string $html): \DOMXPath
    {
        $document = new \DOMDocument();
        $document->loadHTML('<!doctype html><meta charset="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        return new \DOMXPath($document);
    }
}
