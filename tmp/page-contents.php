<?php
///////////////////////////////////////
// 固定ページのコンテンツ
///////////////////////////////////////?>
<?php //パンくずリストがメイントップの場合
if (is_page_breadcrumbs_position_main_top()){
  get_template_part('tmp/breadcrumbs-page');
} ?>

<?php //本文の表示
get_template_part('tmp/content') ?>

<?php //コメントを表示する場合
if (is_page_comment_visible()) {
  comments_template(); //コメントテンプレート
} ?>

<?php //パンくずリストがメインボトムの場合
if (is_page_breadcrumbs_position_main_bottom()){
  get_template_part('tmp/breadcrumbs-page');
} ?>

<?php //メインカラム追従領域
get_template_part('tmp/main-scroll'); ?>