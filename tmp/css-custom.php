<?php //CSS設定用 ?>
<style type="text/css">
<?php //背景画像の指定
if (get_header_background_image_url()): ?>
.header{
  background-image: url(<?php echo get_header_background_image_url(); ?>);
}
<?php endif ?>
</style>
