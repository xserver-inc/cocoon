<?php
/**
 * 外側のトランザクションを維持する、ランキング書き出し用の一時ファイル処理
 *
 * @license GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;

/**
 * 大きな整数PVを浮動小数点へ変換せず、DBと同じPV・公開日・IDの降順で比較する。
 * NULLは数値の後ろとし、負数も桁数と数字列で比較する。
 */
if (!function_exists('cocoon_analytics_export_compare_records')):
function cocoon_analytics_export_compare_records($left, $right){
  if ($left['pv'] === null || $right['pv'] === null) {
    if ($left['pv'] !== $right['pv']) return $left['pv'] === null ? 1 : -1;
  } else {
    // 符号・先頭ゼロの正規化と、桁数・数字列による浮動小数点を使わない大小比較
    $a = (string) $left['pv'];
    $b = (string) $right['pv'];
    $a_digits = ltrim(ltrim($a, '+-'), '0');
    $b_digits = ltrim(ltrim($b, '+-'), '0');
    if ($a_digits === '') $a_digits = '0';
    if ($b_digits === '') $b_digits = '0';
    $a_negative = $a_digits !== '0' && substr($a, 0, 1) === '-';
    $b_negative = $b_digits !== '0' && substr($b, 0, 1) === '-';
    if ($a_negative !== $b_negative) return $a_negative ? 1 : -1;
    $comparison = strlen($a_digits) <=> strlen($b_digits);
    if ($comparison === 0) $comparison = strcmp($a_digits, $b_digits);
    if ($comparison !== 0) return $a_negative ? $comparison : -$comparison;
  }
  $date_comparison = strcmp($right['post_date'], $left['post_date']);
  return $date_comparison !== 0 ? $date_comparison : ((int) $right['post_id'] <=> (int) $left['post_id']);
}
endif;

if (!function_exists('cocoon_analytics_export_write_record')):
function cocoon_analytics_export_write_record($stream, $record){
  $line = wp_json_encode($record);
  if (!is_string($line)) return false;
  $line .= "\n";
  $offset = 0;
  $length = strlen($line);
  // 一部だけの書き込みやディスク不足を見逃さない、行全体の保存確認
  while ($offset < $length) {
    $written = @fwrite($stream, substr($line, $offset));
    if ($written === false || $written === 0) return false;
    $offset += $written;
  }
  return true;
}
endif;

if (!function_exists('cocoon_analytics_export_read_record')):
function cocoon_analytics_export_read_record($stream){
  $line = @fgets($stream, 4096);
  if ($line === false) return feof($stream) ? null : false;
  $record = json_decode($line, true);
  if (!is_array($record) || !isset($record['post_id'], $record['post_type'], $record['post_date']) || !array_key_exists('pv', $record)) return false;
  return $record;
}
endif;

/**
 * 整列済みの2ファイルを1件ずつ比較し、1つの整列済みファイルへまとめる。
 * 入力ファイルは成功・失敗のどちらでも解放し、結果だけを呼び出し側へ渡す。
 */
if (!function_exists('cocoon_analytics_export_merge_streams')):
function cocoon_analytics_export_merge_streams($left, $right){
  $output = @fopen('php://temp/maxmemory:65536', 'w+b');
  $completed = false;
  try {
    if ($output === false) return false;
    $a = cocoon_analytics_export_read_record($left);
    $b = cocoon_analytics_export_read_record($right);
    while ($a !== null || $b !== null) {
      if ($a === false || $b === false) return false;
      $take_left = $b === null || ($a !== null && cocoon_analytics_export_compare_records($a, $b) <= 0);
      if (!cocoon_analytics_export_write_record($output, $take_left ? $a : $b)) return false;
      if ($take_left) $a = cocoon_analytics_export_read_record($left);
      else $b = cocoon_analytics_export_read_record($right);
    }
    if (!@rewind($output)) return false;
    $completed = true;
    return $output;
  } finally {
    if (is_resource($left)) fclose($left);
    if (is_resource($right)) fclose($right);
    if (!$completed && is_resource($output)) fclose($output);
  }
}
endif;

/**
 * DBの一時表を使わず、全件の順位を一時ファイルへ固定する。
 * 単一SELECTで固定した最小限のメタデータを100件ずつ整列し、記事本文を蓄積せず二分マージする。
 */
if (!function_exists('cocoon_analytics_create_export_stream')):
function cocoon_analytics_create_export_stream($database, $from, $to){
  $runs = array();
  $pending = null;
  $suppressed = $database->suppress_errors(true);
  try {
    foreach (cocoon_analytics_export_record_batches($database, $from, $to) as $records) {
      if (!$records) continue;
      usort($records, 'cocoon_analytics_export_compare_records');
      $pending = @fopen('php://temp/maxmemory:65536', 'w+b');
      if ($pending === false) return false;
      foreach ($records as $record) if (!cocoon_analytics_export_write_record($pending, $record)) return false;
      if (!@rewind($pending)) return false;
      // 同じ大きさのファイルだけをまとめる、保持ファイル数とメモリ使用量の抑制
      $level = 0;
      while (isset($runs[$level])) {
        $previous = $runs[$level];
        unset($runs[$level]);
        $pending = cocoon_analytics_export_merge_streams($previous, $pending);
        if ($pending === false) return false;
        $level++;
      }
      $runs[$level] = $pending;
      $pending = null;
    }
    foreach ($runs as $level => $run) {
      unset($runs[$level]);
      $pending = $pending === null ? $run : cocoon_analytics_export_merge_streams($pending, $run);
      if ($pending === false) return false;
    }
    if ($pending === null) $pending = @fopen('php://temp/maxmemory:65536', 'w+b');
    if ($pending === false) return false;
    $result = $pending;
    $pending = null;
    return $result;
  } catch (RuntimeException $error) {
    return false;
  } finally {
    // 途中失敗や例外でも影響を残さない、全作業ファイルの解放とエラー表示設定の復元
    if (is_resource($pending)) fclose($pending);
    foreach ($runs as $run) if (is_resource($run)) fclose($run);
    $database->suppress_errors($suppressed);
  }
}
endif;

if (!function_exists('cocoon_analytics_export_stream_ranking_rows')):
function cocoon_analytics_export_stream_ranking_rows($database, $from, $to){
  $stream = cocoon_analytics_create_export_stream($database, $from, $to);
  if ($stream === false) return new WP_Error('cocoon_analytics_export_stream', __('データの取得に失敗しました。', THEME_NAME));
  // 未開始・途中終了・例外の場合にもファイルを解放するための所有オブジェクト
  $owner = new class($stream) {
    private $stream;
    public function __construct($stream){
      $this->stream = $stream;
    }
    public function release(){
      if (is_resource($this->stream)) fclose($this->stream);
    }
    public function __destruct(){
      $this->release();
    }
  };
  return (static function() use ($stream, $owner){
    try {
      while (($record = cocoon_analytics_export_read_record($stream)) !== null) {
        if ($record === false) throw new RuntimeException(__('データの取得に失敗しました。', THEME_NAME));
        // 標準ランキングと同じ整数型への変換と、キャッシュを蓄積しない行の作成
        $record['post_id'] = (int) $record['post_id'];
        $record['pv'] = (int) $record['pv'];
        yield cocoon_analytics_export_ranking_row($record);
      }
    } finally {
      $owner->release();
    }
  })();
}
endif;
