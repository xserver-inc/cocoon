<?php
/**
 * クリック解析の大規模実データベース性能テスト
 */

namespace Cocoon\Tests\Integration;

class ClickAnalyticsPerformanceIntegrationTest extends IntegrationTestCase
{
    public function testThirtyDayReportWithOneMillionRows(): void
    {
        if (getenv('COCOON_CLICK_LARGE_BENCHMARK') !== '1') {
            $this->markTestSkipped('大規模DBベンチマークは明示実行時だけ行います。');
        }
        global $wpdb;
        create_click_analytics_tables();
        cocoon_click_delete_all_data();
        // WordPressテスト基盤の一時テーブル制約を避け、0～9を独立した派生表として生成します。
        $digits = '(SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9)';
        $number = '(a.n+b.n*10+c.n*100+d.n*1000+e.n*10000)';
        $now = esc_sql(current_time('mysql'));
        $closedMonthStart = gmdate('Y-m-01', strtotime(current_time('Y-m-01') . ' -1 month'));
        $closedMonthEnd = gmdate('Y-m-t', strtotime($closedMonthStart));
        $firstSeenAt = esc_sql($closedMonthStart . ' 00:00:00');
        $lastSeenAt = esc_sql($closedMonthEnd . ' 23:59:59');
        // 初心者向け: 10個の数字表を掛け合わせ、PHPの巨大配列を作らず10万リンクをDB内で生成します。
        $wpdb->query("INSERT INTO `" . CLICK_LINKS_TABLE_NAME . "`
            (link_key,slot_key,source_post_id,destination_key,destination_url,destination_host,destination_type,target_post_id,semantic_area,heading_key,heading_label,occurrence_no,anchor_text,element_type,rel_flags,target_blank,is_affiliate,first_seen_at,last_seen_at)
            SELECT SHA2(CONCAT('link|',{$number}),256),SHA2(CONCAT('slot|',{$number}),256),MOD({$number},1000)+1,SHA2(CONCAT('destination|',{$number}),256),
              CONCAT('https://outside.example/item/',{$number}),'outside.example','external',0,'content','', '',MOD({$number},100),'benchmark','text','',0,0,'{$firstSeenAt}','{$lastSeenAt}'
            FROM {$digits} a CROSS JOIN {$digits} b CROSS JOIN {$digits} c CROSS JOIN {$digits} d CROSS JOIN {$digits} e");
        $revision = str_repeat('a', 64);
        // 初心者向け: 10万リンク×10日をDB内で展開し、100万件の日次集計を生成します。
        $wpdb->query("INSERT INTO `" . CLICK_STATS_DAILY_TABLE_NAME . "`
            (stat_date,source_post_id,link_id,device,layout_revision,clicks,unique_clicks,sampled_impressions,sampled_clicks,weighted_impressions,weighted_clicks,weight_squared,arrivals,engaged_arrivals,total_time_to_click_ms,received_events,rejected_events,accepted_batches,duplicate_batches,updated_at)
            SELECT DATE_ADD('{$closedMonthStart}',INTERVAL days.n DAY),links.source_post_id,links.id,'desktop','{$revision}',1,1,2,1,2,1,2,0,0,500,0,0,0,0,'{$now}'
            FROM `" . CLICK_LINKS_TABLE_NAME . "` links CROSS JOIN {$digits} days");
        $wpdb->query("INSERT INTO `" . CLICK_STATS_DAILY_TABLE_NAME . "`
            (stat_date,source_post_id,link_id,device,layout_revision,clicks,unique_clicks,sampled_impressions,sampled_clicks,weighted_impressions,weighted_clicks,weight_squared,arrivals,engaged_arrivals,total_time_to_click_ms,received_events,rejected_events,accepted_batches,duplicate_batches,updated_at)
            SELECT DATE_ADD('{$closedMonthStart}',INTERVAL days.n DAY),links.source_post_id,0,'desktop','{$revision}',0,0,100,0,100,0,100,0,0,0,0,0,0,0,'{$now}'
            FROM (SELECT DISTINCT source_post_id FROM `" . CLICK_LINKS_TABLE_NAME . "`) links CROSS JOIN {$digits} days");

        // 実運用と同じ確定データの索引統計で測るため、大量投入を確定して統計情報を更新します。
        $wpdb->query('COMMIT');
        $wpdb->query('ANALYZE TABLE `' . CLICK_LINKS_TABLE_NAME . '`, `' . CLICK_STATS_DAILY_TABLE_NAME . '`');
        $this->assertSame(1010000, (int) $wpdb->get_var('SELECT COUNT(*) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`'));
        $rollup = cocoon_click_rollup_monthly();
        $this->assertSame('success', $rollup['status']);
        $wpdb->query('ANALYZE TABLE `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`');

        $from = $closedMonthStart;
        $to = $closedMonthEnd;
        cocoon_click_analytics_flush_cache();
        $started = hrtime(true);
        $uncached = cocoon_click_analytics_links_table($from, $to, array('scope' => 'external', 'page' => 1, 'per_page' => 25));
        $uncachedMs = (hrtime(true) - $started) / 1000000;
        $started = hrtime(true);
        $cached = cocoon_click_analytics_links_table($from, $to, array('scope' => 'external', 'page' => 1, 'per_page' => 25));
        $cachedMs = (hrtime(true) - $started) / 1000000;

        $this->assertSame(100000, $uncached['total']);
        $this->assertSame($uncached, $cached);
        $this->assertLessThanOrEqual(500, $uncachedMs, '未キャッシュ集計が500msを超えました: ' . $uncachedMs . 'ms');
        $this->assertLessThanOrEqual(100, $cachedMs, 'キャッシュ済み集計が100msを超えました: ' . $cachedMs . 'ms');
        cocoon_click_delete_all_data();
    }
}
