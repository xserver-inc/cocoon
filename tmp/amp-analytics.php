<?php //Google Analyticsコード（ログインユーザーはカウントしない）
//var_dump(get_amp_tracking_id());
if ( !is_user_admin() ) {
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