<?php //クリック解析設定フォーム
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
?>
<div class="postbox">
  <h2 class="hndle"><?php _e('クリック解析', THEME_NAME); ?></h2>
  <div class="inside">
    <p><?php _e('内部・外部リンクのクリック、実表示、掲載位置を非同期で集計します。アーカイブ、検索結果は初版の計測対象外です。', THEME_NAME); ?></p>
    <table class="form-table"><tbody>
      <tr><th><?php generate_label_tag(OP_CLICK_ANALYTICS_ENABLE, __('クリック解析', THEME_NAME)); ?></th><td><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_ENABLE, is_click_analytics_enable(), __('クリック解析を有効にする', THEME_NAME)); ?><p class="description"><?php _e('初期設定では有効です。無効にした設定はアップデート後も維持されます。', THEME_NAME); ?></p></td></tr>
      <tr><th><?php _e('計測対象', THEME_NAME); ?></th><td>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_TRACK_INTERNAL, is_click_analytics_track_internal(), __('内部リンク', THEME_NAME)); ?></p>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_TRACK_EXTERNAL, is_click_analytics_track_external(), __('外部リンク', THEME_NAME)); ?></p>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_TRACK_SPECIAL, is_click_analytics_track_special(), __('アンカー・ダウンロード・電話・メール', THEME_NAME)); ?></p>
      </td></tr>
      <tr><th><?php _e('詳細計測', THEME_NAME); ?></th><td>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_IMPRESSIONS, is_click_analytics_impressions_enable(), __('リンクの実表示と推定CTRを計測する', THEME_NAME)); ?></p>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_HEATMAP, is_click_analytics_heatmap_enable(), __('丸めたクリック位置を計測する', THEME_NAME)); ?></p>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_OUTCOMES, is_click_analytics_outcomes_enable(), __('内部リンクの到着・エンゲージを計測する', THEME_NAME)); ?></p>
      </td></tr>
      <tr><th><?php _e('プライバシー', THEME_NAME); ?></th><td>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_EXCLUDE_LOGGED_IN, is_click_analytics_exclude_logged_in(), __('ログインユーザーを除外する', THEME_NAME)); ?></p>
        <p><?php generate_checkbox_tag(OP_CLICK_ANALYTICS_RESPECT_PRIVACY, is_click_analytics_respect_privacy(), __('DNT/GPCを尊重する', THEME_NAME)); ?></p>
        <p class="description"><?php _e('IPアドレス、User-Agent本文、完全リファラー、入力値、生座標は保存しません。', THEME_NAME); ?></p>
      </td></tr>
      <tr><th><?php _e('自動サンプリング', THEME_NAME); ?></th><td><strong><?php echo esc_html(get_click_analytics_sampling_rate()); ?>%</strong><p class="description"><?php _e('表示回数の計測にだけ適用される抽出率です。クリックは抽出にかかわらず常に全数を記録します。直近7日間の規模に応じて100%、20%、5%、1%へ毎日自動調整します。データ不足時は10%です。', THEME_NAME); ?></p></td></tr>
      <tr><th><?php generate_label_tag(OP_CLICK_ANALYTICS_DAILY_RETENTION, __('日次・位置データ保持', THEME_NAME)); ?></th><td><?php generate_selectbox_tag(OP_CLICK_ANALYTICS_DAILY_RETENTION, array('30' => __('30日', THEME_NAME), '90' => __('90日', THEME_NAME), '400' => __('400日', THEME_NAME)), get_click_analytics_daily_retention()); ?><p class="description"><?php esc_html_e('日次データは月次集計の確定まで、保持期限を超えて残る場合があります。', THEME_NAME); ?></p></td></tr>
      <tr><th><?php generate_label_tag(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, __('月次データ保持', THEME_NAME)); ?></th><td><?php generate_selectbox_tag(OP_CLICK_ANALYTICS_MONTHLY_RETENTION, array('12' => __('12か月', THEME_NAME), '24' => __('24か月', THEME_NAME), '60' => __('60か月', THEME_NAME)), get_click_analytics_monthly_retention()); ?></td></tr>
      <tr><th><?php generate_label_tag(OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS, __('除外ドメイン', THEME_NAME)); ?></th><td><?php generate_textarea_tag(OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS, esc_textarea(get_theme_option(OP_CLICK_ANALYTICS_EXCLUDED_DOMAINS, '')), "example.com\nsub.example.net", 4, 60); ?><p class="description"><?php _e('1行に1ドメインを入力します。', THEME_NAME); ?></p></td></tr>
      <tr><th><?php generate_label_tag(OP_CLICK_ANALYTICS_EXCLUDED_URLS, __('除外URL文字列', THEME_NAME)); ?></th><td><?php generate_textarea_tag(OP_CLICK_ANALYTICS_EXCLUDED_URLS, esc_textarea(get_theme_option(OP_CLICK_ANALYTICS_EXCLUDED_URLS, '')), '/private/', 4, 60); ?><p class="description"><?php _e('URLに含まれる文字列を1行ずつ入力します。', THEME_NAME); ?></p></td></tr>
      <tr><th><?php generate_label_tag(OP_CLICK_ANALYTICS_QUERY_ALLOWLIST, __('表示を許可するクエリ', THEME_NAME)); ?></th><td><?php generate_textarea_tag(OP_CLICK_ANALYTICS_QUERY_ALLOWLIST, esc_textarea(get_theme_option(OP_CLICK_ANALYTICS_QUERY_ALLOWLIST, '')), 'example.com:item_id,product', 4, 60); ?><p class="description"><?php _e('「ドメイン:キー,キー」の形式。許可しないクエリ値は管理画面へ保存・表示しません。', THEME_NAME); ?></p></td></tr>
    </tbody></table>
    <?php $health = cocoon_click_analytics_health(); ?>
    <h3><?php _e('収集状態', THEME_NAME); ?></h3>
    <ul class="ul-disc">
      <li><?php printf(esc_html__('現在のサンプリング率: %s%%（表示計測のみ）', THEME_NAME), esc_html($health['sampling_rate'])); ?></li>
      <li><?php printf(esc_html__('直近14日の受信イベント: %s', THEME_NAME), esc_html(number_format_i18n($health['received_14days']))); ?></li>
      <li><?php printf(esc_html__('直近14日の拒否イベント: %s', THEME_NAME), esc_html(number_format_i18n($health['rejected_14days']))); ?></li>
      <li><?php printf(esc_html__('直近14日のバッチ: 正常 %1$s / 重複 %2$s（重複率 %3$s）', THEME_NAME), esc_html(number_format_i18n($health['accepted_batches_14days'])), esc_html(number_format_i18n($health['duplicate_batches_14days'])), esc_html(cocoon_click_format_percent($health['duplicate_rate']))); ?></li>
      <li><?php printf(esc_html__('イベント欠損率（検証除外）: %s', THEME_NAME), esc_html(cocoon_click_format_percent($health['missing_rate']))); ?></li>
      <li><?php printf(esc_html__('直近7日CTR診断: %s', THEME_NAME), esc_html(array_key_exists($health['ctr_anomaly'], array('high' => 1, 'low' => 1, 'stable' => 1)) ? array('high' => __('通常範囲より上昇', THEME_NAME), 'low' => __('通常範囲より低下', THEME_NAME), 'stable' => __('異常なし', THEME_NAME))[$health['ctr_anomaly']] : __('データ不足', THEME_NAME))); ?></li>
      <li><?php printf(esc_html__('最終受信: %s', THEME_NAME), esc_html($health['last_ingested'] ?: __('未受信', THEME_NAME))); ?></li>
      <li><?php printf(esc_html__('最終サンプリング集計: %s', THEME_NAME), esc_html($health['sampling_updated'] ?: __('未集計', THEME_NAME))); ?></li>
      <li><?php printf(esc_html__('月次再集計: %1$s / 完了 %2$s / 更新行 %3$s', THEME_NAME), esc_html(isset($health['monthly_status']['status']) ? $health['monthly_status']['status'] : __('未実行', THEME_NAME)), esc_html(isset($health['monthly_status']['completed_at']) ? $health['monthly_status']['completed_at'] : '—'), esc_html(number_format_i18n(isset($health['monthly_status']['rows_affected']) ? $health['monthly_status']['rows_affected'] : 0))); ?></li>
      <li><?php printf(esc_html__('最終メンテナンス: %1$s / %2$sms', THEME_NAME), esc_html(isset($health['maintenance_status']['completed_at']) ? $health['maintenance_status']['completed_at'] : __('未実行', THEME_NAME)), esc_html(number_format_i18n(isset($health['maintenance_status']['duration_ms']) ? $health['maintenance_status']['duration_ms'] : 0))); ?></li>
      <li><?php printf(esc_html__('DB使用量: %s', THEME_NAME), esc_html(size_format($health['database_bytes']))); ?></li>
      <li><?php printf(esc_html__('次回メンテナンス: %s', THEME_NAME), esc_html($health['next_cron'] ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $health['next_cron']) : __('未予約', THEME_NAME))); ?></li>
      <li><?php printf(esc_html__('Cron遅延: %s', THEME_NAME), $health['cron_delay'] <= 0 ? esc_html__('なし', THEME_NAME) : esc_html(human_time_diff(time() - $health['cron_delay'], time()))); ?></li>
    </ul>
    <?php if (!$health['health_cache_available']): ?><p class="description"><?php _e('トークン不正など、バッチ受理前に拒否したリクエスト数は永続オブジェクトキャッシュがある環境だけ加算します。受理後のイベント数・重複・検証除外はDB集計へ常時記録します。', THEME_NAME); ?></p><?php endif; ?>
    <p><button type="submit" class="button button-secondary" form="cocoon-click-delete-form" onclick="return confirm('<?php echo esc_js(__('クリック解析データをすべて削除します。元に戻せません。よろしいですか？', THEME_NAME)); ?>');"><?php _e('クリック解析データを完全削除', THEME_NAME); ?></button></p>
  </div>
</div>
