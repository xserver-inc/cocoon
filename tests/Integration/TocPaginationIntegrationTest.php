<?php
/**
 * 分割ページの目次リンクと本文アンカーの統合テスト
 */

namespace Cocoon\Tests\Integration;

class TocPaginationIntegrationTest extends IntegrationTestCase
{
    private array $originalThemeMods = [];
    private array $originalGlobals = [];

    protected function setUp(): void
    {
        parent::setUp();
        $mods = get_theme_mods();
        $settings = [
            'toc_visible' => 1,
            'single_toc_visible' => 1,
            'page_toc_visible' => 1,
            'multi_page_toc_visible' => 1,
            'toc_display_count' => 1,
            'toc_depth' => 0,
            'toc_heading_inner_html_tag_enable' => 1,
            'toc_toggle_switch_enable' => 0,
        ];
        // テスト終了時の復元に必要な設定値と存在状態の保存
        foreach ($settings as $name => $value) {
            $this->originalThemeMods[$name] = [
                'exists' => is_array($mods) && array_key_exists($name, $mods),
                'value' => $mods[$name] ?? null,
            ];
            set_theme_mod($name, $value);
        }
        foreach (['wp_query', 'wp_the_query', 'post', 'id', 'authordata', 'currentday', 'currentmonth',
                  'page', 'pages', 'multipage', 'more', 'numpages',
                  '_TOC_INDEX', '_TOC_AVAILABLE_H_COUNT', '_TOC_WIDGET_OR_SHORTCODE_USED'] as $name) {
            $this->originalGlobals[$name] = [
                'exists' => array_key_exists($name, $GLOBALS),
                'value' => $GLOBALS[$name] ?? null,
            ];
        }
        $this->setPermalinkStructure('/%postname%/');
    }

    protected function tearDown(): void
    {
        foreach ($this->originalThemeMods as $name => $original) {
            if ($original['exists']) {
                set_theme_mod($name, $original['value']);
            } else {
                remove_theme_mod($name);
            }
        }
        parent::tearDown();
        // 投稿表示と目次の状態の次のテストへの持ち越しの防止
        foreach ($this->originalGlobals as $name => $original) {
            if ($original['exists']) {
                $GLOBALS[$name] = $original['value'];
            } else {
                unset($GLOBALS[$name]);
            }
        }
    }

    private function createArticle(string $content): int
    {
        return $this->createPost([
            'post_title' => '分割目次テスト',
            'post_name' => 'toc-pagination-test',
            'post_status' => 'publish',
            'post_content' => $content,
        ]);
    }

    private function renderPage(int $postId, int $page): string
    {
        // 実際の投稿表示と同じクエリーによる改ページ本文の準備
        $postType = get_post_type($postId);
        $args = $postType === 'page' ? ['page_id' => $postId] : ['p' => $postId, 'post_type' => $postType];
        $query = new \WP_Query($args + ['page' => $page]);
        $GLOBALS['wp_query'] = $query;
        $GLOBALS['wp_the_query'] = $query;
        $query->the_post();
        return add_toc_before_1st_h2(get_toc_expanded_content());
    }

    private function headingIds(string $html): array
    {
        preg_match_all('/<span id="(toc\d+)">/', $html, $matches);
        return $matches[1];
    }

    private function tocLinks(string $html): array
    {
        preg_match_all('/<a href="([^"]*#toc\d+)"/', $html, $matches);
        return array_map(static fn($url) => html_entity_decode($url, ENT_QUOTES, 'UTF-8'), $matches[1]);
    }

    /**
     * 全ページの目次と本文のアンカーの通し番号およびリンク先の一致
     */
    public function test_all_pages_use_continuous_heading_ids_and_correct_urls(): void
    {
        $postId = $this->createArticle(
            '<h2>一</h2><h3>二</h3><!--nextpage--><h2>三</h2><h3>四</h3>' .
            '<!--nextpage--><h2>五</h2><h3>六</h3>'
        );
        $url = get_permalink($postId);
        for ($page = 1; $page <= 3; $page++) {
            $html = $this->renderPage($postId, $page);
            $first = ($page - 1) * 2 + 1;
            $this->assertSame(['toc' . $first, 'toc' . ($first + 1)], $this->headingIds($html));
            $expected = [];
            for ($index = 1; $index <= 6; $index++) {
                $targetPage = (int) ceil($index / 2);
                $prefix = $targetPage === $page ? '' : ($targetPage === 1 ? $url : $url . $targetPage . '/');
                $expected[] = $prefix . '#toc' . $index;
            }
            $this->assertSame($expected, $this->tocLinks($html));
        }
    }

