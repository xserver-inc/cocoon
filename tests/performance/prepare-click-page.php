<?php
/**
 * Docker版WordPressへクリック解析性能試験ページを準備します。
 */

$slug = 'cocoon-click-performance-test';
$existing = get_page_by_path($slug, OBJECT, 'page');
$links = array();
for ($index = 1; $index <= 100; $index++) {
    $href = $index % 2 === 0
        ? home_url('/?click-test-target=' . $index)
        : 'https://example.com/cocoon-click-test/' . $index;
    $links[] = sprintf(
        '<li><a href="%s" data-cocoon-click-area="content" data-cocoon-click-label="性能試験リンク%d">性能試験リンク %d</a></li>',
        esc_url($href),
        $index,
        $index
    );
}

$content = '<h1>クリック解析性能試験</h1>'
    . '<p>100リンクを使って初期処理、表示計測、Lighthouseを検証するローカル専用ページです。</p>'
    . '<ul>' . implode('', $links) . '</ul>';

$post_data = array(
    'ID' => $existing ? (int) $existing->ID : 0,
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_name' => $slug,
    'post_title' => 'クリック解析性能試験',
    'post_content' => $content,
);
$page_id = $existing ? wp_update_post($post_data) : wp_insert_post($post_data);
if (is_wp_error($page_id) || !$page_id) {
    fwrite(STDERR, "性能試験ページを作成できませんでした。\n");
    exit(1);
}

set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 1);
set_theme_mod(OP_CLICK_ANALYTICS_TRACK_INTERNAL, 1);
set_theme_mod(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, 1);
set_theme_mod(OP_CLICK_ANALYTICS_TRACK_SPECIAL, 1);
set_theme_mod(OP_CLICK_ANALYTICS_IMPRESSIONS, 1);
set_theme_mod(OP_CLICK_ANALYTICS_HEATMAP, 1);
set_theme_mod(OP_CLICK_ANALYTICS_OUTCOMES, 1);
set_theme_mod(OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN, 1);
set_theme_mod(OP_CLICK_ANALYTICS_RESPECT_PRIVACY, 1);
create_click_analytics_tables();

update_option('show_on_front', 'page');
update_option('page_on_front', (int) $page_id);

echo home_url('/') . PHP_EOL;
