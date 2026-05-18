<?php
/**
 * lib/entry-card.php のユニットテスト
 *
 * エントリーカードのサムネイルサイズ取得関数をテストします。
 *
 * 特に「大きなカード（先頭のみ）」設定時のリグレッションを防ぐ目的で
 * 以下のシナリオを網羅します。
 *   - フロントページ1ページ目の先頭カード → large を返す
 *   - フロントページ2ページ目以降の先頭カード → THUMB320 を返す（リグレッション防止）
 *   - カテゴリーページ等フロントページ以外の先頭カード → THUMB320 を返す（リグレッション防止）
 *   - 2枚目以降のカード → THUMB320 を返す
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class EntryCardTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // lib/entry-card.php が依存する定数を定義
        if (!defined('THUMB320')) {
            define('THUMB320', 'thumbnail-320');
        }
        if (!defined('THUMB120')) {
            define('THUMB120', 'thumbnail-120');
        }
        if (!defined('ET_DEFAULT')) {
            define('ET_DEFAULT', 'default');
        }
        if (!defined('ET_LARGE_THUMB')) {
            define('ET_LARGE_THUMB', 'large_thumb');
        }
        if (!defined('ET_LARGE_THUMB_ON')) {
            define('ET_LARGE_THUMB_ON', 'large_thumb_on');
        }

        // lib/entry-card.php が依存するスタブ関数を定義
        if (!function_exists('is_widget_entry_card_large_image_use')) {
            function is_widget_entry_card_large_image_use($type) {
                return false;
            }
        }
        if (!function_exists('get_vertical_card_2_thumbnail_size')) {
            function get_vertical_card_2_thumbnail_size() { return THUMB320; }
        }
        if (!function_exists('get_vertical_card_3_thumbnail_size')) {
            function get_vertical_card_3_thumbnail_size() { return THUMB320; }
        }
        if (!function_exists('get_tile_card_2_thumbnail_size')) {
            function get_tile_card_2_thumbnail_size() { return THUMB320; }
        }
        if (!function_exists('get_tile_card_3_thumbnail_size')) {
            function get_tile_card_3_thumbnail_size() { return THUMB320; }
        }

        require_once dirname(__DIR__, 2) . '/lib/entry-card.php';
    }

    /**
     * 各テスト前にグローバルモック変数をリセットする
     */
    protected function setUp(): void
    {
        parent::setUp();
        // グローバル変数モックのリセット
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        $test_mock_is_front_page   = false;
        $test_mock_is_paged        = false;
        $test_mock_entry_card_type = 'default';
    }

    protected function tearDown(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        unset($test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type);
        parent::tearDown();
    }

    // ========================================================================
    // get_big_card_first_thumbnail_size()
    // フォーラム報告 #88748 に対応するリグレッション防止テスト
    // ========================================================================

    /**
     * フロントページ1ページ目の先頭カード(count=1)は large サムネイルを返す
     */
    public function test_get_big_card_first_thumbnail_size_フロントページ1ページ目の先頭カードはlargeを返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged;
        // フロントページ・1ページ目を模擬
        $test_mock_is_front_page = true;
        $test_mock_is_paged      = false;

        $result = get_big_card_first_thumbnail_size(1);
        $this->assertSame('large', $result);
    }

    /**
     * フロントページ2ページ目以降の先頭カード(count=1)は THUMB320 を返す
     * 【リグレッション防止】is_paged() の判定が欠落すると2ページ目でも large になる
     */
    public function test_get_big_card_first_thumbnail_size_フロントページ2ページ目の先頭カードはTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged;
        // フロントページだが2ページ目以降を模擬
        $test_mock_is_front_page = true;
        $test_mock_is_paged      = true;

        $result = get_big_card_first_thumbnail_size(1);
        $this->assertSame(THUMB320, $result);
    }

    /**
     * カテゴリーページ（フロントページ以外）の先頭カード(count=1)は THUMB320 を返す
     * 【リグレッション防止】is_front_page() の判定が欠落するとカテゴリーページでも large になる
     */
    public function test_get_big_card_first_thumbnail_size_カテゴリーページの先頭カードはTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged;
        // フロントページ以外（カテゴリーページ等）を模擬
        $test_mock_is_front_page = false;
        $test_mock_is_paged      = false;

        $result = get_big_card_first_thumbnail_size(1);
        $this->assertSame(THUMB320, $result);
    }

    /**
     * フロントページ1ページ目でも count=2 以降は THUMB320 を返す
     */
    public function test_get_big_card_first_thumbnail_size_フロントページ1ページ目でも2枚目以降はTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged;
        $test_mock_is_front_page = true;
        $test_mock_is_paged      = false;

        $result = get_big_card_first_thumbnail_size(2);
        $this->assertSame(THUMB320, $result);
    }

    /**
     * count=0（初期化前の状態）は THUMB320 を返す
     */
    public function test_get_big_card_first_thumbnail_size_count0はTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged;
        $test_mock_is_front_page = true;
        $test_mock_is_paged      = false;

        $result = get_big_card_first_thumbnail_size(0);
        $this->assertSame(THUMB320, $result);
    }

    // ========================================================================
    // get_entry_card_thumbnail_size() — カードタイプ別のサムネイルサイズ
    // ========================================================================

    /**
     * big_card_first タイプ、フロントページ先頭カード → large
     */
    public function test_get_entry_card_thumbnail_size_big_card_firstタイプでフロントページ先頭カードはlargeを返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        $test_mock_is_front_page   = true;
        $test_mock_is_paged        = false;
        $test_mock_entry_card_type = 'big_card_first';

        $result = get_entry_card_thumbnail_size(1);
        $this->assertSame('large', $result);
    }

    /**
     * big_card_first タイプ、フロントページ以外の先頭カード → THUMB320
     * 【リグレッション防止】フォーラム #88748 の不具合：フロントページ判定なしで large を返していた
     */
    public function test_get_entry_card_thumbnail_size_big_card_firstタイプでフロントページ以外の先頭カードはTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        $test_mock_is_front_page   = false;
        $test_mock_is_paged        = false;
        $test_mock_entry_card_type = 'big_card_first';

        $result = get_entry_card_thumbnail_size(1);
        $this->assertSame(THUMB320, $result);
    }

    /**
     * big_card_first タイプ、フロントページ2ページ目以降の先頭カード → THUMB320
     * 【リグレッション防止】ページング後の先頭カードに誤って large が返るのを防ぐ
     */
    public function test_get_entry_card_thumbnail_size_big_card_firstタイプでフロントページ2ページ目はTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        $test_mock_is_front_page   = true;
        $test_mock_is_paged        = true;
        $test_mock_entry_card_type = 'big_card_first';

        $result = get_entry_card_thumbnail_size(1);
        $this->assertSame(THUMB320, $result);
    }

    /**
     * big_card タイプは常に large（全カードが大きい）
     */
    public function test_get_entry_card_thumbnail_size_big_cardタイプは常にlargeを返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        $test_mock_is_front_page   = false;
        $test_mock_is_paged        = false;
        $test_mock_entry_card_type = 'big_card';

        $result = get_entry_card_thumbnail_size(1);
        $this->assertSame('large', $result);
    }

    /**
     * デフォルト（通常エントリーカード）タイプは THUMB320
     */
    public function test_get_entry_card_thumbnail_size_デフォルトタイプはTHUMB320を返す(): void
    {
        global $test_mock_is_front_page, $test_mock_is_paged, $test_mock_entry_card_type;
        $test_mock_is_front_page   = true;
        $test_mock_is_paged        = false;
        $test_mock_entry_card_type = 'default';

        $result = get_entry_card_thumbnail_size(1);
        $this->assertSame(THUMB320, $result);
    }
}
