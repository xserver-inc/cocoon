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

        if (!function_exists('get_image_sized_url')) {
            function get_image_sized_url($url, $w = 0, $h = 0) {
                return $url;
            }
        }

        if (!function_exists('url_to_local')) {
            function url_to_local($url) {
                return false;
            }
        }

        if (!defined('ET_LARGE_THUMB')) {
            define('ET_LARGE_THUMB', 'large_thumb');
        }
        if (!defined('ET_LARGE_THUMB_ON')) {
            define('ET_LARGE_THUMB_ON', 'large_thumb_on');
        }
        if (!defined('THUMB120')) {
            define('THUMB120', 'thumb120');
        }
        if (!defined('THUMB120WIDTH')) {
            define('THUMB120WIDTH', 120);
        }
        if (!defined('THUMB120HEIGHT')) {
            define('THUMB120HEIGHT', 68);
        }
        if (!defined('THUMB120WIDTH_DEF')) {
            define('THUMB120WIDTH_DEF', 120);
        }
        if (!defined('THUMB120HEIGHT_DEF')) {
            define('THUMB120HEIGHT_DEF', 68);
        }
        if (!defined('THUMB320')) {
            define('THUMB320', 'thumb320');
        }
        if (!defined('THUMB320WIDTH')) {
            define('THUMB320WIDTH', 320);
        }
        if (!defined('THUMB320HEIGHT')) {
            define('THUMB320HEIGHT', 180);
        }
        if (!defined('THUMB320WIDTH_DEF')) {
            define('THUMB320WIDTH_DEF', 320);
        }
        if (!defined('THUMB320HEIGHT_DEF')) {
            define('THUMB320HEIGHT_DEF', 180);
        }
        if (!defined('DAY_IN_SECONDS')) {
            define('DAY_IN_SECONDS', 86400);
        }
        if (!defined('HOUR_IN_SECONDS')) {
            define('HOUR_IN_SECONDS', 3600);
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

    // ========================================================================
    // get_navi_card_image_attributes()
    // ========================================================================

    public function test_get_navi_card_image_attributes_カスタムリンクの記事IDから自動取得する(): void
    {
        $menu = new \stdClass();
        $menu->url = 'https://example.com/article';
        $menu->object_id = 123;
        $menu->object = 'custom';
        $menu->attr_title = '';
        \Brain\Monkey\Functions\expect('get_transient')->andReturn(false);
        \Brain\Monkey\Functions\expect('url_to_postid')->with('https://example.com/article')->andReturn(999);
        \Brain\Monkey\Functions\expect('set_transient')->andReturn(true);
        \Brain\Monkey\Functions\expect('get_post_thumbnail_id')->with(999)->andReturn(888);
        \Brain\Monkey\Functions\expect('wp_get_attachment_image_src')->withAnyArgs()->andReturn(['https://example.com/article-thumb.jpg', 120, 68]);
        \Brain\Monkey\Functions\expect('get_post_type')->with(999)->andReturn('post');
        \Brain\Monkey\Functions\expect('get_post_types')->zeroOrMoreTimes()->andReturn([]);
        \Brain\Monkey\Functions\expect('taxonomy_exists')->zeroOrMoreTimes()->andReturn(false);
        \Brain\Monkey\Functions\expect('get_post_thumbnail_id')->zeroOrMoreTimes()->andReturn(0);

        $result = get_navi_card_image_attributes($menu, 'default');
        $this->assertIsArray($result);
        $this->assertSame('https://example.com/article-thumb.jpg', $result[0]);
    }

    public function test_get_navi_card_image_attributes_タイトル属性の画像URLをサムネイルとして取得する(): void
    {
        $menu = new \stdClass();
        $menu->url = 'https://example.com/';
        $menu->object_id = 123;
        $menu->object = 'custom';
        $menu->attr_title = 'https://example.com/test.jpg';
        \Brain\Monkey\Functions\expect('get_transient')->andReturn(false);
        \Brain\Monkey\Functions\expect('url_to_postid')->andReturn(0);
        \Brain\Monkey\Functions\expect('set_transient')->andReturn(true);
        \Brain\Monkey\Functions\expect('get_post_types')->andReturn([]);
        \Brain\Monkey\Functions\expect('taxonomy_exists')->andReturn(false);

        $result = get_navi_card_image_attributes($menu, 'default');
        $this->assertIsArray($result);
        $this->assertSame('https://example.com/test.jpg', $result[0]);
    }

    public function test_get_author_list_selectbox_tag_ユーザー取得時にwho_authorsを指定する(): void
    {
        \Brain\Monkey\Functions\expect('get_users')
            ->once()
            ->with(\Mockery::on(function ($args) {
                return is_array($args) &&
                       isset($args['who']) && $args['who'] === 'authors';
            }))
            ->andReturn([]);

        get_author_list_selectbox_tag('test_name', '1');
        
        $this->assertTrue(true);
    }

    public function test_generate_author_check_list_ユーザー取得時にwho_authorsを指定する(): void
    {
        \Brain\Monkey\Functions\expect('get_users')
            ->once()
            ->with(\Mockery::on(function ($args) {
                return is_array($args) &&
                       isset($args['who']) && $args['who'] === 'authors';
            }))
            ->andReturn([]);

        ob_start();
        generate_author_check_list('test_name', []);
        ob_get_clean();
        
        $this->assertTrue(true);
    }

}
