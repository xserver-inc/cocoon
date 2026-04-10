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
      // エディタで設定したalignやカスタムクラスが未ログイン時にも反映されるよう、
      // ブロック本来のラッパークラスを生成してレイアウト崩れを防止する
      $classes = 'wp-block-cocoon-blocks-login-user-only login-user-only block-box';
      if ( ! empty( $attributes['align'] ) ) {
        $classes .= ' align' . $attributes['align'];
      }
      if ( ! empty( $attributes['className'] ) ) {
        $classes .= ' ' . $attributes['className'];
      }
      
      // sanitize_shortcode_valueで既にesc_html済みのためそのまま出力する
      // ※ 以前はここでhtmlspecialchars_decodeしていたが、
      //    esc_htmlによるサニタイズを無効化してしまうため削除した
      return sprintf(
        '<div class="%s">%s</div>',
        esc_attr( $classes ),
        $msg
      );
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
