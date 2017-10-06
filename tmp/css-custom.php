<?php //CSS設定用 ?>
<?php //背景画像の指定
if (get_header_background_image_url()): ?>
.header{
  background-image: url(<?php echo get_header_background_image_url(); ?>);
}
<?php endif ?>
<?php //ヘッダー全体背景色
if (get_header_container_background_color()): ?>
.header-container,
.demo .header-container,
.header-container .navi,
.navi .navi-in > ul .sub-menu{
  background-color: <?php echo get_header_container_background_color(); ?>;
}
<?php endif ?>
<?php //ヘッダー全体文字色
if (get_header_container_text_color()): ?>
.header,
.header .site-name-text-link,
.navi .navi-in a,
.navi .navi-in a:hover{
  color: <?php echo get_header_container_text_color(); ?>;
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
.header,
.header .site-name-text-link{
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
if (get_header_container_background_color() || get_header_background_color() || get_global_navi_background_color()): ?>
.navi .navi-in a:hover{
  background-color: rgba(255, 255, 255, 0.2);
}
<?php endif ?>
<?php //グローバルナビメニュー幅
if (get_global_navi_menu_width()): ?>
.navi .navi-in > ul > li{
  width: <?php echo get_global_navi_menu_width(); ?>px;
}
<?php endif ?>
<?php //グローバルナビサブメニュー幅
if (get_global_navi_sub_menu_width()): ?>
.navi .navi-in > ul .sub-menu{
  min-width: <?php echo get_global_navi_sub_menu_width(); ?>px;
}
.navi .navi-in > ul .sub-menu ul{
  left: <?php echo get_global_navi_sub_menu_width(); ?>px;
}
<?php endif ?>