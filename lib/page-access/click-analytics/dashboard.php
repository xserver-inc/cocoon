<?php //クリック解析ダッシュボード
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'cocoon_click_percent' ) ):
function cocoon_click_percent($value){
  return cocoon_click_format_percent($value);
}
endif;

if ( !function_exists( 'cocoon_click_sufficiency_label' ) ):
function cocoon_click_sufficiency_label($row){
  $labels = array();
  if (in_array('effective_sample_size', $row['data_sufficiency_reasons'], true)) {
    $labels[] = sprintf(__('有効標本数 %s / 100', THEME_NAME), number_format_i18n($row['effective_n'], 1));
  }
  if (in_array('sampled_clicks', $row['data_sufficiency_reasons'], true)) {
    $labels[] = sprintf(__('サンプルクリック %s / 10', THEME_NAME), number_format_i18n($row['sampled_clicks']));
  }
  return implode('、', $labels);
}
endif;

if ( !function_exists( 'cocoon_click_area_label' ) ):
function cocoon_click_area_label($area){
  $labels = array(
    'content' => __('本文', THEME_NAME), 'toc' => __('目次', THEME_NAME), 'blogcard' => __('ブログカード', THEME_NAME),
    'cta' => __('CTA', THEME_NAME), 'related' => __('関連記事', THEME_NAME), 'header' => __('ヘッダー', THEME_NAME),
    'navi' => __('ナビ', THEME_NAME), 'sidebar' => __('サイドバー', THEME_NAME), 'footer' => __('フッター', THEME_NAME),
    'mobile_menu' => __('モバイルメニュー', THEME_NAME), 'other' => __('その他', THEME_NAME),
  );
  return isset($labels[$area]) ? $labels[$area] : $area;
}
endif;

if ( !function_exists( 'cocoon_click_type_label' ) ):
function cocoon_click_type_label($type){
  $labels = array(
    'internal' => __('内部リンク', THEME_NAME), 'external' => __('外部・参考資料', THEME_NAME), 'affiliate' => __('アフィリエイト', THEME_NAME),
    'official' => __('公式サイト', THEME_NAME), 'reference' => __('参考資料', THEME_NAME), 'social' => __('SNS', THEME_NAME), 'anchor' => __('ページ内リンク', THEME_NAME),
    'download' => __('ダウンロード', THEME_NAME), 'mailto' => __('メール', THEME_NAME), 'tel' => __('電話', THEME_NAME), 'sms' => __('SMS', THEME_NAME),
  );
  return isset($labels[$type]) ? $labels[$type] : $type;
}
endif;

if ( !function_exists( 'cocoon_click_render_subtabs' ) ):
function cocoon_click_render_subtabs($current){
  $tabs = array('overview' => __('概要', THEME_NAME), 'internal' => __('内部リンク', THEME_NAME), 'external' => __('外部リンク', THEME_NAME), 'map' => __('クリックマップ', THEME_NAME));
  echo '<h3 class="nav-tab-wrapper cocoon-click-subtabs">';
  foreach ($tabs as $key => $label) {
    $url = add_query_arg(array('page' => 'theme-access', 'view' => 'clicks', 'click_view' => $key), admin_url('admin.php'));
    printf('<a class="nav-tab%s" href="%s">%s</a>', $current === $key ? ' nav-tab-active' : '', esc_url($url), esc_html($label));
  }
  echo '</h3>';
}
endif;

