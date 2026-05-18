<?php //管理画面上部に表示するメッセージ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 管理画面上部にXwriteのメッセージを表示する
class Xwrite_Notice_Manager {

  private $json_url = 'https://im-cocoon.net/api/cocoon-to-xwrite/xwrite-info.json';
  private $transient_key = 'xwrite_notice_data';
  private $meta_key_dismissed = 'xwrite_notice_dismissed_at';
  private $meta_key_graduated = 'xwrite_notice_graduated';
  private $meta_key_last_dismissed = 'xwrite_notice_last_dismissed_phase';
  // 初回フェーズ判定用のメタキー（xwrite_notice_* プレフィックスに統一）
  private $meta_key_base_index = 'xwrite_notice_initial_base_index';
  private $meta_key_base_month = 'xwrite_notice_initial_base_month';

  public function __construct() {
    // dismiss処理はHTMLヘッダー出力前のフックで実行（wp_safe_redirect を確実に動作させるため）
    add_action( 'admin_init', array( $this, 'handle_dismiss' ) );
    // 通知の表示は admin_notices で実行
    add_action( 'admin_notices', array( $this, 'display_notice' ), 5 );
  }

  /**
   * 「通知を表示しない」ボタン押下時の処理
   * admin_init フックで実行することで、HTML出力前にリダイレクトを確実に行う
   */
  public function handle_dismiss() {

    // 管理者権限がないユーザーは処理しない
    if ( ! current_user_can( 'manage_options' ) ) return;

    // xwrite_dismiss パラメータがなければ何もしない
    if ( ! isset( $_GET['xwrite_dismiss'] ) ) return;

    // Nonce を検証（失敗時は wp_die せず静かに終了する）
    if ( ! check_admin_referer( 'xwrite_dismiss_action' ) ) return;

    $user_id = get_current_user_id();

    // dismiss 処理に必要なフェーズ数を取得するためJSONデータを取得
    $data = $this->get_json_data();

    // 現在のフェーズを取得（URLから受け取る）
    $clicked_index = isset( $_GET['phase_index'] ) ? intval( $_GET['phase_index'] ) : 0;

    // 冷却期間の保存（一律30日間隠すためのスタンプ）
    update_user_meta( $user_id, $this->meta_key_dismissed, time() );

    // 最後に非表示にしたフェーズ番号を保存
    update_user_meta( $user_id, $this->meta_key_last_dismissed, $clicked_index );

    // JSONの中の最後のフェーズインデックスを取得して判定
    if ( $data && ! empty( $data['delivery_phases'] ) ) {
      $last_index = count( $data['delivery_phases'] ) - 1;
      if ( $last_index === $clicked_index ) {
        update_user_meta( $user_id, $this->meta_key_graduated, true );
      }
    }

    // パラメータを掃除してページをリロード
    wp_safe_redirect( remove_query_arg( array( 'xwrite_dismiss', 'phase_index', '_wpnonce' ) ) );
    exit;
  }

