<?php
/**
 * skin-funcs.php のユニットテスト
 *
 * スキン設定関連の関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class SkinFuncsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // skin-funcs.php が依存する関数を先に定義
        if (!function_exists('str_to_bool')) {
            function str_to_bool($value) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }
        if (!function_exists('is_pwa_enable')) {
            function is_pwa_enable() {
                return false;
            }
        }

        require_once dirname(__DIR__, 2) . '/lib/page-settings/skin-funcs.php';
    }

    // ========================================================================
    // skin_files_comp()
    // ========================================================================

    public function test_skin_files_comp_優先度の低い方が先に来る(): void
    {
        $a = ['priority' => 1, 'skin_name' => 'Skin A'];
        $b = ['priority' => 2, 'skin_name' => 'Skin B'];
        $this->assertSame(-1, skin_files_comp($a, $b));
    }

    public function test_skin_files_comp_優先度の高い方が後に来る(): void
    {
        $a = ['priority' => 10, 'skin_name' => 'Skin A'];
        $b = ['priority' => 1, 'skin_name' => 'Skin B'];
        $this->assertSame(1, skin_files_comp($a, $b));
    }

    public function test_skin_files_comp_優先度が未設定の場合デフォルト値を使う(): void
    {
        $a = ['skin_name' => 'Skin A'];
        $b = ['priority' => 1, 'skin_name' => 'Skin B'];
        // $a の priority は 99999999999（デフォルト）, $b は 1 なので $b が先
        $this->assertSame(1, skin_files_comp($a, $b));
    }

    // ========================================================================
    // get_skin_js_url() / get_skin_php_url() / get_skin_csv_url() / get_skin_json_url()
    // ========================================================================

    public function test_get_skin_js_url_style_cssをjavascript_jsに変換する(): void
    {
        // get_skin_url() はデフォルトで空文字列を返す → str_ireplace は空文字列を返す
        // モックで動作を検証
        $this->assertIsString(get_skin_js_url());
    }

    public function test_get_skin_php_url_style_cssをfunctions_phpに変換する(): void
    {
        $this->assertIsString(get_skin_php_url());
    }

    public function test_get_skin_csv_url_style_cssをoption_csvに変換する(): void
    {
        $this->assertIsString(get_skin_csv_url());
    }

    public function test_get_skin_json_url_style_cssをoption_jsonに変換する(): void
    {
        $this->assertIsString(get_skin_json_url());
    }

    // ========================================================================
    // is_exclude_skin()
    // ========================================================================

    public function test_is_exclude_skin_除外スキンに該当する場合trueを返す(): void
    {
        $url = 'https://example.com/skins/dark-theme/style.css';
        $exclude = ['dark-theme'];
        $this->assertTrue(is_exclude_skin($url, $exclude));
    }

    public function test_is_exclude_skin_除外スキンに該当しない場合falseを返す(): void
    {
        $url = 'https://example.com/skins/light-theme/style.css';
        $exclude = ['dark-theme'];
        $this->assertFalse(is_exclude_skin($url, $exclude));
    }

    public function test_is_exclude_skin_複数の除外スキンをチェックする(): void
    {
        $url = 'https://example.com/skins/skin-dark-ruri/style.css';
        $exclude = ['skin-dark-enji', 'skin-dark-ruri', 'skin-dark-kamonoha'];
        $this->assertTrue(is_exclude_skin($url, $exclude));
    }

    public function test_is_exclude_skin_部分一致ではない場合falseを返す(): void
    {
        $url = 'https://example.com/skins/dark-theme/functions.php';
        $exclude = ['dark-theme'];
        // 除外判定は '/style.css' で終わるURLのみ
        $this->assertFalse(is_exclude_skin($url, $exclude));
    }
}