    /**
     * 標準のクエリー形式のパーマリンクでの他ページへの目次URLの一致
     */
    public function test_plain_permalink_links_include_page_query_argument(): void
    {
        $this->setPermalinkStructure('');
        $postId = $this->createArticle('<h2>一</h2><h2>二</h2><!--nextpage--><h2>三</h2><h2>四</h2>');
        $url = get_permalink($postId);
        $first = $this->renderPage($postId, 1);
        $second = $this->renderPage($postId, 2);
        $this->assertSame(['#toc1', '#toc2', $url . '&page=2#toc3', $url . '&page=2#toc4'], $this->tocLinks($first));
        $this->assertSame([$url . '#toc1', $url . '#toc2', '#toc3', '#toc4'], $this->tocLinks($second));
        $this->assertSame(['toc3', 'toc4'], $this->headingIds($second));
    }

    /**
     * WordPressが無視する先頭の改ページを含む投稿の採番の一致
     */
    public function test_leading_page_break_does_not_shift_page_numbers(): void
    {
        $postId = $this->createArticle('<!--nextpage--><h2>一</h2><!--nextpage--><h2>二</h2>');
        $first = $this->renderPage($postId, 1);
        $second = $this->renderPage($postId, 2);
        set_theme_mod('multi_page_toc_visible', 0);
        $local = $this->renderPage($postId, 2);
        $this->assertSame(['toc1'], $this->headingIds($first));
        $this->assertSame(['toc2'], $this->headingIds($second));
        $this->assertSame(['toc2'], $this->headingIds($local));
        $this->assertSame(['#toc2'], $this->tocLinks($local));
    }

    /**
     * 全ページの目次の表示設定に左右されない本文IDと現在ページのリンク
     */
    public function test_local_toc_keeps_the_same_heading_ids(): void
    {
        $postId = $this->createArticle('<h2>一</h2><h2>二</h2><!--nextpage--><h2>三</h2><h2>四</h2>');
        $full = $this->renderPage($postId, 2);
        set_theme_mod('multi_page_toc_visible', 0);
        $local = $this->renderPage($postId, 2);
        $this->assertSame(['toc3', 'toc4'], $this->headingIds($local));
        $this->assertSame($this->headingIds($full), $this->headingIds($local));
        $this->assertSame(['#toc3', '#toc4'], $this->tocLinks($local));
    }

    /**
     * 目次の最低表示数に満たないページの本文アンカーの維持
     */
    public function test_hidden_toc_does_not_remove_heading_anchors(): void
    {
        $postId = $this->createArticle('<h2>一</h2><h2>二</h2><!--nextpage--><h2>三</h2>');
        set_theme_mod('toc_display_count', 2);
        $full = $this->renderPage($postId, 2);
        set_theme_mod('multi_page_toc_visible', 0);
        $local = $this->renderPage($postId, 2);
        $this->assertSame(['toc3'], $this->headingIds($full));
        $this->assertSame(['toc3'], $this->headingIds($local));
        $this->assertSame([], $this->tocLinks($local));
    }

    /**
     * H1と表示対象外の階層を除外した目次と本文の採番条件の一致
     */
    public function test_heading_depth_and_h1_do_not_shift_ids(): void
    {
        $postId = $this->createArticle(
            '<h1>対象外</h1><h2>一</h2><h3>対象外</h3><H2>二</H2>' .
            '<!--nextpage--><h3>対象外</h3><h2>三</h2>'
        );
        set_theme_mod('toc_depth', 2);
        $first = $this->renderPage($postId, 1);
        $second = $this->renderPage($postId, 2);
        $this->assertSame(['toc1', 'toc2'], $this->headingIds($first));
        $this->assertSame(['toc3'], $this->headingIds($second));
        $this->assertCount(3, $this->tocLinks($second));
    }

    /**
     * ブロックとクラシックの改ページが混在する見出しなしページの採番維持
     */
    public function test_block_page_break_and_empty_page_keep_continuous_ids(): void
    {
        $postId = $this->createArticle(
            '<h2>一</h2><h2>二</h2><!-- wp:nextpage --><!--nextpage--><!-- /wp:nextpage -->' .
            '<p>見出しなし</p><!--nextpage--><h2>三</h2><h2>四</h2>'
        );
        $this->assertSame([], $this->headingIds($this->renderPage($postId, 2)));
        $html = $this->renderPage($postId, 3);
        $this->assertSame(['toc3', 'toc4'], $this->headingIds($html));
        $this->assertSame([
            get_permalink($postId) . '#toc1', get_permalink($postId) . '#toc2', '#toc3', '#toc4',
        ], $this->tocLinks($html));
    }

