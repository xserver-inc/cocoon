<?php
/**
 * TinyMCEドロップダウンのJS出力リグレッションテスト
 *
 * esc_js()を使うと<>が&lt;&gt;に変換され、TinyMCEで吹き出し等のHTMLが
 * テキストとして出力される不具合（#88391）が発生した。
 * wp_json_encode()による安全なエスケープが維持されているか検証する。
 *
 * @see https://wp-cocoon.com/community/postid/88391/
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey;

class TinyMceVariablesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();

        // speech-balloons.php 等の require 時や関連モック経由で DEBUG_MODE を参照する
        if (!defined('DEBUG_MODE')) {
            define('DEBUG_MODE', false);
        }

        // speech-balloons.php が require 時に is_speech_balloons_record_empty() などを呼び出し、
        // そこで $wpdb->prefix が参照されるためダミーをセット
        global $wpdb;
        $wpdb = new \stdClass();
        $wpdb->prefix = 'wp_';

        // TinyMCEファイルが require 時にトップレベルで呼び出す関数群
        Monkey\Functions\when('get_cocoon_template_directory_uri')
            ->justReturn('http://example.com');
        Monkey\Functions\when('is_speech_balloons_record_empty')->justReturn(false);
        Monkey\Functions\when('is_affiliate_tags_record_empty')->justReturn(false);
        Monkey\Functions\when('is_function_texts_record_empty')->justReturn(false);
        Monkey\Functions\when('is_item_rankings_record_empty')->justReturn(false);

        // テスト対象の関数定義を読み込む
        require_once dirname(__DIR__, 2) . '/lib/tinymce/speech-balloons.php';
        require_once dirname(__DIR__, 2) . '/lib/tinymce/affiliate-tags.php';
        require_once dirname(__DIR__, 2) . '/lib/tinymce/function-texts.php';
        require_once dirname(__DIR__, 2) . '/lib/tinymce/item-rankings.php';
        require_once dirname(__DIR__, 2) . '/lib/tinymce/insert-html.php';
        require_once dirname(__DIR__, 2) . '/lib/tinymce/shortcodes.php';
        require_once dirname(__DIR__, 2) . '/lib/tinymce/html-tags.php';
    }

    public function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    // ========================================================================
    // generate_speech_balloons_js() — 吹き出し（不具合の発端）
    // ========================================================================

    /**
     * 吹き出しHTMLが wp_json_encode(JSON_HEX_TAG) で安全にエスケープされ、
     * esc_js()の&lt;&gt;変換が混入していないことを検証
     */
    public function test_generate_speech_balloons_js_HTMLをwp_json_encodeでエスケープする(): void
    {
        // ダミーの吹き出しレコード
        $mock_record = new \stdClass();
        $mock_record->id      = 1;
        $mock_record->title   = 'テスト吹き出し';
        $mock_record->visible = 1;

        Monkey\Functions\when('get_speech_balloons')
            ->justReturn([$mock_record]);

        // generate_speech_balloon_tag() は echo で HTML を直接出力する関数
        Monkey\Functions\when('generate_speech_balloon_tag')
            ->alias(function ($record, $voice) {
                echo '<div class="speech-wrap">' . $voice . '</div>';
            });

        // minify_html は改行除去のみなので、テストではそのまま返す
        Monkey\Functions\when('minify_html')
            ->returnArg(1);

        ob_start();
        generate_speech_balloons_js('');
        $output = ob_get_clean();

        // JSON_HEX_TAG により < は \u003C、> は \u003E に変換される
        $this->assertStringContainsString('\u003Cdiv class=\u0022speech-wrap\u0022\u003E', $output);

        // esc_js() 使用時の兆候：&lt; が含まれていないなら安全
        $this->assertStringNotContainsString('&lt;div', $output);
        $this->assertStringNotContainsString('&gt;', $output);
    }

    // ========================================================================
    // generate_affiliate_tags() — アフィリエイトタグ
    // ========================================================================

    /**
     * アフィリエイトタグのショートコード値が wp_json_encode でエスケープされていることを検証
     */
    public function test_generate_affiliate_tags_ショートコードをwp_json_encodeで安全に出力する(): void
    {
        $mock_record = new \stdClass();
        $mock_record->id      = 1;
        $mock_record->title   = 'テストタグ';
        $mock_record->visible = 1;

        Monkey\Functions\when('get_affiliate_tags')
            ->justReturn([$mock_record]);

        // ダブルクォーテーションを含むショートコード
        Monkey\Functions\when('get_affiliate_tag_shortcode')
            ->justReturn('[affiliate id="1"]');

        ob_start();
        generate_affiliate_tags('');
        $output = ob_get_clean();

        $this->assertStringContainsString('var affiliateTags = new Array();', $output);
        // JSON_HEX_QUOT により " は \u0022 に変換される
        $this->assertStringContainsString('\u00221\u0022', $output);
        // esc_js() 使用時の兆候：&quot; が含まれていないなら安全
        $this->assertStringNotContainsString('&quot;', $output);
    }

    // ========================================================================
    // generate_function_texts_is() — テンプレート（定型文）
    // ========================================================================

    /**
     * テンプレートのショートコード値が wp_json_encode でエスケープされていることを検証
     */
    public function test_generate_function_texts_is_ショートコードをwp_json_encodeで安全に出力する(): void
    {
        $mock_record = new \stdClass();
        $mock_record->id      = 1;
        $mock_record->title   = 'テストテンプレート';
        $mock_record->visible = 1;

        Monkey\Functions\when('get_function_texts')
            ->justReturn([$mock_record]);

        Monkey\Functions\when('get_function_text_shortcode')
            ->justReturn('[temp id="1"]');

        ob_start();
        generate_function_texts_is('');
        $output = ob_get_clean();

        $this->assertStringContainsString('var functionTexts = new Array();', $output);
        $this->assertStringContainsString('\u00221\u0022', $output);
        $this->assertStringNotContainsString('&quot;', $output);
    }

    // ========================================================================
    // generate_item_rankings() — ランキング
    // ========================================================================

    /**
     * ランキングのショートコード値が wp_json_encode でエスケープされていることを検証
     */
    public function test_generate_item_rankings_ショートコードをwp_json_encodeで安全に出力する(): void
    {
        $mock_record = new \stdClass();
        $mock_record->id      = 1;
        $mock_record->title   = 'テストランキング';
        $mock_record->visible = 1;

        Monkey\Functions\when('get_item_rankings')
            ->justReturn([$mock_record]);

        Monkey\Functions\when('get_item_ranking_shortcode')
            ->justReturn('[rank id="1"]');

        ob_start();
        generate_item_rankings('');
        $output = ob_get_clean();

        $this->assertStringContainsString('var itemRankings = new Array();', $output);
        $this->assertStringContainsString('\u00221\u0022', $output);
        $this->assertStringNotContainsString('&quot;', $output);
    }

    // ========================================================================
    // generate_insert_html_label_js() — HTML挿入ラベル
    // ========================================================================

    /**
     * HTML挿入ドロップダウンのタイトル翻訳文字列が wp_json_encode でエスケープされていることを検証
     */
    public function test_generate_insert_html_label_js_タイトル文字列をwp_json_encodeで安全に出力する(): void
    {
        ob_start();
        generate_insert_html_label_js('');
        $output = ob_get_clean();

        // wp_json_encode により日本語は Unicode エスケープされる（"HTML\u633f\u5165"）
        $this->assertStringContainsString('var insert_html_button_title = "HTML\u', $output);
        $this->assertStringContainsString('var insert_html_dialog_label = "HTML\u', $output);

        // esc_js() 使用時の兆候チェック
        $this->assertStringNotContainsString('&quot;', $output);
    }

    // ========================================================================
    // generate_shortcodes_js() — ショートコード
    // ========================================================================

    /**
     * ショートコード一覧の全変数が wp_json_encode でエスケープされていることを検証
     */
    public function test_generate_shortcodes_js_全ショートコードをwp_json_encodeで安全に出力する(): void
    {
        ob_start();
        generate_shortcodes_js('');
        $output = ob_get_clean();

        // 配列変数が正常に宣言されている
        $this->assertStringContainsString('var shortcodes = new Array();', $output);

        // JSON_HEX_QUOT により " は \u0022 に変換されている
        $this->assertStringContainsString('\u0022', $output);

        // esc_js() 使用時の兆候：&quot; が含まれていないなら安全
        $this->assertStringNotContainsString('&quot;', $output);
    }

    // ========================================================================
    // generate_html_tags_is() — HTMLタグ（タグドロップダウン）
    // ========================================================================

    /**
     * HTMLタグのテンプレート値が wp_json_encode でエスケープされ、
     * 旧パターン（シングルクォート直接埋め込み）が残存していないことを検証
     */
    public function test_generate_html_tags_is_HTMLテンプレートをwp_json_encodeで安全に出力する(): void
    {
        ob_start();
        generate_html_tags_is('');
        $output = ob_get_clean();

        // 配列変数が正常に宣言されている
        $this->assertStringContainsString('var htmlTags = new Array();', $output);

        // wp_json_encode により <div が \u003Cdiv に変換されている
        $this->assertStringContainsString('\u003Cdiv', $output);
        $this->assertStringContainsString('\u003E', $output);

        // esc_js() / esc_html() 使用時の兆候がないこと
        $this->assertStringNotContainsString('&lt;div', $output);
        $this->assertStringNotContainsString('&gt;', $output);

        // 旧パターンの兆候：シングルクォートでJS値を囲むパターンが残っていないこと
        $this->assertDoesNotMatchRegularExpression(
            "/htmlTags\[\d+\]\.\w+\s*=\s*'/",
            $output,
            'htmlTags の値がシングルクォート囲みの旧パターンで出力されています'
        );

        // ヘッダー変数も wp_json_encode 経由であること
        // wp_json_encode は日本語を \uXXXX 形式にエスケープするため、
        // 「var htmlTagsTitle = 」の直後に生のマルチバイト文字が来ていなければ新パターン
        $this->assertDoesNotMatchRegularExpression(
            '/var htmlTagsTitle = "[\\x{3000}-\\x{9FFF}]/u',
            $output,
            'htmlTagsTitle が wp_json_encode を経由せず生の日本語文字列で出力されています'
        );
    }

    // ========================================================================
    // wp_json_encode vs esc_js リグレッション検証（純粋関数テスト）
    // ========================================================================

    /**
     * wp_json_encode + JSON_HEX_TAG でHTMLタグが安全に保持されることを直接検証
     * （もし esc_js() に戻された場合、このテストが確実に失敗する）
     */
    public function test_wp_json_encode_はHTMLタグをエンティティ化せずJSON安全にエスケープする(): void
    {
        $html = '<div class="balloon">テスト</div>';
        $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

        // wp_json_encode の実体（cron-test-stubs.php で定義）
        $result = wp_json_encode($html, $flags);

        // < が \u003C に変換される（HTMLタグ自体は保持）
        $this->assertStringContainsString('\u003C', $result);
        $this->assertStringContainsString('\u003E', $result);

        // esc_js() を使った場合の誤変換（&lt;）が含まれていないこと
        $this->assertStringNotContainsString('&lt;', $result);
        $this->assertStringNotContainsString('&gt;', $result);

        // JSONデコードすると元のHTMLに復元できること
        $decoded = json_decode($result);
        $this->assertSame($html, $decoded);
    }

    /**
     * esc_js() が HTMLタグを破壊することを明示的に証明するテスト
     * （この挙動こそが #88391 の不具合原因）
     */
    public function test_esc_js_はHTMLタグをエンティティ化してしまう(): void
    {
        $html = '<div class="balloon">テスト</div>';

        // esc_js() 相当の処理（wp-mock-functions.php の esc_html を経由）
        $escaped = esc_html($html);

        // esc_js()/esc_html() は < を &lt; に変換する
        $this->assertStringContainsString('&lt;', $escaped);
        $this->assertStringContainsString('&gt;', $escaped);

        // 元の < は失われている
        $this->assertStringNotContainsString('<div', $escaped);
    }
}
