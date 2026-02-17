<?php
/**
 * toc.php のユニットテスト
 *
 * 目次（Table of Contents）生成に関わる関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class TocTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // グローバル名前空間のスタブ関数・定数を読み込み
        require_once dirname(__DIR__) . '/toc-test-helpers.php';

        // toc.phpを読み込み
        require_once dirname(__DIR__, 2) . '/lib/toc.php';
    }

    // ========================================================================
    // get_h_inner_content() — 見出し内容取得
    // ========================================================================

    public function test_get_h_inner_content_プレーンテキストはそのまま返す(): void
    {
        $result = \get_h_inner_content('シンプルな見出し');
        $this->assertSame('シンプルな見出し', $result);
    }

    public function test_get_h_inner_content_HTMLタグ無効時にstripされる(): void
    {
        // デフォルト（is_toc_heading_inner_html_tag_enable = false）では
        // HTMLタグが除去される
        $result = \get_h_inner_content('<strong>太字</strong>見出し');
        $this->assertSame('太字見出し', $result);
    }

    public function test_get_h_inner_content_spanタグが除去される(): void
    {
        $result = \get_h_inner_content('<span class="highlight">装飾付き</span>テキスト');
        $this->assertSame('装飾付きテキスト', $result);
    }

    public function test_get_h_inner_content_複数のHTMLタグが除去される(): void
    {
        $input = '<em>イタリック</em>と<strong>太字</strong>と<span>スパン</span>';
        $result = \get_h_inner_content($input);
        $this->assertSame('イタリックと太字とスパン', $result);
    }

    public function test_get_h_inner_content_アコーディオンボタンはそのまま返す(): void
    {
        // aria-expanded属性を持つbuttonタグは除去しない
        $input = '<button aria-expanded="false">アコーディオン見出し</button>';
        $result = \get_h_inner_content($input);
        $this->assertSame($input, $result);
    }

    public function test_get_h_inner_content_aria_expanded_trueでもそのまま返す(): void
    {
        $input = '<button class="toggle" aria-expanded="true">開いた見出し</button>';
        $result = \get_h_inner_content($input);
        $this->assertSame($input, $result);
    }

    public function test_get_h_inner_content_空文字列を返す(): void
    {
        $this->assertSame('', \get_h_inner_content(''));
    }

    // ========================================================================
    // get_toc_filter_priority() — 目次フィルター優先度
    // ========================================================================

    public function test_get_toc_filter_priority_標準の優先度を返す(): void
    {
        // is_toc_before_ads() = false なのでSTANDARD優先度
        $result = \get_toc_filter_priority();
        $this->assertSame(BEFORE_1ST_H2_TOC_PRIORITY_STANDARD, $result);
    }

    public function test_get_toc_filter_priority_は整数を返す(): void
    {
        $result = \get_toc_filter_priority();
        $this->assertIsInt($result);
    }

    // ========================================================================
    // is_toc_display_count_available() — 目次表示に必要な見出し数チェック
    // ========================================================================

    public function test_is_toc_display_count_available_最小数以上でtrue(): void
    {
        // get_toc_display_count() = 2（モック）なので、2以上でtrue
        $this->assertTrue(\is_toc_display_count_available(3));
    }

    public function test_is_toc_display_count_available_ちょうど最小数でtrue(): void
    {
        $this->assertTrue(\is_toc_display_count_available(2));
    }

    public function test_is_toc_display_count_available_最小数未満でfalse(): void
    {
        $this->assertFalse(\is_toc_display_count_available(1));
    }

    public function test_is_toc_display_count_available_0でfalse(): void
    {
        $this->assertFalse(\is_toc_display_count_available(0));
    }

    // ========================================================================
    // expand_synced_patterns() — 同期パターン展開
    // ========================================================================

    public function test_expand_synced_patterns_パターンなしのコンテンツはそのまま(): void
    {
        $content = '<p>通常のコンテンツです。</p>';
        $result = \expand_synced_patterns($content);
        $this->assertSame($content, $result);
    }

    public function test_expand_synced_patterns_空文字列は空文字列を返す(): void
    {
        $this->assertSame('', \expand_synced_patterns(''));
    }

    public function test_expand_synced_patterns_通常のHTMLブロックはそのまま(): void
    {
        $content = '<!-- wp:paragraph --><p>テスト</p><!-- /wp:paragraph -->';
        $result = \expand_synced_patterns($content);
        $this->assertSame($content, $result);
    }

    public function test_expand_synced_patterns_パターンブロックがある場合でも処理される(): void
    {
        // get_post()がnullを返すモックなので、元のブロックマークアップが残る
        $content = '<p>前のテキスト</p><!-- wp:block {"ref":123} /--><p>後のテキスト</p>';
        $result = \expand_synced_patterns($content);
        // get_post()はnullを返すため、パターンは展開されずそのまま残る
        $this->assertStringContainsString('wp:block', $result);
        $this->assertStringContainsString('前のテキスト', $result);
        $this->assertStringContainsString('後のテキスト', $result);
    }

    // ========================================================================
    // get_toc_tag() — 目次HTML生成（基本パターン）
    // 注: get_toc_tag()は internal で additional-classes.php → テーマオプション関数
    // という深い依存チェーンを持つため、$postがnullの早期リターンのみテスト可能
    // ========================================================================

    public function test_get_toc_tag_postがない場合空文字列を返す(): void
    {
        // global $post が設定されていない、かつカテゴリ/タグページでもない場合
        global $post;
        $post = null;
        $harray = [];
        $result = \get_toc_tag('<p>テスト</p>', $harray);
        $this->assertEmpty($result);
    }
}
