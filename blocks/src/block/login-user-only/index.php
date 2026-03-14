<?php
/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if ( ! function_exists( 'render_block_cocoon_login_user_only' ) ) {
  /**
   * login-user-only ブロックのレンダーコールバック
   * 
   * @param array $attributes ブロック属性
   * @param string $content ブロックのコンテンツ
   * @return string レンダリングされたHTML
   */
  function render_block_cocoon_login_user_only( $attributes, $content ) {
    // msgの取得（指定がなければデフォルトメッセージ）
    $msg = isset( $attributes['msg'] ) ? $attributes['msg'] : __( 'こちらのコンテンツはログインユーザーのみに表示されます。', THEME_NAME );
    
    // xss対策（ショートコードと同じサニタイズ関数を使用）
    $msg = sanitize_shortcode_value( $msg );

    // ログインユーザーの場合
    if ( is_user_logged_in() ) {
      return $content; // そのままInnerBlocksの中身を表示
    } else {
      // 未ログインユーザーの場合
      return '<div class="login-user-only">' . htmlspecialchars_decode( $msg ) . '</div>';
    }
  }
}

/**
 * ブロックの登録
 */
add_action(
  'init',
  function () {
    register_block_type_from_metadata(
      __DIR__ . '/block.json',
      array(
        'render_callback' => 'render_block_cocoon_login_user_only',
      )
    );
  }
);