    /**
     * 前ページのショートコードと同期パターンに含まれる見出しの採番への反映
     */
    public function test_expanded_headings_in_previous_pages_are_counted(): void
    {
        $patternId = $this->createPost([
            'post_type' => 'wp_block',
            'post_status' => 'publish',
            'post_content' => '<!-- wp:heading --><h2 class="wp-block-heading">同期見出し</h2><!-- /wp:heading -->',
        ]);
        add_shortcode('cocoon_toc_test_heading', static fn() => '<h2>ショートコード見出し</h2>');
        try {
            $postId = $this->createArticle(
                '[cocoon_toc_test_heading]<!-- wp:block {"ref":' . $patternId . '} /-->' .
                '<!--nextpage--><h2>次ページ</h2>'
            );
            $full = $this->renderPage($postId, 2);
            set_theme_mod('multi_page_toc_visible', 0);
            $local = $this->renderPage($postId, 2);
            $this->assertSame(['toc3'], $this->headingIds($full));
            $this->assertSame(['toc3'], $this->headingIds($local));
            $this->assertSame(['#toc3'], $this->tocLinks($local));
        } finally {
            remove_shortcode('cocoon_toc_test_heading');
        }
    }

    /**
     * ウィジェットとショートコードからの繰り返し生成時の採番の独立性
     */
    public function test_widget_and_shortcode_calls_do_not_shift_body_ids(): void
    {
        $postId = $this->createArticle('<h2>一</h2><h2>二</h2><!--nextpage--><h2>三</h2><h2>四</h2>');
        $body = $this->renderPage($postId, 2);
        $harray = [];
        $widget = get_toc_tag(get_toc_expanded_content(), $harray, true);
        $shortcode = do_shortcode('[toc]');
        $bodyAgain = add_toc_before_1st_h2(get_toc_expanded_content());
        $this->assertSame($this->tocLinks($body), $this->tocLinks($widget));
        $this->assertSame($this->tocLinks($body), $this->tocLinks($shortcode));
        $this->assertSame(['toc3', 'toc4'], $this->headingIds($bodyAgain));
    }

    /**
     * 目次が表示数に満たない場合の見出し内の装飾とリンクの維持
     */
    public function test_hidden_toc_preserves_heading_html(): void
    {
        set_theme_mod('multi_page_toc_visible', 0);
        set_theme_mod('toc_display_count', 2);
        set_theme_mod('toc_heading_inner_html_tag_enable', 0);
        $inner = '<strong>装飾</strong><a href="https://example.com/">リンク</a>';
        $postId = $this->createArticle('<h2>' . $inner . '</h2>');
        $html = $this->renderPage($postId, 1);
        $this->assertSame(['toc1'], $this->headingIds($html));
        $this->assertSame([], $this->tocLinks($html));
        $this->assertStringContainsString('<span id="toc1">' . $inner . '</span>', $html);
    }

    /**
     * WordPressの改ページフィルターで加工された本文と目次の採番の一致
     */
    public function test_content_pagination_filter_is_shared_with_toc(): void
    {
        $postId = $this->createArticle('<h2>元の本文</h2>');
        $filter = static function ($pages, $post) use ($postId) {
            return $post->ID === $postId ? [
                '<h2>一</h2><h2>二</h2>', '<h2>三</h2>', '<h2>四</h2>',
            ] : $pages;
        };
        add_filter('content_pagination', $filter, 10, 2);
        try {
            $full = $this->renderPage($postId, 2);
            $this->assertSame(['toc3'], $this->headingIds($full));
            $this->assertSame([
                get_permalink($postId) . '#toc1', get_permalink($postId) . '#toc2',
                '#toc3', get_permalink($postId) . '3/#toc4',
            ], $this->tocLinks($full));
            set_theme_mod('multi_page_toc_visible', 0);
            $local = $this->renderPage($postId, 2);
            $this->assertSame(['toc3'], $this->headingIds($local));
        } finally {
            remove_filter('content_pagination', $filter, 10);
        }
    }

