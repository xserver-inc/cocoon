<?php //アクセス解析ダッシュボード - レンダリング関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * 各ウィジェットのHTMLを出力するヘルパ群。
 */
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * タブナビを出力
 */
if ( !function_exists( 'cocoon_analytics_render_tabs' ) ):
function cocoon_analytics_render_tabs($current){
  $tabs = array(
    'dashboard' => __('ダッシュボード', THEME_NAME),
    'ranking'   => __('ランキング', THEME_NAME),
    'posts'     => __('記事別', THEME_NAME),
    'terms'     => __('カテゴリ/タグ', THEME_NAME),
    'authors'   => __('著者', THEME_NAME),
    'lifecycle' => __('ライフサイクル', THEME_NAME),
    'export'    => __('エクスポート', THEME_NAME),
    'settings'  => __('設定', THEME_NAME),
  );
  echo '<h2 class="nav-tab-wrapper cocoon-analytics-tabs">';
  foreach ($tabs as $slug => $label) {
    $url = admin_url('admin.php?page=theme-access&view=' . $slug);
    $cls = 'nav-tab' . ($current === $slug ? ' nav-tab-active' : '');
    printf('<a href="%s" class="%s">%s</a>', esc_url($url), esc_attr($cls), esc_html($label));
  }
  echo '</h2>';
}
endif;

/**
 * 期間選択フォーム
 */
if ( !function_exists( 'cocoon_analytics_render_period_form' ) ):
function cocoon_analytics_render_period_form($current_preset, $from, $to, $view){
  $presets = array(
    'today'     => __('今日', THEME_NAME),
    'yesterday' => __('昨日', THEME_NAME),
    '7days'     => __('直近7日', THEME_NAME),
    '30days'    => __('直近30日', THEME_NAME),
    '90days'    => __('直近90日', THEME_NAME),
    'thismonth' => __('今月', THEME_NAME),
    'lastmonth' => __('先月', THEME_NAME),
    'ytd'       => __('年初来', THEME_NAME),
    'all'       => __('全期間', THEME_NAME),
    'custom'    => __('カスタム', THEME_NAME),
  );
  ?>
  <form method="get" class="cocoon-analytics-period-form">
    <input type="hidden" name="page" value="theme-access">
    <input type="hidden" name="view" value="<?php echo esc_attr($view); ?>">
    <label><?php _e('期間:', THEME_NAME); ?>
      <select name="period">
        <?php foreach ($presets as $k => $v): ?>
          <option value="<?php echo esc_attr($k); ?>" <?php selected($current_preset, $k); ?>><?php echo esc_html($v); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="cocoon-analytics-custom-range" <?php echo $current_preset === 'custom' ? '' : 'style="display:none;"'; ?>>
      <input type="date" name="from" value="<?php echo esc_attr($from); ?>">
      <?php echo esc_html__('〜', THEME_NAME); ?>
      <input type="date" name="to" value="<?php echo esc_attr($to); ?>">
    </label>
    <?php submit_button(__('表示', THEME_NAME), 'secondary', '', false); ?>
    <span class="cocoon-analytics-range-label"><?php echo esc_html(sprintf('%s 〜 %s', $from, $to)); ?></span>
  </form>
  <?php
}
endif;

/**
 * KPIカード群を出力
 */
