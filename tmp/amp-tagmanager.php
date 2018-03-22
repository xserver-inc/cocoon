<?php //Google Tag Managerコード（管理者はカウントしない）
if ( !is_user_admin() ) {
  //AMP用Google Tag Managerトラッキングコードを設定している場合
  $tracking_id = get_google_tag_manager_tracking_id();

  if ( $tracking_id ) { ?>
  <amp-analytics config="https://www.googletagmanager.com/amp.json?id=<?php echo $tracking_id; ?>" data-credentials="include"></amp-analytics>
  <?php
  } //$tracking_id
}//AMP Google Tag Mana終了 ?>