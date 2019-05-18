<?php //モバイル用のホームボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- ホームボタン -->
<li class="home-menu-button menu-button">
  <a href="<?php echo home_url(); ?>" class="menu-button-in">
    <div class="home-menu-icon menu-icon"></div>
    <div class="home-menu-caption menu-caption"><?php _e( 'ホーム', THEME_NAME ) ?></div>
  </a>
</li>
