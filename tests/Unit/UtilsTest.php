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

    public function test_is_dark_hexcolor_不正な長さはfalseを返す(): void
    {
        $this->assertFalse(is_dark_hexcolor('#FFF'));
    }

    public function test_is_dark_hexcolor_中間色のしきい値判定(): void
    {
        // 輝度 = (162 + 162 + 162) / 3 = 162, threshold=162 なので false (not dark)
        $this->assertFalse(is_dark_hexcolor('#a2a2a2'));
    }

    public function test_is_dark_hexcolor_しきい値ギリギリ暗い色(): void
    {
        // 輝度 = (161 + 161 + 161) / 3 = 161 < 162 なので true (dark)
        $this->assertTrue(is_dark_hexcolor('#a1a1a1'));
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
    // get_jquery_core_url() / get_jquery_core_full_version()
    // ========================================================================

    public function test_get_jquery_core_url_バージョン3のURLを返す(): void
    {
        $url = get_jquery_core_url('3');
        $this->assertStringContainsString('jquery/3.6.1', $url);
    }

    public function test_get_jquery_core_url_バージョン2のURLを返す(): void
    {
        $url = get_jquery_core_url('2');
        $this->assertStringContainsString('jquery/2.2.4', $url);
    }

    public function test_get_jquery_core_url_バージョン1のURLを返す(): void
    {
        $url = get_jquery_core_url('1');
        $this->assertStringContainsString('jquery/1.12.4', $url);
    }

    public function test_get_jquery_core_url_不正なバージョンはnullを返す(): void
    {
        $this->assertNull(get_jquery_core_url('99'));
    }

    public function test_get_jquery_core_full_version_バージョン3のフル番号(): void
    {
        $this->assertSame('3.6.1', get_jquery_core_full_version('3'));
    }

    public function test_get_jquery_core_full_version_バージョン2のフル番号(): void
    {
        $this->assertSame('2.2.4', get_jquery_core_full_version('2'));
    }

    public function test_get_jquery_core_full_version_不正なバージョンはnullを返す(): void
    {
        $this->assertNull(get_jquery_core_full_version('4'));
    }

    // ========================================================================
    // get_jquery_migrate_url() / get_jquery_migrate_full_version()
    // ========================================================================

    public function test_get_jquery_migrate_url_バージョン3のURLを返す(): void
    {
        $url = get_jquery_migrate_url('3');
        $this->assertStringContainsString('jquery-migrate/3.3.2', $url);
    }

    public function test_get_jquery_migrate_url_バージョン1のURLを返す(): void
    {
        $url = get_jquery_migrate_url('1');
        $this->assertStringContainsString('jquery-migrate/1.4.1', $url);
    }

    public function test_get_jquery_migrate_url_不正なバージョンはnullを返す(): void
    {
        $this->assertNull(get_jquery_migrate_url('2'));
    }

    public function test_get_jquery_migrate_full_version_バージョン3のフル番号(): void
    {
        $this->assertSame('3.0.1', get_jquery_migrate_full_version('3'));
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

}
