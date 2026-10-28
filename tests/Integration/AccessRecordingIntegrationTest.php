<?php
/**
 * PV記録の入力検証・再閲覧間隔・排他制御の実DB検証
 */
namespace Cocoon\Tests\Integration;

use Cocoon\Tests\Support\AccessRecordingWorkerPool;

require_once dirname(__DIR__) . '/Support/AccessRecordingWorkerPool.php';

class AccessRecordingIntegrationTest extends IntegrationTestCase
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

    private function fixture(string $status = 'publish', string $type = 'post'): int
    {
        $id = $this->createPost(array('post_status' => $status, 'post_type' => $type, 'post_title' => 'PV記録回帰テスト'));
        $this->accessPostIds[] = $id;
        return $id;
    }

    private function countPv(int $id): int
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare('SELECT COALESCE(SUM(count),0) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id));
    }

    /**
     * 記事の存在・公開状態・投稿タイプ・HTTPメソッドの検証
     */
    public function testInvalidRequestsDoNotCreateAccessRecords(): void
    {
        foreach (array('draft', 'private', 'trash') as $status) {
            $id = $this->fixture($status);
            $this->assertFalse(logging_page_access($id, 'post'));
            $this->assertSame(0, $this->countPv($id));
        }
        $missing = 999999999;
        $this->accessPostIds[] = $missing;
        $this->assertNull(get_post($missing));
        $this->assertFalse(logging_page_access($missing, 'post'));
        $this->assertSame(0, $this->countPv($missing));
        $pageId = $this->fixture('publish', 'page');
        $this->assertFalse(logging_page_access($pageId, 'post'));
        $this->assertSame(0, $this->countPv($pageId));
        foreach (array('HEAD', 'POST', 'OPTIONS') as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $this->assertFalse(logging_page_access($pageId, 'page'));
            $this->assertSame(0, $this->countPv($pageId));
        }
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertSame(1, logging_page_access($pageId, 'page'));
        $this->assertSame(1, $this->countPv($pageId));
    }

    /**
     * 同一IPの短時間の連打の除外と30秒後の再計測
     */
    public function testSameIpCanBeCountedAgainAfterThirtySeconds(): void
    {
        global $wpdb;
        $id = $this->fixture();
        $this->assertSame(1, logging_page_access($id, 'post'));
        $this->assertFalse(logging_page_access($id, 'post'));
        $this->assertSame(1, $this->countPv($id));
        $wpdb->update(ACCESSES_TABLE_NAME, array('last_accessed_at' => time() - 30), array('post_id' => $id));
        $this->assertSame(1, logging_page_access($id, 'post'));
        $this->assertSame(2, $this->countPv($id));
        $this->assertFalse(logging_page_access($id, 'post'));
        $this->assertSame(2, $this->countPv($id));
        $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
        $this->assertSame(1, logging_page_access($id, 'post'));
        $this->assertSame(3, $this->countPv($id));
    }

    /**
     * 加算SQLの失敗時にPVや連打判定の時刻が消費されないことの確認
     */
    public function testFailedAtomicWriteDoesNotConsumePvOrTheRepeatInterval(): void
    {
        global $wpdb;
        $id = $this->fixture();
        insert_accesses_record(array('post_id' => $id, 'post_type' => 'post', 'date' => current_time('Y-m-d'), 'count' => 7, 'last_ip' => '192.0.2.1'));
        $original = $wpdb;
        $filter = function($sql) {
            return strpos($sql, 'INSERT INTO `' . ACCESSES_TABLE_NAME . '`') !== false
                && strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false ? '' : $sql;
        };
        add_filter('query', $filter);
        try {
            $this->assertFalse(logging_page_access($id, 'post'));
            $this->assertSame($original, $wpdb);
            $this->assertSame(7, $this->countPv($id));
            $this->assertSame(0, (int) get_accesse_record_from($id, current_time('Y-m-d'))->last_accessed_at);
        } finally {
            remove_filter('query', $filter);
        }
        $this->assertSame(1, logging_page_access($id, 'post'));
        $this->assertSame(8, $this->countPv($id));
    }

    /**
     * 複数IPの記録と連打除外が一意キーを持つ同じ1行で完結することの確認
     */
    public function testRepeatedRecordingUsesOneUniqueRow(): void
    {
        global $wpdb;
        $id = $this->fixture();
        for ($i = 1; $i <= 10; $i++) {
            $_SERVER['REMOTE_ADDR'] = '192.0.2.' . $i;
            $this->assertSame(1, logging_page_access($id, 'post'));
        }
        $this->assertSame(10, $this->countPv($id));
        $this->assertSame('1', (string) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id)));
        $this->assertFalse(logging_page_access($id, 'post'));
        $this->assertSame(cocoon_analytics_record_key($id, current_time('Y-m-d'), 'post'), get_accesse_record_from($id, current_time('Y-m-d'))->record_key);
    }

    /**
     * 既存の重複行や過去PVを削除せずに、更新先を1行へ固定することの確認
     */
    public function testExistingDuplicateRowsKeepTheirHistoricalPv(): void
    {
        global $wpdb;
        $id = $this->fixture();
        foreach (array(2, 3) as $count) {
            insert_accesses_record(array('post_id' => $id, 'post_type' => 'post', 'date' => current_time('Y-m-d'), 'count' => $count, 'last_ip' => '192.0.2.1'));
        }
        $originalIds = $wpdb->get_col($wpdb->prepare('SELECT id FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d ORDER BY id ASC', $id));
        $this->assertCount(2, $originalIds);
        $this->assertSame(1, logging_page_access($id, 'post'));
        $this->assertSame(6, $this->countPv($id));
        $this->assertFalse(logging_page_access($id, 'post'));
        $this->assertSame(6, $this->countPv($id));
        $rows = $wpdb->get_results($wpdb->prepare('SELECT id, count, record_key FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d ORDER BY id ASC', $id));
        $this->assertSame($originalIds, wp_list_pluck($rows, 'id'));
        $this->assertSame(array(3, 3), array_map('intval', wp_list_pluck($rows, 'count')));
        $this->assertNull($rows[1]->record_key);
    }

    /**
     * 別プロセスの20回の同時更新が1行20PVとして残ることの確認
     */
    public function testParallelProcessesDoNotLoseIncrementsOrCreateDuplicateRows(): void
    {
        global $wpdb;
        $id = $this->fixture();
        $this->assertSame(20, $this->runWorkers($id, false));
        $this->assertSame(20, $this->countPv($id));
        $this->assertSame('1', (string) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id)));
    }

    /**
     * 同一IPの初回アクセスが競合しても1行1PVだけが残ることの確認
     */
    public function testParallelFirstViewsFromTheSameIpAreCountedOnce(): void
    {
        global $wpdb;
        $id = $this->fixture();
        $this->assertSame(1, $this->runWorkers($id, true));
        $this->assertSame(1, $this->countPv($id));
        $this->assertSame('1', (string) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id)));
    }

    private function runWorkers(int $id, bool $sameIp): int
    {
        global $wpdb;
        $environment = getenv();
        $environment['COCOON_PV_TEST_WORKER'] = '1';
        $environment['COCOON_PV_TEST_WP_ROOT'] = ABSPATH;
        // 認証情報をコマンド引数へ出さない、テスト専用DBの接続情報の受け渡し
        $environment['WORDPRESS_DB_HOST'] = DB_HOST;
        $environment['WORDPRESS_DB_NAME'] = DB_NAME;
        $environment['WORDPRESS_DB_USER'] = DB_USER;
        $environment['WORDPRESS_DB_PASSWORD'] = DB_PASSWORD;
        $environment['WORDPRESS_TABLE_PREFIX'] = $wpdb->prefix;
        $commands = array();
        // PCの負荷を抑えた、同時2プロセスまでの実行
        foreach (array(1, 101) as $base) {
            $commands[] = array(PHP_BINARY, dirname(__DIR__) . '/fixtures/access-recording-worker.php', (string) $id, (string) $base, $sameIp ? 'same' : 'different');
        }
        return (new AccessRecordingWorkerPool())->run($commands, $environment);
    }

    /**
     * 旧スキーマのPVを維持したまま初回計測で時刻列と一意キーを追加することの確認
     */
    public function testOldSchemaIsUpgradedWithoutLosingPv(): void
    {
        global $wpdb;
        $id = $this->fixture();
        insert_accesses_record(array('post_id' => $id, 'post_type' => 'post', 'date' => current_time('Y-m-d'), 'count' => 7, 'last_ip' => '192.0.2.1'));
        $wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` DROP INDEX `' . INDEX_ACCESSES_RECORD_KEY . '`, DROP COLUMN record_key, DROP COLUMN last_accessed_at');
        set_theme_mod(OP_ACCESSES_TABLE_VERSION, '0.0.4');
        try {
            $this->assertSame(1, logging_page_access($id, 'post'));
            $this->assertSame(8, $this->countPv($id));
            $this->assertSame(ACCESSES_TABLE_VERSION, get_accesses_table_version());
            $this->assertGreaterThan(0, (int) get_accesse_record_from($id, current_time('Y-m-d'))->last_accessed_at);
        } finally {
            create_accesses_table();
        }
    }
}
