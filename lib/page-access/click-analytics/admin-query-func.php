<?php //クリック解析管理画面クエリ
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

define('COCOON_CLICK_ANALYTICS_TRANSIENT_PREFIX', 'cocoon_click_analytics_');

if ( !function_exists( 'cocoon_click_analytics_cached' ) ):
function cocoon_click_analytics_cached($key_parts, $producer){
  $generation = (string) get_option('cocoon_click_analytics_cache_generation', '1');
  $key = COCOON_CLICK_ANALYTICS_TRANSIENT_PREFIX . md5(wp_json_encode(array($generation, $key_parts)));
  $cached = get_transient($key);
  if ($cached !== false) return $cached;
  $value = call_user_func($producer);
  set_transient($key, $value, 15 * MINUTE_IN_SECONDS);
  return $value;
}
endif;

if ( !function_exists( 'cocoon_click_analytics_flush_cache' ) ):
function cocoon_click_analytics_flush_cache(){
  global $wpdb;
  // 永続オブジェクトキャッシュでも古い集計を参照しないよう、手動クリア時だけ世代を進めます。
  update_option('cocoon_click_analytics_cache_generation', (string) microtime(true), false);
  $like = $wpdb->esc_like('_transient_' . COCOON_CLICK_ANALYTICS_TRANSIENT_PREFIX) . '%';
  $like_timeout = $wpdb->esc_like('_transient_timeout_' . COCOON_CLICK_ANALYTICS_TRANSIENT_PREFIX) . '%';
  $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $like, $like_timeout));
}
endif;

if ( !function_exists( 'cocoon_click_stats_source_sql' ) ):
function cocoon_click_stats_source_sql($from, $to){
  $cutoff = max(cocoon_click_retention_cutoff(current_time('Y-m-d'), get_click_analytics_daily_retention()), (string) get_theme_option('click_analytics_daily_purged_before', ''));
  $status = get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_STATUS, array());
  $finalized = isset($status['finalized_through']) ? $status['finalized_through'] : '';
  $columns = 'source_post_id,link_id,device,layout_revision,clicks,unique_clicks,sampled_impressions,sampled_clicks,weighted_impressions,weighted_clicks,weight_squared,arrivals,engaged_arrivals,total_time_to_click_ms,received_events,rejected_events,accepted_batches,duplicate_batches';
  $daily = 'SELECT stat_date AS period_date,' . $columns . ' FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '` WHERE stat_date BETWEEN %s AND %s';
  $monthly = "SELECT CONCAT(stat_month,'-01') AS period_date," . $columns . ' FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '` WHERE stat_month BETWEEN %s AND %s';
  $parts = array();
  $args = array();
  $approximate = false;
  $direct_sql = '';
  $direct_where = '';
  // 日次と月次の保存範囲を別々に判定し、月次より長く残る日次も読み出します。
  $oldest_month = gmdate('Y-m', strtotime(current_time('Y-m-01') . ' -' . get_click_analytics_monthly_retention() . ' months'));
  if (!empty($status['covered_from'])) $oldest_month = max($oldest_month, $status['covered_from']);
  $monthly_from = max(substr($from, 0, 7), $oldest_month);
  $cutoff_month = substr($cutoff, 8, 2) === '01' ? gmdate('Y-m', strtotime($cutoff . ' -1 day')) : substr($cutoff, 0, 7);
  $monthly_to = min(substr($to, 0, 7), $finalized, $cutoff_month);
  if ($from >= $cutoff || $finalized === '' || $monthly_from > $monthly_to) {
    $parts[] = $daily;
    $args = array($from, $to);
    $direct_sql = '`' . CLICK_STATS_DAILY_TABLE_NAME . '`';
    $direct_where = 's.stat_date BETWEEN %s AND %s';
  } else {
    $month_start = $monthly_from . '-01';
    $month_end = gmdate('Y-m-t', strtotime($monthly_to . '-01'));
    if ($from < $month_start) {
      $parts[] = $daily;
      array_push($args, $from, gmdate('Y-m-d', strtotime($month_start . ' -1 day')));
    }
    $parts[] = $monthly;
    array_push($args, $monthly_from, $monthly_to);
    $approximate = $from > $month_start || $to < $month_end;
    if ($to > $month_end) {
      $parts[] = $daily;
      array_push($args, gmdate('Y-m-d', strtotime($month_end . ' +1 day')), $to);
    }
    if (count($parts) === 1) {
      $direct_sql = '`' . CLICK_STATS_MONTHLY_TABLE_NAME . '`';
      $direct_where = 's.stat_month BETWEEN %s AND %s';
    }
  }
  return array('sql' => '(' . implode(' UNION ALL ', $parts) . ')', 'args' => $args,
    'approximate' => $approximate, 'direct_sql' => $direct_sql, 'direct_where' => $direct_where);
}

endif;

if ( !function_exists( 'cocoon_click_analytics_min_date' ) ):
function cocoon_click_analytics_min_date(){
  return cocoon_click_analytics_cached(array('min_date'), function(){
    global $wpdb;
    $daily = $wpdb->get_var('SELECT MIN(stat_date) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`');
    $monthly = $wpdb->get_var('SELECT MIN(stat_month) FROM `' . CLICK_STATS_MONTHLY_TABLE_NAME . '`');
    $monthly_date = $monthly ? $monthly . '-01' : '';
    if ($daily && $monthly_date) return min($daily, $monthly_date);
    return $daily ? $daily : $monthly_date;
  });
}
endif;

