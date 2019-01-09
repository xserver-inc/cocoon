<?php //グローバルナビカスタマイズ用ドロワー
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<div class="mobile-menu-buttons">
  <!-- メニューボタン -->
  <div class="navi-menu-button navi-button">
      <input id="navi-menu-input" type="checkbox" class="display-none">
      <label id="navi-menu-open" class="menu-open" for="navi-menu-input">
        <div class="navi-menu-caption menu-caption"><?php _e( 'メニュー', THEME_NAME ) ?></div>
      </label>
      <label class="display-none" id="navi-menu-close" for="navi-menu-input"></label>
      <div id="navi-menu-content" class="navi-menu-content menu-content">
        <label class="navi-menu-close-button menu-close-button" for="navi-menu-input"></label>
        <?php //ヘッダーナビ
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
        ); ?>
        <!-- <label class="navi-menu-close-button menu-close-button" for="navi-menu-input"></label> -->
      </div>
  </div>


  <!-- サイドバーボタン -->
  <div class="sidebar-menu-button navi-button">
      <input id="sidebar-menu-input" type="checkbox" class="display-none">
      <label id="sidebar-menu-open" class="menu-open" for="sidebar-menu-input">
        <div class="sidebar-menu-caption menu-caption"><?php _e( 'サイドバー', THEME_NAME ) ?></div>
      </label>
      <label class="display-none" id="sidebar-menu-close" for="sidebar-menu-input"></label>
      <div id="sidebar-menu-content" class="sidebar-menu-content menu-content">
        <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"></label>
        <?php //サイドバー
        get_template_part('sidebar'); ?>
        <!-- <label class="sidebar-menu-close-button menu-close-button" for="sidebar-menu-input"></label> -->
      </div>
  </div>

</div onclose="">