if ( !function_exists( 'cocoon_click_render_filters' ) ):
function cocoon_click_render_filters($click_view, $preset, $from, $to, $filters){
  ?>
  <form method="get" class="cocoon-analytics-filter-bar cocoon-click-filter-bar">
    <input type="hidden" name="page" value="theme-access">
    <input type="hidden" name="view" value="clicks">
    <input type="hidden" name="click_view" value="<?php echo esc_attr($click_view); ?>">
    <label><?php _e('期間:', THEME_NAME); ?>
      <select name="period">
        <?php foreach (array('today' => __('今日', THEME_NAME), '7days' => __('直近7日', THEME_NAME), '30days' => __('直近30日', THEME_NAME), '90days' => __('直近90日', THEME_NAME), 'thismonth' => __('今月', THEME_NAME), 'all' => __('全期間', THEME_NAME), 'custom' => __('カスタム', THEME_NAME)) as $key => $label): ?>
          <option value="<?php echo esc_attr($key); ?>" <?php selected($preset, $key); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <span class="cocoon-analytics-custom-range" <?php echo $preset === 'custom' ? '' : 'style="display:none;"'; ?>>
      <input type="date" name="from" value="<?php echo esc_attr($from); ?>"> <?php _e('〜', THEME_NAME); ?>
      <input type="date" name="to" value="<?php echo esc_attr($to); ?>">
    </span>
    <label><?php _e('端末:', THEME_NAME); ?>
      <select name="device">
        <?php foreach (array('all' => __('すべて', THEME_NAME), 'desktop' => __('PC', THEME_NAME), 'tablet' => __('タブレット', THEME_NAME), 'mobile' => __('モバイル', THEME_NAME)) as $key => $label): ?>
          <option value="<?php echo esc_attr($key); ?>" <?php selected($filters['device'], $key); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php
    cocoon_analytics_render_post_picker('source_post_id', $filters['source_post_id'], array(
      'label'       => __('クリック元記事:', THEME_NAME),
      'placeholder' => __('記事タイトル・キーワード・IDで検索...', THEME_NAME),
    ));
    ?>
    <?php if ($click_view !== 'map'): ?>
      <label><?php _e('掲載位置:', THEME_NAME); ?>
        <select name="area">
          <option value="all"><?php _e('すべて', THEME_NAME); ?></option>
          <?php foreach (array('content', 'toc', 'blogcard', 'cta', 'related', 'header', 'navi', 'sidebar', 'footer', 'mobile_menu', 'other') as $area): ?>
            <option value="<?php echo esc_attr($area); ?>" <?php selected($filters['area'], $area); ?>><?php echo esc_html(cocoon_click_area_label($area)); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label><?php _e('リンク種別:', THEME_NAME); ?>
        <select name="link_type">
          <option value="all"><?php _e('すべて', THEME_NAME); ?></option>
          <?php foreach (array('internal', 'external', 'affiliate', 'official', 'reference', 'social', 'anchor', 'download', 'mailto', 'tel') as $type): ?>
            <option value="<?php echo esc_attr($type); ?>" <?php selected($filters['link_type'], $type); ?>><?php echo esc_html(cocoon_click_type_label($type)); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    <?php endif; ?>
    <?php submit_button(__('表示', THEME_NAME), 'secondary', '', false); ?>
  </form>
  <?php
}
endif;

if ( !function_exists( 'cocoon_click_render_kpis' ) ):
function cocoon_click_render_kpis($metrics, $previous = array()){
  $items = array(
    array(__('総クリック', THEME_NAME), number_format_i18n($metrics['clicks']), 'clicks'),
    array(__('日次セッションユニーク合計', THEME_NAME), number_format_i18n($metrics['unique_clicks']), 'unique_clicks'),
    array(__('内部リンククリック', THEME_NAME), number_format_i18n($metrics['internal_clicks']), 'internal_clicks'),
    array(__('外部遷移', THEME_NAME), number_format_i18n($metrics['external_clicks']), 'external_clicks'),
    array(__('推定CTR', THEME_NAME), cocoon_click_percent($metrics['ctr']), 'ctr'),
    array(__('内部到着率', THEME_NAME), cocoon_click_percent($metrics['arrival_rate']), 'arrival_rate'),
    array(__('エンゲージ率', THEME_NAME), cocoon_click_percent($metrics['engagement_rate']), 'engagement_rate'),
  );
  echo '<div class="cocoon-analytics-kpi-grid cocoon-click-kpi-grid">';
  foreach ($items as $item) {
    $comparison = '';
    if (isset($previous[$item[2]]) && $previous[$item[2]] !== null && (float) $previous[$item[2]] != 0.0 && $metrics[$item[2]] !== null) {
      $change = (((float) $metrics[$item[2]] / (float) $previous[$item[2]]) - 1) * 100;
      $comparison = sprintf(__('前期間比 %s%%', THEME_NAME), ($change >= 0 ? '+' : '') . number_format_i18n($change, 1));
    }
    printf('<div class="cocoon-analytics-kpi-card"><span class="cocoon-analytics-kpi-label">%s</span><strong class="cocoon-analytics-kpi-value">%s</strong><small class="cocoon-analytics-kpi-compare">%s</small></div>', esc_html($item[0]), esc_html($item[1]), esc_html($comparison));
  }
  echo '</div>';
  if ($metrics['ctr_lower'] !== null && $metrics['ctr_upper'] !== null) {
    printf('<p class="description">%s: %s〜%s / %s: %s</p>', esc_html__('推定CTRのWilson 95%信頼区間', THEME_NAME), esc_html(cocoon_click_percent($metrics['ctr_lower'])), esc_html(cocoon_click_percent($metrics['ctr_upper'])), esc_html__('有効標本数', THEME_NAME), esc_html(number_format_i18n($metrics['effective_n'], 1)));
  }
  if (!$metrics['data_sufficient']) echo '<p class="description">' . esc_html(sprintf(__('データ不足のためCTRは参考値です（%s）。', THEME_NAME), cocoon_click_sufficiency_label($metrics))) . '</p>';
}
endif;

