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

$post_type_filter = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : 'all';
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
    // 初心者向け: 円グラフが煩雑にならないよう、上位10件+「その他」に集約
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
    $monthly = cocoon_analytics_monthly_pv($from, $to, $pt_arg);
    // 急上昇記事 TOP10（直近7日 vs その前7日）
    $trending = cocoon_analytics_trending(7, 10, 3);

    // 各タイルの可視性を組み立て、保存されたレイアウトを取得
    $visibility = array(
      'daily' => true, 'weekly' => $show_weekly, 'monthly' => $show_monthly,
      'category' => true, 'tag' => true, 'type' => true,
      'top' => true, 'trending' => true, 'dow' => true,
    );
    $tile_ids = cocoon_analytics_default_tile_ids($visibility);
    $layout = cocoon_analytics_get_user_layout($tile_ids, 2);

    // タイル描画クロージャ（id => [title, body]）
    $render_tile = function($id) use ($top, $period_total_pv, $trending) {
      $title = '';
      ob_start();
      switch ($id) {
        case 'daily':
          $title = __('日次PV推移', THEME_NAME);
          echo '<canvas id="cocoon-analytics-daily" role="img" aria-label="' . esc_attr__('日次PV推移グラフ', THEME_NAME) . '"></canvas>';
          break;
        case 'weekly':
          $title = __('週次PV推移', THEME_NAME);
          echo '<canvas id="cocoon-analytics-weekly" role="img" aria-label="' . esc_attr__('週次PV推移グラフ', THEME_NAME) . '"></canvas>';
          break;
        case 'monthly':
          $title = __('月次PV推移', THEME_NAME);
          echo '<canvas id="cocoon-analytics-monthly" role="img" aria-label="' . esc_attr__('月次PV推移グラフ', THEME_NAME) . '"></canvas>';
          break;
        case 'category':
          $title = __('カテゴリ別PV', THEME_NAME);
          echo '<canvas id="cocoon-analytics-category" role="img" aria-label="' . esc_attr__('カテゴリ別PV構成', THEME_NAME) . '"></canvas>';
          break;
        case 'tag':
          $title = __('タグ別PV', THEME_NAME);
          echo '<canvas id="cocoon-analytics-tag" role="img" aria-label="' . esc_attr__('タグ別PV構成', THEME_NAME) . '"></canvas>';
          break;
        case 'type':
          $title = __('投稿タイプ別PV', THEME_NAME);
          echo '<canvas id="cocoon-analytics-type" role="img" aria-label="' . esc_attr__('投稿タイプ別PV構成', THEME_NAME) . '"></canvas>';
          break;
        case 'top':
          $title = __('人気記事 TOP10', THEME_NAME);
          cocoon_analytics_render_ranking_table($top, true, $period_total_pv);
          break;
        case 'trending':
          $title = __('急上昇記事 TOP10（直近7日）', THEME_NAME);
          cocoon_analytics_render_trending_list($trending, 7);
          break;
        case 'dow':
          $title = __('曜日別PV', THEME_NAME);
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
      <?php cocoon_analytics_render_period_form($preset, $from, $to, 'ranking'); ?>
      <label><?php _e('投稿タイプ:', THEME_NAME); ?>
        <?php cocoon_analytics_render_post_type_filter($post_type_filter); ?>
      </label>
      <?php submit_button(__('絞り込み', THEME_NAME), 'secondary', '', false); ?>
    </form>
    <?php
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
      <label><?php _e('著者ID:', THEME_NAME); ?>
        <input type="number" name="author" value="<?php echo esc_attr($author); ?>" min="0" style="width:80px;">
      </label>
      <label><?php _e('カテゴリID:', THEME_NAME); ?>
        <input type="number" name="cat" value="<?php echo esc_attr($category); ?>" min="0" style="width:80px;">
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
      echo '<table class="wp-list-table widefat fixed striped cocoon-analytics-table">';
      echo '<thead><tr><th>' . esc_html__('タイトル', THEME_NAME) . '</th><th style="width:100px;">' . esc_html__('タイプ', THEME_NAME) . '</th><th style="width:100px;">' . esc_html__('著者', THEME_NAME) . '</th><th style="width:130px;">' . esc_html__('公開日', THEME_NAME) . '</th><th style="width:80px;">PV</th></tr></thead><tbody>';
      foreach ($rows as $r) {
        $title = get_the_title($r['post_id']);
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

      // ページネーション
      $per_page = $result['per_page'];
      $total_pages = (int) ceil($total / $per_page);
      if ($total_pages > 1) {
        $base = add_query_arg(array(
          'page' => 'theme-access', 'view' => 'posts',
          'period' => $preset, 'from' => $from, 'to' => $to,
          'post_type' => $post_type_filter, 'author' => $author, 'cat' => $category,
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
    $available_tax = get_taxonomies(array('public' => true), 'names');
    if (!in_array($tax, $available_tax, true)) $tax = 'category';
    ?>
    <form method="get" class="cocoon-analytics-filter-bar">
      <input type="hidden" name="page" value="theme-access">
      <input type="hidden" name="view" value="terms">
      <label><?php _e('タクソノミー:', THEME_NAME); ?>
        <select name="tax">
          <?php foreach ($available_tax as $t): ?>
            <option value="<?php echo esc_attr($t); ?>" <?php selected($tax, $t); ?>><?php echo esc_html($t); ?></option>
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
      echo '<div class="cocoon-analytics-grid-2">';
      echo '<div class="cocoon-analytics-card"><h3>' . esc_html(sprintf(__('%s 別PV ランキング', THEME_NAME), $tax)) . '</h3>';
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
      echo '</tbody></table></div>';
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
      echo '<table class="wp-list-table widefat fixed striped cocoon-analytics-table">';
      echo '<thead><tr><th style="width:50px;">#</th><th>' . esc_html__('著者', THEME_NAME) . '</th><th style="width:100px;">' . esc_html__('記事数', THEME_NAME) . '</th><th style="width:100px;">' . esc_html__('総PV', THEME_NAME) . '</th><th style="width:120px;">' . esc_html__('平均PV/記事', THEME_NAME) . '</th></tr></thead><tbody>';
      $i = 0;
      $chart_rows = array();
      foreach ($rows as $r) {
        $i++;
        $u = get_userdata($r['post_author']);
        $name = $u ? $u->display_name : '(' . __('不明', THEME_NAME) . ')';
        echo '<tr><td>' . esc_html($i) . '</td><td>' . esc_html($name) . '</td><td>' . esc_html(number_format_i18n($r['posts_with_pv'])) . '</td><td>' . esc_html(number_format_i18n($r['pv'])) . '</td><td>' . esc_html(number_format_i18n($r['avg_pv'], 1)) . '</td></tr>';
        $chart_rows[] = array('name' => $name, 'pv' => $r['pv']);
      }
      echo '</tbody></table></div>';
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
      $initial_title = get_the_title($post_id) ?: '(' . __('不明', THEME_NAME) . ')';
      $GLOBALS['cocoon_analytics_chart_data'] = array('lifecycle' => $initial_lifecycle);
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
