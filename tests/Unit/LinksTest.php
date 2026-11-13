<?php
/**
 * リンク処理関数のユニットテスト
 *
 * 配列操作（add_string_to_array, delete_string_from_array）、
 * rel属性操作（noopener, noreferrer, external, follow/nofollow）、
 * target属性置換、アイコンフォント挿入をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

class LinksTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/links.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 内部・外部の判定結果を区別できるリンク設定の準備
        $settings = [
            'get_internal_link_open_type' => 'self',
            'get_internal_link_follow_type' => 'follow',
            'is_internal_link_noopener_enable' => false,
            'is_internal_link_noreferrer_enable' => false,
            'is_internal_link_icon_visible' => true,
            'get_internal_link_icon' => 'fa-home',
            'get_external_link_open_type' => 'blank',
            'get_external_link_follow_type' => 'nofollow',
            'is_external_link_noopener_enable' => true,
            'is_external_link_noreferrer_enable' => true,
            'is_external_link_external_enable' => true,
            'is_external_link_icon_visible' => true,
            'get_external_link_icon' => 'fa-external-link',
        ];
        foreach ($settings as $function => $value) {
            Functions\when($function)->justReturn($value);
        }
    }

    /**
     * 同じホストと別ホストのリンクに適用する開き方・rel属性・アイコンの検証
     */
    #[DataProvider('anchorLinkAttributes')]
    public function test_replace_anchor_links_ホスト名に応じたリンク設定の適用(string $attributes, bool $internal): void
    {
        $result = replace_anchor_links('<a ' . $attributes . '>リンク</a>');

        $this->assertStringContainsString($attributes, $result);
        if ($internal) {
            $this->assertStringContainsString('target="_self"', $result);
            $this->assertStringContainsString('rel="follow"', $result);
            $this->assertStringContainsString('fa-home internal-icon anchor-icon', $result);
            $this->assertStringNotContainsString('external-icon', $result);
            $this->assertStringNotContainsString('nofollow', $result);
        } else {
            $this->assertStringContainsString('target="_blank"', $result);
            $this->assertStringContainsString('rel="nofollow noopener noreferrer external"', $result);
            $this->assertStringContainsString('fa-external-link external-icon anchor-icon', $result);
            $this->assertStringNotContainsString('internal-icon', $result);
        }
    }

    /**
     * プロトコル差・引用符・URLの正規化・長い属性を含む検証データ
     */
    public static function anchorLinkAttributes(): array
    {
        // 大量の属性を持つ開始タグの検証用データ
        $manyAttributes = implode(' ', array_map(static function ($index) {
            return 'data-item-' . $index . '=x';
        }, range(1, 2000)));

        return [
            'HTTPの自サイト' => ['href="http://example.com/page/"', true],
            'HTTPSの自サイト' => ['href="https://example.com/page/"', true],
            '大文字の自サイト' => ['href="HTTPS://EXAMPLE.COM/page/"', true],
            'プロトコル省略の自サイト' => ['href="//example.com/page/"', true],
            'ルート相対URL' => ['href="/page/"', true],
            '相対URL' => ['href="page/"', true],
            '親ディレクトリへの相対URL' => ['href="../page/"', true],
            'クエリーのみ' => ['href="?page=2"', true],
            'メールリンクの既存動作' => ['href="mailto:info@example.com"', true],
            'HTTPの別サイト' => ['href="http://other.test/page/"', false],
            'HTTPSの別サイト' => ['href="https://other.test/page/"', false],
            'プロトコル省略の別サイト' => ['href="//other.test/page/"', false],
            'サブドメイン' => ['href="http://sub.example.com/page/"', false],
            'ホスト名の前方一致' => ['href="http://example.com.other.test/page/"', false],
            'ユーザー情報に自サイト名を含むURL' => ['href="http://example.com@other.test/page/"', false],
            'パスに自サイトURLを含む別サイト' => ['href="http://other.test/http://example.com/page/"', false],
            '一重引用符の自サイト' => ["href='https://example.com/page/'", true],
            '一重引用符の別サイト' => ["href='https://other.test/page/'", false],
            '属性値の前後に空白のある別サイト' => ['href = "https://other.test/page/"', false],
            '自サイトURL末尾の空白' => ['href="http://example.com "', true],
            '自サイトURL前後の空白と改行' => ["href=\" \tHTTPS://example.com\r\n \"", true],
            '別サイトURL前後の空白' => ['href=" https://other.test/page/ "', false],
            '数値文字参照のポート区切り' => ['href="http://example.com&#58;80/page/"', true],
            '名前付き文字参照のポート区切り' => ['href="http://example.com&colon;80/page/"', true],
            '大文字を含む名前付き文字参照' => ['href="http://example.com&NewLine;"', true],
            '二重引用符のtitle内にあるhref' => ["title=\"例 href='https://other.test/'\" href=\"http://example.com/page/\"", true],
            '一重引用符のtitle内にあるhref' => ['title=\'例 href = "http://example.com/"\' href="https://other.test/page/"', false],
            'data属性内にあるhref' => ['data-example=\'href="https://other.test/"\' href="https://example.com/page/"', true],
            'title内に閉じ山括弧とhrefを含む属性' => ["title=\"1 > 0 href='https://other.test/'\" href=\"https://example.com/page/\"", true],
            'title内の引用符に似た文字参照' => ["title=\"例 &#8221; href='https://other.test/'\" href=\"http://example.com/page/\"", true],
            'hrefに似た別属性' => ['data-href="https://other.test/" href="https://example.com/page/"', true],
            'title内にあるページ内リンク' => ['title=\'例 href="#section"\' href="https://other.test/page/"', false],
            '長い引用符なし属性の外部リンク' => ['data-example=' . str_repeat('x', 12000) . ' href="https://other.test/page/"', false],
            '長い属性間空白の外部リンク' => [str_repeat(' ', 12000) . 'title="例" href="https://other.test/page/"', false],
            '大量の属性の外部リンク' => [$manyAttributes . ' href="https://other.test/page/"', false],
            '長い引用符あり属性の外部リンク' => ['data-example="' . str_repeat('x', 12000) . '" href="https://other.test/page/"', false],
            '長い外部URL' => ['href="https://other.test/' . str_repeat('x', 12000) . '"', false],
        ];
    }

    /**
     * 外部リンクの直後に続く相対URLとHTTPSの自サイトURLの内部判定
     */
    public function test_replace_anchor_links_複数リンクの判定を個別に適用(): void
    {
        $html = '<a href="https://other.test/">外部</a>'
            . '<a href="/page/">相対</a>'
            . '<a href="https://example.com/">内部</a>';

        $result = replace_anchor_links($html);

        $this->assertSame(2, substr_count($result, 'internal-icon'));
        $this->assertSame(1, substr_count($result, 'external-icon'));
        $this->assertSame(2, substr_count($result, 'target="_self"'));
        $this->assertSame(1, substr_count($result, 'target="_blank"'));
    }

    /**
     * 目次などのページ内リンクの属性と本文の維持
     */
    public function test_replace_anchor_links_ページ内リンクを変更しない(): void
    {
        $html = '<a href="#section" rel="nofollow">目次</a>';

        $this->assertSame($html, replace_anchor_links($html));
    }

    /**
     * 長い属性の後ろにあるページ内リンクの属性と本文の維持
     */
    public function test_replace_anchor_links_長い属性のページ内リンクを変更しない(): void
    {
        $html = '<a data-example=' . str_repeat('x', 12000) . ' href="#section" rel="nofollow">目次</a>';

        // 長文の差分生成を避けたHTML全体の一致確認
        $this->assertTrue($html === replace_anchor_links($html), '長い属性のあるページ内リンクのHTMLが変更されています');
    }

    // ========================================================================
    // add_string_to_array() - 配列に文字列を追加
    // ========================================================================

    public function test_add_string_to_array_空配列に追加する(): void
    {
        $result = add_string_to_array('noopener', []);
        $this->assertContains('noopener', $result);
    }

    public function test_add_string_to_array_既存の配列に追加する(): void
    {
        $result = add_string_to_array('noreferrer', ['noopener']);
        $this->assertContains('noopener', $result);
        $this->assertContains('noreferrer', $result);
    }

    public function test_add_string_to_array_重複する場合は追加しない(): void
    {
        $result = add_string_to_array('noopener', ['noopener']);
        $this->assertCount(1, $result);
    }

    // ========================================================================
    // delete_string_from_array() - 配列から文字列を削除
    // ========================================================================

    public function test_delete_string_from_array_存在する要素を削除する(): void
    {
        $result = delete_string_from_array('noopener', ['noopener', 'noreferrer']);
        $this->assertNotContains('noopener', $result);
        $this->assertContains('noreferrer', $result);
    }

    public function test_delete_string_from_array_存在しない要素は何も変わらない(): void
    {
        $result = delete_string_from_array('external', ['noopener', 'noreferrer']);
        $this->assertCount(2, $result);
    }

    public function test_delete_string_from_array_空配列から削除しても空配列(): void
    {
        $result = delete_string_from_array('noopener', []);
        $this->assertEmpty($result);
    }

    // ========================================================================
    // get_noopener_rels() - noopenerの追加と削除
    // ========================================================================

    public function test_get_noopener_rels_有効時にnoopenerを追加する(): void
    {
        $result = get_noopener_rels(true, []);
        $this->assertContains('noopener', $result);
    }

    public function test_get_noopener_rels_無効時にnoopenerを削除する(): void
    {
        $result = get_noopener_rels(false, ['noopener', 'noreferrer']);
        $this->assertNotContains('noopener', $result);
        $this->assertContains('noreferrer', $result);
    }

    public function test_get_noopener_rels_有効時に重複追加しない(): void
    {
        $result = get_noopener_rels(true, ['noopener']);
        $count = array_count_values($result);
        $this->assertSame(1, $count['noopener']);
    }

    // ========================================================================
    // get_noreferrer_rels() - noreferrerの追加と削除
    // ========================================================================

    public function test_get_noreferrer_rels_有効時にnoreferrerを追加する(): void
    {
        $result = get_noreferrer_rels(true, []);
        $this->assertContains('noreferrer', $result);
    }

    public function test_get_noreferrer_rels_無効時にnoreferrerを削除する(): void
    {
        $result = get_noreferrer_rels(false, ['noopener', 'noreferrer']);
        $this->assertNotContains('noreferrer', $result);
        $this->assertContains('noopener', $result);
    }

    // ========================================================================
    // get_external_rels() - externalの追加と削除
    // ========================================================================

    public function test_get_external_rels_有効時にexternalを追加する(): void
    {
        $result = get_external_rels(true, []);
        $this->assertContains('external', $result);
    }

    public function test_get_external_rels_無効時にexternalを削除する(): void
    {
        $result = get_external_rels(false, ['external', 'noopener']);
        $this->assertNotContains('external', $result);
        $this->assertContains('noopener', $result);
    }

    // ========================================================================
    // get_rel_follow_attr_values() - follow/nofollowの制御
    // ========================================================================

    public function test_get_rel_follow_attr_values_nofollowタイプでnofollowを追加する(): void
    {
        $result = get_rel_follow_attr_values('nofollow', []);
        $this->assertContains('nofollow', $result);
        $this->assertNotContains('follow', $result);
    }

    public function test_get_rel_follow_attr_values_followタイプでfollowを追加する(): void
    {
        $result = get_rel_follow_attr_values('follow', []);
        $this->assertContains('follow', $result);
        $this->assertNotContains('nofollow', $result);
    }

    public function test_get_rel_follow_attr_values_nofollowタイプで既存のfollowを削除する(): void
    {
        $result = get_rel_follow_attr_values('nofollow', ['follow']);
        $this->assertContains('nofollow', $result);
        $this->assertNotContains('follow', $result);
    }

    public function test_get_rel_follow_attr_values_followタイプで既存のnofollowを削除する(): void
    {
        $result = get_rel_follow_attr_values('follow', ['nofollow']);
        $this->assertContains('follow', $result);
        $this->assertNotContains('nofollow', $result);
    }

    public function test_get_rel_follow_attr_values_defaultタイプは変更しない(): void
    {
        $rels = ['noopener', 'noreferrer'];
        $result = get_rel_follow_attr_values('default', $rels);
        $this->assertSame($rels, $result);
    }

    // ========================================================================
    // replace_target_attr_tag() - target属性の置換
    // ========================================================================

    public function test_replace_target_attr_tag_blankでtarget_blankを追加する(): void
    {
        $tag = '<a href="https://example.com">リンク</a>';
        $result = replace_target_attr_tag('blank', $tag);
        $this->assertStringContainsString('target="_blank"', $result);
    }

    public function test_replace_target_attr_tag_selfでtarget_selfを追加する(): void
    {
        $tag = '<a href="https://example.com">リンク</a>';
        $result = replace_target_attr_tag('self', $tag);
        $this->assertStringContainsString('target="_self"', $result);
    }

    public function test_replace_target_attr_tag_blankで既存のtargetを置換する(): void
    {
        $tag = '<a href="https://example.com" target="_self">リンク</a>';
        $result = replace_target_attr_tag('blank', $tag);
        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertStringNotContainsString('target="_self"', $result);
    }

    public function test_replace_target_attr_tag_defaultでは変更しない(): void
    {
        $tag = '<a href="https://example.com" target="_blank">リンク</a>';
        $result = replace_target_attr_tag('default', $tag);
        $this->assertSame($tag, $result);
    }

    // ========================================================================
    // replace_link_icon_font_tag() - アイコンフォントの挿入
    // ========================================================================

    public function test_replace_link_icon_font_tag_有効時にアイコンを挿入する(): void
    {
        $tag = '<a href="https://example.com">リンク</a>';
        $result = replace_link_icon_font_tag(true, 'fa-external-link', 'external-icon', $tag);
        $this->assertStringContainsString('<span class="fa fa-external-link external-icon"></span></a>', $result);
    }

    public function test_replace_link_icon_font_tag_無効時には変更しない(): void
    {
        $tag = '<a href="https://example.com">リンク</a>';
        $result = replace_link_icon_font_tag(false, 'fa-external-link', 'external-icon', $tag);
        $this->assertSame($tag, $result);
    }

    // ========================================================================
    // is_anchor_link_tag_blogcard() - ブログカード判定
    // ========================================================================

    public function test_is_anchor_link_tag_blogcard_ブログカードクラスがあればtrue(): void
    {
        $tag = '<a href="https://example.com" class="blogcard-wrap internal">リンク</a>';
        $this->assertTrue(is_anchor_link_tag_blogcard($tag));
    }

    public function test_is_anchor_link_tag_blogcard_通常リンクはnull(): void
    {
        $tag = '<a href="https://example.com">リンク</a>';
        $this->assertNull(is_anchor_link_tag_blogcard($tag));
    }
}
