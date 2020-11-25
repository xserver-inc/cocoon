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
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'fa fa-arrow-right';
$nextpost = get_adjacent_post(is_post_navi_same_category_enable(), '', false); //次の記事 ?>
<?php if (is_single() && $nextpost): ?>
<!-- 次へボタン -->
<li class="next-menu-button menu-button">
  <a href="<?php echo esc_url(get_the_permalink($nextpost->ID)); ?>" title="<?php echo esc_attr(get_the_title($nextpost->ID)); ?>" class="menu-button-in">
    <span class="next-menu-icon menu-icon">
      <span class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
    </span>
    <span class="next-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __( '次へ', THEME_NAME ); ?></span>
  </a>
</li>
<?php endif; ?>

