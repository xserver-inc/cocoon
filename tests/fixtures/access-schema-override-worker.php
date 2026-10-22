<?php
/**
 * 従来のスキーマ更新関数の上書きを対象とした統合テスト専用ワーカー
 */
if (PHP_SAPI !== 'cli' || getenv('COCOON_PV_SCHEMA_OVERRIDE_WORKER') !== '1') exit(1);
$mode = isset($argv[2]) ? $argv[2] : '';
if (!in_array($mode, array('void', 'false'), true)) exit(2);
$GLOBALS['cocoon_schema_override_mode'] = $mode;
$GLOBALS['cocoon_schema_override_calls'] = 0;

// 親テーマの読み込みより前に定義される、子テーマ相当の旧契約の上書き関数
function update_accesses_table()
{
    $GLOBALS['cocoon_schema_override_calls']++;
    if ($GLOBALS['cocoon_schema_override_mode'] === 'false') return false;
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '192.0.2.1';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
define('DISABLE_WP_CRON', true);
require rtrim(getenv('COCOON_PV_SCHEMA_OVERRIDE_WP_ROOT'), '/') . '/wp-load.php';
wp_set_current_user(0);
add_filter('theme_mod_access_count_enable', '__return_true');
$postId = (int) $argv[1];
$results = array(logging_page_access($postId, 'post'), logging_page_access($postId, 'post'));
fwrite(STDOUT, json_encode(array('results' => $results, 'calls' => $GLOBALS['cocoon_schema_override_calls'])) . "\n");
