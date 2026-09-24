<?php
/**
 * PV記録時の自動アクセス除外と通常閲覧の実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessRequestExclusionIntegrationTest extends IntegrationTestCase
{
    /**
     * 自動アクセスと先読みの除外後も通常閲覧のPVが記録されることの確認
     */
    public function testOnlyNormalViewsIncreaseTheStoredCount(): void
    {
        global $wpdb;
        $server = $_SERVER;
        $mods = get_theme_mods();
        $userId = get_current_user_id();
        $postId = $this->createPost(array('post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'PV除外テスト'));
        create_accesses_table();
        try {
            wp_set_current_user(0);
            set_theme_mod(OP_ACCESS_COUNT_ENABLE, 1);
            $browser = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
            $_SERVER['HTTP_USER_AGENT'] = $browser;
            $_SERVER['REMOTE_ADDR'] = '192.0.2.1';
            unset($_SERVER['HTTP_PURPOSE'], $_SERVER['HTTP_SEC_PURPOSE'], $_SERVER['HTTP_X_MOZ']);
            logging_page_access($postId, 'post');
            $sql = $wpdb->prepare('SELECT COALESCE(SUM(count), 0) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id = %d', $postId);
            $this->assertSame('1', (string) $wpdb->get_var($sql));

            // IPを変更しても除外対象ではPVが増えないことの確認
            $_SERVER['REMOTE_ADDR'] = '192.0.2.2';
            foreach (array('GoogleOther', 'GPTBot/1.0', 'AhrefsBot/7.0', 'HeadlessChrome/154.0', 'Selenium', '') as $agent) {
                $_SERVER['HTTP_USER_AGENT'] = $agent;
                logging_page_access($postId, 'post');
                $this->assertSame('1', (string) $wpdb->get_var($sql), $agent);
            }
            $_SERVER['HTTP_USER_AGENT'] = $browser;
            foreach (array('HTTP_PURPOSE', 'HTTP_SEC_PURPOSE', 'HTTP_X_MOZ') as $header) {
                $_SERVER[$header] = 'prefetch';
                logging_page_access($postId, 'post');
                $this->assertSame('1', (string) $wpdb->get_var($sql), $header);
                unset($_SERVER[$header]);
            }

            // 先読み終了後の通常閲覧におけるPVの記録
            logging_page_access($postId, 'post');
            $this->assertSame('2', (string) $wpdb->get_var($sql));
        } finally {
            $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $postId), array('%d'));
            $_SERVER = $server;
            update_option('theme_mods_' . get_stylesheet(), $mods);
            wp_set_current_user($userId);
        }
    }
}
