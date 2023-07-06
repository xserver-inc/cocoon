<?php //固定ページのコンテンツ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php //パンくずリストがメイントップの場合
if (is_page_breadcrumbs_position_main_top()){
  cocoon_template_part('tmp/breadcrumbs-page');
} ?>

<?php //本文の表示
cocoon_template_part('tmp/content') ?>

<?php //コメントを表示する場合
if (is_page_comment_visible()) {
  comments_template(); //コメントテンプレート
} ?>

<?php //パンくずリストがメインボトムの場合
if (is_page_breadcrumbs_position_main_bottom()){
  cocoon_template_part('tmp/breadcrumbs-page');
} ?>

<?php //メインカラム追従領域
cocoon_template_part('tmp/main-scroll'); ?>