if ( !function_exists( 'cocoon_analytics_render_kpi_cards' ) ):
function cocoon_analytics_render_kpi_cards(){
  $today = current_time('Y-m-d');
  $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
  $week_from = date('Y-m-d', strtotime($today . ' -6 days'));
  $month_from = date('Y-m-01', strtotime($today));

  $today_pv = cocoon_analytics_total_pv($today, $today);
  $yesterday_pv = cocoon_analytics_total_pv($yesterday, $yesterday);
  $week_pv = cocoon_analytics_total_pv($week_from, $today);
  $month_pv = cocoon_analytics_total_pv($month_from, $today);
  $all_pv = cocoon_analytics_total_pv('1970-01-01', $today);

  // 前期間比
  $pw_from = date('Y-m-d', strtotime($week_from . ' -7 days'));
  $pw_to = date('Y-m-d', strtotime($week_from . ' -1 day'));
  $prev_week_pv = cocoon_analytics_total_pv($pw_from, $pw_to);

  $pm_from = date('Y-m-01', strtotime($month_from . ' -1 month'));
  $pm_to = date('Y-m-t', strtotime($pm_from));
  $prev_month_pv = cocoon_analytics_total_pv($pm_from, $pm_to);

  $cards = array(
    array('label' => __('今日', THEME_NAME), 'pv' => $today_pv, 'compare' => $yesterday_pv, 'compare_label' => __('昨日比', THEME_NAME)),
    array('label' => __('昨日', THEME_NAME), 'pv' => $yesterday_pv, 'compare' => null),
    array('label' => __('直近7日', THEME_NAME), 'pv' => $week_pv, 'compare' => $prev_week_pv, 'compare_label' => __('前週比', THEME_NAME)),
    array('label' => __('今月', THEME_NAME), 'pv' => $month_pv, 'compare' => $prev_month_pv, 'compare_label' => __('前月比', THEME_NAME)),
    array('label' => __('総PV', THEME_NAME), 'pv' => $all_pv, 'compare' => null),
  );

  echo '<div class="cocoon-analytics-kpi-grid">';
  foreach ($cards as $c) {
    echo '<div class="cocoon-analytics-kpi-card">';
    echo '<div class="cocoon-analytics-kpi-label">' . esc_html($c['label']) . '</div>';
    echo '<div class="cocoon-analytics-kpi-value">' . esc_html(number_format_i18n($c['pv'])) . '</div>';
    if ($c['compare'] !== null) {
      $diff_html = cocoon_analytics_format_diff($c['pv'], $c['compare']);
      echo '<div class="cocoon-analytics-kpi-compare">' . esc_html($c['compare_label']) . ' ' . $diff_html . '</div>';
    }
    echo '</div>';
  }
  echo '</div>';
}
endif;

/**
 * 増減HTMLを返す（エスケープ済）
 */
if ( !function_exists( 'cocoon_analytics_format_diff' ) ):
function cocoon_analytics_format_diff($current, $previous){
  if ($previous <= 0) {
    return '<span class="cocoon-analytics-diff cocoon-analytics-diff-neutral">—</span>';
  }
  $diff = $current - $previous;
  $pct = ($diff / $previous) * 100;
  $cls = $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'neutral');
  $sign = $diff > 0 ? '+' : '';
  return '<span class="cocoon-analytics-diff cocoon-analytics-diff-' . esc_attr($cls) . '">' .
         esc_html($sign . number_format_i18n($pct, 1) . '%') . '</span>';
}
endif;

/**
 * ランキングテーブル
 */
