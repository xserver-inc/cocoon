<?php
/**
 * カテゴリ・タグのメタデータ保存処理のユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class TermMetaSaveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 必要な関数を読み込む
        $lib_dir = dirname(__DIR__, 2) . '/lib';
        require_once $lib_dir . '/content-category.php';
        require_once $lib_dir . '/content-tag.php';
    }

    /**
     * unfiltered_html 権限があるユーザーがカテゴリ本文を保存する場合、
     * wp_kses_post によるサニタイズをスキップし、入力された HTML がそのまま保存されることを検証します。
     */
    public function test_save_extra_category_fileds_unfiltered_html権限がある場合はwp_kses_postを通さない(): void
    {
        // 準備
        $term_id = 123;
        $_POST['taxonomy'] = 'category';
        $_POST['_wpnonce'] = 'dummy_nonce';
        $_POST['the_category_content'] = '<input type="checkbox" name="test">';

        // モック
        Functions\when('wp_verify_nonce')->justReturn(true);
        Functions\when('current_user_can')
            ->alias(function(string $cap) {
                if ($cap === 'edit_term') return true;
                if ($cap === 'unfiltered_html') return true; // 権限あり
                return false;
            });
        
        Functions\expect('wp_kses_post')->never(); // 呼ばれないはず
        
        $captured_content = null;
        Functions\when('update_term_meta')
            ->alias(function($id, $key, $val) use (&$captured_content) {
                if ($key === 'the_category_content') {
                    $captured_content = $val;
                }
                return true;
            });
        
        // その他ダミーモック
        Functions\when('get_the_category_meta_key')->justReturn('category_meta_123');
        Functions\when('delete_term_meta')->justReturn(true);

        // 実行
        save_extra_category_fileds($term_id);

        $this->assertSame('<input type="checkbox" name="test">', $captured_content);

        // クリーンアップ
        unset($_POST['taxonomy'], $_POST['_wpnonce'], $_POST['the_category_content']);
    }

    /**
     * unfiltered_html 権限がない一般ユーザーがカテゴリ本文を保存する場合、
     * wp_kses_post によって危険な HTML タグが適切に除去（サニタイズ）されることを検証します。
     */
    public function test_save_extra_category_fileds_unfiltered_html権限がない場合はwp_kses_postを通す(): void
    {
        // 準備
        $term_id = 456;
        $_POST['taxonomy'] = 'category';
        $_POST['_wpnonce'] = 'dummy_nonce';
        $_POST['the_category_content'] = '<input type="checkbox" name="test">';

        // モック
        Functions\when('wp_verify_nonce')->justReturn(true);
        Functions\when('current_user_can')
            ->alias(function(string $cap) {
                if ($cap === 'edit_term') return true;
                if ($cap === 'unfiltered_html') return false; // 権限なし
                return false;
            });
        
        Functions\when('wp_kses_post')->justReturn('サニタイズされた文字列');
        
        $captured_content = null;
        Functions\when('update_term_meta')
            ->alias(function($id, $key, $val) use (&$captured_content) {
                if ($key === 'the_category_content') {
                    $captured_content = $val;
                }
                return true;
            });
        
        // その他ダミーモック
        Functions\when('get_the_category_meta_key')->justReturn('category_meta_456');
        Functions\when('delete_term_meta')->justReturn(true);

        // 実行
        save_extra_category_fileds($term_id);

        $this->assertSame('サニタイズされた文字列', $captured_content);

        // クリーンアップ
        unset($_POST['taxonomy'], $_POST['_wpnonce'], $_POST['the_category_content']);
    }

    /**
     * unfiltered_html 権限があるユーザーがタグ本文を保存する場合、
     * wp_kses_post によるサニタイズをスキップし、入力された HTML がそのまま保存されることを検証します。
     */
    public function test_save_extra_tag_fileds_unfiltered_html権限がある場合はwp_kses_postを通さない(): void
    {
        // 準備
        $term_id = 123;
        $_POST['taxonomy'] = 'post_tag';
        $_POST['_wpnonce'] = 'dummy_nonce';
        $_POST['the_tag_content'] = '<input type="checkbox" name="test">';

        // モック
        Functions\when('wp_verify_nonce')->justReturn(true);
        Functions\when('current_user_can')
            ->alias(function(string $cap) {
                if ($cap === 'edit_term') return true;
                if ($cap === 'unfiltered_html') return true; // 権限あり
                return false;
            });
        
        Functions\expect('wp_kses_post')->never(); // 呼ばれないはず
        
        $captured_content = null;
        Functions\when('update_term_meta')
            ->alias(function($id, $key, $val) use (&$captured_content) {
                if ($key === 'the_tag_content') {
                    $captured_content = $val;
                }
                return true;
            });
        
        // その他ダミーモック
        Functions\when('get_the_tag_meta_key')->justReturn('tag_meta_123');
        Functions\when('delete_term_meta')->justReturn(true);

        // 実行
        save_extra_tag_fileds($term_id);

        $this->assertSame('<input type="checkbox" name="test">', $captured_content);

        // クリーンアップ
        unset($_POST['taxonomy'], $_POST['_wpnonce'], $_POST['the_tag_content']);
    }

    /**
     * unfiltered_html 権限がない一般ユーザーがタグ本文を保存する場合、
     * wp_kses_post によって危険な HTML タグが適切に除去（サニタイズ）されることを検証します。
     */
    public function test_save_extra_tag_fileds_unfiltered_html権限がない場合はwp_kses_postを通す(): void
    {
        // 準備
        $term_id = 456;
        $_POST['taxonomy'] = 'post_tag';
        $_POST['_wpnonce'] = 'dummy_nonce';
        $_POST['the_tag_content'] = '<input type="checkbox" name="test">';

        // モック
        Functions\when('wp_verify_nonce')->justReturn(true);
        Functions\when('current_user_can')
            ->alias(function(string $cap) {
                if ($cap === 'edit_term') return true;
                if ($cap === 'unfiltered_html') return false; // 権限なし
                return false;
            });
        
        Functions\when('wp_kses_post')->justReturn('サニタイズされた文字列');
        
        $captured_content = null;
        Functions\when('update_term_meta')
            ->alias(function($id, $key, $val) use (&$captured_content) {
                if ($key === 'the_tag_content') {
                    $captured_content = $val;
                }
                return true;
            });
        
        // その他ダミーモック
        Functions\when('get_the_tag_meta_key')->justReturn('tag_meta_456');
        Functions\when('delete_term_meta')->justReturn(true);

        // 実行
        save_extra_tag_fileds($term_id);

        $this->assertSame('サニタイズされた文字列', $captured_content);

        // クリーンアップ
        unset($_POST['taxonomy'], $_POST['_wpnonce'], $_POST['the_tag_content']);
    }
}
