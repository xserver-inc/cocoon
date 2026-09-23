<?php //クリック解析REST受信
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('rest_api_init', 'cocoon_click_register_rest_routes');

if ( !function_exists( 'cocoon_click_register_rest_routes' ) ):
function cocoon_click_register_rest_routes(){
  // 未ログイン訪問者とキャッシュ配信ページからも受信するため、権限判定ではなくトークン・Origin・レート制限で保護します。
  // トークンは公開ページのHTMLに含まれるため、偽の計測データ送信を完全には防げません。改ざん耐性より計測の網羅性を優先した設計です。
  register_rest_route('cocoon/v1', '/click-events', array(
    'methods' => 'POST',
    'callback' => 'cocoon_click_rest_receive_events',
    'permission_callback' => '__return_true',
  ));
}
endif;

if ( !function_exists( 'cocoon_click_rest_error' ) ):
function cocoon_click_rest_error($code, $message, $status){
  cocoon_click_health_increment('request_rejected', 1);
  return new WP_Error($code, $message, array('status' => (int) $status));
}
endif;

if ( !function_exists( 'cocoon_click_health_increment' ) ):
function cocoon_click_health_increment($metric, $amount = 1){
  $amount = max(0, (int) $amount);
  if ($metric !== 'request_rejected' || !$amount || !is_click_analytics_enable()) return;
  $key = $metric . '|' . current_time('Y-m-d');
  if (function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache()) {
    if (wp_cache_add($key, $amount, 'cocoon_click_analytics_health', 15 * DAY_IN_SECONDS)) return;
    if (wp_cache_incr($key, $amount, 'cocoon_click_analytics_health') !== false) return;
  }
  // 拒否理由やIPで行数を増やさない、日付ごとに1行の代替記録
  if (!cocoon_click_tables_exist()) return;
  global $wpdb;
  $previous = $wpdb->suppress_errors(true);
  try {
    $wpdb->query($wpdb->prepare('INSERT INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES (%s,%d,%s) ON DUPLICATE KEY UPDATE request_count=request_count+VALUES(request_count)', cocoon_click_hmac('health|' . $key), $amount, gmdate('Y-m-d H:i:s', time() + 15 * DAY_IN_SECONDS)));
  } finally {
    $wpdb->suppress_errors($previous);
  }
}
endif;

if ( !function_exists( 'cocoon_click_rejected_requests_count' ) ):
function cocoon_click_rejected_requests_count($tables_exist){
  global $wpdb;
  if (!$tables_exist) return null;
  $keys = array();
  $cached = 0;
  for ($offset = 0; $offset < 14; $offset++) {
    $date = gmdate('Y-m-d', strtotime(current_time('Y-m-d') . ' -' . $offset . ' days'));
    $key = 'request_rejected|' . $date;
    $keys[] = cocoon_click_hmac('health|' . $key);
    $cached += (int) wp_cache_get($key, 'cocoon_click_analytics_health');
  }
  // キャッシュの有効・無効切り替え前後の履歴も含めた合計
  $count = $wpdb->get_var($wpdb->prepare('SELECT COALESCE(SUM(request_count),0) FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key IN (' . implode(',', array_fill(0, count($keys), '%s')) . ')', $keys));
  return $count === null || $wpdb->last_error ? null : (int) $count + $cached;
}
endif;

if ( !function_exists( 'cocoon_click_request_origin_is_valid' ) ):
function cocoon_click_request_origin_is_valid($request){
  $fetch_site = strtolower((string) $request->get_header('sec-fetch-site'));
  if ($fetch_site !== '' && !in_array($fetch_site, array('same-origin', 'same-site'), true)) return false;
  $origin = (string) $request->get_header('origin');
  if ($origin === '') return true;
  $origin_host = strtolower((string) wp_parse_url($origin, PHP_URL_HOST));
  return $origin_host !== '' && cocoon_click_is_internal_host($origin_host);
}
endif;