if ( !function_exists( 'cocoon_click_filter_sql' ) ):
function cocoon_click_filter_sql($args, $include_links = true){
  $where = array();
  $values = array();
  $device = isset($args['device']) ? $args['device'] : 'all';
  if (in_array($device, array('mobile', 'tablet', 'desktop'), true)) {
    $where[] = 's.device=%s';
    $values[] = $device;
  }
  $source_post_id = isset($args['source_post_id']) ? (int) $args['source_post_id'] : 0;
  if ($source_post_id > 0) {
    $where[] = 's.source_post_id=%d';
    $values[] = $source_post_id;
  }
  $layout_revision = isset($args['layout_revision']) ? strtolower((string) $args['layout_revision']) : '';
  if (preg_match('/^[a-f0-9]{64}$/', $layout_revision)) {
    $where[] = 's.layout_revision=%s';
    $values[] = $layout_revision;
  }
  if ($include_links) {
    $area = isset($args['area']) ? sanitize_key($args['area']) : 'all';
    $areas = array('content', 'toc', 'blogcard', 'cta', 'related', 'header', 'navi', 'sidebar', 'footer', 'mobile_menu', 'other');
    if (in_array($area, $areas, true)) {
      $where[] = 'l.semantic_area=%s';
      $values[] = $area;
    }
    $link_type = isset($args['link_type']) ? sanitize_key($args['link_type']) : 'all';
    $types = array('internal', 'external', 'affiliate', 'official', 'reference', 'social', 'anchor', 'download', 'mailto', 'tel', 'sms');
    if (in_array($link_type, $types, true)) {
      $where[] = 'l.destination_type=%s';
      $values[] = $link_type;
    }
    $scope = isset($args['scope']) ? $args['scope'] : 'all';
    if ($scope === 'internal') {
      $where[] = "l.destination_type='internal'";
    } elseif ($scope === 'external') {
      $where[] = "l.destination_type<>'internal' AND l.destination_type<>'anchor'";
    } elseif ($scope === 'anchor') {
      $where[] = "l.destination_type='anchor'";
    }
  }
  return array('sql' => $where ? ' AND ' . implode(' AND ', $where) : '', 'args' => $values);
}
endif;

if ( !function_exists( 'cocoon_click_definition_filter_sql' ) ):
function cocoon_click_definition_filter_sql($args){
  $where = array();
  $values = array();
  $source_post_id = isset($args['source_post_id']) ? (int) $args['source_post_id'] : 0;
  if ($source_post_id > 0) {
    $where[] = 'l.source_post_id=%d';
    $values[] = $source_post_id;
  }
  $area = isset($args['area']) ? sanitize_key($args['area']) : 'all';
  $areas = array('content', 'toc', 'blogcard', 'cta', 'related', 'header', 'navi', 'sidebar', 'footer', 'mobile_menu', 'other');
  if (in_array($area, $areas, true)) {
    $where[] = 'l.semantic_area=%s';
    $values[] = $area;
  }
  $link_type = isset($args['link_type']) ? sanitize_key($args['link_type']) : 'all';
  $types = array('internal', 'external', 'affiliate', 'official', 'reference', 'social', 'anchor', 'download', 'mailto', 'tel', 'sms');
  if (in_array($link_type, $types, true)) {
    $where[] = 'l.destination_type=%s';
    $values[] = $link_type;
  }
  $scope = isset($args['scope']) ? $args['scope'] : 'all';
  if ($scope === 'internal') {
    $where[] = "l.destination_type='internal'";
  } elseif ($scope === 'external') {
    $where[] = "l.destination_type<>'internal' AND l.destination_type<>'anchor'";
  } elseif ($scope === 'anchor') {
    $where[] = "l.destination_type='anchor'";
  }
  return array('sql' => $where ? ' AND ' . implode(' AND ', $where) : '', 'args' => $values);
}
endif;

if ( !function_exists( 'cocoon_click_prepare_query' ) ):
function cocoon_click_prepare_query($sql, $args){
  global $wpdb;
  return $args ? $wpdb->prepare($sql, $args) : $sql;
}
endif;

