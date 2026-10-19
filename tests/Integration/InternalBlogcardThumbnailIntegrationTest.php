<?php
/**
 * 内部ブログカードの代替画像とリンク先情報のWordPress統合テスト
 */

namespace Cocoon\Tests\Integration;

class InternalBlogcardThumbnailIntegrationTest extends IntegrationTestCase
{
    private array $savedGlobals = [];
    private array $filters = [];
    private array $cardUrls = [];
    private array $taxonomies = [];
    private int $fallbackImageCalls = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // カード生成前後に変わる投稿・クエリー・スキン設定の退避
        $globalNames = [
            'post', 'wp_query', 'wp_the_query', '_THEME_OPTIONS',
            'id', 'authordata', 'currentday', 'currentmonth',
            'page', 'pages', 'multipage', 'more', 'numpages',
        ];
        foreach ($globalNames as $name) {
            $this->savedGlobals[$name] = [array_key_exists($name, $GLOBALS), $GLOBALS[$name] ?? null];
        }
        $GLOBALS['_THEME_OPTIONS'] = [];
        $this->addTestFilter('theme_mod_no_image_url', fn () => $this->assetUrl('transparence.png'));
        $this->addTestFilter('theme_mod_auto_post_thumbnail_enable', static fn () => 0);

        // サムネイルの有無に応じた代替画像取得回数の監視
        $this->addTestFilter('get_sized_no_image_url', function ($url) {
            $this->fallbackImageCalls++;
            return $url;
        });
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            // テスト用フィルター・URLキャッシュ・分類登録の後始末
            foreach ($this->filters as [$name, $callback]) {
                remove_filter($name, $callback, 10);
            }
            foreach ($this->cardUrls as $url) {
                delete_transient('cocoon_url_tpi_' . md5($url));
            }
            foreach ($this->taxonomies as $taxonomy) {
                unregister_taxonomy($taxonomy);
            }
            foreach ($this->savedGlobals as $name => [$exists, $value]) {
                if ($exists) {
                    $GLOBALS[$name] = $value;
                } else {
                    unset($GLOBALS[$name]);
                }
            }
        }
    }

    /**
     * 異なるカテゴリーの記事に置いたカードがリンク先のカテゴリー画像を使う確認
     */
    public function test_リンク元と異なるリンク先のカテゴリー画像を選択(): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $targetId = $this->createCategorizedPost('ojisan.png');

        $this->assertSame($this->assetUrl('ojisan.png'), $this->cardImage(get_permalink($targetId)));
    }

    /**
     * カテゴリーのない固定ページに置いたカードもリンク先のカテゴリー画像を使う確認
     */
    public function test_リンク元が固定ページでもリンク先のカテゴリー画像を選択(): void
    {
        $sourceId = $this->createPost(['post_title' => 'カード設置ページ', 'post_type' => 'page', 'post_status' => 'publish']);
        $this->setSourcePost($sourceId);
        $targetId = $this->createCategorizedPost('ojisan.png');

        $this->assertSame($this->assetUrl('ojisan.png'), $this->cardImage(get_permalink($targetId)));
    }

    /**
     * リンク先に代替画像がない場合にリンク元の画像を使わずNO IMAGEへ戻る確認
     */
    public function test_リンク先にカテゴリー画像がなければNO_IMAGEを選択(): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $targetId = $this->createCategorizedPost();

        $this->assertSame($this->assetUrl('transparence.png'), $this->cardImage(get_permalink($targetId)));
    }

    /**
     * リンク先で指定したメインカテゴリーの画像を優先する確認
     */
    public function test_リンク先のメインカテゴリー画像を優先(): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $targetId = $this->createCategorizedPost('ojisan.png');
        $mainId = $this->createCategory(['name' => 'メイン-' . wp_generate_uuid4()]);
        update_term_meta($mainId, 'the_category_eye_catch_url', $this->assetUrl('obasan.png'));
        wp_set_post_categories($targetId, [$mainId], true);
        update_post_meta($targetId, 'the_page_main_category', $mainId);

        $this->assertSame($this->assetUrl('obasan.png'), $this->cardImage(get_permalink($targetId)));
    }

    /**
     * カテゴリー画像のないリンク先に設定したタグ画像を補完する確認
     */
    public function test_リンク先のタグ画像による補完(): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $targetId = $this->createCategorizedPost();
        $tagId = $this->createTag(['name' => '画像タグ-' . wp_generate_uuid4()]);
        update_term_meta($tagId, 'the_tag_eye_catch_url', $this->assetUrl('obasan.png'));
        wp_set_post_tags($targetId, [$tagId]);

        $this->assertSame($this->assetUrl('obasan.png'), $this->cardImage(get_permalink($targetId)));
    }

    /**
     * カスタム分類の画像も表示中の記事ではなくリンク先のタームを参照する確認
     */
    public function test_リンク先のカスタムタクソノミー画像による補完(): void
    {
        $taxonomy = 'bc_image_test';
        register_taxonomy($taxonomy, 'post', ['public' => true]);
        $this->taxonomies[] = $taxonomy;
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $targetId = $this->createCategorizedPost();
        $term = wp_insert_term('独自画像-' . wp_generate_uuid4(), $taxonomy);
        $this->assertIsArray($term);
        $termId = (int) $term['term_id'];

        try {
            update_term_meta($termId, 'the_tag_eye_catch_url', $this->assetUrl('obasan.png'));
            wp_set_object_terms($targetId, [$termId], $taxonomy);
            $this->assertSame($this->assetUrl('obasan.png'), $this->cardImage(get_permalink($targetId)));
        } finally {
            wp_delete_term($termId, $taxonomy);
        }
    }

    /**
     * リンク先のサムネイルHTMLを優先し代替画像を取得しない確認
     */
    public function test_リンク先のサムネイルHTMLを優先(): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $targetId = $this->createCategorizedPost('ojisan.png');
        $imageUrl = $this->assetUrl('obasan.png');

        // WordPressの公開フックによるリンク先サムネイルの検証用HTML
        $this->addTestFilter('post_thumbnail_html', static function ($html, $postId) use ($targetId, $imageUrl) {
            return (int) $postId === $targetId ? '<img src="' . esc_url($imageUrl) . '">' : $html;
        }, 2);

        $callsBefore = $this->fallbackImageCalls;
        $this->assertSame($imageUrl, $this->cardImage(get_permalink($targetId)));
        $this->assertSame($callsBefore, $this->fallbackImageCalls);
    }

    /**
     * カテゴリーとタグのカードが画像のない場合だけNO IMAGEを取得する確認
     *
     * @dataProvider archiveImageProvider
     */
    public function test_アーカイブへのカードへリンク元の画像を混入しない(string $taxonomy, bool $hasImage): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        if ($taxonomy === 'category') {
            $termId = $this->createCategory(['name' => 'リンク先分類-' . wp_generate_uuid4()]);
            $metaKey = 'the_category_eye_catch_url';
            $url = get_category_link($termId);
        } else {
            $termId = $this->createTag(['name' => 'リンク先タグ-' . wp_generate_uuid4()]);
            $metaKey = 'the_tag_eye_catch_url';
            $url = get_tag_link($termId);
        }
        if ($hasImage) {
            update_term_meta($termId, $metaKey, $this->assetUrl('obasan.png'));
        }

        $expected = $hasImage ? 'obasan.png' : 'transparence.png';
        $callsBefore = $this->fallbackImageCalls;
        $this->assertSame($this->assetUrl($expected), $this->cardImage($url));
        $this->assertSame($callsBefore + ($hasImage ? 0 : 1), $this->fallbackImageCalls);
    }

    public static function archiveImageProvider(): array
    {
        return [
            'カテゴリー画像あり' => ['category', true],
            'カテゴリー画像なし' => ['category', false],
            'タグ画像あり' => ['post_tag', true],
            'タグ画像なし' => ['post_tag', false],
        ];
    }

    /**
     * ホームへのカードがホーム画像のない場合だけNO IMAGEを取得する確認
     */
    public function test_ホームへのカードへリンク元の画像を混入しない(): void
    {
        $this->setSourcePost($this->createCategorizedPost('woman.png'));
        $homeImage = '';
        $this->addTestFilter('get_ogp_home_image_url', static function () use (&$homeImage) { return $homeImage; });

        $callsBefore = $this->fallbackImageCalls;
        $this->assertSame($this->assetUrl('transparence.png'), $this->cardImage(home_url('/')));
        $this->assertSame($callsBefore + 1, $this->fallbackImageCalls);
        $homeImage = $this->assetUrl('obasan.png');
        $this->assertSame($homeImage, $this->cardImage(home_url('/')));
        $this->assertSame($callsBefore + 1, $this->fallbackImageCalls);
    }

    /**
     * カテゴリー画像があっても通常投稿のOGP画像には補完しない既存仕様の確認
     */
    public function test_OGP画像のカテゴリー補完を追加しない(): void
    {
        $this->setSourcePost($this->createCategorizedPost('ojisan.png'));

        $this->assertSame($this->assetUrl('transparence.png'), get_singular_sns_share_image_url());
    }

    private function createCategorizedPost(string $image = ''): int
    {
        $categoryId = $this->createCategory(['name' => 'カード分類-' . wp_generate_uuid4()]);
        if ($image !== '') {
            update_term_meta($categoryId, 'the_category_eye_catch_url', $this->assetUrl($image));
        }

        return $this->createPost([
            'post_title' => 'ブログカード確認記事',
            'post_status' => 'publish',
            'post_category' => [$categoryId],
        ]);
    }

    private function setSourcePost(int $postId): void
    {
        // WordPressのリセット処理でもリンク元を維持するメインクエリーの設定
        $query = new \WP_Query(['p' => $postId, 'post_type' => get_post_type($postId)]);
        $GLOBALS['wp_query'] = $query;
        $GLOBALS['wp_the_query'] = $query;
        $GLOBALS['post'] = get_post($postId);
    }

    private function cardImage(string $url): string
    {
        $this->cardUrls[] = $url;
        $html = url_to_internal_blogcard_tag($url);
        $this->assertIsString($html);

        // ファビコンを除外したカード画像要素の取得
        $document = new \DOMDocument();
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($document);
        $image = $xpath->query('//figure[contains(@class, "internal-blogcard-thumbnail")]//img')->item(0);
        $this->assertInstanceOf(\DOMElement::class, $image);

        return $image->hasAttribute('data-src') ? $image->getAttribute('data-src') : $image->getAttribute('src');
    }

    private function assetUrl(string $filename): string
    {
        return get_cocoon_template_directory_uri() . '/images/' . $filename;
    }

    private function addTestFilter(string $name, callable $callback, int $acceptedArgs = 1): void
    {
        add_filter($name, $callback, 10, $acceptedArgs);
        $this->filters[] = [$name, $callback];
    }
}
