<?php //Google Tag Managerコード（管理者はカウントしない）
if ( !is_user_admin() ) {
  //AMP用Google Tag Managerトラッキングコードを設定している場合
  $tracking_id = get_google_tag_manager_amp_tracking_id();

  if ( $tracking_id ) { ?>
  <!-- AMP Google Tag Manager -->
  <amp-analytics config="https://www.googletagmanager.com/amp.json?id=<?php echo $tracking_id; ?>&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>
  <!-- /AMP Google Tag Manager -->
  <?php
  } //$tracking_id
}//AMP Google Tag Mana終了 ?>