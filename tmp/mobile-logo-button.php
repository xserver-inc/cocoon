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
//$icon_class = $_MENU_ICON ? $_MENU_ICON : 'logo-menu-icon';
$home_url = user_trailingslashit(get_home_url());
$home_url = apply_filters('site_logo_url', $home_url);
$home_url = apply_filters('mobile_header_site_logo_url', $home_url);
$site_logo_text = apply_filters('site_logo_text', get_bloginfo('name'));
$site_logo_text = apply_filters('mobile_header_site_logo_text', $site_logo_text);
?>
<!-- ロゴボタン -->
<li class="logo-menu-button menu-button">
  <a href="<?php echo esc_url($home_url); ?>" class="menu-button-in">
    <?php
    $logo_url = get_the_site_logo_url();
    //ロゴが存在する場合は画像
    if ($logo_url): ?>
      <img class="site-logo-image" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_logo_text); ?>">
    <?php else: ?>
      <?php echo $_MENU_CAPTION ? $_MENU_CAPTION : $site_logo_text; ?>
    <?php endif; ?>
  </a>
</li>