  /**
   * 通知の表示判定と出力
   */
  public function display_notice() {

    // 管理者権限がないユーザーには何も表示しない
    if ( ! current_user_can( 'manage_options' ) ) return;

    // 表示を許可するページを限定する
    $screen = get_current_screen();
    if ( ! $screen ) return;

    $allowed_screens = array(
      'dashboard',     // ダッシュボード
      'edit-post',     // 投稿一覧
      'edit-page',     // 固定ページ一覧
      'upload',        // メディアライブラリ
      'plugins',       // プラグイン一覧
      'users',         // ユーザー一覧
      'themes',        // テーマ一覧
      'nav-menus',     // メニュー
      'widgets',       // ウィジェット
      'options-general', // 設定：一般
      'toplevel_page_theme-settings' // Cocoon設定
    );

    // 現在の画面がリストに含まれているか、または独自のページかチェック
    $is_allowed = in_array( $screen->id, $allowed_screens );

    // もし許可されたページ以外なら、ここで即座に終了
    if ( ! $is_allowed ) return;

    $user_id = get_current_user_id();

    // 【データ取得】外部サーバーからJSONデータを取得（失敗したら終了）
    $data = $this->get_json_data();
    if ( ! $data || empty($data['delivery_phases']) ) return;

    // 案3を非表示にした人はここで即座に終了
    if ( get_user_meta( $user_id, $this->meta_key_graduated, true ) ) return;

    // 通知を行うかどうかの判定 (JSON構造 v1.1.0対応)
    if ( isset($data['global_settings']['is_active']) && ! $data['global_settings']['is_active'] ) {
      return;
    }

    // 冷却期間（「表示しない」を押してから30日間など）をチェック
    $dismissed_at = get_user_meta( $user_id, $this->meta_key_dismissed, true );
    $cool_off = $data['global_settings']['thresholds']['cool_off_days'] ?? 30; // JSONから設定取得
    if ( $dismissed_at && ( time() - $dismissed_at ) < ( $cool_off * DAY_IN_SECONDS ) ) {
      return;
    }

    // 現在のユーザーがどのフェーズ（0〜）に該当するか計算
    $phase_index = $this->calculate_phase_index( $user_id, $data );
    if ( $phase_index === -1 ) return;

    // もし現在のフェーズが、以前非表示にしたフェーズと同じ（またはそれ以下）なら表示しない
    $last_dismissed = get_user_meta( $user_id, $this->meta_key_last_dismissed, true );
    if ( $last_dismissed !== '' && $phase_index <= (int)$last_dismissed ) {
      return;
    }

    // 該当フェーズのメッセージを取得
    $target_phase = $data['delivery_phases'][$phase_index] ?? null;
    if ( ! $target_phase ) return;

    $defaults = $data['global_settings']['defaults'] ?? array();

    // --- ここからHTML出力準備 ---

    // セキュリティ用の「鍵（Nonce）」付き消去URLを作成
    $dismiss_url = wp_nonce_url(
      add_query_arg( array(
        'xwrite_dismiss' => '1',
        'phase_index'    => $phase_index // 現在のインデックスをURLに載せる
      ) ),
      'xwrite_dismiss_action'
    );

    // リンク間の区切り線（セパレーター）のHTML
    $sep = '<span style="margin: 0 8px; color: #ccc;">|</span>';

    // JSONから表示内容を取得（個別設定がなければデフォルトを採用）
    $text          = $target_phase['content']['text'] ?? '';
    $notice_type   = $target_phase['content']['type'] ?? $defaults['type'] ?? 'info';
    $dismiss_label = $target_phase['content']['dismiss_label'] ?? $defaults['dismiss_label'] ?? '通知を表示しない';

    // JSONのデフォルト設定からラベルを取得
    $action_labels = $defaults['action_labels'] ?? array();
    // 各フェーズ固有のURLを取得
    $urls = $target_phase['content']['urls'] ?? array();

    $notice_class = 'notice notice-' . esc_attr( $notice_type ) . ' is-dismissible';
    ?>

    <div class="<?php echo $notice_class; ?>" style="padding: 12px; border-left-width: 4px !important;">

      <p style="margin: 0 0 4px 0; font-size: 13px; font-weight: bold; color: #333;">
        <?php echo wp_kses_post( $text ); ?>
      </p>

      <p style="margin: 0; font-size: 13px;">

        <?php
        // アクションボタンをループで出力
        if ( ! empty( $urls ) ) {
          foreach ( $urls as $key => $url ) {
            // primary や secondary に対応するラベルを defaults から取得
            $label = $action_labels[$key] ?? '';

            if ( ! empty( $url ) && ! empty( $label ) ) {
              // target="_blank" に対して念のため rel="noopener noreferrer" を付与
              echo '<a href="' . esc_url( $url ) . '" style="text-decoration: none;" target="_blank" rel="noopener noreferrer">' . esc_html( $label ) . '</a>';
              echo $sep;
            }
          }
        }
        ?>

        <a href="<?php echo esc_url( $dismiss_url ); ?>" style="text-decoration: none;">
          <?php echo esc_html( $dismiss_label ); ?>
        </a>

      </p>
    </div>
    <?php
  }

