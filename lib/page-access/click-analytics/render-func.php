<?php // クリック解析一覧の表示部品
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

if ( !function_exists( 'cocoon_click_link_preview' ) ):
function cocoon_click_link_preview($row){
  $raw = html_entity_decode((string) $row['anchor_text'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
  // 保存途中で途切れた画像HTMLも含めたタグの除去
  $label = trim(preg_replace('/\s+/u', ' ', strip_tags(preg_replace('/<[a-z!\/][^>]*(?:>|$)/is', '', $raw))));
  $image_url = cocoon_click_sanitize_image_url(isset($row['image_url']) ? $row['image_url'] : '');
  // 旧データの画像だけのキャプションから、引用符まで完結したsrcのみ復元
  if ($image_url === '' && $label === '' && preg_match('/<img\b[^>]*?\s+src\s*=\s*(["\'])(.*?)\1/is', $raw, $match)) {
    $image_url = cocoon_click_sanitize_image_url($match[2]);
  }
  if ($label === '') $label = $image_url !== '' || preg_match('/<img\b/i', $raw) ? __('画像', THEME_NAME) : $row['destination_url'];
  if (!empty($row['is_affiliate'])) $label .= ' [' . __('アフィリエイト', THEME_NAME) . ']';
  return array('label' => $label, 'image_url' => $image_url);
}
endif;

if ( !function_exists( 'cocoon_click_render_link_title' ) ):
function cocoon_click_render_link_title($row){
  return cocoon_click_link_preview($row)['label'];
}
endif;

if ( !function_exists( 'cocoon_click_image_can_autoload' ) ):
function cocoon_click_image_can_autoload($url){
  $url = cocoon_click_sanitize_image_url($url);
  // 動的な画像エンドポイントへの自動通信の禁止
  if ($url === '' || wp_parse_url($url, PHP_URL_QUERY) !== null) return false;
  $uploads = wp_get_upload_dir();
  $roots = array(array(get_cocoon_template_directory_uri(), get_cocoon_template_directory()));
  if (empty($uploads['error']) && !empty($uploads['baseurl']) && !empty($uploads['basedir'])) {
    $roots[] = array($uploads['baseurl'], $uploads['basedir']);
  }
  foreach ($roots as $root) {
    $base_url = rtrim($root[0], '/') . '/';
    if (strpos($url, $base_url) !== 0) continue;
    $relative = rawurldecode(explode('#', substr($url, strlen($base_url)), 2)[0]);
    if (preg_match('/[\x00-\x20\\\\]/', $relative) || !preg_match('/\.(?:png|jpe?g|gif|webp|avif|ico|bmp)$/i', $relative)) return false;
    $base_path = realpath($root[1]);
    $file = $base_path ? realpath($base_path . '/' . $relative) : false;
    // ディレクトリ移動・シンボリックリンクによる公開素材領域からの逸脱防止
    if (!$file || strpos($file, $base_path . DIRECTORY_SEPARATOR) !== 0 || !is_file($file) || !is_readable($file)) return false;
    $size = @getimagesize($file);
    return is_array($size) && !empty($size[0]) && !empty($size[1]);
  }
  return false;
}
endif;

if ( !function_exists( 'cocoon_click_render_links_table' ) ):
function cocoon_click_render_links_table($result, $show_source = true){
  if (!$result['rows']) {
    echo '<div class="notice notice-info inline"><p>' . esc_html__('該当するクリックデータがありません。', THEME_NAME) . '</p></div>';
    return;
  }
  $columns = array();
  if ($show_source) $columns['source'] = __('クリック元', THEME_NAME);
  $columns += array(
    'link' => __('リンク', THEME_NAME), 'location' => __('種別・位置', THEME_NAME),
    'impressions' => __('推定表示', THEME_NAME), 'clicks' => __('クリック', THEME_NAME),
    'unique' => __('日次ユニーク合計', THEME_NAME), 'ctr' => __('推定CTR', THEME_NAME),
    'arrival' => __('到着率', THEME_NAME), 'engagement' => __('エンゲージ率', THEME_NAME),
    'time' => __('平均クリック時間', THEME_NAME),
  );
  ?>
  <div class="cocoon-analytics-table-scroll cocoon-click-table-scroll" tabindex="0" role="region" aria-label="<?php echo esc_attr(__('クリック解析', THEME_NAME)); ?>">
    <table class="widefat striped cocoon-click-table<?php echo $show_source ? '' : ' cocoon-click-table-no-source'; ?>">
      <colgroup>
        <?php foreach ($columns as $key => $label): ?><col class="cocoon-click-col-<?php echo esc_attr($key); ?>"><?php endforeach; ?>
      </colgroup>
      <thead><tr>
        <?php foreach ($columns as $key => $label): ?><th scope="col" class="cocoon-click-cell-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></th><?php endforeach; ?>
      </tr></thead><tbody>
      <?php foreach ($result['rows'] as $row):
        $preview = cocoon_click_link_preview($row);
        $source_title = (int) $row['source_post_id'] > 0 ? (cocoon_analytics_plain_title($row['source_post_id']) ?: '#' . $row['source_post_id']) : __('複数記事', THEME_NAME);
        ?>
        <tr>
          <?php if ($show_source): ?>
            <td class="cocoon-click-cell-source">
              <?php if ((int) $row['source_post_id'] > 0): ?>
                <a class="cocoon-click-source-title" href="<?php echo esc_url(get_permalink($row['source_post_id'])); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr($source_title); ?>"><?php echo esc_html($source_title); ?></a>
              <?php else: ?><?php echo esc_html($source_title); ?><?php endif; ?>
            </td>
          <?php endif; ?>
          <td class="cocoon-click-cell-link">
            <div class="cocoon-click-link-preview">
              <?php if ($preview['image_url'] !== ''): ?>
                <?php if (cocoon_click_image_can_autoload($preview['image_url'])): ?>
                  <img class="cocoon-click-thumbnail" src="<?php echo esc_url($preview['image_url']); ?>" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer">
                <?php else: ?>
                  <span class="cocoon-click-image-consent">
                    <button type="button" class="button button-small cocoon-click-load-image" data-image-url="<?php echo esc_attr($preview['image_url']); ?>"><?php esc_html_e('画像を読み込む', THEME_NAME); ?></button>
                    <small><?php echo esc_html(sprintf(__('読み込み先: %s', THEME_NAME), wp_parse_url($preview['image_url'], PHP_URL_HOST))); ?></small>
                    <small class="cocoon-click-image-error" role="status" hidden><?php esc_html_e('画像を読み込めませんでした。', THEME_NAME); ?></small>
                  </span>
                <?php endif; ?>
              <?php endif; ?>
              <strong class="cocoon-click-link-caption" tabindex="0" title="<?php echo esc_attr($preview['label']); ?>"><?php echo esc_html($preview['label']); ?></strong>
            </div>
            <?php if (!empty($row['definition_count']) && $row['definition_count'] > 1): ?><small class="cocoon-click-link-note"><?php esc_html_e('代表リンク', THEME_NAME); ?></small><?php endif; ?>
            <code class="cocoon-click-link-url" tabindex="0" title="<?php echo esc_attr($row['destination_url']); ?>"><?php echo esc_html($row['destination_url']); ?></code>
            <?php if ($row['heading_label'] !== ''): ?><small class="cocoon-click-link-heading" title="<?php echo esc_attr($row['heading_label']); ?>"><?php echo esc_html($row['heading_label']); ?></small><?php endif; ?>
          </td>
          <td class="cocoon-click-cell-location">
            <div class="cocoon-click-link-badges"><span class="cocoon-click-badge"><?php echo esc_html(cocoon_click_type_label($row['destination_type'])); ?></span><span class="cocoon-click-badge"><?php echo esc_html(cocoon_click_area_label($row['semantic_area'])); ?></span></div>
            <small><?php echo esc_html($row['element_type'] . ' #' . ((int) $row['occurrence_no'] + 1)); ?></small>
          </td>
          <td class="cocoon-click-number"><?php echo number_format_i18n($row['weighted_impressions']); ?></td>
          <td class="cocoon-click-number cocoon-click-cell-clicks"><strong><?php echo number_format_i18n($row['clicks']); ?></strong></td>
          <td class="cocoon-click-number"><?php echo number_format_i18n($row['unique_clicks']); ?></td>
          <td class="cocoon-click-number">
            <span><?php echo esc_html(cocoon_click_percent($row['ctr'])); ?></span>
            <?php if (!$row['data_sufficient']): ?><small class="cocoon-click-insufficient"><?php esc_html_e('データ不足', THEME_NAME); ?></small><?php endif; ?>
            <?php if ($row['ctr_lower'] !== null || !$row['data_sufficient']): ?>
              <details class="cocoon-click-stat-details">
                <summary aria-label="<?php echo esc_attr(__('推定CTR', THEME_NAME) . ': ' . $preview['label'] . ' — ' . __('詳細', THEME_NAME)); ?>"><?php esc_html_e('詳細', THEME_NAME); ?></summary>
                <?php if ($row['ctr_lower'] !== null): ?><small><?php printf(esc_html__('95%% CI %1$s〜%2$s / n=%3$s', THEME_NAME), esc_html(cocoon_click_percent($row['ctr_lower'])), esc_html(cocoon_click_percent($row['ctr_upper'])), esc_html(number_format_i18n($row['effective_n'], 1))); ?></small><?php endif; ?>
                <?php if (!$row['data_sufficient']): ?><small><?php echo esc_html(cocoon_click_sufficiency_label($row)); ?></small><?php endif; ?>
              </details>
            <?php endif; ?>
          </td>
          <td class="cocoon-click-number"><?php echo esc_html(cocoon_click_percent($row['arrival_rate'])); ?></td>
          <td class="cocoon-click-number"><?php echo esc_html(cocoon_click_percent($row['engagement_rate'])); ?></td>
          <td class="cocoon-click-number"><?php echo $row['average_time_to_click_ms'] === null ? '—' : esc_html(number_format_i18n($row['average_time_to_click_ms'] / 1000, 1) . __('秒', THEME_NAME)); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php
}
endif;
