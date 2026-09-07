<?php
/**
 * use_gutenberg_editor() の画面情報と旧 Gutenberg 互換判定を検証します。
 *
 * AJAX 処理などで get_current_screen() が null になってもエラーを起こさず、
 * 既存のブロックエディター判定と旧プラグインの判定を維持するための回帰テストです。
 *
 * @see https://wp-cocoon.com/community/postid/88990/
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

// 関数のモック定義を別のテストへ残さず、旧関数が未定義の環境も再現します。
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class UseGutenbergEditorTest extends TestCase
{
    public function test_画面情報がnullで旧関数も未定義ならfalseを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn(null);
        $this->assertFalse(function_exists('is_gutenberg_page'));

        // 管理画面情報がない AJAX リクエストでも TypeError を発生させません。
        $this->assertFalse(use_gutenberg_editor());
    }

    public function test_ブロックエディターなら旧判定を呼ばずtrueを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn($this->createScreen(true));

        // 画面情報だけで true が確定した場合は、従来どおり後半の判定を省略します。
        Functions\expect('is_gutenberg_page')->never();

        $this->assertTrue(use_gutenberg_editor());
    }

    public function test_旧関数が未定義でもブロックエディターならtrueを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn($this->createScreen(true));
        $this->assertFalse(function_exists('is_gutenberg_page'));

        // 通常のブロックエディターの判定は、旧 Gutenberg 関数の存在に依存させません。
        $this->assertTrue(use_gutenberg_editor());
    }

    public function test_クラシックエディターで旧判定もfalseならfalseを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn($this->createScreen(false));
        Functions\expect('is_gutenberg_page')->once()->andReturn(false);

        $this->assertFalse(use_gutenberg_editor());
    }

    public function test_クラシックエディターでも旧判定がtrueならtrueを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn($this->createScreen(false));
        Functions\expect('is_gutenberg_page')->once()->andReturn(true);

        $this->assertTrue(use_gutenberg_editor());
    }

    public function test_クラシックエディターで旧関数が未定義ならfalseを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn($this->createScreen(false));
        $this->assertFalse(function_exists('is_gutenberg_page'));

        $this->assertFalse(use_gutenberg_editor());
    }

    public function test_画面に判定メソッドがなく旧判定もfalseならfalseを返す(): void
    {
        // 古い WordPress の画面を想定し、is_block_editor() のないオブジェクトを渡します。
        Functions\expect('get_current_screen')->once()->andReturn(new \stdClass());
        Functions\expect('is_gutenberg_page')->once()->andReturn(false);

        $this->assertFalse(use_gutenberg_editor());
    }

    public function test_画面に判定メソッドがなくても旧判定がtrueならtrueを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn(new \stdClass());
        Functions\expect('is_gutenberg_page')->once()->andReturn(true);

        $this->assertTrue(use_gutenberg_editor());
    }

    public function test_画面情報がnullでも旧判定がtrueならtrueを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn(null);
        Functions\expect('is_gutenberg_page')->once()->andReturn(true);

        // null で即座に false を返す修正にすると、この互換判定が失われます。
        $this->assertTrue(use_gutenberg_editor());
    }

    public function test_画面情報がnullで旧判定もfalseならfalseを返す(): void
    {
        Functions\expect('get_current_screen')->once()->andReturn(null);
        Functions\expect('is_gutenberg_page')->once()->andReturn(false);

        $this->assertFalse(use_gutenberg_editor());
    }

    // WordPress 本体を読み込まず、画面オブジェクトの判定メソッドだけを再現します。
    private function createScreen(bool $isBlockEditor): object
    {
        return new class($isBlockEditor) {
            private bool $isBlockEditor;

            public function __construct(bool $isBlockEditor)
            {
                $this->isBlockEditor = $isBlockEditor;
            }

            public function is_block_editor(): bool
            {
                return $this->isBlockEditor;
            }
        };
    }
}
