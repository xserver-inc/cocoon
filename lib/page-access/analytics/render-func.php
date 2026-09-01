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
 * グラフ軸など幅の限られる箇所で使う短縮日付書式を返す
 *
 * 管理画面のグラフ軸はラベル幅が非常に狭いため、年を含む書式では表示が崩れます。
 * WordPress本体が月別アーカイブ書式を翻訳可能にしているのと同じ考え方で、
 * ロケールごとに月日の並び順・区切り文字を差し替えられるようにしています。
 */
if ( !function_exists( 'cocoon_analytics_short_date_format' ) ):
function cocoon_analytics_short_date_format(){
  return _x('n/j', 'アクセス解析グラフ軸の短縮日付書式', THEME_NAME);
}
endif;

/**
 * 年を必ず含む日付書式を返す
 *
 * サイトの日付書式は設定で年を外せるため、そのまま使うと何年のデータか判別できなくなります。
 * 年の指定子が含まれないときだけ WordPress本体の既定書式へ切り替えます。
 */
if ( !function_exists( 'cocoon_analytics_full_date_format' ) ):
function cocoon_analytics_full_date_format(){
  $format = get_option('date_format');
  // バックスラッシュでエスケープされた文字を除いた上での年指定子の有無の判定
  if (!preg_match('/[Yyo]/', preg_replace('/\\\\./', '', $format))) {
    $format = translate('F j, Y', 'default');
  }
  return $format;
}
endif;

/**
 * 日付書式が翻訳を必要としない（数字だけで表せる）かを返す
 *
 * date_i18n() は1呼び出しごとに日時オブジェクトを生成するため、
 * 数千行のグラフデータを整形すると大きなオーバーヘッドになります。
 * 曜日名・月名・午前午後など言語に依存する指定子を含まない書式なら、
 * 軽量な gmdate() で同じ結果を得られるため、その判定に使います。
 */
if ( !function_exists( 'cocoon_analytics_is_numeric_date_format' ) ):
function cocoon_analytics_is_numeric_date_format($format){
  if ($format === '') return false;
  // バックスラッシュでエスケープされた文字を除いた上での判定
  return !preg_match('/[DlMFaAT]/', preg_replace('/\\\\./', '', $format));
}
endif;

/**
 * PV推移グラフの軸ラベルを、サイトの言語・書式で整形する
 *
 * 各行に label キーを付与して返します。JS側での日本語固定の整形を避けるため、
 * PHP側で date_i18n() を使ってサイトの言語・日付フォーマットに追従させます。
 */
if ( !function_exists( 'cocoon_analytics_chart_labels' ) ):
function cocoon_analytics_chart_labels($rows, $type){
  if (empty($rows) || !is_array($rows)) return $rows;
  // 最大3700行のループになるため、書式取得はループ外で1回のみ
  $day_format = ($type === 'daily' || $type === 'weekly') ? cocoon_analytics_short_date_format() : '';
  // 軸ラベルは月日のみのため、ツールチップは年を含む書式で何年のデータかを示す
  $full_format = ($day_format !== '') ? cocoon_analytics_full_date_format() : '';
  // 週次は1本の棒が複数日を表すため、単日と誤読されないよう範囲表記に使う書式
  /* translators: 1: 開始日, 2: 終了日 */
  $range_format = ($type === 'weekly') ? __('%1$s 〜 %2$s', THEME_NAME) : '';
  // WordPress本体（defaultドメイン）のアーカイブ書式を流用したサイト言語追従
  // cocoon.pot への無意味な抽出を避けるため、_x() ではなく下位関数を直接使用
  $month_format = ($type === 'monthly') ? translate_with_gettext_context('F Y', 'monthly archives date format', 'default') : '';
  $year_format  = ($type === 'yearly') ? translate_with_gettext_context('Y', 'yearly archives date format', 'default') : '';

  // date_i18n() は渡されたタイムスタンプをUTCとして解釈し直すため、数字だけの書式なら gmdate() で同じ結果
  $numeric = array(
    $day_format   => cocoon_analytics_is_numeric_date_format($day_format),
    $full_format  => cocoon_analytics_is_numeric_date_format($full_format),
    $month_format => cocoon_analytics_is_numeric_date_format($month_format),
    $year_format  => cocoon_analytics_is_numeric_date_format($year_format),
  );
  $format_date = function($format, $timestamp) use ($numeric) {
    return $numeric[$format] ? gmdate($format, $timestamp) : date_i18n($format, $timestamp);
  };

  foreach ($rows as $i => $row) {
    $date = isset($row['date']) ? $row['date'] : '';
    $label = $date;
    $timestamp = false;
    if ($day_format !== '') {
      $timestamp = strtotime($date);
      if ($timestamp !== false) {
        $label = $format_date($day_format, $timestamp);
      }
    } elseif ($month_format !== '') {
      $timestamp = strtotime($date . '-01');
      if ($timestamp !== false) {
        $label = $format_date($month_format, $timestamp);
      }
    } elseif ($year_format !== '') {
      $timestamp = strtotime($date . '-01-01');
      if ($timestamp !== false) {
        $label = $format_date($year_format, $timestamp);
      }
    }
    $rows[$i]['label'] = $label;

    // 月次・年次のラベルは既に年を含むため、重複データを送らずJS側のフォールバックに任せる
    if ($full_format === '' || $timestamp === false) continue;
    $tooltip_label = $format_date($full_format, $timestamp);
    $days = isset($row['days']) ? (int) $row['days'] : 0;
    if ($range_format !== '' && $days > 1) {
      // 集計対象が連続日で0埋め済みのため、開始日＋（日数-1）がその棒の最終日
      $end_timestamp = strtotime('+' . ($days - 1) . ' days', $timestamp);
      if ($end_timestamp !== false) {
        $tooltip_label = sprintf($range_format, $tooltip_label, $format_date($full_format, $end_timestamp));
      }
    }
    $rows[$i]['tooltip_label'] = $tooltip_label;
  }
  return $rows;
}
endif;

