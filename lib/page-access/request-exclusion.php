<?php
/**
 * アクセス解析の自動アクセス・先読み除外
 *
 * @license GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('cocoon_analytics_compile_bot_patterns')):
function cocoon_analytics_compile_bot_patterns($json){
  // 欠落・破損した同梱ファイルによる計測処理全体の停止の防止
  if (!is_string($json)) return array();
  $data = json_decode($json, true);
  if (!is_array($data) || !isset($data['patterns']) || !is_array($data['patterns'])) return array();
  foreach ($data['patterns'] as $pattern) {
    if (!is_string($pattern) || trim($pattern) === '') return array();
  }

  $patterns = array();
  // コンパイル上限を避けつつ照合回数を抑える40件単位の正規表現
  foreach (array_chunk(array_reverse($data['patterns']), 40) as $chunk) {
    $regex = '/(?:^|[^A-Z0-9_-]|[^A-Z0-9-]_|sprd-|MZ-)(?:' . str_replace('/', '\\/', implode('|', $chunk)) . ')/i';
    // 壊れた正規表現と空文字への一致による全閲覧の誤除外の防止
    if (@preg_match($regex, '') === 0) $patterns[] = $regex;
  }
  return $patterns;
}
endif;

if (!function_exists('cocoon_analytics_is_automated_user_agent')):
function cocoon_analytics_is_automated_user_agent($user_agent){
  // 空値・不正な型・異常に長い入力による誤計測と正規表現負荷の抑制
  if (!is_string($user_agent) || trim($user_agent) === '' || strlen($user_agent) > 4096) return true;

  // ボットとは別分類のヘッドレスブラウザーと自動操作ツールの除外
  if (preg_match('/HeadlessChrome|PhantomJS|Puppeteer|Playwright|Selenium/i', $user_agent)) return true;

  // 外部通信やプラグインに依存しない、同梱されたMatomoボット定義の読み込み
  static $patterns = null;
  if ($patterns === null) {
    $patterns = cocoon_analytics_compile_bot_patterns(@file_get_contents(__DIR__ . '/bot-patterns.json'));
  }

  // Matomoと同じ語頭条件による、通常ブラウザー名や端末名への部分一致の抑制
  foreach ($patterns as $regex) {
    if (preg_match($regex, $user_agent)) return true;
  }
  return false;
}
endif;

if (!function_exists('cocoon_analytics_request_is_excluded')):
function cocoon_analytics_request_is_excluded(){
  // 通常閲覧とは区別した、明示的な先読み・事前描画リクエストの除外
  foreach (array('HTTP_PURPOSE', 'HTTP_SEC_PURPOSE', 'HTTP_X_MOZ') as $header) {
    if (isset($_SERVER[$header]) && is_string($_SERVER[$header])
      && preg_match('/(?:^|[\s,;])(?:prefetch|prerender)(?=$|[\s,;=])/i', $_SERVER[$header])) return true;
  }

  $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
  if (cocoon_analytics_is_automated_user_agent($user_agent)) return true;

  // 既存のボット除外および子テーマによる既存判定の上書きとの互換性維持
  if (function_exists('is_useragent_robot') && is_useragent_robot()) return true;

  // 接続元が確認できないリクエストの除外と、転送ヘッダーへの非依存
  return empty($_SERVER['REMOTE_ADDR']) || !is_string($_SERVER['REMOTE_ADDR']);
}
endif;
