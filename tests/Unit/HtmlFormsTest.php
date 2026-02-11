<?php
/**
 * html-forms.php のユニットテスト
 *
 * HTMLフォーム生成関数（get_* 関数）をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class HtmlFormsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // html-forms.php が依存する定数を定義
        if (!defined('DEFAULT_INPUT_COLS')) {
            define('DEFAULT_INPUT_COLS', 60);
        }
        if (!defined('DEFAULT_INPUT_ROWS')) {
            define('DEFAULT_INPUT_ROWS', 10);
        }

        // html-forms.php が依存する関数を定義
        if (!function_exists('get_label_tag')) {
            function get_label_tag($id, $caption) {
                return '<label for="' . $id . '">' . $caption . '</label>';
            }
        }
        if (!function_exists('stripslashes_deep')) {
            function stripslashes_deep($value) {
                return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
            }
        }

        require_once dirname(__DIR__, 2) . '/lib/html-forms.php';
    }

    // ========================================================================
    // get_howto_tag()
    // ========================================================================

    public function test_get_howto_tag_ハウツー説明文を生成する(): void
    {
        $result = get_howto_tag('テスト説明文');
        $this->assertSame('<p class="howto">テスト説明文</p>', $result);
    }

    public function test_get_howto_tag_空文字列でもpタグを生成する(): void
    {
        $result = get_howto_tag('');
        $this->assertSame('<p class="howto"></p>', $result);
    }

    public function test_get_howto_tag_HTMLを含むキャプション(): void
    {
        $result = get_howto_tag('<strong>重要</strong>な説明');
        $this->assertStringContainsString('<strong>重要</strong>', $result);
    }

    // ========================================================================
    // get_not_allowed_form_class()
    // ========================================================================

    public function test_get_not_allowed_form_class_許可されない場合クラス属性を返す(): void
    {
        $result = get_not_allowed_form_class(false);
        $this->assertSame(' class="not-allowed-form"', $result);
    }

    public function test_get_not_allowed_form_class_許可される場合nullを返す(): void
    {
        $result = get_not_allowed_form_class(true);
        $this->assertNull($result);
    }

    public function test_get_not_allowed_form_class_in_trueでクラス名のみ返す(): void
    {
        $result = get_not_allowed_form_class(false, true);
        $this->assertSame(' not-allowed-form', $result);
    }

    // ========================================================================
    // get_help_page_tag()
    // ========================================================================

    public function test_get_help_page_tag_解説ページリンクを生成する(): void
    {
        $result = get_help_page_tag('https://wp-cocoon.com/help');
        $this->assertStringContainsString('href="https://wp-cocoon.com/help"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertStringContainsString('class="help-page"', $result);
    }

    public function test_get_help_page_tag_カスタムテキストを表示する(): void
    {
        $result = get_help_page_tag('https://wp-cocoon.com/help', 'ヘルプ');
        $this->assertStringContainsString('ヘルプ', $result);
    }

    // ========================================================================
    // the_checkbox_checked() - 出力テスト
    // ========================================================================

    public function test_the_checkbox_checked_値が一致するとchecked属性を出力する(): void
    {
        ob_start();
        the_checkbox_checked(1, 1);
        $output = ob_get_clean();
        $this->assertSame(' checked="checked"', $output);
    }

    public function test_the_checkbox_checked_値が一致しないと何も出力しない(): void
    {
        ob_start();
        the_checkbox_checked(0, 1);
        $output = ob_get_clean();
        $this->assertSame('', $output);
    }

    public function test_the_checkbox_checked_配列内に値が含まれるとchecked属性を出力する(): void
    {
        ob_start();
        the_checkbox_checked([1, 2, 3], 2);
        $output = ob_get_clean();
        $this->assertSame(' checked="checked"', $output);
    }

    public function test_the_checkbox_checked_配列内に値がないと何も出力しない(): void
    {
        ob_start();
        the_checkbox_checked([1, 2, 3], 5);
        $output = ob_get_clean();
        $this->assertSame('', $output);
    }

    // ========================================================================
    // the_option_selected()
    // ========================================================================

    public function test_the_option_selected_値が一致するとselected属性を出力する(): void
    {
        ob_start();
        the_option_selected('abc', 'abc');
        $output = ob_get_clean();
        $this->assertSame(' selected="selected"', $output);
    }

    public function test_the_option_selected_値が一致しないと何も出力しない(): void
    {
        ob_start();
        the_option_selected('abc', 'def');
        $output = ob_get_clean();
        $this->assertSame('', $output);
    }

    // ========================================================================
    // get_label_tag() (スタブ経由)
    // ========================================================================

    public function test_get_label_tag_ラベルタグを生成する(): void
    {
        $result = get_label_tag('test_id', 'テストラベル');
        $this->assertSame('<label for="test_id">テストラベル</label>', $result);
    }
}
