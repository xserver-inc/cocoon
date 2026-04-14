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

  // WordPressのタイムゾーン設定に基づく現在日時を取得
  // ※ current_time('timestamp') + strtotime() の組み合わせだと、
  //    gmt_offsetのみ設定された環境でタイムゾーンのズレが生じるため、
  //    wp_timezone() + DateTimeImmutable で統一して比較する
  $tz     = wp_timezone();
  $now    = current_datetime();
  $now_ts = $now->getTimestamp();

  // 開始日時の判定（未入力の場合は1日前扱い）
  try {
    $from_time = ! empty( $from )
      ? ( new DateTimeImmutable( $from, $tz ) )->getTimestamp()
      : $now_ts - DAY_IN_SECONDS;
  } catch ( \Exception $e ) {
    $from_time = $now_ts - DAY_IN_SECONDS;
  }

  // 終了日時の判定（未入力の場合は1日後扱い）
  try {
    $to_time = ! empty( $to )
      ? ( new DateTimeImmutable( $to, $tz ) )->getTimestamp()
      : $now_ts + DAY_IN_SECONDS;
  } catch ( \Exception $e ) {
    $to_time = $now_ts + DAY_IN_SECONDS;
  }

  // 期間外の場合は空文字を返す（表示しない）
  // ※ 開始・終了時刻ちょうども期間内として扱うため <= を使用
  if ( ! ( $from_time <= $now_ts && $now_ts <= $to_time ) ) {
    return '';
  }

  // 期間内の場合はコンテンツをそのまま返す
  return $content;
}
endif;
