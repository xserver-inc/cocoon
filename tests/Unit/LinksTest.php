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
use PHPUnit\Framework\Attributes\DataProvider;

class LinksTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/links.php';
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
