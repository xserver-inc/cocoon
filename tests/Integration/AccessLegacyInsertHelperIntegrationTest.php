<?php
/**
 * 旧スキーマへの直接挿入と、任意の最終計測時刻の実DB互換性検証
 */
namespace Cocoon\Tests\Integration;

class AccessLegacyInsertHelperIntegrationTest extends IntegrationTestCase
{
    /**
     * 移行前の旧表への従来5項目による挿入と、既存データ・スキーマバージョンの維持を検証
     */
    public function testLegacyFiveFieldsRemainInsertableBeforeSchemaMigration(): void
    {
        global $wpdb;
        $version = get_accesses_table_version();
        $this->withTemporarySchema(false, function () use ($wpdb) {
            $data = $this->recordData();
            $this->assertSame(1, insert_db_table_record(ACCESSES_TABLE_NAME, $data, array('%d', '%s', '%s', '%d', '%s')));
            $data['post_id']++;
            $this->assertSame(1, insert_accesses_record($data));
            $this->assertSame('', $wpdb->last_error);
            $rows = $wpdb->get_results('SELECT post_id, date, post_type, count, last_ip FROM `' . ACCESSES_TABLE_NAME . '` ORDER BY id LIMIT 2', ARRAY_A);
            $this->assertCount(2, $rows);
            $this->assertSame((string) $data['post_id'], $rows[1]['post_id']);
            unset($rows[0]['post_id'], $rows[1]['post_id']);
            $this->assertSame($rows[0], $rows[1]);
            $this->assertSame(array(), $wpdb->get_col('SHOW COLUMNS FROM `' . ACCESSES_TABLE_NAME . "` WHERE Field IN ('last_accessed_at', 'record_key')"));
        });
        $this->assertSame($version, get_accesses_table_version(), '直接挿入によるスキーマ移行やバージョン変更の防止');
    }

    /**
     * 時刻未指定時のINSERT列の省略と、新表の既定時刻0・未採番キーの維持を検証
     */
    public function testUnspecifiedTimestampUsesTheNewSchemaDefaultWithoutAddingTheColumn(): void
    {
        global $wpdb;
        $this->withTemporarySchema(true, function () use ($wpdb) {
            $insert = '';
            $observe = static function ($sql) use (&$insert) {
                if (preg_match('/^\s*INSERT\s+INTO\b/i', $sql)) $insert = $sql;
                return $sql;
            };
            add_filter('query', $observe);
            try {
                $this->assertSame(1, insert_accesses_record($this->recordData()));
            } finally {
                remove_filter('query', $observe);
            }
            $this->assertNotSame('', $insert);
            $this->assertStringNotContainsString('last_accessed_at', $insert);
            $this->assertSame('0', $wpdb->get_var('SELECT last_accessed_at FROM `' . ACCESSES_TABLE_NAME . '` LIMIT 1'));
            $this->assertNull($wpdb->get_var('SELECT record_key FROM `' . ACCESSES_TABLE_NAME . '` LIMIT 1'));
            $this->assertSame('', $wpdb->last_error);
        });
    }

    /**
     * 明示した0と文字列形式の最終計測時刻の、整数値での保存を検証
     */
    public function testExplicitTimestampIncludingZeroIsStoredOnTheNewSchema(): void
    {
        global $wpdb;
        $this->withTemporarySchema(true, function () use ($wpdb) {
            foreach (array(0, '1700000000') as $index => $timestamp) {
                $data = $this->recordData();
                $data['post_id'] += $index;
                $data['last_accessed_at'] = $timestamp;
                $this->assertSame(1, insert_accesses_record($data));
                $stored = $wpdb->get_var($wpdb->prepare('SELECT last_accessed_at FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d LIMIT 1', $data['post_id']));
                $this->assertSame((string) (int) $timestamp, $stored);
                $this->assertSame('', $wpdb->last_error);
            }
        });
    }

    /**
     * NULLの最終計測時刻の未指定扱いと、旧表への正常な挿入を検証
     */
    public function testNullTimestampRemainsOmittedForTheOldSchema(): void
    {
        global $wpdb;
        $this->withTemporarySchema(false, function () use ($wpdb) {
            $data = $this->recordData();
            $data['last_accessed_at'] = null;
            $this->assertSame(1, insert_accesses_record($data));
            $this->assertSame('7', $wpdb->get_var('SELECT count FROM `' . ACCESSES_TABLE_NAME . '` LIMIT 1'));
            $this->assertSame('', $wpdb->last_error);
        });
    }

    private function recordData(): array
    {
        return array('post_id' => 1530000201, 'date' => '2098-10-23', 'post_type' => 'post', 'count' => 7, 'last_ip' => '192.0.2.1');
    }

    private function withTemporarySchema(bool $withTimestamp, callable $verify): void
    {
        global $wpdb;
        $temporary = false;
        $suppressed = $wpdb->suppress_errors(false);
        try {
            // 永続表やテーマ設定の変更を避ける、同名の接続専用テスト表による置き換え
            $extra = $withTimestamp ? ', last_accessed_at bigint(20) unsigned DEFAULT 0, record_key varchar(64) DEFAULT NULL' : '';
            $this->assertNotFalse($wpdb->query('CREATE TEMPORARY TABLE `' . ACCESSES_TABLE_NAME . "` (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                post_id bigint(20),
                date varchar(20),
                post_type varchar(126) DEFAULT 'post',
                count bigint(20) DEFAULT 0,
                last_ip varchar(40)" . $extra . ',
                PRIMARY KEY (id))'));
            $temporary = true;
            $verify();
        } finally {
            // 既存の永続アクセス表を対象に含めない、一時表だけの解放
            if ($temporary) $wpdb->query('DROP TEMPORARY TABLE IF EXISTS `' . ACCESSES_TABLE_NAME . '`');
            $wpdb->suppress_errors($suppressed);
        }
    }
}
