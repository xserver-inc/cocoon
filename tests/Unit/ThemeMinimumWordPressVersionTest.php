<?php
/**
 * WordPress初版の表記とテーマ最低要件の互換性テスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ThemeMinimumWordPressVersionTest extends TestCase
{
    /**
     * 配布CSSと生成元SCSSの最低要件による6.1初版の許可と6.0系列の拒否
     */
    public function testThemeRequirementsAcceptWordPress61InitialRelease(): void
    {
        $requirements = [];
        foreach (['scss/style.scss', 'style.css'] as $file) {
            $contents = file_get_contents(dirname(__DIR__, 2) . '/' . $file);
            $this->assertSame(1, preg_match('/^Requires at least:\s*(\S+)/m', $contents, $matches));
            $requirements[$file] = $matches[1];
            // ゼロ末尾の正規化がない旧WordPressと同じ最低版の比較
            $this->assertTrue(version_compare('6.1', $matches[1], '>='), $file);
            $this->assertFalse(version_compare('6.0.9', $matches[1], '>='), $file);
        }
        $this->assertSame($requirements['scss/style.scss'], $requirements['style.css']);
    }
}
