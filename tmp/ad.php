<?php if (is_ads_visible()):
//レスポンシブAdSenseコードを取得
$ad_code = generate_adsense_responsive_code(to_adsense_format($format));
//AdSenseコード時なかった場合は設定コードをそのまま取得
var_dump($format);
if (!$ad_code) {
  $ad_code = get_ad_code();
}
 ?>
<div class="ad-area<?php echo $wrap_class ?> cf">
  <div class="ad-label"><?php echo get_ad_label();//広告ラベルの取得 ?></div>
  <div class="ad-wrap">
    <div class="ad-responsive"><?php echo $ad_code;//広告コードの取得 ?></div>
    <?php //ダブルレクタングルの場合
    if ($format == AD_FORMAT_DABBLE_RECTANGLE): ?>
      <div class="ad-responsive ad-rectangle-right"><?php echo $ad_code;//広告コードの取得 ?></div>
    <?php endif ?>
  </div>

</div>
<?php endif ?>
