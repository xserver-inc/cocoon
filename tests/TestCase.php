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
