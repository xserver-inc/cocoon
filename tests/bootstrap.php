<?php
/**
 * Cocoon テーマ ユニットテスト ブートストラップ
 *
 * テスト実行前の初期化処理を行います。
 * WordPress関数のモック化と必要な定数の定義を行い、
 * テーマファイルをWordPressなしでテスト可能にします。
 */

// Composerオートローダー読み込み
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Brain\Monkey セットアップ
require_once __DIR__ . '/wp-mock-functions.php';

// テーマに必要な定数を定義
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__, 5) . '/');
}
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', dirname(__DIR__, 3));
}
if (!defined('THEME_NAME')) {
    define('THEME_NAME', 'cocoon-master');
}

// テーマの中核ユーティリティファイルを事前読み込み
// ほぼ全てのテーマファイルがこれに依存するため、ブートストラップで読み込む
require_once dirname(__DIR__) . '/lib/utils.php';

// テストの基底クラス読み込み
require_once __DIR__ . '/TestCase.php';
