<?php if (is_ads_visible()):
//レスポンシブAdSenseコードを取得
$ad_code = generate_adsense_responsive_code($format);
//AdSenseコード時なかった場合は設定コードをそのまま取得
if (!$ad_code) {
  $ad_code = get_ad_code();
}
 ?>
<div class="ad-area<?php echo $wrap_class ?> cf">
  <div class="ad-label"><?php echo get_ad_label();//広告ラベルの取得 ?></div>
  <div class="ad-responsive"><?php echo $ad_code;//広告コードの取得 ?></div>
</div>
<?php endif ?>