if ( !function_exists( 'cocoon_click_rate_limit_allows' ) ):
function cocoon_click_rate_limit_allows($session_id){
  $minute = (int) floor(time() / 60);
  $remote = cocoon_click_rate_limit_ip();
  $network = cocoon_click_network_bucket($remote);
  // サイト全体から順に制限し、セッションIDを変える大量送信でも一時行を増やし続けません。
  $checks = array(
    array('site|' . $minute, max(1, (int) apply_filters('cocoon_click_analytics_requests_per_minute', 6000))),
    array('network|' . $minute . '|' . $network, 600),
    array('session|' . $minute . '|' . $session_id, 30),
  );
  foreach ($checks as $check) {
    $key = cocoon_click_hmac('rate|' . $check[0]);
    if (function_exists('wp_using_ext_object_cache') && wp_using_ext_object_cache()) {
      if (wp_cache_add($key, 1, 'cocoon_click_analytics', 120)) continue;
      $count = wp_cache_incr($key, 1, 'cocoon_click_analytics');
      if ($count !== false) {
        if ((int) $count > $check[1]) return false;
        continue;
      }
    }
    // 永続キャッシュがない環境でも、DB上で原子的に加算して制限を適用します。
    global $wpdb;
    $sql = $wpdb->prepare('INSERT INTO `' . CLICK_LIMITS_TABLE_NAME . '` (limit_key,request_count,expires_at) VALUES (%s,1,%s) ON DUPLICATE KEY UPDATE request_count=request_count+1', $key, gmdate('Y-m-d H:i:s', time() + 120));
    if ($wpdb->query($sql) === false) return false;
    $count = $wpdb->get_var($wpdb->prepare('SELECT request_count FROM `' . CLICK_LIMITS_TABLE_NAME . '` WHERE limit_key=%s', $key));
    if ($count === null || (int) $count > $check[1]) return false;
  }
  return true;
}
endif;

if ( !function_exists( 'cocoon_click_rate_limit_ip' ) ):
function cocoon_click_rate_limit_ip(){
  $remote = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
  // 信頼済みプロキシを管理者が明示した場合だけの接続元IP差し替え
  $filtered = apply_filters('cocoon_click_analytics_rate_limit_ip', $remote);
  return is_string($filtered) && filter_var($filtered, FILTER_VALIDATE_IP) !== false ? $filtered : $remote;
}
endif;

if ( !function_exists( 'cocoon_click_network_bucket' ) ):
function cocoon_click_network_bucket($ip){
  // IPそのものを保存せず、レート制限に必要なネットワーク範囲だけを一時キー化
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $parts = explode('.', $ip);
    return implode('.', array_slice($parts, 0, 3)) . '.0/24';
  }
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $packed = inet_pton($ip);
    return $packed === false ? '' : bin2hex(substr($packed, 0, 8)) . '/64';
  }
  return '';
}
endif;

if ( !function_exists( 'cocoon_click_accept_batch' ) ):
function cocoon_click_accept_batch($batch_id, $now){
  global $wpdb;
  $batch_key = cocoon_click_hmac('batch|' . $batch_id);
  $expires = gmdate('Y-m-d H:i:s', strtotime($now . ' +2 days'));
  $sql = $wpdb->prepare(
    'INSERT IGNORE INTO `' . CLICK_BATCHES_TABLE_NAME . '` (batch_key,received_at,expires_at) VALUES (%s,%s,%s)',
    $batch_key,
    $now,
    $expires
  );
  $result = $wpdb->query($sql);
  return array('accepted' => $result !== false && (int) $wpdb->rows_affected === 1, 'error' => $result === false, 'batch_key' => $batch_key);
}
endif;

if ( !function_exists( 'cocoon_click_event_is_trackable' ) ):
function cocoon_click_event_is_trackable($event_type, $destination_type){
  if ($event_type === 'page_sample') return is_click_analytics_impressions_enable();
  if ($event_type === 'impression' && !is_click_analytics_impressions_enable()) return false;
  if ($event_type === 'internal_outcome') return is_click_analytics_outcomes_enable() && is_click_analytics_track_internal() && $destination_type === 'internal';
  if ($destination_type === 'internal') return is_click_analytics_track_internal();
  if ($destination_type === 'external') return is_click_analytics_track_external();
  if (in_array($destination_type, array('anchor', 'download', 'mailto', 'tel', 'sms'), true)) return is_click_analytics_track_special();
  return is_click_analytics_track_external();
}
endif;