/**
 * 投稿タイトルをプレーンテキスト用に取得する
 *
 * get_the_title() は the_title フィルタ（wptexturize 等）を通すため、
 * 「&#8211;」のようなHTMLエンティティを含んだ文字列を返すことがあります。
 * これをそのまま esc_html() や JavaScript の textContent に渡すと、
 * 「&」がさらにエスケープされて画面にエンティティがそのまま表示されてしまいます。
 * そこで一度エンティティを実体文字（–など）へデコードしてから返します。
 */
if ( !function_exists( 'cocoon_analytics_plain_title' ) ):
function cocoon_analytics_plain_title($post){
  return html_entity_decode(get_the_title($post), ENT_QUOTES, get_bloginfo('charset'));
}
endif;

/**
 * NO IMAGEサムネイルのimgタグを取得する
 *
 * アイキャッチ未設定の記事でも、テーマ標準のNO IMAGE画像（16:9）を表示します。
 */
if ( !function_exists( 'cocoon_analytics_get_no_image_thumb_tag' ) ):
function cocoon_analytics_get_no_image_thumb_tag(){
  return '<img src="' . esc_url(get_no_image_120x68_url()) . '" alt="' . esc_attr__('NO IMAGE', THEME_NAME) . '" class="cocoon-analytics-ranking-thumb cocoon-analytics-ranking-thumb-no-image" width="120" height="68" loading="lazy">';
}
endif;

/**
 * アクセス解析画面の表示ビューを解決する
 */
if ( !function_exists( 'cocoon_analytics_resolve_view' ) ):
function cocoon_analytics_resolve_view($requested_view = null){
  // 機能が無効な場合は、古い集計系URLからでも設定画面を直接表示する
  if (!is_access_analytics_enable()) {
    return 'settings';
  }

  $allowed_views = array('dashboard', 'ranking', 'posts', 'terms', 'authors', 'lifecycle', 'export', 'settings');
  $view = is_null($requested_view) ? 'dashboard' : sanitize_key($requested_view);

  if (!in_array($view, $allowed_views, true)) {
    return 'dashboard';
  }

  return $view;
}
endif;

/**
 * タブナビを出力
 */
