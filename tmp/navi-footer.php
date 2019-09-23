<?php //フッターナビ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<nav id="navi-footer" class="navi-footer">
  <div id="navi-footer-in" class="navi-footer-in">
    <?php wp_nav_menu(
      array (
        //カスタムメニュー名
        'theme_location' => NAV_MENU_FOOTER,
        //ul 要素に適用するCSS クラス名
        'menu_class' => 'menu-footer',
        //コンテナを表示しない
        'container' => false,
        //カスタムメニューを設定しない際に固定ページでメニューを作成しない
        'fallback_cb' => false,
        //出力されるulに対してidやclassを表示しない
        //'items_wrap' => '<ul>%3$s</ul>',
        //メニューの深さ
        'depth' => 1,
      )
    ); ?>
  </div>
</nav>
