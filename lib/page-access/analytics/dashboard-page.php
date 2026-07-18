<?php //アクセス解析ダッシュボード - ページ本体（ビュー切替）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 権限チェック（親から呼ばれる前提だが二重チェック）
if (!current_user_can('manage_options')) {
  wp_die(__('このページにアクセスする管理者権限がありません。', THEME_NAME));
}

// 入力値
$view = isset($_GET['view']) ? sanitize_key($_GET['view']) : 'dashboard';
$allowed_views = array('dashboard', 'ranking', 'posts', 'terms', 'authors', 'lifecycle', 'export', 'settings');
if (!in_array($view, $allowed_views, true)) $view = 'dashboard';

$default_period = get_access_analytics_default_period();
$preset = isset($_GET['period']) ? sanitize_key($_GET['period']) : $default_period;
$from_in = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : '';
$to_in = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : '';
$period = cocoon_analytics_resolve_period($preset, $from_in, $to_in);
$from = $period['from'];
$to = $period['to'];

// パラメータ名に post_type を使うとWP管理画面の予約パラメータ（$typenow）と衝突し
// 「Cannot load theme-access.」エラーになるため、独自名「pt」を使用
$post_type_filter = isset($_GET['pt']) ? sanitize_key($_GET['pt']) : 'all';
$pt_arg = $post_type_filter === 'all' ? null : $post_type_filter;

?>
<div class="wrap admin-settings cocoon-analytics-wrap">
  <h1><?php _e('アクセス集計', THEME_NAME); ?></h1>
  <?php cocoon_analytics_render_tabs($view); ?>

<?php
// ダッシュボード機能が無効な場合は設定のみ表示
if (!is_access_analytics_enable() && $view !== 'settings') {
  echo '<div class="notice notice-warning"><p>';
  echo esc_html__('アクセス解析ダッシュボード機能は現在無効化されています。「設定」タブで有効化してください。', THEME_NAME);
  echo '</p></div>';
  echo '</div>';
  return;
}

// テーブル存在チェック
if ($view !== 'settings' && !is_accesses_table_exist()) {
  echo '<div class="notice notice-error"><p>';
  echo esc_html__('アクセス集計テーブルが存在しません。先に「集計設定」を開いてテーブルを作成してください。', THEME_NAME);
  echo '</p></div>';
  echo '</div>';
  return;
}