if ( !function_exists( 'cocoon_analytics_render_ranking_table' ) ):
function cocoon_analytics_render_ranking_table($rows, $show_rank = true, $total_pv_override = null){
  if (empty($rows)) {
    echo '<p>' . esc_html__('データがありません。', THEME_NAME) . '</p>';
    return;
  }
  // 初心者向け: シェア率は「期間全体のPV」を分母にする（指定が無ければ表示中行の合計を使う）
  if ($total_pv_override !== null) {
    $total_pv = max(0, (int) $total_pv_override);
  } else {
    $total_pv = 0;
    foreach ($rows as $r) { $total_pv += (int) $r['pv']; }
  }
  $max_pv = isset($rows[0]['pv']) ? max(1, (int) $rows[0]['pv']) : 1;

  echo '<ol class="cocoon-analytics-ranking-list">';
  $i = 0;
  foreach ($rows as $r) {
    $i++;
    $post_id   = (int) $r['post_id'];
    $pv        = (int) $r['pv'];
    $post_type = $r['post_type'];
    $post      = get_post($post_id);
    $title     = $post ? get_the_title($post) : '';
    if (empty($title)) $title = '(' . __('削除済み', THEME_NAME) . ')';
    $permalink = get_permalink($post_id);
    $edit      = get_edit_post_link($post_id);
    $author    = $post ? get_the_author_meta('display_name', $post->post_author) : '';
    $pub_date  = $post ? mysql2date(get_option('date_format'), $post->post_date) : '';
    $mod_date  = $post ? mysql2date(get_option('date_format'), $post->post_modified) : '';
    // YouTubeと同様のアスペクト比（16:9）を持つTHUMB120サイズを取得するように変更
    $thumb     = $post_id ? get_the_post_thumbnail($post_id, THUMB120, array('class' => 'cocoon-analytics-ranking-thumb')) : '';
    $share     = $total_pv > 0 ? round($pv * 100 / $total_pv, 1) : 0;
    $bar       = max(2, round($pv * 100 / $max_pv));
    $rank_cls  = ($i === 1) ? ' is-rank-1' : (($i === 2) ? ' is-rank-2' : (($i === 3) ? ' is-rank-3' : ''));

    echo '<li class="cocoon-analytics-ranking-item' . esc_attr($rank_cls) . '">';
    echo '<div class="cocoon-analytics-ranking-thumb-wrap">';
    if ($show_rank) {
      echo '<span class="cocoon-analytics-ranking-num">' . esc_html($i) . '</span>';
    }
    if ($thumb) {
      echo $thumb;
    } else {
      echo '<div class="cocoon-analytics-ranking-thumb cocoon-analytics-ranking-thumb-placeholder"><span class="dashicons dashicons-format-aside"></span></div>';
    }
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-body">';
    echo '<div class="cocoon-analytics-ranking-title">';
    if ($permalink) {
      echo '<a href="' . esc_url($permalink) . '" target="_blank" rel="noopener">' . esc_html($title) . '</a>';
    } else {
      echo esc_html($title);
    }
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-meta">';
    echo '<span class="cocoon-analytics-badge">' . esc_html($post_type) . '</span>';
    if ($author)   echo '<span class="cocoon-analytics-meta-item"><span class="dashicons dashicons-admin-users"></span>' . esc_html($author) . '</span>';
    if ($pub_date) echo '<span class="cocoon-analytics-meta-item"><span class="dashicons dashicons-calendar-alt"></span>' . esc_html($pub_date) . '</span>';
    if ($mod_date && $mod_date !== $pub_date) echo '<span class="cocoon-analytics-meta-item"><span class="dashicons dashicons-update"></span>' . esc_html($mod_date) . '</span>';
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-bar"><span style="width:' . esc_attr($bar) . '%"></span></div>';

    echo '<div class="cocoon-analytics-ranking-actions">';
    if ($permalink) echo '<a href="' . esc_url($permalink) . '" target="_blank" rel="noopener">' . esc_html__('表示', THEME_NAME) . '</a>';
    if ($edit)      echo '<a href="' . esc_url($edit) . '">' . esc_html__('編集', THEME_NAME) . '</a>';
    echo '<a href="' . esc_url(admin_url('admin.php?page=theme-access&view=lifecycle&post_id=' . $post_id)) . '">' . esc_html__('ライフサイクル', THEME_NAME) . '</a>';
    echo '</div>';
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-pv">';
    echo '<div class="cocoon-analytics-ranking-pv-num">' . esc_html(number_format_i18n($pv)) . '</div>';
    echo '<div class="cocoon-analytics-ranking-pv-label">PV</div>';
    $share_tip = __('期間全体の総PVに占めるこの記事の割合', THEME_NAME);
    echo '<div class="cocoon-analytics-ranking-pv-share" title="' . esc_attr($share_tip) . '">'
       . esc_html__('シェア', THEME_NAME) . ' ' . esc_html($share) . '%</div>';
    echo '</div>';

    echo '</li>';
  }
  echo '</ol>';
}
endif;