if ( !function_exists( 'cocoon_click_sanitize_link_event' ) ):
function cocoon_click_sanitize_link_event($event, $source_post_id, $source_url){
  $event_type = isset($event['type']) ? sanitize_key($event['type']) : '';
  if (!in_array($event_type, array('impression', 'click', 'internal_outcome'), true)) return false;
  $attributes = array(
    'download' => !empty($event['download']),
    'rel' => isset($event['rel']) ? sanitize_text_field($event['rel']) : '',
    'is_affiliate' => !empty($event['is_affiliate']),
    'classification_hint' => isset($event['classification_hint']) ? sanitize_key($event['classification_hint']) : '',
  );
  if (!in_array($attributes['classification_hint'], array('', 'official', 'reference'), true)) $attributes['classification_hint'] = '';
  $destination = cocoon_click_normalize_destination(isset($event['href']) ? $event['href'] : '', $source_url, $attributes);
  if (!$destination || !cocoon_click_event_is_trackable($event_type, $destination['type'])) return false;
  $identity = cocoon_click_build_link_identity($source_post_id, $destination, $event);
  $rel = preg_split('/\s+/', strtolower($attributes['rel']));
  $rel = array_intersect((array) $rel, array('nofollow', 'sponsored', 'ugc', 'noopener', 'noreferrer'));
  $definition = array_merge($identity, array(
    'source_post_id' => (int) $source_post_id,
    'destination_url' => $destination['display'],
    'destination_host' => $destination['host'],
    'destination_type' => $destination['type'],
    'image_url' => in_array($destination['type'], array('mailto', 'tel', 'sms'), true) ? '' : cocoon_click_sanitize_image_url(isset($event['image_url']) ? $event['image_url'] : ''),
    'target_post_id' => isset($destination['target_post_id']) ? (int) $destination['target_post_id'] : 0,
    'rel_flags' => implode(' ', $rel),
    'target_blank' => !empty($event['target_blank']) ? 1 : 0,
    'is_affiliate' => !empty($event['is_affiliate']) ? 1 : 0,
  ));
  return array('type' => $event_type, 'event' => $event, 'definition' => $definition);
}
endif;

if ( !function_exists( 'cocoon_click_upsert_link_definitions' ) ):
function cocoon_click_upsert_link_definitions($definitions, $now, $new_definition_count = null){
  global $wpdb;
  $definitions = array_values($definitions);
  if (!$definitions) return array();
  // 単独呼び出しでも上限判定・定義登録・容量加算を一括確定するためのトランザクション
  if ($new_definition_count === null) {
    $original_db = cocoon_click_begin_transaction(5);
    if (!$original_db) return false;
    try {
      $keyed = array_column($definitions, null, 'link_key');
      $accepted = cocoon_click_filter_definitions_by_capacity($keyed, (int) $definitions[0]['source_post_id'], $new_definition_count);
      if (count($accepted) !== count($keyed)) throw new RuntimeException('definition_capacity');
      $map = cocoon_click_upsert_link_definitions($accepted, $now, $new_definition_count);
      if ($map === false || $wpdb->query('COMMIT') === false) throw new RuntimeException('definitions');
      return $map;
    } catch (Throwable $error) {
      $wpdb->query('ROLLBACK');
      return false;
    } finally {
      cocoon_click_end_transaction($original_db, true);
    }
  }
  $placeholders = array();
  $args = array();
  foreach ($definitions as $definition) {
    $placeholders[] = '(%s,%s,%d,%s,%s,%s,%s,%d,%s,%s,%s,%d,%s,%s,%s,%s,%d,%d,%s,%s)';
    array_push($args,
      $definition['link_key'], $definition['slot_key'], $definition['source_post_id'], $definition['destination_key'],
      $definition['destination_url'], $definition['destination_host'], $definition['destination_type'], $definition['target_post_id'],
      $definition['area'], $definition['heading_key'], $definition['heading_label'], $definition['occurrence'],
      $definition['anchor_text'], isset($definition['image_url']) ? $definition['image_url'] : '', $definition['element_type'], $definition['rel_flags'], $definition['target_blank'],
      $definition['is_affiliate'], $now, $now
    );
  }
  // 1リンクずつSQLを実行せず、複数リンクを1回のINSERTへ集約
  $sql = 'INSERT INTO `' . CLICK_LINKS_TABLE_NAME . '` '
    . '(link_key,slot_key,source_post_id,destination_key,destination_url,destination_host,destination_type,target_post_id,semantic_area,heading_key,heading_label,occurrence_no,anchor_text,image_url,element_type,rel_flags,target_blank,is_affiliate,first_seen_at,last_seen_at) VALUES '
    . implode(',', $placeholders)
    . ' ON DUPLICATE KEY UPDATE destination_url=VALUES(destination_url),destination_host=VALUES(destination_host),destination_type=VALUES(destination_type),rel_flags=VALUES(rel_flags),target_blank=VALUES(target_blank),last_seen_at=VALUES(last_seen_at),target_post_id=GREATEST(target_post_id,VALUES(target_post_id)),is_affiliate=GREATEST(is_affiliate,VALUES(is_affiliate)),image_url=IF(VALUES(image_url)<>\'\',VALUES(image_url),image_url)';
  if ($wpdb->query($wpdb->prepare($sql, $args)) === false) return false;
  $keys = array_column($definitions, 'link_key');
  $in = implode(',', array_fill(0, count($keys), '%s'));
  $rows = $wpdb->get_results($wpdb->prepare('SELECT id,link_key FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE link_key IN (' . $in . ')', $keys), ARRAY_A);
  if ($wpdb->last_error) return false;
  $map = array();
  foreach ((array) $rows as $row) $map[$row['link_key']] = (int) $row['id'];
  if (count($map) !== count($definitions)) return false;
  // 更新行を含むaffected rowsではなく、ロック下で確定した新規定義数だけの加算
  if ($new_definition_count > 0 && $wpdb->query($wpdb->prepare('UPDATE `' . CLICK_LIMITS_TABLE_NAME . '` SET request_count=request_count+%d WHERE limit_key=%s', $new_definition_count, cocoon_click_hmac('definition_capacity'))) !== 1) return false;
  return $map;
}
endif;