if ( !function_exists( 'cocoon_click_metric_row' ) ):
function cocoon_click_metric_row($row){
  $defaults = array(
    'clicks' => 0, 'unique_clicks' => 0, 'sampled_impressions' => 0, 'sampled_clicks' => 0,
    'weighted_impressions' => 0, 'weighted_clicks' => 0, 'weight_squared' => 0,
    'arrivals' => 0, 'engaged_arrivals' => 0, 'total_time_to_click_ms' => 0,
  );
  $row = array_merge($defaults, (array) $row);
  foreach ($defaults as $key => $unused) $row[$key] = (int) $row[$key];
  $interval = cocoon_click_wilson_interval($row['weighted_clicks'], $row['weighted_impressions'], $row['weight_squared']);
  $row['ctr'] = $interval['rate'];
  $row['ctr_lower'] = $interval['lower'];
  $row['ctr_upper'] = $interval['upper'];
  $row['effective_n'] = $interval['effective_n'];
  $row['data_sufficient'] = cocoon_click_data_is_sufficient($row['effective_n'], $row['sampled_clicks']);
  $row['data_sufficiency_reasons'] = cocoon_click_data_sufficiency_reasons($row['effective_n'], $row['sampled_clicks']);
  // 外部クリックを内部到着率の分母に含めません。
  $internal_clicks = isset($row['internal_clicks']) ? (int) $row['internal_clicks'] : (isset($row['destination_type']) && $row['destination_type'] !== 'internal' ? 0 : $row['clicks']);
  $row['arrival_rate'] = $internal_clicks > 0 ? $row['arrivals'] / $internal_clicks : null;
  $row['engagement_rate'] = $row['arrivals'] > 0 ? $row['engaged_arrivals'] / $row['arrivals'] : null;
  $row['average_time_to_click_ms'] = $row['clicks'] > 0 ? $row['total_time_to_click_ms'] / $row['clicks'] : null;
  return $row;
}
endif;

if ( !function_exists( 'cocoon_click_analytics_metrics' ) ):
function cocoon_click_analytics_metrics($from, $to, $args = array()){
  return cocoon_click_analytics_cached(array('metrics', $from, $to, $args), function() use ($from, $to, $args){
    global $wpdb;
    $source = cocoon_click_stats_source_sql($from, $to);
    $filter = cocoon_click_filter_sql($args, true);
    $sql = "SELECT
      COALESCE(SUM(s.clicks),0) AS clicks,COALESCE(SUM(s.unique_clicks),0) AS unique_clicks,
      COALESCE(SUM(s.sampled_impressions),0) AS sampled_impressions,COALESCE(SUM(s.sampled_clicks),0) AS sampled_clicks,
      COALESCE(SUM(s.weighted_impressions),0) AS weighted_impressions,COALESCE(SUM(s.weighted_clicks),0) AS weighted_clicks,
      COALESCE(SUM(s.weight_squared),0) AS weight_squared,COALESCE(SUM(s.arrivals),0) AS arrivals,
      COALESCE(SUM(s.engaged_arrivals),0) AS engaged_arrivals,COALESCE(SUM(s.total_time_to_click_ms),0) AS total_time_to_click_ms,
      COALESCE(SUM(CASE WHEN l.destination_type='internal' THEN s.clicks ELSE 0 END),0) AS internal_clicks,
      COALESCE(SUM(CASE WHEN l.destination_type<>'internal' AND l.destination_type<>'anchor' THEN s.clicks ELSE 0 END),0) AS external_clicks
      FROM {$source['sql']} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE s.link_id>0{$filter['sql']}";
    $row = $wpdb->get_row(cocoon_click_prepare_query($sql, array_merge($source['args'], $filter['args'])), ARRAY_A);
    $result = cocoon_click_metric_row($row);
    $result['internal_clicks'] = isset($row['internal_clicks']) ? (int) $row['internal_clicks'] : 0;
    $result['external_clicks'] = isset($row['external_clicks']) ? (int) $row['external_clicks'] : 0;
    $result['approximate_period'] = $source['approximate'];
    return $result;
  });
}
endif;

if ( !function_exists( 'cocoon_click_analytics_pageviews' ) ):
function cocoon_click_analytics_pageviews($from, $to, $args = array()){
  global $wpdb;
  $source = cocoon_click_stats_source_sql($from, $to);
  $filter = cocoon_click_filter_sql($args, false);
  $source_sql = $source['direct_sql'] ? $source['direct_sql'] : $source['sql'];
  $period_where = $source['direct_where'] ? $source['direct_where'] . ' AND ' : '';
  $sql = "SELECT s.source_post_id,COALESCE(SUM(s.weighted_impressions),0) AS pageviews FROM {$source_sql} s WHERE {$period_where}s.link_id=0{$filter['sql']} GROUP BY s.source_post_id";
  $rows = $wpdb->get_results(cocoon_click_prepare_query($sql, array_merge($source['args'], $filter['args'])), ARRAY_A);
  $map = array();
  foreach ((array) $rows as $row) $map[(int) $row['source_post_id']] = (int) $row['pageviews'];
  return $map;
}
endif;

if ( !function_exists( 'cocoon_click_analytics_trend' ) ):
function cocoon_click_analytics_trend($from, $to, $args = array()){
  return cocoon_click_analytics_cached(array('trend', $from, $to, $args), function() use ($from, $to, $args){
    global $wpdb;
    $source = cocoon_click_stats_source_sql($from, $to);
    $filter = cocoon_click_filter_sql($args, true);
    $sql = "SELECT s.period_date AS date,SUM(s.clicks) AS clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.weighted_clicks) AS weighted_clicks,SUM(s.weight_squared) AS weight_squared
      FROM {$source['sql']} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE s.link_id>0{$filter['sql']} GROUP BY s.period_date ORDER BY s.period_date ASC";
    $rows = $wpdb->get_results(cocoon_click_prepare_query($sql, array_merge($source['args'], $filter['args'])), ARRAY_A);
    foreach ($rows as &$row) {
      $interval = cocoon_click_wilson_interval($row['weighted_clicks'], $row['weighted_impressions'], $row['weight_squared']);
      $row['clicks'] = (int) $row['clicks'];
      $row['ctr'] = $interval['rate'];
    }
    unset($row);
    return $rows;
  });
}
endif;

