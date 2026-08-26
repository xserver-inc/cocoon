<?php
/**
 * Cocoon独自プロフィール項目の保存処理に関する回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;

class ProfileSaveTest extends TestCase
{
    /** @var array<string, string> */
    private array $updates = [];

    /** @var array<string, mixed> */
    private array $original_post = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/lib/profile.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->original_post = $_POST;
        $_POST = [];
        $this->updates = [];
        Functions\when('wp_unslash')
            ->alias(static function ($value) {
                return is_array($value) ? array_map('stripslashes', $value) : stripslashes((string) $value);
            });
    }

    protected function tearDown(): void
    {
        $_POST = $this->original_post;

        parent::tearDown();
    }

    /**
     * 全入力欄が送信された通常保存で送信値だけが更新されることを検証
     */
    public function test_全入力欄が送信された通常保存では送信値だけを更新する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'upladed_avatar' => 'https://example.com/avatar.png',
            'profile_page_url' => 'https://example.com/profile?name=日本語',
            'line_at_url' => 'https://line.me/R/ti/p/%40cocoon',
        ];

        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame(
            [
                'upladed_avatar' => esc_url_raw('https://example.com/avatar.png'),
                'profile_page_url' => esc_url_raw('https://example.com/profile?name=日本語'),
            ],
            $this->updates
        );
        $this->assertSame(esc_url_raw('https://line.me/R/ti/p/@cocoon'), $_POST['line_at_url']);
    }

    /**
     * Cocoon独自項目が未送信なら既存メタが更新されないことを検証
     */
    public function test_Cocoon独自項目が未送信なら既存メタを更新しない(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'display_name' => 'WordPress標準項目だけ',
        ];

        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame([], $this->updates);
    }

    /**
     * 一部項目だけの送信では他のメタが保持されることを検証
     */
    public function test_一部項目だけの送信では他のメタを保持する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'profile_page_url' => 'https://example.com/新しいプロフィール',
        ];

        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame(
            ['profile_page_url' => esc_url_raw('https://example.com/新しいプロフィール')],
            $this->updates
        );
        $this->assertArrayNotHasKey('upladed_avatar', $this->updates);
    }

    /**
     * 明示的な空文字で対象項目をクリアできることを検証
     */
    public function test_明示的な空文字は対象項目をクリアする(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'upladed_avatar' => '',
        ];

        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame(['upladed_avatar' => ''], $this->updates);
    }

    /**
     * javascript URLが空文字へ正規化されることを検証
     */
    public function test_javascript_URLは空文字へ正規化する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'profile_page_url' => 'javascript:alert(1)',
        ];

        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame(['profile_page_url' => ''], $this->updates);
    }

    /**
     * 他ユーザーを編集できる権限があれば対象ユーザーが更新されることを検証
     */
    public function test_他ユーザーを編集できる権限があれば対象ユーザーを更新する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'upladed_avatar' => 'https://example.com/other-user.png',
        ];

        $this->allow_profile_update(99);

        update_avatar_to_user_profile(99);

        $this->assertSame(['upladed_avatar' => 'https://example.com/other-user.png'], $this->updates);
    }

    /**
     * edit_user権限がなければ更新されないことを検証
     */
    public function test_権限がなければ更新しない(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'upladed_avatar' => 'https://example.com/avatar.png',
        ];
        Functions\when('current_user_can')->justReturn(false);
        Functions\expect('wp_verify_nonce')->never();
        Functions\expect('update_user_meta')->never();

        update_avatar_to_user_profile(42);

        $this->addToAssertionCount(1);
    }

    /**
     * nonceが送信されなければ更新されないことを検証
     */
    public function test_nonceがなければ更新しない(): void
    {
        $_POST = [
            'upladed_avatar' => 'https://example.com/avatar.png',
        ];
        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('wp_verify_nonce')->never();
        Functions\expect('update_user_meta')->never();

        update_avatar_to_user_profile(42);

        $this->addToAssertionCount(1);
    }

    /**
     * 不正または期限切れnonceでは更新されないことを検証
     */
    public function test_不正または期限切れnonceでは更新しない(): void
    {
        $_POST = [
            '_wpnonce' => 'invalid-or-expired-nonce',
            'upladed_avatar' => 'https://example.com/avatar.png',
        ];
        Functions\when('current_user_can')->justReturn(true);
        Functions\when('wp_verify_nonce')->justReturn(false);
        Functions\expect('update_user_meta')->never();

        update_avatar_to_user_profile(42);

        $this->addToAssertionCount(1);
    }

    /**
     * nonceが配列でもWarningを出さず更新されないことを検証
     */
    public function test_nonceが配列なら更新しない(): void
    {
        $_POST = [
            '_wpnonce' => ['不正な配列'],
            'upladed_avatar' => 'https://example.com/avatar.png',
        ];
        Functions\when('current_user_can')->justReturn(true);
        Functions\expect('wp_verify_nonce')->never();
        Functions\expect('update_user_meta')->never();

        update_avatar_to_user_profile(42);

        $this->addToAssertionCount(1);
    }

    /**
     * URLをwp_unslashした後に保存用URLとして正規化することを検証
     */
    public function test_スラッシュを除去してからURLを正規化する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'profile_page_url' => 'slash-added-url',
        ];
        Functions\when('wp_unslash')
            ->alias(static function ($value): string {
                return $value === 'slash-added-url' ? 'https://example.com/unslashed' : (string) $value;
            });
        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame(
            ['profile_page_url' => esc_url_raw('https://example.com/unslashed')],
            $this->updates
        );
    }

    /**
     * line_at_urlの明示的な空文字をWordPress標準保存へ渡せることを検証
     */
    public function test_line_at_urlの明示的な空文字を維持する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'line_at_url' => '',
        ];
        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertArrayHasKey('line_at_url', $_POST);
        $this->assertSame('', $_POST['line_at_url']);
        $this->assertSame([], $this->updates);
    }

    /**
     * 配列が送信されてもWarningを出さず既存値が保持されることを検証
     */
    public function test_配列が送信されてもWarningを出さず既存値を保持する(): void
    {
        $_POST = [
            '_wpnonce' => 'valid-nonce',
            'upladed_avatar' => ['不正な配列'],
            'line_at_url' => ['不正な配列'],
        ];

        $this->allow_profile_update(42);

        update_avatar_to_user_profile(42);

        $this->assertSame([], $this->updates);
        $this->assertArrayNotHasKey('line_at_url', $_POST);
    }

    /**
     * 標準プロフィールの権限・nonce検証を通し、更新内容をキーごとに記録する
     */
    private function allow_profile_update(int $user_id): void
    {
        Functions\when('current_user_can')
            ->alias(static function (string $capability, int $target_user_id) use ($user_id): bool {
                return $capability === 'edit_user' && $target_user_id === $user_id;
            });
        Functions\when('wp_verify_nonce')
            ->alias(static function (string $nonce, string $action) use ($user_id): bool {
                return $nonce === 'valid-nonce' && $action === 'update-user_' . $user_id;
            });
        Functions\when('update_user_meta')
            ->alias(function (int $target_user_id, string $key, string $value) use ($user_id): bool {
                if ($target_user_id === $user_id) {
                    $this->updates[$key] = $value;
                }

                return true;
            });
    }
}
