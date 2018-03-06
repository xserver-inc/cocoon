<?php if (is_ads_visible() && is_auto_adsense_enable()): ?>
<!-- Google Auto AdSense -->
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "<?php echo get_adsense_data_ad_client(); ?>",
          enable_page_level_ads: true
     });
</script>
<!-- End Google Auto AdSense -->
<?php endif ?>