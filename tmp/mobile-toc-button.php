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
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'toc-menu-icon'; ?>

<?php if (is_the_page_toc_use()): ?>

<?php
$on = null;
//AMP用のイベントを設定
if (is_amp()) {
  $on = AMP_GO_TO_TOC_ON_CODE;
}
  ?>
<!-- 目次へボタン -->
<li class="toc-menu-button menu-button">
  <a class="go-to-toc-common toc-menu-a menu-button-in"<?php echo $on; ?>>
    <div class="<?php echo esc_attr($icon_class); ?> menu-icon"></div>
    <div class="cop-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __( '目次へ', THEME_NAME ); ?></div>
  </a>
</li>

<?php endif; ?>
