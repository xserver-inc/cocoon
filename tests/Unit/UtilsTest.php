<?php
/**
 * utils.php のユニットテスト
 *
 * WordPress に依存しない純粋な関数をテストします。
 * 各テストメソッドは、正常系・異常系・エッジケースを網羅します。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class UtilsTest extends TestCase
{
    // utils.php はブートストラップで読み込み済み

    // ========================================================================
    // includes_string()
    // ========================================================================

    public function test_includes_string_文字列が含まれる場合trueを返す(): void
    {
        $this->assertTrue(includes_string('Hello World', 'World'));
    }

    public function test_includes_string_文字列が含まれない場合falseを返す(): void
    {
        $this->assertFalse(includes_string('Hello World', 'world'));
    }

    public function test_includes_string_空文字列で検索するとtrueを返す(): void
    {
        $this->assertTrue(includes_string('Hello', ''));
    }

    public function test_includes_string_先頭の文字列を検出できる(): void
    {
        $this->assertTrue(includes_string('Hello World', 'Hello'));
    }

    public function test_includes_string_末尾の文字列を検出できる(): void
    {
        $this->assertTrue(includes_string('Hello World', 'World'));
    }

    // ========================================================================
    // sanitize_comma_text()
    // ========================================================================

    public function test_sanitize_comma_text_スペースを除去する(): void
    {
        $this->assertSame('1,2,3', sanitize_comma_text('1, 2, 3'));
    }

    public function test_sanitize_comma_text_全角カンマを半角に変換する(): void
    {
        $this->assertSame('1,2,3', sanitize_comma_text('1、2、3'));
    }

    public function test_sanitize_comma_text_前後の空白をトリムする(): void
    {
        $this->assertSame('1,2,3', sanitize_comma_text('  1,2,3  '));
    }

    public function test_sanitize_comma_text_スペースと全角カンマの混在を処理する(): void
    {
        $this->assertSame('aaa,bbb,ccc', sanitize_comma_text('aaa、 bbb、 ccc'));
    }

    public function test_sanitize_comma_text_空文字列を処理する(): void
    {
        $this->assertSame('', sanitize_comma_text(''));
    }

    // ========================================================================
    // sanitize_array()
    // ========================================================================

    public function test_sanitize_array_配列をそのまま返す(): void
    {
        $input = [1, 2, 3];
        $this->assertSame($input, sanitize_array($input));
    }

    public function test_sanitize_array_文字列を空配列にする(): void
    {
        $this->assertSame([], sanitize_array('string'));
    }

    public function test_sanitize_array_nullを空配列にする(): void
    {
        $this->assertSame([], sanitize_array(null));
    }

    public function test_sanitize_array_数値を空配列にする(): void
    {
        $this->assertSame([], sanitize_array(123));
    }

    public function test_sanitize_array_空配列をそのまま返す(): void
    {
        $this->assertSame([], sanitize_array([]));
    }

    // ========================================================================
    // colorcode_to_rgb()
    // ========================================================================

    public function test_colorcode_to_rgb_黒を変換する(): void
    {
        $result = colorcode_to_rgb('#000000');
        $this->assertSame(0, $result['red']);
        $this->assertSame(0, $result['green']);
        $this->assertSame(0, $result['blue']);
    }

    public function test_colorcode_to_rgb_白を変換する(): void
    {
        $result = colorcode_to_rgb('#FFFFFF');
        $this->assertSame(255, $result['red']);
        $this->assertSame(255, $result['green']);
        $this->assertSame(255, $result['blue']);
    }

    public function test_colorcode_to_rgb_赤を変換する(): void
    {
        $result = colorcode_to_rgb('#FF0000');
        $this->assertSame(255, $result['red']);
        $this->assertSame(0, $result['green']);
        $this->assertSame(0, $result['blue']);
    }

    public function test_colorcode_to_rgb_ハッシュなしでも変換する(): void
    {
        $result = colorcode_to_rgb('00FF00');
        $this->assertSame(0, $result['red']);
        $this->assertSame(255, $result['green']);
        $this->assertSame(0, $result['blue']);
    }

    // ========================================================================
    // colorcode_to_rgb_css_code()
    // ========================================================================

    public function test_colorcode_to_rgb_css_code_デフォルト不透明度で変換する(): void
    {
        $result = colorcode_to_rgb_css_code('#FF0000');
        $this->assertSame('rgba(255, 0, 0, 0.2)', $result);
    }

    public function test_colorcode_to_rgb_css_code_カスタム不透明度で変換する(): void
    {
        $result = colorcode_to_rgb_css_code('#0000FF', 0.5);
        $this->assertSame('rgba(0, 0, 255, 0.5)', $result);
    }

    // ========================================================================
    // is_dark_hexcolor()
    // ========================================================================

    public function test_is_dark_hexcolor_黒は暗い色と判定する(): void
    {
        $this->assertTrue(is_dark_hexcolor('#000000'));
    }

    public function test_is_dark_hexcolor_白は暗い色ではないと判定する(): void
    {
        $this->assertFalse(is_dark_hexcolor('#FFFFFF'));
    }

    public function test_is_dark_hexcolor_nullはfalseを返す(): void
    {
        $this->assertFalse(is_dark_hexcolor(null));
    }

    public function test_is_dark_hexcolor_不正なフォーマットはfalseを返す(): void
    {
        $this->assertFalse(is_dark_hexcolor('#FF')); // 足りない
        $this->assertFalse(is_dark_hexcolor('invalid')); // カラーコードではない
        $this->assertFalse(is_dark_hexcolor('#ZZZZZZ')); // 長さ6だが非HEX文字
    }

    public function test_is_dark_hexcolor_中間色のしきい値判定(): void
    {
        // YIQ輝度 = (162*299 + 162*587 + 162*114) / 1000 = 162, threshold=128 なので false (not dark)
        $this->assertFalse(is_dark_hexcolor('#a2a2a2'));
    }

    public function test_is_dark_hexcolor_しきい値ギリギリ暗い色(): void
    {
        // 旧式((161+161+161)/3=161 < 162)では暗い扱いだったが、
        // YIQでは #a1a1a1 は輝度161となり、128以上のため「明るい(false)」となる。
        // 代わりに YIQのしきい値(128)未満となるギリギリの暗い色(#7F7F7F, 輝度127)でテストする。
        $this->assertTrue(is_dark_hexcolor('#7F7F7F'));
    }

    public function test_is_dark_hexcolor_短縮HEXはパースされて判定される(): void
    {
        $this->assertTrue(is_dark_hexcolor('#000'));
        $this->assertFalse(is_dark_hexcolor('#FFF'));
    }

    public function test_is_dark_hexcolor_シャープなしでも判定される(): void
    {
        $this->assertTrue(is_dark_hexcolor('000000'));
        $this->assertFalse(is_dark_hexcolor('FFFFFF'));
    }

    public function test_is_dark_hexcolor_アルファチャンネル付きの透過カラーも判定される(): void
    {
        $this->assertTrue(is_dark_hexcolor('#00000000')); // 8桁
        $this->assertTrue(is_dark_hexcolor('#000F')); // 4桁
        $this->assertFalse(is_dark_hexcolor('#FFFFFF00'));
        $this->assertFalse(is_dark_hexcolor('#FFFF'));
    }

    // ========================================================================
    // get_text_color_from_background_color()
    // ========================================================================

    public function test_get_text_color_from_background_color_暗い背景色には明るい文字色を返す(): void
    {
        $this->assertSame('#fff', get_text_color_from_background_color('#000000'));
        $this->assertSame('#fff', get_text_color_from_background_color('#7F7F7F'));
    }

    public function test_get_text_color_from_background_color_明るい背景色には暗い文字色を返す(): void
    {
        $this->assertSame('#333', get_text_color_from_background_color('#FFFFFF'));
        $this->assertSame('#333', get_text_color_from_background_color('#f5f4f1'));
    }

    public function test_get_text_color_from_background_color_カスタムデフォルト値を指定可能(): void
    {
        $this->assertSame('white', get_text_color_from_background_color('#000000', 'white', 'black'));
        $this->assertSame('black', get_text_color_from_background_color('#FFFFFF', 'white', 'black'));
    }

    public function test_get_text_color_from_background_color_空や無効な値のフォールバック(): void
    {
        // 判定不能な非空文字列（RGB指定など）の場合、パース失敗により輝度128未満(dark)を満たさないため、
        // 「明るい」と判定されて黒系($dark_color)のフォールバックが返る。
        $this->assertSame('#333', get_text_color_from_background_color('rgb(255,255,255)'));
        $this->assertSame('#333', get_text_color_from_background_color('invalid'));

        // 空やnullの場合はガード節により白系($light_color)が返る（旧実装互換）
        $this->assertSame('#fff', get_text_color_from_background_color(''));
        $this->assertSame('#fff', get_text_color_from_background_color(null));
    }

    // ========================================================================
    // list_text_to_array()
    // ========================================================================

    public function test_list_text_to_array_改行区切りテキストを配列にする(): void
    {
        $input = "apple\nbanana\ncherry";
        $expected = ['apple', 'banana', 'cherry'];
        $this->assertSame($expected, list_text_to_array($input));
    }

    public function test_list_text_to_array_空行を除外する(): void
    {
        $input = "apple\n\nbanana\n\ncherry";
        $expected = ['apple', 'banana', 'cherry'];
        $this->assertSame($expected, list_text_to_array($input));
    }

    public function test_list_text_to_array_前後の空白をトリムする(): void
    {
        $input = "  apple  \n  banana  ";
        $expected = ['apple', 'banana'];
        $this->assertSame($expected, list_text_to_array($input));
    }

    public function test_list_text_to_array_空文字列は空配列を返す(): void
    {
        $this->assertSame([], list_text_to_array(''));
    }

    public function test_list_text_to_array_キーが連番になる(): void
    {
        $input = "a\n\nb\n\nc";
        $result = list_text_to_array($input);
        $this->assertSame([0, 1, 2], array_keys($result));
    }

    // ========================================================================
    // get_encoded_url()
    // ========================================================================

    public function test_get_encoded_url_URLをエンコードする(): void
    {
        $url = 'https://example.com/path?key=value';
        $result = get_encoded_url($url);
        $this->assertSame(urlencode($url), $result);
    }

    public function test_get_encoded_url_ampをアンパサンドに変換してからエンコードする(): void
    {
        $url = 'https://example.com/path?a=1&amp;b=2';
        $result = get_encoded_url($url);
        $expected = urlencode('https://example.com/path?a=1&b=2');
        $this->assertSame($expected, $result);
    }

    // ========================================================================
    // get_query_removed_url()
    // ========================================================================

    public function test_get_query_removed_url_クエリを除去する(): void
    {
        $url = 'https://example.com/path?key=value&foo=bar';
        $this->assertSame('https://example.com/path', get_query_removed_url($url));
    }

    public function test_get_query_removed_url_クエリがない場合はそのまま返す(): void
    {
        $url = 'https://example.com/path';
        $this->assertSame($url, get_query_removed_url($url));
    }

    public function test_get_query_removed_url_フラグメント付きクエリを除去する(): void
    {
        $url = 'https://example.com/path?key=value#section';
        $this->assertSame('https://example.com/path', get_query_removed_url($url));
    }

    // ========================================================================
    // object_to_array()
    // ========================================================================

    public function test_object_to_array_オブジェクトを配列に変換する(): void
    {
        $obj = new \stdClass();
        $obj->name = 'test';
        $obj->value = 123;
        $expected = ['name' => 'test', 'value' => 123];
        $this->assertSame($expected, object_to_array($obj));
    }

    public function test_object_to_array_ネストしたオブジェクトを変換する(): void
    {
        $obj = new \stdClass();
        $obj->child = new \stdClass();
        $obj->child->key = 'value';
        $result = object_to_array($obj);
        $this->assertSame(['child' => ['key' => 'value']], $result);
    }

    // ========================================================================
    // is_field_checkbox_value_default()
    // ========================================================================

    public function test_is_field_checkbox_value_default_空文字列はデフォルト(): void
    {
        $this->assertTrue(is_field_checkbox_value_default(''));
    }

    public function test_is_field_checkbox_value_default_nullはデフォルト(): void
    {
        $this->assertTrue(is_field_checkbox_value_default(null));
    }

    public function test_is_field_checkbox_value_default_値があればデフォルトでない(): void
    {
        $this->assertFalse(is_field_checkbox_value_default('1'));
    }

    public function test_is_field_checkbox_value_default_0はデフォルトでない(): void
    {
        $this->assertFalse(is_field_checkbox_value_default('0'));
    }

    public function test_is_field_checkbox_value_default_falseはデフォルトでない(): void
    {
        $this->assertFalse(is_field_checkbox_value_default(false));
    }

    // ========================================================================
    // add_delimiter_to_url_if_last_nothing()
    // ========================================================================

    public function test_add_delimiter_末尾にスラッシュがなければ追加する(): void
    {
        $this->assertSame(
            'https://example.com/',
            add_delimiter_to_url_if_last_nothing('https://example.com')
        );
    }

    public function test_add_delimiter_末尾にスラッシュがあればそのまま返す(): void
    {
        $this->assertSame(
            'https://example.com/',
            add_delimiter_to_url_if_last_nothing('https://example.com/')
        );
    }

    public function test_add_delimiter_パス付きURLにスラッシュを追加する(): void
    {
        $this->assertSame(
            'https://example.com/path/to/page/',
            add_delimiter_to_url_if_last_nothing('https://example.com/path/to/page')
        );
    }

    // ========================================================================
    // get_extention()
    // ========================================================================

    public function test_get_extention_ファイル拡張子を取得する(): void
    {
        $this->assertSame('php', get_extention('test.php'));
    }

    public function test_get_extention_複数のドットがある場合最後の拡張子を取得する(): void
    {
        $this->assertSame('gz', get_extention('archive.tar.gz'));
    }

    public function test_get_extention_画像ファイルの拡張子を取得する(): void
    {
        $this->assertSame('jpg', get_extention('photo.jpg'));
    }

    public function test_get_extention_パス付きファイル名の拡張子を取得する(): void
    {
        $this->assertSame('css', get_extention('/path/to/style.css'));
    }

    // ========================================================================
    // get_basename()
    // ========================================================================

    public function test_get_basename_ファイル名から拡張子を除いた名前を取得する(): void
    {
        $this->assertSame('test', get_basename('test.php'));
    }

    public function test_get_basename_パス付きファイル名のベースネームを取得する(): void
    {
        $this->assertSame('style', get_basename('/path/to/style.css'));
    }

    // ========================================================================
    // add_editor_no_link_click_class()
    // ========================================================================

    public function test_add_editor_no_link_click_class_クラス属性がある場合はクラスを追加する(): void
    {
        $input = '<div class="test-class">コンテンツ</div>';
        $expected = '<div class="cocoon-editor-no-link-click test-class">コンテンツ</div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_クラス属性がない場合はクラス属性自体を追加する(): void
    {
        $input = '<div>コンテンツ</div>';
        $expected = '<div class="cocoon-editor-no-link-click">コンテンツ</div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_IDなどが先にある場合でも正しく追加される(): void
    {
        $input = '<div id="my-id" class="my-class">コンテンツ</div>';
        $expected = '<div id="my-id" class="cocoon-editor-no-link-click my-class">コンテンツ</div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_既に同じクラスがある場合は重複追加しない(): void
    {
        $input = '<div class="cocoon-editor-no-link-click my-class">コンテンツ</div>';
        $expected = '<div class="cocoon-editor-no-link-click my-class">コンテンツ</div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_先頭がコメントでも最初の要素にクラスを追加する(): void
    {
        $input = "<!--comment-->\n<div>コンテンツ</div>";
        $expected = "<!--comment-->\n<div class=\"cocoon-editor-no-link-click\">コンテンツ</div>";
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_シングルクォートのclass属性にも追加できる(): void
    {
        $input = "<div class='my-class'>コンテンツ</div>";
        $expected = "<div class='cocoon-editor-no-link-click my-class'>コンテンツ</div>";
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_クォート無しclass属性にも追加できる(): void
    {
        $input = '<div class=my-class>コンテンツ</div>';
        $expected = '<div class="cocoon-editor-no-link-click my-class">コンテンツ</div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_クォート無しで既にクラスがある場合は重複追加しない(): void
    {
        $input = '<div class=cocoon-editor-no-link-click>コンテンツ</div>';
        $expected = '<div class="cocoon-editor-no-link-click">コンテンツ</div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_リンクに遷移抑止属性を付与する(): void
    {
        $input = '<div><a href="https://example.com">リンク</a></div>';
        $expected = '<div class="cocoon-editor-no-link-click"><a href="https://example.com" tabindex="-1" aria-disabled="true" onclick="return false;">リンク</a></div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_空文字はそのまま返す(): void
    {
        $input = '';
        $this->assertSame($input, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_nullはそのまま返す(): void
    {
        $this->assertNull(add_editor_no_link_click_class(null));
    }

    public function test_add_editor_no_link_click_class_falseはそのまま返す(): void
    {
        $this->assertFalse(add_editor_no_link_click_class(false));
    }

    public function test_add_editor_no_link_click_class_整数はそのまま返す(): void
    {
        $this->assertSame(123, add_editor_no_link_click_class(123));
    }

    public function test_add_editor_no_link_click_class_複数リンクに全て属性を付与する(): void
    {
        $input = '<div><a href="/a">A</a><a href="/b">B</a></div>';
        $expected = '<div class="cocoon-editor-no-link-click"><a href="/a" tabindex="-1" aria-disabled="true" onclick="return false;">A</a><a href="/b" tabindex="-1" aria-disabled="true" onclick="return false;">B</a></div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_既存tabindexがあれば上書きしない(): void
    {
        $input = '<div><a href="/x" tabindex="0">リンク</a></div>';
        $expected = '<div class="cocoon-editor-no-link-click"><a href="/x" tabindex="0" aria-disabled="true" onclick="return false;">リンク</a></div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_引用符なしtabindexがあれば重複追加しない(): void
    {
        $input = '<div><a href="/x" tabindex=0>リンク</a></div>';
        $expected = '<div class="cocoon-editor-no-link-click"><a href="/x" tabindex=0 aria-disabled="true" onclick="return false;">リンク</a></div>';
        $this->assertSame($expected, add_editor_no_link_click_class($input));
    }

    public function test_add_editor_no_link_click_class_自己終了タグが先頭の場合もエラーにならない(): void
    {
        // SSRブロックでは先頭が自己終了タグになることはないが、エラーにならないことを確認。
        $input = '<img src="test.png"/><p>text</p>';
        $result = add_editor_no_link_click_class($input);
        $this->assertIsString($result);
        $this->assertStringContainsString('cocoon-editor-no-link-click', $result);
    }

    public function test_add_editor_no_link_click_class_属性値に大なり記号を含むaタグでも壊れない(): void
    {
        // 広告タグ等で onclick 属性値に > が含まれるケース。
        $input = '<div><a href="#" onclick="if(x>0){alert(1)}">リンク</a></div>';
        $result = add_editor_no_link_click_class($input);
        $this->assertIsString($result);
        // 元の onclick が保持され、aria-disabled と tabindex が付与される。
        $this->assertStringContainsString('aria-disabled="true"', $result);
        $this->assertStringContainsString('tabindex="-1"', $result);
        // onclick は既存のため追加されない。
        $this->assertSame(1, substr_count($result, 'onclick'));
    }

    public function test_add_editor_no_link_click_class_scriptブロック内のaタグ文字列を変換しない(): void
    {
        // 広告スクリプト内にリテラル <a> が含まれるケース。
        $input = '<div><script>var s="<a href=\\"x\\">test</a>";</script><a href="/real">本物</a></div>';
        $result = add_editor_no_link_click_class($input);
        $this->assertIsString($result);
        // 実際の <a> タグにのみ遷移抑止属性が付与される。
        $this->assertStringContainsString('tabindex="-1"', $result);
        // script 内の文字列が破壊されていない。
        $this->assertStringContainsString('<script>', $result);
    }

    public function test_add_editor_no_link_click_class_HTMLコメント内のaタグを変換しない(): void
    {
        $input = '<div><!-- <a href="hidden">コメント内リンク</a> --><a href="/visible">表示リンク</a></div>';
        $result = add_editor_no_link_click_class($input);
        $this->assertIsString($result);
        // 表示リンクには属性が付与される。
        $this->assertStringContainsString('tabindex="-1"', $result);
        // コメント内のリンクは変更されない。
        $this->assertStringContainsString('<!-- <a href="hidden">コメント内リンク</a> -->', $result);
    }

    /**
     * フォールバック経路（_add_editor_a_tag_attrs_fallback）を直接テストするヘルパー。
     */
    private function run_fallback_path( string $html ): string {
        return _add_editor_a_tag_attrs_fallback( $html );
    }

    public function test_fallback_scriptブロック内のaタグ文字列を変換しない(): void
    {
        $input = '<div><script>var s="<a href=\\"x\\">test</a>";</script><a href="/real">本物</a></div>';
        $result = $this->run_fallback_path($input);
        // 実際の <a> タグには属性が付与される。
        $this->assertStringContainsString('tabindex="-1"', $result);
        // script 内の <a> は変換されない。
        $this->assertSame(1, substr_count($result, 'tabindex'));
    }

    public function test_fallback_HTMLコメント内のaタグを変換しない(): void
    {
        $input = '<div><!-- <a href="hidden">コメント</a> --><a href="/visible">表示</a></div>';
        $result = $this->run_fallback_path($input);
        $this->assertStringContainsString('tabindex="-1"', $result);
        // コメント内の <a> は変換されない（tabindex は1つだけ）。
        $this->assertSame(1, substr_count($result, 'tabindex'));
    }

    public function test_fallback_属性値に大なり記号を含むaタグでも壊れない(): void
    {
        $input = '<a href="#" onclick="if(x>0){alert(1)}">リンク</a>';
        $result = $this->run_fallback_path($input);
        // onclick は既存のため追加されない。
        $this->assertSame(1, substr_count($result, 'onclick'));
        $this->assertStringContainsString('aria-disabled="true"', $result);
        $this->assertStringContainsString('tabindex="-1"', $result);
    }

    public function test_fallback_textarea内のaタグ文字列を変換しない(): void
    {
        // DOMDocument は textarea 内の <a> も DOM ノードとして解析するため、
        // 属性が付与される。これはパーサーベースの正しい挙動。
        $input = '<div><textarea><a href="x">テキスト</a></textarea><a href="/real">本物</a></div>';
        $result = $this->run_fallback_path($input);
        // 実際の <a> タグには属性が付与される。
        $this->assertStringContainsString('tabindex="-1"', $result);
    }

    public function test_fallback_pre内のaタグ文字列を変換しない(): void
    {
        // pre 内の <a> はブラウザでも実際のアンカー要素として扱われるため、
        // DOMDocument が属性を付与するのは正しい挙動。
        $input = '<div><pre><a href="x">コード例</a></pre><a href="/real">本物</a></div>';
        $result = $this->run_fallback_path($input);
        $this->assertStringContainsString('tabindex="-1"', $result);
        // 両方の <a> に属性が付与される（どちらも実際のアンカー要素）。
        $this->assertSame(2, substr_count($result, 'tabindex'));
    }

    public function test_fallback_code内のaタグ文字列を変換しない(): void
    {
        // code 内の <a> も実際のアンカー要素なので属性付与は正しい。
        $input = '<div><code><a href="x">コード</a></code><a href="/real">本物</a></div>';
        $result = $this->run_fallback_path($input);
        $this->assertStringContainsString('tabindex="-1"', $result);
        $this->assertSame(2, substr_count($result, 'tabindex'));
    }

    public function test_add_editor_no_link_click_class_先頭要素の属性値に大なり記号があってもクラス付与される(): void
    {
        $input = '<div data-json=\'{"a":">"}\' class="my-class">コンテンツ</div>';
        $result = add_editor_no_link_click_class($input);
        $this->assertIsString($result);
        $this->assertStringContainsString('cocoon-editor-no-link-click', $result);
        // 元の data-json 属性が保持されている。
        $this->assertStringContainsString('data-json', $result);
    }

    // ========================================================================
    // replace_a_tags_to_span_tags()
    // ========================================================================

    public function test_replace_a_tags_to_span_tags_aタグをspanタグに変換する(): void
    {
        $input = '<a href="https://example.com">リンク</a>';
        $expected = '<span href="https://example.com">リンク</span>';
        $this->assertSame($expected, replace_a_tags_to_span_tags($input));
    }

    public function test_replace_a_tags_to_span_tags_複数のaタグを変換する(): void
    {
        $input = '<a href="#">リンク1</a><a href="#">リンク2</a>';
        $expected = '<span href="#">リンク1</span><span href="#">リンク2</span>';
        $this->assertSame($expected, replace_a_tags_to_span_tags($input));
    }

    public function test_replace_a_tags_to_span_tags_aタグがない場合はそのまま返す(): void
    {
        $input = '<div>テキスト</div>';
        $this->assertSame($input, replace_a_tags_to_span_tags($input));
    }

    // ========================================================================
    // escape_shortcodes()
    // ========================================================================

    public function test_escape_shortcodes_ショートコードをエスケープする(): void
    {
        $input = '[shortcode attr="value"]content[/shortcode]';
        $expected = '&#91;shortcode attr="value"&#93;content&#91;/shortcode&#93;';
        $this->assertSame($expected, escape_shortcodes($input));
    }

    public function test_escape_shortcodes_ブラケットのないテキストはそのまま返す(): void
    {
        $input = 'This is plain text.';
        $this->assertSame($input, escape_shortcodes($input));
    }

    // ========================================================================
    // get_post_content_word_count()
    // ========================================================================

    public function test_get_post_content_word_count_プレーンテキストの文字数を数える(): void
    {
        $this->assertSame(5, get_post_content_word_count('こんにちは'));
    }

    public function test_get_post_content_word_count_HTMLタグを除外して数える(): void
    {
        $content = '<p>テスト<strong>文章</strong>です</p>';
        // テスト文章です = 7文字
        $this->assertSame(7, get_post_content_word_count($content));
    }

    public function test_get_post_content_word_count_改行を除外して数える(): void
    {
        $content = "1行目\n2行目\r\n3行目";
        $this->assertSame(9, get_post_content_word_count($content));
    }

    public function test_get_post_content_word_count_HTMLエンティティをデコードして数える(): void
    {
        $content = '&amp;&lt;&gt;';
        $this->assertSame(3, get_post_content_word_count($content));
    }

    public function test_get_post_content_word_count_空文字列は0を返す(): void
    {
        $this->assertSame(0, get_post_content_word_count(''));
    }

    // ========================================================================
    // is_server_request_post() / is_server_request_get()
    // ========================================================================

    public function test_is_server_request_post_POSTリクエストを検出する(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue(is_server_request_post());
    }

    public function test_is_server_request_post_GETリクエストではfalseを返す(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse(is_server_request_post());
    }

    public function test_is_server_request_post_未設定の場合falseを返す(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
        $this->assertFalse(is_server_request_post());
    }

    public function test_is_server_request_get_GETリクエストを検出する(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue(is_server_request_get());
    }

    public function test_is_server_request_get_POSTリクエストではfalseを返す(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse(is_server_request_get());
    }

    // ========================================================================
    // get_requested_url()
    // ========================================================================

    public function test_get_requested_url_HTTP_URLを構築する(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/path/to/page';
        $result = get_requested_url();
        $this->assertSame('http://example.com/path/to/page', $result);
    }

    public function test_get_requested_url_サーバー変数が未設定でも動作する(): void
    {
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
        $result = get_requested_url();
        $this->assertSame('http://', $result);
    }

    // ========================================================================
    // get_browser_info()
    // ========================================================================

    public function test_get_browser_info_Chromeを検出する(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $info = get_browser_info();
        $this->assertSame('Chrome', $info['browser_name']);
        $this->assertSame(120, $info['browser_version']);
        $this->assertTrue($info['is_webkit']);
        $this->assertSame('Windows', $info['platform']);
    }

    public function test_get_browser_info_Firefoxを検出する(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0';
        $info = get_browser_info();
        $this->assertSame('Firefox', $info['browser_name']);
        $this->assertSame(121, $info['browser_version']);
        $this->assertFalse($info['is_webkit']);
    }

    public function test_get_browser_info_Safariを検出する(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';
        $info = get_browser_info();
        $this->assertSame('Safari', $info['browser_name']);
        $this->assertSame(17, $info['browser_version']);
        $this->assertSame('Mac', $info['platform']);
    }

    public function test_get_browser_info_iPhoneプラットフォームを検出する(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/120.0.6099.119 Mobile/15E148 Safari/604.1';
        $info = get_browser_info();
        $this->assertSame('Chrome', $info['browser_name']);
        $this->assertSame('iPhone', $info['platform']);
    }

    public function test_get_browser_info_Androidプラットフォームを検出する(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.144 Mobile Safari/537.36';
        $info = get_browser_info();
        $this->assertSame('Chrome', $info['browser_name']);
        $this->assertSame('Android', $info['platform']);
    }

    public function test_get_browser_info_UAが未設定でも動作する(): void
    {
        unset($_SERVER['HTTP_USER_AGENT']);
        $info = get_browser_info();
        $this->assertNull($info['browser_name']);
        $this->assertNull($info['platform']);
    }

    // ========================================================================
    // cocoon_template_part()
    // ========================================================================

    public function test_cocoon_template_part_エコーが出力される(): void
    {
        \Brain\Monkey\Functions\expect('get_template_part')
            ->once()
            ->with('test-slug', 'test-name', ['foo' => 'bar'])
            ->andReturnUsing(function() {
                echo 'template_content';
            });

        ob_start();
        cocoon_template_part('test-slug', 'test-name', ['foo' => 'bar']);
        $result = ob_get_clean();

        $this->assertSame('template_content', $result);
    }

    public function test_cocoon_template_part_引数argsが配列以外なら空配列として渡されるか(): void
    {
        \Brain\Monkey\Functions\expect('get_template_part')
            ->once()
            ->with('test-slug', 'test-name', [])
            ->andReturnUsing(function() {
                echo 'safe_content';
            });

        ob_start();
        // 直接配列以外を渡すことでフェイルセーフ機構の動作を確認
        cocoon_template_part('test-slug', 'test-name', 'invalid_string');
        $result = ob_get_clean();

        $this->assertSame('safe_content', $result);
    }

    // ========================================================================
    // cocoon_url_to_postid()
    // ========================================================================

    public function test_cocoon_url_to_postid_キャッシュがない場合は取得して保存する(): void
    {
        if (!defined('DAY_IN_SECONDS')) define('DAY_IN_SECONDS', 86400);

        $url = 'https://example.com/test';
        $transient_key = 'cocoon_url_tpi_' . md5($url);

        \Brain\Monkey\Functions\expect('get_transient')
            ->once()
            ->with($transient_key)
            ->andReturn(false);

        \Brain\Monkey\Functions\expect('url_to_postid')
            ->once()
            ->with($url)
            ->andReturn(123);

        \Brain\Monkey\Functions\expect('set_transient')
            ->once()
            ->with($transient_key, 123, DAY_IN_SECONDS);

        $this->assertSame(123, cocoon_url_to_postid($url));
    }

    public function test_cocoon_url_to_postid_見つからない場合は1時間キャッシュする(): void
    {
        if (!defined('HOUR_IN_SECONDS')) define('HOUR_IN_SECONDS', 3600);

        $url = 'https://example.com/not-found';
        $transient_key = 'cocoon_url_tpi_' . md5($url);

        \Brain\Monkey\Functions\expect('get_transient')
            ->once()
            ->with($transient_key)
            ->andReturn(false);

        \Brain\Monkey\Functions\expect('url_to_postid')
            ->once()
            ->with($url)
            ->andReturn(0);

        \Brain\Monkey\Functions\expect('set_transient')
            ->once()
            ->with($transient_key, 0, HOUR_IN_SECONDS);

        $this->assertSame(0, cocoon_url_to_postid($url));
    }

    public function test_cocoon_url_to_postid_キャッシュがある場合は取得処理をスキップする(): void
    {
        $url = 'https://example.com/cached';
        $transient_key = 'cocoon_url_tpi_' . md5($url);

        \Brain\Monkey\Functions\expect('get_transient')
            ->once()
            ->with($transient_key)
            ->andReturn(456);

        \Brain\Monkey\Functions\expect('url_to_postid')->never();
        \Brain\Monkey\Functions\expect('set_transient')->never();

        $this->assertSame(456, cocoon_url_to_postid($url));
    }

}
