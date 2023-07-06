<?php //モバイル用のデフォルトボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<ul class="mobile-header-menu-buttons mobile-menu-buttons">

  <?php
  if (has_nav_menu( NAV_MENU_HEADER )) {
    //ナビメニュー
    cocoon_template_part( 'tmp/mobile-navi-button' );
  } else {
    //フォローボタン
    cocoon_template_part( 'tmp/mobile-home-button' );
  } ?>

  <?php //ロゴメニュー
  cocoon_template_part( 'tmp/mobile-logo-button' ); ?>

  <?php //検索メニュー
  cocoon_template_part( 'tmp/mobile-search-button' ); ?>

</ul>
