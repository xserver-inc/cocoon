<?php
/**
 * 統合テスト専用の同時PV記録ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_PV_TEST_WORKER') !== '1') exit(1);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
define('DISABLE_WP_CRON', true);
require rtrim(getenv('COCOON_PV_TEST_WP_ROOT'), '/') . '/wp-load.php';
wp_set_current_user(0);

// 両方のワーカーの起動完了後に親プロセスから受け取る開始合図
fwrite(STDOUT, "COCOON_PV_READY\n");
fflush(STDOUT);
if (trim((string) fgets(STDIN)) !== 'START') exit(2);
$postId = (int) $argv[1];
$base = (int) $argv[2];
$sameIp = $argv[3] === 'same';
$accepted = 0;
for ($i = 0; $i < 10; $i++) {
    $_SERVER['REMOTE_ADDR'] = $sameIp ? '192.0.2.1' : '198.51.100.' . ($base + $i);
    if (logging_page_access($postId, 'post') === 1) $accepted++;
}
fwrite(STDOUT, 'COCOON_PV_RESULT ' . json_encode(array('accepted' => $accepted)) . "\n");
fflush(STDOUT);