if ( !function_exists( 'cocoon_click_analytics_links_table' ) ):
function cocoon_click_analytics_links_table($from, $to, $args = array()){
  $defaults = array('page' => 1, 'per_page' => 25, 'group' => 'occurrence', 'scope' => 'all', 'order' => 'clicks');
  $args = array_merge($defaults, $args);
  $args['page'] = max(1, (int) $args['page']);
  $args['per_page'] = max(1, min(100, (int) $args['per_page']));
  return cocoon_click_analytics_cached(array('links_table', $from, $to, $args), function() use ($from, $to, $args){
    global $wpdb;
    $source = cocoon_click_stats_source_sql($from, $to);
    $filter = cocoon_click_filter_sql($args, true);
    $stats_filter = cocoon_click_filter_sql($args, false);
    $definition_filter = cocoon_click_definition_filter_sql($args);
    $groups = array(
      'occurrence' => array('expr' => 'l.id', 'key' => 'l.id'),
      'destination' => array('expr' => 'l.destination_key', 'key' => 'l.destination_key'),
      'domain' => array('expr' => 'l.destination_host', 'key' => 'l.destination_host'),
    );
    $group = isset($groups[$args['group']]) ? $groups[$args['group']] : $groups['occurrence'];
    // 少数データのCTRを上位へ出さないWilson下限での並べ替え
    $effective_n = '(POW(weighted_impressions,2)/NULLIF(weight_squared,0))';
    $wilson_lower = cocoon_click_wilson_lower_sql();
    $order_map = array('clicks' => 'clicks DESC', 'unique' => 'unique_clicks DESC', 'impressions' => 'weighted_impressions DESC', 'ctr' => "CASE WHEN sampled_clicks>=10 AND {$effective_n}>=100 THEN {$wilson_lower} ELSE -1 END DESC");
    $order = isset($order_map[$args['order']]) ? $order_map[$args['order']] : $order_map['clicks'];
    // 日次データだけの期間は派生表を作らず、主キーの日付範囲を直接走査します。
    $source_sql = $source['direct_sql'] ? $source['direct_sql'] : $source['sql'];
    $period_where = $source['direct_where'] ? $source['direct_where'] . ' AND ' : '';
    $base_where = "{$period_where}s.link_id>0{$filter['sql']}";
    $offset = ($args['page'] - 1) * $args['per_page'];
    $total_is_estimate = false;
    if ($args['group'] === 'occurrence') {
      $stats_where = "{$period_where}s.link_id>0{$stats_filter['sql']}";
      $stats_args = array_merge($source['args'], $stats_filter['args']);
      $metric_aggregate = "SELECT s.link_id,
        SUM(s.clicks) AS clicks,SUM(s.unique_clicks) AS unique_clicks,SUM(s.sampled_impressions) AS sampled_impressions,
        SUM(s.sampled_clicks) AS sampled_clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.weighted_clicks) AS weighted_clicks,
        SUM(s.weight_squared) AS weight_squared,SUM(s.arrivals) AS arrivals,SUM(s.engaged_arrivals) AS engaged_arrivals,
        SUM(s.total_time_to_click_ms) AS total_time_to_click_ms
        FROM {$source_sql} s WHERE {$stats_where} GROUP BY s.link_id";
      $device = isset($args['device']) ? $args['device'] : 'all';
      $layout_revision = isset($args['layout_revision']) ? strtolower((string) $args['layout_revision']) : '';
      $can_use_definition_count = !in_array($device, array('mobile', 'tablet', 'desktop'), true) && !preg_match('/^[a-f0-9]{64}$/', $layout_revision);
      if ($can_use_definition_count) {
        // ページング件数はリンクの観測期間で絞り、10万回の集計表ランダム参照を避けます。
        $count_sql = "SELECT COUNT(*) FROM `" . CLICK_LINKS_TABLE_NAME . "` l WHERE l.first_seen_at<=%s AND l.last_seen_at>=%s{$definition_filter['sql']}";
        $count_args = array_merge(array($to . ' 23:59:59', $from . ' 00:00:00'), $definition_filter['args']);
        $total = (int) $wpdb->get_var(cocoon_click_prepare_query($count_sql, $count_args));
        $total_is_estimate = true;
      } else {
        $count_sql = "SELECT COUNT(*) FROM ({$metric_aggregate}) click_metrics INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON click_metrics.link_id=l.id WHERE 1=1{$definition_filter['sql']}";
        $total = (int) $wpdb->get_var(cocoon_click_prepare_query($count_sql, array_merge($stats_args, $definition_filter['args'])));
      }
      $candidate_columns = array(
        'clicks' => 'SUM(s.clicks) AS clicks',
        'unique' => 'SUM(s.unique_clicks) AS unique_clicks',
        'impressions' => 'SUM(s.weighted_impressions) AS weighted_impressions',
        'ctr' => 'SUM(s.sampled_clicks) AS sampled_clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.weighted_clicks) AS weighted_clicks,SUM(s.weight_squared) AS weight_squared',
      );
      $candidate_column = isset($candidate_columns[$args['order']]) ? $candidate_columns[$args['order']] : $candidate_columns['clicks'];
      $candidate_index = $source['direct_sql'] === '`' . CLICK_STATS_MONTHLY_TABLE_NAME . '`' ? ' FORCE INDEX (`report_candidates`)' : '';
      // 並び順に必要な数値だけで候補を絞り、全指標の集計対象を最大100件に抑えます。
      $candidate_aggregate = "SELECT s.link_id,{$candidate_column} FROM {$source_sql} s{$candidate_index} WHERE {$stats_where} GROUP BY s.link_id";
      $candidate_sql = "SELECT candidate_metrics.link_id FROM ({$candidate_aggregate}) candidate_metrics
        INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON candidate_metrics.link_id=l.id
        WHERE 1=1{$definition_filter['sql']} ORDER BY {$order} LIMIT %d OFFSET %d";
      $candidate_args = array_merge($stats_args, $definition_filter['args'], array($args['per_page'], $offset));
      $candidate_ids = array_map('intval', (array) $wpdb->get_col(cocoon_click_prepare_query($candidate_sql, $candidate_args)));
      $rows = array();
      if ($candidate_ids) {
        $in = implode(',', array_fill(0, count($candidate_ids), '%d'));
        $details_sql = "SELECT l.id AS link_id,l.source_post_id,l.destination_url,l.destination_host,l.destination_type,l.target_post_id,
          l.semantic_area,l.heading_label,l.occurrence_no,l.anchor_text,l.image_url,l.element_type,l.is_affiliate,
          SUM(s.clicks) AS clicks,SUM(s.unique_clicks) AS unique_clicks,SUM(s.sampled_impressions) AS sampled_impressions,
          SUM(s.sampled_clicks) AS sampled_clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.weighted_clicks) AS weighted_clicks,
          SUM(s.weight_squared) AS weight_squared,SUM(s.arrivals) AS arrivals,SUM(s.engaged_arrivals) AS engaged_arrivals,
          SUM(s.total_time_to_click_ms) AS total_time_to_click_ms
          FROM {$source_sql} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id
          WHERE {$stats_where} AND s.link_id IN ({$in}) GROUP BY l.id";
        $detail_rows = $wpdb->get_results(cocoon_click_prepare_query($details_sql, array_merge($stats_args, $candidate_ids)), ARRAY_A);
        $row_map = array();
        foreach ((array) $detail_rows as $detail_row) $row_map[(int) $detail_row['link_id']] = $detail_row;
        foreach ($candidate_ids as $candidate_id) {
          if (isset($row_map[$candidate_id])) $rows[] = $row_map[$candidate_id];
        }
      }
    } else {
      if ($source['direct_sql']) {
      $count_sql = "SELECT COUNT(DISTINCT {$group['expr']}) FROM {$source_sql} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE {$base_where}";
      } else {
        $count_sql = "SELECT COUNT(*) FROM (SELECT {$group['expr']} FROM {$source_sql} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE {$base_where} GROUP BY {$group['key']}) click_groups";
      }
      $base_args = array_merge($source['args'], $filter['args']);
      $total = (int) $wpdb->get_var(cocoon_click_prepare_query($count_sql, $base_args));
      $aggregate_sql = "SELECT MIN(l.id) AS link_id,COUNT(DISTINCT l.source_post_id) AS source_count,
        COUNT(DISTINCT l.id) AS definition_count,
        SUM(CASE WHEN l.destination_type='internal' THEN s.clicks ELSE 0 END) AS internal_clicks,
        SUM(s.clicks) AS clicks,SUM(s.unique_clicks) AS unique_clicks,SUM(s.sampled_impressions) AS sampled_impressions,
        SUM(s.sampled_clicks) AS sampled_clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.weighted_clicks) AS weighted_clicks,
        SUM(s.weight_squared) AS weight_squared,SUM(s.arrivals) AS arrivals,SUM(s.engaged_arrivals) AS engaged_arrivals,
        SUM(s.total_time_to_click_ms) AS total_time_to_click_ms
        FROM {$source_sql} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE {$base_where}
        GROUP BY {$group['key']}";
      // 代表表示は1つの定義から取得し、複数行のラベル・URLを混ぜません。
      $sql = "SELECT grouped.*,representative.source_post_id,representative.destination_url,representative.destination_host,
        representative.destination_type,representative.target_post_id,representative.semantic_area,representative.heading_label,
        representative.occurrence_no,representative.anchor_text,representative.image_url,representative.element_type,representative.is_affiliate
        FROM ({$aggregate_sql}) grouped INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` representative ON grouped.link_id=representative.id
        ORDER BY {$order} LIMIT %d OFFSET %d";
      $query_args = array_merge($base_args, array($args['per_page'], $offset));
      $rows = $wpdb->get_results(cocoon_click_prepare_query($sql, $query_args), ARRAY_A);
    }
    $pageviews = cocoon_click_analytics_pageviews($from, $to, $args);
    foreach ($rows as &$row) {
      $row = cocoon_click_metric_row($row);
      $row['group'] = $args['group'];
      if (isset($row['source_count']) && (int) $row['source_count'] > 1) $row['source_post_id'] = 0;
      $source_post_id = (int) $row['source_post_id'];
      $row['reach_rate'] = isset($pageviews[$source_post_id]) && $pageviews[$source_post_id] > 0 ? min(1, $row['weighted_impressions'] / $pageviews[$source_post_id]) : null;
    }
    unset($row);
    return array('rows' => $rows, 'total' => $total, 'total_is_estimate' => $total_is_estimate, 'page' => $args['page'], 'per_page' => $args['per_page'], 'approximate_period' => $source['approximate']);
  });
}
endif;

if ( !function_exists( 'cocoon_click_percentile' ) ):
function cocoon_click_percentile($values, $percentile){
  $values = array_values(array_filter(array_map('floatval', $values), 'is_finite'));
  if (!$values) return null;
  sort($values, SORT_NUMERIC);
  $index = (int) floor((count($values) - 1) * max(0, min(1, $percentile)));
  return $values[$index];
}
endif;

if ( !function_exists( 'cocoon_click_wilson_lower_sql' ) ):
function cocoon_click_wilson_lower_sql($clicks = 'weighted_clicks', $impressions = 'weighted_impressions', $weight_squared = 'weight_squared'){
  $effective_n = "(POW({$impressions},2)/NULLIF({$weight_squared},0))";
  $rate = "LEAST(1,GREATEST(0,{$clicks}/NULLIF({$impressions},0)))";
  return "(({$rate}+3.8416/(2*NULLIF({$effective_n},0))-1.96*SQRT(({$rate}*(1-{$rate})/NULLIF({$effective_n},0))+3.8416/(4*POW(NULLIF({$effective_n},0),2))))/(1+3.8416/NULLIF({$effective_n},0)))";
}
endif;

if ( !function_exists( 'cocoon_click_analytics_benchmarks' ) ):
function cocoon_click_analytics_benchmarks($from, $to, $args = array()){
  return cocoon_click_analytics_cached(array('benchmarks', $from, $to, $args), function() use ($from, $to, $args){
    global $wpdb;
    $source = cocoon_click_stats_source_sql($from, $to);
    $filter = cocoon_click_filter_sql($args, true);
    $base_args = array_merge($source['args'], $filter['args']);
    // 画面に出る上位行だけでなく、対象リンク全体からの比較基準の作成
    $sql = "SELECT l.destination_type,l.semantic_area,SUM(s.weighted_clicks) AS weighted_clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.arrivals) AS arrivals,SUM(s.engaged_arrivals) AS engaged_arrivals
      FROM {$source['sql']} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE s.link_id>0{$filter['sql']} GROUP BY l.destination_type,l.semantic_area";
    $rows = $wpdb->get_results(cocoon_click_prepare_query($sql, $base_args), ARRAY_A);
    $groups = array();
    $arrivals = 0;
    $engaged = 0;
    foreach ((array) $rows as $row) {
      $key = $row['destination_type'] . '|' . $row['semantic_area'];
      $groups[$key] = (float) $row['weighted_impressions'] > 0 ? min(1, max(0, (float) $row['weighted_clicks'] / (float) $row['weighted_impressions'])) : null;
      $arrivals += (int) $row['arrivals'];
      $engaged += (int) $row['engaged_arrivals'];
    }
    $aggregate = "SELECT l.id,SUM(s.sampled_clicks) AS sampled_clicks,SUM(s.weighted_impressions) AS weighted_impressions,SUM(s.weighted_clicks) AS weighted_clicks,SUM(s.weight_squared) AS weight_squared
      FROM {$source['sql']} s INNER JOIN `" . CLICK_LINKS_TABLE_NAME . "` l ON s.link_id=l.id WHERE s.link_id>0 AND l.destination_type<>'internal' AND l.destination_type<>'anchor'{$filter['sql']} GROUP BY l.id";
    $effective_n = '(POW(weighted_impressions,2)/NULLIF(weight_squared,0))';
    $sufficient = "sampled_clicks>=10 AND {$effective_n}>=100";
    $count = (int) $wpdb->get_var(cocoon_click_prepare_query("SELECT COUNT(*) FROM ({$aggregate}) external_links WHERE {$sufficient}", $base_args));
    $external_cutoff = null;
    if ($count > 0) {
      $offset = (int) floor(($count - 1) * 0.75);
      $lower = cocoon_click_wilson_lower_sql();
      $cutoff_sql = "SELECT {$lower} AS ctr_lower FROM ({$aggregate}) external_links WHERE {$sufficient} ORDER BY ctr_lower ASC LIMIT 1 OFFSET %d";
      $external_cutoff = (float) $wpdb->get_var(cocoon_click_prepare_query($cutoff_sql, array_merge($base_args, array($offset))));
    }
    return array(
      'groups' => $groups,
      'engagement_rate' => $arrivals > 0 ? $engaged / $arrivals : null,
      'external_lower_quartile' => $external_cutoff,
      'external_sufficient_links' => $count,
    );
  });
}
endif;

if ( !function_exists( 'cocoon_click_analytics_insights' ) ):
function cocoon_click_analytics_insights($from, $to, $args = array()){
  $query_args = array_merge($args, array('page' => 1, 'per_page' => 100, 'group' => 'occurrence', 'order' => 'impressions'));
  $result = cocoon_click_analytics_links_table($from, $to, $query_args);
  $rows = $result['rows'];
  $population = cocoon_click_analytics_benchmarks($from, $to, $args);
  $benchmarks = $population['groups'];
  $engagement_benchmark = $population['engagement_rate'];
  $external_cutoff = $population['external_lower_quartile'];
  $insights = array('high_demand_low_reach' => array(), 'high_exposure_low_response' => array(), 'post_click_mismatch' => array(), 'external_candidates' => array());
  foreach ($rows as $row) {
    $key = $row['destination_type'] . '|' . $row['semantic_area'];
    $benchmark = array_key_exists($key, $benchmarks) ? $benchmarks[$key] : null;
    if ($row['data_sufficient'] && $benchmark !== null && $row['reach_rate'] !== null) {
      if ($row['ctr_lower'] >= $benchmark && $row['reach_rate'] < 0.5) $insights['high_demand_low_reach'][] = $row;
      if ($row['ctr_upper'] < $benchmark && $row['reach_rate'] >= 0.5) $insights['high_exposure_low_response'][] = $row;
    }
    if ($engagement_benchmark !== null && $row['arrivals'] >= 10 && $row['engagement_rate'] < $engagement_benchmark) $insights['post_click_mismatch'][] = $row;
    if ($external_cutoff !== null && $row['destination_type'] !== 'internal' && $row['data_sufficient'] && $row['ctr_lower'] >= $external_cutoff) $insights['external_candidates'][] = $row;
  }
  foreach ($insights as &$items) $items = array_slice($items, 0, 5);
  unset($items);
  return $insights;
}
endif;

if ( !function_exists( 'cocoon_click_analytics_heatmap' ) ):
function cocoon_click_analytics_heatmap($from, $to, $source_post_id, $device = 'desktop'){
  global $wpdb;
  $device = cocoon_click_sanitize_device($device);
  $sql = $wpdb->prepare(
    'SELECT layout_revision,x_bin,y_bin,SUM(clicks) AS clicks FROM `' . CLICK_HEATMAP_DAILY_TABLE_NAME . '` WHERE stat_date BETWEEN %s AND %s AND source_post_id=%d AND device=%s GROUP BY layout_revision,x_bin,y_bin ORDER BY clicks DESC',
    $from,
    $to,
    (int) $source_post_id,
    $device
  );
  return $wpdb->get_results($sql, ARRAY_A);
}
endif;

if ( !function_exists( 'cocoon_click_analytics_map_links' ) ):
function cocoon_click_analytics_map_links($from, $to, $source_post_id, $device = 'desktop', $layout_revision = ''){
  $result = cocoon_click_analytics_links_table($from, $to, array('source_post_id' => (int) $source_post_id, 'device' => $device, 'layout_revision' => $layout_revision, 'page' => 1, 'per_page' => 100, 'group' => 'occurrence', 'order' => 'clicks'));
  return $result['rows'];
}
endif;

if ( !function_exists( 'cocoon_click_analytics_health' ) ):
function cocoon_click_analytics_health(){
  global $wpdb;
  $tables_exist = cocoon_click_tables_exist(true);
  $last = $tables_exist ? $wpdb->get_var('SELECT MAX(updated_at) FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '`') : '';
  $database = defined('DB_NAME') ? DB_NAME : '';
  $bytes = 0;
  if ($database) {
    $tables = array(CLICK_LINKS_TABLE_NAME, CLICK_STATS_DAILY_TABLE_NAME, CLICK_STATS_MONTHLY_TABLE_NAME, CLICK_HEATMAP_DAILY_TABLE_NAME, CLICK_BATCHES_TABLE_NAME, CLICK_UNIQUES_TABLE_NAME, CLICK_LIMITS_TABLE_NAME);
    $in = implode(',', array_fill(0, count($tables), '%s'));
    $bytes = (int) $wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(data_length+index_length),0) FROM information_schema.tables WHERE table_schema=%s AND table_name IN ({$in})", array_merge(array($database), $tables)));
  }
  $received = 0;
  $rejected = 0;
  $accepted_batches = 0;
  $duplicate_batches = 0;
  if ($tables_exist) {
    $health_from = gmdate('Y-m-d', strtotime(current_time('Y-m-d') . ' -13 days'));
    $health_row = $wpdb->get_row($wpdb->prepare(
      'SELECT COALESCE(SUM(received_events),0) AS received_events,COALESCE(SUM(rejected_events),0) AS rejected_events,COALESCE(SUM(accepted_batches),0) AS accepted_batches,COALESCE(SUM(duplicate_batches),0) AS duplicate_batches FROM `' . CLICK_STATS_DAILY_TABLE_NAME . '` WHERE stat_date BETWEEN %s AND %s AND link_id=0',
      $health_from,
      current_time('Y-m-d')
    ), ARRAY_A);
    $received = (int) $health_row['received_events'];
    $rejected = (int) $health_row['rejected_events'];
    $accepted_batches = (int) $health_row['accepted_batches'];
    $duplicate_batches = (int) $health_row['duplicate_batches'];
  }
  $cache_health_available = function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache();
  if ($cache_health_available) {
    for ($offset = 0; $offset < 14; $offset++) {
      $date = gmdate('Y-m-d', strtotime(current_time('Y-m-d') . ' -' . $offset . ' days'));
      $rejected += (int) wp_cache_get('request_rejected|' . $date, 'cocoon_click_analytics_health');
    }
  }
  $today = current_time('Y-m-d');
  $current_from = gmdate('Y-m-d', strtotime($today . ' -6 days'));
  $previous_to = gmdate('Y-m-d', strtotime($current_from . ' -1 day'));
  $previous_from = gmdate('Y-m-d', strtotime($previous_to . ' -6 days'));
  $current_metrics = $tables_exist ? cocoon_click_analytics_metrics($current_from, $today) : cocoon_click_metric_row(array());
  $previous_metrics = $tables_exist ? cocoon_click_analytics_metrics($previous_from, $previous_to) : cocoon_click_metric_row(array());
  $maintenance = get_theme_option(OP_CLICK_ANALYTICS_MAINTENANCE_STATUS, array());
  $monthly = get_theme_option(OP_CLICK_ANALYTICS_MONTHLY_STATUS, array());
  $next_cron = wp_next_scheduled(COCOON_CLICK_CRON_HOOK);
  $last_maintenance = !empty($maintenance['completed_at']) ? strtotime($maintenance['completed_at']) : false;
  $cron_delay = $next_cron && $next_cron < time() ? time() - $next_cron : 0;
  if ($last_maintenance) $cron_delay = max($cron_delay, max(0, time() - $last_maintenance - DAY_IN_SECONDS));
  return array(
    'sampling_rate' => get_click_analytics_sampling_rate(),
    'sampling_updated' => get_theme_option(OP_CLICK_ANALYTICS_SAMPLING_UPDATED, ''),
    'last_ingested' => $last,
    'database_bytes' => $bytes,
    'next_cron' => $next_cron,
    'cron_delay' => $next_cron ? $cron_delay : null,
    'received_14days' => $received,
    'rejected_14days' => $rejected,
    'accepted_batches_14days' => $accepted_batches,
    'duplicate_batches_14days' => $duplicate_batches,
    'duplicate_rate' => ($accepted_batches + $duplicate_batches) > 0 ? $duplicate_batches / ($accepted_batches + $duplicate_batches) : null,
    'missing_rate' => ($received + $rejected) > 0 ? $rejected / ($received + $rejected) : null,
    'ctr_anomaly' => cocoon_click_detect_ctr_anomaly($current_metrics, $previous_metrics),
    'maintenance_status' => $maintenance,
    'monthly_status' => $monthly,
    'health_cache_available' => $cache_health_available,
  );
}
endif;

