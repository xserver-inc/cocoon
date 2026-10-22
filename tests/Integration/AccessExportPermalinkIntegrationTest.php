<?php
/**
 * 接続の取得上限とキャッシュ状態に依存しない、書き出しURLの実DB検証
 */
namespace Cocoon\Tests\Integration;

class AccessExportPermalinkIntegrationTest extends IntegrationTestCase
{
    /**
     * 取得上限・キャッシュ有無・追加停止状態に依存しない、親子カテゴリーと投稿者付きURLの確認
     */
    public function testCategoryAndAuthorPermalinksSurviveEverySelectLimitAndCacheState(): void
    {
        global $wpdb;
        $originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1');
        $originalSuspended = wp_suspend_cache_addition();
        $root = $this->createCategory(array('name' => '書出し祖先', 'slug' => 'export-root'));
        $parent = $this->createCategory(array('name' => '書出し親', 'slug' => 'export-parent', 'parent' => $root));
        $child = $this->createCategory(array('name' => 'Z書出し子', 'slug' => 'export-child', 'parent' => $parent));
        $other = $this->createCategory(array('name' => 'A書出し別', 'slug' => 'export-other'));
        $authorId = (int) $wpdb->get_var("SELECT ID FROM {$wpdb->users} ORDER BY ID LIMIT 1");
        $this->assertGreaterThan(0, $authorId);
        $author = get_userdata($authorId);
        $this->assertInstanceOf(\WP_User::class, $author);
        $postId = $this->createPost(array(
            'post_status' => 'publish', 'post_type' => 'post', 'post_title' => '親子カテゴリーURL',
            'post_name' => 'export-category-post', 'post_author' => $authorId,
        ));
        $this->assertNotWPError(wp_set_post_categories($postId, array($child, $other)));
        $this->setPermalinkStructure('/%category%/%author%/%postname%/');
        $expected = home_url('/export-root/export-parent/export-child/' . $author->user_nicename . '/export-category-post/');
        $this->assertSame($expected, get_permalink($postId));
        $record = array('post_id' => $postId, 'post_type' => 'post', 'pv' => 7);
        try {
            foreach (array(0, 1, 10) as $limit) {
                foreach (array(false, true) as $warm) {
                    foreach (array(false, true) as $suspended) {
                        // 各条件の独立した、投稿・関連カテゴリー・祖先・投稿者キャッシュの準備
                        $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
                        wp_suspend_cache_addition(false);
                        clean_post_cache($postId);
                        // 階層の再構築による再キャッシュを避けた、個別タームキャッシュだけの削除
                        clean_term_cache(array($root, $parent, $child, $other), 'category', false);
                        clean_user_cache($authorId);
                        if ($warm) $this->assertSame($expected, get_permalink($postId));
                        if (!$warm) {
                            $this->assertFalse(wp_cache_get($postId, 'posts'));
                            foreach (array($root, $parent, $child, $other) as $termId) {
                                $this->assertFalse(wp_cache_get($termId, 'terms'));
                            }
                        }
                        wp_suspend_cache_addition($suspended);
                        $termsFilters = $this->filterCallbacks('get_terms_args');
                        $clausesFilters = $this->filterCallbacks('terms_clauses');
                        $queryFilters = $this->filterCallbacks('query');
                        $this->assertNotFalse($wpdb->query('SET SESSION sql_select_limit=' . $limit));
                        $row = cocoon_analytics_export_ranking_row($record);
                        $context = 'limit=' . $limit . ', warm=' . (int) $warm . ', suspended=' . (int) $suspended;
                        $this->assertSame(array($postId, 'post', '親子カテゴリーURL', 7, $expected), $row, $context);
                        $this->assertSame((string) $limit, (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'), $context);
                        $this->assertSame($suspended, wp_suspend_cache_addition(), $context);
                        $this->assertSame($termsFilters, $this->filterCallbacks('get_terms_args'), $context);
                        $this->assertSame($clausesFilters, $this->filterCallbacks('terms_clauses'), $context);
                        $this->assertSame($queryFilters, $this->filterCallbacks('query'), $context);
                        if (!$warm) {
                            // 全記事の反復後にも蓄積させない、投稿本文とターム検索結果のキャッシュ抑制
                            $this->assertFalse(wp_cache_get($postId, 'posts'), $context);
                            foreach (array($root, $parent, $child, $other) as $termId) {
                                $this->assertFalse(wp_cache_get($termId, 'terms'), $context);
                            }
                        }
                        $this->assertSame('', $wpdb->last_error, $context);
                    }
                }
            }
        } finally {
            $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
            wp_suspend_cache_addition($originalSuspended);
        }
    }

    /**
     * カテゴリー参照を含むタイトルフィルターとURL生成の両方で、完全な分類情報を利用することの確認
     */
    public function testCategoryDependentTitleFiltersUseTheSameCompleteTermData(): void
    {
        global $wpdb;
        $originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1');
        $parent = $this->createCategory(array('name' => 'タイトル親', 'slug' => 'export-title-parent'));
        $child = $this->createCategory(array('name' => 'タイトル子', 'slug' => 'export-title-child', 'parent' => $parent));
        $postId = $this->createPost(array('post_status' => 'publish', 'post_title' => '分類付きタイトル', 'post_name' => 'export-title-post'));
        $this->assertNotWPError(wp_set_post_categories($postId, array($child)));
        $this->setPermalinkStructure('/%category%/%postname%/');
        $categoryTitle = static function($title, $id) use ($postId){
            if ((int) $id !== $postId) return $title;
            $categories = get_the_category($postId);
            return $title . ':' . ($categories ? $categories[0]->slug : '欠損');
        };
        add_filter('the_title', $categoryTitle, 10, 2);
        try {
            foreach (array(0, 1, 10) as $limit) {
                // URL生成前のタイトル変換にも対応する、冷えたカテゴリーキャッシュからの検証
                $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
                clean_post_cache($postId);
                clean_term_cache(array($parent, $child), 'category', false);
                $this->assertFalse(wp_cache_get($parent, 'terms'));
                $this->assertFalse(wp_cache_get($child, 'terms'));
                $this->assertNotFalse($wpdb->query('SET SESSION sql_select_limit=' . $limit));
                $row = cocoon_analytics_export_ranking_row(array('post_id' => $postId, 'post_type' => 'post', 'pv' => 2));
                $this->assertSame('分類付きタイトル:export-title-child', $row[2]);
                $this->assertSame(home_url('/export-title-parent/export-title-child/export-title-post/'), $row[4]);
                $this->assertSame((string) $limit, (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'));
            }
        } finally {
            remove_filter('the_title', $categoryTitle);
            $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
        }
    }

    /**
     * 関連付けのない投稿でも、取得上限0を含めた既定カテゴリーURLの維持の確認
     */
    public function testUnassignedPostsKeepTheirDefaultCategoryWithColdCaches(): void
    {
        global $wpdb;
        $originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1');
        $originalDefault = get_option('default_category');
        $defaultId = $this->createCategory(array('name' => '書出し既定', 'slug' => 'export-default'));
        $postId = $this->createPost(array('post_status' => 'publish', 'post_title' => '既定カテゴリーURL', 'post_name' => 'export-default-post'));
        $this->assertNotWPError(wp_set_object_terms($postId, array(), 'category'));
        $this->setPermalinkStructure('/%category%/%postname%/');
        try {
            update_option('default_category', $defaultId);
            $expected = home_url('/export-default/export-default-post/');
            $this->assertSame($expected, get_permalink($postId));
            foreach (array(0, 1, 10) as $limit) {
                $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
                clean_post_cache($postId);
                clean_term_cache($defaultId, 'category', false);
                $this->assertFalse(wp_cache_get($defaultId, 'terms'));
                $this->assertNotFalse($wpdb->query('SET SESSION sql_select_limit=' . $limit));
                $row = cocoon_analytics_export_ranking_row(array('post_id' => $postId, 'post_type' => 'post', 'pv' => 1));
                $this->assertSame($expected, $row[4]);
                $this->assertSame((string) $limit, (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'));
            }
        } finally {
            $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
            update_option('default_category', $originalDefault);
        }
    }

    /**
     * SQL文字列内のLIMIT・既存上限・独自SQLを変更しない、標準ターム検索だけの上限補完の確認
     */
    public function testOnlyUnboundedTermSelectsAreAdjustedDuringPermalinkGeneration(): void
    {
        global $wpdb;
        $originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1');
        $termId = $this->createCategory(array('name' => '検索範囲の確認', 'slug' => 'export-query-scope'));
        $postId = $this->createPost(array('post_status' => 'publish', 'post_title' => '限定SQL', 'post_name' => 'export-sql-post'));
        $this->assertNotWPError(wp_set_post_categories($postId, array($termId)));
        $this->setPermalinkStructure('/%category%/%postname%/');
        $termQuery = $wpdb->prepare("SELECT t.term_id FROM {$wpdb->terms} AS t WHERE t.term_id=%d", $termId);
        $observed = array();
        $inspect = static function($url) use ($wpdb, $termQuery, $termId, &$observed){
            $observed['unrelated'] = $wpdb->get_var('SELECT 17');
            $observed['explicit_zero'] = $wpdb->get_col($termQuery . ' LIMIT 0');
            $observed['explicit_one'] = $wpdb->get_col($termQuery . ' LIMIT 1');
            $observed['unrelated_term_sql'] = $wpdb->get_col($termQuery);
            $args = array('taxonomy' => 'category', 'include' => array($termId), 'hide_empty' => false, 'hierarchical' => false, 'fields' => 'ids');
            $observed['explicit_terms'] = get_terms(array_merge($args, array('number' => 1)));
            $observed['unbounded_terms'] = get_terms($args);
            wp_cache_delete($termId, 'terms');
            $observed['direct_lookup'] = get_term($termId, 'category');
            return $url;
        };
        // SQLリテラル内のLIMITと取得上限句を取り違えない、実ターム検索への条件追加
        $limitLiteral = static function($clauses){
            $clauses['where'] .= " AND 'LIMIT 1'='LIMIT 1'";
            return $clauses;
        };
        add_filter('terms_clauses', $limitLiteral);
        add_filter('post_link', $inspect);
        $queryFilters = $this->filterCallbacks('query');
        try {
            $wpdb->query('SET SESSION sql_select_limit=0');
            $row = cocoon_analytics_export_ranking_row(array('post_id' => $postId, 'post_type' => 'post', 'pv' => 1));
            $this->assertSame(home_url('/export-query-scope/export-sql-post/'), $row[4]);
            $this->assertNull($observed['unrelated']);
            $this->assertSame(array(), $observed['explicit_zero']);
            $this->assertSame(array((string) $termId), $observed['explicit_one']);
            $this->assertSame(array(), $observed['unrelated_term_sql']);
            $this->assertSame(array($termId), $observed['explicit_terms']);
            $this->assertSame(array($termId), $observed['unbounded_terms']);
            $this->assertInstanceOf(\WP_Term::class, $observed['direct_lookup']);
            $this->assertSame($termId, $observed['direct_lookup']->term_id);
            $this->assertSame(array(), $wpdb->get_col($termQuery));
            $this->assertSame($queryFilters, $this->filterCallbacks('query'));
            $this->assertSame('0', (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'));
        } finally {
            remove_filter('post_link', $inspect);
            remove_filter('terms_clauses', $limitLiteral);
            $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
        }
    }

    /**
     * タイトルとURLの生成例外後にも、すべてのフィルター・キャッシュ追加設定・接続上限の維持の確認
     */
    public function testRowExceptionsRestoreAllFiltersAndTheCacheSetting(): void
    {
        global $wpdb;
        $originalLimit = (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1');
        $originalSuspended = wp_suspend_cache_addition();
        $postId = $this->createPost(array('post_status' => 'publish', 'post_title' => 'URL例外時の復元'));
        $fail = static function(){
            throw new \RuntimeException('URL生成の試験例外');
        };
        try {
            foreach (array('the_title', 'post_link') as $hook) {
                add_filter($hook, $fail);
                try {
                    foreach (array(false, true) as $suspended) {
                        wp_suspend_cache_addition($suspended);
                        $termsFilters = $this->filterCallbacks('get_terms_args');
                        $clausesFilters = $this->filterCallbacks('terms_clauses');
                        $queryFilters = $this->filterCallbacks('query');
                        $wpdb->query('SET SESSION sql_select_limit=0');
                        try {
                            cocoon_analytics_export_ranking_row(array('post_id' => $postId, 'post_type' => 'post', 'pv' => 1));
                            $this->fail('行の生成例外が呼び出し元へ通知されていません。');
                        } catch (\RuntimeException $error) {
                            $this->assertSame('URL生成の試験例外', $error->getMessage());
                        }
                        $this->assertSame($suspended, wp_suspend_cache_addition());
                        $this->assertSame($termsFilters, $this->filterCallbacks('get_terms_args'));
                        $this->assertSame($clausesFilters, $this->filterCallbacks('terms_clauses'));
                        $this->assertSame($queryFilters, $this->filterCallbacks('query'));
                        $this->assertSame('0', (string) $wpdb->get_var('SELECT @@SESSION.sql_select_limit LIMIT 1'));
                    }
                } finally {
                    remove_filter($hook, $fail);
                }
            }
        } finally {
            $wpdb->query('SET SESSION sql_select_limit=' . $originalLimit);
            wp_suspend_cache_addition($originalSuspended);
        }
    }

    private function filterCallbacks(string $hook): array
    {
        global $wp_filter;
        return isset($wp_filter[$hook]) ? $wp_filter[$hook]->callbacks : array();
    }

    private function assertNotWPError($value): void
    {
        $this->assertFalse(is_wp_error($value), is_wp_error($value) ? $value->get_error_message() : '');
    }
}
