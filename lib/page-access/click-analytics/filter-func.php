<?php
// クリック解析の絞り込みと集計表示
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'cocoon_click_filter_options' ) ):
function cocoon_click_filter_options($view){
  $options = array(
    'device' => array('all' => __('すべて', THEME_NAME), 'desktop' => __('PC', THEME_NAME), 'tablet' => __('タブレット', THEME_NAME), 'mobile' => __('モバイル', THEME_NAME)),
    'area' => array('all' => __('すべて', THEME_NAME)),
    'link_type' => array('all' => __('すべて', THEME_NAME)),
  );
  if ($view === 'map') return $options;
  foreach (array('content', 'toc', 'blogcard', 'cta', 'related', 'header', 'navi', 'sidebar', 'footer', 'mobile_menu', 'other') as $area) {
    $options['area'][$area] = cocoon_click_area_label($area);
  }
  // 表の対象範囲と一致するリンク種別だけの選択候補
  $types = $view === 'internal' ? array() : array('external', 'affiliate', 'official', 'reference', 'social', 'download', 'mailto', 'tel', 'sms');
  if ($view === 'overview') $types = array_merge(array('internal', 'anchor'), $types);
  foreach ($types as $type) $options['link_type'][$type] = cocoon_click_type_label($type);
  return $options;
}
endif;

if ( !function_exists( 'cocoon_click_filter_values' ) ):
function cocoon_click_filter_values($view, $query){
  $filters = array();
  // 非表示の条件や不正なURL指定による、意図しない絞り込みの防止
  foreach (cocoon_click_filter_options($view) as $name => $options) {
    $value = isset($query[$name]) && is_string($query[$name]) ? $query[$name] : 'all';
    $filters[$name] = isset($options[$value]) ? $value : 'all';
  }
  $filters['source_post_id'] = isset($query['source_post_id']) && is_scalar($query['source_post_id']) ? max(0, (int) $query['source_post_id']) : 0;
  return $filters;
}
endif;

if ( !function_exists( 'cocoon_click_render_filter_select' ) ):
function cocoon_click_render_filter_select($name, $label, $options, $value){
  ?>
  <label><?php echo esc_html($label); ?>
    <select name="<?php echo esc_attr($name); ?>">
      <?php foreach ($options as $key => $text): ?>
        <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>><?php echo esc_html($text); ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <?php
}
endif;