if ( !function_exists( 'cocoon_analytics_render_tabs' ) ):
function cocoon_analytics_render_tabs($current){
  // 機能が無効な場合は切り替え先がないため、単独の設定タブも出力しない
  if (!is_access_analytics_enable()) {
    return;
  }

  $tabs = array(
    'dashboard' => __('ダッシュボード', THEME_NAME),
    'ranking'   => __('ランキング', THEME_NAME),
    'posts'     => __('記事別', THEME_NAME),
    'terms'     => __('カテゴリー/タグ', THEME_NAME),
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
 * $extra_params: 期間変更時に失われないよう引き継ぐフィルター値（例: tax、pt、author等）の連想配列
 */
if ( !function_exists( 'cocoon_analytics_render_period_form' ) ):
function cocoon_analytics_render_period_form($current_preset, $from, $to, $view, $extra_params = array()){
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
    <?php // タクソノミーや投稿タイプなどの絞り込み条件を、期間変更の送信でも維持できるようhiddenで出力します ?>
    <?php foreach ((array) $extra_params as $param_key => $param_value): ?>
      <?php if ($param_value === '' || $param_value === null) { continue; } ?>
      <input type="hidden" name="<?php echo esc_attr($param_key); ?>" value="<?php echo esc_attr($param_value); ?>">
    <?php endforeach; ?>
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
    <?php
    // 入力欄の値は ISO 形式のため、表示用ラベルだけサイトの日付書式へ揃える
    $label_format = cocoon_analytics_full_date_format();
    $from_ts = strtotime($from);
    $to_ts = strtotime($to);
    $range_label = ($from_ts !== false && $to_ts !== false) ? sprintf(
      /* translators: 1: 開始日, 2: 終了日 */
      __('%1$s 〜 %2$s', THEME_NAME),
      date_i18n($label_format, $from_ts),
      date_i18n($label_format, $to_ts)
    ) : '';
    ?>
    <span class="cocoon-analytics-range-label"><?php echo esc_html($range_label); ?></span>
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
  // シェア率の分母となる期間全体のPV（指定が無い場合は表示中行の合計）
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
    $title     = $post ? cocoon_analytics_plain_title($post) : '';
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
      // アイキャッチ未設定の場合はテーマ標準のNO IMAGE画像を表示します
      echo cocoon_analytics_get_no_image_thumb_tag();
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
    echo '<div class="cocoon-analytics-ranking-pv-label">' . esc_html__('PV', THEME_NAME) . '</div>';
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
 * 投稿タイプのラベル名を取得（取得できない場合はスラッグをそのまま返す）
 */
if ( !function_exists( 'cocoon_analytics_post_type_label' ) ):
function cocoon_analytics_post_type_label($post_type){
  $type_object = get_post_type_object($post_type);
  return ($type_object && !empty($type_object->labels->name)) ? $type_object->labels->name : $post_type;
}
endif;

/**
 * 投稿タイプセレクタ
 *
 * name属性に post_type を使うとWP管理画面の予約パラメータと衝突するため、既定名は「pt」にしています。
 */
if ( !function_exists( 'cocoon_analytics_render_post_type_filter' ) ):
function cocoon_analytics_render_post_type_filter($current = 'all', $name = 'pt'){
  $types = cocoon_analytics_allowed_post_types();
  echo '<select name="' . esc_attr($name) . '">';
  echo '<option value="all"' . selected($current, 'all', false) . '>' . esc_html__('すべて', THEME_NAME) . '</option>';
  foreach ($types as $t) {
    // スラッグではなく管理画面と同じ投稿タイプのラベル名を表示します
    echo '<option value="' . esc_attr($t) . '"' . selected($current, $t, false) . '>' . esc_html(cocoon_analytics_post_type_label($t)) . '</option>';
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
    $title     = $post ? cocoon_analytics_plain_title($post) : '';
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
      // アイキャッチ未設定の場合はテーマ標準のNO IMAGE画像を表示します
      echo cocoon_analytics_get_no_image_thumb_tag();
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
    $growth_tip = sprintf(__( '直近%1$d日のPVとその前%1$d日のPVの比較', THEME_NAME), $days);
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
  // 直近52週間 + 今週 = 371日ぶんの日曜始まりでの描画
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

  //グリッドは日曜始まりなので、開始日からの相対日数で曜日名をロケール追従させる
  $wd = array();
  for ($i = 0; $i < 7; $i++) {
    $wd[$i] = date_i18n('D', strtotime('+' . $i . ' days', $start_ts));
  }

  //ツールチップは371セル分生成するため、書式取得はループ外で1回のみ
  $tip_format = cocoon_analytics_full_date_format();

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
      $first_ts = strtotime($first['date']);
      $m = date('n', $first_ts);
      if ($m !== $last_month) {
        $month_label = date_i18n('M', $first_ts);
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
      $tip = esc_attr(date_i18n($tip_format, strtotime($c['date'])) . ' : ' . number_format_i18n($pv) . ' ' . __('PV', THEME_NAME));
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