if ( !function_exists( 'cocoon_click_export_dataset' ) ):
function cocoon_click_export_dataset($target, $from, $to){
  // 全期間の書き出しでもメモリ上限を超えないよう、1ファイルの行数に上限を設けます。
  $max_rows = max(1000, (int) apply_filters('cocoon_click_analytics_export_max_rows', 50000));
  if ($target === 'click_daily') {
    $headers = array('date', 'clicks', 'estimated_impressions', 'estimated_sampled_clicks', 'ctr');
    $rows = array();
    foreach (cocoon_click_analytics_trend($from, $to) as $row) $rows[] = array($row['date'], $row['clicks'], $row['weighted_impressions'], $row['weighted_clicks'], $row['ctr']);
    return array('headers' => $headers, 'rows' => $rows);
  }
  if ($target === 'click_positions') {
    global $wpdb;
    $data = $wpdb->get_results($wpdb->prepare('SELECT stat_date,source_post_id,device,layout_revision,x_bin,y_bin,clicks FROM `' . CLICK_HEATMAP_DAILY_TABLE_NAME . '` WHERE stat_date BETWEEN %s AND %s ORDER BY stat_date,source_post_id LIMIT %d', $from, $to, $max_rows), ARRAY_N);
    return array('headers' => array('date', 'source_post_id', 'device', 'layout_revision', 'x_bin', 'y_bin', 'clicks'), 'rows' => $data);
  }
  $scope = $target === 'click_internal' ? 'internal' : 'external';
  $group = $target === 'click_domains' ? 'domain' : ($target === 'click_external' ? 'destination' : 'occurrence');
  $headers = array('source_post_id', 'destination_url', 'domain', 'type', 'area', 'heading', 'label', 'impressions', 'clicks', 'unique_clicks', 'ctr', 'arrivals', 'engaged_arrivals', 'group', 'source_count', 'representative_link');
  $rows = array();
  $page = 1;
  do {
    $result = cocoon_click_analytics_links_table($from, $to, array('scope' => $scope, 'group' => $group, 'page' => $page, 'per_page' => 100, 'order' => 'clicks'));
    foreach ($result['rows'] as $row) {
      $rows[] = array($row['source_post_id'], $row['destination_url'], $row['destination_host'], $row['destination_type'], $row['semantic_area'], $row['heading_label'], $row['anchor_text'], $row['weighted_impressions'], $row['clicks'], $row['unique_clicks'], $row['ctr'], $row['arrivals'], $row['engaged_arrivals'], $group, isset($row['source_count']) ? $row['source_count'] : 1, !empty($row['definition_count']) && $row['definition_count'] > 1 ? 1 : 0);
    }
    $page++;
  } while ($result['rows'] && (($page - 1) * 100) < $result['total'] && count($rows) < $max_rows);
  return array('headers' => $headers, 'rows' => array_slice($rows, 0, $max_rows));
}
endif;
