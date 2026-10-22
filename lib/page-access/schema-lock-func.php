<?php
/**
 * スキーマ移行用の名前付きロックのDB互換性判定
 */
if (!defined('ABSPATH')) exit;

// 接続内での複数名前付きロックに対応した、DB製品別のバージョン判定
if (!function_exists('cocoon_analytics_schema_supports_multiple_locks')):
function cocoon_analytics_schema_supports_multiple_locks($version) {
  if (!is_string($version)) return null;
  if (stripos($version, 'MariaDB') !== false) {
    // MariaDB互換用の5.5.5接頭辞を除いた、実サーバーバージョンの抽出
    if (!preg_match('/(?:^|-)(\d+\.\d+\.\d+)-MariaDB\b/i', $version, $matches)) return null;
    return version_compare($matches[1], '10.0.2', '>=');
  }
  if (!preg_match('/^(\d+\.\d+\.\d+)(?:[-+.]|$)/', $version, $matches)) return null;
  return version_compare($matches[1], '5.7.5', '>=');
}
endif;
