<?php
// HTTP経由での実行を防ぐ、Docker内の目次スクロール検証用HTMLの生成
if (PHP_SAPI !== 'cli') exit;

$_SERVER['HTTP_HOST'] = 'localhost:' . ($argv[2] ?? '8085');
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';

$mode = $argv[1] ?? 'fixed';
// データベースに保存しない、検証プロセス内だけのテーマ設定
global $_THEME_OPTIONS;
$_THEME_OPTIONS = array_merge($_THEME_OPTIONS, array(
    'header_fixed' => $mode === 'static' ? 0 : 1,
    'skin_url' => '',
    'toc_visible' => 1,
    'single_toc_visible' => 1,
    'toc_display_count' => 1,
    'toc_depth' => 0,
    'toc_toggle_switch_enable' => 0,
    'mobile_button_layout_type' => 'header_and_footer_mobile_buttons',
    'fixed_mobile_buttons_enable' => 0,
));
add_filter('get_skin_url', '__return_empty_string');
// 目次と無関係な外部Clipboardスクリプトの読み込みを避けるための設定
add_action('wp_footer', static function () {
    $GLOBALS['_MOBILE_COPY_BUTTON'] = false;
}, PHP_INT_MAX);

if ($mode === 'logged') {
    // ログインCookieを作成しない、管理バー表示用ユーザーの設定
    $users = get_users(array('role' => 'administrator', 'number' => 1));
    if (!$users) throw new RuntimeException('管理バー検証用の管理者が見つかりません。');
    wp_set_current_user($users[0]->ID);
    show_admin_bar(true);
    _wp_admin_bar_init();
}

// 既存投稿を更新しない、メモリ上の投稿と本文の複製
$post_ids = get_posts(array('numberposts' => 1, 'fields' => 'ids'));
if (!$post_ids) throw new RuntimeException('検証に使う投稿が見つかりません。');
$query = new WP_Query(array('p' => $post_ids[0], 'post_type' => 'post'));
$query->posts[0] = clone $query->posts[0];
$query->posts[0]->post_title = 'Cocoon 目次の移動位置の検証';
$content = '<p><a id="jump-toc" href="#toc">目次へ移動</a> '
    . '<a id="jump-native" href="#native-heading">独自アンカーの見出しへ移動</a></p>';
for ($index = 1; $index <= 4; $index++) {
    $id = $index === 3 ? ' id="native-heading"' : '';
    $content .= '<h2' . $id . '>確認用の見出し' . $index . '</h2>';
    $content .= str_repeat('<p>目次と固定ヘッダーの移動位置を確認するための本文です。</p>', 18);
}
$query->posts[0]->post_content = $content;
$GLOBALS['wp_query'] = $query;
$GLOBALS['wp_the_query'] = $query;
$query->the_post();
do_action('wp');

// 実際のテーマ・目次生成処理・配布JavaScriptを使う検証ページ
get_header();
echo '<article class="article"><div class="entry-content">';
echo add_toc_before_1st_h2($content);
echo '</div></article>';
get_footer();
