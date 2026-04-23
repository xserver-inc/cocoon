<?php //アクセス解析ダッシュボード - 設定タブ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 設定保存処理
if ( isset($_POST[HIDDEN_FIELD_NAME]) &&
     wp_verify_nonce($_POST[HIDDEN_FIELD_NAME], 'access') ) {
  // 既存の3項目 + 新設4項目 を保存
  require dirname(__FILE__) . '/../access-posts.php';
  update_theme_option(OP_ACCESS_ANALYTICS_ENABLE);
  update_theme_option(OP_ACCESS_ANALYTICS_CACHE_TTL);
  update_theme_option(OP_ACCESS_ANALYTICS_DEFAULT_PERIOD);
  update_theme_option(OP_ACCESS_ANALYTICS_EXPORT_ENABLE);

  // キャッシュクリアボタン
  if (isset($_POST['cocoon_analytics_flush'])) {
    cocoon_analytics_flush_cache();
    echo '<div class="notice notice-success is-dismissible"><p>';
    echo esc_html__('アクセス解析のキャッシュをクリアしました。', THEME_NAME);
    echo '</p></div>';
  }

  echo '<div class="notice notice-success is-dismissible"><p><strong>';
  echo esc_html__('設定を変更しました。', THEME_NAME);
  echo '</strong></p></div>';
}
?>

<form name="form1" method="post" action="" class="admin-settings">
  <p><?php _e('テーマ内で独自にアクセス集計を行います。集計結果は「人気記事」ウィジェットや本ダッシュボードで使用されます。', THEME_NAME); ?></p>

  <div class="access metabox-holder">
    <?php require_once dirname(__FILE__) . '/../access-forms.php'; ?>

    <!-- アクセス解析ダッシュボード設定 -->
    <div class="postbox">
      <h2 class="hndle"><?php _e('アクセス解析ダッシュボード', THEME_NAME); ?></h2>
      <div class="inside">
        <p><?php _e('集計済みデータを可視化するダッシュボードの設定です。', THEME_NAME); ?></p>
        <table class="form-table"><tbody>
          <tr>
            <th scope="row"><?php generate_label_tag(OP_ACCESS_ANALYTICS_ENABLE, __('ダッシュボード機能', THEME_NAME)); ?></th>
            <td>
              <?php generate_checkbox_tag(OP_ACCESS_ANALYTICS_ENABLE, is_access_analytics_enable(), __('アクセス解析ダッシュボードを有効にする', THEME_NAME)); ?>
              <?php generate_tips_tag(__('無効にするとダッシュボード系タブは非表示になり、「設定」タブのみ表示されます。', THEME_NAME)); ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php generate_label_tag(OP_ACCESS_ANALYTICS_CACHE_TTL, __('キャッシュTTL（分）', THEME_NAME)); ?></th>
            <td>
              <?php
              $ttl_options = array(
                '5' => __('5分', THEME_NAME),
                '15' => __('15分', THEME_NAME),
                '30' => __('30分', THEME_NAME),
                '60' => __('1時間', THEME_NAME),
                '180' => __('3時間', THEME_NAME),
                '360' => __('6時間', THEME_NAME),
                '1440' => __('24時間', THEME_NAME),
              );
              generate_selectbox_tag(OP_ACCESS_ANALYTICS_CACHE_TTL, $ttl_options, get_access_analytics_cache_ttl());
              generate_tips_tag(__('ダッシュボード集計結果のキャッシュ時間。新規アクセス記録時は自動的に無効化されます。', THEME_NAME));
              ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php generate_label_tag(OP_ACCESS_ANALYTICS_DEFAULT_PERIOD, __('既定の集計期間', THEME_NAME)); ?></th>
            <td>
              <?php
              $period_options = array(
                'today'     => __('今日', THEME_NAME),
                '7days'     => __('直近7日', THEME_NAME),
                '30days'    => __('直近30日', THEME_NAME),
                '90days'    => __('直近90日', THEME_NAME),
                'thismonth' => __('今月', THEME_NAME),
                'all'       => __('全期間', THEME_NAME),
              );
              generate_selectbox_tag(OP_ACCESS_ANALYTICS_DEFAULT_PERIOD, $period_options, get_access_analytics_default_period());
              ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php generate_label_tag(OP_ACCESS_ANALYTICS_EXPORT_ENABLE, __('エクスポート', THEME_NAME)); ?></th>
            <td>
              <?php generate_checkbox_tag(OP_ACCESS_ANALYTICS_EXPORT_ENABLE, is_access_analytics_export_enable(), __('CSV/JSONエクスポートを有効にする', THEME_NAME)); ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php _e('キャッシュのクリア', THEME_NAME); ?></th>
            <td>
              <button type="submit" name="cocoon_analytics_flush" value="1" class="button">
                <?php _e('ダッシュボードのキャッシュをクリア', THEME_NAME); ?>
              </button>
              <?php generate_tips_tag(__('集計結果の transient キャッシュを一括削除します。', THEME_NAME)); ?>
            </td>
          </tr>
        </tbody></table>
      </div>
    </div>

  </div>

  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('access'); ?>">
  <?php submit_button(__('変更を保存', THEME_NAME)); ?>
</form>
