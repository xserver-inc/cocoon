<?php
/**
 * Cocoon Blocks - キャンペーンブロック
 * 指定した期間中のみコンテンツを表示するブロック
 *
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if ( function_exists( 'register_block_type' ) ) {
  add_action( 'init', 'register_block_cocoon_campaign', 99 );
  // キャンペーンブロックの登録
  function register_block_cocoon_campaign() {
    register_block_type(
      __DIR__,
      array(
        // サーバーサイドでの期間判定に使うレンダリングコールバック
        'render_callback' => 'render_block_cocoon_campaign',
      )
    );
  }
}

if ( ! function_exists( 'render_block_cocoon_campaign' ) ):
/**
 * キャンペーンブロックのレンダリングコールバック
 * 期間外の場合は空文字を返してコンテンツを非表示にする
 */
function render_block_cocoon_campaign( $attributes, $content ) {
  // 開始日時の取得
  $from = isset( $attributes['from'] ) ? $attributes['from'] : '';
  // 終了日時の取得
  $to = isset( $attributes['to'] ) ? $attributes['to'] : '';

  // 現在のWordPressローカル日時を取得
  $now = current_time( 'timestamp' );

  // 開始日時の判定（未入力の場合は1日前扱い）
  $from_time = ! empty( $from ) ? strtotime( $from ) : strtotime( '-1 day' );
  // 終了日時の判定（未入力の場合は1日後扱い）
  $to_time = ! empty( $to ) ? strtotime( $to ) : strtotime( '+1 day' );

  // 期間外の場合は空文字を返す（表示しない）
  if ( ! ( $from_time < $now && $to_time > $now ) ) {
    return '';
  }

  // 期間内の場合はコンテンツをそのまま返す
  return $content;
}
endif;
