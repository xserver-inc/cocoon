<?php //モバイル用のスライドインボタンメニューの表示
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
global $_MENU_CAPTION;
global $_MENU_ICON;
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'fa fa-arrow-up'; ?>

<?php
$on = null;
//AMP用のイベントを設定
if (is_amp()) {
  $on = AMP_GO_TO_TOP_ON_CODE;
}
  ?>
<!-- トップボタン -->
<li class="top-menu-button menu-button">
  <a class="go-to-top-common top-menu-a menu-button-in"<?php echo $on; ?>>
    <span class="top-menu-icon menu-icon">
      <span class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
    </span>
    <span class="top-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __( 'トップ', THEME_NAME ); ?></span>
  </a>
</li>