if ( !function_exists( 'cocoon_click_empty_stat_row' ) ):
function cocoon_click_empty_stat_row($date, $source_post_id, $link_id, $device, $layout_revision = ''){
  return array(
    'stat_date' => $date, 'source_post_id' => (int) $source_post_id, 'link_id' => (int) $link_id, 'device' => $device, 'layout_revision' => $layout_revision,
    'clicks' => 0, 'unique_clicks' => 0, 'sampled_impressions' => 0, 'sampled_clicks' => 0,
    'weighted_impressions' => 0, 'weighted_clicks' => 0, 'weight_squared' => 0,
    'arrivals' => 0, 'engaged_arrivals' => 0, 'total_time_to_click_ms' => 0,
    'received_events' => 0, 'rejected_events' => 0, 'accepted_batches' => 0, 'duplicate_batches' => 0,
  );
}
endif;

if ( !function_exists( 'cocoon_click_add_stat_row' ) ):
function cocoon_click_add_stat_row(&$rows, $date, $source_post_id, $link_id, $device, $layout_revision, $increments){
  $key = implode('|', array($date, (int) $source_post_id, (int) $link_id, $device, $layout_revision));
  if (!isset($rows[$key])) $rows[$key] = cocoon_click_empty_stat_row($date, $source_post_id, $link_id, $device, $layout_revision);
  foreach ($increments as $column => $amount) {
    if (array_key_exists($column, $rows[$key])) $rows[$key][$column] += max(0, (int) $amount);
  }
}
endif;

if ( !function_exists( 'cocoon_click_insert_uniques' ) ):
function cocoon_click_insert_uniques($date, $session_id, $batch_key, $link_ids, $now){
  global $wpdb;
  $link_ids = array_values(array_unique(array_filter(array_map('intval', $link_ids))));
  if (!$link_ids || $session_id === '') return array();
  $session_key = cocoon_click_hmac('session|' . $date . '|' . $session_id);
  $values = array();
  $args = array();
  foreach ($link_ids as $link_id) {
    $values[] = '(%s,%d,%s,%s,%s)';
    array_push($args, $date, $link_id, $session_key, $batch_key, $now);
  }
  $result = $wpdb->query($wpdb->prepare(
    'INSERT IGNORE INTO `' . CLICK_UNIQUES_TABLE_NAME . '` (stat_date,link_id,session_key,batch_key,created_at) VALUES ' . implode(',', $values),
    $args
  ));
  if ($result === false) return false;
  if (count($link_ids) === 1) {
    return array($link_ids[0] => (int) $wpdb->rows_affected);
  }
  $rows = $wpdb->get_results($wpdb->prepare(
    'SELECT link_id,COUNT(*) AS total FROM `' . CLICK_UNIQUES_TABLE_NAME . '` WHERE batch_key=%s GROUP BY link_id',
    $batch_key
  ), ARRAY_A);
  if ($wpdb->last_error) return false;
  $result = array();
  foreach ((array) $rows as $row) $result[(int) $row['link_id']] = (int) $row['total'];
  return $result;
}
endif;

