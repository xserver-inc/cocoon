<?php //AdSense AMP自動広告の</head>手前コード
if (is_ads_visible() && is_auto_adsense_enable()): ?>
<!-- Google AMP Auto AdSense script -->
<script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>
<!-- End Google AMP Auto AdSense script -->
<?php endif ?>