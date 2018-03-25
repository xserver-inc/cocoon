<?php //AdSense AMP自動広告の<body>直後コード
if (is_ads_visible() && is_auto_adsense_enable()): ?>
<!-- Google AMP Auto AdSense -->
<amp-auto-ads type="adsense" data-ad-client="<?php echo get_adsense_data_ad_client(); ?>"></amp-auto-ads>
<!-- End Google AMP Auto AdSense -->
<?php endif ?>