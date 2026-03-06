<?php
/**
 * db.php のユニットテスト
 *
 * データベース関連ユーティリティ関数の中で、
 * WordPress依存が少ない純粋関数をテストします。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class DbFuncTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // db.php をロード（グローバル$wpdbが不要な関数のみテスト）
        require_once dirname(__DIR__, 2) . '/lib/db.php';
    }

    // ========================================================================
    // is_update_db_table()
    // ========================================================================

    public function test_バージョンが異なる場合trueを返す(): void
    {
        // バージョンが異なるとき、テーブル更新が必要
        $this->assertTrue(is_update_db_table('1.0', '2.0'));
    }

    public function test_バージョンが同じ場合falsyを返す(): void
    {
        // バージョンが同じとき、テーブル更新は不要（null=falsy）
        $result = is_update_db_table('1.0', '1.0');
        $this->assertEmpty($result);
    }

    public function test_空文字列と値の比較でtrueを返す(): void
    {
        // 初回インストール時を想定（installed_verが空）
        $this->assertTrue(is_update_db_table('', '1.0'));
    }

    public function test_マイナーバージョンの違いでtrueを返す(): void
    {
        $this->assertTrue(is_update_db_table('1.0', '1.1'));
    }

    public function test_同じバージョン文字列でfalsyを返す(): void
    {
        $result = is_update_db_table('2.5.1', '2.5.1');
        $this->assertEmpty($result);
    }

    public function test_数値文字列の比較が正しく動作する(): void
    {
        // 文字列としての比較であることを検証
        $this->assertTrue(is_update_db_table('9', '10'));
    }
}
