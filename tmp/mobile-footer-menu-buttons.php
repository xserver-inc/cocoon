<?php //モバイル用のフッターボタンメニューの表示
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp() && (is_mobile_button_layout_type_footer_mobile_buttons() || is_mobile_button_layout_type_header_and_footer_mobile_buttons())): ?>

<?php
if (has_nav_menu( NAV_MENU_FOOTER_MOBILE_BUTTONS )) {
  //モバイルフッターメニュー
  get_template_part( 'tmp/mobile-footer-custom-navi-buttons' );
} else {
  //デフォルトモバイルボタン
  get_template_part( 'tmp/mobile-footer-default-buttons' );
}
?>

<?php endif ?>

