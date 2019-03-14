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
}//AMP Analytics終了 ?>
