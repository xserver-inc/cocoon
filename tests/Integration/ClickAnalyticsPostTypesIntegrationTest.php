<?php
/**
 * カスタム投稿のクリック計測と内部リンク解決の統合テスト
 */

namespace Cocoon\Tests\Integration;

class ClickAnalyticsPostTypesIntegrationTest extends IntegrationTestCase
{
    private array $previousServer = array();
    private mixed $previousMods = null;
    private int $previousUser = 0;
    private mixed $previousQuery = null;
    private mixed $previousRewriteOption = null;
    private mixed $previousRewriteRules = null;
    private array $previousFrontOptions = array();
    private bool $initialized = false;
    private array $types = array('click_public_type', 'click_queryable_type', 'click_hidden_type');

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousServer = $_SERVER;
        $this->previousMods = get_theme_mods();
        $this->previousUser = get_current_user_id();
        $this->previousQuery = $GLOBALS['wp_query'] ?? null;
        $this->previousRewriteOption = get_option('rewrite_rules');
        $this->previousRewriteRules = $GLOBALS['wp_rewrite']->rules;
        $this->previousFrontOptions = array('show_on_front' => get_option('show_on_front'), 'page_on_front' => get_option('page_on_front'));
        $this->initialized = true;
        wp_set_current_user(0);
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/154.0.0.0 Safari/537.36';
        $_SERVER['REMOTE_ADDR'] = '192.0.2.3';
        unset($_SERVER['HTTP_PURPOSE'], $_SERVER['HTTP_SEC_PURPOSE'], $_SERVER['HTTP_X_MOZ']);
        register_post_type('click_public_type', array('public' => true, 'rewrite' => array('slug' => 'click-public')));
        // publicの値に依存しない、個別ページの公開設定による対象判定
        register_post_type('click_queryable_type', array('public' => false, 'publicly_queryable' => true));
        register_post_type('click_hidden_type', array('public' => true, 'publicly_queryable' => false));
        if (!function_exists('create_click_analytics_tables')) {
            require_once dirname(__DIR__, 2) . '/lib/page-access/click-analytics/_loader.php';
        }
        create_click_analytics_tables();
        cocoon_click_delete_all_data();
        cocoon_click_analytics_flush_cache();
        set_theme_mod(OP_CLICK_ANALYTICS_ENABLE, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_TRACK_INTERNAL, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, 1);
        set_theme_mod(OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN, 0);
        set_theme_mod(OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS, '');
        set_theme_mod(OP_CLICK_ANALYTICS_EXCLUDED_URLS, '');
    }

    protected function tearDown(): void
    {
        if ($this->initialized) {
            cocoon_click_delete_all_data();
        }
        parent::tearDown();
        if ($this->initialized) {
            foreach ($this->types as $type) {
                unregister_post_type($type);
            }
            $_SERVER = $this->previousServer;
            $GLOBALS['wp_query'] = $this->previousQuery;
            wp_set_current_user($this->previousUser);
            update_option('theme_mods_' . get_stylesheet(), $this->previousMods);
            // テスト用カスタム投稿のリライトルールを後続テストへ持ち越さない復元
            update_option('rewrite_rules', $this->previousRewriteOption);
            $GLOBALS['wp_rewrite']->rules = $this->previousRewriteRules;
            foreach ($this->previousFrontOptions as $name => $value) update_option($name, $value);
        }
    }

    private function createTypedPost(string $type, string $status = 'publish'): int
    {
        return $this->createPost(array(
            'post_type' => $type,
            'post_status' => $status,
            'post_title' => 'カスタム投稿クリック計測 ' . $type,
            'post_name' => 'click-type-' . $type . '-' . $status,
            'post_content' => '<a href="https://outside.example.org/guide/">外部リンク</a>',
        ));
    }

    /**
     * 終了時の解除対象を保持する、基本URL検証用投稿タイプの登録
     */
    private function registerRoutingType(string $type, array $args = array()): void
    {
        $result = register_post_type($type, array_merge(array('public' => true, 'rewrite' => false), $args));
        $this->assertInstanceOf(\WP_Post_Type::class, $result);
        $this->types[] = $type;
    }

    /**
     * 公開APIを通した内部リンク定義の保存と保存済み行の取得
     */
    private function storeInternalDefinition(string $url, int $sourceId, bool $unresolved = false): array
    {
        global $wpdb;
        $item = cocoon_click_sanitize_link_event(array('type' => 'click', 'href' => $url, 'label' => '内部リンク先検証', 'area' => 'content'), $sourceId, get_permalink($sourceId));
        $this->assertIsArray($item);
        // 旧版で保存された、補完前のリンク定義の再現
        if ($unresolved) $item['definition']['target_post_id'] = 0;
        $map = cocoon_click_upsert_link_definitions(array($item['definition']), current_time('mysql'));
        $this->assertIsArray($map);
        $this->assertArrayHasKey($item['definition']['link_key'], $map);
        $row = $wpdb->get_row($wpdb->prepare('SELECT id,destination_url,target_post_id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE id=%d', $map[$item['definition']['link_key']]), ARRAY_A);
        $this->assertIsArray($row);
        return $row;
    }

    /**
     * 誤ったホームページ補完を検出するための固定フロントページの準備
     */
    private function createStaticFrontPage(): int
    {
        $frontId = $this->createTypedPost('page');
        update_option('show_on_front', 'page');
        update_option('page_on_front', $frontId);
        return $frontId;
    }

    private function receiveForPost(int $postId, string $suffix): mixed
    {
        $revision = cocoon_click_layout_revision($postId);
        $request = new \WP_REST_Request('POST', '/cocoon/v1/click-events');
        $request->set_header('origin', home_url('/'));
        $request->set_header('sec-fetch-site', 'same-origin');
        $request->set_body(wp_json_encode(array(
            'batch_id' => 'custom-post-batch-' . $suffix,
            'session_id' => 'custom-post-session-' . $suffix,
            'source_post_id' => $postId,
            'layout_revision' => $revision,
            'sampling_rate' => 100,
            'device' => 'desktop',
            'token' => cocoon_click_tracking_token($postId, $revision, 100),
            'events' => array(array('type' => 'click', 'href' => 'https://outside.example.org/guide/', 'label' => '外部リンク', 'area' => 'content')),
        )));
        return cocoon_click_rest_receive_events($request);
    }

    /**
     * WordPressの個別公開設定に従う対象タイプと内部用途投稿の除外確認
     */
    public function testTrackablePostTypesFollowWordPressViewability(): void
    {
        $types = cocoon_click_trackable_post_types();
        foreach (array('post', 'page', 'click_public_type', 'click_queryable_type') as $type) {
            $this->assertContains($type, $types);
            $postId = $this->createTypedPost($type);
            $this->assertTrue(cocoon_click_is_trackable_post($postId));
            $this->assertTrue(cocoon_click_is_trackable_post(get_post($postId)));
        }
        foreach (array('click_hidden_type', 'attachment', 'revision', 'wp_block', 'nav_menu_item') as $type) {
            $this->assertNotContains($type, $types);
        }
    }

    /**
     * 公開カスタム投稿でのフロント計測と未公開・内部用途投稿の除外確認
     */
    public function testFrontendTrackingAcceptsPublishedCustomPostsAndKeepsExclusions(): void
    {
        $cases = array(
            array('post', 'publish', true),
            array('page', 'publish', true),
            array('click_public_type', 'publish', true),
            array('click_queryable_type', 'publish', true),
            array('click_hidden_type', 'publish', false),
            array('click_public_type', 'draft', false),
            array('click_public_type', 'private', false),
            array('attachment', 'publish', false),
            array('wp_block', 'publish', false),
        );
        foreach ($cases as [$type, $status, $expected]) {
            $postId = $this->createTypedPost($type, $status);
            // 実際の個別ページと同じ投稿オブジェクトを持つメインクエリーの準備
            $query = new \WP_Query();
            $query->is_singular = true;
            $query->queried_object = get_post($postId);
            $query->queried_object_id = $postId;
            $GLOBALS['wp_query'] = $query;
            $this->assertSame($expected, cocoon_click_should_enqueue(), $type . ':' . $status);
        }
    }

    /**
     * 投稿・固定ページ・公開カスタム投稿からのREST受信とクリック数保存の確認
     */
    public function testRestRecordsStandardAndCustomPublishedSources(): void
    {
        global $wpdb;
        foreach (array('post', 'page', 'click_public_type', 'click_queryable_type') as $type) {
            $postId = $this->createTypedPost($type);
            $response = $this->receiveForPost($postId, $type);
            $this->assertInstanceOf(\WP_REST_Response::class, $response, $type);
            $this->assertSame(204, $response->get_status());
            $clicks = $wpdb->get_var($wpdb->prepare('SELECT SUM(clicks) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '` WHERE source_post_id=%d AND link_id>0', $postId));
            $this->assertSame(1, (int) $clicks, $type);
        }
    }

    /**
     * 正しいトークンを持つ未公開・内部用途投稿からのREST受信拒否の確認
     */
    public function testRestRejectsUnpublishedHiddenAndInternalSources(): void
    {
        global $wpdb;
        foreach (array(array('click_hidden_type', 'publish'), array('click_public_type', 'draft'), array('click_public_type', 'private'), array('attachment', 'publish'), array('wp_block', 'publish')) as [$type, $status]) {
            $postId = $this->createTypedPost($type, $status);
            $response = $this->receiveForPost($postId, $type . '-' . $status);
            $this->assertInstanceOf(\WP_Error::class, $response, $type . ':' . $status);
            $this->assertSame('click_analytics_source', $response->get_error_code());
            $this->assertSame(400, $response->get_error_data()['status']);
        }
        $this->assertSame(0, (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '`'));
    }

    /**
     * 基本パーマリンクの公開カスタム投稿IDと非対象投稿IDの識別確認
     */
    public function testBasicPermalinkTargetsRespectTheSamePostRules(): void
    {
        foreach (array(array('click_public_type', 'publish', true), array('click_queryable_type', 'publish', true), array('click_hidden_type', 'publish', false), array('click_public_type', 'draft', false), array('attachment', 'publish', false)) as [$type, $status, $expected]) {
            $postId = $this->createTypedPost($type, $status);
            $destination = cocoon_click_normalize_destination(home_url('/?post_type=' . $type . '&p=' . $postId), home_url('/source/'));
            $this->assertSame('internal', $destination['type']);
            $this->assertSame($expected ? $postId : 0, $destination['target_post_id'], $type . ':' . $status);
        }
    }

    /**
     * WordPress標準の基本CPTリンクと別名・無効・階層クエリの公開ID化と実保存の確認
     */
    public function testGeneratedBasicCustomPermalinksPersistSafePublicIds(): void
    {
        $this->setPermalinkStructure('');
        $sourceId = $this->createTypedPost('post');
        $types = array(
            'click_route_basic' => array(),
            'click_route_alias' => array('query_var' => 'click_book'),
            'click_route_id' => array('query_var' => false),
            'click_route_tree' => array('query_var' => 'click_tree', 'hierarchical' => true),
        );
        foreach ($types as $type => $args) {
            $this->registerRoutingType($type, $args);
            $post = array('post_type' => $type, 'post_status' => 'publish', 'post_title' => '標準URL検証 ' . $type, 'post_name' => 'route-target');
            // 実際の親子パスと日本語スラッグを含む、WordPress生成URLの検証材料
            if (!empty($args['hierarchical'])) $post['post_parent'] = $this->createPost(array('post_type' => $type, 'post_status' => 'publish', 'post_title' => '親記事', 'post_name' => 'route-parent'));
            if ($type === 'click_route_alias') $post['post_name'] = '日本語スラッグ';
            $targetId = $this->createPost($post);
            $rawUrl = get_permalink($targetId);
            $this->assertIsString($rawUrl);
            $url = add_query_arg(array('token' => 'private-token', 'email' => 'private@example.invalid'), $rawUrl);
            $destination = cocoon_click_normalize_destination($url, get_permalink($sourceId));
            $expected = home_url('/?post_type=' . $type . '&p=' . $targetId);
            $this->assertSame($targetId, $destination['target_post_id'], $type);
            $this->assertSame($expected, $destination['display'], $type);
            $this->assertSame($expected, $destination['canonical'], $type);
            $row = $this->storeInternalDefinition($url, $sourceId);
            $this->assertSame($targetId, (int) $row['target_post_id'], $type);
            $this->assertSame($expected, $row['destination_url'], $type);
            $this->assertStringNotContainsString('private', $row['destination_url']);
        }
    }

    /**
     * 敏感キーと同名の合法なCPTクエリにおける記事間の識別とURL別名の統一確認
     */
    public function testSensitiveCustomQueryNamesKeepDistinctPublicPostIdentities(): void
    {
        $this->setPermalinkStructure('');
        $sourceId = $this->createTypedPost('post');
        $canonicals = array();
        $linkIds = array();
        foreach (array('click_route_code' => 'code', 'click_route_mail' => 'mail') as $type => $queryVar) {
            $this->registerRoutingType($type, array('query_var' => $queryVar));
            foreach (array('first', 'second') as $slug) {
                $targetId = $this->createPost(array('post_type' => $type, 'post_status' => 'publish', 'post_title' => $slug, 'post_name' => $slug));
                $slugUrl = get_permalink($targetId);
                $idUrl = home_url('/?post_type=' . $type . '&p=' . $targetId);
                $bySlug = cocoon_click_normalize_destination(add_query_arg('token', 'private-token', $slugUrl), get_permalink($sourceId));
                $byId = cocoon_click_normalize_destination($idUrl, get_permalink($sourceId));
                $this->assertSame($targetId, $bySlug['target_post_id']);
                $this->assertSame($idUrl, $bySlug['canonical']);
                $this->assertSame($byId['canonical'], $bySlug['canonical']);
                $this->assertSame($idUrl, $bySlug['display']);
                $this->assertStringNotContainsString('private-token', $bySlug['canonical']);
                $canonicals[] = $bySlug['canonical'];
                $row = $this->storeInternalDefinition($slugUrl, $sourceId);
                $linkIds[] = (int) $row['id'];
            }
        }
        $this->assertCount(4, array_unique($canonicals));
        $this->assertCount(4, array_unique($linkIds));
    }

    /**
     * 未公開・閲覧不可・無効スラッグ・添付ファイルをCPT公開リンク先と誤認しない動作の確認
     */
    public function testBasicCustomPermalinksRejectUnpublishedAndInvalidTargets(): void
    {
        global $wpdb;
        $this->setPermalinkStructure('');
        $this->createStaticFrontPage();
        $sourceId = $this->createTypedPost('post');
        $draftId = $this->createTypedPost('click_public_type', 'draft');
        $privateId = $this->createTypedPost('click_public_type', 'private');
        $hiddenId = $this->createTypedPost('click_hidden_type');
        $attachmentId = $this->createPost(array('post_type' => 'attachment', 'post_status' => 'publish', 'post_title' => '添付のみ', 'post_name' => 'attachment-only-route'));
        $urls = array(
            get_permalink($draftId), get_permalink($privateId), get_permalink($hiddenId),
            add_query_arg('click_public_type', get_post_field('post_name', $draftId), home_url('/')),
            add_query_arg('click_public_type', 'missing-route-target', home_url('/')),
            add_query_arg('click_public_type', get_post_field('post_name', $attachmentId), home_url('/')),
            add_query_arg('click_public_type', array('invalid-array'), home_url('/')),
            add_query_arg('p', array((string) $sourceId), home_url('/')),
            add_query_arg(array('p' => $sourceId, 'post_type' => array('post')), home_url('/')),
            home_url('/?click_public_type' . str_repeat('[nested]', 70) . '=invalid-array'),
        );
        $rows = array();
        foreach ($urls as $url) {
            $destination = cocoon_click_normalize_destination($url, get_permalink($sourceId));
            $this->assertSame(0, $destination['target_post_id'], $url);
            $row = $this->storeInternalDefinition($url, $sourceId);
            $this->assertSame(0, (int) $row['target_post_id']);
            $rows[] = (int) $row['id'];
        }
        $this->assertTrue(cocoon_click_enrich_internal_targets());
        $values = $wpdb->get_col('SELECT target_post_id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE id IN (' . implode(',', $rows) . ')');
        $this->assertCount(count($rows), $values);
        foreach ($values as $id) $this->assertSame(0, (int) $id);
    }

    /**
     * 値を失ったroot・index.phpの旧CPTリンクをフロントページへ誤補完しない動作の確認
     */
    public function testMaskedRootQueriesDoNotFallBackToTheStaticFrontPage(): void
    {
        global $wpdb;
        $this->setPermalinkStructure('');
        $frontId = $this->createStaticFrontPage();
        $sourceId = $this->createTypedPost('post');
        $this->registerRoutingType('click_route_alias', array('query_var' => 'click_book'));
        $rows = array();
        foreach (array('/?click_public_type', '/?click_book', '/index.php?click_book', '/?retired_click_type', '/?p', '/?page_id', '/?click_book=missing-route-target') as $path) {
            $row = $this->storeInternalDefinition(home_url($path), $sourceId, true);
            $rows[] = (int) $row['id'];
        }
        $this->assertTrue(cocoon_click_enrich_internal_targets());
        $values = $wpdb->get_col('SELECT target_post_id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE id IN (' . implode(',', $rows) . ')');
        $this->assertCount(count($rows), $values);
        foreach ($values as $id) $this->assertSame(0, (int) $id);
        $this->assertNotContains((string) $frontId, $values);
    }

    /**
     * 実際のフロントページ・投稿・固定ページ・ID形式CPTの旧リンク補完の継続確認
     */
    public function testCronKeepsTheFrontPageAndExistingBasicPostTargets(): void
    {
        global $wpdb;
        $this->setPermalinkStructure('');
        $frontId = $this->createStaticFrontPage();
        $sourceId = $this->createTypedPost('post');
        $postId = $this->createTypedPost('post');
        $pageId = $this->createTypedPost('page');
        $customId = $this->createTypedPost('click_public_type');
        $cases = array(
            array(home_url('/'), $frontId),
            array(home_url('/?p=' . $postId), $postId),
            array(home_url('/?page_id=' . $pageId), $pageId),
            array(home_url('/?post_type=click_public_type&p=' . $customId), $customId),
        );
        $rows = array();
        foreach ($cases as [$url, $expected]) {
            $row = $this->storeInternalDefinition($url, $sourceId, true);
            $rows[(int) $row['id']] = $expected;
        }
        $this->assertTrue(cocoon_click_enrich_internal_targets());
        foreach ($rows as $id => $expected) {
            $actual = $wpdb->get_var($wpdb->prepare('SELECT target_post_id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE id=%d', $id));
            $this->assertSame($expected, (int) $actual);
        }
    }

    /**
     * 既存投稿・固定ページにおける正規化URLと基本表示URLの維持確認
     */
    public function testPostAndPageCanonicalQueriesRemainUnchanged(): void
    {
        $this->setPermalinkStructure('');
        $sourceId = $this->createTypedPost('post');
        foreach (array('post' => 'p', 'page' => 'page_id') as $type => $key) {
            $targetId = $this->createTypedPost($type);
            $canonical = home_url('/?' . $key . '=' . $targetId . '&view=compact');
            $url = $canonical . '&token=private-token&utm_source=analytics';
            $destination = cocoon_click_normalize_destination($url, get_permalink($sourceId));
            $this->assertSame($targetId, $destination['target_post_id']);
            $this->assertSame($canonical, $destination['canonical']);
            $this->assertSame(home_url('/?' . $key . '=' . $targetId), $destination['display']);
        }
    }

    /**
     * 通常パーマリンクのカスタム投稿を内部リンク先として補完する動作の確認
     */
    public function testPrettyPermalinkTargetsAreEnrichedForPublishedCustomPosts(): void
    {
        global $wpdb, $wp_rewrite;
        $this->setPermalinkStructure('/%postname%/');
        // 通常パーマリンクの設定後の再登録によるカスタム投稿用URL構造の生成
        unregister_post_type('click_public_type');
        register_post_type('click_public_type', array('public' => true, 'rewrite' => array('slug' => 'click-public')));
        $sourceId = $this->createTypedPost('post');
        $targetId = $this->createTypedPost('click_public_type');
        // 登録直後のカスタム投稿のURL解決に必要なリライトルールの再生成
        $wp_rewrite->flush_rules(false);
        $targetUrl = get_permalink($targetId);
        $this->assertSame($targetId, url_to_postid($targetUrl));
        $postId = $this->createTypedPost('post');
        $pageId = $this->createTypedPost('page');
        $rows = array();
        foreach (array($targetId, $postId, $pageId) as $expected) {
            $url = get_permalink($expected);
            // マスク済み・無関係な配列クエリを含む、CPT・投稿・固定ページの通常URL補完対象
            foreach (array($url, add_query_arg('view', 'summary', $url), add_query_arg('filter', array('unrelated'), $url)) as $variant) {
                $row = $this->storeInternalDefinition($variant, $sourceId, true);
                $rows[(int) $row['id']] = $expected;
            }
        }
        $this->assertTrue(cocoon_click_enrich_internal_targets());
        foreach ($rows as $id => $expected) {
            $storedTarget = $wpdb->get_var($wpdb->prepare('SELECT target_post_id FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE id=%d', $id));
            $this->assertSame($expected, (int) $storedTarget);
        }
    }
}
