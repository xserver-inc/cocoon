<?php
/**
 * コードベース堅牢化のリグレッションテスト
 *
 * 今回の修正セッションで行った以下の変更が将来壊されないことを保証します：
 * 1. COCOON_RAKUTEN_API_VERSION 定数のリネーム・統一
 * 2. blogcard-out.php / blogcard-in.php での Punycode デコード方針
 * 3. blogcard-out.php での OGP url プロパティのコピー
 * 4. blogcard-out.php での function_exists 安全チェック
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class HardeningRegressionTest extends TestCase
{
    // ========================================================================
    // COCOON_RAKUTEN_API_VERSION — 定数リネーム
    // 旧名 RAKUTEN_API_VERSION がコードベースに残っていないことを保証する
    // ========================================================================

    /**
     * @dataProvider 楽天API定数使用ファイルプロバイダ
     */
    public function test_楽天API定数は全箇所でCOCOON接頭辞付きを使用している(string $relativePath): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/' . $relativePath);

        // 旧名 RAKUTEN_API_VERSION（COCOON_ なし）が使われていないことを確認
        // ただし COCOON_RAKUTEN_API_VERSION 内に含まれる部分一致は除外するため、
        // 否定先読みで COCOON_ プレフィックスが付いていないものだけを検出する
        $matches = [];
        preg_match_all('/(?<!COCOON_)RAKUTEN_API_VERSION/', $file, $matches);
        $this->assertCount(0, $matches[0],
            "$relativePath に旧名 RAKUTEN_API_VERSION（COCOON_ プレフィックスなし）が残っています");
    }

    public static function 楽天API定数使用ファイルプロバイダ(): array
    {
        return [
            '_defins.php' => ['lib/_defins.php'],
            'shortcodes-rakuten.php' => ['lib/shortcodes-rakuten.php'],
            'block-rakuten-product-link.php' => ['lib/block-rakuten-product-link.php'],
            'init.php (blocks)' => ['blocks/src/init.php'],
        ];
    }

    public function test_COCOON_RAKUTEN_API_VERSIONはdefinedガード付きで定義されている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/_defins.php');

        // !defined() チェックがあること
        $this->assertStringContainsString(
            "!defined('COCOON_RAKUTEN_API_VERSION')",
            $file,
            'COCOON_RAKUTEN_API_VERSION の定義に !defined() ガードが必要です'
        );
    }

    // ========================================================================
    // blogcard-out.php — OGP url プロパティのコピー
    // ========================================================================

    public function test_blogcard_outのOGPコピー処理にurlプロパティが含まれている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // $ogp->url への代入があることを確認
        $this->assertMatchesRegularExpression(
            '/\$ogp\s*->\s*url\s*=/',
            $file,
            'blogcard-out.php の OGP コピー処理に $ogp->url の代入が必要です（og:url のコピー漏れ防止）'
        );
    }

    public function test_blogcard_outのdurl変数にpunycode_decodeが適用されている(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // $durl に punycode_decode が使われていること（フルURL用ラッパーの正しい使い方）
        $this->assertMatchesRegularExpression(
            '/\$durl\s*=.*punycode_decode\s*\(/',
            $file,
            '$durl にはフルURL用ラッパー punycode_decode() を使用する必要があります'
        );
    }

    public function test_blogcard_outのdurl割り当てにfunction_existsガードがある(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // function_exists ガード付きで punycode_decode を呼んでいること
        $this->assertStringContainsString(
            "function_exists('punycode_decode')",
            $file,
            'punycode_decode() の呼び出しには function_exists ガードが必要です'
        );
    }

    // ========================================================================
    // blogcard-in.php — Punycode デコード方針
    // ========================================================================

    public function test_blogcard_inのドメインデコードにPunycodeクラスを直接使用している(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-in.php');

        // ホスト名単体には Punycode クラスを直接使用すること
        $this->assertStringContainsString(
            'new Punycode()',
            $file,
            'blogcard-in.php のドメインデコードには Punycode::decode() を直接使用する必要があります'
        );
    }

    public function test_blogcard_inのドメイン取得にフルURL版punycode_decodeを使っていない(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-in.php');

        // blogcard-in.php では $domain に対してのデコードは Punycode::decode() を使うべき
        // punycode_decode($url) → get_domain_name() という旧パターンが残っていないことを確認
        $this->assertDoesNotMatchRegularExpression(
            '/get_domain_name\s*\(\s*punycode_decode\s*\(/',
            $file,
            'blogcard-in.php で get_domain_name(punycode_decode($url)) パターンを使ってはいけません。' .
            'ドメイン抽出後に Punycode::decode() を適用してください。'
        );
    }

    // ========================================================================
    // utils.php — is_current_url_same() の Punycode デコード方針
    // ========================================================================

    public function test_is_current_url_sameのホスト比較にPunycodeクラスを直接使用している(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/utils.php');

        // is_current_url_same 関数内で Punycode クラスを使っていること
        // 関数の範囲を正規表現で大まかに特定
        $funcStart = strpos($file, "function is_current_url_same");
        $funcEnd = strpos($file, "endif;", $funcStart);
        $funcBody = substr($file, $funcStart, $funcEnd - $funcStart);

        $this->assertStringContainsString(
            'new Punycode()',
            $funcBody,
            'is_current_url_same() 内のホスト比較には Punycode::decode() を直接使用する必要があります'
        );
    }

    public function test_is_current_url_sameにフルURL版punycode_decodeを使っていない(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/utils.php');

        $funcStart = strpos($file, "function is_current_url_same");
        $funcEnd = strpos($file, "endif;", $funcStart);
        $funcBody = substr($file, $funcStart, $funcEnd - $funcStart);

        // punycode_decode() ラッパーの直接呼び出しが残っていないこと
        // （コメントは許容するため、実際の関数呼び出しパターンのみを検出）
        $this->assertDoesNotMatchRegularExpression(
            '/[^\/\*]\s*punycode_decode\s*\(\s*\$/',
            $funcBody,
            'is_current_url_same() 内でホスト名に punycode_decode() ラッパーを使ってはいけません。' .
            'Punycode::decode() を直接使用してください。'
        );
    }

    // ========================================================================
    // blogcard-out.php — function_exists 安全チェック
    // ========================================================================

    public function test_blogcard_outのREST判定にfunction_existsガードがある(): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/lib/blogcard-out.php');

        // is_rest() の呼び出しに function_exists ガードがあること
        $this->assertStringContainsString(
            "function_exists('is_rest')",
            $file,
            'blogcard-out.php の REST判定に function_exists ガードが必要です'
        );

        // is_external_blogcard_refresh_mode の呼び出しにガードがあること
        $this->assertStringContainsString(
            "function_exists('is_external_blogcard_refresh_mode')",
            $file,
            'is_external_blogcard_refresh_mode() に function_exists ガードが必要です'
        );

        // is_user_administrator の呼び出しにガードがあること
        $this->assertStringContainsString(
            "function_exists('is_user_administrator')",
            $file,
            'is_user_administrator() に function_exists ガードが必要です'
        );
    }

    // ========================================================================
    // Cron ファイル — $wpdb->update の注意コメント
    // ========================================================================

    /**
     * @dataProvider cronファイルプロバイダ
     */
    public function test_cronファイルにwpdb_updateの注意コメントがある(string $relativePath): void
    {
        $file = file_get_contents(dirname(__DIR__, 2) . '/' . $relativePath);

        // wp_update_post を介さない注意が記載されていること
        $this->assertStringContainsString(
            'wp_update_post',
            $file,
            "$relativePath に \$wpdb->update の注意コメント（wp_update_post を介さない旨）が必要です"
        );
    }

    public static function cronファイルプロバイダ(): array
    {
        return [
            'Amazon Cron' => ['lib/block-amazon-product-link-cron.php'],
            'Rakuten Cron' => ['lib/block-rakuten-product-link-cron.php'],
        ];
    }
}
