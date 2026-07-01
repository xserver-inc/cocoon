<?php
/**
 * Xwrite_Notice_Manager ユニットテスト
 *
 * 対象: lib/dashboard-message.php (Xwrite_Notice_Manager)
 *
 * テスト方針:
 * - display_notice() の各ガード条件が正しく機能するか（出力なしで終了）
 * - calculate_phase_index() のコアロジックを ReflectionMethod で直接検証
 *
 * @see lib/dashboard-message.php
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;
use Brain\Monkey\Functions;

class XwriteNoticeManagerTest extends TestCase
{
    /** クラスファイルの多重読み込みを防ぐフラグ */
    private static bool $loaded = false;

    /** テスト用JSONデータ（3フェーズ構成） */
    private array $sample_data;

    // ====================================================================
    // テストクラスのセットアップ
    // ====================================================================

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // dashboard-message.php が使う WordPress 時間定数を事前定義
        if ( ! defined( 'DAY_IN_SECONDS' ) ) {
            define( 'DAY_IN_SECONDS', 86400 );
        }
        if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
            define( 'HOUR_IN_SECONDS', 3600 );
        }

        if ( ! self::$loaded ) {
            require_once dirname( __DIR__, 2 ) . '/lib/dashboard-message.php';
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 各テストで最低限必要なスタブ（呼ばれると undefined function になるもの）
        // ※ get_bloginfo は wp-mock-functions.php で定義済みのため Brain\Monkey では登録しない
        Functions\when( 'get_current_user_id' )->justReturn( 1 );
        Functions\when( 'get_transient' )->justReturn( false );
        Functions\when( 'set_transient' )->justReturn( true );
        Functions\when( 'update_user_meta' )->justReturn( true );

        // テスト用サンプルJSON（3フェーズ構成）
        $this->sample_data = [
            'global_settings' => [
                'is_active'  => true,
                'thresholds' => [
                    'new_site_days'      => 30,
                    'silent_period_days' => 30,
                    'cool_off_days'      => 30,
                ],
                'defaults'   => [
                    'conditions'    => [
                        'min_posts'       => 0,
                        'duration_months' => 3,
                    ],
                    'type'          => 'info',
                    'dismiss_label' => '通知を表示しない',
                    'action_labels' => [
                        'primary'   => '詳細を見る',
                        'secondary' => '後で確認',
                    ],
                ],
            ],
            'delivery_phases' => [
                [
                    'conditions' => [ 'min_posts' => 0,  'duration_months' => 3 ],
                    'content'    => [ 'text' => 'フェーズ0', 'type' => 'info', 'urls' => [], 'dismiss_label' => '表示しない' ],
                ],
                [
                    'conditions' => [ 'min_posts' => 10, 'duration_months' => 3 ],
                    'content'    => [ 'text' => 'フェーズ1', 'type' => 'info', 'urls' => [], 'dismiss_label' => '表示しない' ],
                ],
                [
                    'conditions' => [ 'min_posts' => 30 ],
                    'content'    => [ 'text' => 'フェーズ2', 'type' => 'info', 'urls' => [], 'dismiss_label' => '表示しない' ],
                ],
            ],
        ];
    }

    // ====================================================================
    // display_notice() — ガード条件テスト
    // ====================================================================

    /**
     * manage_options 権限がないユーザーには何も出力しない
     * TestCase のデフォルトで current_user_can は false を返す
     */
    public function test_display_notice_管理者権限がない場合は何も出力しない(): void
    {
        // TestCase のデフォルトで current_user_can → false → 即 return
        $instance = new \Xwrite_Notice_Manager();

        ob_start();
        $instance->display_notice();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    /**
     * get_current_screen() が null を返す場合は何も出力しない
     */
    public function test_display_notice_current_screenがnullなら何も出力しない(): void
    {
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\when( 'get_current_screen' )->justReturn( null );

        $instance = new \Xwrite_Notice_Manager();

        ob_start();
        $instance->display_notice();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    /**
     * 許可リスト外のスクリーン（例: WooCommerce注文一覧）では何も出力しない
     */
    public function test_display_notice_許可スクリーン外では何も出力しない(): void
    {
        Functions\when( 'current_user_can' )->justReturn( true );
        // 許可リストに含まれない画面ID
        Functions\when( 'get_current_screen' )->justReturn( (object)[ 'id' => 'edit-shop_order' ] );

        $instance = new \Xwrite_Notice_Manager();

        ob_start();
        $instance->display_notice();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    /**
     * graduated フラグが true のユーザーには何も出力しない
     * （最後のフェーズを非表示にした後、全フェーズ表示完了とみなされた状態）
     */
    public function test_display_notice_graduatedユーザーには何も出力しない(): void
    {
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\when( 'get_current_screen' )->justReturn( (object)[ 'id' => 'dashboard' ] );
        Functions\when( 'get_transient' )->justReturn( $this->sample_data );
        // graduated フラグが true のユーザーを模擬
        Functions\when( 'get_user_meta' )->alias( function ( $user_id, $key, $single ) {
            if ( $key === 'xwrite_notice_graduated' ) return true;
            return '';
        } );

        $instance = new \Xwrite_Notice_Manager();

        ob_start();
        $instance->display_notice();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    /**
     * is_active = false のJSONが返ってきた場合は何も出力しない
     * （JSONサーバー側でキャンペーンを停止した状態）
     */
    public function test_display_notice_is_active_falseのとき何も出力しない(): void
    {
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\when( 'get_current_screen' )->justReturn( (object)[ 'id' => 'dashboard' ] );

        $inactive_data                                          = $this->sample_data;
        $inactive_data['global_settings']['is_active']         = false;
        Functions\when( 'get_transient' )->justReturn( $inactive_data );
        Functions\when( 'get_user_meta' )->alias( function ( $user_id, $key, $single ) {
            if ( $key === 'xwrite_notice_graduated' ) return false;
            return '';
        } );

        $instance = new \Xwrite_Notice_Manager();

        ob_start();
        $instance->display_notice();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    /**
     * 冷却期間中（dismissed_at が 1 時間前）は何も出力しない
     * cool_off_days = 30 なので、1時間前の dismiss は冷却期間内
     */
    public function test_display_notice_冷却期間中は何も出力しない(): void
    {
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\when( 'get_current_screen' )->justReturn( (object)[ 'id' => 'dashboard' ] );
        Functions\when( 'get_transient' )->justReturn( $this->sample_data );
        Functions\when( 'get_user_meta' )->alias( function ( $user_id, $key, $single ) {
            if ( $key === 'xwrite_notice_graduated' )    return false;
            if ( $key === 'xwrite_notice_dismissed_at' ) return time() - HOUR_IN_SECONDS; // 1時間前
            return '';
        } );

        $instance = new \Xwrite_Notice_Manager();

        ob_start();
        $instance->display_notice();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    // ====================================================================
    // calculate_phase_index() — フェーズ計算ロジック
    // ReflectionMethod を使ってプライベートメソッドを直接テスト
    // ====================================================================

    /**
     * get_userdata() が false を返す場合は -1 を返す
     * （存在しないユーザーIDや取得失敗時のフォールバック）
     */
    public function test_calculate_phase_index_userdataが取得できない場合は_1を返す(): void
    {
        Functions\when( 'get_userdata' )->justReturn( false );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $result = $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        $this->assertSame( -1, $result );
    }

    /**
     * サイレント期間内（登録から 10 日）のユーザーは -1 を返す
     * new_site_days(30) 未満なので base_index=0 がセットされるが、
     * silent_period_days(30) を満たしていないため即 -1
     */
    public function test_calculate_phase_index_サイレント期間中は_1を返す(): void
    {
        $userdata = (object)[ 'user_registered' => date( 'Y-m-d H:i:s', time() - 10 * DAY_IN_SECONDS ) ];
        Functions\when( 'get_userdata' )->justReturn( $userdata );
        Functions\when( 'get_user_meta' )->justReturn( '' ); // base_index 未設定
        Functions\when( 'wp_count_posts' )->justReturn( (object)[ 'publish' => 0 ] );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $result = $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        $this->assertSame( -1, $result );
    }

    /**
     * 新規サイト（new_site_days 未満）は記事数が多くても base_index=0 を選ぶ
     * update_user_meta へ渡された値をキャプチャして検証する
     */
    public function test_calculate_phase_index_新規サイトは記事数によらずbase_index_0を選ぶ(): void
    {
        // 10 日前に登録 → new_site_days(30) 未満
        $userdata = (object)[ 'user_registered' => date( 'Y-m-d H:i:s', time() - 10 * DAY_IN_SECONDS ) ];
        Functions\when( 'get_userdata' )->justReturn( $userdata );
        Functions\when( 'get_user_meta' )->justReturn( '' ); // base_index 未設定
        Functions\when( 'wp_count_posts' )->justReturn( (object)[ 'publish' => 100 ] ); // 記事100件でも

        $captured_base_index = null;
        Functions\when( 'update_user_meta' )->alias(
            function ( $uid, $key, $value ) use ( &$captured_base_index ) {
                if ( $key === 'xwrite_notice_initial_base_index' ) {
                    $captured_base_index = $value;
                }
                return true;
            }
        );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        $this->assertSame( 0, $captured_base_index, '新規サイト(new_site_days未満)は記事数に関わらず base_index=0 を選ぶ' );
    }

    /**
     * サイレント期間を過ぎた既存サイトで記事 0 件 → フェーズ0 を返す
     * 経過月数が duration_months(3) 未満なのでステップアップなし
     */
    public function test_calculate_phase_index_既存サイト記事0件でフェーズ0を返す(): void
    {
        // 50 日前に登録 → new_site_days(30) 超、silent_period_days(30) 超
        $userdata = (object)[ 'user_registered' => date( 'Y-m-d H:i:s', time() - 50 * DAY_IN_SECONDS ) ];
        Functions\when( 'get_userdata' )->justReturn( $userdata );
        Functions\when( 'get_user_meta' )->justReturn( '' ); // base_index 未設定
        Functions\when( 'wp_count_posts' )->justReturn( (object)[ 'publish' => 0 ] );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $result = $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        $this->assertSame( 0, $result );
    }

    /**
     * 既存サイトで記事 10 件（sample_data の phase[1].min_posts=10 に達する）
     * → base_index=1 から開始し、経過月数が duration_months 未満 → フェーズ1 を返す
     */
    public function test_calculate_phase_index_記事10件でフェーズ1を返す(): void
    {
        // 50 日前に登録
        $userdata = (object)[ 'user_registered' => date( 'Y-m-d H:i:s', time() - 50 * DAY_IN_SECONDS ) ];
        Functions\when( 'get_userdata' )->justReturn( $userdata );
        Functions\when( 'get_user_meta' )->justReturn( '' ); // base_index 未設定
        Functions\when( 'wp_count_posts' )->justReturn( (object)[ 'publish' => 10 ] );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $result = $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        $this->assertSame( 1, $result );
    }

    /**
     * base_index=0 / start_month=0 で経過 2 ヶ月 → duration_months(3) 未満
     * → ステップアップなしでフェーズ0 のまま
     */
    public function test_calculate_phase_index_2ヶ月経過ではステップアップしない(): void
    {
        // 60 日前に登録 → 60/30 = 2 ヶ月経過
        $userdata = (object)[ 'user_registered' => date( 'Y-m-d H:i:s', time() - 60 * DAY_IN_SECONDS ) ];
        Functions\when( 'get_userdata' )->justReturn( $userdata );
        Functions\when( 'get_user_meta' )->alias( function ( $uid, $key, $single ) {
            if ( $key === 'xwrite_notice_initial_base_index' ) return '0'; // base_index 設定済み
            if ( $key === 'xwrite_notice_initial_base_month' ) return '0'; // 月0 から開始
            return '';
        } );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $result = $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        // 2 ヶ月 < duration_months(3) → ステップアップなし → フェーズ0
        $this->assertSame( 0, $result );
    }

    /**
     * base_index=0 / start_month=0 で経過 6.5 ヶ月（195 日）→ 2 回ステップアップしフェーズ2
     * 各フェーズ duration_months=3 なので: 0→1(3ヶ月), 1→2(6ヶ月) でフェーズ2 に到達
     */
    public function test_calculate_phase_index_6ヶ月超経過で2回ステップアップしフェーズ2を返す(): void
    {
        // 195 日前に登録 → 195/30 ≒ 6.5 ヶ月経過
        $userdata = (object)[ 'user_registered' => date( 'Y-m-d H:i:s', time() - 195 * DAY_IN_SECONDS ) ];
        Functions\when( 'get_userdata' )->justReturn( $userdata );
        Functions\when( 'get_user_meta' )->alias( function ( $uid, $key, $single ) {
            if ( $key === 'xwrite_notice_initial_base_index' ) return '0'; // base_index 設定済み
            if ( $key === 'xwrite_notice_initial_base_month' ) return '0'; // 月0 から開始
            return '';
        } );

        $ref = new \ReflectionMethod( \Xwrite_Notice_Manager::class, 'calculate_phase_index' );

        $result = $ref->invoke( new \Xwrite_Notice_Manager(), 1, $this->sample_data );

        // 6.5 ヶ月 >= 3 → フェーズ1、6.5 ヶ月 >= 6 → フェーズ2（最終フェーズ）
        $this->assertSame( 2, $result );
    }
}
