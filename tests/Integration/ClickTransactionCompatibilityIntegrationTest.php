<?php
/**
 * 旧WordPressを含むクリック解析のDB接続引き継ぎテスト
 */

namespace Cocoon\Tests\Integration;

class ClickTransactionCompatibilityIntegrationTest extends IntegrationTestCase
{
    /**
     * 接続コピーによるSELECTと元のMySQL接続の維持
     */
    public function testCopiedConnectionQueriesAndRestoresOriginalConnection(): void
    {
        global $wpdb;
        $original = $wpdb;
        $connection_id = $original->get_var('SELECT CONNECTION_ID()');
        $copy = new \Cocoon_Click_Transaction_DB($original);

        try {
            $this->assertSame('1', $copy->get_var('SELECT 1'));
            $this->assertSame($connection_id, $copy->get_var('SELECT CONNECTION_ID()'));
            // フラグのない新しいwpdbへの不要なプロパティ追加の防止
            $this->assertArrayNotHasKey('use_mysqli', get_object_vars($copy));
        } finally {
            $copy->restore($original);
        }
        $this->assertSame($original, $wpdb);
        $this->assertSame($connection_id, $original->get_var('SELECT CONNECTION_ID()'));
    }

    /**
     * 同じ接続を使った更新の取り消しと通常wpdbへの復帰
     */
    public function testTransactionRollbackPreservesOriginalConnection(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'cocoon_click_transaction_compat';
        $original = $wpdb;
        $connection_id = $original->get_var('SELECT CONNECTION_ID()');
        $this->assertNotFalse($wpdb->query("CREATE TEMPORARY TABLE `$table` (id BIGINT PRIMARY KEY) ENGINE=InnoDB"));
        $transaction = false;
        try {
            $transaction = cocoon_click_begin_transaction();
            $this->assertSame($original, $transaction);
            $this->assertNotFalse($wpdb->query("INSERT INTO `$table` (id) VALUES (1)"));
            $this->assertSame('1', $wpdb->get_var("SELECT COUNT(*) FROM `$table`"));
            $this->assertNotFalse($wpdb->query('ROLLBACK'));
            $this->assertSame('0', $wpdb->get_var("SELECT COUNT(*) FROM `$table`"));
        } finally {
            if ($transaction) {
                $wpdb->query('ROLLBACK');
                cocoon_click_end_transaction($transaction);
            }
            $original->query("DROP TEMPORARY TABLE IF EXISTS `$table`");
        }
        $this->assertSame($original, $wpdb);
        $this->assertSame($connection_id, $wpdb->get_var('SELECT CONNECTION_ID()'));
    }
}
