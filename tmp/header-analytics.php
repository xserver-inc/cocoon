
<?php //ヘッダーのアクセス解析
if (!is_user_logged_in()) : //ログインしていないユーザーのみ適用 ?>
<?php //Google Tag Manager (noscript)
if ( get_google_tag_manager_tracking_id() )://トラッキングIDが設定されているとき ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo get_google_tag_manager_tracking_id(); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>
<?php if ( get_google_analytics_tracking_id() )://トラッキングIDが設定されているとき ?>
<!-- Google Analytics -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo get_google_analytics_tracking_id(); ?>', 'auto');
  ga('send', 'pageview');

</script>
<!-- /Google Analytics -->
<?php endif; ?>
<?php if ( get_ptengine_tracking_id() ): ?>
<!-- Ptengine -->
<script type="text/javascript">
  window._pt_sp_2 = [];
  _pt_sp_2.push('setAccount,<?php echo get_ptengine_tracking_id(); ?>');
  var _protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
  (function() {
    var atag = document.createElement('script'); atag.type = 'text/javascript'; atag.async = true;
    atag.src = _protocol + 'js.ptengine.jp/pta.js';
    var stag = document.createElement('script'); stag.type = 'text/javascript'; stag.async = true;
    stag.src = _protocol + 'js.ptengine.jp/pts.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(atag, s);s.parentNode.insertBefore(stag, s);
  })();
</script>
<!-- /Ptengine -->
<?php endif ?>
<?php //以下その他の解析コードなど
if (get_other_analytics_header_tags()) {
  echo '<!-- Other Analytics -->'.PHP_EOL;
  echo get_other_analytics_header_tags().PHP_EOL;
  echo '<!-- /Other Analytics -->'.PHP_EOL;
}?>
<?php endif; //!is_user_logged_in() ?>