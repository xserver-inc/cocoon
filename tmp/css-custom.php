<?php //CSS設定用 ?>
<style type="text/css">
<?php //背景画像の指定
if (get_header_background_image_url()): ?>
.header{
  background-image: url(<?php echo get_header_background_image_url(); ?>);
}
<?php endif ?>
<?php //ヘッダー背景色
if (get_header_background_color()): ?>
.header{
  background-color: <?php echo get_header_background_color(); ?>;
}
<?php endif ?>
<?php //ヘッダー文字色
if (get_header_text_color()): ?>
.header{
  color: <?php echo get_header_text_color(); ?>;
}
<?php endif ?>
</style>
