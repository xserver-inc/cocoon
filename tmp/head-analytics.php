<?php //ログインユーザー以外
if (!is_user_administrator()): ?>
  <?php //Google Analytics(gtag.js)
  if ( get_google_analytics_tracking_id() )://トラッキングIDが設定されているとき ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo get_google_analytics_tracking_id(); ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo get_google_analytics_tracking_id(); ?>');
  </script>
  <!-- /Global site tag (gtag.js) - Google Analytics -->
  <?php endif; ?>
<?php endif ?>
