<?php
/**
 * ブロックエディターのスタイル配置とREST事前取得の回帰テスト
 */

namespace Cocoon\Tests\Integration;

class BlockEditorCompatibilityTest extends IntegrationTestCase
{
    private array $originalGlobals = [];
    private array $originalHooks = [];
    private array $screenRegistry = [];
    private ?\ReflectionProperty $screenRegistryProperty = null;
    private $originalPreloadPriority = false;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['current_screen', 'wp_styles', 'wp_scripts', 'wp_actions'] as $name) {
            $this->originalGlobals[$name] = [array_key_exists($name, $GLOBALS), $GLOBALS[$name] ?? null];
        }
        $this->originalPreloadPriority = has_filter('block_editor_rest_api_preload_paths', 'cocoon_adjust_block_editor_preload_paths');
        foreach (['enqueue_block_assets', 'enqueue_block_editor_assets', 'block_editor_rest_api_preload_paths', 'init'] as $hook) {
            $this->originalHooks[$hook] = isset($GLOBALS['wp_filter'][$hook]) ? clone $GLOBALS['wp_filter'][$hook] : null;
            if ($hook !== 'init') {
                unset($GLOBALS['wp_filter'][$hook]);
            }
        }
        if (function_exists('cocoon_adjust_block_editor_preload_paths')) {
            add_filter('block_editor_rest_api_preload_paths', 'cocoon_adjust_block_editor_preload_paths', 10, 2);
        }
        require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
        require_once ABSPATH . 'wp-admin/includes/screen.php';
        // 他のテストの画面オブジェクトを変更しないレジストリの隔離
        $this->screenRegistryProperty = new \ReflectionProperty(\WP_Screen::class, '_registry');
        $this->screenRegistry = $this->screenRegistryProperty->getValue();
        $this->screenRegistryProperty->setValue(null, []);
        $GLOBALS['current_screen'] = \WP_Screen::get('post');
        $GLOBALS['wp_styles'] = new \WP_Styles();
        $GLOBALS['wp_scripts'] = new \WP_Scripts();
        add_action('enqueue_block_editor_assets', 'wp_enqueue_global_styles_css_custom_properties');
    }

    protected function tearDown(): void
    {
        if ($this->screenRegistryProperty !== null) {
            $this->screenRegistryProperty->setValue(null, $this->screenRegistry);
        }
        foreach ($this->originalHooks as $hook => $original) {
            if ($original === null) {
                unset($GLOBALS['wp_filter'][$hook]);
            } else {
                $GLOBALS['wp_filter'][$hook] = $original;
            }
        }
        foreach ($this->originalGlobals as $name => [$existed, $value]) {
            if ($existed) {
                $GLOBALS[$name] = $value;
            } else {
                unset($GLOBALS[$name]);
            }
        }
        parent::tearDown();
    }

    public function test_コアのCSS変数をコンテンツ用フックから一度だけ読み込む(): void
    {
        $this->assertTrue(function_exists('cocoon_setup_block_editor_asset_hooks'));
        cocoon_setup_block_editor_asset_hooks();
        $handle = 'global-styles-css-custom-properties';
        // iframeの資産収集方式が異なる旧WordPressでの従来フックの維持
        if (version_compare(get_bloginfo('version'), '6.3', '<')) {
            $this->assertSame(10, has_action('enqueue_block_editor_assets', 'wp_enqueue_global_styles_css_custom_properties'));
            $this->assertFalse(has_action('enqueue_block_assets', 'cocoon_enqueue_editor_global_style_variables'));
            do_action('enqueue_block_editor_assets');
            $this->assertTrue(wp_style_is($handle, 'enqueued'));
            $before = wp_styles()->get_data($handle, 'after');
            $this->assertCount(1, $before);
            $this->assertStringContainsString('--wp--preset--color--', $before[0]);
            do_action('enqueue_block_assets');
            $this->assertSame($before, wp_styles()->get_data($handle, 'after'));
            return;
        }
        $this->assertFalse(has_action('enqueue_block_editor_assets', 'wp_enqueue_global_styles_css_custom_properties'));
        $this->assertSame(10, has_action('enqueue_block_assets', 'cocoon_enqueue_editor_global_style_variables'));
        do_action('enqueue_block_assets');
        $this->assertTrue(wp_style_is($handle, 'enqueued'));
        $before = wp_styles()->get_data($handle, 'after');
        $this->assertCount(1, $before);
        $this->assertStringContainsString('--wp--preset--color--', $before[0]);
        do_action('enqueue_block_editor_assets');
        $this->assertSame($before, wp_styles()->get_data($handle, 'after'));
        // 実際のiframe用アセット収集でも同じCSSを増やさないことの確認
        if (function_exists('_wp_get_iframed_editor_assets')) {
            $assets = _wp_get_iframed_editor_assets();
            $this->assertStringContainsString($handle . '-inline-css', $assets['styles']);
            $this->assertSame($before, wp_styles()->get_data($handle, 'after'));
        }
    }

    public function test_登録済みのコンテンツ用フックを重複させない(): void
    {
        $this->assertTrue(function_exists('cocoon_setup_block_editor_asset_hooks'));
        add_action('enqueue_block_assets', 'wp_enqueue_global_styles_css_custom_properties', 20);
        cocoon_setup_block_editor_asset_hooks();
        cocoon_setup_block_editor_asset_hooks();
        $this->assertSame(20, has_action('enqueue_block_assets', 'wp_enqueue_global_styles_css_custom_properties'));
        do_action('enqueue_block_assets');
        $this->assertCount(1, wp_styles()->get_data('global-styles-css-custom-properties', 'after'));
    }

    public function test_フロント側の登録先を変更しない(): void
    {
        $this->assertTrue(function_exists('cocoon_setup_block_editor_asset_hooks'));
        unset($GLOBALS['current_screen']);
        cocoon_setup_block_editor_asset_hooks();
        $this->assertSame(10, has_action('enqueue_block_editor_assets', 'wp_enqueue_global_styles_css_custom_properties'));
        $this->assertFalse(has_action('enqueue_block_assets', 'wp_enqueue_global_styles_css_custom_properties'));
        $this->assertFalse(has_action('enqueue_block_assets', 'cocoon_enqueue_editor_global_style_variables'));
    }

    public function test_公開フックを経由して事前取得の補正が登録される(): void
    {
        $this->assertSame(10, $this->originalPreloadPriority);
        $this->assertSame(10, has_action('after_setup_theme', 'cocoon_setup_block_editor_asset_hooks'));
    }

    public function test_日本語スラッグのテンプレート事前取得を実リクエストと一致させる(): void
    {
        $post = $this->makePost('post', 'editor-warning-日本語');
        $slug = 'single-post-' . $post->post_name;
        $original = add_query_arg('slug', $slug, '/wp/v2/templates/lookup');
        $paths = $this->filterPaths([$original, '/wp/v2/templates/lookup?slug=front-page'], $post);
        wp_parse_str(wp_parse_url($paths[0], PHP_URL_QUERY), $query);
        $this->assertSame($slug, $query['slug']);
        $this->assertSame('/wp/v2/templates/lookup?slug=front-page', $paths[1]);
        $this->assertSame($paths, $this->filterPaths($paths, $post));
    }

    public function test_英数字のスラッグは元のURLを保持する(): void
    {
        $post = $this->makePost('post', 'editor-warning-ascii');
        $paths = ['/wp/v2/templates/lookup?slug=single-post-' . $post->post_name];
        $this->assertSame($paths, $this->filterPaths($paths, $post));
    }

    public function test_文字列ゼロのスラッグをJavaScriptと同じく保持する(): void
    {
        $this->setPermalinkStructure('');
        $post = get_post($this->createPost(['post_type' => 'post', 'post_name' => '0', 'post_title' => '0', 'post_content' => '<p>zero slug</p>', 'post_status' => 'publish', 'post_author' => 1]));
        $this->assertSame('0', $post->post_name);
        // PHPのemptyとJavaScriptの真偽判定が異なる文字列0の維持
        $paths = $this->filterPaths(['/wp/v2/templates/lookup?slug=single-post'], $post);
        wp_parse_str(wp_parse_url($paths[0], PHP_URL_QUERY), $query);
        $this->assertSame('single-post-0', $query['slug']);
    }

    public function test_固定ページとカスタム投稿も正しいテンプレート名を保持する(): void
    {
        register_post_type('warning_review', ['public' => true, 'show_in_rest' => true, 'supports' => ['editor', 'author']]);
        try {
            foreach (['page' => 'page', 'warning_review' => 'single-warning_review'] as $type => $prefix) {
                $post = $this->makePost($type, 'editor-warning-日本語');
                $slug = $prefix . '-' . $post->post_name;
                $paths = $this->filterPaths([add_query_arg('slug', $slug, '/wp/v2/templates/lookup')], $post);
                wp_parse_str(wp_parse_url($paths[0], PHP_URL_QUERY), $query);
                $this->assertSame($slug, $query['slug']);
            }
        } finally {
            unregister_post_type('warning_review');
        }
    }

    public function test_未消費の投稿者GETだけを除外して他の要求を保持する(): void
    {
        $post = $this->makePost('post', 'editor-warning-author');
        $author = '/wp/v2/users/1?context=view&_fields=id,name';
        $preserved = [
            '/wp/v2/users/me',
            '/wp/v2/users?context=view&include%5B0%5D=1&who=authors',
            '/wp/v2/users/2?context=view&_fields=id,name',
            '/wp/v2/users/1?context=view&_fields=id,name,slug',
            '/wp/v2/users/1?context=edit&_fields=id,name',
            '/wp/v2/users/1?context=view&_fields=id,name&_embed=1',
            [$author, 'OPTIONS'],
            ['/wp/v2/posts', 'OPTIONS'],
        ];
        $this->assertSame($preserved, $this->filterPaths(array_merge([$author], $preserved), $post));
    }

    public function test_GETの配列形式とクエリ順序の差にも対応する(): void
    {
        $post = $this->makePost('post', 'editor-warning-日本語');
        $slug = 'single-post-' . $post->post_name;
        $template = add_query_arg('slug', $slug, '/wp/v2/templates/lookup');
        $paths = $this->filterPaths([['/wp/v2/users/1?_fields=id%2Cname&context=view', 'GET'], [$template, 'GET'], [$template, 'OPTIONS']], $post);
        $this->assertCount(2, $paths);
        $this->assertSame('GET', $paths[0][1]);
        wp_parse_str(wp_parse_url($paths[0][0], PHP_URL_QUERY), $query);
        $this->assertSame($slug, $query['slug']);
        $this->assertSame([$template, 'OPTIONS'], $paths[1]);
    }

    public function test_投稿がない画面や投稿編集以外の事前取得を変更しない(): void
    {
        $paths = ['/wp/v2/users/1?context=view&_fields=id,name'];
        $this->assertSame($paths, apply_filters('block_editor_rest_api_preload_paths', $paths, new \WP_Block_Editor_Context()));
        $post = $this->makePost('post', 'editor-warning-context');
        foreach (['core/edit-site', 'core/edit-widgets'] as $name) {
            $context = new \WP_Block_Editor_Context(['post' => $post, 'name' => $name]);
            $this->assertSame($paths, apply_filters('block_editor_rest_api_preload_paths', $paths, $context));
        }
    }

    private function makePost(string $type, string $slug): \WP_Post
    {
        return get_post($this->createPost(['post_type' => $type, 'post_name' => $slug, 'post_title' => 'Editor warning test', 'post_status' => 'draft', 'post_author' => 1]));
    }

    private function filterPaths(array $paths, \WP_Post $post): array
    {
        return apply_filters('block_editor_rest_api_preload_paths', $paths, new \WP_Block_Editor_Context(['post' => $post]));
    }
}
