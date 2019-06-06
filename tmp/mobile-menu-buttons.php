<?php //モバイル用のスライドインボタンメニューの表示
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_mobile_button_layout_type_slide_in()): ?>

<?php
if (has_nav_menu( NAV_MENU_FOOTER_MOBILE )) {
  //モバイルフッターメニュー
  get_template_part( 'tmp/mobile-footer-menu-buttons' );
} else {
  //デフォルトモバイルボタン
  get_template_part( 'tmp/mobile-default-buttons' );
}
?>

<?php endif ?>

