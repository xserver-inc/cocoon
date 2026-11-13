<?php
/**
 * 本文の内部・外部リンク判定に関するWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class LinksIntegrationTest extends IntegrationTestCase
{
    /**
     * プロトコル差・属性内の引用符・URLの正規化・長い属性の検証データ
     */
    public static function internalLinkCases(): array
    {
        // 大量の属性を持つ開始タグの検証用データ
        $manyAttributes = implode(' ', array_map(static function ($index) {
            return 'data-item-' . $index . '=x';
        }, range(1, 2000)));

        return [
            'HTTPSサイトからHTTP' => ['https://example.com', 'http://example.com/page/'],
            'HTTPサイトからHTTPS' => ['http://example.com', 'https://example.com/page/'],
            'HTTPSサブディレクトリからHTTP' => ['https://example.com/blog', 'http://example.com/blog/page/'],
            'HTTPサブディレクトリからHTTPS' => ['http://example.com/blog', 'https://example.com/blog/page/'],
            '自サイトURL末尾の空白' => ['https://example.com', 'https://example.com ', 'href="https://example.com "'],
            '文字参照のポート区切り' => ['https://example.com', 'https://example.com:443/page/', 'href="https://example.com&#58;443/page/"'],
            'title内の引用符に似た文字参照' => [
                'https://example.com',
                'http://example.com/page/',
                'title="例 &#8221; href=\'https://other.test/\'" href="http://example.com/page/"',
            ],
            '別属性内のhrefを含む内部と外部リンク' => [
                'https://example.com',
                'http://example.com/page/',
                'title="例 href=\'https://other.test/\'" href="http://example.com/page/"',
                'title=\'例 href = "https://example.com/"\' href="https://other.test/page/"',
            ],
            'title内のページ内リンク' => [
                'https://example.com',
                'http://example.com/page/',
                null,
                'title=\'例 href="#section"\' href="https://other.test/page/"',
            ],
            '長い引用符なし属性の外部リンク' => [
                'https://example.com',
                'http://example.com/page/',
                null,
                'data-example=' . str_repeat('x', 12000) . ' href="https://other.test/page/"',
            ],
            '長い属性間空白の外部リンク' => [
                'https://example.com',
                'http://example.com/page/',
                null,
                str_repeat(' ', 12000) . 'title="例" href="https://other.test/page/"',
            ],
            '大量の属性の外部リンク' => [
                'https://example.com',
                'http://example.com/page/',
                null,
                $manyAttributes . ' href="https://other.test/page/"',
            ],
        ];
    }

    /**
     * 実際の本文フィルターを通した内部・外部リンク設定と元URLの保持の検証
     *
     * @dataProvider internalLinkCases
     */
    public function testContentUsesInternalLinkSettings(
        string $home,
        string $url,
        ?string $internalAttributes = null,
        ?string $externalAttributes = null
    ): void {
        $originalHome = get_option('home');
        $originalThemeMods = get_theme_mods();
        $skinOptionsExisted = array_key_exists('_THEME_OPTIONS', $GLOBALS);
        $originalSkinOptions = $GLOBALS['_THEME_OPTIONS'] ?? null;

        try {
            // 実際のWordPress設定によるホームURLとリンク設定の準備
            update_option('home', $home);
            $GLOBALS['_THEME_OPTIONS'] = [];
            $settings = [
                'internal_link_open_type' => 'self',
                'internal_link_follow_type' => 'follow',
                'internal_link_icon_visible' => 1,
                'internal_link_icon' => 'fa-home',
                'external_link_open_type' => 'blank',
                'external_link_follow_type' => 'nofollow',
                'external_link_icon_visible' => 1,
                'external_link_icon' => 'fa-external-link',
                'easy_ssl_enable' => 0,
            ];
            foreach ($settings as $name => $value) {
                set_theme_mod($name, $value);
            }

            // 空白や文字参照も含めた検証用HTML属性の保持
            $internalAttributes = $internalAttributes ?? 'href="' . esc_url($url) . '"';
            $externalAttributes = $externalAttributes ?? 'href="https://other.test/page/"';
            $html = '<p><a id="scheme-internal" ' . $internalAttributes . '>内部</a> '
                . '<a id="scheme-external" ' . $externalAttributes . '>外部</a></p>';
            $result = apply_filters('the_content', $html);
            $internal = $this->getHtmlElementsByXPath($result, '//a[@id="scheme-internal"]')->item(0);
            $external = $this->getHtmlElementsByXPath($result, '//a[@id="scheme-external"]')->item(0);

            $this->assertInstanceOf(\DOMElement::class, $internal);
            $this->assertInstanceOf(\DOMElement::class, $external);
            $this->assertSame($url, $internal->getAttribute('href'));
            $this->assertSame('_self', $internal->getAttribute('target'));
            $this->assertStringContainsString('follow', $internal->getAttribute('rel'));
            $this->assertStringNotContainsString('nofollow', $internal->getAttribute('rel'));
            $this->assertSame('_blank', $external->getAttribute('target'));
            $this->assertSame('https://other.test/page/', $external->getAttribute('href'));
            $this->assertStringContainsString('nofollow', $external->getAttribute('rel'));
            $this->assertCount(1, $this->getHtmlElementsByXPath($result, '//a[@id="scheme-internal"]/span[contains(@class, "internal-icon")]'));
            $this->assertCount(1, $this->getHtmlElementsByXPath($result, '//a[@id="scheme-external"]/span[contains(@class, "external-icon")]'));
        } finally {
            // 他のテストへ影響を残さないホームURL・テーマ設定・スキン設定の復元
            update_option('home', $originalHome);
            $themeModsOption = 'theme_mods_' . get_option('stylesheet');
            if ($originalThemeMods === false) {
                delete_option($themeModsOption);
            } else {
                update_option($themeModsOption, $originalThemeMods);
            }
            if ($skinOptionsExisted) {
                $GLOBALS['_THEME_OPTIONS'] = $originalSkinOptions;
            } else {
                unset($GLOBALS['_THEME_OPTIONS']);
            }
        }
    }
}
