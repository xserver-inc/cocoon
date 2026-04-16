<?php
/**
 * shortcodes.php のユニットテスト
 *
 * ショートコード関連の純粋関数、正規表現ロジック、
 * パラメータ解析のテストを行います。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ShortcodesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // shortcodes.php が依存する定数を定義
        if (!defined('AFFI_SHORTCODE')) {
            define('AFFI_SHORTCODE', 'affi');
        }
        if (!defined('TIME_ERROR_MESSAGE')) {
            define('TIME_ERROR_MESSAGE', '日時入力エラー');
        }
        if (!defined('TOC_SHORTCODE_REG')) {
            define('TOC_SHORTCODE_REG', '/\[toc[^\]]*\]/i');
        }
        if (!defined('TOC_SHORTCODE_ERROR_MESSAGE')) {
            define('TOC_SHORTCODE_ERROR_MESSAGE', '目次ショートコードエラー');
        }

        require_once dirname(__DIR__, 2) . '/lib/shortcodes.php';
    }

    // ========================================================================
    // get_affiliate_tag_shortcode()
    // ========================================================================

    public function test_get_affiliate_tag_shortcode_正しいショートコード文字列を生成する(): void
    {
        $result = get_affiliate_tag_shortcode(123);
        $this->assertSame('[affi id=123]', $result);
    }

    public function test_get_affiliate_tag_shortcode_ID_0でも文字列を生成する(): void
    {
        $result = get_affiliate_tag_shortcode(0);
        $this->assertSame('[affi id=0]', $result);
    }

    // ========================================================================
    // get_function_text_shortcode()
    // ========================================================================

    public function test_get_function_text_shortcode_正しいショートコード文字列を生成する(): void
    {
        $result = get_function_text_shortcode(456);
        $this->assertSame('[temp id=456]', $result);
    }

    // ========================================================================
    // get_item_ranking_shortcode()
    // ========================================================================

    public function test_get_item_ranking_shortcode_正しいショートコード文字列を生成する(): void
    {
        $result = get_item_ranking_shortcode(789);
        $this->assertSame('[rank id=789]', $result);
    }

    // ========================================================================
    // remove_wrap_shortcode_wpautop()
    // ========================================================================

    public function test_remove_wrap_shortcode_wpautop_tiショートコードのみ抽出する(): void
    {
        $content = '<p>余計なテキスト</p>[ti title="手順1"]内容1[/ti]<p>余計なテキスト2</p>[ti title="手順2"]内容2[/ti]';
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertSame('[ti title="手順1"]内容1[/ti][ti title="手順2"]内容2[/ti]', $result);
    }

    public function test_remove_wrap_shortcode_wpautop_マッチしない場合空文字を返す(): void
    {
        $content = '<p>テキストのみ</p>';
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertSame('', $result);
    }

    public function test_remove_wrap_shortcode_wpautop_ネストしたコンテンツを処理する(): void
    {
        $content = '[ti title="テスト"]<p>ネストされた<strong>コンテンツ</strong></p>[/ti]';
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertSame('[ti title="テスト"]<p>ネストされた<strong>コンテンツ</strong></p>[/ti]', $result);
    }

    public function test_remove_wrap_shortcode_wpautop_改行を含むコンテンツを処理する(): void
    {
        $content = "[ti title=\"A\"]\n内容A\n[/ti]\n[ti title=\"B\"]\n内容B\n[/ti]";
        $result = remove_wrap_shortcode_wpautop('ti', $content);
        $this->assertNotNull($result);
        $this->assertStringContainsString('[ti title="A"]', $result);
        $this->assertStringContainsString('[ti title="B"]', $result);
    }

    // ========================================================================
    // html_shortcode()
    // ========================================================================

    public function test_html_shortcode_HTMLエンティティをデコードする(): void
    {
        $content = '&lt;div&gt;テスト&lt;/div&gt;';
        $result = html_shortcode([], $content);
        $this->assertSame('<div>テスト</div>', $result);
    }

    public function test_html_shortcode_クォートはデコードしない(): void
    {
        // ENT_NOQUOTES ではクォートはデコードされない
        $content = '&quot;テスト&quot;';
        $result = html_shortcode([], $content);
        $this->assertSame('&quot;テスト&quot;', $result);
    }

    public function test_html_shortcode_nullの場合空文字を返す(): void
    {
        $result = html_shortcode([], null);
        $this->assertSame('', $result);
    }

    // ========================================================================
    // shortcodes_to_exempt_from_wptexturize()
    // ========================================================================

    public function test_shortcodes_to_exempt_htmlを配列に追加する(): void
    {
        $shortcodes = ['code', 'pre'];
        $result = shortcodes_to_exempt_from_wptexturize($shortcodes);
        $this->assertContains('html', $result);
        $this->assertContains('code', $result);
        $this->assertCount(3, $result);
    }

    public function test_shortcodes_to_exempt_空配列にhtmlを追加する(): void
    {
        $result = shortcodes_to_exempt_from_wptexturize([]);
        $this->assertSame(['html'], $result);
    }

    // ========================================================================
    // get_countdown_days()
    // ========================================================================

    public function test_get_countdown_days_未来日までの日数を返す(): void
    {
        // 10日後のタイムスタンプ
        $to = time() + (86400 * 10);
        $result = get_countdown_days($to);
        $this->assertSame('10', $result);
    }

    public function test_get_countdown_days_過去日は0を返す(): void
    {
        // 5日前のタイムスタンプ
        $to = time() - (86400 * 5);
        $result = get_countdown_days($to);
        $this->assertSame('0', $result);
    }

    public function test_get_countdown_days_今日は1を返す(): void
    {
        // 12時間後 = ceil(0.5) = 1
        $to = time() + (43200);
        $result = get_countdown_days($to);
        $this->assertSame('1', $result);
    }

    // ========================================================================
    // campaign_shortcode()
    // ========================================================================

    /**
     * キャンペーン期間内の場合にコンテンツが表示されることをテスト
     */
    public function test_campaign_shortcode_期間内の場合コンテンツを表示する(): void
    {
        if (!defined('DAY_IN_SECONDS')) {
            define('DAY_IN_SECONDS', 86400);
        }

        $tz = new \DateTimeZone('Asia/Tokyo');
        $now = new \DateTimeImmutable('2026-04-10 15:00:00', $tz);

        \Brain\Monkey\Functions\when('wp_timezone')->justReturn($tz);
        \Brain\Monkey\Functions\when('current_datetime')->justReturn($now);

        $atts = [
            'from' => '2026-04-01',
            'to' => '2026-04-30',
            'class' => 'my-class'
        ];
        $content = 'テストコンテンツ';

        // apply_filtersのモック（そのまま返す）
        \Brain\Monkey\Filters\expectApplied('campaign_shortcode_content')->andReturn($content);

        $result = campaign_shortcode($atts, $content);
        $this->assertSame('<div class="campaign my-class">テストコンテンツ</div>', $result);
    }

    /**
     * キャンペーン期間外（終了後）の場合にnullが返されることをテスト
     */
    public function test_campaign_shortcode_期間外はnullを返す(): void
    {
        if (!defined('DAY_IN_SECONDS')) {
            define('DAY_IN_SECONDS', 86400);
        }

        $tz = new \DateTimeZone('Asia/Tokyo');
        $now = new \DateTimeImmutable('2026-05-01 15:00:00', $tz);

        \Brain\Monkey\Functions\when('wp_timezone')->justReturn($tz);
        \Brain\Monkey\Functions\when('current_datetime')->justReturn($now);

        $atts = [
            'from' => '2026-04-01',
            'to' => '2026-04-30',
        ];
        $content = 'テストコンテンツ';

        \Brain\Monkey\Filters\expectApplied('campaign_shortcode_content')->andReturn($content);

        $result = campaign_shortcode($atts, $content);
        $this->assertNull($result);
    }

    /**
     * 不正な日付フォーマットが渡された場合にFatal Errorとならずフォールバックされることをテスト
     */
    public function test_campaign_shortcode_不正な日付フォーマットの場合はフォールバックする(): void
    {
        if (!defined('DAY_IN_SECONDS')) {
            define('DAY_IN_SECONDS', 86400);
        }

        $tz = new \DateTimeZone('Asia/Tokyo');
        $now = new \DateTimeImmutable('2026-04-10 15:00:00', $tz);

        \Brain\Monkey\Functions\when('wp_timezone')->justReturn($tz);
        \Brain\Monkey\Functions\when('current_datetime')->justReturn($now);

        $atts = [
            'from' => 'invalid-date-format', // 不正な日付
            'to'   => 'invalid-date-format', // 不正な日付
        ];
        $content = 'テストコンテンツ';

        \Brain\Monkey\Filters\expectApplied('campaign_shortcode_content')->andReturn($content);

        // catchブロック内で $now_ts - DAY_IN_SECONDS と $now_ts + DAY_IN_SECONDS にフォールバックされるため、
        // 期間中と判定されてコンテンツが表示される想定
        $result = campaign_shortcode($atts, $content);
        $this->assertSame('<div class="campaign">テストコンテンツ</div>', $result);
    }

    // ========================================================================
    // login_user_only_shortcode()
    // ========================================================================

    /**
     * ログインユーザーの場合にコンテンツがそのまま評価されて表示されることをテスト
     */
    public function test_login_user_only_shortcode_ログイン時はコンテンツを表示する(): void
    {
        global $test_mock_is_user_logged_in;
        $test_mock_is_user_logged_in = true;

        $atts = [];
        $content = 'ログイン限定コンテンツ';

        $result = login_user_only_shortcode($atts, $content);
        $this->assertSame('ログイン限定コンテンツ', $result);
    }

    /**
     * 未ログインの場合に制限メッセージが表示されることをテスト
     */
    public function test_login_user_only_shortcode_未ログイン時はメッセージを表示する(): void
    {
        global $test_mock_is_user_logged_in;
        $test_mock_is_user_logged_in = false;

        // THEME_NAME 定数の回避のため、__()を使わずに固定値でテストするケースも考慮し、
        // msg属性を明示的に指定してテストする
        $atts = ['msg' => '制限されています'];
        $content = 'ログイン限定コンテンツ';

        $result = login_user_only_shortcode($atts, $content);
        $this->assertSame('<div class="login-user-only">制限されています</div>', $result);
    }
    /**
     * $contentにnullが渡された場合でもエラーにならず（空文字扱い）、例外なく処理されることをテスト
     */
    public function test_login_user_only_shortcode_nullコンテンツの場合もエラーにならない(): void
    {
        global $test_mock_is_user_logged_in;
        $test_mock_is_user_logged_in = true;

        $atts = [];
        $result = login_user_only_shortcode($atts, null);
        $this->assertSame('', $result);
    }

    // ========================================================================
    // timeline_shortcode()
    // ========================================================================

    public function test_timeline_shortcode_nullコンテンツの場合もエラーにならない(): void
    {
        $atts = ['title' => 'タイムライン'];
        $result = timeline_shortcode($atts, null);
        $this->assertStringContainsString('タイムライン', $result);
        $this->assertStringContainsString('timeline', $result);
    }

    // ========================================================================
    // timeline_item_shortcode()
    // ========================================================================

    public function test_timeline_item_shortcode_nullコンテンツの場合もエラーにならない(): void
    {
        $atts = ['title' => 'ステップ1', 'label' => '手順'];
        $result = timeline_item_shortcode($atts, null);
        $this->assertStringContainsString('ステップ1', $result);
        $this->assertStringContainsString('手順', $result);
        $this->assertStringContainsString('timeline-item', $result);
    }

    // ========================================================================
    // get_cta_tag()
    // ========================================================================

    public function test_get_cta_tag_nullコンテンツの場合もエラーにならない(): void
    {
        $atts = ['heading' => 'CTAタイトル'];
        
        \Brain\Monkey\Functions\when('get_template_part')->justReturn();
        
        // 内部で wpautop() やテンプレート呼び出しなどを通すが、
        // get_cta_tag() 自体が null を受け取っても警告が出ず安全に実行できるかテスト
        $result = get_cta_tag($atts, null);
        // get_template_partなどをモックしているため、出力はなく空文字として扱われる
        $this->assertSame('', $result);
    }
}

