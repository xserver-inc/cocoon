<?php
/**
 * WordPress 統合テスト 基底クラス
 *
 * WP_UnitTestCase を拡張し、WordPress のフル環境でテストを実行します。
 * DB トランザクションによるテスト分離が自動的に行われます。
 */

namespace Cocoon\Tests\Integration;

/**
 * WP_UnitTestCase が存在する場合のみ定義
 * （統合テスト環境が利用可能な場合）
 */
if (class_exists('WP_UnitTestCase')) {

    abstract class IntegrationTestCase extends \WP_UnitTestCase
    {
        /**
         * テーマがアクティブであることを確認
         */
        public function assertThemeActive(): void
        {
            $this->assertSame('cocoon-master', get_stylesheet());
        }
    }

} else {

    // WordPress テスト環境がない場合のフォールバック
    abstract class IntegrationTestCase extends \PHPUnit\Framework\TestCase
    {
        protected function setUp(): void
        {
            parent::setUp();
            $this->markTestSkipped('WordPress テスト環境が利用できません。WP_TESTS_DIR を設定してください。');
        }
    }

}
