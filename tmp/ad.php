<?php if (is_ads_visible() && !is_auto_adsens_only_enable()):
//レスポンシブAdSenseコードを取得
//var_dump(to_adsense_format($format));
$ad_code = get_adsense_responsive_code(to_adsense_format($format), $ad_code);
//AdSenseコード時なかった場合は設定コードをそのまま取得
//var_dump(htmlspecialchars($ad_code));
if (!$ad_code) {
  $ad_code = get_ad_code();
}
 ?>
<div class="ad-area<?php echo $wrap_class ?> cf" itemscope itemtype="https://schema.org/WPAdBlock">
  <div class="ad-label" itemprop="name"><?php echo get_ad_label();//広告ラベルの取得 ?></div>
  <div class="ad-wrap">
    <div class="ad-responsive ad-usual"><?php echo $ad_code;//広告コードの取得 ?></div>
    <?php //ダブルレクタングルの場合
    if ($format == AD_FORMAT_DABBLE_RECTANGLE): ?>
      <div class="ad-responsive ad-additional ad-additional-double"><?php echo $ad_code;//広告コード ?></div>
    <?php endif ?>
    <?php //スカイスクレイパーの場合
    if ($format == DATA_AD_FORMAT_VERTICAL):
      //レスポンシブレクタングル広告を生成
      $ad_additional_code = get_adsense_responsive_code(DATA_AD_FORMAT_RECTANGLE); ?>
      <div class="ad-responsive ad-additional ad-additional-vertical"><?php echo $ad_additional_code;//追加の広告コード ?></div>
    <?php endif ?>
  </div>

</div>
<?php endif ?>
