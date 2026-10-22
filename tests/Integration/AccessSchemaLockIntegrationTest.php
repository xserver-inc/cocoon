<?php
/**
 * 旧DBでの名前付きロック保持と、移行成否の実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessSchemaLockIntegrationTest extends IntegrationTestCase
{
    public function testLegacyMigrationPreservesTheCallerNamedLockAndExistingPv(): void
    {
        $this->assertMigrationPreservesTheCallerNamedLock('5.6.51', 0);
    }

    public function testCurrentMigrationRetainsItsNamedExclusionAndTheCallerNamedLock(): void
    {
        $this->assertMigrationPreservesTheCallerNamedLock('8.0.46', 2);
    }

    private function assertMigrationPreservesTheCallerNamedLock(string $version, int $expectedNamedCalls): void
    {
        global $wpdb;
        $this->withOldSchema(function () use ($wpdb, $version, $expectedNamedCalls) {
            $lock = 'cocoon_schema_caller_' . bin2hex(random_bytes(8));
            $connection = $wpdb->get_var('SELECT CONNECTION_ID() LIMIT 1');
            $this->assertSame('1', $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0) LIMIT 1', $lock)));
            $named = array();
            $queries = array();
            $observe = static function ($sql) use ($version, &$named, &$queries) {
                $queries[] = $sql;
                if ($sql === 'SELECT VERSION() LIMIT 1') return "SELECT '" . $version . "' LIMIT 1";
                if (preg_match('/\b(?:GET_LOCK|RELEASE_LOCK)\s*\(/i', $sql)) $named[] = $sql;
                return $sql;
            };
            try {
                add_filter('query', $observe);
                try {
                    $this->assertTrue(update_accesses_table());
                } finally {
                    remove_filter('query', $observe);
                }
                $this->assertCount($expectedNamedCalls, $named);
                $this->assertContains('SELECT VERSION() LIMIT 1', $queries);
                $this->assertSame($connection, $wpdb->get_var($wpdb->prepare('SELECT IS_USED_LOCK(%s) LIMIT 1', $lock)), '呼び出し元の名前付きロックの保持');
                $this->assertSame(ACCESSES_TABLE_VERSION, get_accesses_table_version());
                $this->assertCount(2, $wpdb->get_col('SHOW COLUMNS FROM `' . ACCESSES_TABLE_NAME . "` WHERE Field IN ('last_accessed_at', 'record_key')"));
                $indexes = $wpdb->get_results($wpdb->prepare('SHOW INDEX FROM `' . ACCESSES_TABLE_NAME . '` WHERE Key_name = %s', INDEX_ACCESSES_RECORD_KEY));
                $this->assertCount(1, $indexes);
                $this->assertSame('0', $indexes[0]->Non_unique);
                $this->assertSame('7', $wpdb->get_var('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '` LIMIT 1'));
                $this->assertFalse($wpdb->suppress_errors);
            } finally {
                $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s) LIMIT 1', $lock));
            }
        });
    }

    public function testFailedLegacyDdlDoesNotAdvanceTheSchemaVersionOrPrintDatabaseErrors(): void
    {
        global $wpdb;
        $this->withOldSchema(function () use ($wpdb) {
            $observe = static function ($sql) {
                if ($sql === 'SELECT VERSION() LIMIT 1') return "SELECT '5.6.51' LIMIT 1";
                if (preg_match('/^\s*ALTER\s+TABLE\s+`?' . preg_quote(ACCESSES_TABLE_NAME, '/') . '`?\b/i', $sql)) return 'INVALID COCOON SCHEMA MIGRATION';
                return $sql;
            };
            add_filter('query', $observe);
            ob_start();
            try {
                $this->assertFalse(update_accesses_table());
                $output = ob_get_contents();
            } finally {
                ob_end_clean();
                remove_filter('query', $observe);
            }
            $this->assertSame('', $output);
            $this->assertSame('0.0.4', get_accesses_table_version());
            $this->assertSame(array(), $wpdb->get_col('SHOW COLUMNS FROM `' . ACCESSES_TABLE_NAME . "` WHERE Field IN ('last_accessed_at', 'record_key')"));
            $this->assertSame('7', $wpdb->get_var('SELECT SUM(count) FROM `' . ACCESSES_TABLE_NAME . '` LIMIT 1'));
            $this->assertFalse($wpdb->suppress_errors);
        });
    }

    public function testUnknownServerVersionDefersMigrationWithoutDdlOrNamedLocks(): void
    {
        global $wpdb;
        $this->withOldSchema(function () use ($wpdb) {
            $mutations = array();
            $observe = static function ($sql) use (&$mutations) {
                if ($sql === 'SELECT VERSION() LIMIT 1') return 'SELECT NULL LIMIT 1';
                if (preg_match('/^\s*(?:CREATE|ALTER|DROP)\b|\b(?:GET_LOCK|RELEASE_LOCK)\s*\(/i', $sql)) $mutations[] = $sql;
                return $sql;
            };
            add_filter('query', $observe);
            try {
                $this->assertFalse(update_accesses_table());
            } finally {
                remove_filter('query', $observe);
            }
            $this->assertSame(array(), $mutations);
            $this->assertSame('0.0.4', get_accesses_table_version());
            $this->assertFalse($wpdb->suppress_errors);
        });
    }

    private function withOldSchema(callable $verify): void
    {
        global $wpdb;
        $this->assertTrue(create_accesses_table());
        $mods = get_theme_mods();
        $suppressed = $wpdb->suppress_errors(false);
        $temporary = false;
        try {
            // 開発用アクセス表の変更を避ける、接続専用の旧0.0.4スキーマ
            $schema = $wpdb->get_row('SHOW CREATE TABLE `' . ACCESSES_TABLE_NAME . '`', ARRAY_N);
            $this->assertIsArray($schema);
            $this->assertNotFalse($wpdb->query(preg_replace('/^CREATE TABLE /', 'CREATE TEMPORARY TABLE ', $schema[1], 1)));
            $temporary = true;
            $this->assertNotFalse($wpdb->query('ALTER TABLE `' . ACCESSES_TABLE_NAME . '` DROP INDEX `' . INDEX_ACCESSES_RECORD_KEY . '`, DROP COLUMN record_key, DROP COLUMN last_accessed_at'));
            $this->assertSame(1, $wpdb->insert(ACCESSES_TABLE_NAME, array('post_id' => 1530000101, 'post_type' => 'post', 'date' => '2098-10-23', 'count' => 7, 'last_ip' => '192.0.2.1')));
            set_theme_mod(OP_ACCESSES_TABLE_VERSION, '0.0.4');
            $verify();
        } finally {
            if ($temporary) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . ACCESSES_TABLE_NAME . '`');
            update_option('theme_mods_' . get_stylesheet(), $mods);
            $wpdb->suppress_errors($suppressed);
        }
    }
}
