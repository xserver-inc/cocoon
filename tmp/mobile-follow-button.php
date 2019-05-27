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
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'follow-menu-icon'; ?>

<!-- フォローボタン -->
<li class="follow-menu-button menu-button">
  <input id="follow-menu-input" type="checkbox" class="display-none">
  <label id="follow-menu-open" class="menu-open menu-button-in" for="follow-menu-input">
    <div class="<?php echo esc_attr($icon_class); ?> menu-icon"></div>
    <div class="follow-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __( 'フォロー', THEME_NAME ); ?></div>
  </label>
  <label class="display-none" id="follow-menu-close" for="follow-menu-input"></label>
  <div id="follow-menu-content" class="follow-menu-content">
    <?php //フォローボタンテンプレート
    get_template_part_with_option('tmp/sns-follow-buttons', SF_MOBILE); ?>
  </div>
</li>