if ( !function_exists( 'cocoon_click_upsert_stats' ) ):
function cocoon_click_upsert_stats($rows, $now){
  global $wpdb;
  $rows = array_values($rows);
  if (!$rows) return true;
  $values = array();
  $args = array();
  foreach ($rows as $row) {
    $values[] = '(%s,%d,%d,%s,%s,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%s)';
    array_push($args, $row['stat_date'], $row['source_post_id'], $row['link_id'], $row['device'], $row['layout_revision'], $row['clicks'],
      $row['unique_clicks'], $row['sampled_impressions'], $row['sampled_clicks'], $row['weighted_impressions'],
      $row['weighted_clicks'], $row['weight_squared'], $row['arrivals'], $row['engaged_arrivals'],
      $row['total_time_to_click_ms'], $row['received_events'], $row['rejected_events'], $row['accepted_batches'],
      $row['duplicate_batches'], $now);
  }
  // 同時アクセスでも増分を失わないDB側での原子的な加算
  $updates = array();
  foreach (array('clicks', 'unique_clicks', 'sampled_impressions', 'sampled_clicks', 'weighted_impressions', 'weighted_clicks', 'weight_squared', 'arrivals', 'engaged_arrivals', 'total_time_to_click_ms', 'received_events', 'rejected_events', 'accepted_batches', 'duplicate_batches') as $column) {
    $updates[] = $column . '=' . $column . '+VALUES(' . $column . ')';
  }
  $updates[] = 'updated_at=VALUES(updated_at)';
  $sql = 'INSERT INTO `' . CLICK_STATS_DAILY_TABLE_NAME . '` '
    . '(stat_date,source_post_id,link_id,device,layout_revision,clicks,unique_clicks,sampled_impressions,sampled_clicks,weighted_impressions,weighted_clicks,weight_squared,arrivals,engaged_arrivals,total_time_to_click_ms,received_events,rejected_events,accepted_batches,duplicate_batches,updated_at) VALUES '
    . implode(',', $values) . ' ON DUPLICATE KEY UPDATE ' . implode(',', $updates);
  return $wpdb->query($wpdb->prepare($sql, $args)) !== false;
}
endif;

if ( !function_exists( 'cocoon_click_upsert_heatmap' ) ):
function cocoon_click_upsert_heatmap($rows, $now){
  global $wpdb;
  if (!$rows || !is_click_analytics_heatmap_enable()) return true;
  $values = array();
  $args = array();
  foreach ($rows as $row) {
    $values[] = '(%s,%d,%s,%s,%d,%d,%d,%s)';
    array_push($args, $row['stat_date'], $row['source_post_id'], $row['device'], $row['layout_revision'], $row['x_bin'], $row['y_bin'], $row['clicks'], $now);
  }
  $sql = 'INSERT INTO `' . CLICK_HEATMAP_DAILY_TABLE_NAME . '` (stat_date,source_post_id,device,layout_revision,x_bin,y_bin,clicks,updated_at) VALUES '
    . implode(',', $values) . ' ON DUPLICATE KEY UPDATE clicks=clicks+VALUES(clicks),updated_at=VALUES(updated_at)';
  return $wpdb->query($wpdb->prepare($sql, $args)) !== false;
}
endif;