/**
 * 投稿タイプセレクタ
 */
if ( !function_exists( 'cocoon_analytics_render_post_type_filter' ) ):
function cocoon_analytics_render_post_type_filter($current = 'all', $name = 'post_type'){
  $types = cocoon_analytics_allowed_post_types();
  echo '<select name="' . esc_attr($name) . '">';
  echo '<option value="all"' . selected($current, 'all', false) . '>' . esc_html__('すべて', THEME_NAME) . '</option>';
  foreach ($types as $t) {
    echo '<option value="' . esc_attr($t) . '"' . selected($current, $t, false) . '>' . esc_html($t) . '</option>';
  }
  echo '</select>';
}
endif;

/**
 * 空データ時の案内
 */
if ( !function_exists( 'cocoon_analytics_render_empty_notice' ) ):
function cocoon_analytics_render_empty_notice(){
  echo '<div class="notice notice-info inline"><p>';
  echo esc_html__('指定した期間のアクセスデータがありません。', THEME_NAME);
  echo '</p></div>';
}
endif;

/**
 * 急上昇記事ランキング（カード表示）
 */
if ( !function_exists( 'cocoon_analytics_render_trending_list' ) ):
function cocoon_analytics_render_trending_list($rows, $days = 7){
  if (empty($rows)) {
    echo '<p>' . esc_html__('急上昇中の記事はありません。', THEME_NAME) . '</p>';
    return;
  }
  $max_pv = isset($rows[0]['cur_pv']) ? max(1, (int) $rows[0]['cur_pv']) : 1;

  echo '<ol class="cocoon-analytics-ranking-list">';
  $i = 0;
  foreach ($rows as $r) {
    $i++;
    $post_id   = (int) $r['post_id'];
    $cur_pv    = (int) $r['cur_pv'];
    $prev_pv   = (int) $r['prev_pv'];
    $growth    = isset($r['growth']) ? $r['growth'] : 0;
    $post_type = $r['post_type'];
    $post      = get_post($post_id);
    $title     = $post ? get_the_title($post) : '';
    if (empty($title)) $title = '(' . __('削除済み', THEME_NAME) . ')';
    $permalink = get_permalink($post_id);
    $edit      = get_edit_post_link($post_id);
    $author    = $post ? get_the_author_meta('display_name', $post->post_author) : '';
    $pub_date  = $post ? mysql2date(get_option('date_format'), $post->post_date) : '';
    // YouTubeと同様のアスペクト比（16:9）を持つTHUMB120サイズを取得するように変更
    $thumb     = $post_id ? get_the_post_thumbnail($post_id, THUMB120, array('class' => 'cocoon-analytics-ranking-thumb')) : '';
    $bar       = max(2, round($cur_pv * 100 / $max_pv));
    $rank_cls  = ($i === 1) ? ' is-rank-1' : (($i === 2) ? ' is-rank-2' : (($i === 3) ? ' is-rank-3' : ''));

    echo '<li class="cocoon-analytics-ranking-item' . esc_attr($rank_cls) . '">';
    echo '<div class="cocoon-analytics-ranking-thumb-wrap">';
    echo '<span class="cocoon-analytics-ranking-num">' . esc_html($i) . '</span>';
    if ($thumb) {
      echo $thumb;
    } else {
      echo '<div class="cocoon-analytics-ranking-thumb cocoon-analytics-ranking-thumb-placeholder"><span class="dashicons dashicons-format-aside"></span></div>';
    }
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-body">';
    echo '<div class="cocoon-analytics-ranking-title">';
    if ($permalink) {
      echo '<a href="' . esc_url($permalink) . '" target="_blank" rel="noopener">' . esc_html($title) . '</a>';
    } else {
      echo esc_html($title);
    }
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-meta">';
    echo '<span class="cocoon-analytics-badge">' . esc_html($post_type) . '</span>';
    if ($author)   echo '<span class="cocoon-analytics-meta-item"><span class="dashicons dashicons-admin-users"></span>' . esc_html($author) . '</span>';
    if ($pub_date) echo '<span class="cocoon-analytics-meta-item"><span class="dashicons dashicons-calendar-alt"></span>' . esc_html($pub_date) . '</span>';
    // 比較情報
    $compare_tip = sprintf(__('直近%1$d日 %2$s PV / 前%1$d日 %3$s PV', THEME_NAME),
      $days, number_format_i18n($cur_pv), number_format_i18n($prev_pv));
    echo '<span class="cocoon-analytics-meta-item" title="' . esc_attr($compare_tip) . '">'
       . '<span class="dashicons dashicons-backup"></span>'
       . esc_html(number_format_i18n($prev_pv)) . ' → ' . esc_html(number_format_i18n($cur_pv))
       . '</span>';
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-bar"><span style="width:' . esc_attr($bar) . '%"></span></div>';

    echo '<div class="cocoon-analytics-ranking-actions">';
    if ($permalink) echo '<a href="' . esc_url($permalink) . '" target="_blank" rel="noopener">' . esc_html__('表示', THEME_NAME) . '</a>';
    if ($edit)      echo '<a href="' . esc_url($edit) . '">' . esc_html__('編集', THEME_NAME) . '</a>';
    echo '<a href="' . esc_url(admin_url('admin.php?page=theme-access&view=lifecycle&post_id=' . $post_id)) . '">' . esc_html__('ライフサイクル', THEME_NAME) . '</a>';
    echo '</div>';
    echo '</div>';

    echo '<div class="cocoon-analytics-ranking-pv">';
    echo '<div class="cocoon-analytics-ranking-pv-num">' . esc_html(number_format_i18n($cur_pv)) . '</div>';
    echo '<div class="cocoon-analytics-ranking-pv-label">PV</div>';
    if ($growth >= 999) {
      $growth_label = __('新登場', THEME_NAME);
      $growth_cls   = 'cocoon-analytics-growth-new';
    } elseif ($growth >= 0) {
      $growth_label = '+' . number_format_i18n($growth, 1) . '%';
      $growth_cls   = 'cocoon-analytics-growth-up';
    } else {
      $growth_label = number_format_i18n($growth, 1) . '%';
      $growth_cls   = 'cocoon-analytics-growth-down';
    }
    $growth_tip = sprintf(__('\u76f4\u8fd1%1$d\u65e5\u306ePV\u3068\u305d\u306e\u524d%1$d\u65e5\u306ePV\u306e\u6bd4\u8f03', THEME_NAME), $days);
    echo '<div class="cocoon-analytics-ranking-pv-share ' . esc_attr($growth_cls) . '" title="' . esc_attr($growth_tip) . '">'
       . esc_html($growth_label) . '</div>';
    echo '</div>';

    echo '</li>';
  }
  echo '</ol>';
}
endif;