  /**
   * データの取得とキャッシュ (Transient)
   */
  private function get_json_data() {
    // 1. キャッシュチェック
    $data = get_transient( $this->transient_key );
    if ( false !== $data ) return $data;

    // 2. 通信実行（標準的な設定）
    $args = array(
      'timeout'   => 10,
      'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
    );

    $response = wp_remote_get( $this->json_url, $args );

    // 3. 通信エラー時は静かに終了（ユーザーにエラーを見せない）
    if ( is_wp_error( $response ) ) return false;

    // 4. ステータスコードが200(OK)以外なら終了
    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $code ) return false;

    // 5. ボディの取得とデコード
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    // 6. JSONが正常なら12時間キャッシュに保存
    if ( is_array( $data ) ) {
      set_transient( $this->transient_key, $data, 12 * HOUR_IN_SECONDS );
      return $data;
    }

    return false;
  }

  /**
   * フェーズ計算ロジック
   */
  private function calculate_phase_index( $user_id, $data ) {
    $userdata = get_userdata( $user_id );
    if ( ! $userdata ) return -1;

    // ユーザー登録日からの経過日数を計算
    $registration_date = strtotime( $userdata->user_registered );
    $now = time();
    $days_since_site_start = ( $now - $registration_date ) / DAY_IN_SECONDS;

    // ユーザー登録日からの総月数
    $months_since_site_start = $days_since_site_start / 30;

    // JSONから閾値設定を取得
    $thresholds    = $data['global_settings']['thresholds'] ?? array();
    $defaults_cond = $data['global_settings']['defaults']['conditions'] ?? array();

    // 1. 【初回判定】開始フェーズと、その時の「経過月数」を保存
    $base_index = get_user_meta( $user_id, $this->meta_key_base_index, true );
    $start_month = get_user_meta( $user_id, $this->meta_key_base_month, true );

    // 初回のみ記事数を計算してフェーズを保存
    if ( $base_index === '' ) {
      // ユーザー登録から30日以内なら、記事数に関わらず強制的に「案1」から開始
      if ( $days_since_site_start < ($thresholds['new_site_days'] ?? 30) ) {
        $base_index = 0;
      } else {
        // 30日以上経っているユーザーのみ、サイトに投稿されている記事数に基づいてベースを決定
        $post_counts_obj = wp_count_posts( 'post' );
        $post_count = isset( $post_counts_obj->publish ) ? (int) $post_counts_obj->publish : 0;
        $base_index = 0;
        foreach ( $data['delivery_phases'] as $i => $p ) {
          $min_posts = $p['conditions']['min_posts'] ?? $defaults_cond['min_posts'] ?? 0;
          if ( $post_count >= (int)$min_posts ) {
            $base_index = $i;
          }
        }
      }

      // 保存：どの案からスタートしたか、何ヶ月目の時にスタートしたか
      update_user_meta( $user_id, $this->meta_key_base_index, $base_index );
      update_user_meta( $user_id, $this->meta_key_base_month, $months_since_site_start );
      $start_month = $months_since_site_start;
    }

    // 表示待機期間（例：30日）チェック
    if ( $days_since_site_start < ($thresholds['silent_period_days'] ?? 30) ) {
      return -1;
    }

    // ステップアップ計算
    // 「通知を初めて見た時」から今日までに何ヶ月経ったかを算出
    $elapsed_since_start = (float)$months_since_site_start - (float)$start_month;

    $current_index = (int)$base_index;
    $accumulated_duration = 0;

    // base_index からスタートして、3ヶ月経つごとに index を増やす
    for ( $i = $current_index; $i < count($data['delivery_phases']) - 1; $i++ ) {
      $p = $data['delivery_phases'][$i];
      $duration = isset($p['conditions']['duration_months']) ? $p['conditions']['duration_months'] : ($defaults_cond['duration_months'] ?? 3);

      if ( $duration !== null && $elapsed_since_start >= ($accumulated_duration + $duration) ) {
        $current_index++;
        $accumulated_duration += $duration;
      } else {
        break;
      }
    }

    return $current_index;
  }
}

new Xwrite_Notice_Manager();
