<?php
/**
 * 新着情報リストのHTML出力テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

// WordPressを読み込まない子プロセス内で利用するクエリースタブ
final class InfoListWpQueryStub
{
    /** @var array<int, object> テストで返す投稿 */
    public static array $test_posts = [];

    /** @var array<string, mixed>|string 最後に受け取ったクエリー引数 */
    public static $last_query_args = null;

    /** @var array<int, object> クエリー生成時に確定した投稿 */
    public array $posts = [];

    /** @var int クエリー結果の件数 */
    public int $post_count = 0;

    /** @var int 現在の投稿位置 */
    public int $current_post = -1;

    /** @var object|null 現在の投稿 */
    public $post = null;

    /** @var bool ループ処理中か */
    public bool $in_the_loop = false;

    // 本物のWP_Queryと同じく文字列または配列を受け取るための型指定省略
    public function __construct($args = [])
    {
        self::$last_query_args = $args;
        $this->posts = self::$test_posts;
        $this->post_count = count($this->posts);
    }

    public function have_posts(): bool
    {
        if ($this->current_post + 1 < $this->post_count) {
            return true;
        }

        if ($this->current_post + 1 === $this->post_count && $this->post_count > 0) {
            $this->rewind_posts();
        }

        $this->in_the_loop = false;

        return false;
    }

    public function the_post(): void
    {
        $this->in_the_loop = true;
        $this->current_post++;
        $this->post = $this->posts[$this->current_post];
        $GLOBALS['post'] = $this->post;
    }

    public function rewind_posts(): void
    {
        $this->current_post = -1;
        $this->post = $this->post_count > 0 ? $this->posts[0] : null;
    }

    public static function reset(): void
    {
        self::$test_posts = [];
        self::$last_query_args = null;
    }
}

// 新着情報ウィジェットの呼び出し元テスト用WP_Widgetスタブ
class InfoListWpWidgetStub
{
    /** @var string ウィジェットのIDベース */
    public $id_base = '';

