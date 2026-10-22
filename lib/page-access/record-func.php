<?php
/**
 * 既存のDB接続クラスを維持したPVの原子的な記録
 *
 * @license GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;

/**
 * 自動コミットが有効で、外側のトランザクションも存在しないと確認できた場合だけtrueを返す。
 * トランザクション中や判定不能の場合はfalseとし、呼び出し元の確定・取消・分離レベルは変更しない。
 * SAVEPOINTはトランザクション外では作成されず、RELEASE時のエラー1305でその状態を判別できる。
 */
if (!function_exists('cocoon_analytics_database_is_autocommit')):
function cocoon_analytics_database_is_autocommit($database){
  $suppressed = $database->suppress_errors(true);
  try {
    // 自動コミット無効時と接続状態を確認できない場合の、安全側への延期
    if ($database->query('SELECT @@SESSION.autocommit LIMIT 1') === false || (string) $database->get_var() !== '1') return false;

    // 呼び出し元のセーブポイント名との衝突を避けた、固定長の一意な識別子
    $savepoint = 'cocoon_pv_' . md5(uniqid('', true));
    if ($database->query('SAVEPOINT `' . $savepoint . '`') === false) return false;
    try {
      $released = $database->query('RELEASE SAVEPOINT `' . $savepoint . '`');
    } catch (Throwable $error) {
      // 例外を送出するDB設定でも診断結果を確認するための、RELEASE失敗への統一
      $released = false;
    }
    if ($released !== false) return false;

    // 翻訳されたエラーメッセージに依存しない、セーブポイント未存在コードだけの許可
    if ($database->query('SHOW ERRORS LIMIT 1') === false) return false;
    return (string) $database->get_var(null, 1) === '1305';
  } catch (Throwable $error) {
    return false;
  } finally {
    $database->suppress_errors($suppressed);
  }
}
endif;

if (!function_exists('cocoon_analytics_record_key')):
function cocoon_analytics_record_key($post_id, $date, $post_type){
  // 記事・日付・投稿タイプを一意に識別する、固定長の記録キー
  return hash('sha256', (int) $post_id . '|' . $date . '|' . $post_type);
}
endif;

/**
 * 標準構成では原子的な記録を維持し、旧ヘルパーの上書きがあれば従来の引数で呼び出す。
 * 上書き側の保存先・SQLの原子性・最終計測時刻の保存は、その実装の契約に従う。
 * 時刻が不明な旧行を永久に除外せず、既知の時刻だけを30秒判定に使用する。
 */
if (!function_exists('cocoon_analytics_record_page_access_with_helpers')):
function cocoon_analytics_record_page_access_with_helpers($post_id, $date, $post_type, $ip){
  // 上位・下位それぞれの上書きを尊重した、挿入側と更新側ごとの標準実装の判定
  $default_insert = defined('COCOON_ANALYTICS_DEFAULT_INSERT_ACCESSES_RECORD') && defined('COCOON_DEFAULT_INSERT_DB_TABLE_RECORD');
  $default_update = defined('COCOON_ANALYTICS_DEFAULT_UPDATE_ACCESSES_RECORD') && defined('COCOON_DEFAULT_UPDATE_DB_TABLE_RECORD');
  // 上位・下位の両側とも標準実装の場合の、一意制約による原子的な記録
  if ($default_insert && $default_update) return cocoon_analytics_record_page_access($post_id, $date, $post_type, $ip);

  // 並列追加・削除後も反対側の上書きを迂回しない、旧ヘルパー1操作だけの呼び分け
  $record = get_accesse_record_from($post_id, $date, $post_type);
  if ($record) {
    $last_accessed_at = isset($record->last_accessed_at) ? (int) $record->last_accessed_at : 0;
    // 保存時刻が既知の場合だけを対象とした、上書きヘルパー呼び出し前の連打除外
    if (!DEBUG_MODE && $record->last_ip === $ip && $last_accessed_at > 0 && $last_accessed_at > time() - 30) return false;
    $posts = array('last_ip' => $ip, 'count' => (int) $record->count + 1);
    // 独自の更新ヘルパーへの従来2項目の維持と、標準側だけの計測時刻の補完
    if ($default_update) $posts['last_accessed_at'] = time();
    return update_accesses_record($record->id, $posts);
  }
  $posts = array('post_id' => $post_id, 'date' => $date, 'post_type' => $post_type, 'last_ip' => $ip, 'count' => 1);
  // 独自の挿入ヘルパーへの従来5項目の維持と、標準側だけの計測時刻の補完
  if ($default_insert) $posts['last_accessed_at'] = time();
  return insert_accesses_record($posts);
}
endif;

