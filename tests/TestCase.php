<?php
/**
 * Cocoon テーマ テスト基底クラス
 *
 * すべてのテストクラスはこのクラスを継承します。
 * Brain\Monkey のセットアップ/ティアダウンを自動的に行います。
 */

namespace Cocoon\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Brain\Monkey;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * テストのセットアップ
     * Brain\Monkey を初期化し、WordPress 関数のモック環境を準備します。
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // テスト環境全体のデフォルトモック定義（wp-mock-functions.php から移行）
        Monkey\Functions\when('current_user_can')->justReturn(false);
        Monkey\Functions\when('wp_kses_post')->returnArg();
        Monkey\Functions\when('metadata_exists')->justReturn(false);
    }

    /**
     * テストのティアダウン
     * Brain\Monkey のクリーンアップを行います。
     */
    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