if ( !function_exists( 'cocoon_click_rest_receive_events' ) ):
function cocoon_click_rest_receive_events($request){
  if (!is_click_analytics_enable()) return cocoon_click_rest_error('click_analytics_disabled', __('クリック解析は無効です。', THEME_NAME), 403);
  if (get_theme_option(OP_CLICK_ANALYTICS_TABLE_VERSION, '') !== CLICK_ANALYTICS_TABLE_VERSION || get_theme_option('click_analytics_schema_error', false) || !cocoon_click_tables_exist()) return cocoon_click_rest_error('click_analytics_tables_missing', __('クリック解析テーブルがありません。', THEME_NAME), 503);
  $raw = (string) $request->get_body();
  if ($raw === '' || strlen($raw) > 32768) return cocoon_click_rest_error('click_analytics_payload_size', __('送信サイズが不正です。', THEME_NAME), 413);
  $payload = json_decode($raw, true);
  if (!is_array($payload)) return cocoon_click_rest_error('click_analytics_json', __('JSONが不正です。', THEME_NAME), 400);
  if (!cocoon_click_request_origin_is_valid($request)) return cocoon_click_rest_error('click_analytics_origin', __('送信元が不正です。', THEME_NAME), 403);
  if (cocoon_click_request_user_is_excluded()) return new WP_REST_Response(null, 204);
  if (function_exists('is_useragent_robot') && is_useragent_robot()) return new WP_REST_Response(null, 204);
  // 配列やオブジェクトを文字列へ変換せず、不正な型は受信時点で拒否します。
  foreach (array('batch_id', 'session_id', 'layout_revision', 'device', 'token') as $field) {
    if (!isset($payload[$field]) || !is_string($payload[$field])) return cocoon_click_rest_error('click_analytics_payload', __('送信内容が不正です。', THEME_NAME), 400);
  }
  $batch_id = isset($payload['batch_id']) ? (string) $payload['batch_id'] : '';
  $session_id = isset($payload['session_id']) ? (string) $payload['session_id'] : '';
  $source_post_id = isset($payload['source_post_id']) ? (int) $payload['source_post_id'] : 0;
  $layout_revision = isset($payload['layout_revision']) ? (string) $payload['layout_revision'] : '';
  $sampling_rate = isset($payload['sampling_rate']) ? (int) $payload['sampling_rate'] : 0;
  $device = isset($payload['device']) ? sanitize_key($payload['device']) : '';
  $token = isset($payload['token']) ? (string) $payload['token'] : '';
  $events = isset($payload['events']) && is_array($payload['events']) ? array_values($payload['events']) : array();
  if (!preg_match('/^[A-Za-z0-9_-]{16,128}$/', $batch_id) || !preg_match('/^[A-Za-z0-9_-]{16,128}$/', $session_id)) {
    return cocoon_click_rest_error('click_analytics_identifier', __('識別子が不正です。', THEME_NAME), 400);
  }
  if (!$source_post_id || !preg_match('/^[a-f0-9]{64}$/', $layout_revision) || !in_array($sampling_rate, cocoon_click_allowed_sampling_rates(), true) || !in_array($device, array('mobile', 'tablet', 'desktop'), true) || count($events) < 1 || count($events) > 50) {
    return cocoon_click_rest_error('click_analytics_payload', __('送信内容が不正です。', THEME_NAME), 400);
  }
  if (!cocoon_click_verify_tracking_token($source_post_id, $layout_revision, $sampling_rate, $token)) return cocoon_click_rest_error('click_analytics_token', __('計測トークンが不正です。', THEME_NAME), 403);
  $source_post = get_post($source_post_id);
  if (!$source_post || $source_post->post_status !== 'publish' || !in_array($source_post->post_type, array('post', 'page'), true) || !is_post_type_viewable($source_post->post_type)) return cocoon_click_rest_error('click_analytics_source', __('クリック元が不正です。', THEME_NAME), 400);
  if (!cocoon_click_rate_limit_allows($session_id)) return cocoon_click_rest_error('click_analytics_rate', __('送信回数が多すぎます。', THEME_NAME), 429);
  $now = current_time('mysql');
  $date = current_time('Y-m-d');
  global $wpdb;
  $original_db = cocoon_click_begin_transaction(max(1, (int) apply_filters('cocoon_click_analytics_lock_wait_timeout', 5)));
  if (!$original_db) return cocoon_click_rest_error('click_analytics_storage', __('計測データを保存できませんでした。再送してください。', THEME_NAME), 503);
  // バッチID・リンク・ユニーク数・集計を同時に確定し、途中失敗時はすべて元に戻します。
  try {
    if (!cocoon_click_lock_month(substr($date, 0, 7))) throw new RuntimeException('month_lock');
    // 月替わりで待機した受信は日付を取り直し、確定済みの前月へ後から書き込みません。
    $now = current_time('mysql');
    $current_date = current_time('Y-m-d');
    if (substr($date, 0, 7) !== substr($current_date, 0, 7) && !cocoon_click_lock_month(substr($current_date, 0, 7))) throw new RuntimeException('month_lock');
    $date = $current_date;
    $accepted_batch = cocoon_click_accept_batch($batch_id, $now);
    if ($accepted_batch['error']) throw new RuntimeException('batch');
    if (!$accepted_batch['accepted']) {
      $duplicate_stats = array();
      cocoon_click_add_stat_row($duplicate_stats, $date, $source_post_id, 0, $device, $layout_revision, array('duplicate_batches' => 1));
      if (!cocoon_click_upsert_stats($duplicate_stats, $now) || $wpdb->query('COMMIT') === false) throw new RuntimeException('duplicate');
      return new WP_REST_Response(null, 204);
    }
    $weight = cocoon_click_sampling_weight($sampling_rate);
    $source_url = get_permalink($source_post_id);
    $prepared = array();
    $definitions = array();
    foreach ($events as $event) {
      if (!is_array($event)) continue;
      $type = isset($event['type']) ? sanitize_key($event['type']) : '';
      if ($type === 'page_sample') {
        if (is_click_analytics_impressions_enable()) $prepared[] = array('type' => 'page_sample', 'event' => $event, 'definition' => null);
        continue;
      }
      $item = cocoon_click_sanitize_link_event($event, $source_post_id, $source_url);
      if (!$item) continue;
      $definitions[$item['definition']['link_key']] = $item['definition'];
      $prepared[] = $item;
    }
    // 上限を超える新規リンクだけを除外し、同じバッチの既存クリックは保存します。
    $definitions = cocoon_click_filter_definitions_by_capacity($definitions, $source_post_id, $new_definition_count);
    $prepared = array_values(array_filter($prepared, function($item) use ($definitions){
      return $item['type'] === 'page_sample' || isset($definitions[$item['definition']['link_key']]);
    }));
    $link_map = cocoon_click_upsert_link_definitions($definitions, $now, $new_definition_count);
    if ($link_map === false) throw new RuntimeException('links');
    $stats = array();
    $heatmap = array();
    $unique_link_ids = array();
    cocoon_click_add_stat_row($stats, $date, $source_post_id, 0, $device, $layout_revision, array(
      'received_events' => count($prepared),
      'rejected_events' => max(0, count($events) - count($prepared)),
      'accepted_batches' => 1,
    ));
    foreach ($prepared as $item) {
      $type = $item['type'];
      $event = $item['event'];
      if ($type === 'page_sample') {
        cocoon_click_add_stat_row($stats, $date, $source_post_id, 0, $device, $layout_revision, array('sampled_impressions' => 1, 'weighted_impressions' => $weight, 'weight_squared' => $weight * $weight));
        continue;
      }
      $link_key = $item['definition']['link_key'];
      if (!isset($link_map[$link_key])) continue;
      $link_id = $link_map[$link_key];
      if ($type === 'impression') {
        cocoon_click_add_stat_row($stats, $date, $source_post_id, $link_id, $device, $layout_revision, array('sampled_impressions' => 1, 'weighted_impressions' => $weight, 'weight_squared' => $weight * $weight));
      } elseif ($type === 'click') {
        $increments = array('clicks' => 1, 'total_time_to_click_ms' => isset($event['time_to_click_ms']) ? min(86400000, max(0, (int) $event['time_to_click_ms'])) : 0);
        if (is_click_analytics_impressions_enable() && !empty($event['sampled'])) {
          $increments['sampled_clicks'] = 1;
          $increments['weighted_clicks'] = $weight;
          if (!empty($event['forced_impression'])) {
            $increments['sampled_impressions'] = 1;
            $increments['weighted_impressions'] = $weight;
            $increments['weight_squared'] = $weight * $weight;
          }
        }
        cocoon_click_add_stat_row($stats, $date, $source_post_id, $link_id, $device, $layout_revision, $increments);
        $unique_link_ids[] = $link_id;
        if (is_click_analytics_heatmap_enable() && isset($event['x_bp'], $event['y_bp'])) {
          $x_bp = (int) $event['x_bp'];
          $y_bp = (int) $event['y_bp'];
          if ($x_bp >= 0 && $x_bp <= 10000 && $y_bp >= 0 && $y_bp <= 10000) {
            $heat_key = implode('|', array($date, $source_post_id, $device, $layout_revision, cocoon_click_coordinate_bin($x_bp, 10), cocoon_click_coordinate_bin($y_bp, 50)));
            if (!isset($heatmap[$heat_key])) $heatmap[$heat_key] = array('stat_date' => $date, 'source_post_id' => $source_post_id, 'device' => $device, 'layout_revision' => $layout_revision, 'x_bin' => cocoon_click_coordinate_bin($x_bp, 10), 'y_bin' => cocoon_click_coordinate_bin($y_bp, 50), 'clicks' => 0);
            $heatmap[$heat_key]['clicks']++;
          }
        }
      } elseif ($type === 'internal_outcome') {
        cocoon_click_add_stat_row($stats, $date, $source_post_id, $link_id, $device, $layout_revision, array('arrivals' => 1, 'engaged_arrivals' => !empty($event['engaged']) ? 1 : 0));
      }
    }
    $unique_counts = cocoon_click_insert_uniques($date, $session_id, $accepted_batch['batch_key'], $unique_link_ids, $now);
    if ($unique_counts === false) throw new RuntimeException('uniques');
    foreach ($unique_counts as $link_id => $count) cocoon_click_add_stat_row($stats, $date, $source_post_id, $link_id, $device, $layout_revision, array('unique_clicks' => $count));
    if (!cocoon_click_upsert_stats($stats, $now) || !cocoon_click_upsert_heatmap(array_values($heatmap), $now)) throw new RuntimeException('stats');
    if ($wpdb->query('COMMIT') === false) throw new RuntimeException('commit');
  } catch (Throwable $error) {
    $wpdb->query('ROLLBACK');
    return cocoon_click_rest_error('click_analytics_storage', __('計測データを保存できませんでした。再送してください。', THEME_NAME), 503);
  } finally {
    cocoon_click_end_transaction($original_db, true);
  }
  do_action('cocoon_click_analytics_batch_recorded', count($prepared), $source_post_id);
  return new WP_REST_Response(null, 204);
}
endif;