    /**
     * 目次と同じ行に並ぶショートコードが生成する見出しの採番への反映
     */
    public function test_toc_does_not_remove_adjacent_heading_shortcode(): void
    {
        set_theme_mod('multi_page_toc_visible', 0);
        add_shortcode('cocoon_toc_review_heading', static fn() => '<h2>二</h2>');
        try {
            $postId = $this->createArticle('<h2>一</h2>[toc] [cocoon_toc_review_heading]<!--nextpage--><h2>三</h2>');
            $this->renderPage($postId, 1);
            $first = apply_filters('the_content', get_the_content());
            $this->assertSame(['toc1', 'toc2'], $this->headingIds($first));
            $second = $this->renderPage($postId, 2);
            $this->assertSame(['toc3'], $this->headingIds($second));
        } finally {
            remove_shortcode('cocoon_toc_review_heading');
        }
    }

    /**
     * ページ番号に応じて変わるショートコードの各ページの見出し数の反映
     */
    public function test_page_dependent_shortcodes_use_the_target_page_context(): void
    {
        set_theme_mod('multi_page_toc_visible', 0);
        add_shortcode('cocoon_toc_review_context', static fn() =>
            (int) get_query_var('page') === 1 ? '<h2>一</h2><h2>二</h2>' : '<h2>一</h2>');
        try {
            $postId = $this->createArticle('[cocoon_toc_review_context]<!--nextpage--><h2>三</h2>');
            $first = $this->renderPage($postId, 1);
            $second = $this->renderPage($postId, 2);
            $this->assertSame(['toc1', 'toc2'], $this->headingIds($first));
            $this->assertSame(['toc3'], $this->headingIds($second));
            $this->assertSame(2, (int) get_query_var('page'));
            $this->assertSame(2, (int) $GLOBALS['page']);
        } finally {
            remove_shortcode('cocoon_toc_review_context');
        }
    }

    /**
     * 末尾スラッシュなしのパーマリンクの改ページURLの一致
     */
    public function test_pretty_permalink_without_trailing_slash(): void
    {
        $this->setPermalinkStructure('/%postname%');
        $postId = $this->createArticle('<h2>一</h2><!--nextpage--><h2>二</h2>');
        $html = $this->renderPage($postId, 1);
        $this->assertSame(['#toc1', get_permalink($postId) . '/2#toc2'], $this->tocLinks($html));
    }

