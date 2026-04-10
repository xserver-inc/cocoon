<?php
/**
 * ログインユーザー限定ブロックのユニットテスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class LoginUserOnlyBlockTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 2) . '/blocks/src/block/login-user-only/index.php';
    }

    /**
     * ログイン状況に応じてコンテンツがそのまま表示されることをテスト
     */
    public function test_render_block_cocoon_login_user_only_ログイン時はコンテンツを表示する()
    {
        global $test_mock_is_user_logged_in;
        $test_mock_is_user_logged_in = true;

        $attributes = [];
        $content = 'ログイン限定の秘密のコンテンツ';

        $result = render_block_cocoon_login_user_only($attributes, $content);
        $this->assertSame('ログイン限定の秘密のコンテンツ', $result);
    }

    /**
     * 未ログイン状況では指定されたメッセージがラッパークラス付きで表示されることをテスト
     */
    public function test_render_block_cocoon_login_user_only_未ログイン時はメッセージとラッパークラスを表示する()
    {
        global $test_mock_is_user_logged_in;
        $test_mock_is_user_logged_in = false;

        $attributes = [
            'msg' => '会員限定です',
            'align' => 'wide',
            'className' => 'custom-login-box'
        ];
        $content = 'ログイン限定の秘密のコンテンツ';

        $result = render_block_cocoon_login_user_only($attributes, $content);

        // align と className がラッパーのクラスに正しく含まれているか確認
        $this->assertStringContainsString('class="wp-block-cocoon-blocks-login-user-only login-user-only block-box alignwide custom-login-box"', $result);
        $this->assertStringContainsString('>会員限定です</div>', $result);
    }
}
