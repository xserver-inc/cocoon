<?php //モバイル用のホームボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
global $_MENU_CAPTION;
global $_MENU_ICON;
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'logo-menu-icon'; ?>

<!-- ロゴボタン -->
<li class="logo-menu-button menu-button">
  <a href="<?php echo esc_url(get_home_url()); ?>" class="menu-button-in">
    <img class="site-logo-image" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
  </a>
</li>
