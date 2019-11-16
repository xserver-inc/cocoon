<?php //モバイル用のサイドバーボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
global $_MENU_CAPTION;
global $_MENU_ICON;
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'fa fa-outdent'; ?>

<?php if (is_active_sidebar( 'sidebar' )): ?>
<!-- サイドバーボタン -->
  <li class="sidebar-menu-button menu-button">
    <input id="sidebar-menu-input" type="checkbox" class="display-none">
    <label id="sidebar-menu-open" class="menu-open menu-button-in" for="sidebar-menu-input">
      <span class="sidebar-menu-icon menu-icon">
        <span class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
      </span>
      <span class="sidebar-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __( 'サイドバー', THEME_NAME ); ?></span>
    </label>
    <label class="display-none" id="sidebar-menu-close" for="sidebar-menu-input"></label>
    <div id="sidebar-menu-content" class="sidebar-menu-content menu-content">
      <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"><span class="fa fa-close" aria-hidden="true"></span></label>
      <?php //サイドバー
      ob_start();
      get_template_part('sidebar');
      $sidebar = ob_get_clean();
      //ドロワーメニュー用のサイドバーからIDを削除（IDの重複HTML5エラー対応）
      $sidebar = preg_replace('/ id="([^"]+?)"/i', ' id="slide-in-$1"', $sidebar);
      $sidebar = preg_replace('/ for="([^"]+?)"/i', ' for="slide-in-$1"', $sidebar);

      //AdSense広告の除去
      $sidebar = preg_replace('/<ins.+?<\/ins>/is', '', $sidebar);
      $sidebar = preg_replace('/<script.+?<\/script>/is', '', $sidebar);

      echo $sidebar; ?>
    </div>
  </li>
<?php endif ?>
