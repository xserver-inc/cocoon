<?php //モバイル用の「次へ」ボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
global $_MENU_CAPTION;
global $_MENU_ICON;
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'fa fa-comments';
$caption = $_MENU_CAPTION ? $_MENU_CAPTION : __( 'コメント', THEME_NAME ) ?>
<?php if (is_single() && is_single_comment_visible()): ?>
<!-- コメント -->
<li class="comments-menu-button menu-button">
  <a href="#comment-area" title="<?php echo $caption; ?>" class="go-to-comment-area menu-button-in">
    <span class="comments-menu-icon menu-icon">
      <span class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
    </span>
    <span class="comments-menu-caption menu-caption"><?php echo $caption; ?></span>
  </a>
</li>
<?php endif; ?>

