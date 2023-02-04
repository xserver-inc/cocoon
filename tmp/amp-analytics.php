<?php //Google Analyticsコード（ログインユーザーはカウントしない）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//タグマネージャIDが設定されているときは計測しない
if ( is_analytics() && !get_google_tag_manager_tracking_id() ) {
  //AMP用Analyticsトラッキングコードを設定している場合
  $tracking_id = get_google_analytics_tracking_id();
  $after_title = '[AMP]';

  if ( $tracking_id ) { ?>

  <!-- AMP Google Analytics -->
  <amp-analytics type="googleanalytics" id="analytics-amp">
  <script type="application/json">
  {
    "vars": {
      "account": "<?php echo $tracking_id ?>"
    },
    "triggers": {
      "trackPageviewWithAmpdocUrl": {
        "on": "visible",
        "request": "pageview",
        "vars": {
          "title": "<?php the_title() ?><?php echo $after_title; ?>",
          "ampdocUrl": "<?php echo get_amp_permalink() ?>"
        }
      }
    }
  }
  </script>
  </amp-analytics>
  <!-- /AMP Google Analytics -->
  <?php //AMP用Analyticsトラッキングコードを設定しておらず通常ステージ用の場合
  }
  //GA4でAMP情報を取得（Google非推奨）
  //https://www.thyngster.com/how-to-track-amp-pages-with-google-analytics-4
  //https://www.kagua.biz/wp/wpplugin/amp2ga4.html
  //https://github.com/analytics-debugger/google-analytics-4-for-amp/blob/main/ga4.json
  $ga4_tracking_id = get_ga4_tracking_id();
  if ( is_analytics() && $ga4_tracking_id ) { ?>
    <amp-analytics type="googleanalytics" config="<?php echo home_url('/wp-content/themes/cocoon-master/ga4.json'); ?>" data-credentials="include">
    <script type="application/json">
    {
      "vars": {
          "GA4_MEASUREMENT_ID": "<?php echo $ga4_tracking_id; ?>",
          "GA4_ENDPOINT_HOSTNAME": "www.google-analytics.com",
          "DEFAULT_PAGEVIEW_ENABLED": true,
          "GOOGLE_CONSENT_ENABLED": false,
          "WEBVITALS_TRACKING": false,
          "PERFORMANCE_TIMING_TRACKING": false,
          "SEND_DOUBLECLICK_BEACON": false
      }
    }
    </script>
    </amp-analytics>
  <?php }
}//AMP Analytics終了 ?>
