<?php
/**
 * WordPress 統合テスト ブートストラップ
 *
 * WordPress のテストスイートを使用して、実際の WordPress 環境で
 * テーマ機能をテストします。
 *
 * 使い方:
 * 1. WP_TESTS_DIR 環境変数に WordPress テストスイートのパスを設定
 *    例: export WP_TESTS_DIR=/path/to/wordpress-develop/tests/phpunit
 *
 * 2. wp-tests-config.php を設定（テスト用DB接続情報）
 *
 * 3. テスト実行:
 *    vendor/bin/phpunit --testsuite integration
 *
 * ローカル環境でのセットアップ:
 * - Docker を使用する場合は docker/docker-compose.test.yml を参照
 * - GitHub Actions では自動的にセットアップされます
 */

// WordPress テストスイートのパスを環境変数から取得
$_tests_dir = getenv('WP_TESTS_DIR');

if (!$_tests_dir) {
    // 一般的なパスをフォールバックとして試行
    $possible_paths = [
        dirname(__DIR__, 6) . '/tests/phpunit',                    // WordPress develop ルート
        '/tmp/wordpress-tests-lib',                                 // 標準的な CI パス
        getenv('HOME') . '/wordpress-tests-lib',                   // ホームディレクトリ
    ];

    foreach ($possible_paths as $path) {
        if (file_exists($path . '/includes/functions.php')) {
            $_tests_dir = $path;
            break;
        }
    }
}

if (!$_tests_dir) {
    echo "WordPress テストスイートが見つかりません。\n";
    echo "WP_TESTS_DIR 環境変数を設定するか、WordPress テストスイートをインストールしてください。\n\n";
    echo "セットアップ方法:\n";
    echo "  1. GitHub Actions: .github/workflows/phpunit.yml に設定済み\n";
    echo "  2. ローカル: bin/install-wp-tests.sh を使用\n";
    echo "  3. 手動: export WP_TESTS_DIR=/path/to/wordpress-develop/tests/phpunit\n";
    exit(1);
}

// Composer オートローダー
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// テーマ読み込み関数を WordPress のテストブートストラップ前に登録
$_theme_dir = dirname(__DIR__, 2);
tests_add_filter('setup_theme', function() use ($_theme_dir) {
    // テーマを切り替え
    switch_theme('cocoon-master');
});

// WordPress テストスイートを読み込み
require $_tests_dir . '/includes/bootstrap.php';

// 統合テスト用基底クラス
require_once __DIR__ . '/IntegrationTestCase.php';