    /**
     * 固定フロントページの改ページURLへのページネーション基底の反映
     */
    public function test_static_front_page_links_use_pagination_base(): void
    {
        $showOnFront = get_option('show_on_front');
        $pageOnFront = get_option('page_on_front');
        $postId = $this->createPost([
            'post_type' => 'page', 'post_status' => 'publish',
            'post_content' => '<h2>一</h2><!--nextpage--><h2>二</h2>',
        ]);
        try {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $postId);
            $html = $this->renderPage($postId, 1);
            $this->assertSame([
                '#toc1', trailingslashit(home_url()) . $GLOBALS['wp_rewrite']->pagination_base . '/2/#toc2',
            ], $this->tocLinks($html));
            $this->assertSame(['toc2'], $this->headingIds($this->renderPage($postId, 2)));
        } finally {
            update_option('show_on_front', $showOnFront);
            update_option('page_on_front', $pageOnFront);
        }
    }

    /**
     * リライトなしのカスタム投稿タイプのクエリー形式の改ページURLの維持
     */
    public function test_custom_post_type_without_rewrite_keeps_query_url(): void
    {
        register_post_type('cocoon_toc_test', ['public' => true, 'rewrite' => false]);
        try {
            $postId = $this->createPost([
                'post_type' => 'cocoon_toc_test', 'post_status' => 'publish',
                'post_content' => '<h2>一</h2><!--nextpage--><h2>二</h2>',
            ]);
            $html = $this->renderPage($postId, 1);
            $this->assertSame([
                '#toc1', add_query_arg('page', 2, get_permalink($postId)) . '#toc2',
            ], $this->tocLinks($html));
            $this->assertSame(['toc2'], $this->headingIds($this->renderPage($postId, 2)));
        } finally {
            unregister_post_type('cocoon_toc_test');
        }
    }

    /**
     * 公開済み投稿のプレビューと下書きの改ページURLへのクエリー引数の反映
     */
    public function test_preview_and_draft_page_urls(): void
    {
        $postId = $this->createArticle('<h2>一</h2><!--nextpage--><h2>二</h2>');
        $this->renderPage($postId, 1);
        $originalGet = $_GET;
        $originalPreview = $GLOBALS['wp_query']->is_preview;
        try {
            $GLOBALS['wp_query']->is_preview = true;
            $_GET['preview_id'] = (string) $postId;
            $_GET['preview_nonce'] = 'toc-test-nonce';
            $this->assertSame(add_query_arg([
                'preview_id' => (string) $postId, 'preview_nonce' => 'toc-test-nonce', 'preview' => 'true',
            ], get_permalink($postId) . '2/'), get_toc_page_url($postId, 2));
            $draftId = $this->createPost([
                'post_status' => 'draft', 'post_title' => '下書き目次テスト',
                'post_content' => '<h2>一</h2><!--nextpage--><h2>二</h2>',
            ]);
            $this->assertSame(add_query_arg([
                'page' => 2, 'preview' => 'true',
            ], get_permalink($draftId)), get_toc_page_url($draftId, 2));
        } finally {
            $_GET = $originalGet;
            $GLOBALS['wp_query']->is_preview = $originalPreview;
        }
    }

    /**
     * 別ページの展開における本文フィルターの適用と改ページフィルターの再実行防止
     */
    public function test_expansion_filters_match_without_reapplying_pagination(): void
    {
        set_theme_mod('multi_page_toc_visible', 0);
        $postId = $this->createArticle('<h2>一</h2><!--nextpage--><h2>三</h2>');
        $calls = 0;
        $pagination = static function ($pages, $post) use ($postId, &$calls) {
            if ($post->ID === $postId) $calls++;
            return $pages;
        };
        $expanded = static fn($content) => (int) get_query_var('page') === 1 ? $content . '<h2>二</h2>' : $content;
        add_filter('content_pagination', $pagination, 10, 2);
        add_filter('get_toc_expanded_content', $expanded);
        try {
            $html = $this->renderPage($postId, 2);
            $this->assertSame(['toc3'], $this->headingIds($html));
            $this->assertSame(1, $calls);
        } finally {
            remove_filter('content_pagination', $pagination, 10);
            remove_filter('get_toc_expanded_content', $expanded);
        }
    }

    /**
     * ショートコードの例外発生時のページ番号の値と未定義状態の復元
     */
    public function test_expansion_restores_page_context_after_exception(): void
    {
        $postId = $this->createArticle('<h2>一</h2><!--nextpage--><h2>二</h2>');
        $this->renderPage($postId, 2);
        add_shortcode('cocoon_toc_review_throw', static function () {
            throw new \RuntimeException('展開テストの例外');
        });
        try {
            foreach ([true, false] as $defined) {
                if (!$defined) {
                    unset($GLOBALS['page'], $GLOBALS['wp_query']->query_vars['page']);
                }
                try {
                    get_toc_expanded_page_content('[cocoon_toc_review_throw]', 1);
                    $this->fail('例外が発生する必要があります');
                } catch (\RuntimeException $error) {
                    $this->assertSame('展開テストの例外', $error->getMessage());
                }
                $this->assertSame($defined, array_key_exists('page', $GLOBALS));
                $this->assertSame($defined, array_key_exists('page', $GLOBALS['wp_query']->query_vars));
                if ($defined) {
                    $this->assertSame(2, (int) $GLOBALS['page']);
                    $this->assertSame(2, (int) get_query_var('page'));
                }
            }
        } finally {
            remove_shortcode('cocoon_toc_review_throw');
        }
    }

    /**
     * 除外対象の派生名と囲み形式および隣接する通常ショートコードの維持
     */
    public function test_shortcode_removal_preserves_unrelated_neighbors(): void
    {
        add_shortcode('navi_toc_review', static fn() => '<h2>対象外</h2>');
        try {
            $this->assertSame(' [cocoon_heading]  [cocoon_heading] ', get_shortcode_removed_content(
                '[toc] [cocoon_heading] [[escaped]] [cocoon_heading] [navi_toc_review]対象外[/navi_toc_review]'
            ));
        } finally {
            remove_shortcode('navi_toc_review');
        }
    }

    /**
     * 改ページのない投稿の通常の本文フィルターでの従来の採番維持
     */
    public function test_single_page_content_filter_starts_at_toc1(): void
    {
        $postId = $this->createArticle('<h2>同じ見出し</h2><p>本文</p><h2>同じ見出し</h2>');
        $this->renderPage($postId, 1);
        $html = apply_filters('the_content', get_the_content());
        $this->assertSame(['toc1', 'toc2'], $this->headingIds($html));
        $this->assertSame(['#toc1', '#toc2'], $this->tocLinks($html));
    }
}
