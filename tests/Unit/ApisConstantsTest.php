<?php
/**
 * 商品リンク自動更新定数の定義に関する回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class ApisConstantsTest extends TestCase
{
    /**
     * 先に定義された商品リンク自動更新定数が警告なく維持されることを検証
     */
    public function test_既存の商品リンク自動更新定数を維持する(): void
    {
        define('PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT', 5);
        define('PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS', 0);
        define('PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS', 0);

        require dirname(__DIR__, 2) . '/lib/page-settings/apis-funcs.php';

        $this->assertSame(5, PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT);
        $this->assertSame(0, PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS);
        $this->assertSame(0, PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS);
    }

    /**
     * 未定義の場合は従来の商品リンク自動更新既定値が使われることを検証
     */
    public function test_未定義なら従来の商品リンク自動更新既定値を使う(): void
    {
        require dirname(__DIR__, 2) . '/lib/page-settings/apis-funcs.php';

        $this->assertSame(30, PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT);
        $this->assertSame(2, PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS);
        $this->assertSame(2, PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS);
    }
}