// --- 各ビュー ---
switch ($view) {

  case 'dashboard':
    cocoon_analytics_render_period_form($preset, $from, $to, 'dashboard');
    echo '<div class="cocoon-analytics-section">';
    cocoon_analytics_render_kpi_cards();
    echo '</div>';

    // アクセスカレンダー（直近52週ヒートマップ）― 横いっぱい
    ?>
    <div class="cocoon-analytics-section">
      <div class="cocoon-analytics-card">
        <h3><?php _e('アクセスカレンダー（直近52週）', THEME_NAME); ?></h3>
        <?php cocoon_analytics_render_heatmap(); ?>
      </div>
    </div>
    <?php

    $daily = cocoon_analytics_daily_pv($from, $to, $pt_arg);
    $by_type = cocoon_analytics_pv_by_post_type($from, $to);
    // 円グラフが煩雑にならないよう、上位10件+「その他」に集約
    if (is_array($by_type) && count($by_type) > 10) {
      $top_types = array_slice($by_type, 0, 10);
      $rest_types = array_slice($by_type, 10);
      $rest_pv = 0;
      foreach ($rest_types as $r) { $rest_pv += (int) $r['pv']; }
      if ($rest_pv > 0) {
        $top_types[] = array('post_type' => __('その他', THEME_NAME), 'pv' => $rest_pv);
      }
      $by_type = $top_types;
    }
    $by_dow = cocoon_analytics_pv_by_dow($from, $to);
    // カテゴリ別PV（上位10 + その他に集約）
    $by_category = cocoon_analytics_pv_by_taxonomy($from, $to, 'category', 100);
    if (is_array($by_category) && count($by_category) > 10) {
      $top_cat = array_slice($by_category, 0, 10);
      $rest_cat = array_slice($by_category, 10);
      $rest_pv = 0;
      foreach ($rest_cat as $r) { $rest_pv += (int) $r['pv']; }
      if ($rest_pv > 0) {
        $top_cat[] = array('term_id' => 0, 'name' => __('その他', THEME_NAME), 'slug' => 'others', 'pv' => $rest_pv);
      }
      $by_category = $top_cat;
    }
    // タグ別PV（上位10 + その他に集約）
    $by_tag = cocoon_analytics_pv_by_taxonomy($from, $to, 'post_tag', 100);
    if (is_array($by_tag) && count($by_tag) > 10) {
      $top_tag = array_slice($by_tag, 0, 10);
      $rest_tag = array_slice($by_tag, 10);
      $rest_pv = 0;
      foreach ($rest_tag as $r) { $rest_pv += (int) $r['pv']; }
      if ($rest_pv > 0) {
        $top_tag[] = array('term_id' => 0, 'name' => __('その他', THEME_NAME), 'slug' => 'others', 'pv' => $rest_pv);
      }
      $by_tag = $top_tag;
    }
    $top = cocoon_analytics_ranking($from, $to, $pt_arg, 10, 0);
    $period_total_pv = cocoon_analytics_total_pv($from, $to, $pt_arg);
    // 週次・月次PV推移は常時表示
    $show_weekly  = true;
    $show_monthly = true;
    $weekly  = cocoon_analytics_weekly_pv($from, $to, $pt_arg);
    // 月次PVデータも上部フィルターバーの期間選択と完全連動するように戻します
    $monthly = cocoon_analytics_monthly_pv($from, $to, $pt_arg);
    // 急上昇記事 TOP10（直近7日 vs その前7日）
    $trending = cocoon_analytics_trending(7, 10, 3);

    // 各タイルの可視性を組み立て、保存されたレイアウトを取得
    $visibility = array(
      'trend' => true,
      'category' => true, 'tag' => true, 'type' => true,
      'top' => true, 'trending' => true, 'dow' => true,
    );
    $tile_ids = cocoon_analytics_default_tile_ids($visibility);
    $layout = cocoon_analytics_get_user_layout($tile_ids, 2);

    // タイル描画クロージャ（id => [title, body]）
    $render_tile = function($id) use ($top, $period_total_pv, $trending, $preset, $from, $to) {
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
      );
      // 表示用の期間ラベルを作成します（カスタム期間の場合は日付範囲を表示します）
      $period_label = isset($presets[$preset]) ? $presets[$preset] : '';
      if ($preset === 'custom') {
        $period_label = sprintf('%s 〜 %s', $from, $to);
      }
      $suffix = $period_label ? ' （' . $period_label . '）' : '';

      $title = '';
      ob_start();
      switch ($id) {
        case 'trend':
          // 日次・週次・月次の3つのグラフを1つのタイルにまとめ、タブで切り替えられるようにします
          $title = __('PV推移', THEME_NAME) . $suffix;
          ?>
          <div class="cocoon-analytics-trend-switcher">
            <button type="button" class="cocoon-analytics-trend-btn" data-type="daily"><?php _e('日', THEME_NAME); ?></button>
            <button type="button" class="cocoon-analytics-trend-btn" data-type="weekly"><?php _e('週', THEME_NAME); ?></button>
            <button type="button" class="cocoon-analytics-trend-btn" data-type="monthly"><?php _e('月', THEME_NAME); ?></button>
          </div>
          <div style="height: 250px; position: relative;">
            <canvas id="cocoon-analytics-trend" role="img" aria-label="<?php echo esc_attr__('PV推移グラフ', THEME_NAME); ?>"></canvas>
          </div>
          <?php
          break;
        case 'category':
          $title = __('カテゴリー別PV', THEME_NAME) . $suffix;
          echo '<canvas id="cocoon-analytics-category" role="img" aria-label="' . esc_attr__('カテゴリー別PV構成', THEME_NAME) . '"></canvas>';
          break;
        case 'tag':
          $title = __('タグ別PV', THEME_NAME) . $suffix;
          echo '<canvas id="cocoon-analytics-tag" role="img" aria-label="' . esc_attr__('タグ別PV構成', THEME_NAME) . '"></canvas>';
          break;
        case 'type':
          $title = __('投稿タイプ別PV', THEME_NAME) . $suffix;
          echo '<canvas id="cocoon-analytics-type" role="img" aria-label="' . esc_attr__('投稿タイプ別PV構成', THEME_NAME) . '"></canvas>';
          break;
        case 'top':
          $title = __('人気記事 TOP10', THEME_NAME) . $suffix;
          cocoon_analytics_render_ranking_table($top, true, $period_total_pv);
          break;
        case 'trending':
          $title = __('急上昇記事 TOP10（直近7日）', THEME_NAME);
          cocoon_analytics_render_trending_list($trending, 7);
          break;
        case 'dow':
          $title = __('曜日別PV', THEME_NAME) . $suffix;
          echo '<canvas id="cocoon-analytics-dow" role="img" aria-label="' . esc_attr__('曜日別PV', THEME_NAME) . '"></canvas>';
          break;
      }
      $body = ob_get_clean();
      return array($title, $body);
    };
    ?>
    <div class="cocoon-analytics-tiles-toolbar">
      <span class="cocoon-analytics-tiles-hint"><?php _e('タイトルバーをドラッグしてタイルを並び替えできます。配置はユーザーごとに保存されます。', THEME_NAME); ?></span>
      <button type="button" class="button button-secondary cocoon-analytics-reset-layout"><?php _e('タイル配置を既定に戻す', THEME_NAME); ?></button>
      <span class="cocoon-analytics-save-status" aria-live="polite"></span>
    </div>
    <div class="cocoon-analytics-tiles" data-col-count="<?php echo (int) $layout['col_count']; ?>">
      <?php foreach ($layout['columns'] as $col_idx => $col_ids): ?>
        <div class="cocoon-analytics-tiles-col" data-col-index="<?php echo (int) $col_idx; ?>">
          <?php foreach ($col_ids as $tile_id):
            list($tile_title, $tile_body) = $render_tile($tile_id);
            if ($tile_body === '') continue;
          ?>
            <div class="cocoon-analytics-card cocoon-analytics-tile" data-tile-id="<?php echo esc_attr($tile_id); ?>">
              <h3 class="cocoon-analytics-tile-handle">
                <span class="cocoon-analytics-tile-grip" aria-hidden="true">⋮⋮</span>
                <?php echo esc_html($tile_title); ?>
              </h3>
              <div class="cocoon-analytics-tile-body"><?php echo $tile_body; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php
    // JS 側にデータを注入
    $GLOBALS['cocoon_analytics_chart_data'] = array(
      'daily'       => $daily,
      'weekly'      => $weekly,
      'monthly'     => $monthly,
      'by_type'     => $by_type,
      'by_dow'      => $by_dow,
      'by_category' => $by_category,
      'by_tag'      => $by_tag,
    );
    break;

  case 'ranking':
    ?>
    <form method="get" class="cocoon-analytics-filter-bar">
      <input type="hidden" name="page" value="theme-access">
      <input type="hidden" name="view" value="ranking">
      <!-- 期間フォームは別フォームのため、絞り込みフォーム側でも期間をhiddenで引き継ぎます -->
      <input type="hidden" name="period" value="<?php echo esc_attr($preset); ?>">
      <input type="hidden" name="from" value="<?php echo esc_attr($from); ?>">
      <input type="hidden" name="to" value="<?php echo esc_attr($to); ?>">
      <label><?php _e('投稿タイプ:', THEME_NAME); ?>
        <?php cocoon_analytics_render_post_type_filter($post_type_filter); ?>
      </label>
      <?php submit_button(__('絞り込み', THEME_NAME), 'secondary', '', false); ?>
    </form>
    <?php
    // 期間選択フォームは独立したformとして絞り込みフォームの下に配置します（form入れ子を避けるため）
    cocoon_analytics_render_period_form($preset, $from, $to, 'ranking');
    $rows = cocoon_analytics_ranking($from, $to, $pt_arg, 100, 0);
    if (empty($rows)) { cocoon_analytics_render_empty_notice(); }
    else {
      $period_total_pv = cocoon_analytics_total_pv($from, $to, $pt_arg);
      cocoon_analytics_render_ranking_table($rows, true, $period_total_pv);
    }
    break;

  case 'posts':
    $author = isset($_GET['author']) ? (int) $_GET['author'] : 0;
    $category = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;
    $min_pv = isset($_GET['min_pv']) && $_GET['min_pv'] !== '' ? (int) $_GET['min_pv'] : -1;
    $max_pv = isset($_GET['max_pv']) && $_GET['max_pv'] !== '' ? (int) $_GET['max_pv'] : -1;
    $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    $page_num = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;

    // 初期表示時の著者名とカテゴリ名をIDから取得します
    $author_name = '';
    if ($author > 0) {
      $user_info = get_userdata($author);
      if ($user_info) {
        $author_name = $user_info->display_name;
      }
    }
    $category_name = '';
    if ($category > 0) {
      $cat_info = get_category($category);
      if ($cat_info) {
        $category_name = $cat_info->name;
      }
    }
    ?>
    <form method="get" class="cocoon-analytics-filter-bar">
      <input type="hidden" name="page" value="theme-access">
      <input type="hidden" name="view" value="posts">
      <input type="hidden" name="period" value="<?php echo esc_attr($preset); ?>">
      <input type="hidden" name="from" value="<?php echo esc_attr($from); ?>">
      <input type="hidden" name="to" value="<?php echo esc_attr($to); ?>">
      <label><?php _e('投稿タイプ:', THEME_NAME); ?>
        <?php cocoon_analytics_render_post_type_filter($post_type_filter); ?>
      </label>
      <!-- 著者名の検索サジェスト入力欄です（実際の送信値はhidden inputにセットされます） -->
      <label class="cocoon-analytics-suggest-container"><?php _e('著者:', THEME_NAME); ?>
        <input type="text" class="cocoon-analytics-suggest-input" data-type="author" value="<?php echo esc_attr($author_name); ?>" placeholder="<?php esc_attr_e('名前で検索...', THEME_NAME); ?>" style="width:130px;" autocomplete="off">
        <input type="hidden" name="author" class="cocoon-analytics-suggest-hidden" value="<?php echo esc_attr($author); ?>">
        <div class="cocoon-analytics-suggest-dropdown"></div>
      </label>
      <!-- カテゴリー名の検索サジェスト入力欄です -->
      <label class="cocoon-analytics-suggest-container"><?php _e('カテゴリー:', THEME_NAME); ?>
        <input type="text" class="cocoon-analytics-suggest-input" data-type="category" value="<?php echo esc_attr($category_name); ?>" placeholder="<?php esc_attr_e('カテゴリー名で検索...', THEME_NAME); ?>" style="width:130px;" autocomplete="off">
        <input type="hidden" name="cat" class="cocoon-analytics-suggest-hidden" value="<?php echo esc_attr($category); ?>">
        <div class="cocoon-analytics-suggest-dropdown"></div>
      </label>
      <label><?php _e('最小PV:', THEME_NAME); ?>
        <input type="number" name="min_pv" value="<?php echo $min_pv >= 0 ? esc_attr($min_pv) : ''; ?>" min="0" style="width:80px;">
      </label>
      <label><?php _e('最大PV:', THEME_NAME); ?>
        <input type="number" name="max_pv" value="<?php echo $max_pv >= 0 ? esc_attr($max_pv) : ''; ?>" min="0" style="width:80px;">
      </label>
      <label><?php _e('並び:', THEME_NAME); ?>
        <select name="order">
          <option value="DESC" <?php selected($order, 'DESC'); ?>><?php _e('PV降順', THEME_NAME); ?></option>
          <option value="ASC" <?php selected($order, 'ASC'); ?>><?php _e('PV昇順（低PV）', THEME_NAME); ?></option>
        </select>
      </label>
      <?php submit_button(__('絞り込み', THEME_NAME), 'secondary', '', false); ?>
    </form>
    <?php cocoon_analytics_render_period_form($preset, $from, $to, 'posts'); ?>
    <?php
    $result = cocoon_analytics_posts_table($from, $to, array(
      'post_types' => $pt_arg,
      'author' => $author,
      'category' => $category,
      'order' => $order,
      'min_pv' => $min_pv,
      'max_pv' => $max_pv,
      'per_page' => 50,
      'page' => $page_num,
    ));
    $rows = $result['rows'];
    $total = $result['total'];
    if (empty($rows)) {
      cocoon_analytics_render_empty_notice();
    } else {
      echo '<p>' . esc_html(sprintf(__('全 %s 件', THEME_NAME), number_format_i18n($total))) . '</p>';
      // 狭い画面でのテーブル崩れ防止用の横スクロール領域
      echo '<div class="cocoon-analytics-table-scroll">';
      echo '<table class="wp-list-table widefat fixed striped cocoon-analytics-table">';
      echo '<thead><tr><th>' . esc_html__('タイトル', THEME_NAME) . '</th><th style="width:100px;">' . esc_html__('タイプ', THEME_NAME) . '</th><th style="width:100px;">' . esc_html__('著者', THEME_NAME) . '</th><th style="width:130px;">' . esc_html__('公開日', THEME_NAME) . '</th><th style="width:80px;">PV</th></tr></thead><tbody>';
      foreach ($rows as $r) {
        $title = cocoon_analytics_plain_title($r['post_id']);
        if (empty($title)) $title = '(' . __('無題', THEME_NAME) . ')';
        $author_obj = get_userdata($r['post_author']);
        $author_name = $author_obj ? $author_obj->display_name : '—';
        echo '<tr>';
        echo '<td><strong><a href="' . esc_url(get_edit_post_link($r['post_id'])) . '">' . esc_html($title) . '</a></strong></td>';
        echo '<td>' . esc_html($r['post_type']) . '</td>';
        echo '<td>' . esc_html($author_name) . '</td>';
        echo '<td>' . esc_html(mysql2date(get_option('date_format'), $r['post_date'])) . '</td>';
        echo '<td>' . esc_html(number_format_i18n($r['pv'])) . '</td>';
        echo '</tr>';
      }
      echo '</tbody></table>';
      echo '</div>';

      // ページネーション
      $per_page = $result['per_page'];
      $total_pages = (int) ceil($total / $per_page);
      if ($total_pages > 1) {
        $base = add_query_arg(array(
          'page' => 'theme-access', 'view' => 'posts',
          'period' => $preset, 'from' => $from, 'to' => $to,
          'pt' => $post_type_filter, 'author' => $author, 'cat' => $category,
          'min_pv' => $min_pv >= 0 ? $min_pv : '', 'max_pv' => $max_pv >= 0 ? $max_pv : '',
          'order' => $order,
          'paged' => '%#%',
        ), admin_url('admin.php'));
        echo '<div class="tablenav"><div class="tablenav-pages">';
        echo paginate_links(array(
          'base' => $base,
          'format' => '',
          'current' => $page_num,
          'total' => $total_pages,
          'prev_text' => '‹',
          'next_text' => '›',
        ));
        echo '</div></div>';
      }
    }
    break;

  case 'terms':
    $tax = isset($_GET['tax']) ? sanitize_key($_GET['tax']) : 'category';
    // タクソノミーはオブジェクトで取得し、管理画面と同じラベル名を表示できるようにします
    $available_tax = get_taxonomies(array('public' => true), 'objects');
    // 投稿フォーマット（post_format）は集計対象として適さないため選択肢から除外します
    unset($available_tax['post_format']);
    if (!array_key_exists($tax, $available_tax)) $tax = 'category';
    ?>
    <form method="get" class="cocoon-analytics-filter-bar">
      <input type="hidden" name="page" value="theme-access">
      <input type="hidden" name="view" value="terms">
      <?php /* タクソノミー切替時に選択中の期間が既定値へ戻ってしまわないよう、期間パラメータを維持します */ ?>
      <input type="hidden" name="period" value="<?php echo esc_attr($preset); ?>">
      <input type="hidden" name="from" value="<?php echo esc_attr($from); ?>">
      <input type="hidden" name="to" value="<?php echo esc_attr($to); ?>">
      <label><?php _e('タクソノミー:', THEME_NAME); ?>
        <select name="tax">
          <?php foreach ($available_tax as $t): ?>
            <option value="<?php echo esc_attr($t->name); ?>" <?php selected($tax, $t->name); ?>><?php echo esc_html($t->labels->name); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <?php submit_button(__('適用', THEME_NAME), 'secondary', '', false); ?>
    </form>
    <?php cocoon_analytics_render_period_form($preset, $from, $to, 'terms'); ?>
    <?php
    $rows = cocoon_analytics_pv_by_taxonomy($from, $to, $tax, 100);
    if (empty($rows)) {
      cocoon_analytics_render_empty_notice();
    } else {
      // 見出しにはスラッグではなく管理画面と同じタクソノミーのラベル名を表示します
      $tax_label = isset($available_tax[$tax]) ? $available_tax[$tax]->labels->name : $tax;
      echo '<div class="cocoon-analytics-grid-2">';
      echo '<div class="cocoon-analytics-card"><h3>' . esc_html(sprintf(__('%s 別PV ランキング', THEME_NAME), $tax_label)) . '</h3>';
      // 狭い画面でのテーブル崩れ防止用の横スクロール領域
      echo '<div class="cocoon-analytics-table-scroll">';
      echo '<table class="wp-list-table widefat fixed striped cocoon-analytics-table">';
      echo '<thead><tr><th style="width:50px;">#</th><th>' . esc_html__('名称', THEME_NAME) . '</th><th style="width:120px;">PV</th></tr></thead><tbody>';
      $i = 0;
      foreach ($rows as $r) {
        $i++;
        $link = get_term_link((int) $r['term_id'], $tax);
        echo '<tr><td>' . esc_html($i) . '</td><td>';
        if (!is_wp_error($link)) echo '<a href="' . esc_url($link) . '" target="_blank" rel="noopener">';
        echo esc_html($r['name']);
        if (!is_wp_error($link)) echo '</a>';
        echo '</td><td>' . esc_html(number_format_i18n($r['pv'])) . '</td></tr>';
      }
      echo '</tbody></table></div></div>';
      echo '<div class="cocoon-analytics-card"><h3>' . esc_html__('上位10分布', THEME_NAME) . '</h3>';
      echo '<canvas id="cocoon-analytics-terms" role="img"></canvas></div>';
      echo '</div>';
      $GLOBALS['cocoon_analytics_chart_data'] = array('terms' => array_slice($rows, 0, 10));
    }
    break;

  case 'authors':
    cocoon_analytics_render_period_form($preset, $from, $to, 'authors');
    $rows = cocoon_analytics_pv_by_author($from, $to, 100);
    if (empty($rows)) {
      cocoon_analytics_render_empty_notice();
    } else {
      echo '<div class="cocoon-analytics-grid-2">';
      echo '<div class="cocoon-analytics-card"><h3>' . esc_html__('著者別集計', THEME_NAME) . '</h3>';
      // 狭い画面でのテーブル崩れ防止用の横スクロール領域
      echo '<div class="cocoon-analytics-table-scroll">';
      echo '<table class="wp-list-table widefat fixed striped cocoon-analytics-table">';
      echo '<thead><tr><th style="width:36px;">#</th><th>' . esc_html__('著者', THEME_NAME) . '</th><th style="width:70px;">' . esc_html__('記事数', THEME_NAME) . '</th><th style="width:70px;">' . esc_html__('総PV', THEME_NAME) . '</th><th style="width:90px;">' . esc_html__('平均PV/記事', THEME_NAME) . '</th></tr></thead><tbody>';
      $i = 0;
      $chart_rows = array();
      foreach ($rows as $r) {
        $i++;
        $u = get_userdata($r['post_author']);
        $name = $u ? $u->display_name : '(' . __('不明', THEME_NAME) . ')';
        echo '<tr><td>' . esc_html($i) . '</td><td>' . esc_html($name) . '</td><td>' . esc_html(number_format_i18n($r['posts_with_pv'])) . '</td><td>' . esc_html(number_format_i18n($r['pv'])) . '</td><td>' . esc_html(number_format_i18n($r['avg_pv'], 1)) . '</td></tr>';
        $chart_rows[] = array('name' => $name, 'pv' => $r['pv']);
      }
      echo '</tbody></table></div></div>';
      echo '<div class="cocoon-analytics-card"><h3>' . esc_html__('著者別PV（上位10）', THEME_NAME) . '</h3>';
      echo '<canvas id="cocoon-analytics-authors" role="img"></canvas></div>';
      echo '</div>';
      $GLOBALS['cocoon_analytics_chart_data'] = array('authors' => array_slice($chart_rows, 0, 10));
    }
    break;

  case 'lifecycle':
    // 遷移パラメータから投稿IDを取得します（ない場合は0）
    $post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0;
    $initial_lifecycle = array();
    $initial_title = '';
    // 投稿IDが指定されている場合は、初期グラフ表示用のデータを読み込みます
    if ($post_id > 0) {
      $initial_lifecycle = cocoon_analytics_lifecycle($post_id);
      $initial_title = cocoon_analytics_plain_title($post_id) ?: '(' . __('不明', THEME_NAME) . ')';
      // 初期データとしてライフサイクル履歴と記事公開日（post_date）を格納します
      $GLOBALS['cocoon_analytics_chart_data'] = array(
        'lifecycle' => $initial_lifecycle,
        'post_date' => get_the_date('Y-m-d', $post_id),
      );
    }
    ?>
    <!-- ライフサイクル画面のメインコンテナ。初期投稿IDをデータ属性として保持させます -->
    <div class="cocoon-analytics-lifecycle-container" data-initial-post-id="<?php echo esc_attr($post_id); ?>">
      <!-- 左側カラム: キーワード検索ボックスとスクロール可能な記事リスト -->
      <div class="lifecycle-sidebar">
        <div class="lifecycle-search-box">
          <input type="text" id="lifecycle-search-input" placeholder="<?php esc_attr_e('記事タイトルで検索...', THEME_NAME); ?>" autocomplete="off">
          <span class="clear-search-btn" id="clear-search-btn" style="display:none;">&times;</span>
        </div>
        <div id="lifecycle-posts-list" class="lifecycle-posts-list">
          <!-- JavaScriptによってここに記事カードが追加されていきます -->
        </div>
        <!-- 読み込み中を示すスピナー（初期状態は非表示） -->
        <div id="lifecycle-list-loader" class="lifecycle-loader" style="display:none;">
          <span class="spinner is-active" style="float:none; margin:10px auto; display:block;"></span>
        </div>
      </div>

      <!-- 右側カラム: 選択された記事のアクセス推移グラフ -->
      <div class="lifecycle-content">
        <h3 id="lifecycle-chart-title">
          <?php echo $initial_title ? esc_html($initial_title) : esc_html__('記事を選択してください', THEME_NAME); ?>
        </h3>

        <!-- 期間選択のトグルボタン（記事選択時のみJSで表示制御します） -->
        <div id="lifecycle-period-selector" class="lifecycle-period-selector" style="<?php echo $post_id > 0 ? '' : 'display:none;'; ?>">
          <button type="button" class="lifecycle-period-btn is-active" data-period="90"><?php _e('3ヶ月', THEME_NAME); ?></button>
          <button type="button" class="lifecycle-period-btn" data-period="180"><?php _e('6ヶ月', THEME_NAME); ?></button>
          <button type="button" class="lifecycle-period-btn" data-period="365"><?php _e('1年', THEME_NAME); ?></button>
          <button type="button" class="lifecycle-period-btn" data-period="all"><?php _e('全期間', THEME_NAME); ?></button>
          <button type="button" class="lifecycle-period-btn" data-period="custom" id="lifecycle-custom-period-btn"><?php _e('カスタム', THEME_NAME); ?></button>

          <!-- カスタム期間の日付カレンダー選択フォーム（初期は非表示にします） -->
          <div id="lifecycle-custom-range" class="lifecycle-custom-range" style="display:none;">
            <input type="date" id="lifecycle-custom-from" class="lifecycle-date-picker">
            <span>〜</span>
            <input type="date" id="lifecycle-custom-to" class="lifecycle-date-picker">
          </div>
        </div>

        <div class="cocoon-analytics-card lifecycle-chart-card">
          <!-- 記事が選択されていないときに表示するメッセージエリア -->
          <div id="lifecycle-chart-placeholder" class="lifecycle-chart-placeholder" style="<?php echo $post_id > 0 ? 'display:none;' : ''; ?>">
            <div class="placeholder-icon">📈</div>
            <p><?php _e('左側のリストから記事を選択すると、公開後のアクセス推移グラフが表示されます。', THEME_NAME); ?></p>
          </div>
          <!-- グラフを描画するためのキャンバス領域 -->
          <div id="lifecycle-chart-wrapper" style="<?php echo $post_id > 0 ? '' : 'display:none;'; ?> height: 400px; position: relative;">
            <canvas id="cocoon-analytics-lifecycle" role="img"></canvas>
          </div>
        </div>
      </div>
    </div>
    <?php
    break;

  case 'export':
    if (!is_access_analytics_export_enable()) {
      echo '<div class="notice notice-warning"><p>' . esc_html__('エクスポート機能は無効化されています。', THEME_NAME) . '</p></div>';
      break;
    }
    ?>
    <form method="get" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="cocoon-analytics-export-form">
      <input type="hidden" name="action" value="cocoon_analytics_export">
      <?php wp_nonce_field('cocoon_analytics_export', '_wpnonce'); ?>
      <table class="form-table"><tbody>
        <tr><th><?php _e('対象', THEME_NAME); ?></th><td>
          <select name="target">
            <option value="daily"><?php _e('日次PV推移', THEME_NAME); ?></option>
            <option value="ranking"><?php _e('人気記事ランキング', THEME_NAME); ?></option>
            <option value="posts"><?php _e('記事別全量', THEME_NAME); ?></option>
            <option value="authors"><?php _e('著者別', THEME_NAME); ?></option>
          </select>
        </td></tr>
        <tr><th><?php _e('形式', THEME_NAME); ?></th><td>
          <select name="format">
            <option value="csv">CSV</option>
            <option value="json">JSON</option>
          </select>
        </td></tr>
        <tr><th><?php _e('期間', THEME_NAME); ?></th><td>
          <input type="date" name="from" value="<?php echo esc_attr($from); ?>">
          <?php echo esc_html__('〜', THEME_NAME); ?>
          <input type="date" name="to" value="<?php echo esc_attr($to); ?>">
        </td></tr>
      </tbody></table>
      <?php submit_button(__('ダウンロード', THEME_NAME), 'primary'); ?>
    </form>
    <?php
    break;

  case 'settings':
    require_once dirname(__FILE__) . '/settings-forms.php';
    break;
}
?>
</div><!-- /.wrap -->
