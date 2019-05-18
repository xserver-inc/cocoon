<?php //モバイル用のスライドインボタンメニューの表示
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

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
    <div class="top-menu-icon menu-icon"></div>
    <div class="top-menu-caption menu-caption"><?php _e( 'トップ', THEME_NAME ) ?></div>
  </a>
</li>
