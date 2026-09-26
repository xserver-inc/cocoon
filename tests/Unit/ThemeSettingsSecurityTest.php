<?php
/**
 * テーマ設定バックアップの安全な解析に関するテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-backup/backup-func.php';

class ThemeSettingsSecurityTest extends TestCase
{
    /**
     * 正常なバックアップ配列を復元できる確認
     */
    public function test_正常な設定配列を解析できる(): void
    {
        $mods = ['color' => '#123456', 'flags' => ['enabled' => true]];
        $this->assertSame($mods, \parse_theme_settings_backup(serialize($mods)));
    }

    /**
     * シリアライズされたオブジェクトを拒否する確認
     */
    public function test_オブジェクトを含むバックアップを拒否する(): void
    {
        $this->assertFalse(\parse_theme_settings_backup(serialize(['payload' => new \stdClass()])));
    }

    /**
     * 壊れたバックアップと配列以外の値を拒否する確認
     */
    public function test_不正なバックアップを拒否する(): void
    {
        $this->assertFalse(\parse_theme_settings_backup('a:1:{broken'));
        $this->assertFalse(\parse_theme_settings_backup(serialize('not an array')));
    }
}
