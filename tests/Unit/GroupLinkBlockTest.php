<?php
/**
 * グループブロックのリンク化機能のユニットテスト
 */

// WP_HTML_Tag_Processor の簡易モッククラス
// （Cocoonのユニットテスト環境ではWPコアクラスがロードされないため）
namespace {
    if (!class_exists('WP_HTML_Tag_Processor')) {
        class WP_HTML_Tag_Processor {
            private $html;
            public $attributes = [];
            public $classes = [];

            public function __construct($html) {
                $this->html = $html;
            }

            public function next_tag() {
                return !empty(trim($this->html));
            }

            public function set_attribute($name, $value) {
                $this->attributes[$name] = $value;
            }

            public function add_class($class) {
                $this->classes[] = $class;
            }

            public function get_updated_html() {
                $attr_str = '';
                foreach ($this->attributes as $k => $v) {
                    $attr_str .= ' ' . $k . '="' . $v . '"';
                }
                if (!empty($this->classes)) {
                    $attr_str .= ' class="' . implode(' ', $this->classes) . '"';
                }
                return preg_replace('/^(<[a-zA-Z0-9-]+)([^>]*>)/', '$1' . $attr_str . '$2', $this->html);
            }
        }
    }
}

namespace Cocoon\Tests\Unit {

    use Cocoon\Tests\TestCase;
    use Brain\Monkey\Functions;

    class GroupLinkBlockTest extends TestCase
    {
        public static function setUpBeforeClass(): void
        {
            parent::setUpBeforeClass();
            require_once dirname(__DIR__, 2) . '/lib/block-editor-group-link.php';
        }

        protected function setUp(): void
        {
            parent::setUp();
        }

        /**
         * core/group ブロックに対してカスタム属性（cocoonLinkUrl 等）が正しく登録されることをテスト
         */
        public function test_register_attributes_core_group()
        {
            $args = [];
            $result = cocoon_group_block_link_register_attributes($args, 'core/group');

            $this->assertArrayHasKey('attributes', $result);
            $this->assertArrayHasKey('cocoonLinkUrl', $result['attributes']);
            $this->assertArrayHasKey('cocoonLinkTarget', $result['attributes']);
            $this->assertSame('', $result['attributes']['cocoonLinkUrl']['default']);
            $this->assertFalse($result['attributes']['cocoonLinkTarget']['default']);
        }

        /**
         * core/group 以外のブロックではカスタム属性が登録されない（スキップされる）ことをテスト
         */
        public function test_register_attributes_not_group()
        {
            $args = ['attributes' => ['foo' => 'bar']];
            $result = cocoon_group_block_link_register_attributes($args, 'core/paragraph');

            $this->assertSame($args, $result);
            $this->assertArrayNotHasKey('cocoonLinkUrl', $result['attributes']);
        }

        /**
         * リンクURLが設定され、かつ「同じタブで開く（Target=false）」の場合に、
         * 正しい data 属性、アクセシビリティ属性、およびCSSクラスが付与されることをテスト
         */
        public function test_render_with_url_same_tab()
        {
            $block = [
                'blockName' => 'core/group',
                'attrs' => [
                    'cocoonLinkUrl' => 'https://example.com/',
                    'cocoonLinkTarget' => false
                ]
            ];
            $content = '<div class="wp-block-group">Test</div>';

            $result = cocoon_group_block_link_render($content, $block);

            $this->assertStringContainsString('data-cocoon-group-link="https://example.com/"', $result);
            $this->assertStringNotContainsString('data-cocoon-group-link-target', $result);
            $this->assertStringContainsString('role="link"', $result);
            $this->assertStringContainsString('tabindex="0"', $result);
            $this->assertStringContainsString('onkeydown="if(event.target === this && (event.key === \'Enter\' || event.key === \' \')){ event.preventDefault(); this.click(); }"', $result);
            // aria-label は URL 読み上げを避けるため付与しない設計
            $this->assertStringNotContainsString('aria-label=', $result);
            $this->assertStringContainsString('class="is-cocoon-group-link"', $result);
        }

        /**
         * リンクURLが設定され、かつ「新しいタブで開く（Target=true）」の場合に、
         * 新規タブ用（_blank）の data 属性が付与されることをテスト
         */
        public function test_render_with_url_new_tab()
        {
            $block = [
                'blockName' => 'core/group',
                'attrs' => [
                    'cocoonLinkUrl' => 'https://example.com/',
                    'cocoonLinkTarget' => true
                ]
            ];
            $content = '<div class="wp-block-group">Test</div>';

            $result = cocoon_group_block_link_render($content, $block);

            $this->assertStringContainsString('data-cocoon-group-link-target="_blank"', $result);
        }

        /**
         * リンクURLが空（空白文字のみ等）の場合は、ブロックのHTMLが改変されずそのまま返されることをテスト
         */
        public function test_render_empty_url()
        {
            $block = [
                'blockName' => 'core/group',
                'attrs' => [
                    'cocoonLinkUrl' => '   ',
                ]
            ];
            $content = '<div class="wp-block-group">Test</div>';
            $result = cocoon_group_block_link_render($content, $block);
            $this->assertSame($content, $result);
        }

        /**
         * ブロックのコンテンツ自体が空の場合は、処理がスキップされ空のまま返されることをテスト
         */
        public function test_render_empty_content()
        {
            $block = [
                'blockName' => 'core/group',
                'attrs' => [
                    'cocoonLinkUrl' => 'https://example.com/',
                ]
            ];
            $content = '';
            $result = cocoon_group_block_link_render($content, $block);
            $this->assertSame('', $result);
        }
    }
}