if ( !function_exists( 'cocoon_click_render_link_title' ) ):
function cocoon_click_render_link_title($row){
  $label = $row['anchor_text'] !== '' ? $row['anchor_text'] : $row['destination_url'];
  if ($row['is_affiliate']) $label .= ' [' . __('アフィリエイト', THEME_NAME) . ']';
  return $label;
}
endif;

if ( !function_exists( 'cocoon_click_render_links_table' ) ):
function cocoon_click_render_links_table($result, $show_source = true){
  if (!$result['rows']) {
    echo '<div class="notice notice-info inline"><p>' . esc_html__('該当するクリックデータがありません。', THEME_NAME) . '</p></div>';
    return;
  }
  ?>
  <div class="cocoon-analytics-table-scroll"><table class="widefat striped cocoon-click-table">
    <thead><tr>
      <?php if ($show_source): ?><th><?php _e('クリック元', THEME_NAME); ?></th><?php endif; ?>
      <th><?php _e('リンク', THEME_NAME); ?></th><th><?php _e('種別・位置', THEME_NAME); ?></th>
      <th><?php _e('推定表示', THEME_NAME); ?></th><th><?php _e('クリック', THEME_NAME); ?></th><th><?php _e('日次ユニーク合計', THEME_NAME); ?></th>
      <th><?php _e('推定CTR', THEME_NAME); ?></th><th><?php _e('到着率', THEME_NAME); ?></th><th><?php _e('エンゲージ率', THEME_NAME); ?></th><th><?php _e('平均クリック時間', THEME_NAME); ?></th>
    </tr></thead><tbody>
    <?php foreach ($result['rows'] as $row): ?>
      <tr>
        <?php if ($show_source): ?><td><a href="<?php echo esc_url(get_permalink($row['source_post_id'])); ?>" target="_blank" rel="noopener"><?php echo esc_html(cocoon_analytics_plain_title($row['source_post_id']) ?: '#' . $row['source_post_id']); ?></a></td><?php endif; ?>
        <td><strong><?php echo esc_html(cocoon_click_render_link_title($row)); ?></strong><br><code><?php echo esc_html($row['destination_url']); ?></code><?php if ($row['heading_label'] !== ''): ?><br><small><?php echo esc_html($row['heading_label']); ?></small><?php endif; ?></td>
        <td><span class="cocoon-click-badge"><?php echo esc_html(cocoon_click_type_label($row['destination_type'])); ?></span> <span class="cocoon-click-badge"><?php echo esc_html(cocoon_click_area_label($row['semantic_area'])); ?></span><br><small><?php echo esc_html($row['element_type'] . ' #' . ((int) $row['occurrence_no'] + 1)); ?></small></td>
        <td><?php echo number_format_i18n($row['weighted_impressions']); ?></td><td><?php echo number_format_i18n($row['clicks']); ?></td><td><?php echo number_format_i18n($row['unique_clicks']); ?></td>
        <td><?php echo esc_html(cocoon_click_percent($row['ctr'])); ?><?php if ($row['ctr_lower'] !== null): ?><br><small><?php printf(esc_html__('95%% CI %1$s〜%2$s / n=%3$s', THEME_NAME), esc_html(cocoon_click_percent($row['ctr_lower'])), esc_html(cocoon_click_percent($row['ctr_upper'])), esc_html(number_format_i18n($row['effective_n'], 1))); ?></small><?php endif; ?><?php if (!$row['data_sufficient']): ?><br><small><?php echo esc_html(cocoon_click_sufficiency_label($row)); ?></small><?php endif; ?></td>
        <td><?php echo esc_html(cocoon_click_percent($row['arrival_rate'])); ?></td><td><?php echo esc_html(cocoon_click_percent($row['engagement_rate'])); ?></td><td><?php echo $row['average_time_to_click_ms'] === null ? '—' : esc_html(number_format_i18n($row['average_time_to_click_ms'] / 1000, 1) . __('秒', THEME_NAME)); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?php
}
endif;