if ( !function_exists( 'cocoon_click_request_user_is_excluded' ) ):
function cocoon_click_request_user_is_excluded(){
  if ((function_exists('is_user_administrator') && is_user_administrator()) || (is_click_analytics_exclude_logged_in() && is_user_logged_in())) return true;
  // REST nonceがない匿名受信でも、有効なログインCookieを除外判定だけに使います。
  $user_id = function_exists('wp_validate_auth_cookie') ? wp_validate_auth_cookie('', 'logged_in') : 0;
  return $user_id && (is_click_analytics_exclude_logged_in() || user_can($user_id, 'manage_options'));
}
endif;

if ( !function_exists( 'cocoon_click_filter_definitions_by_capacity' ) ):
function cocoon_click_filter_definitions_by_capacity($definitions, $source_post_id, &$new_definition_count = 0){
  global $wpdb;
  $new_definition_count = 0;
  if (!$definitions) return array();
  $keys = array_keys($definitions);
  $in = implode(',', array_fill(0, count($keys), '%s'));
  $existing = $wpdb->get_col($wpdb->prepare('SELECT link_key FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE link_key IN (' . $in . ')', $keys));
  if ($wpdb->last_error) throw new RuntimeException('definition_lookup');
  if (!array_diff($keys, (array) $existing)) {
    // パージとの競合による既存定義の再作成を防ぐ、対象リンクだけのロック
    $locked = $wpdb->get_col($wpdb->prepare('SELECT link_key FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE link_key IN (' . $in . ') FOR UPDATE', $keys));
    if ($wpdb->last_error || array_diff($keys, (array) $locked)) throw new RuntimeException('definition_changed');
    return $definitions;
  }
  // 新規定義だけの直列化と、容量専用行からの最新総数の取得
  $site_count = cocoon_click_lock_definition_count();
  if ($site_count === false) throw new RuntimeException('definition_lock');
  // ロック取得後に追加済みの定義も含めた現在読み取り
  $existing = $wpdb->get_col($wpdb->prepare('SELECT link_key FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE link_key IN (' . $in . ') FOR UPDATE', $keys));
  if ($wpdb->last_error) throw new RuntimeException('definition_lookup');
  $site_limit = max(1, (int) apply_filters('cocoon_click_analytics_definition_limit', 100000));
  $post_limit = max(1, (int) apply_filters('cocoon_click_analytics_post_definition_limit', 5000));
  $post_count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . CLICK_LINKS_TABLE_NAME . '` WHERE source_post_id=%d FOR UPDATE', $source_post_id));
  if ($post_count === null) throw new RuntimeException('definition_count');
  $available = max(0, min($site_limit - (int) $site_count, $post_limit - (int) $post_count));
  $existing = array_fill_keys((array) $existing, true);
  $accepted = array();
  foreach ($definitions as $link_key => $definition) {
    if (isset($existing[$link_key])) $accepted[$link_key] = $definition;
    elseif ($available > 0) { $accepted[$link_key] = $definition; $available--; $new_definition_count++; }
  }
  return $accepted;
}
endif;

if ( !function_exists( 'cocoon_click_definition_capacity_allows' ) ):
function cocoon_click_definition_capacity_allows($definitions, $source_post_id){
  return count(cocoon_click_filter_definitions_by_capacity($definitions, $source_post_id)) === count($definitions);
}
endif;