if ( !function_exists( 'cocoon_click_render_filters' ) ):
function cocoon_click_render_filters($click_view, $preset, $from, $to, $filters, $table_args = array()){
  $options = cocoon_click_filter_options($click_view);
  $labels = array('device' => __('端末:', THEME_NAME), 'area' => __('掲載位置:', THEME_NAME), 'link_type' => __('リンク種別:', THEME_NAME));
  $advanced = $click_view === 'internal' ? array('device', 'area') : array('device', 'area', 'link_type');
  if ($click_view === 'map') $advanced = array();
  $active = array();
  foreach ($advanced as $name) {
    if ($filters[$name] !== 'all') $active[$name] = $labels[$name] . ' ' . $options[$name][$filters[$name]];
  }
  // 条件解除後も期間・記事・並び順を保持する、検証済みの画面パラメーター
  $query = array_merge(array('page' => 'theme-access', 'view' => 'clicks', 'click_view' => $click_view, 'period' => $preset), $filters, $table_args);
  if ($preset === 'custom') $query = array_merge($query, array('from' => $from, 'to' => $to));
  ?>
  <form id="cocoon-click-filters" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" class="cocoon-analytics-filter-bar cocoon-click-filter-bar">
    <input type="hidden" name="page" value="theme-access">
    <input type="hidden" name="view" value="clicks">
    <input type="hidden" name="click_view" value="<?php echo esc_attr($click_view); ?>">
    <?php foreach (array('order', 'direction') as $key): ?>
      <?php if (isset($table_args[$key])): ?><input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($table_args[$key]); ?>"><?php endif; ?>
    <?php endforeach; ?>
    <div class="cocoon-click-filter-main">
      <?php cocoon_click_render_filter_select('period', __('期間:', THEME_NAME), array('today' => __('今日', THEME_NAME), '7days' => __('直近7日', THEME_NAME), '30days' => __('直近30日', THEME_NAME), '90days' => __('直近90日', THEME_NAME), 'thismonth' => __('今月', THEME_NAME), 'all' => __('全期間', THEME_NAME), 'custom' => __('カスタム', THEME_NAME)), $preset); ?>
      <span class="cocoon-analytics-custom-range">
        <label><?php esc_html_e('開始日', THEME_NAME); ?><input type="date" name="from" value="<?php echo esc_attr($from); ?>"></label>
        <span aria-hidden="true">〜</span>
        <label><?php esc_html_e('終了日', THEME_NAME); ?><input type="date" name="to" value="<?php echo esc_attr($to); ?>"></label>
      </span>
      <?php
      if ($click_view === 'map') {
        cocoon_click_render_filter_select('device', $labels['device'], $options['device'], $filters['device']);
        cocoon_analytics_render_map_post_picker($filters['source_post_id']);
      } else {
        cocoon_analytics_render_post_picker('source_post_id', $filters['source_post_id'], array(
          'label' => __('クリック元ページ:', THEME_NAME),
          'placeholder' => __('すべてのページ（ページ名で絞り込み）', THEME_NAME),
        ));
      }
      ?>
      <button type="submit" class="button button-primary cocoon-click-filter-submit" <?php if (isset($table_args['group'])): ?>name="group" value="<?php echo esc_attr($table_args['group']); ?>"<?php endif; ?>><?php echo esc_html($click_view === 'map' ? __('表示', THEME_NAME) : __('結果を表示', THEME_NAME)); ?></button>
    </div>
    <?php if ($advanced): ?>
      <details class="cocoon-click-filter-details">
        <summary>
          <span><?php esc_html_e('詳細条件', THEME_NAME); ?></span>
          <span class="cocoon-click-filter-summary"><?php echo esc_html($click_view === 'internal' ? __('端末・掲載位置', THEME_NAME) : __('端末・掲載位置・リンク種別', THEME_NAME)); ?></span>
          <?php if ($active): ?><span class="cocoon-click-filter-count"><?php /* translators: %d: 適用中の詳細条件の数 */ printf(esc_html__('%d件を適用中', THEME_NAME), count($active)); ?></span><?php endif; ?>
        </summary>
        <div class="cocoon-click-filter-fields">
          <?php foreach ($advanced as $name) cocoon_click_render_filter_select($name, $labels[$name], $options[$name], $filters[$name]); ?>
        </div>
      </details>
      <?php if ($active): ?>
        <div class="cocoon-click-active-filters" aria-label="<?php esc_attr_e('適用中の詳細条件', THEME_NAME); ?>">
          <?php foreach ($active as $name => $text): ?>
            <?php /* translators: %s: 解除する条件の名前と値 */ $remove_label = sprintf(__('%sを解除', THEME_NAME), $text); ?>
            <a class="cocoon-click-filter-chip" href="<?php echo esc_url(add_query_arg(array_merge($query, array($name => 'all')), admin_url('admin.php'))); ?>" aria-label="<?php echo esc_attr($remove_label); ?>"><?php echo esc_html($text); ?><span aria-hidden="true">×</span></a>
          <?php endforeach; ?>
          <a class="cocoon-click-filter-clear" href="<?php echo esc_url(add_query_arg(array_merge($query, array_fill_keys($advanced, 'all')), admin_url('admin.php'))); ?>"><?php esc_html_e('詳細条件をクリア', THEME_NAME); ?></a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </form>
  <?php
}
endif;

if ( !function_exists( 'cocoon_click_render_group_switcher' ) ):
function cocoon_click_render_group_switcher($view, $group){
  $groups = array(
    'occurrence' => array(__('掲載箇所ごと', THEME_NAME), __('同じリンク先でも、ページ内の掲載箇所ごとに表示します。', THEME_NAME)),
    'destination' => array(__('リンク先ごと', THEME_NAME), __('同じリンク先へのクリックをまとめて表示します。', THEME_NAME)),
  );
  if ($view === 'external') $groups['domain'] = array(__('ドメインごと', THEME_NAME), __('同じドメインへのクリックをまとめて表示します。', THEME_NAME));
  if (!isset($groups[$group])) $group = 'occurrence';
  ?>
  <fieldset class="cocoon-click-group-switcher" aria-describedby="cocoon-click-group-description">
    <legend><?php esc_html_e('表のまとめ方', THEME_NAME); ?></legend>
    <div class="cocoon-click-group-options">
      <?php foreach ($groups as $key => $item): ?>
        <button type="submit" form="cocoon-click-filters" name="group" value="<?php echo esc_attr($key); ?>" aria-pressed="<?php echo $group === $key ? 'true' : 'false'; ?>"><?php echo esc_html($item[0]); ?></button>
      <?php endforeach; ?>
    </div>
    <p id="cocoon-click-group-description" class="description"><?php echo esc_html($groups[$group][1]); ?></p>
  </fieldset>
  <?php
}
endif;