if ( !function_exists( 'cocoon_click_render_pagination' ) ):
function cocoon_click_render_pagination($result){
  $pages = (int) ceil($result['total'] / max(1, $result['per_page']));
  if (!empty($result['total_is_estimate'])) echo '<p class="description">' . esc_html__('高速表示のため、リンク総数とページ数は初回・最終観測期間から算出した概算です。', THEME_NAME) . '</p>';
  if ($pages <= 1) return;
  $links = paginate_links(array('base' => add_query_arg('paged', '%#%'), 'format' => '', 'current' => $result['page'], 'total' => $pages, 'type' => 'list'));
  if ($links) echo '<div class="tablenav"><div class="tablenav-pages">' . $links . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
endif;

if ( !function_exists( 'cocoon_click_render_insight_list' ) ):
function cocoon_click_render_insight_list($title, $rows, $empty){
  echo '<div class="cocoon-analytics-card"><h3>' . esc_html($title) . '</h3>';
  if (!$rows) {
    echo '<p class="description">' . esc_html($empty) . '</p></div>';
    return;
  }
  echo '<ul class="cocoon-click-insight-list">';
  foreach ($rows as $row) printf('<li><strong>%s</strong><br><small>%s / CTR %s / %s clicks</small></li>', esc_html(cocoon_click_render_link_title($row)), esc_html(cocoon_click_area_label($row['semantic_area'])), esc_html(cocoon_click_percent($row['ctr'])), number_format_i18n($row['clicks']));
  echo '</ul></div>';
}
endif;

$click_view = isset($_GET['click_view']) ? sanitize_key($_GET['click_view']) : 'overview';
if (!in_array($click_view, array('overview', 'internal', 'external', 'map'), true)) $click_view = 'overview';
$filters = array(
  'device' => isset($_GET['device']) && in_array($_GET['device'], array('all', 'desktop', 'tablet', 'mobile'), true) ? $_GET['device'] : 'all',
  'source_post_id' => isset($_GET['source_post_id']) ? max(0, (int) $_GET['source_post_id']) : 0,
  'area' => isset($_GET['area']) ? sanitize_key($_GET['area']) : 'all',
  'link_type' => isset($_GET['link_type']) ? sanitize_key($_GET['link_type']) : 'all',
);

cocoon_click_render_subtabs($click_view);

if (!is_click_analytics_enable()) {
  echo '<div class="notice notice-warning inline"><p>' . esc_html__('クリック解析は現在無効です。過去データは閲覧できますが、新しいデータは収集されません。', THEME_NAME) . '</p></div>';
}
if (!cocoon_click_tables_exist()) {
  echo '<div class="notice notice-error inline"><p>' . esc_html__('クリック解析テーブルを作成できていません。設定画面を開き直してください。', THEME_NAME) . '</p></div>';
  return;
}
if ($preset === 'all') {
  $click_min_date = cocoon_click_analytics_min_date();
  if ($click_min_date) $from = $click_min_date;
}

cocoon_click_render_filters($click_view, $preset, $from, $to, $filters);

if ($click_view === 'overview') {
  $metrics = cocoon_click_analytics_metrics($from, $to, $filters);
  $days = max(1, (int) floor((strtotime($to) - strtotime($from)) / DAY_IN_SECONDS) + 1);
  $previous_to = gmdate('Y-m-d', strtotime($from . ' -1 day'));
  $previous_from = gmdate('Y-m-d', strtotime($previous_to . ' -' . ($days - 1) . ' days'));
  $previous_metrics = cocoon_click_analytics_metrics($previous_from, $previous_to, $filters);
  cocoon_click_render_kpis($metrics, $previous_metrics);
  if ($metrics['approximate_period']) echo '<p class="description">' . esc_html__('日次保持期間より古い範囲は月次集計を使用するため、月途中の開始・終了日は概算です。', THEME_NAME) . '</p>';
  $trend = cocoon_click_analytics_trend($from, $to, $filters);
  $GLOBALS['cocoon_analytics_chart_data']['click_trend'] = $trend;
  ?>
  <div class="cocoon-analytics-card cocoon-click-trend-card"><h3><?php _e('クリック・推定CTR推移', THEME_NAME); ?></h3><div class="cocoon-click-chart-wrap"><canvas id="cocoon-click-trend" role="img" aria-label="<?php echo esc_attr__('クリック推移', THEME_NAME); ?>"></canvas></div></div>
  <?php
  $insights = cocoon_click_analytics_insights($from, $to, $filters);
  echo '<div class="cocoon-click-insights-grid">';
  cocoon_click_render_insight_list(__('高需要・低到達', THEME_NAME), $insights['high_demand_low_reach'], __('十分なデータがたまると、上部へ移す候補を表示します。', THEME_NAME));
  cocoon_click_render_insight_list(__('高露出・低反応', THEME_NAME), $insights['high_exposure_low_response'], __('十分なデータがたまると、文言やデザインの改善候補を表示します。', THEME_NAME));
  cocoon_click_render_insight_list(__('遷移後ミスマッチ', THEME_NAME), $insights['post_click_mismatch'], __('到着後の閲覧が弱いリンクはありません。', THEME_NAME));
  cocoon_click_render_insight_list(__('外部遷移候補', THEME_NAME), $insights['external_candidates'], __('外部クリックは購入・申込みではなく外部遷移です。', THEME_NAME));
  echo '</div>';
  $top = cocoon_click_analytics_links_table($from, $to, array_merge($filters, array('page' => 1, 'per_page' => 10, 'group' => 'occurrence', 'order' => 'clicks')));
  echo '<div class="cocoon-analytics-card"><h3>' . esc_html__('クリック上位リンク', THEME_NAME) . '</h3>';
  cocoon_click_render_links_table($top);
  echo '</div>';
} elseif ($click_view === 'internal' || $click_view === 'external') {
  $scope = $click_view;
  $group = isset($_GET['group']) && in_array($_GET['group'], array('occurrence', 'destination', 'domain'), true) ? $_GET['group'] : 'occurrence';
  if ($click_view === 'internal' && $group === 'domain') $group = 'destination';
  $order = isset($_GET['order']) && in_array($_GET['order'], array('clicks', 'unique', 'impressions', 'ctr'), true) ? $_GET['order'] : 'clicks';
  $paged = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
  ?>
  <form method="get" class="cocoon-click-group-form">
    <?php foreach ($_GET as $key => $value) if (!in_array($key, array('group', 'order', 'paged'), true) && is_scalar($value)) printf('<input type="hidden" name="%s" value="%s">', esc_attr($key), esc_attr($value)); ?>
    <label><?php _e('集計単位:', THEME_NAME); ?> <select name="group"><option value="occurrence" <?php selected($group, 'occurrence'); ?>><?php _e('掲載箇所別', THEME_NAME); ?></option><option value="destination" <?php selected($group, 'destination'); ?>><?php _e('リンク先別', THEME_NAME); ?></option><?php if ($click_view === 'external'): ?><option value="domain" <?php selected($group, 'domain'); ?>><?php _e('ドメイン別', THEME_NAME); ?></option><?php endif; ?></select></label>
    <label><?php _e('並び順:', THEME_NAME); ?> <select name="order"><option value="clicks" <?php selected($order, 'clicks'); ?>><?php _e('クリック', THEME_NAME); ?></option><option value="unique" <?php selected($order, 'unique'); ?>><?php _e('ユニーク', THEME_NAME); ?></option><option value="impressions" <?php selected($order, 'impressions'); ?>><?php _e('表示', THEME_NAME); ?></option><option value="ctr" <?php selected($order, 'ctr'); ?>><?php _e('CTR', THEME_NAME); ?></option></select></label>
    <?php submit_button(__('変更', THEME_NAME), 'secondary', '', false); ?>
  </form>
  <?php
  if ($click_view === 'external') echo '<p class="description">' . esc_html__('ここで示す数値は外部サイトへの遷移であり、購入・申込みの確定コンバージョンではありません。', THEME_NAME) . '</p>';
  $result = cocoon_click_analytics_links_table($from, $to, array_merge($filters, array('scope' => $scope, 'group' => $group, 'order' => $order, 'page' => $paged, 'per_page' => 25)));
  cocoon_click_render_links_table($result);
  cocoon_click_render_pagination($result);
} else {
  $source_post_id = $filters['source_post_id'];
  if (!$source_post_id) {
    echo '<div class="notice notice-info inline"><p>' . esc_html__('クリックマップを表示する記事を検索して選択してください。', THEME_NAME) . '</p></div>';
  } else {
    $post = get_post($source_post_id);
    if (!$post || $post->post_status !== 'publish') {
      echo '<div class="notice notice-error inline"><p>' . esc_html__('公開中の記事を指定してください。', THEME_NAME) . '</p></div>';
    } else {
      $device = $filters['device'] === 'all' ? 'desktop' : $filters['device'];
      $heatmap = cocoon_click_analytics_heatmap($from, $to, $source_post_id, $device);
      $current_layout = cocoon_click_layout_revision($source_post_id);
      $current_rows = array_values(array_filter($heatmap, function($row) use ($current_layout){ return $row['layout_revision'] === $current_layout; }));
      $old_clicks = 0;
      foreach ($heatmap as $row) if ($row['layout_revision'] !== $current_layout) $old_clicks += (int) $row['clicks'];
      $max_clicks = 1;
      foreach ($current_rows as $row) $max_clicks = max($max_clicks, (int) $row['clicks']);
      $post_modified_date = substr((string) $post->post_modified, 0, 10);
      $site_revision = (float) get_option('cocoon_click_layout_revision', 0);
      $site_revision_date = $site_revision > 1000000000 ? wp_date('Y-m-d', (int) $site_revision) : $from;
      $current_data_from = max($from, $post_modified_date, $site_revision_date);
      $map_links = $current_data_from <= $to ? cocoon_click_analytics_map_links($current_data_from, $to, $source_post_id, $device, $current_layout) : array();
      $GLOBALS['cocoon_click_map_data'] = $map_links;
      ?>
      <div class="cocoon-analytics-card cocoon-click-map-card">
        <h3><?php echo esc_html(cocoon_analytics_plain_title($source_post_id)); ?> — <?php echo esc_html(strtoupper($device)); ?></h3>
        <?php if ($old_clicks > 0): ?>
          <div class="notice notice-warning inline"><p><?php printf(esc_html__('現在と異なる旧レイアウトのクリックが%件あります。現在のページには重ねていません。', THEME_NAME), $old_clicks); ?></p>
            <details><summary><?php _e('旧レイアウト一覧', THEME_NAME); ?></summary><ul>
              <?php
              $old_layouts = array();
              foreach ($heatmap as $row) if ($row['layout_revision'] !== $current_layout) $old_layouts[$row['layout_revision']] = isset($old_layouts[$row['layout_revision']]) ? $old_layouts[$row['layout_revision']] + (int) $row['clicks'] : (int) $row['clicks'];
              foreach ($old_layouts as $revision => $clicks) printf('<li><code>%s</code>: %s</li>', esc_html(substr($revision, 0, 12)), esc_html(number_format_i18n($clicks)));
              ?>
            </ul></details>
          </div>
        <?php endif; ?>
        <div class="cocoon-click-map-frame-wrap">
          <iframe id="cocoon-click-map-frame" src="<?php echo esc_url(add_query_arg('cocoon_click_map_preview', '1', get_permalink($source_post_id))); ?>" title="<?php echo esc_attr__('クリックマップページプレビュー', THEME_NAME); ?>" loading="lazy"></iframe>
          <div id="cocoon-click-map-overlay" class="cocoon-click-map-overlay" aria-hidden="true">
            <?php foreach ($current_rows as $row): $opacity = 0.15 + (0.65 * ((int) $row['clicks'] / $max_clicks)); ?>
              <span class="cocoon-click-heat-cell" style="left:<?php echo (int) $row['x_bin'] * 10; ?>%;top:<?php echo (int) $row['y_bin'] * 2; ?>%;width:10%;height:2%;opacity:<?php echo esc_attr(number_format($opacity, 2, '.', '')); ?>" title="<?php echo esc_attr(number_format_i18n($row['clicks']) . ' clicks'); ?>"></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="cocoon-analytics-card"><h3><?php _e('リンク別クリック', THEME_NAME); ?></h3><?php cocoon_click_render_links_table(array('rows' => $map_links, 'total' => count($map_links), 'page' => 1, 'per_page' => 100), false); ?></div>
      <?php
    }
  }
}

echo '<p class="description cocoon-click-causality-note">' . esc_html__('掲載位置による差は観察データです。この画面だけでは、位置変更がクリックを増やしたという因果関係までは証明できません。', THEME_NAME) . '</p>';
