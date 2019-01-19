<?php //モバイル用のスライドインボタンメニューの表示
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_mobile_button_layout_type_slide_in()): ?>

<div class="mobile-menu-buttons">
  <?php if (has_nav_menu( 'navi-header' )): ?>
    <!-- メニューボタン -->
    <div class="navi-menu-button menu-button">
      <input id="navi-menu-input" type="checkbox" class="display-none">
      <label id="navi-menu-open" class="menu-open menu-button-in" for="navi-menu-input">
        <span class="navi-menu-icon menu-icon"></span>
        <span class="navi-menu-caption menu-caption"><?php _e( 'メニュー', THEME_NAME ) ?></span>
      </label>
      <label class="display-none" id="navi-menu-close" for="navi-menu-input"></label>
      <div id="navi-menu-content" class="navi-menu-content menu-content">
        <label class="navi-menu-close-button menu-close-button" for="navi-menu-input"></label>
        <?php //ヘッダーナビ
        ob_start();
        wp_nav_menu(
          array (
            //カスタムメニュー名
            'theme_location' => 'navi-header',
            //ul 要素に適用するCSS クラス名
            'menu_class' => 'menu-drawer',
            //コンテナを表示しない
            'container' => false,
            //カスタムメニューを設定しない際に固定ページでメニューを作成しない
            'fallback_cb' => false,
          )
        );
        $wp_nav_menu = ob_get_clean();
        //ドロワーメニュー用のグローバルナビからIDを削除（IDの重複HTML5エラー対応）
        $wp_nav_menu = preg_replace('/ id="[^"]+?"/i', '', $wp_nav_menu);
        //_v($wp_nav_menu);
        echo $wp_nav_menu;
         ?>
        <!-- <label class="navi-menu-close-button menu-close-button" for="navi-menu-input"></label> -->
      </div>
    </div>
  <?php endif ?>


  <!-- ホームボタン -->
  <div class="home-menu-button menu-button">
    <a href="<?php echo home_url(); ?>" class="menu-button-in">
      <div class="home-menu-icon menu-icon"></div>
      <div class="home-menu-caption menu-caption"><?php _e( 'ホーム', THEME_NAME ) ?></div>
    </a>
  </div>

  <!-- 検索ボタン -->
  <?php if (!is_amp() || (is_amp() && is_ssl())): ?>
    <!-- 検索ボタン -->
    <div class="search-menu-button menu-button">
      <input id="search-menu-input" type="checkbox" class="display-none">
      <label id="search-menu-open" class="menu-open menu-button-in" for="search-menu-input">
        <span class="search-menu-icon menu-icon"></span>
        <span class="search-menu-caption menu-caption"><?php _e( '検索', THEME_NAME ) ?></span>
      </label>
      <label class="display-none" id="search-menu-close" for="search-menu-input"></label>
      <div id="search-menu-content" class="search-menu-content">
        <?php //検索フォーム
        get_template_part('searchform') ?>
      </div>
    </div>
  <?php endif ?>


  <?php
  $on = null;
  //AMP用のイベントを設定
  if (is_amp()) {
    $on = AMP_GO_TO_TOP_ON_CODE;
  }
   ?>
  <!-- トップボタン -->
  <div class="top-menu-button menu-button">
    <a class="go-to-top-common top-menu-a menu-button-in"<?php echo $on; ?>>
      <div class="top-menu-icon menu-icon"></div>
      <div class="top-menu-caption menu-caption"><?php _e( 'トップ', THEME_NAME ) ?></div>
    </a>
  </div>

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
</div>

<?php endif ?>

