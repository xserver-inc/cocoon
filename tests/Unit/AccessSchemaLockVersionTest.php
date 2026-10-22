<?php
/**
 * DB製品とバージョンによる複数名前付きロック対応の検証
 */
namespace Cocoon\Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/lib/page-access/schema-lock-func.php';

class AccessSchemaLockVersionTest extends TestCase
{
    public function testSupportUsesTheServerProductAndVersion(): void
    {
        $versions = array(
            '5.6.51' => false,
            '5.7.4-m14' => false,
            '5.7.5-m15' => true,
            '5.7.44-log' => true,
            '8.0.46' => true,
            '8.4.6-0ubuntu0.24.04.1' => true,
            '5.5.68-MariaDB' => false,
            '10.0.1-MariaDB' => false,
            '10.0.2-MariaDB' => true,
            '5.5.5-10.11.14-MariaDB-0+deb12u2' => true,
            '11.4.8-MariaDB-log' => true,
            '' => null,
            'unknown' => null,
            'MariaDB unknown' => null,
        );
        foreach ($versions as $version => $expected) {
            $this->assertSame($expected, cocoon_analytics_schema_supports_multiple_locks($version), $version);
        }
        $this->assertNull(cocoon_analytics_schema_supports_multiple_locks(null));
    }
}