    public function __construct($id_base = '', $name = '', $widget_options = [], $control_options = [])
    {
        $this->id_base = $id_base;
    }
}

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class InfoListHtmlFormsTest extends TestCase
{
    /** @var bool テスト開始時に$GLOBALS['post']が存在したか */
    private $had_global_post = false;

    /** @var mixed テスト開始時の$GLOBALS['post'] */
    private $original_global_post = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once dirname(__DIR__, 2) . '/lib/html-forms.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists('WP_Query', false)) {
            throw new \LogicException('WP_Queryがテスト開始前に定義されています。');
        }
        class_alias(InfoListWpQueryStub::class, 'WP_Query', false);
        if (!class_exists('WP_Widget', false)) {
            class_alias(InfoListWpWidgetStub::class, 'WP_Widget', false);
        }

        InfoListWpQueryStub::reset();

        // ループで書き換えるグローバルの退避
        $this->had_global_post = array_key_exists('post', $GLOBALS);
        $this->original_global_post = $this->had_global_post ? $GLOBALS['post'] : null;

        // 新着情報リストの生成に必要なWordPress関数のテスト用置換
        Functions\when('get_archive_exclude_category_ids')->justReturn([]);
        Functions\when('get_archive_exclude_post_ids')->justReturn([]);
        Functions\when('get_site_date_format')->justReturn('Y/m/d');
        Functions\when('get_post_timestamp')->justReturn(false);
        Functions\when('get_post_type')->justReturn('post');
        Functions\when('get_taxonomy')->justReturn((object) ['hierarchical' => true]);
        Functions\when('get_the_terms')->justReturn([]);
        Functions\when('get_comments_number')->justReturn(7);
        Functions\when('post_class')->alias(function ($class): void {
            echo 'class="' . esc_attr($class) . '"';
        });
        Functions\when('the_permalink')->alias(function (): void {
            $post_id = isset($GLOBALS['post']->ID) ? $GLOBALS['post']->ID : 0;
            echo 'https://example.com/test/' . esc_attr($post_id) . '/';
        });
    }

    protected function tearDown(): void
    {
        // 静的プロパティーとグローバルの後始末
        InfoListWpQueryStub::reset();

        if ($this->had_global_post) {
            $GLOBALS['post'] = $this->original_global_post;
        } else {
            unset($GLOBALS['post']);
        }
        unset($GLOBALS['test_mock_translations']);

        parent::tearDown();
    }

    private function expectPostdataReset(): void
    {
        Functions\expect('wp_reset_postdata')->once()->andReturn(null);
    }

    private function renderInfoList(array $attributes): string
    {
        ob_start();
        generate_info_list_tag($attributes);
        return ob_get_clean();
    }

    private function createXPath(string $html): \DOMXPath
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML(
                '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>'
                . $html
                . '</body></html>',
                LIBXML_HTML_NODEFDTD | LIBXML_NONET
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $this->assertTrue($loaded);

        return new \DOMXPath($document);
    }

    public function test_generate_info_list_tag_記事がある場合は表示用クラスを出力する(): void
    {
        InfoListWpQueryStub::$test_posts = [(object) ['ID' => 1]];
        $this->expectPostdataReset();

        $output = $this->renderInfoList([
            'caption' => '新着情報',
            'frame' => 1,
            'divider' => 1,
        ]);

        $this->assertStringContainsString(
            '<div class="info-list is-style-frame-border is-style-divider-line">',
            $output
        );
        $this->assertStringContainsString('class="info-list-item"', $output);
        $this->assertStringNotContainsString('is-empty', $output);
        $this->assertSame(5, InfoListWpQueryStub::$last_query_args['posts_per_page']);
        $this->assertSame([], InfoListWpQueryStub::$last_query_args['cat']);
    }

    public function test_generate_info_list_tag_記事がある場合は全件を出力する(): void
    {
        InfoListWpQueryStub::$test_posts = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
            (object) ['ID' => 3],
        ];
        $this->expectPostdataReset();

        $output = $this->renderInfoList([
            'caption' => '新着情報',
        ]);

        $this->assertSame(3, substr_count($output, 'class="info-list-item"'));
        $this->assertMatchesRegularExpression(
            '#test/1/.*test/2/.*test/3/#s',
            $output
        );
    }

    public function test_generate_info_list_tag_枠線と区切り線を無効にできる(): void
    {
        InfoListWpQueryStub::$test_posts = [(object) ['ID' => 1]];
        $this->expectPostdataReset();

        $output = $this->renderInfoList([
            'caption' => '',
            'frame' => 0,
            'divider' => 0,
        ]);

        $this->assertStringContainsString('<div class="info-list">', $output);
        $this->assertStringNotContainsString('is-style-frame-border', $output);
        $this->assertStringNotContainsString('is-style-divider-line', $output);
        $this->assertStringNotContainsString('info-list-caption', $output);
    }

    public function test_generate_info_list_tag_WP_Queryへ必要な引数を渡す(): void
    {
        $this->expectPostdataReset();

        $this->renderInfoList([
            'count' => 3,
            'cats' => [10, 20],
            'caption' => '',
            'modified' => 1,
            'offset' => 2,
            'action' => 'unit-test',
            'post_type' => 'book',
            'taxonomy' => 'genre',
        ]);

        $this->assertSame(
            [
                'post_type' => 'book',
                'no_found_rows' => true,
                'ignore_sticky_posts' => true,
                'posts_per_page' => 3,
                'offset' => 2,
                'action' => 'unit-test',
                'orderby' => 'modified',
                'tax_query' => [
                    [
                        'taxonomy' => 'genre',
                        'field' => 'term_id',
                        'terms' => [10, 20],
                    ],
                ],
            ],
            InfoListWpQueryStub::$last_query_args
        );
    }

    public function test_generate_info_list_tag_記事がない場合は空状態を識別できる(): void
    {
        $unsafe_message = '<script>alert("x")</script>';
        $escaped_message = '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;';

        $GLOBALS['test_mock_translations']['記事は見つかりませんでした。'] = $unsafe_message;
        $this->expectPostdataReset();

        $output = $this->renderInfoList([
            'caption' => '',
            'frame' => 1,
            'divider' => 1,
        ]);

        $this->assertStringContainsString('<div class="info-list is-empty">', $output);
        $this->assertStringContainsString($escaped_message, $output);
        $this->assertStringNotContainsString($unsafe_message, $output);
        $this->assertStringNotContainsString('is-style-frame-border', $output);
        $this->assertStringNotContainsString('is-style-divider-line', $output);
        $this->assertStringNotContainsString('info-list-caption', $output);
        $this->assertStringNotContainsString('info-list-item', $output);

        $xpath = $this->createXPath($output);
        $messages = $xpath->query(
            '//div['
            . 'contains(concat(" ", normalize-space(@class), " "), " info-list ")'
            . ' and contains(concat(" ", normalize-space(@class), " "), " is-empty ")'
            . ']/p[@class="info-list-empty-message"]'
        );
        $all_messages = $xpath->query('//p[@class="info-list-empty-message"]');
        $scripts = $xpath->query('//p[@class="info-list-empty-message"]//script');

        $this->assertNotFalse($messages);
        $this->assertNotFalse($all_messages);
        $this->assertNotFalse($scripts);
        $this->assertSame(1, $messages->length);
        $this->assertSame(1, $all_messages->length);
        $this->assertSame(0, $scripts->length);
        $this->assertSame($unsafe_message, $messages->item(0)->textContent);
    }

    public function test_ショートコードの既定値を生成関数へ渡す(): void
    {
        require_once dirname(__DIR__, 2) . '/lib/shortcodes.php';

        InfoListWpQueryStub::$test_posts = [(object) ['ID' => 1]];
        $this->expectPostdataReset();

        $output = get_info_list_shortcode([]);

        $this->assertSame(5, InfoListWpQueryStub::$last_query_args['posts_per_page']);
        $this->assertSame([], InfoListWpQueryStub::$last_query_args['cat']);
        $this->assertSame('post', InfoListWpQueryStub::$last_query_args['post_type']);
        $this->assertArrayNotHasKey('tax_query', InfoListWpQueryStub::$last_query_args);
        $this->assertStringNotContainsString('info-list-item-comment', $output);
    }

    public function test_ショートコードの投稿タイプとタクソノミーを生成関数へ渡す(): void
    {
        require_once dirname(__DIR__, 2) . '/lib/shortcodes.php';

        InfoListWpQueryStub::$test_posts = [(object) ['ID' => 1]];
        $this->expectPostdataReset();

        $output = get_info_list_shortcode([
            'post_type' => 'book',
            'cats' => '10,20',
            'taxonomy' => 'genre',
            'comment' => 1,
            'caption' => '',
        ]);

        $this->assertSame('book', InfoListWpQueryStub::$last_query_args['post_type']);
        $this->assertArrayNotHasKey('cat', InfoListWpQueryStub::$last_query_args);
        $this->assertSame(
            [
                [
                    'taxonomy' => 'genre',
                    'field' => 'term_id',
                    'terms' => [10, 20],
                ],
            ],
            InfoListWpQueryStub::$last_query_args['tax_query']
        );
        $this->assertStringContainsString('info-list-item-comment', $output);
    }

    public function test_ウィジェットのカテゴリーを生成関数へ渡す(): void
    {
        if (!defined('WIDGET_NAME_PREFIX')) {
            define('WIDGET_NAME_PREFIX', '');
        }
        if (!defined('WM_DEFAULT')) {
            define('WM_DEFAULT', 0);
        }
        if (!defined('EC_DEFAULT')) {
            define('EC_DEFAULT', 5);
        }
        require_once dirname(__DIR__, 2) . '/lib/widgets/info-list.php';

        InfoListWpQueryStub::$test_posts = [(object) ['ID' => 1]];
        $this->expectPostdataReset();

        $widget = new \InfoListWidgetItem();
        ob_start();
        $widget->widget(
            [
                'before_widget' => '<aside class="widget widget_info_list">',
                'after_widget' => '</aside>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ],
            [
                'count' => 4,
                'cat_ids' => [3, 5],
                'is_frame' => 1,
                'is_divider' => 1,
                'modified' => 1,
            ]
        );
        $output = ob_get_clean();

        $this->assertSame(4, InfoListWpQueryStub::$last_query_args['posts_per_page']);
        $this->assertSame('3,5', InfoListWpQueryStub::$last_query_args['cat']);
        $this->assertSame('post', InfoListWpQueryStub::$last_query_args['post_type']);
        $this->assertSame('modified', InfoListWpQueryStub::$last_query_args['orderby']);
        $this->assertStringContainsString('<aside class="widget widget_info_list">', $output);
        $this->assertStringNotContainsString('info-list-item-comment', $output);
    }

    public function test_ブロックの属性を生成関数へ渡してクラスをエスケープする(): void
    {
        require_once dirname(__DIR__, 2) . '/blocks/src/block/info-list/index.php';

        InfoListWpQueryStub::$test_posts = [(object) ['ID' => 1]];
        $this->expectPostdataReset();

        $output = render_block_cocoon_block_info_list(
            [
                'classNames' => 'info-list-box" onmouseover="alert(1)',
                'count' => 6,
                'showAllCats' => false,
                'cats' => '2,9',
                'caption' => '',
                'showFrame' => false,
                'showDivider' => false,
                'modified' => false,
                'comment' => true,
            ],
            ''
        );

        $this->assertSame(6, InfoListWpQueryStub::$last_query_args['posts_per_page']);
        $this->assertSame('2,9', InfoListWpQueryStub::$last_query_args['cat']);
        $this->assertSame('post', InfoListWpQueryStub::$last_query_args['post_type']);
        $this->assertStringContainsString('class="info-list-box&quot; onmouseover=&quot;alert(1)"', $output);
        $this->assertStringNotContainsString('class="info-list-box" onmouseover=', $output);
        $this->assertStringContainsString('info-list-item-comment', $output);
        $this->assertStringNotContainsString('is-style-frame-border', $output);
        $this->assertStringNotContainsString('is-style-divider-line', $output);
    }

    public function test_SCSSの変更が配布用CSSへ生成されている(): void
    {
        $theme_root = dirname(__DIR__, 2);
        $stylesheets = [
            $theme_root . '/style.css',
            $theme_root . '/amp.css',
            $theme_root . '/css/entry-content.css',
            $theme_root . '/css/admin.css',
        ];

        foreach ($stylesheets as $stylesheet) {
            $css = file_get_contents($stylesheet);

            $this->assertNotFalse($css, $stylesheet);
            $this->assertMatchesRegularExpression(
                '/\.info-list\.is-empty\s*\{\s*padding:\s*0;\s*\}/',
                $css,
                $stylesheet
            );
            $this->assertMatchesRegularExpression(
                '/\.widget_info_list > \.info-list:last-child\s*\{\s*margin-bottom:\s*0;\s*\}/',
                $css,
                $stylesheet
            );
        }

        $dynamic_css_source = file_get_contents($theme_root . '/tmp/css-custom.php');

        $this->assertNotFalse($dynamic_css_source);
        $this->assertMatchesRegularExpression(
            '/\.body \.info-list,\R/',
            $dynamic_css_source
        );
    }
}
