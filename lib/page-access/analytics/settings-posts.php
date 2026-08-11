<?php //アクセス解析ダッシュボード - 設定タブの保存処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * 設定タブの送信内容を保存し、表示すべき通知のHTMLを返す
 *
 * WordPress本体のcommon.jsは「.wrap内で最初の見出しの直後」へ通知を移動させるため、
 * 通知をその場で出力すると描画後に位置が飛んでしまいます。
 * 呼び出し側が見出し直後へ差し込めるよう、出力せずHTMLを返します。
 */
if ( !function_exists( 'cocoon_analytics_save_settings' ) ):
function cocoon_analytics_save_settings(){
  if ( !isset($_POST[HIDDEN_FIELD_NAME]) ||
       !wp_verify_nonce($_POST[HIDDEN_FIELD_NAME], 'access') ) {
    return '';
  }

  // キャッシュクリアは設定変更操作ではないため保存処理を行わない
  if (isset($_POST['cocoon_analytics_flush'])) {
    cocoon_analytics_flush_cache();
    return '<div class="notice notice-success is-dismissible"><p><strong>' .
           esc_html__('アクセス解析のキャッシュをクリアしました。', THEME_NAME) . '</strong></p></div>';
  }

  // 既存の3項目 + 新設4項目 を保存
  require dirname(__FILE__) . '/../access-posts.php';
  update_theme_option(OP_ACCESS_ANALYTICS_ENABLE);
  update_theme_option(OP_ACCESS_ANALYTICS_CACHE_TTL);
  update_theme_option(OP_ACCESS_ANALYTICS_DEFAULT_PERIOD);
  update_theme_option(OP_ACCESS_ANALYTICS_EXPORT_ENABLE);

  return '<div class="notice notice-success is-dismissible"><p><strong>' .
         esc_html__('設定を変更しました。', THEME_NAME) . '</strong></p></div>';
}
endif;
