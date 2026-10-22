<?php
/**
 * 独自公開ステータスと投稿タイプの公開判定に従ったPV記録の実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessPublicStatusIntegrationTest extends IntegrationTestCase
{
    private array $server;
    private array $mods;
    private int $userId;
    private array $accessPostIds = array();
    private const PUBLIC_STATUS = 'cocoon_pv_public';
    private const HIDDEN_STATUS = 'cocoon_pv_hidden';
    private const PUBLIC_TYPE = 'cocoon_pv_book';
    private const HIDDEN_TYPE = 'cocoon_pv_internal';

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = $_SERVER;
        $this->mods = get_theme_mods() ?: array();
        $this->userId = get_current_user_id();
        $this->assertTrue(create_accesses_table());
        register_post_status(self::PUBLIC_STATUS, array('public' => true));
        register_post_status(self::HIDDEN_STATUS, array('public' => false, 'private' => true));
        register_post_type(self::PUBLIC_TYPE, array('public' => true));
        register_post_type(self::HIDDEN_TYPE, array('public' => false, 'publicly_queryable' => false));
        set_theme_mod(OP_ACCESS_COUNT_ENABLE, 1);
        wp_set_current_user(0);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '192.0.2.1';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
        unset($_SERVER['HTTP_PURPOSE'], $_SERVER['HTTP_SEC_PURPOSE'], $_SERVER['HTTP_X_MOZ']);
    }

    protected function tearDown(): void
    {
        global $wpdb, $wp_post_statuses;
        foreach ($this->accessPostIds as $id) $wpdb->delete(ACCESSES_TABLE_NAME, array('post_id' => $id), array('%d'));
        $_SERVER = $this->server;
        update_option('theme_mods_' . get_stylesheet(), $this->mods);
        wp_set_current_user($this->userId);
        try {
            // 登録中の投稿タイプに対応するWordPress削除処理の完了後の解除
            parent::tearDown();
        } finally {
            unregister_post_type(self::PUBLIC_TYPE);
            unregister_post_type(self::HIDDEN_TYPE);
            unset($wp_post_statuses[self::PUBLIC_STATUS], $wp_post_statuses[self::HIDDEN_STATUS]);
        }
    }

    private function fixture(string $status, string $type): int
    {
        $data = array('post_status' => $status, 'post_type' => $type, 'post_title' => '公開判定PVテスト');
        // 予約投稿が日時不足で公開状態へ変わることを避けるための将来日時
        if ($status === 'future') $data['post_date'] = '2099-01-01 00:00:00';
        $id = $this->createPost($data);
        $this->accessPostIds[] = $id;
        return $id;
    }

    private function countPv(int $id): int
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare('SELECT COALESCE(SUM(count),0) FROM `' . ACCESSES_TABLE_NAME . '` WHERE post_id=%d', $id));
    }

    /**
     * 独自公開ステータスと公開カスタム投稿タイプのPV記録の確認
     */
    public function testPublicCustomStatusesAndPostTypesAreCounted(): void
    {
        foreach (array(
            array(self::PUBLIC_STATUS, 'post'),
            array(self::PUBLIC_STATUS, self::PUBLIC_TYPE),
            array('publish', self::PUBLIC_TYPE),
        ) as $fixture) {
            list($status, $type) = $fixture;
            $id = $this->fixture($status, $type);
            $this->assertTrue(is_post_publicly_viewable($id));
            $this->assertTrue(cocoon_analytics_is_valid_page_access($id, $type));
            $this->assertSame(1, logging_page_access($id, $type));
            $this->assertSame(1, $this->countPv($id));
        }
    }

    /**
     * 標準・独自の非公開状態と非公開投稿タイプに対するPV除外の維持
     */
    public function testNonPublicStatusesTypesAndMismatchedTypesAreRejected(): void
    {
        foreach (array('draft', 'private', 'trash', 'pending', 'future', self::HIDDEN_STATUS) as $status) {
            $id = $this->fixture($status, self::PUBLIC_TYPE);
            $this->assertFalse(is_post_publicly_viewable($id), $status);
            $this->assertFalse(logging_page_access($id, self::PUBLIC_TYPE), $status);
            $this->assertSame(0, $this->countPv($id), $status);
        }
        $id = $this->fixture('publish', self::HIDDEN_TYPE);
        $this->assertFalse(is_post_publicly_viewable($id));
        $this->assertFalse(logging_page_access($id, self::HIDDEN_TYPE));
        $this->assertSame(0, $this->countPv($id));

        $id = $this->fixture(self::PUBLIC_STATUS, self::PUBLIC_TYPE);
        $this->assertFalse(logging_page_access($id, 'post'));
        $this->assertSame(0, $this->countPv($id));
    }
}
