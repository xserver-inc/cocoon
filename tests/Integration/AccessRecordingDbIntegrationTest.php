<?php
/**
 * 独自DBクラス・接続切断・旧スキーマを対象としたPV記録の実DB検証
 */
namespace Cocoon\Tests\Integration;

// WordPressを読み込まないユニットテスト収集時のDBクラス定義の保留
if (class_exists('wpdb')):
class AccessRecordingRoutedDatabase extends \wpdb
{
    public string $logicalTable = '';
    public string $destinationTable = '';
    public array $recordingQueries = array();

    public function query($sql)
    {
        if ($this->logicalTable !== '' && strpos($sql, $this->logicalTable) !== false) {
            $this->recordingQueries[] = $sql;
            $sql = str_replace($this->logicalTable, $this->destinationTable, $sql);
        }
        return parent::query($sql);
    }

    public function rawQuery($sql)
    {
        return parent::query($sql);
    }
}
endif;

class AccessRecordingDbIntegrationTest extends IntegrationTestCase
{
    private array $server;
    private array $mods;
    private int $userId;
    private array $accessPostIds = array();

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = $_SERVER;
        $this->mods = get_theme_mods() ?: array();
        $this->userId = get_current_user_id();
        $this->assertTrue(create_accesses_table());
        set_theme_mod(OP_ACCESS_COUNT_ENABLE, 1);
        wp_set_current_user(0);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '192.0.2.1';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
        unset($_SERVER['HTTP_PURPOSE'], $_SERVER['HTTP_SEC_PURPOSE'], $_SERVER['HTTP_X_MOZ']);
    }

    protected function tearDown(): void
    {
        // WordPress環境の初期化前にスキップされた場合の復元処理の除外
        if (!isset($this->server)) {
            parent::tearDown();
            return;
        }

        global $wpdb;
        foreach ($this->accessPostIds as $id) $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id), array('%d'));
        $_SERVER = $this->server;
        update_option('theme_mods_' . get_stylesheet(), $this->mods);
        wp_set_current_user($this->userId);
        parent::tearDown();
    }

    private function fixture(): int
    {
        $id = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'DB互換性のPV検証'));
        $this->accessPostIds[] = $id;
        return $id;
    }

    /**
     * 記録中も独自DBクラスを維持し、振り分け先だけへ加算することの確認
     */
    public function testCustomDatabaseRoutingIsPreservedForEveryWrite(): void
    {
        global $wpdb;
        $original = $wpdb;
        $schema = $original->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
        $temporarySql = preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1);
        $driver = new AccessRecordingRoutedDatabase(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $driver->logicalTable = ACCESSES_TABLE_NAME;
        $driver->destinationTable = ACCESSES_TABLE_NAME . '_routed_test';
        try {
            // 通常テーブルへの書き込みを避けた、接続専用の論理テーブルと振り分け先
            $this->assertNotFalse($driver->rawQuery($temporarySql));
            $this->assertNotFalse($driver->rawQuery(str_replace(ACCESSES_TABLE_NAME, $driver->destinationTable, $temporarySql)));
            $wpdb = $driver;
            $date = '2098-10-21';
            $this->assertSame(1, cocoon_analytics_record_page_access(2145000001, $date, 'post', '192.0.2.1'));
            $this->assertFalse(cocoon_analytics_record_page_access(2145000001, $date, 'post', '192.0.2.1'));
            $this->assertSame(1, cocoon_analytics_record_page_access(2145000001, $date, 'post', '192.0.2.2'));
            $this->assertSame($driver, $wpdb);
            $this->assertCount(6, $driver->recordingQueries);
            $this->assertSame(1, $driver->rawQuery('SELECT * FROM `' . $driver->destinationTable . '`'));
            $this->assertSame(2, (int) $driver->last_result[0]->count);
            $this->assertSame(0, $driver->rawQuery('SELECT * FROM `' . ACCESSES_TABLE_NAME . '`'));
        } finally {
            $wpdb = $original;
            $driver->close();
        }
    }

    /**
     * 加算SQL直前の接続切断後も、再接続で1行1PVとして記録することの確認
     */
    public function testReconnectBeforeTheAtomicWriteDoesNotDuplicateTheRecord(): void
    {
        global $wpdb;
        $id = $this->fixture();
        $original = $wpdb;
        $victim = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $killer = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        $victim->suppress_errors(true);
        $connectionId = (int) $victim->get_var('SELECT CONNECTION_ID()');
        $killed = false;
        $filter = function($sql) use ($killer, $connectionId, &$killed) {
            if (!$killed && strpos($sql, 'INSERT INTO `' . ACCESSES_TABLE_NAME . '`') !== false
                && strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false) {
                $killed = true;
                $this->assertNotFalse($killer->query('KILL CONNECTION ' . $connectionId));
            }
            return $sql;
        };
        try {
            $wpdb = $victim;
            add_filter('query', $filter);
            $this->assertSame(1, @cocoon_analytics_record_page_access($id, current_time('Y-m-d'), 'post', '192.0.2.1'));
            remove_filter('query', $filter);
            $this->assertTrue($killed);
            $this->assertSame($victim, $wpdb);
            $this->assertNotSame($connectionId, (int) $victim->get_var('SELECT CONNECTION_ID()'));
            $row = $victim->get_row($victim->prepare('SELECT COUNT(*) AS rows_count, SUM(count) AS pv FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d', $id));
            $this->assertSame(1, (int) $row->rows_count);
            $this->assertSame(1, (int) $row->pv);
        } finally {
            remove_filter('query', $filter);
            $wpdb = $original;
            $victim->close();
            $killer->close();
        }
    }

    /**
     * 時刻列だけを持つ旧スキーマからの、一意キー追加と既存PVの引き継ぎ
     */
    public function testTimestampOnlySchemaIsUpgradedWithoutLosingPv(): void
    {
        global $wpdb;
        $id = $this->fixture();
        insert_accesses_record(array('post_id' => $id, 'date' => current_time('Y-m-d'), 'post_type' => 'post', 'count' => 7, 'last_ip' => '192.0.2.1', 'last_accessed_at' => time() - 31));
        $this->assertNotFalse($wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` DROP INDEX `' . INDEX_ACCESSES_RECORD_KEY . '`, DROP COLUMN record_key'));
        set_theme_mod(OP_ACCESSES_TABLE_VERSION, '0.0.5');
        try {
            $this->assertSame(1, logging_page_access($id, 'post'));
            $row = get_accesse_record_from($id, current_time('Y-m-d'), 'post');
            $this->assertSame(8, (int) $row->count);
            $this->assertSame(cocoon_analytics_record_key($id, current_time('Y-m-d'), 'post'), $row->record_key);
            $this->assertSame(ACCESSES_TABLE_VERSION, get_accesses_table_version());
            $this->assertSame('1', (string) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d', $id)));
        } finally {
            create_accesses_table();
        }
    }

    /**
     * 保存時刻が未来の場合にも符号なし整数の減算エラーが発生しないことの確認
     */
    public function testFutureTimestampDoesNotCauseAnUnsignedSubtractionError(): void
    {
        global $wpdb;
        $id = $this->fixture();
        insert_accesses_record(array('post_id' => $id, 'date' => current_time('Y-m-d'), 'post_type' => 'post', 'count' => 7, 'last_ip' => '192.0.2.1', 'last_accessed_at' => time() + 60));
        $this->assertFalse(logging_page_access($id, 'post'));
        $this->assertSame('', $wpdb->last_error);
        $this->assertSame(7, (int) get_accesse_record_from($id, current_time('Y-m-d'))->count);
        $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
        $this->assertSame(1, logging_page_access($id, 'post'));
        $this->assertSame(8, (int) get_accesse_record_from($id, current_time('Y-m-d'))->count);
    }

    /**
     * 検索後に同じIDの所属が変わった場合にも、別の記事・日付・投稿タイプへ誤加算しないことの確認
     */
    public function testLegacyAdoptionRechecksTheRecordIdentityAfterLookup(): void
    {
        global $wpdb;
        $id = $this->fixture();
        $otherId = $this->fixture();
        $date = '2098-10-23';
        $created = false;
        try {
            // 検索と更新の間の差し替えを通常データへ残さない、接続専用のアクセス表
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertIsArray($schema);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $created = true;
            foreach (array(array('post_id' => $otherId), array('date' => '2098-10-24'), array('post_type' => 'page')) as $changes) {
                $this->assertNotFalse($wpdb->query('DELETE FROM `' . ACCESSES_TABLE_NAME . '`'));
                $original = array('post_id' => $id, 'date' => $date, 'post_type' => 'post', 'count' => 7, 'last_ip' => '192.0.2.1', 'last_accessed_at' => 0);
                $this->assertSame(1, $wpdb->insert(ACCESSES_TABLE_NAME, $original));
                $legacyId = (string) $wpdb->insert_id;
                $swapped = false;
                $swap = function ($sql) use ($changes, $legacyId, &$swapped, $wpdb) {
                    if (!$swapped && strpos($sql, 'UPDATE IGNORE `' . ACCESSES_TABLE_NAME . '` SET record_key') === 0) {
                        // 実writerの通常SELECT完了後、採番UPDATE直前だけの対象差し替え
                        $swapped = true;
                        $this->assertSame(1, $wpdb->update(ACCESSES_TABLE_NAME, $changes, array('id' => $legacyId)));
                    }
                    return $sql;
                };
                add_filter('query', $swap);
                try {
                    $this->assertSame(1, cocoon_analytics_record_page_access($id, $date, 'post', '192.0.2.1'));
                    $this->assertSame('', $wpdb->last_error);
                } finally {
                    remove_filter('query', $swap);
                }
                $this->assertTrue($swapped, '採番UPDATE直前の差し替え未実行');
                $rows = $wpdb->get_results('SELECT * FROM `' . ACCESSES_TABLE_NAME . '` ORDER BY id ASC', ARRAY_A);
                $this->assertCount(2, $rows);
                $expected = array_merge($original, $changes);
                $this->assertSame($legacyId, $rows[0]['id']);
                $this->assertSame((string) $expected['post_id'], $rows[0]['post_id']);
                $this->assertSame($expected['date'], $rows[0]['date']);
                $this->assertSame($expected['post_type'], $rows[0]['post_type']);
                $this->assertSame('7', $rows[0]['count']);
                $this->assertNull($rows[0]['record_key']);
                $this->assertSame((string) $id, $rows[1]['post_id']);
                $this->assertSame($date, $rows[1]['date']);
                $this->assertSame('post', $rows[1]['post_type']);
                $this->assertSame('1', $rows[1]['count']);
                $this->assertSame(cocoon_analytics_record_key($id, $date, 'post'), $rows[1]['record_key']);
            }
        } finally {
            if ($created) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . ACCESSES_TABLE_NAME . '`');
        }
    }
}
