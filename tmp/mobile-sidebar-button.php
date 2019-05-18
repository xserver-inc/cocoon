<?php //モバイル用のサイドバーボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php if (is_active_sidebar( 'sidebar' ) || is_active_sidebar( 'sidebar-scroll' )): ?>
<!-- サイドバーボタン -->
  <div class="sidebar-menu-button menu-button">
    <input id="sidebar-menu-input" type="checkbox" class="display-none">
    <label id="sidebar-menu-open" class="menu-open menu-button-in" for="sidebar-menu-input">
      <span class="sidebar-menu-icon menu-icon"></span>
      <span class="sidebar-menu-caption menu-caption"><?php _e( 'サイドバー', THEME_NAME ) ?></span>
    </label>
    <label class="display-none" id="sidebar-menu-close" for="sidebar-menu-input"></label>
    <div id="sidebar-menu-content" class="sidebar-menu-content menu-content">
      <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"></label>
      <?php //サイドバー
      ob_start();
      get_template_part('sidebar');
      $sidebar = ob_get_clean();
      //ドロワーメニュー用のサイドバーからIDを削除（IDの重複HTML5エラー対応）
      $sidebar = preg_replace('/ id="([^"]+?)"/i', ' id="slide-in-$1"', $sidebar);
      $sidebar = preg_replace('/ for="([^"]+?)"/i', ' for="slide-in-$1"', $sidebar);
      echo $sidebar;
        ?>
      <!-- <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"></label> -->
    </div>
  </div>
<?php endif ?>
