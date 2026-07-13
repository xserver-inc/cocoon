<?php
/**
 * lib/custom-fields/update-field.php のユニットテスト
 *
 * 「更新しない」(update_level=low) 選択時に post_modified が
 * 不正な 0000-00-00 にならないことを検証します。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class UpdateFieldTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/lib/custom-fields/update-field.php';
    }

    protected function tearDown(): void
    {
        unset($_POST['update_level']);
        parent::tearDown();
    }

    private function baseData(): array
    {
        return [
            'post_status'       => 'publish',
            'post_date'         => '2025-03-01 12:00:00',
            'post_modified'     => '2025-03-01 12:00:00',
            'post_modified_gmt' => '2025-03-01 03:00:00',
        ];
    }

    public function test_更新しない選択でlast_modifiedが空なら更新日を投稿日にしDBを汚さない(): void
    {
        Functions\when('get_post_meta')->justReturn(''); // last_modified 未設定（初回投稿など）
        Functions\when('get_gmt_from_date')->alias(fn($d) => $d . ' GMT');
        Functions\when('add_post_meta')->justReturn(true);
        Functions\when('update_post_meta')->justReturn(true);

        $_POST['update_level'] = 'low';
        $result = update_custom_insert_post_data($this->baseData(), ['ID' => 0]);

        $this->assertSame('2025-03-01 12:00:00', $result['post_modified']);
        $this->assertNotSame('', $result['post_modified']);
    }

    public function test_更新しない選択でlast_modifiedがあればその値に固定する(): void
    {
        Functions\when('get_post_meta')->justReturn('2024-01-01 00:00:00');
        Functions\when('get_gmt_from_date')->alias(fn($d) => $d . ' GMT');
        Functions\when('add_post_meta')->justReturn(true);
        Functions\when('update_post_meta')->justReturn(true);

        $_POST['update_level'] = 'low';
        $result = update_custom_insert_post_data($this->baseData(), ['ID' => 123]);

        $this->assertSame('2024-01-01 00:00:00', $result['post_modified']);
    }
}
