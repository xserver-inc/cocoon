<?php //CSS設定用 ?>
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
<?php //グローバルナビ背景色
if (get_global_navi_background_color()): ?>
.navi,
.navi .navi-in > ul .sub-menu{
  background-color: <?php echo get_global_navi_background_color(); ?>;
}
<?php endif ?>
<?php //グローバルナビ文字色
if (get_global_navi_text_color()): ?>
.navi .navi-in a,
.navi .navi-in a:hover{
  color: <?php echo get_global_navi_text_color(); ?>;
}
<?php endif ?>
<?php //グローバルナビホバー背景色
if (get_global_navi_hover_background_color()): ?>
.navi .navi-in a:hover{
  background-color: <?php echo get_global_navi_hover_background_color(); ?>;
}
<?php endif ?>
