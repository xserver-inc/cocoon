<?php //モバイル用の検索ボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
global $_MENU_CAPTION;
global $_MENU_ICON;
global $_MOBILE_COPY_BUTTON;
$_MOBILE_COPY_BUTTON = true;
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'fa fa-share-alt'; ?>

<!-- シェアボタン -->
<li class="share-menu-button menu-button">
  <input id="share-menu-input" type="checkbox" class="display-none">
  <label id="share-menu-open" class="menu-open menu-button-in" for="share-menu-input">
    <span class="share-menu-icon menu-icon">
      <span class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
    </span>
    <span class="share-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __( 'シェア', THEME_NAME ); ?></span>
  </label>
  <label class="display-none" id="share-menu-close" for="share-menu-input"></label>
  <div id="share-menu-content" class="share-menu-content">
    <?php //シェアボタンテンプレート
    get_template_part_with_option('tmp/sns-share-buttons', SS_MOBILE); ?>
  </div>
</li>
