<?php //AMPページでは呼び出さない（通常ページのみで呼び出す）
if (!is_amp()): ?>
  <?php //Pinterestシェア用のスクリプト
  if (is_pinterest_share_button_visible() && is_singular()): ?>
  <script async defer data-pin-height="28" data-pin-hover="true" src="//assets.pinterest.com/js/pinit.js"></script>
  <?php endif ?>
  <?php //本文中のJavaScriptをまとめて出力
  global $_THE_CONTENT_SCRIPTS;
  if ($_THE_CONTENT_SCRIPTS): ?>
  <script><?php echo $_THE_CONTENT_SCRIPTS; ?></script>
  <?php endif ?>
<?php endif ?>