/**
 * 一意制約と単一SQLで初回記録・加算・30秒以内の連打除外を完結する。
 * 旧行があれば先頭行を引き継ぎ、残りの重複行や過去のPVは保持する。
 */
if (!function_exists('cocoon_analytics_record_page_access')):
function cocoon_analytics_record_page_access($post_id, $date, $post_type, $ip){
  global $wpdb;
  $table = ACCESSES_TABLE_NAME;
  $key = cocoon_analytics_record_key($post_id, $date, $post_type);

  // 新規行への範囲ロックを避けた、一意キー未登録の先頭の旧行IDの通常検索
  $selected = $wpdb->query($wpdb->prepare(
    "SELECT id FROM `{$table}`
     WHERE post_id = %d AND date = %s AND post_type = %s AND record_key IS NULL ORDER BY id ASC LIMIT 1",
    $post_id, $date, $post_type
  ));
  if ($selected === false) return false;
  $legacy_id = $wpdb->get_var();
  if ($legacy_id !== null) {
    // 検索後の対象変更も除外する、主キーと元の記事・日付・投稿タイプによる一意キーの採番
    $adopted = $wpdb->query($wpdb->prepare(
      "UPDATE IGNORE `{$table}` SET record_key = %s WHERE id = %d AND post_id = %d AND date = %s AND post_type = %s AND record_key IS NULL",
      $key, $legacy_id, $post_id, $date, $post_type
    ));
    if ($adopted === false) return false;
  }

  $now = time();
  $count_expression = 'COALESCE(count, 0) + 1';
  $timestamp = '%d';
  $last_ip = '%s';
  $args = array($post_id, $date, $post_type, $ip, $now, $key);
  if (!DEBUG_MODE) {
    // 更新前のIPと時刻による連打判定と、除外時のNULLを含む元のPV・時刻の維持
    $count_expression = 'IF(last_ip <=> %s AND COALESCE(last_accessed_at, 0) > %d, count, COALESCE(count, 0) + 1)';
    $timestamp = 'IF(last_ip <=> %s AND COALESCE(last_accessed_at, 0) > %d, last_accessed_at, %d)';
    $args = array_merge($args, array($ip, $now - 30, $ip, $now - 30));
  }
  $args[] = $now;
  if (!DEBUG_MODE) {
    // PV判定と同じIP比較による、除外時の保存表記の維持と変更なし応答の保証
    $last_ip = 'IF(last_ip <=> %s, last_ip, %s)';
    $args[] = $ip;
  }
  $args[] = $ip;

  // 独自のDB振り分けを通した、初回INSERTと既存行への加算の原子的な実行
  $result = $wpdb->query($wpdb->prepare(
    "INSERT INTO `{$table}` (post_id, date, post_type, count, last_ip, last_accessed_at, record_key)
     VALUES (%d, %s, %s, 1, %s, %d, %s)
     ON DUPLICATE KEY UPDATE count = {$count_expression},
       last_accessed_at = {$timestamp}, last_ip = {$last_ip}",
    $args
  ));
  // FOUND_ROWS接続でも、同じSQL応答の生成IDによる初回INSERTと変更なしの区別
  if ($result === false || $result === 0 || ($result === 1 && (string) $wpdb->insert_id === '0')) return false;
  // INSERTの1件とUPDATEの2件を、既存の呼び出し元と同じ成功値へ統一
  return 1;
}
endif;
