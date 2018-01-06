<?php //通知テンプレート
$msg = get_notice_area_message();
if (is_notice_area_visible() && $msg && !is_amp()):
  $url = get_notice_area_url();
 ?>

<?php //リンクの開始タグ
if ($url): ?>
<a href="<?php echo $url; ?>" class="notice-area-wrap">
<?php endif ?>

<div id="notice-area" class="notice-area notice-area nt-<?php echo get_notice_type(); ?>">
  <?php echo $msg; ?>
</div>

<?php //aリンクの閉じタグ
if ($url): ?>
</a>
<?php endif ?>

<?php endif ?>