/**
 * カレンダーヒートマップ（直近52週）
 */
if ( !function_exists( 'cocoon_analytics_render_heatmap' ) ):
function cocoon_analytics_render_heatmap(){
  // 初心者向け: 直近52週間 + 今週 = 371日ぶんを日曜始まりで描画する
  $today = current_time('Y-m-d');
  $today_ts = strtotime($today);
  // 今日を含む週の土曜日まで表示するため、今日の曜日番号(0=日)を使って週末へ
  $dow_today = (int) date('w', $today_ts);
  $end_ts = strtotime('+' . (6 - $dow_today) . ' days', $today_ts);
  $start_ts = strtotime('-52 weeks', strtotime(date('Y-m-d', $end_ts) . ' -6 days'));
  $from = date('Y-m-d', $start_ts);
  $to   = date('Y-m-d', $end_ts);

  $map = cocoon_analytics_daily_pv_map($from, $to);
  $max_pv = 0;
  foreach ($map as $v) { if ($v > $max_pv) $max_pv = $v; }

  // 日本語曜日
  $wd = array(
    __('日', THEME_NAME), __('月', THEME_NAME), __('火', THEME_NAME),
    __('水', THEME_NAME), __('木', THEME_NAME), __('金', THEME_NAME), __('土', THEME_NAME)
  );

  // 週単位で配列化
  $weeks = array();
  $cur = $start_ts;
  $w = 0;
  while ($cur <= $end_ts) {
    $d = (int) date('w', $cur);
    $date = date('Y-m-d', $cur);
    $pv = isset($map[$date]) ? (int) $map[$date] : 0;
    $weeks[$w][$d] = array('date' => $date, 'pv' => $pv, 'future' => ($cur > $today_ts));
    if ($d === 6) $w++;
    $cur = strtotime($date . ' +1 day');
  }

  echo '<div class="cocoon-analytics-heatmap">';
  echo '<div class="cocoon-analytics-heatmap-grid">';
  // 曜日ラベル（縦軸）: 月/水/金のみ表示
  echo '<div class="cocoon-analytics-heatmap-days">';
  foreach ($wd as $idx => $label) {
    $visible = ($idx === 1 || $idx === 3 || $idx === 5) ? '' : ' is-invisible';
    echo '<div class="cocoon-analytics-heatmap-day' . $visible . '">' . esc_html($label) . '</div>';
  }
  echo '</div>';

  // セル本体
  echo '<div class="cocoon-analytics-heatmap-weeks">';
  $last_month = '';
  foreach ($weeks as $week_cells) {
    echo '<div class="cocoon-analytics-heatmap-week">';
    // 月ラベル（週の最初の日の月）
    $first = null;
    for ($i = 0; $i < 7; $i++) {
      if (isset($week_cells[$i])) { $first = $week_cells[$i]; break; }
    }
    $month_label = '';
    if ($first) {
      $m = date('n', strtotime($first['date']));
      if ($m !== $last_month) {
        $month_label = $m . __('月', THEME_NAME);
        $last_month = $m;
      }
    }
    echo '<div class="cocoon-analytics-heatmap-month">' . esc_html($month_label) . '</div>';
    for ($i = 0; $i < 7; $i++) {
      if (!isset($week_cells[$i])) {
        echo '<div class="cocoon-analytics-heatmap-cell is-empty"></div>';
        continue;
      }
      $c = $week_cells[$i];
      $pv = $c['pv'];
      $level = 0;
      if ($max_pv > 0 && $pv > 0) {
        $ratio = $pv / $max_pv;
        if     ($ratio > 0.75) $level = 4;
        elseif ($ratio > 0.50) $level = 3;
        elseif ($ratio > 0.25) $level = 2;
        else                    $level = 1;
      }
      $cls = 'cocoon-analytics-heatmap-cell is-level-' . $level;
      if ($c['future']) $cls .= ' is-future';
      $tip = esc_attr($c['date'] . ' : ' . number_format_i18n($pv) . ' PV');
      // ブラウザ標準の遅いツールチップの代わりに、モダンなカスタムツールチップ用の属性を付与します
      echo '<div class="' . esc_attr($cls) . '" data-tooltip="' . $tip . '"></div>';
    }
    echo '</div>';
  }
  echo '</div>'; // weeks
  echo '</div>'; // grid

  // 凡例
  echo '<div class="cocoon-analytics-heatmap-legend">';
  echo '<span>' . esc_html__('少ない', THEME_NAME) . '</span>';
  for ($l = 0; $l <= 4; $l++) {
    echo '<span class="cocoon-analytics-heatmap-cell is-level-' . $l . '"></span>';
  }
  echo '<span>' . esc_html__('多い', THEME_NAME) . '</span>';
  echo '</div>';
  echo '</div>';
}
endif;
