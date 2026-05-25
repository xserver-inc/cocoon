<?php
/**
 * gutenberg_stylesheets_custom() のリグレッションテスト
 *
 * コミット 42297e80a7f5d77550041055e3cbdb7736da3ea3 の修正
 * （ブロックエディタのサイドバープレビューにスキンスタイルが反映されない不具合修正）が
 * 誤って元に戻されたときに検知するためのテストです。
 *
 * 修正前: !use_gutenberg_editor() 条件で囲まれていたため、ブロックエディタ使用時に
 *         カラーパレット・スキン・カスタムスタイルが wp_enqueue_style() で登録されなかった。
 * 修正後: 常に wp_enqueue_style() で登録される。
 *
 * settings.php はファイルレベルで多数の WordPress 関数を呼び出すため、
 * ソースコードの構造を直接検証する静的テストとして実装しています。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class GutenbergStylesheetsTest extends TestCase
{
    /** settings.php の gutenberg_stylesheets_custom 関数本体（抽出済み） */
    private static string $funcBody = '';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $source = file_get_contents(dirname(__DIR__, 2) . '/lib/settings.php');

        // gutenberg_stylesheets_custom 関数の本体を抽出する
        if (preg_match('/function gutenberg_stylesheets_custom\(\).*?^endif;/ms', $source, $matches)) {
            self::$funcBody = $matches[0];
        }
    }

    // ========================================================================
    // gutenberg_stylesheets_custom() — ブロックエディタ使用時のリグレッションテスト
    //
    // 以下のテストは「is_wp_5_8_or_over() ブロック内の wp_enqueue_style 呼び出しが
    // use_gutenberg_editor() 条件に囲まれていないこと」をソースコードから直接検証します。
    // 誰かが !use_gutenberg_editor() 条件を再追加した場合、これらのテストは FAIL します。
    // ========================================================================

    public function test_gutenberg_stylesheets_custom関数が定義されている(): void
    {
        $this->assertNotEmpty(
            self::$funcBody,
            'gutenberg_stylesheets_custom 関数が lib/settings.php に見つかりません'
        );
    }

    /**
     * カラーパレットスタイルの enqueue が use_gutenberg_editor 条件に囲まれていないことを確認。
     *
     * 誰かが以下のような条件を再追加した場合にこのテストは FAIL する:
     *   if ( !use_gutenberg_editor() ) {
     *     wp_enqueue_style( THEME_NAME . '-color-palette-style', ... );
     *   }
     */
    public function test_カラーパレットスタイル登録がuse_gutenberg_editor条件に囲まれていない(): void
    {
        $this->assertEnqueueNotWrappedInUseGutenbergEditor(
            "-color-palette-style'",
            'カラーパレットスタイル'
        );
    }

    /**
     * スキンスタイルの enqueue が use_gutenberg_editor 条件に囲まれていないことを確認。
     */
    public function test_スキンスタイル登録がuse_gutenberg_editor条件に囲まれていない(): void
    {
        $this->assertEnqueueNotWrappedInUseGutenbergEditor(
            "-skin-style'",
            'スキンスタイル'
        );
    }

    /**
     * カスタムスタイルキャッシュの enqueue が use_gutenberg_editor 条件に囲まれていないことを確認。
     */
    public function test_カスタムスタイル登録がuse_gutenberg_editor条件に囲まれていない(): void
    {
        $this->assertEnqueueNotWrappedInUseGutenbergEditor(
            "-css-cache-style'",
            'カスタムスタイルキャッシュ'
        );
    }

    // ========================================================================
    // ヘルパー
    // ========================================================================

    /**
     * 指定ハンドル文字列を含む wp_enqueue_style() 呼び出しが、
     * use_gutenberg_editor を条件とする if ブロック内に存在しないことを検証する。
     *
     * 「use_gutenberg_editor を含む if 文」と「対象の wp_enqueue_style 呼び出し」の間に
     * 同じか深いインデントレベルの閉じ括弧 "}" が存在すれば、条件ブロックは閉じている
     * （＝条件に囲まれていない）と判断する。
     */
    private function assertEnqueueNotWrappedInUseGutenbergEditor(
        string $handleFragment,
        string $styleName
    ): void {
        $body = self::$funcBody;

        // 対象の wp_enqueue_style 行が関数本体に存在することを前提確認
        $enqueuePattern = '/wp_enqueue_style\s*\([^)]*' . preg_quote($handleFragment, '/') . '/';
        $this->assertMatchesRegularExpression(
            $enqueuePattern,
            $body,
            "{$styleName} の wp_enqueue_style 呼び出しが gutenberg_stylesheets_custom に見つかりません"
        );

        // use_gutenberg_editor を条件とする if 文と対象 enqueue の相対位置を検査する。
        // use_gutenberg_editor を含む if 文が存在しない場合はそのままパスする（条件なし = 修正済み）。
        if (!preg_match('/if\s*\([^)]*use_gutenberg_editor[^)]*\)\s*\{/', $body, $ifMatch, PREG_OFFSET_CAPTURE)) {
            // use_gutenberg_editor の if 文自体が存在しない → 条件は存在しない
            $this->assertTrue(true);
            return;
        }

        $ifPos      = $ifMatch[0][1];
        $ifStr      = $ifMatch[0][0];
        $afterIf    = substr($body, $ifPos + strlen($ifStr));

        // if ブロックの終端 "}" の位置を追跡する（ネスト深度を考慮）
        $depth    = 1;
        $closePos = 0;
        $len      = strlen($afterIf);
        for ($i = 0; $i < $len; $i++) {
            if ($afterIf[$i] === '{') {
                $depth++;
            } elseif ($afterIf[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    $closePos = $ifPos + strlen($ifStr) + $i;
                    break;
                }
            }
        }

        // 対象 enqueue の位置
        preg_match($enqueuePattern, $body, $enqueueMatch, PREG_OFFSET_CAPTURE);
        $enqueuePos = $enqueueMatch[0][1];

        // enqueue が if ブロックの外（closePos 以降）にあるか確認
        $this->assertGreaterThan(
            $closePos,
            $enqueuePos,
            "{$styleName} の wp_enqueue_style が !use_gutenberg_editor() 条件ブロック内に" .
            "あります。この条件を削除してブロックエディタ時も常に enqueue してください。" .
            "（コミット 42297e80 参照）"
        );
    }
}
